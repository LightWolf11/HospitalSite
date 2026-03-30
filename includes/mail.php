<?php
declare(strict_types=1);

require_once __DIR__ . '/smtp_mail.php';

function send_html_mail(array $config, string $toEmail, string $subject, string $htmlBody): bool
{
    if (!empty($config['mail']['smtp_host'])) {
        return smtp_send_html($config, $toEmail, $subject, $htmlBody);
    }

    $from = $config['mail']['from_email'] ?? 'noreply@localhost';
    $fromName = $config['mail']['from_name'] ?? 'Hospital';
    $headers = [
        'MIME-Version: 1.0',
        'Content-type: text/html; charset=UTF-8',
        'From: ' . encode_header_name($fromName) . ' <' . $from . '>',
        'Reply-To: ' . $from,
        'X-Mailer: PHP/' . PHP_VERSION,
    ];
    $encodedSubject = '=?UTF-8?B?' . base64_encode($subject) . '?=';
    return @mail($toEmail, $encodedSubject, $htmlBody, implode("\r\n", $headers));
}

function encode_header_name(string $name): string
{
    return '=?UTF-8?B?' . base64_encode($name) . '?=';
}
function email_site_layout(string $innerHtml, array $config, string $headerSubtitle): string
{
    $site = h($config['mail']['from_name'] ?? 'Клиника');
    $sub = h($headerSubtitle);

    return <<<HTML
<!DOCTYPE html>
<html lang="ru">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width"></head>
<body style="margin:0;font-family:Georgia,'Times New Roman',serif;background:#ede8f5;color:#1a1a1a;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#ede8f5;padding:32px 16px;">
  <tr><td align="center">
    <table width="560" cellpadding="0" cellspacing="0" style="max-width:560px;background:#ffffff;border-radius:14px;overflow:hidden;box-shadow:0 12px 40px rgba(136,18,190,.18);">
      <tr><td style="background:linear-gradient(135deg,#8812be,#8b7dff);color:#f7f2ea;padding:28px 32px;">
        <h1 style="margin:0;font-size:22px;font-weight:600;letter-spacing:.02em;">{$site}</h1>
        <p style="margin:8px 0 0;font-size:14px;opacity:.95;">{$sub}</p>
      </td></tr>
      <tr><td style="padding:28px 32px 36px;color:#333;">
        {$innerHtml}
      </td></tr>
    </table>
  </td></tr>
</table>
</body>
</html>
HTML;
}

function email_reply_template(string $recipientName, string $adminReply, array $config): string
{
    $site = h($config['mail']['from_name'] ?? 'Клиника');
    $body = nl2br(h($adminReply));
    $name = h($recipientName);
    $inner = <<<HTML
<p style="margin:0 0 16px;font-size:16px;">Здравствуйте, {$name}!</p>
<div style="font-size:15px;line-height:1.65;color:#333;border-left:4px solid #8812be;padding-left:16px;margin:16px 0;">
  {$body}
</div>
<p style="margin:24px 0 0;font-size:13px;color:#5c4d6e;">С уважением,<br>команда {$site}</p>
HTML;

    return email_site_layout($inner, $config, 'Ответ на ваше обращение');
}

function email_appointment_reminder(string $toName, string $whenHuman, string $secondLine, string $kind, array $config, bool $forDoctor = false): string
{
    $site = h($config['mail']['from_name'] ?? 'Клиника');
    $title = $kind === 'before5' ? 'Напоминание: приём через 5 минут' : 'Напоминание: время приёма';
    $n = h($toName);
    $w = h($whenHuman);
    $s = h($secondLine);
    if ($forDoctor) {
        $body = "<p style=\"margin:0 0 12px;font-size:15px;line-height:1.6;\">{$n}, напоминаем о приёме пациента <strong style=\"color:#631fac;\">{$s}</strong>.</p><p style=\"margin:0;font-size:15px;\"><strong>Время:</strong> {$w}</p>";
    } else {
        $body = "<p style=\"margin:0 0 12px;font-size:15px;line-height:1.6;\">{$n}, напоминаем о записи к врачу <strong style=\"color:#631fac;\">{$s}</strong>.</p><p style=\"margin:0;font-size:15px;\"><strong>Время:</strong> {$w}</p>";
    }
    $inner = $body . '<p style="margin:20px 0 0;font-size:13px;color:#5c4d6e;">' . $site . '</p>';

    return email_site_layout($inner, $config, $title);
}
