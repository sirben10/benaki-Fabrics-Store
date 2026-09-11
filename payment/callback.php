<?php
require __DIR__.'/../includes/bootstrap.php';
$reference=trim((string)($_GET['reference']??''));
if($reference==='') redirect_to(base_url('checkout.php'));
$secret=trim((string)(paystack_config()['secret_key']??''));
$orderStmt=$pdo->prepare('SELECT * FROM orders WHERE order_ref=? OR paystack_reference=? LIMIT 1');$orderStmt->execute([$reference,$reference]);$order=$orderStmt->fetch();
if(!$order){http_response_code(404);exit('Order not found.');}
if($secret!==''){
  $ch=curl_init('https://api.paystack.co/transaction/verify/'.rawurlencode($reference));curl_setopt_array($ch,[CURLOPT_RETURNTRANSFER=>true,CURLOPT_TIMEOUT=>30,CURLOPT_HTTPHEADER=>['Authorization: Bearer '.$secret]]);$raw=curl_exec($ch);curl_close($ch);$r=json_decode((string)$raw,true);$d=$r['data']??[];
  if(($d['status']??'')==='success' && (int)($d['amount']??0)===(int)round((float)$order['total_amount']*100)){$pdo->prepare('UPDATE orders SET payment_status="paid",paystack_reference=?,paystack_transaction_id=?,paid_at=NOW() WHERE id=?')->execute([$reference,(int)($d['id']??0),$order['id']]);$order['payment_status']='paid';$order['receipt_token']= (string)$order['receipt_token'];unset($_SESSION['cart']);}
}
if(($order['payment_status']??'')==='paid') redirect_to(base_url('receipt.php?ref='.rawurlencode($order['order_ref']).'&token='.rawurlencode((string)$order['receipt_token'])));
http_response_code(402);echo 'Payment could not be verified. Please contact Benaki Fabrics with reference '.e($order['order_ref']).'.';
