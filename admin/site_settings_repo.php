<?php

/**
 * Data-access layer for the `site_content` table (the "Site Settings" drawer
 * in the admin control panel).
 *
 * Rules enforced by this module:
 *   1. One row per content_key. Writes never create a duplicate.
 *   2. Reads and deletes address a single primary key, never a whole key group.
 *   3. Every value is bound as a prepared-statement parameter.
 *
 * On this stack (PHP 8.1+, mysqli_report = MYSQLI_REPORT_ERROR) a failed
 * prepare/execute raises mysqli_sql_exception rather than returning false, so
 * each call is wrapped to keep a database fault from surfacing as a fatal
 * error. Callers get a plain bool/int and the detail goes to the error log.
 */

/**
 * Resolve the authoritative row for a content_key: the highest id, which is
 * always the most recent write. Returns 0 when the key does not exist.
 */
function siteContentResolveId(mysqli $conn, $key)
{
    try {
        $stmt = $conn->prepare(
            'SELECT id FROM site_content WHERE content_key = ? ORDER BY id DESC LIMIT 1'
        );

        $stmt->bind_param('s', $key);
        $stmt->execute();

        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        return $row ? (int) $row['id'] : 0;
    } catch (mysqli_sql_exception $e) {
        error_log('BlogHub site_content resolve failed: ' . $e->getMessage());
        return 0;
    }
}

/**
 * Save a setting. If the key already exists the existing row is updated in
 * place; a row is only inserted when the key is genuinely new.
 *
 * $inserted receives true when a new row was created.
 */
function siteContentSave(mysqli $conn, $key, $value, &$inserted = null)
{
    $inserted = false;

    $key = trim($key);
    $value = trim($value);

    if ($key === '') {
        return false;
    }

    // Never UPDATE blindly: with an unchanged value MySQL reports 0 affected
    // rows, which must not be mistaken for "no such key" and trigger an INSERT.
    $existing_id = siteContentResolveId($conn, $key);

    if ($existing_id > 0) {
        try {
            $stmt = $conn->prepare(
                'UPDATE site_content SET content_value = ? WHERE id = ? LIMIT 1'
            );

            $stmt->bind_param('si', $value, $existing_id);
            $ok = $stmt->execute();
            $stmt->close();

            return $ok;
        } catch (mysqli_sql_exception $e) {
            error_log('BlogHub site_content update failed: ' . $e->getMessage());
            return false;
        }
    }

    try {
        $stmt = $conn->prepare(
            'INSERT INTO site_content (content_key, content_value) VALUES (?, ?)'
        );

        $stmt->bind_param('ss', $key, $value);
        $ok = $stmt->execute();
        $inserted = $ok;
        $stmt->close();

        return $ok;
    } catch (mysqli_sql_exception $e) {
        // A concurrent request can win the race to INSERT the same key; the
        // UNIQUE index turns that into a duplicate-key error rather than a
        // second row, so fall back to updating the row that now exists.
        error_log('BlogHub site_content insert failed: ' . $e->getMessage());

        $existing_id = siteContentResolveId($conn, $key);

        if ($existing_id < 1) {
            return false;
        }

        try {
            $stmt = $conn->prepare(
                'UPDATE site_content SET content_value = ? WHERE id = ? LIMIT 1'
            );

            $stmt->bind_param('si', $value, $existing_id);
            $ok = $stmt->execute();
            $stmt->close();

            return $ok;
        } catch (mysqli_sql_exception $e) {
            error_log('BlogHub site_content retry failed: ' . $e->getMessage());
            return false;
        }
    }
}

/**
 * Delete exactly one setting, addressed by primary key. Passing a content_key
 * is rejected: it could match several rows and wipe them all.
 *
 * Returns false when the id is invalid or no row matched, so the caller does
 * not report a delete that never happened.
 */
function siteContentDeleteById(mysqli $conn, $id)
{
    $id = (int) $id;

    if ($id <= 0) {
        return false;
    }

    try {
        $stmt = $conn->prepare('DELETE FROM site_content WHERE id = ? LIMIT 1');

        $stmt->bind_param('i', $id);
        $ok = $stmt->execute() && $stmt->affected_rows === 1;
        $stmt->close();

        return $ok;
    } catch (mysqli_sql_exception $e) {
        error_log('BlogHub site_content delete failed: ' . $e->getMessage());
        return false;
    }
}

/**
 * Remove duplicate content_key rows, keeping the highest id (the most recent
 * write) for each key. Returns the number of rows deleted, or -1 on failure.
 */
function siteContentDeduplicate(mysqli $conn)
{
    try {
        $groups = $conn->query(
            'SELECT content_key, MAX(id) AS keep_id
             FROM site_content
             GROUP BY content_key
             HAVING COUNT(*) > 1'
        );

        $targets = [];

        while ($group = $groups->fetch_assoc()) {
            $targets[] = $group;
        }

        $groups->free();

        $removed = 0;

        foreach ($targets as $target) {
            $key = $target['content_key'];
            $keep_id = (int) $target['keep_id'];

            $stmt = $conn->prepare(
                'DELETE FROM site_content WHERE content_key = ? AND id <> ?'
            );

            $stmt->bind_param('si', $key, $keep_id);
            $stmt->execute();
            $removed += $stmt->affected_rows;
            $stmt->close();
        }

        return $removed;
    } catch (mysqli_sql_exception $e) {
        error_log('BlogHub site_content dedupe failed: ' . $e->getMessage());
        return -1;
    }
}

/**
 * True when the UNIQUE index that makes a duplicate insert impossible exists.
 */
function siteContentHasUniqueKeyIndex(mysqli $conn)
{
    try {
        $stmt = $conn->prepare(
            'SELECT 1
             FROM information_schema.statistics
             WHERE table_schema = DATABASE()
               AND table_name = ?
               AND index_name = ?
             LIMIT 1'
        );

        $table = 'site_content';
        $index = 'uq_site_content_key';

        $stmt->bind_param('ss', $table, $index);
        $stmt->execute();

        $exists = $stmt->get_result()->num_rows > 0;
        $stmt->close();

        return $exists;
    } catch (mysqli_sql_exception $e) {
        error_log('BlogHub site_content index check failed: ' . $e->getMessage());
        return false;
    }
}

/**
 * Idempotent migration: drop duplicate rows, then enforce one row per key so
 * the database itself rejects a duplicate insert.
 *
 * The table and index names in the ALTER are compile-time literals, not user
 * input, so they cannot be injected. Everything else is bound.
 */
function siteContentEnsureIntegrity(mysqli $conn)
{
    if (siteContentHasUniqueKeyIndex($conn)) {
        return true;
    }

    if (siteContentDeduplicate($conn) < 0) {
        return false;
    }

    try {
        return (bool) $conn->query(
            'ALTER TABLE site_content
             ADD UNIQUE KEY uq_site_content_key (content_key)'
        );
    } catch (mysqli_sql_exception $e) {
        error_log('BlogHub site_content index creation failed: ' . $e->getMessage());
        return false;
    }
}
