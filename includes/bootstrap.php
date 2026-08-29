<?php
declare(strict_types=1);
$config=require __DIR__.'/../config/config.php';
date_default_timezone_set($config['app']['timezone']??'Africa/Lagos');
require_once __DIR__.'/../config/db.php';
require_once __DIR__.'/functions.php';
