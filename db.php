<?php

$conn = new mysqli("localhost", "root", "", "db_bloghub");

if ($conn->connect_error) {
    die("DB connection failed");
}
// echo "DB connected successfully";

$page = $_GET['page'] ?? 'home';
$id = $_GET['id'] ?? null;
$q = $_GET['q'] ?? '';

function getContent($key)
{
    global $conn;

    static $cache = [];

    if (isset($cache[$key])) {
        return $cache[$key];
    }

    $stmt = $conn->prepare("SELECT content_value FROM site_content WHERE content_key = ?");
    $stmt->bind_param("s", $key);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    $cache[$key] = $result['content_value'] ?? '';

    return $cache[$key];
}
