<?php
declare(strict_types=1);
$config=require __DIR__.'/../config/config.php';
date_default_timezone_set($config['app']['timezone']??'Africa/Lagos');
if (session_status() !== PHP_SESSION_ACTIVE) { session_name('benaki_session'); session_start(['cookie_httponly'=>true,'cookie_samesite'=>'Lax','cookie_secure'=>(!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')]); }
require_once __DIR__.'/../config/db.php';
require_once __DIR__.'/functions.php';
