<?php
declare(strict_types=1);
header('Content-Type: application/json; charset=utf-8');
require __DIR__.'/../config/db.php';
$config=require __DIR__.'/../config/config.php';
function respond(bool $ok,string $message,array $extra=[]):never { http_response_code($ok?200:422); echo json_encode(array_merge(['ok'=>$ok,'message'=>$message],$extra),JSON_UNESCAPED_UNICODE); exit; }
if(($_SERVER['REQUEST_METHOD']??'GET')!=='POST') respond(false,'Invalid request method.');
if(trim((string)($_POST['website']??''))!=='') respond(true,'Thank you.');
$pid=filter_input(INPUT_POST,'product_id',FILTER_VALIDATE_INT); $qty=(float)($_POST['quantity']??0); $measurement=(string)($_POST['measurement']??'');
$fullname=trim((string)($_POST['fullname']??'')); $phone=trim((string)($_POST['phone']??'')); $location=trim((string)($_POST['location']??'')); $description=trim((string)($_POST['description']??''));
$colors=$_POST['colors']??[]; if(!is_array($colors)) $colors=[]; $colors=array_values(array_unique(array_filter(array_map(fn($v)=>trim((string)$v),$colors))));
$allowedMeasurements=['yard','trouser_length']; $allowedColors=['White','Cream','Gold','Yellow','Pink','Purple','Wine','Red','Blue','Navy','Green','Teal','Black','Grey','Brown'];
if(!$pid || $qty<=0 || $qty>1000 || !in_array($measurement,$allowedMeasurements,true) || !$fullname || !$phone || !$location || !$colors) respond(false,'Please complete all required order fields.');
if(count($colors)>15 || array_diff($colors,$allowedColors)) respond(false,'Please choose valid fabric colours.');
if(mb_strlen($fullname)>120||mb_strlen($phone)>50||mb_strlen($location)>200||mb_strlen($description)>3000) respond(false,'One or more fields are too long.');
try {
 $stmt=$pdo->prepare('SELECT id,name,yard_price,trouser_price FROM products WHERE id=? AND is_active=1 LIMIT 1'); $stmt->execute([$pid]); $product=$stmt->fetch(); if(!$product) respond(false,'The selected fabric is no longer available.');
 $unit=(float)($measurement==='yard'?$product['yard_price']:$product['trouser_price']); $subtotal=round($unit*$qty,2);
 $discount=0; if($unit>=4000){if($qty>30)$discount=4500;elseif($qty>=20)$discount=3000;elseif($qty>=10)$discount=2000;elseif($qty>=5)$discount=1500;elseif($qty>=3)$discount=1000;}elseif($unit>=3000){if($qty>30)$discount=3500;elseif($qty>=20)$discount=2500;elseif($qty>=10)$discount=2000;elseif($qty>=5)$discount=1500;elseif($qty>=3)$discount=500;}else{if($qty>30)$discount=3000;elseif($qty>=20)$discount=2000;elseif($qty>=10)$discount=1500;elseif($qty>=5)$discount=1000;elseif($qty>=3)$discount=500;} $total=max(0,$subtotal-$discount);
 $ref='BF-'.date('ymd').'-'.strtoupper(bin2hex(random_bytes(3)));
 $stmt=$pdo->prepare('INSERT INTO orders(order_ref,product_id,fabric_type,colors,measurement,quantity,unit_price,subtotal,discount,total_amount,fullname,phone,location,description) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?)');
 $stmt->execute([$ref,(int)$product['id'],$product['name'],json_encode($colors,JSON_UNESCAPED_UNICODE),$measurement,$qty,$unit,$subtotal,$discount,$total,$fullname,$phone,$location,$description?:null]);
 respond(true,'Order request received successfully.', ['order_ref'=>$ref,'total'=>number_format($total,2),'discount'=>number_format($discount,2)]);
} catch(Throwable $e) { respond(false,'We could not save your order right now. Please call or WhatsApp Benaki Fabrics.'); }
