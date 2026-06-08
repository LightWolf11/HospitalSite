<?php
declare(strict_types=1);

function smtp_phpmailer_bootstrap(): void
{
    static $done = false;
    if ($done) {
        return;
    }
    $src = dirname(__DIR__) . '/vendor/PHPMailer/src';
    require_once $src . '/Exception.php';
    require_once $src . '/PHPMailer.php';
    require_once $src . '/SMTP.php';
    $done = true;
}

function smtp_send_html(array $config, string $toEmail, string $subject, string $htmlBody): bool
{
    $mailCfg = $config['mail'] ?? [];
    $host = trim((string) ($mailCfg['smtp_host'] ?? ''));
    if ($host === '') {
        return false;
    }

    smtp_phpmailer_bootstrap();

    $mail = new \PHPMailer\PHPMailer\PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = $host;
        $mail->Port = (int) ($mailCfg['smtp_port'] ?? 587);

        $secure = strtolower((string) ($mailCfg['smtp_secure'] ?? 'tls'));
        if ($secure === 'ssl') {
            $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;
        } elseif ($secure === 'tls') {
            $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        } else {
            $mail->SMTPSecure = '';
            $mail->SMTPAutoTLS = false;
        }

        $user = (string) ($mailCfg['smtp_user'] ?? '');
        if ($user !== '') {
            $mail->SMTPAuth = true;
            $mail->Username = $user;
            $mail->Password = (string) ($mailCfg['smtp_pass'] ?? '');
        } else {
            $mail->SMTPAuth = false;
        }

        if (!empty($mailCfg['smtp_relaxed_ssl'])) {
            $mail->SMTPOptions = [
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true,
                ],
            ];
        }

        $debug = (int) ($mailCfg['smtp_debug'] ?? 0);
        if ($debug > 0) {
            $mail->SMTPDebug = $debug;
            $mail->Debugoutput = 'error_log';
        }

        $from = (string) ($mailCfg['from_email'] ?? 'noreply@localhost');
        $fromName = (string) ($mailCfg['from_name'] ?? '');
        $mail->setFrom($from, $fromName, true);
        $mail->CharSet = \PHPMailer\PHPMailer\PHPMailer::CHARSET_UTF8;
        $mail->isHTML(true);

        $mail->clearAddresses();
        $mail->addAddress($toEmail);
        $mail->Subject = $subject;
        $mail->Body = $htmlBody;
        $mail->AltBody = smtp_strip_html_for_alt($htmlBody);

        return $mail->send();
    } catch (\PHPMailer\PHPMailer\Exception $e) {
        error_log('PHPMailer: ' . $e->getMessage());
        return false;
    }
}

function smtp_strip_html_for_alt(string $html): string
{
    $t = preg_replace('~<br\s*/?>~i', "\n", $html);
    $t = strip_tags((string) $t);
    return html_entity_decode(trim($t), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
