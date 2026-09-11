<?php
declare(strict_types=1);
$config = require __DIR__ . '/config.php';
$db = $config['db'] ?? [];
$dsn = sprintf('mysql:host=%s;dbname=%s;charset=%s', $db['host'] ?? '127.0.0.1', $db['name'] ?? 'benaki_fabrics', $db['charset'] ?? 'utf8mb4');
try {
    $pdo = new PDO($dsn, $db['user'] ?? 'root', $db['pass'] ?? '', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::ATTR_STRINGIFY_FETCHES => false,
    ]);
} catch (Throwable $e) {
    http_response_code(500);
    exit('Unable to connect to the Benaki Fabrics database. Check config.php and MySQL.');
}
