<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/bootstrap.php';
require_once dirname(__DIR__) . '/includes/notifications.php';
require_once dirname(__DIR__) . '/includes/password_reset.php';

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$input = [];
if ($method === 'POST' || $method === 'PUT' || $method === 'PATCH') {
    $input = require_post_json();
}
$action = $_GET['action'] ?? ($input['action'] ?? '');

switch ($action) {
    case 'register':
        handle_register($pdo, $input, $config);
        break;
    case 'login':
        handle_login($pdo, $input);
        break;
    case 'password_forgot':
        handle_password_forgot($pdo, $input, $config);
        break;
    case 'password_reset':
        handle_password_reset($pdo, $input);
        break;
    case 'logout':
        handle_logout();
        break;
    case 'me':
        handle_me($pdo);
        break;
    case 'profile_update':
        handle_profile_update($pdo, $input);
        break;
    case 'doctors_public':
        handle_doctors_public($pdo, $config);
        break;
    case 'services_public':
        handle_services_public($pdo, $config);
        break;
    case 'doctors_options':
        handle_doctors_options($pdo);
        break;
    case 'appointment_create':
        handle_appointment_create($pdo, $input, $config);
        break;
    case 'my_appointments':
        handle_my_appointments($pdo);
        break;
    case 'feedback':
        handle_feedback($pdo, $input);
        break;
    case 'team_apply':
        handle_team_apply_json($pdo, $input);
        break;
    case 'doctor_appointments':
        handle_doctor_appointments($pdo);
        break;
    case 'doctor_appointment_update':
        handle_doctor_appointment_update($pdo, $input);
        break;
    case 'notifications':
        handle_notifications_list($pdo);
        break;
    case 'notification_read':
        handle_notification_read($pdo, $input);
        break;
    case 'notifications_read_all':
        handle_notifications_read_all($pdo);
        break;
    default:
        json_out(['ok' => false, 'error' => 'Неизвестное действие'], 404);
}

function handle_register(PDO $pdo, array $in, array $config): void
{
    $email = trim((string) ($in['email'] ?? ''));
    $pass = (string) ($in['password'] ?? '');
    $name = trim((string) ($in['full_name'] ?? ''));
    $phone = trim((string) ($in['phone'] ?? ''));
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($pass) < 6 || strlen($name) < 2) {
        json_out(['ok' => false, 'error' => 'Проверьте email, пароль (от 6 символов) и ФИО'], 400);
    }
    $st = $pdo->prepare('SELECT id FROM users WHERE email = ?');
    $st->execute([$email]);
    if ($st->fetch()) {
        json_out(['ok' => false, 'error' => 'Пользователь с таким email уже есть'], 409);
    }
    $hash = password_hash($pass, PASSWORD_DEFAULT);
    $st = $pdo->prepare(
        'INSERT INTO users (email, password_hash, full_name, phone, role) VALUES (?,?,?,?,?)'
    );
    $st->execute([$email, $hash, $name, $phone, 'patient']);
    $id = (int) $pdo->lastInsertId();
    login_user($id);
    json_out(['ok' => true, 'user' => ['id' => $id, 'email' => $email, 'full_name' => $name, 'role' => 'patient']]);
}

function handle_password_forgot(PDO $pdo, array $in, array $config): void
{
    $email = trim((string) ($in['email'] ?? ''));
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        json_out(['ok' => false, 'error' => 'Укажите корректный email'], 400);
    }
    password_reset_request_for_email($pdo, $config, $email);
    json_out([
        'ok' => true,
        'message' => 'Если email зарегистрирован, мы отправили ссылку для сброса пароля.',
    ]);
}

function handle_password_reset(PDO $pdo, array $in): void
{
    $token = trim((string) ($in['token'] ?? ''));
    $pass = (string) ($in['password'] ?? '');
    $pass2 = (string) ($in['password_confirm'] ?? $pass);
    if (!preg_match('/^[a-f0-9]{64}$/', $token)) {
        json_out(['ok' => false, 'error' => 'Недействительная ссылка'], 400);
    }
    if (strlen($pass) < 6) {
        json_out(['ok' => false, 'error' => 'Пароль не короче 6 символов'], 400);
    }
    if ($pass !== $pass2) {
        json_out(['ok' => false, 'error' => 'Пароли не совпадают'], 400);
    }
    $uid = password_reset_user_id_by_token($pdo, $token);
    if (!$uid) {
        json_out(['ok' => false, 'error' => 'Ссылка устарела или уже использована'], 400);
    }
    password_reset_apply($pdo, $uid, $pass);
    json_out(['ok' => true, 'message' => 'Пароль обновлён. Можно войти.']);
}

