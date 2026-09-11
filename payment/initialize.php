<?php

declare(strict_types=1);
require __DIR__ . '/../includes/bootstrap.php';
header('Content-Type: application/json; charset=utf-8');
function pay_json(bool $ok, string $message, array $extra = [], int $status = 200): never
{
  http_response_code($status);
  echo json_encode(array_merge(['ok' => $ok, 'message' => $message], $extra), JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
  exit;
}
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') pay_json(false, 'Invalid request.', [], 405);
$cfg = paystack_config();
$secret = trim((string)($cfg['secret_key'] ?? ''));
$public = trim((string)($cfg['public_key'] ?? ''));
if ($secret === '' || $public === '') pay_json(false, 'Paystack is not configured yet. Add PAYSTACK_PUBLIC_KEY and PAYSTACK_SECRET_KEY to your server environment.', [], 503);
$cart = cart_items();
if (!$cart) pay_json(false, 'Your cart is empty.', [], 422);
$name = trim((string)($_POST['fullname'] ?? ''));
$email = trim((string)($_POST['email'] ?? ''));
$phone = trim((string)($_POST['phone'] ?? ''));
$location = trim((string)($_POST['location'] ?? ''));
$description = trim((string)($_POST['description'] ?? ''));
if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || $phone === '' || $location === '') pay_json(false, 'Please complete your name, email, phone and delivery location.', [], 422);
try {
  $ids = array_values(array_unique(array_map(fn($x) => (int)($x['product_id'] ?? 0), $cart)));
  $ph = implode(',', array_fill(0, count($ids), '?'));
  $s = $pdo->prepare("SELECT id,name,slug,image_path,yard_price,trouser_price FROM products WHERE is_active=1 AND id IN ($ph)");
  $s->execute($ids);
  $rows = $s->fetchAll();
  $products = [];
  foreach ($rows as $r) {
    $products[(int)$r['id']] = $r;
  }
  $items = [];
  $subtotal = 0;
  foreach ($cart as $item) {
    $pid = (int)$item['product_id'];
    if (!isset($products[$pid])) throw new RuntimeException('A fabric in your cart is no longer available.');
    $p = $products[$pid];
    $measurement = $item['measurement'] === 'trouser_length' ? 'trouser_length' : 'yard';
    $unit = (float)($measurement === 'yard' ? $p['yard_price'] : $p['trouser_price']);
    $qty = (float)$item['quantity'];
    if ($qty <= 0 || $unit <= 0) throw new RuntimeException('Invalid cart item.');
    $line = round($unit * $qty, 2);
    $colors = is_array($item['colors'] ?? null) ? $item['colors'] : [];
    $items[] = ['product_id' => $pid, 'product_name' => $p['name'], 'colors' => $colors, 'measurement' => $measurement, 'quantity' => $qty, 'unit_price' => $unit, 'line_total' => $line, 'discount' => anniversary_discount($qty, $unit)];
    $subtotal += $line;
  }
  $discount = cart_discount($cart);
  $total = max(0, round($subtotal - $discount, 2));
  if ($total < 1) pay_json(false, 'The order total must be greater than zero.', [], 422);
  $ref = 'BF-' . date('ymd') . '-' . strtoupper(bin2hex(random_bytes(4)));
  $receiptToken = bin2hex(random_bytes(32));
  $pdo->beginTransaction();
  $first = $items[0];
  $stmt = $pdo->prepare('INSERT INTO orders(order_ref,product_id,fabric_type,colors,measurement,quantity,unit_price,subtotal,discount,total_amount,fullname,phone,email,location,description,status,payment_status,receipt_token) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)');
  $stmt->execute([$ref, $first['product_id'], $first['product_name'], json_encode($first['colors'], JSON_UNESCAPED_UNICODE), $first['measurement'], $first['quantity'], $first['unit_price'], $subtotal, $discount, $total, $name, $phone, $email, $location, $description ?: null, 'new', 'pending', $receiptToken]);
  $orderId = (int)$pdo->lastInsertId();
  $oi = $pdo->prepare('INSERT INTO order_items(order_id,product_id,product_name,colors,measurement,quantity,unit_price,line_total) VALUES(?,?,?,?,?,?,?,?)');
  foreach ($items as $it) $oi->execute([$orderId, $it['product_id'], $it['product_name'], json_encode($it['colors'], JSON_UNESCAPED_UNICODE), $it['measurement'], $it['quantity'], $it['unit_price'], $it['line_total']]);
  $pdo->commit();
  $payload = json_encode(['email' => $email, 'amount' => (string)round($total * 100), 'currency' => (string)$cfg['currency'], 'reference' => $ref, 'callback_url' => absolute_site_url('payment/callback.php'), 'metadata' => json_encode(['order_id' => $orderId, 'order_ref' => $ref])], JSON_UNESCAPED_SLASHES);
  $ch = curl_init('https://api.paystack.co/transaction/initialize');
  curl_setopt_array($ch, [CURLOPT_POST => true, CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 30, CURLOPT_HTTPHEADER => ['Authorization: Bearer ' . $secret, 'Content-Type: application/json'], CURLOPT_POSTFIELDS => $payload]);
  $raw = curl_exec($ch);
  $err = curl_error($ch);
  $code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
  curl_close($ch);
  if ($raw === false || $err) throw new RuntimeException('Paystack connection failed.');
  $resp = json_decode($raw, true);
  if ($code < 200 || $code >= 300 || empty($resp['status'])) {
    error_log('[Benaki Paystack initialize] ' . $raw);
    throw new RuntimeException('Paystack could not initialize the payment.');
  }
  $paystackReference = trim((string)($resp['data']['reference'] ?? $ref));
  $pdo->prepare('UPDATE orders SET paystack_reference=?,payment_status=? WHERE id=?')->execute([$paystackReference, 'pending', $orderId]);
  pay_json(true, 'Payment initialized.', [
    'authorization_url' => (string)($resp['data']['authorization_url'] ?? ''),
    'access_code' => (string)($resp['data']['access_code'] ?? ''),
    'reference' => $paystackReference,
    'public_key' => $public,
    'amount' => (int)round($total * 100),
    'receipt_token' => $receiptToken,
  ]);
} catch (Throwable $e) {
  if ($pdo->inTransaction()) $pdo->rollBack();
  error_log('[Benaki checkout] ' . $e->getMessage());
  pay_json(false, $e instanceof RuntimeException ? $e->getMessage() : 'Unable to start checkout right now. Please try again.', [], 500);
}
