<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../config/db.php';

$prices = [
    'Jonkoso' => ['yard' => 3000, 'trouser_length' => 4000],
    'Crepe' => ['yard' => 2500, 'trouser_length' => 3000],
    'Stock' => ['yard' => 4000, 'trouser_length' => 5000],
    'Vintage' => ['yard' => 2000, 'trouser_length' => 2000],
    'Chinos' => ['yard' => 3000, 'trouser_length' => 3000],
];

function respond(bool $ok, string $message, array $extra = []): never {
    echo json_encode(array_merge(['ok' => $ok, 'message' => $message], $extra));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') respond(false, 'Invalid request method.');

mysqli_report(MYSQLI_REPORT_OFF);

$honeypot = trim($_POST['website'] ?? '');
if ($honeypot !== '') respond(true, 'Thank you.');

$fabric = trim($_POST['fabric_type'] ?? '');
$measurement = trim($_POST['measurement'] ?? '');
$quantity = filter_var($_POST['quantity'] ?? null, FILTER_VALIDATE_FLOAT);
$fullname = trim($_POST['fullname'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$location = trim($_POST['location'] ?? '');
$description = trim($_POST['description'] ?? '');
$colors = $_POST['colors'] ?? [];

if (!is_array($colors)) $colors = [];
$allowedColors = ['White','Cream','Gold','Yellow','Pink','Purple','Wine','Red','Blue','Navy','Green','Teal','Black','Grey','Brown'];
$colors = array_values(array_intersect($allowedColors, array_map('trim', $colors)));

if (!isset($prices[$fabric])) respond(false, 'Please select a valid fabric type.');
if (!isset($prices[$fabric][$measurement])) respond(false, 'Please select a valid measurement type.');
if ($quantity === false || $quantity <= 0 || $quantity > 10000) respond(false, 'Enter a valid quantity.');
if (!$fullname || mb_strlen($fullname) < 2) respond(false, 'Please enter your full name.');
if (!preg_match('/^[0-9+()\s-]{7,25}$/', $phone)) respond(false, 'Please enter a valid phone number.');
if (!$location) respond(false, 'Please enter your delivery/location details.');
if (count($colors) < 1) respond(false, 'Please select at least one colour.');

$unitPrice = $prices[$fabric][$measurement];
$total = $unitPrice * $quantity;
$orderRef = 'BF' . date('ymdHis') . strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));
$colorString = implode(', ', $colors);

$stmt = $con->prepare('INSERT INTO orders (order_ref, fabric_type, colors, measurement, quantity, unit_price, total_amount, fullname, phone, location, description) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
if (!$stmt) respond(false, 'We could not prepare your order. Please try again later.');

$stmt->bind_param('ssssdddssss', $orderRef, $fabric, $colorString, $measurement, $quantity, $unitPrice, $total, $fullname, $phone, $location, $description);

if (!$stmt->execute()) {
    $stmt->close();
    respond(false, 'We could not save your order right now. Please try again or contact us by phone/WhatsApp.');
}
$stmt->close();

respond(true, 'Order received successfully.', [
    'order_ref' => $orderRef,
    'total' => number_format($total, 2),
    'currency' => '₦'
]);
