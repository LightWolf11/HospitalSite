<?php
return [
    'db' => [
        'host' => '127.0.0.1',
        'name' => 'hospital_site',
        'user' => 'root',
        'pass' => '',
        'charset' => 'utf8mb4',
    ],
    'app' => [
        'base_url' => 'http://hospitalsite',
        'public_path' => '',
        'session_name' => 'HOSPITAL_SESSID',
    ],
    'upload' => [
        'max_bytes' => 10 * 1024 * 1024,
        'allowed_mime' => ['image/jpeg', 'image/png', 'image/webp', 'image/gif', 'application/pdf'],
    ],
];
