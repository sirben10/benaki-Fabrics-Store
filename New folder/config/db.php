<?php
$config = require __DIR__ . '/config.php';
$db = $config['db'];

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
try {
    $con = new mysqli($db['host'], $db['user'], $db['pass'], $db['name']);
    $con->set_charset($db['charset']);
} catch (Throwable $e) {
    http_response_code(500);
    exit('Database connection failed. Please check the server configuration.');
}
