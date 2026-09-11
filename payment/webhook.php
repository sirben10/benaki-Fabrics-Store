<?php
declare(strict_types=1);
require __DIR__.'/../includes/bootstrap.php';

$secret=trim((string)(paystack_config()['secret_key']??''));
$raw=file_get_contents('php://input') ?: '';
$signature=(string)($_SERVER['HTTP_X_PAYSTACK_SIGNATURE']??'');
if($secret===''||$raw===''||$signature===''||!hash_equals(hash_hmac('sha512',$raw,$secret),$signature)){http_response_code(401);exit('Unauthorized');}
$event=json_decode($raw,true);$data=$event['data']??[];
if(($event['event']??'')!=='charge.success'){http_response_code(200);exit('Ignored');}
$reference=trim((string)($data['reference']??''));$amount=(int)($data['amount']??0);if($reference===''){http_response_code(200);exit('No reference');}
$stmt=$pdo->prepare('SELECT * FROM orders WHERE order_ref=? OR paystack_reference=? LIMIT 1');$stmt->execute([$reference,$reference]);$order=$stmt->fetch();if(!$order){http_response_code(200);exit('Unknown order');}
$expected=(int)round((float)$order['total_amount']*100);if($amount!==$expected){error_log('[Benaki Paystack webhook] Amount mismatch for '.$reference);http_response_code(400);exit('Amount mismatch');}
$pdo->prepare('UPDATE orders SET payment_status="paid",paystack_reference=?,paystack_transaction_id=?,paid_at=COALESCE(paid_at,NOW()) WHERE id=?')->execute([$reference,(int)($data['id']??0),$order['id']]);
http_response_code(200);echo 'OK';
