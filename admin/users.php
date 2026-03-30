<?php
declare(strict_types=1);
require __DIR__ . '/_auth.php';

if (!is_superadmin($user ?? null)) {
    http_response_code(403);
    $pageTitle = 'Доступ запрещён';
    $NAV_BASE = '..';
    $ASSETS = '../assets/';
    $ADMIN_ACTIVE = 'users';
    require dirname(__DIR__) . '/includes/partials/public_head.php';
    require dirname(__DIR__) . '/includes/partials/public_nav.php';
    require dirname(__DIR__) . '/includes/partials/admin_subnav.php';
    ?>
    <section class="app-page admin-panel">
        <div class="container" style="max-width: 900px;">
            <h1 class="section-title">403</h1>
            <p class="section-subtitle">Доступ к управлению администраторами разрешён только для <code>admin@hospital.local</code>.</p>
        </div>
    </section>
    <?php
    require dirname(__DIR__) . '/includes/partials/public_footer.php';
    ?>
    <script src="../assets/js/main.js"></script>
    </body>
    </html>
    <?php
    exit;
}

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = (string) ($_POST['action'] ?? '');
    try {
        if ($action === 'set_admin_by_email' || $action === '') {
            $email = strtolower(trim((string) ($_POST['email'] ?? '')));
            $make = (int) ($_POST['make_admin'] ?? 0) === 1;
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new RuntimeException('Укажите корректный email');
            }

            if ($email === 'admin@hospital.local') {
                $pdo->prepare('UPDATE users SET is_admin = 1 WHERE email = ?')->execute([$email]);
                $message = 'Суперадмин всегда админ';
            } else {
                $st = $pdo->prepare('SELECT id FROM users WHERE email = ?');
                $st->execute([$email]);
                $row = $st->fetch();
                if (!$row) {
                    throw new RuntimeException('Пользователь не найден');
                }
                $pdo->prepare('UPDATE users SET is_admin = ? WHERE email = ?')->execute([$make ? 1 : 0, $email]);
                $message = $make ? 'Права администратора выданы' : 'Права администратора сняты';
            }
        } elseif ($action === 'toggle_admin') {
            $id = (int) ($_POST['id'] ?? 0);
            $make = (int) ($_POST['make_admin'] ?? 0) === 1;
            if ($id < 1) throw new RuntimeException('Некорректный id');

            $st = $pdo->prepare('SELECT email FROM users WHERE id = ? LIMIT 1');
            $st->execute([$id]);
            $row = $st->fetch();
            if (!$row) throw new RuntimeException('Пользователь не найден');

            $email = strtolower(trim((string) $row['email'] ?? ''));
            if ($email === 'admin@hospital.local') {
                $pdo->prepare('UPDATE users SET is_admin = 1 WHERE id = ?')->execute([$id]);
                $message = 'Суперадмин всегда админ';
            } else {
                $pdo->prepare('UPDATE users SET is_admin = ? WHERE id = ?')->execute([$make ? 1 : 0, $id]);
                $message = $make ? 'Права администратора выданы' : 'Права администратора сняты';
            }
        } elseif ($action === 'delete_user') {
            $id = (int) ($_POST['id'] ?? 0);
            if ($id < 1) throw new RuntimeException('Некорректный id');

            $st = $pdo->prepare('SELECT id, email, role, doctor_profile_id FROM users WHERE id = ? LIMIT 1');
            $st->execute([$id]);
            $u = $st->fetch();
            if (!$u) throw new RuntimeException('Пользователь не найден');

            $email = strtolower(trim((string) ($u['email'] ?? '')));
            if ($email === 'admin@hospital.local') {
                throw new RuntimeException('Суперадмина удалять нельзя');
            }

            $pdo->beginTransaction();

            $dpId = !empty($u['doctor_profile_id']) ? (int) $u['doctor_profile_id'] : 0;
            if ($dpId > 0) {
                $pdo->prepare('UPDATE doctor_profiles SET user_id = NULL WHERE id = ?')->execute([$dpId]);
            }

            $pdo->prepare('DELETE FROM users WHERE id = ?')->execute([$id]);

            $pdo->commit();
            $message = 'Пользователь удалён';
        } elseif ($action === 'update_user') {
            $id = (int) ($_POST['id'] ?? 0);
            if ($id < 1) throw new RuntimeException('Некорректный id');

            $email = strtolower(trim((string) ($_POST['email'] ?? '')));
            $fullName = trim((string) ($_POST['full_name'] ?? ''));
            $phone = trim((string) ($_POST['phone'] ?? ''));
            $role = trim((string) ($_POST['role'] ?? 'patient'));
            $doctorProfileId = (int) ($_POST['doctor_profile_id'] ?? 0);
            $makeAdmin = (int) ($_POST['make_admin'] ?? 0) === 1;

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new RuntimeException('Укажите корректный email');
            }
            if (strlen($fullName) < 2) {
                throw new RuntimeException('Укажите ФИО');
            }
            if (!in_array($role, ['patient', 'doctor'], true)) {
                throw new RuntimeException('Некорректная роль');
            }

            $st = $pdo->prepare('SELECT id, email, role, doctor_profile_id FROM users WHERE id = ? LIMIT 1');
            $st->execute([$id]);
            $editU = $st->fetch();
            if (!$editU) throw new RuntimeException('Пользователь не найден');

            $prevDpId = !empty($editU['doctor_profile_id']) ? (int) $editU['doctor_profile_id'] : 0;
            $isSuper = strtolower(trim((string) ($editU['email'] ?? ''))) === 'admin@hospital.local';
            if ($isSuper) {
                $makeAdmin = true;
            }

            $st = $pdo->prepare('SELECT id FROM users WHERE email = ? AND id <> ?');
            $st->execute([$email, $id]);
            if ($st->fetch()) {
                throw new RuntimeException('Этот email уже занят другим пользователем');
            }

            if ($role === 'doctor') {
                if ($doctorProfileId < 1) {
                    throw new RuntimeException('Для роли врача выберите doctor_profile_id');
                }
                $st = $pdo->prepare('SELECT id, user_id FROM doctor_profiles WHERE id = ? LIMIT 1');
                $st->execute([$doctorProfileId]);
                $dp = $st->fetch();
                if (!$dp) {
                    throw new RuntimeException('doctor_profile_id не найден');
                }

                $existingUserId = !empty($dp['user_id']) ? (int) $dp['user_id'] : 0;
                if ($existingUserId > 0 && $existingUserId !== $id) {
                    throw new RuntimeException('Эта анкета врача уже привязана к другому пользователю');
                }

                $st = $pdo->prepare('SELECT id FROM users WHERE doctor_profile_id = ? AND id <> ?');
                $st->execute([$doctorProfileId, $id]);
                if ($st->fetch()) {
                    throw new RuntimeException('Этот doctor_profile_id уже используется другим пользователем');
                }
            } else {
                $doctorProfileId = 0;
            }

            $newPass = trim((string) ($_POST['new_password'] ?? ''));
            $newPass2 = trim((string) ($_POST['new_password_confirm'] ?? ''));
            $passProvided = $newPass !== '' || $newPass2 !== '';

            $pdo->beginTransaction();

            if ($role === 'doctor') {
                $pdo->prepare('UPDATE users SET email = ?, full_name = ?, phone = ?, role = ?, doctor_profile_id = ?, is_admin = ? WHERE id = ?')
                    ->execute([$email, $fullName, $phone, $role, $doctorProfileId, $makeAdmin ? 1 : 0, $id]);

                // Снять привязку со старого профиля, если была
                if ($prevDpId > 0 && $prevDpId !== $doctorProfileId) {
                    $pdo->prepare('UPDATE doctor_profiles SET user_id = NULL WHERE id = ?')->execute([$prevDpId]);
                }

                // Привязать выбранный профайл к этому пользователю
                $pdo->prepare('UPDATE doctor_profiles SET user_id = ? WHERE id = ?')->execute([$id, $doctorProfileId]);
            } else {
                $pdo->prepare('UPDATE users SET email = ?, full_name = ?, phone = ?, role = ?, doctor_profile_id = NULL, is_admin = ? WHERE id = ?')
                    ->execute([$email, $fullName, $phone, $role, $makeAdmin ? 1 : 0, $id]);

                if ($prevDpId > 0) {
                    $pdo->prepare('UPDATE doctor_profiles SET user_id = NULL WHERE id = ?')->execute([$prevDpId]);
                }
            }

            if ($passProvided) {
                if (strlen($newPass) < 6) {
                    throw new RuntimeException('Пароль должен быть не короче 6 символов');
                }
                if ($newPass !== $newPass2) {
                    throw new RuntimeException('Новый пароль и подтверждение не совпадают');
                }
                $hash = password_hash($newPass, PASSWORD_DEFAULT);
                $pdo->prepare('UPDATE users SET password_hash = ? WHERE id = ?')->execute([$hash, $id]);
            }

            $pdo->commit();
            $message = 'Пользователь сохранён';
        } else {
            throw new RuntimeException('Неизвестное действие');
        }
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        $error = $e->getMessage();
    }
}

