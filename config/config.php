<?php
declare(strict_types=1);

$config = require __DIR__ . '/config.connection.php';
$mail = __DIR__ . '/config.mail.php';
if (is_readable($mail)) {
    $mailConfig = require $mail;
    if (is_array($mailConfig)) {
        $config = array_replace_recursive($config, $mailConfig);
    }
}

return $config;