function handle_login(PDO $pdo, array $in): void
{
    $email = trim((string) ($in['email'] ?? ''));
    $pass = (string) ($in['password'] ?? '');
    $st = $pdo->prepare(
        'SELECT id, password_hash, email, full_name, phone, role, is_admin, doctor_profile_id FROM users WHERE email = ?'
    );
    $st->execute([$email]);
    $u = $st->fetch();
    if (!$u || !password_verify($pass, $u['password_hash'])) {
        json_out(['ok' => false, 'error' => 'Неверный email или пароль'], 401);
    }
    login_user((int) $u['id']);
    unset($u['password_hash']);
    json_out(['ok' => true, 'user' => $u]);
}

function handle_logout(): void
{
    logout_user();
    json_out(['ok' => true]);
}

function handle_me(PDO $pdo): void
{
    $u = current_user($pdo);
    if (!$u) {
        json_out(['ok' => true, 'user' => null]);
    }
    json_out(['ok' => true, 'user' => $u]);
}

function handle_profile_update(PDO $pdo, array $in): void
{
    $u = require_login($pdo);
    if (!user_can_access_patient_area($u)) {
        json_out(['ok' => false, 'error' => 'Редактирование профиля недоступно для этой учётной записи'], 403);
    }
    $name = trim((string) ($in['full_name'] ?? $u['full_name']));
    $phone = trim((string) ($in['phone'] ?? $u['phone']));
    if (strlen($name) < 2) {
        json_out(['ok' => false, 'error' => 'Укажите ФИО'], 400);
    }
    $st = $pdo->prepare('UPDATE users SET full_name = ?, phone = ? WHERE id = ?');
    $st->execute([$name, $phone, (int) $u['id']]);
    $u2 = current_user($pdo);
    json_out(['ok' => true, 'user' => $u2]);
}

function handle_doctors_public(PDO $pdo, array $config): void
{
    $st = $pdo->query(
        'SELECT id, full_name, specialty, bio, photo_path, contact_email, contact_phone
         FROM doctor_profiles WHERE is_active = 1 ORDER BY sort_order ASC, id ASC'
    );
    $rows = $st->fetchAll();
    foreach ($rows as &$r) {
        if (!empty($r['photo_path'])) {
            $r['photo_url'] = public_upload_path($r['photo_path'], $config);
        } else {
            $r['photo_url'] = null;
        }
    }
    unset($r);
    json_out(['ok' => true, 'doctors' => $rows]);
}

function handle_services_public(PDO $pdo, array $config): void
{
    $st = $pdo->query(
        'SELECT id, title, description, image_path FROM services WHERE is_active = 1 ORDER BY sort_order ASC, id ASC'
    );
    $rows = $st->fetchAll();
    foreach ($rows as &$r) {
        if (!empty($r['image_path'])) {
            $r['image_url'] = public_upload_path($r['image_path'], $config);
        } else {
            $r['image_url'] = null;
        }
    }
    unset($r);
    json_out(['ok' => true, 'services' => $rows]);
}

function handle_doctors_options(PDO $pdo): void
{
    $st = $pdo->query(
        'SELECT id, full_name, specialty FROM doctor_profiles WHERE is_active = 1 ORDER BY full_name'
    );
    json_out(['ok' => true, 'doctors' => $st->fetchAll()]);
}

