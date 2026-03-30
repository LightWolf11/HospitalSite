<?php
declare(strict_types=1);

function current_user(PDO $pdo): ?array
{
    if (empty($_SESSION['user_id'])) {
        return null;
    }
    $st = $pdo->prepare(
        'SELECT id, email, full_name, phone, role, is_admin, doctor_profile_id FROM users WHERE id = ? LIMIT 1'
    );
    $st->execute([(int) $_SESSION['user_id']]);
    $u = $st->fetch();
    return $u ?: null;
}

function user_can_access_admin(?array $u): bool
{
    if (!$u) {
        return false;
    }
    if (($u['role'] ?? '') === 'admin') {
        return true;
    }
    return !empty($u['is_admin']);
}


function user_can_access_patient_area(?array $u): bool
{
    if (!$u) {
        return false;
    }
    $r = $u['role'] ?? '';
    if ($r === 'doctor') {
        return false;
    }
    return $r === 'patient' || $r === 'admin' || !empty($u['is_admin']);
}

function require_login(PDO $pdo): array
{
    $u = current_user($pdo);
    if (!$u) {
        json_out(['ok' => false, 'error' => 'Требуется авторизация'], 401);
    }
    return $u;
}

function require_role(PDO $pdo, string $role): array
{
    $u = require_login($pdo);
    if (($u['role'] ?? '') !== $role) {
        json_out(['ok' => false, 'error' => 'Недостаточно прав'], 403);
    }
    return $u;
}

function login_user(int $userId): void
{
    $_SESSION['user_id'] = $userId;
    session_regenerate_id(true);
}

function logout_user(): void
{
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $p = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
    }
    session_destroy();
}
