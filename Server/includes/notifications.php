<?php
declare(strict_types=1);

function notify_user(PDO $pdo, int $userId, string $type, string $title, string $body = '', ?string $link = null): void
{
    $st = $pdo->prepare(
        'INSERT INTO notifications (user_id, type, title, body, link) VALUES (?,?,?,?,?)'
    );
    $st->execute([$userId, $type, $title, $body, $link]);
}

function notify_admin_users(PDO $pdo, string $type, string $title, string $body = '', ?string $link = null): void
{
    $st = $pdo->query("SELECT id FROM users WHERE role = 'admin' OR is_admin = 1");
    $seen = [];
    foreach ($st->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $id = (int) ($row['id'] ?? 0);
        if ($id < 1 || isset($seen[$id])) {
            continue;
        }
        $seen[$id] = true;
        notify_user($pdo, $id, $type, $title, $body, $link);
    }
}