function handle_appointment_create(PDO $pdo, array $in, array $config): void
{
    $u = require_login($pdo);
    if (!user_can_access_patient_area($u)) {
        json_out(['ok' => false, 'error' => 'Запись на приём недоступна для этой учётной записи'], 403);
    }
    $doctorId = (int) ($in['doctor_profile_id'] ?? 0);
    $date = trim((string) ($in['date'] ?? ''));
    $time = trim((string) ($in['time'] ?? ''));
    $note = trim((string) ($in['message'] ?? ''));
    if ($doctorId < 1 || $date === '' || $time === '') {
        json_out(['ok' => false, 'error' => 'Выберите врача, дату и время'], 400);
    }
    $scheduled = $date . ' ' . $time . ':00';
    $dt = DateTime::createFromFormat('Y-m-d H:i:s', $scheduled);
    if (!$dt || $dt->format('Y-m-d H:i:s') !== $scheduled) {
        json_out(['ok' => false, 'error' => 'Некорректная дата или время'], 400);
    }
    if ($dt < new DateTime('now')) {
        json_out(['ok' => false, 'error' => 'Нельзя записаться на прошедшее время'], 400);
    }
    $st = $pdo->prepare('SELECT id, full_name, contact_email, user_id FROM doctor_profiles WHERE id = ? AND is_active = 1');
    $st->execute([$doctorId]);
    $doc = $st->fetch();
    if (!$doc) {
        json_out(['ok' => false, 'error' => 'Врач не найден'], 404);
    }
    $st = $pdo->prepare(
        'INSERT INTO appointments (patient_user_id, doctor_profile_id, scheduled_at, status, patient_note)
         VALUES (?,?,?,?,?)'
    );
    $st->execute([(int) $u['id'], $doctorId, $scheduled, 'pending', $note]);
    $apptId = (int) $pdo->lastInsertId();

    $human = $dt->format('d.m.Y H:i');
    $docName = $doc['full_name'];
    notify_user(
        $pdo,
        (int) $u['id'],
        'appointment',
        'Запись на приём создана',
        'Вы записаны к врачу ' . $docName . ' на ' . $human . '.',
        null
    );
    if (!empty($doc['user_id'])) {
        $pu = $pdo->prepare('SELECT full_name, email FROM users WHERE id = ?');
        $pu->execute([(int) $u['id']]);
        $pat = $pu->fetch();
        $pname = $pat['full_name'] ?? 'Пациент';
        notify_user(
            $pdo,
            (int) $doc['user_id'],
            'appointment',
            'Новая запись на приём',
            $pname . ' записан(а) на ' . $human . '.',
            null
        );
    }

    json_out(['ok' => true, 'appointment_id' => $apptId]);
}

function handle_my_appointments(PDO $pdo): void
{
    $u = require_login($pdo);
    if (!user_can_access_patient_area($u)) {
        json_out(['ok' => false, 'error' => 'Список записей недоступен для этой учётной записи'], 403);
    }
    $st = $pdo->prepare(
        'SELECT a.id, a.scheduled_at, a.status, a.patient_note, a.patient_arrived, a.doctor_comment,
                d.full_name AS doctor_name, d.specialty
         FROM appointments a
         JOIN doctor_profiles d ON d.id = a.doctor_profile_id
         WHERE a.patient_user_id = ?
         ORDER BY a.scheduled_at DESC'
    );
    $st->execute([(int) $u['id']]);
    json_out(['ok' => true, 'appointments' => $st->fetchAll()]);
}

function handle_feedback(PDO $pdo, array $in): void
{
    $name = trim((string) ($in['name'] ?? ''));
    $email = trim((string) ($in['email'] ?? ''));
    $message = trim((string) ($in['message'] ?? ''));
    if (strlen($name) < 2 || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($message) < 5) {
        json_out(['ok' => false, 'error' => 'Заполните имя, email и текст сообщения'], 400);
    }
    $uid = null;
    $me = current_user($pdo);
    if ($me) {
        $uid = (int) $me['id'];
    }
    $st = $pdo->prepare(
        'INSERT INTO feedback_messages (user_id, name, email, message, is_read) VALUES (?,?,?,?,0)'
    );
    $st->execute([$uid, $name, $email, $message]);
    $fid = (int) $pdo->lastInsertId();
    $preview = $message;
    if (function_exists('mb_strlen') && function_exists('mb_substr') && mb_strlen($message) > 280) {
        $preview = mb_substr($message, 0, 280) . '…';
    } elseif (strlen($message) > 280) {
        $preview = substr($message, 0, 277) . '…';
    }
    notify_admin_users(
        $pdo,
        'feedback',
        'Обратная связь: ' . $name,
        $preview,
        'admin/notifications.php?tab=feedback&view=' . $fid
    );
    json_out(['ok' => true]);
}

function handle_team_apply_json(PDO $pdo, array $in): void
{
    $name = trim((string) ($in['full_name'] ?? ''));
    $email = trim((string) ($in['email'] ?? ''));
    $phone = trim((string) ($in['phone'] ?? ''));
    $position = trim((string) ($in['position'] ?? ''));
    $experience = trim((string) ($in['experience'] ?? ''));
    $message = trim((string) ($in['message'] ?? ''));
    if (strlen($name) < 2 || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        json_out(['ok' => false, 'error' => 'Укажите ФИО и корректный email'], 400);
    }
    $st = $pdo->prepare(
        'INSERT INTO team_applications (full_name, email, phone, position, experience, message, cv_path, status, is_read)
         VALUES (?,?,?,?,?,?,?,?,?)'
    );
    $st->execute([$name, $email, $phone, $position, $experience, $message, null, 'new', 0]);
    $tid = (int) $pdo->lastInsertId();
    $sub = $position !== '' ? 'Должность: ' . $position : 'Новая заявка в команду';
    notify_admin_users(
        $pdo,
        'team',
        'Анкета «В команду»: ' . $name,
        $sub,
        'admin/notifications.php?tab=team&view=' . $tid
    );
    json_out(['ok' => true]);
}