$pageTitle = 'Пользователи — админ-панель';
$NAV_BASE = '..';
$ASSETS = '../assets/';
$ADMIN_ACTIVE = 'users';
$extraCss = ['appointment.css'];
require dirname(__DIR__) . '/includes/partials/public_head.php';
require dirname(__DIR__) . '/includes/partials/public_nav.php';
require dirname(__DIR__) . '/includes/partials/admin_subnav.php';

$editUser = null;
if (isset($_GET['edit'])) {
    $editId = (int) $_GET['edit'];
    if ($editId > 0) {
        $st = $pdo->prepare('SELECT id, email, full_name, phone, role, is_admin, doctor_profile_id FROM users WHERE id = ? LIMIT 1');
        $st->execute([$editId]);
        $editUser = $st->fetch() ?: null;
    }
}

$usersList = $pdo->query(
    "SELECT u.id, u.email, u.full_name, u.phone, u.role, u.is_admin, u.doctor_profile_id,
            d.full_name AS doctor_name
     FROM users u
     LEFT JOIN doctor_profiles d ON d.id = u.doctor_profile_id
     WHERE u.role = 'doctor' OR COALESCE(u.is_admin, 0) = 1
     ORDER BY (u.role = 'doctor') DESC, COALESCE(u.is_admin, 0) DESC, u.id DESC"
)->fetchAll();

