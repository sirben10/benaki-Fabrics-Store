<?php
declare(strict_types=1);
require __DIR__.'/../includes/bootstrap.php';
header('Content-Type: application/json; charset=utf-8');
function cart_json(bool $ok,string $message='',array $extra=[]): never { echo json_encode(array_merge(['ok'=>$ok,'message'=>$message,'count'=>cart_count()],$extra),JSON_UNESCAPED_UNICODE|JSON_INVALID_UTF8_SUBSTITUTE); exit; }
if (($_SERVER['REQUEST_METHOD']??'GET')!=='POST') cart_json(false,'Invalid request.');
$action=(string)($_POST['action']??'');
if ($action==='add') {
    $id=filter_input(INPUT_POST,'product_id',FILTER_VALIDATE_INT); $qty=(float)($_POST['quantity']??1); $measurement=(string)($_POST['measurement']??'yard'); $colors=$_POST['colors']??[];
    if(!$id || $qty<=0 || $qty>1000 || !in_array($measurement,['yard','trouser_length'],true) || !is_array($colors) || !$colors) cart_json(false,'Select a fabric, quantity, measurement and at least one colour.');
    $colors=array_values(array_unique(array_filter(array_map('trim',$colors))));
    if(!$colors) cart_json(false,'Select at least one colour.');
    $stmt=$pdo->prepare('SELECT id,name,slug,image_path,yard_price,trouser_price FROM products WHERE id=? AND is_active=1 LIMIT 1'); $stmt->execute([$id]); $p=$stmt->fetch();
    if(!$p) cart_json(false,'The selected fabric is unavailable.');
    $unit=(float)($measurement==='yard'?$p['yard_price']:$p['trouser_price']); if($unit<=0) cart_json(false,'This fabric does not have a valid price.');
    // Each checked colour is a separate cart item. The requested quantity applies to each colour.
    foreach($colors as $color){
        $key=cart_key((int)$p['id'],$measurement,$color);
        if(isset($_SESSION['cart'][$key])){
            $_SESSION['cart'][$key]['quantity']=min(1000,(float)$_SESSION['cart'][$key]['quantity']+$qty);
        }else{
            $_SESSION['cart'][$key]=['key'=>$key,'product_id'=>(int)$p['id'],'name'=>$p['name'],'slug'=>$p['slug'],'image_path'=>$p['image_path'],'colors'=>[$color],'measurement'=>$measurement,'quantity'=>$qty,'unit_price'=>$unit];
        }
    }
    cart_json(true,'Added '.count($colors).' colour item'.(count($colors)===1?'':'s').' to cart.',['cart_url'=>base_url('cart.php'),'added_items'=>count($colors)]);
}
if($action==='update') {
    $key=(string)($_POST['key']??''); $qty=(float)($_POST['quantity']??0); if(!isset($_SESSION['cart'][$key])) cart_json(false,'Cart item not found.'); if($qty<=0){unset($_SESSION['cart'][$key]);}else{$_SESSION['cart'][$key]['quantity']=min($qty,1000);} cart_json(true,'Cart updated.');
}
if($action==='remove') { $key=(string)($_POST['key']??''); unset($_SESSION['cart'][$key]); cart_json(true,'Item removed.'); }
if($action==='clear') { unset($_SESSION['cart']); cart_json(true,'Cart cleared.'); }
cart_json(false,'Unknown cart action.');