function handle_doctor_appointments(PDO $pdo): void
{
    $u = require_login($pdo);
    if (($u['role'] ?? '') !== 'doctor') {
        json_out(['ok' => false, 'error' => 'Доступно врачам'], 403);
    }
    $dpId = (int) ($u['doctor_profile_id'] ?? 0);
    if ($dpId < 1) {
        json_out(['ok' => false, 'error' => 'Профиль врача не привязан'], 403);
    }
    $st = $pdo->prepare(
        'SELECT a.id, a.scheduled_at, a.status, a.patient_note, a.patient_arrived, a.doctor_comment,
                u.full_name AS patient_name, u.email AS patient_email, u.phone AS patient_phone
         FROM appointments a
         JOIN users u ON u.id = a.patient_user_id
         WHERE a.doctor_profile_id = ?
         ORDER BY a.scheduled_at DESC'
    );
    $st->execute([$dpId]);
    json_out(['ok' => true, 'appointments' => $st->fetchAll()]);
}

function handle_doctor_appointment_update(PDO $pdo, array $in): void
{
    $u = require_login($pdo);
    if (($u['role'] ?? '') !== 'doctor') {
        json_out(['ok' => false, 'error' => 'Доступно врачам'], 403);
    }
    $dpId = (int) ($u['doctor_profile_id'] ?? 0);
    if ($dpId < 1) {
        json_out(['ok' => false, 'error' => 'Профиль врача не привязан'], 403);
    }
    $id = (int) ($in['id'] ?? 0);
    $arrived = isset($in['patient_arrived']) ? (int) (bool) $in['patient_arrived'] : null;
    $comment = isset($in['doctor_comment']) ? trim((string) $in['doctor_comment']) : null;
    if ($id < 1) {
        json_out(['ok' => false, 'error' => 'Нет id записи'], 400);
    }
    $chk = $pdo->prepare('SELECT id FROM appointments WHERE id = ? AND doctor_profile_id = ?');
    $chk->execute([$id, $dpId]);
    if (!$chk->fetch()) {
        json_out(['ok' => false, 'error' => 'Запись не найдена'], 404);
    }
    if ($arrived !== null && $comment !== null) {
        $st = $pdo->prepare('UPDATE appointments SET patient_arrived = ?, doctor_comment = ? WHERE id = ? AND doctor_profile_id = ?');
        $st->execute([$arrived, $comment, $id, $dpId]);
    } elseif ($arrived !== null) {
        $st = $pdo->prepare('UPDATE appointments SET patient_arrived = ? WHERE id = ? AND doctor_profile_id = ?');
        $st->execute([$arrived, $id, $dpId]);
    } elseif ($comment !== null) {
        $st = $pdo->prepare('UPDATE appointments SET doctor_comment = ? WHERE id = ? AND doctor_profile_id = ?');
        $st->execute([$comment, $id, $dpId]);
    } else {
        json_out(['ok' => false, 'error' => 'Нечего обновлять'], 400);
    }
    json_out(['ok' => true]);
}

function handle_notifications_list(PDO $pdo): void
{
    $u = require_login($pdo);
    $scope = notifications_type_filter_sql($u);
    $st = $pdo->prepare(
        'SELECT id, type, title, body, link, read_at, created_at FROM notifications WHERE user_id = ?'
        . $scope
        . ' ORDER BY id DESC LIMIT 100'
    );
    $st->execute([(int) $u['id']]);
    $rows = $st->fetchAll();
    $unread = 0;
    foreach ($rows as $r) {
        if (empty($r['read_at'])) {
            $unread++;
        }
    }
    json_out(['ok' => true, 'notifications' => $rows, 'unread_count' => $unread]);
}

function handle_notification_read(PDO $pdo, array $in): void
{
    $u = require_login($pdo);
    $id = (int) ($in['id'] ?? 0);
    if ($id < 1) {
        json_out(['ok' => false, 'error' => 'Нет id'], 400);
    }
    $st = $pdo->prepare('UPDATE notifications SET read_at = NOW() WHERE id = ? AND user_id = ?');
    $st->execute([$id, (int) $u['id']]);
    json_out(['ok' => true]);
}

function handle_notifications_read_all(PDO $pdo): void
{
    $u = require_login($pdo);
    $st = $pdo->prepare('UPDATE notifications SET read_at = NOW() WHERE user_id = ? AND read_at IS NULL');
    $st->execute([(int) $u['id']]);
    json_out(['ok' => true]);
}
