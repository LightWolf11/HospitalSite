<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/bootstrap.php';
require_once dirname(__DIR__) . '/includes/notifications.php';

header('Content-Type: application/json; charset=utf-8');

$name = trim((string) ($_POST['full_name'] ?? ''));
$email = trim((string) ($_POST['email'] ?? ''));
$phone = trim((string) ($_POST['phone'] ?? ''));
$position = trim((string) ($_POST['position'] ?? ''));
$experience = trim((string) ($_POST['experience'] ?? ''));
$message = trim((string) ($_POST['message'] ?? ''));

if (strlen($name) < 2 || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['ok' => false, 'error' => 'Укажите ФИО и корректный email'], JSON_UNESCAPED_UNICODE);
    exit;
}

$cvPath = null;
if (!empty($_FILES['cv']) && is_array($_FILES['cv']) && $_FILES['cv']['error'] !== UPLOAD_ERR_NO_FILE) {
    $cvPath = upload_pdf($_FILES['cv'], 'team', $config);
}

$st = $pdo->prepare(
    'INSERT INTO team_applications (full_name, email, phone, position, experience, message, cv_path, status, is_read)
     VALUES (?,?,?,?,?,?,?,?,?)'
);
$st->execute([$name, $email, $phone, $position, $experience, $message, $cvPath, 'new', 0]);
$tid = (int) $pdo->lastInsertId();
$sub = $position !== '' ? 'Должность: ' . $position : 'Новая заявка в команду';
notify_admin_users(
    $pdo,
    'team',
    'Анкета «В команду»: ' . $name,
    $sub,
    'admin/notifications.php?tab=team&view=' . $tid
);
echo json_encode(['ok' => true], JSON_UNESCAPED_UNICODE);
