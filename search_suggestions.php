<?php

include("db.php");

$q = $_GET['q'] ?? '';

if (strlen($q) < 2) {
    exit;
}

$term = "%" . $q . "%";


$stmt = $conn->prepare("
            SELECT blogs.*, author.username
            FROM blogs
            JOIN author ON blogs.author_id = author.author_id
            WHERE blogs.title LIKE ? 
               OR blogs.subtitle LIKE ? 
               OR author.username LIKE ?
            LIMIT 5
        ");

$stmt->bind_param(
    "sss",
    $term,
    $term,
    $term
);
$stmt->execute();

$result = $stmt->get_result();

// If matching records exist, loop through them
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo '
        <div class="suggestion-item p-3 hover:bg-gray-100 cursor-pointer text-gray-700 text-sm border-b last:border-0 border-gray-100">
            ' . htmlspecialchars($row['title']) . '
        </div>';
    }
} else {
    // NEW: If nothing matches, echo a clean notice element (without the 'suggestion-item' class)
    echo '<div class="p-4 text-center text-sm text-gray-500 italic">No matching blogs found...</div>';
}

$stmt->close();
