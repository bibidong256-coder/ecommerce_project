<?php
// ── contact_submit.php ──────────────────────────────
header('Content-Type: application/json');

require_once __DIR__ . '/config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
    exit;
}

// ── 1. reCAPTCHA v3 verification ────────────────────
// ✅ Replace with your actual Secret Key from Google reCAPTCHA admin
$recaptchaSecret   = '6LfcTP0sAAAAALFNjoGbF6x1SDL-VSPCOKHnBnAF';
$recaptchaResponse = trim($_POST['g-recaptcha-response'] ?? '');

if (empty($recaptchaResponse)) {
    echo json_encode(['success' => false, 'message' => 'reCAPTCHA token missing. Please try again.']);
    exit;
}

$verifyUrl = 'https://www.google.com/recaptcha/api/siteverify?'
    . 'secret='    . urlencode($recaptchaSecret)
    . '&response=' . urlencode($recaptchaResponse)
    . '&remoteip=' . urlencode($_SERVER['REMOTE_ADDR'] ?? '');

$verifyResult = @file_get_contents($verifyUrl);

// Fallback: use cURL if allow_url_fopen is disabled
if ($verifyResult === false) {
    $ch = curl_init($verifyUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    $verifyResult = curl_exec($ch);
    curl_close($ch);
}

if ($verifyResult === false) {
    echo json_encode(['success' => false, 'message' => 'reCAPTCHA check failed. Please try again.']);
    exit;
}

$captchaData = json_decode($verifyResult, true);

// ✅ v3: check success, action match, AND score (0.0 = bot, 1.0 = human)
// A score of 0.5 or above is generally safe; lower = more suspicious
if (
    empty($captchaData['success']) ||
    ($captchaData['action'] ?? '') !== 'contact_form' ||
    ($captchaData['score']  ?? 0)  < 0.5
) {
    echo json_encode(['success' => false, 'message' => 'Spam detected. Please try again.']);
    exit;
}

// ── 2. Validate form fields ──────────────────────────
$name    = trim($_POST['name']    ?? '');
$email   = trim($_POST['email']   ?? '');
$subject = trim($_POST['subject'] ?? '');
$message = trim($_POST['message'] ?? '');

if (empty($name) || empty($email) || empty($message)) {
    echo json_encode(['success' => false, 'message' => 'Please fill in all required fields.']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Please enter a valid email address.']);
    exit;
}

if (strlen($name) > 100 || strlen($subject) > 200 || strlen($message) > 5000) {
    echo json_encode(['success' => false, 'message' => 'Input too long. Please shorten your message.']);
    exit;
}

// ── 3. Save to database ──────────────────────────────
try {
    $stmt = $conn->prepare(
        "INSERT INTO contact_messages (name, email, subject, message, submitted_at)
         VALUES (:name, :email, :subject, :message, NOW())"
    );
    $stmt->execute([
        ':name'    => $name,
        ':email'   => $email,
        ':subject' => $subject,
        ':message' => $message,
    ]);
    echo json_encode(['success' => true, 'message' => "Message sent! We'll get back to you soon."]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Something went wrong. Please try again.']);
}
?>