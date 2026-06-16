<?php

$conn = new mysqli("localhost", "root", "", "db_bloghub");

if ($conn->connect_error) {
    die("DB connection failed");
}
// echo "DB connected successfully";

$page = $_GET['page'] ?? 'home';
$id = $_GET['id'] ?? null;
$q = $_GET['q'] ?? '';
