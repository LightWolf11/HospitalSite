<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/bootstrap.php';

$user = current_user($pdo);
if (!$user || !user_can_access_patient_area($user)) {
    header('Location: login.php');
    exit;
}

$message = '';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['change_password'])) {
        $current = (string) ($_POST['current_password'] ?? '');
        $new = (string) ($_POST['new_password'] ?? '');
        $confirm = (string) ($_POST['new_password_confirm'] ?? '');
        if ($current === '') {
            $error = 'Введите текущий пароль.';
        } elseif ($new !== $confirm) {
            $error = 'Новый пароль и подтверждение не совпадают.';
        } elseif (strlen($new) < 6) {
            $error = 'Новый пароль: не менее 6 символов.';
        } else {
            $ph = $pdo->prepare('SELECT password_hash FROM users WHERE id = ? LIMIT 1');
            $ph->execute([(int) $user['id']]);
            $row = $ph->fetch();
            if (!$row || !password_verify($current, (string) $row['password_hash'])) {
                $error = 'Неверный текущий пароль.';
            } else {
                $hash = password_hash($new, PASSWORD_DEFAULT);
                $pdo->prepare('UPDATE users SET password_hash = ? WHERE id = ?')->execute([$hash, (int) $user['id']]);
                $message = 'Пароль изменён.';
            }
        }
    } else {
        $name = trim((string) ($_POST['full_name'] ?? ''));
        $phone = trim((string) ($_POST['phone'] ?? ''));
        if (strlen($name) >= 2) {
            $pdo->prepare('UPDATE users SET full_name = ?, phone = ? WHERE id = ?')->execute([$name, $phone, (int) $user['id']]);
            $message = 'Профиль сохранён';
            $user = current_user($pdo);
        }
    }
}

$st = $pdo->prepare(
    'SELECT a.id, a.scheduled_at, a.status, a.patient_note, a.patient_arrived, a.doctor_comment,
            d.full_name AS doctor_name, d.specialty
     FROM appointments a
     JOIN doctor_profiles d ON d.id = a.doctor_profile_id
     WHERE a.patient_user_id = ?
     ORDER BY a.scheduled_at DESC'
);
$st->execute([(int) $user['id']]);
$appointments = $st->fetchAll();

$scope = notifications_type_filter_sql($user);
$ns = $pdo->prepare(
    'SELECT id, title, body, read_at, created_at FROM notifications WHERE user_id = ?'
    . $scope
    . ' ORDER BY id DESC LIMIT 30'
);
$ns->execute([(int) $user['id']]);
$notifications = $ns->fetchAll();

