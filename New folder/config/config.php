<?php
// Benaki Fabrics Anniversary App configuration.
// Copy this file to config/config.local.php and update credentials for production.

function envv(string $key, string $default = ''): string {
    $value = getenv($key);
    return ($value === false || $value === '') ? $default : $value;
}

return [
    'db' => [
        'host' => envv('BENAKI_DB_HOST', '127.0.0.1'),
        'name' => envv('BENAKI_DB_NAME', 'benaki_anniversary'),
        'user' => envv('BENAKI_DB_USER', 'root'),
        'pass' => envv('BENAKI_DB_PASS', ''),
        'charset' => 'utf8mb4',
    ],
    'business' => [
        'name' => 'Benaki Fabrics',
        'phone1' => '08133314846',
        'phone2' => '09069351146',
        'location' => 'Calabar',
        'delivery' => 'South East / South South',
        'whatsapp' => '2348133314846',
    ],
];
