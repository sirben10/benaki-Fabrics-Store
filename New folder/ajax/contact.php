<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../config/db.php';

function respond(bool $ok, string $message): never {
    echo json_encode(['ok' => $ok, 'message' => $message]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') respond(false, 'Invalid request method.');
if (trim($_POST['website'] ?? '') !== '') respond(true, 'Thank you.');

$fullname = trim($_POST['fullname'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$email = trim($_POST['email'] ?? '');
$message = trim($_POST['message'] ?? '');

if (mb_strlen($fullname) < 2) respond(false, 'Please enter your name.');
if ($phone && !preg_match('/^[0-9+()\s-]{7,25}$/', $phone)) respond(false, 'Please enter a valid phone number.');
if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) respond(false, 'Please enter a valid email address.');
if (mb_strlen($message) < 5) respond(false, 'Please enter your message.');

$stmt = $con->prepare('INSERT INTO contact_messages (fullname, phone, email, message) VALUES (?, ?, ?, ?)');
$stmt->bind_param('ssss', $fullname, $phone, $email, $message);
$stmt->execute();
$stmt->close();
respond(true, 'Thank you for contacting Benaki Fabrics. We will get back to you soon.');
