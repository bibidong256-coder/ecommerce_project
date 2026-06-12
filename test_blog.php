<?php
require_once 'config/db.php';

echo "<h2>DB Test</h2>";

// Test 1: Can we query blog_posts?
$stmt = $conn->query("SELECT * FROM blog_posts LIMIT 5");
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "<pre>";
print_r($posts);
echo "</pre>";

// Test 2: Can we query with the JOIN?
$stmt = $conn->query("
    SELECT p.*, u.name AS author, c.name AS category
    FROM blog_posts p
    JOIN users u ON u.id = p.author_id
    JOIN blog_categories c ON c.id = p.category_id
    WHERE p.status = 'published'
    LIMIT 5
");
$posts2 = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "<h2>JOIN Test</h2><pre>";
print_r($posts2);
echo "</pre>";
?>