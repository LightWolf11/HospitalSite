<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/bootstrap.php';

$user = current_user($pdo);
if (!$user || ($user['role'] ?? '') !== 'doctor' || empty($user['doctor_profile_id'])) {
    header('Location: login.php');
    exit;
}

$dpId = (int) $user['doctor_profile_id'];
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int) ($_POST['appointment_id'] ?? 0);
    $arrived = isset($_POST['patient_arrived']) ? 1 : 0;
    $comment = trim((string) ($_POST['doctor_comment'] ?? ''));
    if ($id > 0) {
        $chk = $pdo->prepare('SELECT id FROM appointments WHERE id = ? AND doctor_profile_id = ?');
        $chk->execute([$id, $dpId]);
        if ($chk->fetch()) {
            $pdo->prepare('UPDATE appointments SET patient_arrived = ?, doctor_comment = ? WHERE id = ? AND doctor_profile_id = ?')
                ->execute([$arrived, $comment, $id, $dpId]);
            $message = 'Сохранено';
        }
    }
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
$rows = $st->fetchAll();

$dp = $pdo->prepare('SELECT full_name FROM doctor_profiles WHERE id = ?');
$dp->execute([$dpId]);
$docName = $dp->fetchColumn();

$pageTitle = 'Кабинет врача — ' . (string) $docName;
$NAV_BASE = '..';
$ASSETS = '../assets/';
$extraCss = ['appointment.css'];
require dirname(__DIR__) . '/includes/partials/public_head.php';
require dirname(__DIR__) . '/includes/partials/public_nav.php';
?>
    <section class="app-page">
        <div class="container" style="max-width: 960px;">
            <?php if ($message): ?><p class="app-msg ok"><?= h($message) ?></p><?php endif; ?>
            <h1 class="section-title" style="margin-bottom: 0.25rem;">Записи пациентов</h1>
            <p class="section-subtitle"><?= h((string) $docName) ?></p>
            <?php foreach ($rows as $r): ?>
                <div class="app-card" style="margin-bottom: 1.25rem;">
                    <h2 style="font-size: 1.1rem; margin-top: 0; color: var(--dark-color);"><?= h($r['scheduled_at']) ?> · <?= h($r['patient_name']) ?></h2>
                    <p><small>Email:</small> <?= h($r['patient_email']) ?> · <small>Тел:</small> <?= h($r['patient_phone']) ?></p>
                    <?php if ($r['patient_note']): ?>
                        <p style="white-space:pre-wrap;"><small>Комментарий пациента:</small><br><?= h($r['patient_note']) ?></p>
                    <?php endif; ?>
                    <form method="post" class="appointment-form" style="margin-top: 1rem;">
                        <input type="hidden" name="appointment_id" value="<?= (int) $r['id'] ?>">
                        <div class="form-group">
                            <label><input type="checkbox" name="patient_arrived" value="1" <?= $r['patient_arrived'] ? 'checked' : '' ?>> Пациент пришёл</label>
                        </div>
                        <div class="form-group">
                            <label for="dc<?= (int) $r['id'] ?>">Комментарий врача (виден пациенту в личном кабинете)</label>
                            <textarea id="dc<?= (int) $r['id'] ?>" name="doctor_comment" rows="4"><?= h($r['doctor_comment'] ?? '') ?></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Сохранить</button>
                    </form>
                </div>
            <?php endforeach; ?>
            <?php if (!$rows): ?>
                <p style="color: var(--text-color); opacity: 0.75;">Пока нет записей.</p>
            <?php endif; ?>
        </div>
    </section>
<?php
require dirname(__DIR__) . '/includes/partials/public_footer.php';
?>
    <script src="../assets/js/main.js"></script>
</body>
</html>
