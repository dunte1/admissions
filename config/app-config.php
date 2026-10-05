<?php

$appConfig = [];
$configPath = __DIR__ . '/app-config.json';

if (file_exists($configPath)) {
    $appConfig = json_decode(file_get_contents($configPath), true);
}

return [
    'name' => $appConfig['app']['name'] ?? env('APP_NAME', 'Admission Portal'),
    'env' => $appConfig['app']['env'] ?? env('APP_ENV', 'local'),
    'debug' => $appConfig['app']['debug'] ?? env('APP_DEBUG', false),
    'url' => $appConfig['app']['url'] ?? env('APP_URL', 'http://localhost'),

    'application_fee' => $appConfig['application_fee'] ?? env('APPLICATION_FEE', 2000),
    'currency' => $appConfig['currency'] ?? env('CURRENCY', 'KES'),

    'campus' => [
        'default' => $appConfig['campus']['default'] ?? 'main',
        'list' => $appConfig['campus']['list'] ?? [
            'main' => 'Main Campus',
        ],
    ],

    'languages' => [
        'default' => $appConfig['languages']['default'] ?? 'en',
        'supported' => $appConfig['languages']['supported'] ?? ['en', 'sw'],
    ],

    'files' => [
        'max_size_kb' => $appConfig['files']['max_size_kb'] ?? 5120,
        'allowed_types' => $appConfig['files']['allowed_types'] ?? ['pdf', 'jpg', 'jpeg', 'png'],
    ],

    'otp' => [
        'provider' => $appConfig['otp']['provider'] ?? 'email',
        'expiry_minutes' => $appConfig['otp']['expiry_minutes'] ?? 15,
    ],
];
