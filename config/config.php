<?php
declare(strict_types=1);
if (!function_exists('benaki_env')) {
    function benaki_env(string $key, string $default=''): string {
        static $fileEnv;
        if ($fileEnv === null) {
            $fileEnv = [];
            foreach ([__DIR__ . '/../.env', __DIR__ . '/../.env.example'] as $path) {
                if (!is_file($path)) continue;
                foreach (file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [] as $line) {
                    $line = trim($line);
                    if ($line === '' || $line[0] === '#') continue;
                    $separator = strpos($line, '=');
                    if ($separator === false) continue;
                    $name = trim(substr($line, 0, $separator));
                    $value = trim(substr($line, $separator + 1));
                    if ($value !== '' && (($value[0] === '"' && substr($value, -1) === '"') || ($value[0] === "'" && substr($value, -1) === "'"))) {
                        $value = substr($value, 1, -1);
                    }
                    if ($name !== '' && !array_key_exists($name, $fileEnv)) $fileEnv[$name] = $value;
                }
            }
        }
        $value = getenv($key);
        if ($value !== false && $value !== '') return (string)$value;
        $value = $fileEnv[$key] ?? '';
        return $value === '' ? $default : (string)$value;
    }
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
    'paystack' => [
        'public_key' => benaki_env('PAYSTACK_PUBLIC_KEY',''),
        'secret_key' => benaki_env('PAYSTACK_SECRET_KEY',''),
        'currency' => 'NGN',
    ],
    'admin' => [
        'setup_key' => benaki_env('BENAKI_ADMIN_SETUP_KEY','BE260911-000'),
        'session_minutes' => 30,
    ],
];