$pageTitle = 'Личный кабинет';
$NAV_BASE = '..';
$ASSETS = '../assets/';
$extraCss = [];
require dirname(__DIR__) . '/includes/partials/public_head.php';
require dirname(__DIR__) . '/includes/partials/public_nav.php';
?>
    <section class="app-page">
        <div class="container">
            <?php if ($message): ?><p class="app-msg ok"><?= h($message) ?></p><?php endif; ?>
            <?php if ($error): ?><p class="app-msg err"><?= h($error) ?></p><?php endif; ?>

            <div class="app-card" style="margin-bottom: 1.5rem;">
                <h2 class="section-title" style="font-size: 1.5rem;">Профиль</h2>
                <form method="post" class="cabinet-form cabinet-form--polish">
                    <div class="form-group cabinet-field">
                        <label for="full_name">ФИО</label>
                        <input type="text" id="full_name" name="full_name" required value="<?= h($user['full_name']) ?>" placeholder="Как к вам обращаться">
                    </div>
                    <div class="form-group cabinet-field">
                        <label for="phone">Телефон</label>
                        <input type="text" id="phone" name="phone" value="<?= h($user['phone'] ?? '') ?>" placeholder="+375 (__) ___-__-__" inputmode="tel">
                    </div>
                    <div class="form-group cabinet-field cabinet-field--muted">
                        <label>Email</label>
                        <input type="email" value="<?= h($user['email']) ?>" disabled title="Смена email через администратора">
                    </div>
                    <button type="submit" class="btn btn-primary">Сохранить</button>
                </form>
            </div>

            <div class="app-card" style="margin-bottom: 1.5rem;">
                <h2 class="section-title" style="font-size: 1.5rem;">Смена пароля</h2>
                <p class="cabinet-muted" style="margin-top: 0;">Укажите текущий пароль и новый (не короче 6 символов).</p>
                <form method="post" class="cabinet-form cabinet-form--polish" id="cabinet-password-form" autocomplete="off">
                    <input type="hidden" name="change_password" value="1">
                    <div class="form-group cabinet-field">
                        <label for="current_password">Текущий пароль</label>
                        <input type="password" id="current_password" name="current_password" required autocomplete="current-password" placeholder="••••••••">
                    </div>
                    <div class="form-group cabinet-field">
                        <label for="new_password">Новый пароль</label>
                        <input type="password" id="new_password" name="new_password" required minlength="6" autocomplete="new-password" placeholder="Не менее 6 символов">
                    </div>
                    <div class="form-group cabinet-field">
                        <label for="new_password_confirm">Подтверждение пароля</label>
                        <input type="password" id="new_password_confirm" name="new_password_confirm" required minlength="6" autocomplete="new-password" placeholder="Повторите новый пароль">
                    </div>
                    <p id="cabinet-pwd-hint" class="app-msg err" style="display: none;" role="alert"></p>
                    <button type="submit" class="btn btn-primary">Сменить пароль</button>
                </form>
            </div>

            <div class="app-card" id="cabinet-notifications" style="margin-bottom: 1.5rem;">
                <h2 class="section-title" style="font-size: 1.5rem;">Уведомления</h2>
                <?php if (!$notifications): ?>
                    <p class="cabinet-muted">Пока нет уведомлений.</p>
                <?php else: ?>
                    <?php foreach ($notifications as $n): ?>
                        <div class="notif-item <?= empty($n['read_at']) ? 'unread' : '' ?>">
                            <strong><?= h($n['title']) ?></strong>
                            <div class="notif-body"><?= nl2br(h($n['body'] ?? '')) ?></div>
                            <small class="notif-date"><?= h($n['created_at']) ?></small>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <div class="app-card">
                <h2 class="section-title" style="font-size: 1.5rem;">История записей</h2>
                <?php if (!$appointments): ?>
                    <p>Записей пока нет. <a href="../index.php#appointment">Записаться на приём</a></p>
                <?php else: ?>
                    <div style="overflow-x: auto;">
                        <table class="cabinet-table">
                            <thead>
                                <tr>
                                    <th>Дата и время</th>
                                    <th>Врач</th>
                                    <th>Статус</th>
                                    <th>Комментарий врача</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($appointments as $a): ?>
                                <tr>
                                    <td><?= h($a['scheduled_at']) ?></td>
                                    <td><?= h($a['doctor_name']) ?><br><small><?= h($a['specialty']) ?></small></td>
                                    <td><?= h($a['status']) ?><?= $a['patient_arrived'] ? ', пришёл' : '' ?></td>
                                    <td><?= $a['doctor_comment'] !== null && $a['doctor_comment'] !== '' ? h($a['doctor_comment']) : '—' ?></td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
<?php
require dirname(__DIR__) . '/includes/partials/public_footer.php';
?>
    <script src="../assets/js/main.js"></script>
    <script>
    (function () {
        var form = document.getElementById('cabinet-password-form');
        if (!form) return;
        var n = document.getElementById('new_password');
        var c = document.getElementById('new_password_confirm');
        var hint = document.getElementById('cabinet-pwd-hint');
        function checkMatch() {
            if (!n.value || !c.value) {
                hint.style.display = 'none';
                return;
            }
            if (n.value !== c.value) {
                hint.textContent = 'Новый пароль и подтверждение не совпадают.';
                hint.style.display = 'block';
            } else {
                hint.style.display = 'none';
            }
        }
        n.addEventListener('input', checkMatch);
        c.addEventListener('input', checkMatch);
        form.addEventListener('submit', function (e) {
            if (n.value !== c.value) {
                e.preventDefault();
                hint.textContent = 'Новый пароль и подтверждение не совпадают.';
                hint.style.display = 'block';
                c.focus();
            }
        });
    })();
    </script>
</body>
</html>
