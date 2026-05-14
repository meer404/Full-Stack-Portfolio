<?php
require_once __DIR__ . '/../config.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
    exit;
}

$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$subject = trim($_POST['subject'] ?? '');
$message = trim($_POST['message'] ?? '');

if ($name === '' || $email === '' || $subject === '' || $message === '') {
    echo json_encode(['success' => false, 'message' => 'Please fill in all fields.']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Invalid email address.']);
    exit;
}

$stmt = $pdo->prepare('INSERT INTO contact_messages (sender_name, sender_email, subject, message) VALUES (?, ?, ?, ?)');
$stmt->execute([
    htmlspecialchars($name, ENT_QUOTES, 'UTF-8'),
    htmlspecialchars($email, ENT_QUOTES, 'UTF-8'),
    htmlspecialchars($subject, ENT_QUOTES, 'UTF-8'),
    htmlspecialchars($message, ENT_QUOTES, 'UTF-8'),
]);

echo json_encode(['success' => true, 'message' => 'Message sent successfully.']);
