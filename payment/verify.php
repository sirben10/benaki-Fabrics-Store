<?php

declare(strict_types=1);
require __DIR__ . '/../includes/bootstrap.php';
header('Content-Type: application/json; charset=utf-8');
function vjson(bool $ok, string $message, array $extra = [], int $status = 200): never
{
    http_response_code($status);
    echo json_encode(array_merge(['ok' => $ok, 'message' => $message], $extra), JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
    exit;
}
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') vjson(false, 'Invalid request.', [], 405);
$reference = trim((string)($_POST['reference'] ?? ''));
if ($reference === '') vjson(false, 'Missing payment reference.', [], 422);
$secret = trim((string)(paystack_config()['secret_key'] ?? ''));
if ($secret === '') vjson(false, 'Paystack is not configured.', [], 503);
try {
    $s = $pdo->prepare('SELECT * FROM orders WHERE paystack_reference=? OR order_ref=? LIMIT 1');
    $s->execute([$reference, $reference]);
    $order = $s->fetch();
    if (!$order) vjson(false, 'Order not found.', [], 404);
    $ch = curl_init('https://api.paystack.co/transaction/verify/' . rawurlencode($reference));
    curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 30, CURLOPT_HTTPHEADER => ['Authorization: Bearer ' . $secret]]);
    $raw = curl_exec($ch);
    $err = curl_error($ch);
    $code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    if ($raw === false || $err) throw new RuntimeException('Could not contact Paystack.');
    $resp = json_decode($raw, true);
    $data = $resp['data'] ?? [];
    if ($code < 200 || $code >= 300 || empty($resp['status'])) throw new RuntimeException('Payment verification failed.');
    $expected = (int)round((float)$order['total_amount'] * 100);
    $actual = (int)($data['amount'] ?? 0);
    if (($data['status'] ?? '') !== 'success' || $actual !== $expected) {
        $pdo->prepare('UPDATE orders SET payment_status=? WHERE id=?')->execute(['failed', $order['id']]);
        vjson(false, 'Payment was not verified. If money was debited, please contact Benaki Fabrics with reference ' . $order['order_ref'] . '.', [], 402);
    }
    $paystackReference = trim((string)($data['reference'] ?? $reference));
    $transactionId = (int)($data['id'] ?? 0);
    $pdo->prepare('UPDATE orders SET payment_status="paid",paystack_reference=?,paystack_transaction_id=?,paid_at=COALESCE(paid_at,NOW()),status="new" WHERE id=?')->execute([$paystackReference, $transactionId, $order['id']]);
    $paidStmt = $pdo->prepare('SELECT paid_at FROM orders WHERE id=?');
    $paidStmt->execute([$order['id']]);
    $paidAt = (string)($paidStmt->fetchColumn() ?: date('Y-m-d H:i:s'));
    unset($_SESSION['cart']);
    vjson(true, 'Payment verified successfully.', [
        'order_ref' => $order['order_ref'],
        'payment_reference' => $paystackReference,
        'transaction_id' => $transactionId,
        'paid_at' => $paidAt,
        'payment_status' => 'paid',
        'receipt_token' => $order['receipt_token'],
        'receipt_url' => absolute_site_url('receipt.php?ref=' . rawurlencode($order['order_ref']) . '&token=' . rawurlencode((string)$order['receipt_token'])),
    ]);
} catch (Throwable $e) {
    error_log('[Benaki Paystack verify] ' . $e->getMessage());
    vjson(false, 'We could not verify the payment right now. Please do not pay again until the order status is checked.', [], 500);
}
