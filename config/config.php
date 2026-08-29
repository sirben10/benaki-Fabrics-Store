<?php
declare(strict_types=1);
function benaki_env(string $key, string $default=''): string {
    $value = getenv($key);
    return ($value === false || $value === '') ? $default : (string)$value;
}
return [
    'db' => [
        'host' => benaki_env('BENAKI_DB_HOST','127.0.0.1'),
        'name' => benaki_env('BENAKI_DB_NAME','benaki_fabrics'),
        'user' => benaki_env('BENAKI_DB_USER','root'),
        'pass' => benaki_env('BENAKI_DB_PASS',''),
        'charset' => 'utf8mb4',
    ],
    'app' => [
        'name' => 'Benaki Fabrics',
        // Leave empty for automatic detection in XAMPP/subfolders; set BENAKI_BASE_URL for production/rewrite deployments.
        'base_url' => rtrim(benaki_env('BENAKI_BASE_URL',''),'/'),
        'timezone' => 'Africa/Lagos',
        'upload_max_bytes' => 5 * 1024 * 1024,
    ],
    'business' => [
        'name' => 'Benaki Fabrics', 'tagline' => 'Finest Quality Textiles',
        'phone1' => '08133314846', 'phone2' => '09069351146', 'whatsapp' => '2348133314846',
        'email' => benaki_env('BENAKI_BUSINESS_EMAIL',''), 'location' => 'Calabar, Cross River State',
        'delivery' => 'South East & South South Nigeria',
    ],
    'admin' => [
        'setup_key' => benaki_env('BENAKI_ADMIN_SETUP_KEY','CHANGE-ME-BEFORE-PRODUCTION'),
        'session_minutes' => 30,
    ],
];
