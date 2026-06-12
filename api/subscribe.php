<?php
// api/subscribe.php — Newsletter signup
// Uses your existing newsletter table and config.php
header('Content-Type: application/json');

require_once '../config/db.php'; // gives us $conn

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
    exit;
}

$email = trim($_POST['email'] ?? '');

if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Please enter a valid email address.']);
    exit;
}

try {
    // Check if already subscribed — adjust column names if yours differ
    $check = $conn->prepare("SELECT id FROM newsletter WHERE email = ?");
    $check->execute([$email]);
    $existing = $check->fetch(PDO::FETCH_ASSOC);

    if ($existing) {
        echo json_encode(['success' => false, 'message' => 'This email is already subscribed!']);
    } else {
        $conn->prepare("INSERT INTO newsletter (email) VALUES (?)")->execute([$email]);
        echo json_encode(['success' => true, 'message' => 'Thank you for subscribing! 🎉']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Something went wrong. Please try again.']);
}
