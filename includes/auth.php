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