$doctorProfiles = $pdo->query(
    "SELECT id, full_name, specialty, user_id
     FROM doctor_profiles
     ORDER BY sort_order ASC, id ASC"
)->fetchAll();

$editRole = $editUser ? (string) ($editUser['role'] ?? 'patient') : 'patient';
$editIsSuper = $editUser ? (strtolower(trim((string) ($editUser['email'] ?? ''))) === 'admin@hospital.local') : false;
$editPrevDpId = $editUser && !empty($editUser['doctor_profile_id']) ? (int) $editUser['doctor_profile_id'] : 0;
?>
<section class="app-page admin-panel">
    <div class="container" style="max-width: 900px;">
        <h1 class="section-title" style="margin-bottom: .4rem;">Пользователи</h1>
        <p class="section-subtitle" style="margin-bottom: 1.25rem;">
            Список врачей и пользователей с админ-правами. Можно выдать/снять админку, отредактировать или удалить пользователя.
        </p>

        <?php if ($message): ?><p class="app-msg ok"><?= h($message) ?></p><?php endif; ?>
        <?php if ($error): ?><p class="app-msg err"><?= h($error) ?></p><?php endif; ?>

        <form method="post" class="admin-detail" style="margin-top: 1rem;">
            <input type="hidden" name="action" value="set_admin_by_email">
            <label>Email пользователя</label>
            <input type="email" name="email" required placeholder="user@example.com">
            <label style="margin-top:.75rem;display:block;">
                <input type="checkbox" name="make_admin" value="1"> Выдать права администратора
            </label>
            <p style="margin-top: 1rem;">
                <button type="submit" class="btn-admin">Сохранить</button>
            </p>
        </form>

        <div style="margin-top: 1.5rem;">
            <h2 class="section-title" style="font-size: 1.1rem; margin-bottom: .5rem;">Список</h2>
            <?php if (!$usersList): ?>
                <p class="cabinet-muted">Пока никого нет.</p>
            <?php else: ?>
                <div style="overflow-x:auto;">
                    <table class="admin-data">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Email</th>
                                <th>ФИО</th>
                                <th>Роль</th>
                                <th>Админ</th>
                                <th>Врач</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($usersList as $r): ?>
                                <tr>
                                    <td><?= (int) $r['id'] ?></td>
                                    <td><?= h($r['email']) ?></td>
                                    <td><?= h($r['full_name'] ?? '') ?></td>
                                    <td><?= h((string) $r['role']) ?></td>
                                    <td><?= !empty($r['is_admin']) ? 'да' : 'нет' ?></td>
                                    <td>
                                        <?php
                                        $isDoctor = (string) ($r['role'] ?? '') === 'doctor';
                                        ?>
                                        <?= $isDoctor ? h((string) ($r['doctor_name'] ?? '')) : '—' ?>
                                    </td>
                                    <td>
                                        <?php
                                        $isSuper = strtolower(trim((string) ($r['email'] ?? ''))) === 'admin@hospital.local';
                                        $canToggle = !$isSuper;
                                        $isAdminNow = !empty($r['is_admin']) ? 1 : 0;
                                        ?>
                                        <div style="display:flex; gap:.5rem; flex-wrap: wrap;">
                                            <form method="post" style="display:inline;">
                                                <input type="hidden" name="action" value="toggle_admin">
                                                <input type="hidden" name="id" value="<?= (int) $r['id'] ?>">
                                                <input type="hidden" name="make_admin" value="<?= $isAdminNow ? 0 : 1 ?>">
                                                <button type="submit" class="btn-admin btn-admin--muted" <?= $canToggle ? '' : 'disabled' ?>>
                                                    <?= $isAdminNow ? 'Снять админку' : 'Дать админку' ?>
                                                </button>
                                            </form>
                                            <a class="btn-admin" href="users.php?edit=<?= (int) $r['id'] ?>">Редактировать</a>
                                            <form method="post" style="display:inline;" onsubmit="return confirm('Удалить пользователя?');">
                                                <input type="hidden" name="action" value="delete_user">
                                                <input type="hidden" name="id" value="<?= (int) $r['id'] ?>">
                                                <button type="submit" class="btn-admin btn-admin--danger" <?= $isSuper ? 'disabled' : '' ?>>
                                                    Удалить
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>

        <?php if ($editUser): ?>
            <div class="admin-detail" style="margin-top: 2rem;">
                <h2 class="section-title" style="font-size: 1.1rem; margin-top: 0;">Редактирование</h2>
                <form method="post">
                    <input type="hidden" name="action" value="update_user">
                    <input type="hidden" name="id" value="<?= (int) $editUser['id'] ?>">

                    <label>Email</label>
                    <input type="email" name="email" required value="<?= h($editUser['email']) ?>" <?= $editIsSuper ? 'readonly' : '' ?>>

                    <label style="margin-top: .75rem; display:block;">ФИО</label>
                    <input type="text" name="full_name" required value="<?= h($editUser['full_name'] ?? '') ?>" placeholder="ФИО">

                    <label style="margin-top: .75rem; display:block;">Телефон</label>
                    <input type="text" name="phone" value="<?= h($editUser['phone'] ?? '') ?>" placeholder="+375 (__) ___-__-__" inputmode="tel">

                    <label style="margin-top: .75rem; display:block;">Роль</label>
                    <select name="role">
                        <option value="patient" <?= $editRole === 'patient' ? 'selected' : '' ?>>patient</option>
                        <option value="doctor" <?= $editRole === 'doctor' ? 'selected' : '' ?>>doctor</option>
                    </select>

                    <label style="margin-top: .75rem; display:block;">doctor_profile_id</label>
                    <select name="doctor_profile_id" <?= $editRole === 'doctor' ? '' : 'disabled' ?>>
                        <option value="0">— не задан</option>
                        <?php foreach ($doctorProfiles as $dp): ?>
                            <?php
                            $dpId = (int) $dp['id'];
                            $dpUserId = !empty($dp['user_id']) ? (int) $dp['user_id'] : 0;
                            $disabled = false;
                            if ($dpUserId > 0 && $dpUserId !== (int) $editUser['id']) {
                                $disabled = true;
                            }
                            $selected = $dpId > 0 && $dpId === $editPrevDpId;
                            ?>
                            <option value="<?= $dpId ?>" <?= $selected ? 'selected' : '' ?> <?= $disabled ? 'disabled' : '' ?>>
                                <?= h((string) ($dp['full_name'] ?? '')) ?>
                                <?php if (!empty($dp['specialty'])): ?> (<?= h((string) $dp['specialty']) ?>)<?php endif; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <label style="margin-top:.75rem;display:block;">
                        <input type="checkbox" name="make_admin" value="1" <?= !empty($editUser['is_admin']) ? 'checked' : '' ?> <?= $editIsSuper ? 'disabled' : '' ?>>
                        Админ
                    </label>

                    <label style="margin-top: .75rem; display:block;">Новый пароль (необязательно)</label>
                    <input type="password" name="new_password" value="">
                    <label style="margin-top: .5rem; display:block;">Повтор нового пароля</label>
                    <input type="password" name="new_password_confirm" value="">

                    <p style="margin-top: 1rem;">
                        <button type="submit" class="btn-admin">Сохранить</button>
                        <a class="btn-admin btn-admin--muted" href="users.php" style="margin-left:.75rem;">Отмена</a>
                    </p>
                </form>
            </div>
        <?php endif; ?>
    </div>
</section>
<?php
require dirname(__DIR__) . '/includes/partials/public_footer.php';
?>
<script src="../assets/js/main.js"></script>
</body>
</html>

