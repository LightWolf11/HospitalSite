<?php
declare(strict_types=1);


require_once dirname(__DIR__) . '/includes/bootstrap.php';
require_once dirname(__DIR__) . '/includes/notifications.php';

$before = $pdo->query(
    "SELECT a.*, u.email AS patient_email, u.full_name AS patient_name,
            d.full_name AS doctor_name, d.contact_email, d.user_id AS doctor_user_id
     FROM appointments a
     JOIN users u ON u.id = a.patient_user_id
     JOIN doctor_profiles d ON d.id = a.doctor_profile_id
     WHERE a.status IN ('pending','confirmed')
     AND a.reminder_before_sent = 0
     AND TIMESTAMPDIFF(MINUTE, NOW(), a.scheduled_at) = 5"
)->fetchAll();

foreach ($before as $row) {
    process_reminder_row($pdo, $config, $row, 'before5');
    $u = $pdo->prepare('UPDATE appointments SET reminder_before_sent = 1 WHERE id = ?');
    $u->execute([(int) $row['id']]);
}

$at = $pdo->query(
    "SELECT a.*, u.email AS patient_email, u.full_name AS patient_name,
            d.full_name AS doctor_name, d.contact_email, d.user_id AS doctor_user_id
     FROM appointments a
     JOIN users u ON u.id = a.patient_user_id
     JOIN doctor_profiles d ON d.id = a.doctor_profile_id
     WHERE a.status IN ('pending','confirmed')
     AND a.reminder_at_sent = 0
     AND NOW() >= a.scheduled_at
     AND NOW() < DATE_ADD(a.scheduled_at, INTERVAL 3 MINUTE)"
)->fetchAll();

foreach ($at as $row) {
    process_reminder_row($pdo, $config, $row, 'at');
    $u = $pdo->prepare('UPDATE appointments SET reminder_at_sent = 1 WHERE id = ?');
    $u->execute([(int) $row['id']]);
}

function process_reminder_row(PDO $pdo, array $config, array $row, string $kind): void
{
    $dt = new DateTime($row['scheduled_at']);
    $when = $dt->format('d.m.Y H:i');
    $subject = $kind === 'before5'
        ? 'Напоминание: приём через 5 минут'
        : 'Напоминание: время приёма';

    $htmlPatient = email_appointment_reminder(
        $row['patient_name'],
        $when,
        $row['doctor_name'],
        $kind,
        $config
    );
    if (!empty($row['patient_email'])) {
        send_html_mail($config, $row['patient_email'], $subject, $htmlPatient);
    }

    $htmlDoc = email_appointment_reminder(
        $row['doctor_name'],
        $when,
        $row['patient_name'],
        $kind,
        $config,
        true
    );
    if (!empty($row['contact_email'])) {
        send_html_mail($config, $row['contact_email'], $subject . ' — ' . $row['patient_name'], $htmlDoc);
    }

    $pid = (int) $row['patient_user_id'];
    notify_user(
        $pdo,
        $pid,
        'reminder',
        $subject,
        'Приём у ' . $row['doctor_name'] . ' в ' . $when,
        null
    );
    if (!empty($row['doctor_user_id'])) {
        notify_user(
            $pdo,
            (int) $row['doctor_user_id'],
            'reminder',
            $subject,
            'Пациент ' . $row['patient_name'] . ', ' . $when,
            null
        );
    }
}

echo "OK\n";
