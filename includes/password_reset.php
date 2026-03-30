<?php
declare(strict_types=1);

require_once __DIR__ . '/mail.php';

function password_reset_ttl_minutes(): int
{
    return 30;
}

function password_reset_issue(PDO $pdo, int $userId): string
{
    $raw = bin2hex(random_bytes(32));
    $hash = hash('sha256', $raw);
    $pdo->prepare('DELETE FROM password_resets WHERE user_id = ?')->execute([$userId]);
    $expires = (new DateTimeImmutable('+' . password_reset_ttl_minutes() . ' minutes'))->format('Y-m-d H:i:s');
    $st = $pdo->prepare('INSERT INTO password_resets (user_id, token_hash, expires_at) VALUES (?,?,?)');
    $st->execute([$userId, $hash, $expires]);

    return $raw;
}

function password_reset_user_id_by_token(PDO $pdo, string $rawToken): ?int
{
    if (!preg_match('/^[a-f0-9]{64}$/', $rawToken)) {
        return null;
    }
    $hash = hash('sha256', $rawToken);
    $st = $pdo->prepare('SELECT user_id FROM password_resets WHERE token_hash = ? AND expires_at > NOW() LIMIT 1');
    $st->execute([$hash]);
    $row = $st->fetch();

    return $row ? (int) $row['user_id'] : null;
}

function password_reset_send_mail(array $config, string $toEmail, string $toName, string $rawToken): bool
{
    $url = public_url('cabinet/reset_password.php?token=' . urlencode($rawToken), $config);
    $name = h($toName !== '' ? $toName : 'пользователь');
    $safeUrl = h($url);

    $inner = <<<HTML
<p style="margin:0 0 16px;font-size:16px;">Здравствуйте, {$name}!</p>
<p style="margin:0 0 20px;font-size:15px;line-height:1.6;color:#333;">Чтобы задать новый пароль, нажмите кнопку ниже. Ссылка действует около 30 минут.</p>
<p style="margin:0 0 24px;text-align:center;">
  <a href="{$safeUrl}" style="display:inline-block;background:linear-gradient(135deg,#8812be,#733fcc);color:#f7f2ea;text-decoration:none;font-weight:600;font-size:15px;padding:14px 28px;border-radius:12px;box-shadow:0 8px 24px rgba(136,18,190,.35);">Сбросить пароль</a>
</p>
<p style="margin:0 0 8px;font-size:12px;color:#5c4d6e;word-break:break-all;">Или скопируйте адрес:<br><a href="{$safeUrl}" style="color:#631fac;">{$safeUrl}</a></p>
<p style="margin:20px 0 0;font-size:13px;color:#5c4d6e;">Если вы не запрашивали сброс, проигнорируйте это письмо.</p>
HTML;

    $html = email_site_layout($inner, $config, 'Восстановление пароля');

    return send_html_mail($config, $toEmail, 'Восстановление пароля', $html);
}

function password_reset_apply(PDO $pdo, int $userId, string $newPassword): void
{
    $hash = password_hash($newPassword, PASSWORD_DEFAULT);
    $pdo->prepare('UPDATE users SET password_hash = ? WHERE id = ?')->execute([$hash, $userId]);
    $pdo->prepare('DELETE FROM password_resets WHERE user_id = ?')->execute([$userId]);
}

function password_reset_request_for_email(PDO $pdo, array $config, string $email): bool
{
    $email = trim($email);
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return false;
    }
    $st = $pdo->prepare('SELECT id, full_name FROM users WHERE email = ? LIMIT 1');
    $st->execute([$email]);
    $row = $st->fetch();
    if (!$row) {
        return true;
    }
    $token = password_reset_issue($pdo, (int) $row['id']);
    $ok = password_reset_send_mail($config, $email, (string) $row['full_name'], $token);
    if (!$ok) {
        error_log('password_reset: не удалось отправить письмо на ' . $email);
    }

    return true;
}
