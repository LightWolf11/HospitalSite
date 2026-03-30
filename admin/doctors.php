<?php
declare(strict_types=1);
require __DIR__ . '/_auth.php';

$message = '';
$error = '';
if (!empty($_SESSION['flash_admin_doctors_ok'])) {
    $message = (string) $_SESSION['flash_admin_doctors_ok'];
    unset($_SESSION['flash_admin_doctors_ok']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    try {
        if ($action === 'create' || $action === 'edit') {
            $id = (int) ($_POST['id'] ?? 0);
            $fullName = trim((string) ($_POST['full_name'] ?? ''));
            $specialty = trim((string) ($_POST['specialty'] ?? ''));
            $bio = trim((string) ($_POST['bio'] ?? ''));
            $contactEmail = trim((string) ($_POST['contact_email'] ?? ''));
            $contactPhone = trim((string) ($_POST['contact_phone'] ?? ''));
            $sortOrder = (int) ($_POST['sort_order'] ?? 0);
            $isActive = isset($_POST['is_active']) ? 1 : 0;
            $loginEmail = trim((string) ($_POST['login_email'] ?? ''));
            $loginPass = (string) ($_POST['login_password'] ?? '');
            if (strlen($fullName) < 2) {
                throw new RuntimeException('Укажите ФИО');
            }
            $photoPath = null;
            $photoFile = $_FILES['photo'] ?? null;
            if ($photoFile && ($photoFile['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
                if ((int) ($photoFile['error'] ?? 0) !== UPLOAD_ERR_OK) {
                    throw new RuntimeException(upload_php_err_message((int) $photoFile['error']));
                }
                $photoPath = upload_image_or_throw($photoFile, 'doctors', $config);
            }
            if ($action === 'create') {
                $pdo->beginTransaction();
                $st = $pdo->prepare(
                    'INSERT INTO doctor_profiles (full_name, specialty, bio, photo_path, contact_email, contact_phone, sort_order, is_active)
                     VALUES (?,?,?,?,?,?,?,?)'
                );
                $st->execute([$fullName, $specialty, $bio, $photoPath, $contactEmail, $contactPhone, $sortOrder, $isActive]);
                $newId = (int) $pdo->lastInsertId();
                $bindEmail = $contactEmail !== '' ? $contactEmail : $loginEmail;
                $bindEmail = strtolower(trim($bindEmail));
                if ($bindEmail !== '' && filter_var($bindEmail, FILTER_VALIDATE_EMAIL)) {
                    $st = $pdo->prepare('SELECT id, doctor_profile_id, role FROM users WHERE email = ?');
                    $st->execute([$bindEmail]);
                    $urow = $st->fetch();
                    if ($urow) {
                        $role = (string) ($urow['role'] ?? '');
                        if ($role !== '' && $role !== 'doctor') {
                            throw new RuntimeException('Этот email уже используется обычным аккаунтом. Для врача укажите отдельный email (или привяжите email уже существующего врача).');
                        }
                        if (!empty($urow['doctor_profile_id']) && (int) $urow['doctor_profile_id'] !== $newId) {
                            throw new RuntimeException('Этот email уже привязан к другому врачу');
                        }
                        $uid = (int) $urow['id'];
                    } else {
                        if (strlen($loginPass) < 6) {
                            $pdo->commit();
                            $_SESSION['flash_admin_doctors_ok'] = 'Врач добавлен (привязка к email не выполнена: нет пароля для создания учётной записи)';
                            header('Location: doctors.php?edit=' . $newId);
                            exit;
                        }
                        $hash = password_hash($loginPass, PASSWORD_DEFAULT);
                        $st = $pdo->prepare('INSERT INTO users (email, password_hash, full_name, phone, role, doctor_profile_id) VALUES (?,?,?,?,?,?)');
                        $st->execute([$bindEmail, $hash, $fullName, $contactPhone, 'doctor', $newId]);
                        $uid = (int) $pdo->lastInsertId();
                    }
                    $pdo->prepare("UPDATE users SET role = 'doctor', doctor_profile_id = ? WHERE id = ?")->execute([$newId, $uid]);
                    $pdo->prepare('UPDATE doctor_profiles SET user_id = ? WHERE id = ?')->execute([$uid, $newId]);
                }
                $pdo->commit();
                $_SESSION['flash_admin_doctors_ok'] = 'Врач добавлен';
                header('Location: doctors.php?edit=' . $newId);
                exit;
            }
            $st = $pdo->prepare('SELECT photo_path FROM doctor_profiles WHERE id = ?');
            $st->execute([$id]);
            $old = $st->fetch();
            if (!$old) {
                throw new RuntimeException('Не найдено');
            }
            $finalPhoto = $photoPath !== null ? $photoPath : $old['photo_path'];
            $pdo->prepare(
                'UPDATE doctor_profiles SET full_name=?, specialty=?, bio=?, photo_path=?, contact_email=?, contact_phone=?, sort_order=?, is_active=? WHERE id=?'
            )->execute([$fullName, $specialty, $bio, $finalPhoto, $contactEmail, $contactPhone, $sortOrder, $isActive, $id]);

            $bindEmail = strtolower(trim($contactEmail));
            if ($bindEmail !== '' && filter_var($bindEmail, FILTER_VALIDATE_EMAIL)) {
                $pdo->beginTransaction();
                $st = $pdo->prepare('SELECT id, doctor_profile_id, role FROM users WHERE email = ?');
                $st->execute([$bindEmail]);
                $urow = $st->fetch();
                if ($urow) {
                    $role = (string) ($urow['role'] ?? '');
                    if ($role !== '' && $role !== 'doctor') {
                        throw new RuntimeException('Этот email уже используется обычным аккаунтом. Для врача укажите отдельный email (или привяжите email уже существующего врача).');
                    }
                    if (!empty($urow['doctor_profile_id']) && (int) $urow['doctor_profile_id'] !== $id) {
                        throw new RuntimeException('Этот email уже привязан к другому врачу');
                    }
                    $uid = (int) $urow['id'];
                    $pdo->prepare("UPDATE users SET role = 'doctor', doctor_profile_id = ? WHERE id = ?")->execute([$id, $uid]);
                    $pdo->prepare('UPDATE doctor_profiles SET user_id = ? WHERE id = ?')->execute([$uid, $id]);
                }
                $pdo->commit();
            }
            $_SESSION['flash_admin_doctors_ok'] = 'Сохранено';
            header('Location: doctors.php?edit=' . $id);
            exit;
        } elseif ($action === 'delete' && ($id = (int) ($_POST['id'] ?? 0)) > 0) {
            try {
                $pdo->prepare('DELETE FROM doctor_profiles WHERE id = ?')->execute([$id]);
                $_SESSION['flash_admin_doctors_ok'] = 'Удалено';
                header('Location: doctors.php');
                exit;
            } catch (PDOException $e) {
                $error = 'Нельзя удалить: есть записи с этим врачом. Сначала удалите или отмените записи.';
            }
        }
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        $error = $e->getMessage();
    }
}

$list = $pdo->query('SELECT * FROM doctor_profiles ORDER BY sort_order, id')->fetchAll();
$edit = null;
if (isset($_GET['edit'])) {
    $eid = (int) $_GET['edit'];
    $st = $pdo->prepare('SELECT * FROM doctor_profiles WHERE id = ?');
    $st->execute([$eid]);
    $row = $st->fetch();
    $edit = $row !== false ? $row : null;
}
$formPreview = is_array($edit) ? $edit : [];
$pageTitle = 'Врачи — админ-панель';
$NAV_BASE = '..';
$ASSETS = '../assets/';
$ADMIN_ACTIVE = 'doctors';
$extraCss = ['appointment.css', 'doctors.css'];
require dirname(__DIR__) . '/includes/partials/public_head.php';
require dirname(__DIR__) . '/includes/partials/public_nav.php';
require dirname(__DIR__) . '/includes/partials/admin_subnav.php';
?>
    <section class="app-page admin-panel">
        <div class="container" style="max-width: 1000px;">
    <?php if ($message): ?><p class="app-msg ok"><?= h($message) ?></p><?php endif; ?>
    <?php if ($error): ?><p class="app-msg err"><?= h($error) ?></p><?php endif; ?>

    <div class="admin-form-block admin-form-block--with-preview">
        <h2 class="section-title" style="font-size: 1.25rem; margin-top: 0;"><?= $edit ? 'Редактировать врача' : 'Добавить врача' ?></h2>
        <div class="admin-edit-layout">
            <div class="admin-form-col">
        <form id="doctorAdminForm" method="post" enctype="multipart/form-data" action="<?= h($edit ? 'doctors.php?edit=' . (int) $edit['id'] : 'doctors.php') ?>">
            <input type="hidden" name="action" value="<?= $edit ? 'edit' : 'create' ?>">
            <?php if ($edit): ?><input type="hidden" name="id" value="<?= (int) $edit['id'] ?>"><?php endif; ?>
            <label>ФИО</label>
            <input type="text" id="docFullName" name="full_name" required value="<?= h($edit['full_name'] ?? '') ?>">
            <label>Специальность</label>
            <input type="text" id="docSpecialty" name="specialty" value="<?= h($edit['specialty'] ?? '') ?>">
            <label>О враче (био)</label>
            <textarea id="docBio" name="bio"><?= h($edit['bio'] ?? '') ?></textarea>
            <label>Email для связи (на сайте)</label>
            <input type="email" name="contact_email" value="<?= h($edit['contact_email'] ?? '') ?>">
            <label>Телефон</label>
            <input type="text" name="contact_phone" value="<?= h($edit['contact_phone'] ?? '') ?>">
            <label>Фото <?= $edit ? '(новый файл заменит текущий)' : '' ?></label>
            <?php if ($edit && !empty($edit['photo_path'])): ?>
            <p class="admin-current-file" style="margin:0 0 .5rem;font-size:.85rem;color:var(--muted);">
                Файл в базе: <code style="font-size:.8rem;"><?= h($edit['photo_path']) ?></code>
            </p>
            <p style="margin:0 0 .75rem;">
                <img src="<?= h(public_upload_path($edit['photo_path'], $config)) ?>" alt="" style="max-height:100px;border-radius:10px;border:1px solid var(--line);">
            </p>
            <?php endif; ?>
            <input type="file" id="docPhoto" name="photo" accept="image/jpeg,image/png,image/webp,image/gif">
            <label>Порядок сортировки</label>
            <input type="number" name="sort_order" value="<?= (int) ($edit['sort_order'] ?? 0) ?>">
            <label><input type="checkbox" name="is_active" value="1" <?= ($edit['is_active'] ?? 1) ? 'checked' : '' ?>> Показывать на сайте</label>
            <?php if (!$edit): ?>
            <h3 style="margin-top:1rem;font-size:1rem;">Личный кабинет врача (необязательно)</h3>
            <p style="font-size:.85rem;color:var(--muted);">Если указать email и пароль, врач сможет войти на страницу «Кабинет врача».</p>
            <label>Email для входа</label>
            <input type="email" name="login_email" autocomplete="off">
            <label>Пароль (от 6 символов)</label>
            <input type="password" name="login_password" autocomplete="new-password">
            <?php endif; ?>
            <p style="margin-top:1rem;"><button type="submit" class="btn-admin"><?= $edit ? 'Сохранить' : 'Добавить' ?></button>
            <?php if ($edit): ?> <a class="btn-admin btn-admin--muted" href="doctors.php">Отмена</a><?php endif; ?></p>
        </form>
            </div>
            <aside class="admin-preview-col">
                <p class="admin-preview-label">Как на сайте</p>
                <div class="doctor-card admin-preview-card" id="doctorPreviewCard">
                    <div class="doctor-image">
                        <?php
                        $docPreviewUrl = !empty($formPreview['photo_path'])
                            ? public_upload_path($formPreview['photo_path'], $config)
                            : '';
                        ?>
                        <img id="doctorPreviewImg" class="doctor-photo" alt=""
                            data-original-src="<?= h($docPreviewUrl) ?>"<?php
                            if ($docPreviewUrl !== ''):
                                ?> src="<?= h($docPreviewUrl) ?>"<?php
                            else:
                                ?> style="display:none"<?php
                            endif; ?>>
                    </div>
                    <h3 id="doctorPreviewName"><?= h(trim((string) ($formPreview['full_name'] ?? '')) !== '' ? $formPreview['full_name'] : 'ФИО врача') ?></h3>
                    <p class="specialty" id="doctorPreviewSpec"><?= h(trim((string) ($formPreview['specialty'] ?? '')) !== '' ? $formPreview['specialty'] : 'Специальность') ?></p>
                    <p class="bio" id="doctorPreviewBio"><?= h(trim((string) ($formPreview['bio'] ?? '')) !== '' ? $formPreview['bio'] : 'Краткое описание') ?></p>
                    <div class="social-links" aria-hidden="true">
                        <span class="social-icon"><i class="fas fa-envelope"></i></span>
                        <span class="social-icon"><i class="fas fa-phone"></i></span>
                    </div>
                </div>
            </aside>
        </div>
    </div>

    <h2 class="section-title" style="font-size: 1.25rem;">Список</h2>
    <table class="admin-data">
        <thead><tr><th>ID</th><th>Фото</th><th>ФИО</th><th>Специальность</th><th>Активен</th><th></th></tr></thead>
        <tbody>
        <?php foreach ($list as $r): ?>
            <tr>
                <td><?= (int) $r['id'] ?></td>
                <td><?php if (!empty($r['photo_path'])): ?><img class="thumb" src="<?= h(public_upload_path($r['photo_path'], $config)) ?>" alt=""><?php endif; ?></td>
                <td><?= h($r['full_name']) ?></td>
                <td><?= h($r['specialty']) ?></td>
                <td><?= $r['is_active'] ? 'да' : 'нет' ?></td>
                <td>
                    <a class="btn-admin" href="doctors.php?edit=<?= (int) $r['id'] ?>">Изменить</a>
                    <form method="post" style="display:inline;" onsubmit="return confirm('Удалить?');">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" value="<?= (int) $r['id'] ?>">
                        <button type="submit" class="btn-admin btn-admin--danger">Удалить</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
        </div>
    </section>
<?php
require dirname(__DIR__) . '/includes/partials/public_footer.php';
?>
    <script src="../assets/js/main.js"></script>
    <script>
    (function () {
        var name = document.getElementById('docFullName');
        var spec = document.getElementById('docSpecialty');
        var bio = document.getElementById('docBio');
        var file = document.getElementById('docPhoto');
        var img = document.getElementById('doctorPreviewImg');
        var elName = document.getElementById('doctorPreviewName');
        var elSpec = document.getElementById('doctorPreviewSpec');
        var elBio = document.getElementById('doctorPreviewBio');
        if (!name || !elName) return;
        function syncText() {
            elName.textContent = (name.value && name.value.trim()) ? name.value.trim() : 'ФИО врача';
            elSpec.textContent = (spec && spec.value && spec.value.trim()) ? spec.value.trim() : 'Специальность';
            elBio.textContent = (bio && bio.value && bio.value.trim()) ? bio.value.trim() : 'Краткое описание';
        }
        name.addEventListener('input', syncText);
        if (spec) spec.addEventListener('input', syncText);
        if (bio) bio.addEventListener('input', syncText);
        if (file && img) {
            file.addEventListener('change', function () {
                var f = file.files && file.files[0];
                var orig = img.getAttribute('data-original-src') || '';
                if (!f) {
                    if (orig) {
                        img.src = orig;
                        img.style.display = '';
                    } else {
                        img.removeAttribute('src');
                        img.style.display = 'none';
                    }
                    return;
                }
                var reader = new FileReader();
                reader.onload = function () {
                    img.src = reader.result;
                    img.style.display = '';
                };
                reader.readAsDataURL(f);
            });
        }
        syncText();
    })();
    </script>
</body>
</html>
