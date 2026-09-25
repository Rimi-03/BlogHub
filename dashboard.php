<?php

declare(strict_types=1);

$target = 'admin/dashboard.php';

$queryString = $_SERVER['QUERY_STRING'] ?? '';
if ($queryString !== '') {
    $target .= '?' . $queryString;
}

header('Location: ' . $target, true, 302);
exit;
