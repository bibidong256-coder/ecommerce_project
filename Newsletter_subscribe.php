<?php
// ── newsletter_subscribe.php ────────────────────────
// Handles newsletter subscriptions → saves to MySQL
// ────────────────────────────────────────────────────

header('Content-Type: application/json');

// Ensure the path to your config is correct
require_once __DIR__ . '/config/db.php'; 

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
    exit;
}

// ── Get & sanitize input ────────────────────────────
$email = trim($_POST['email'] ?? '');

// ── Validate ────────────────────────────────────────
if (empty($email)) {
    echo json_encode(['success' => false, 'message' => 'Please enter your email address.']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Please enter a valid email address.']);
    exit;
}

// ── Save to database ────────────────────────────────
try {
    // 1. Check if already subscribed
    $check = $conn->prepare("SELECT id FROM newsletter_subscribers WHERE email = :email");
    $check->execute([':email' => $email]);

    if ($check->fetch()) {
        echo json_encode(['success' => true, 'message' => 'You are already subscribed! Thank you.']);
        exit;
    }

    // 2. Insert new subscriber
    $stmt = $conn->prepare(
        "INSERT INTO newsletter_subscribers (email, subscribed_at) VALUES (:email, NOW())"
    );
    $stmt->execute([':email' => $email]);

    echo json_encode(['success' => true, 'message' => 'Thank you for subscribing to our newsletter!']);

} catch (PDOException $e) {
    // Log the error $e->getMessage() internally for debugging
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Something went wrong. Please try again.']);
}
?>