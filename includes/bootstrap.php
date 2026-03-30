<?php
declare(strict_types=1);

$configPath = dirname(__DIR__) . '/config/config.php';
if (!is_readable($configPath)) {
    http_response_code(500);
    exit('Создайте config/config.php на основе config/config.sample.php');
}

$config = require $configPath;

session_name($config['app']['session_name'] ?? 'HOSPITAL_SESSID');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/db_schema_ensure.php';
db_ensure_app_schema($pdo);
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/mail.php';
