<?php
declare(strict_types=1); header('Content-Type: application/json; charset=utf-8'); require __DIR__.'/../config/db.php';
function out(bool $ok,string $m):never{http_response_code($ok?200:422);echo json_encode(['ok'=>$ok,'message'=>$m],JSON_UNESCAPED_UNICODE);exit;}
if(($_SERVER['REQUEST_METHOD']??'GET')!=='POST')out(false,'Invalid request method.'); if(trim((string)($_POST['website']??''))!=='')out(true,'Thank you.');
$name=trim((string)($_POST['fullname']??''));$phone=trim((string)($_POST['phone']??''));$email=trim((string)($_POST['email']??''));$message=trim((string)($_POST['message']??''));
if(!$name||!$message)out(false,'Please provide your name and message.');if(mb_strlen($name)>120||mb_strlen($phone)>50||mb_strlen($email)>160||mb_strlen($message)>3000)out(false,'One or more fields are too long.');if($email && !filter_var($email,FILTER_VALIDATE_EMAIL))out(false,'Please enter a valid email address.');
try{$s=$pdo->prepare('INSERT INTO contact_messages(fullname,phone,email,message) VALUES(?,?,?,?)');$s->execute([$name,$phone?:null,$email?:null,$message]);out(true,'Thank you. Your message has been received and our team will get back to you soon.');}catch(Throwable $e){out(false,'We could not send your message right now. Please try again.');}
