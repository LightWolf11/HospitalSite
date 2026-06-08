<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/bootstrap.php';

$user = current_user($pdo);
if (!$user || !user_can_access_admin($user)) {
    header('Location: login.php');
    exit;
}

$adminNoteBadge = (int) $pdo->query('SELECT COUNT(*) FROM feedback_messages WHERE is_read = 0')->fetchColumn()
    + (int) $pdo->query('SELECT COUNT(*) FROM team_applications WHERE is_read = 0')->fetchColumn();
