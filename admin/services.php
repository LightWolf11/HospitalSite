<?php
declare(strict_types=1);
require __DIR__ . '/_auth.php';

$message = '';
$error = '';
if (!empty($_SESSION['flash_admin_services_ok'])) {
    $message = (string) $_SESSION['flash_admin_services_ok'];
    unset($_SESSION['flash_admin_services_ok']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    try {
        if ($action === 'create' || $action === 'edit') {
            $id = (int) ($_POST['id'] ?? 0);
            $title = trim((string) ($_POST['title'] ?? ''));
            $description = trim((string) ($_POST['description'] ?? ''));
            $sortOrder = (int) ($_POST['sort_order'] ?? 0);
            $isActive = isset($_POST['is_active']) ? 1 : 0;
            if (strlen($title) < 2) {
                throw new RuntimeException('Укажите название');
            }
            $imagePath = null;
            $imgFile = $_FILES['image'] ?? null;
            if ($imgFile && ($imgFile['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
                if ((int) ($imgFile['error'] ?? 0) !== UPLOAD_ERR_OK) {
                    throw new RuntimeException(upload_php_err_message((int) $imgFile['error']));
                }
                $imagePath = upload_image_or_throw($imgFile, 'services', $config);
            }
            if ($action === 'create') {
                $st = $pdo->prepare(
                    'INSERT INTO services (title, description, image_path, sort_order, is_active) VALUES (?,?,?,?,?)'
                );
                $st->execute([$title, $description, $imagePath, $sortOrder, $isActive]);
                $newId = (int) $pdo->lastInsertId();
                $_SESSION['flash_admin_services_ok'] = 'Услуга добавлена';
                header('Location: services.php?edit=' . $newId);
                exit;
            }
            $st = $pdo->prepare('SELECT image_path FROM services WHERE id = ?');
            $st->execute([$id]);
            $old = $st->fetch();
            if (!$old) {
                throw new RuntimeException('Не найдено');
            }
            $final = $imagePath !== null ? $imagePath : $old['image_path'];
            $pdo->prepare(
                'UPDATE services SET title=?, description=?, image_path=?, sort_order=?, is_active=? WHERE id=?'
            )->execute([$title, $description, $final, $sortOrder, $isActive, $id]);
            $_SESSION['flash_admin_services_ok'] = 'Сохранено';
            header('Location: services.php?edit=' . $id);
            exit;
        } elseif ($action === 'delete' && ($id = (int) ($_POST['id'] ?? 0)) > 0) {
            $pdo->prepare('DELETE FROM services WHERE id = ?')->execute([$id]);
            $_SESSION['flash_admin_services_ok'] = 'Удалено';
            header('Location: services.php');
            exit;
        }
    } catch (Throwable $e) {
        $error = $e->getMessage();
    }
}

$list = $pdo->query('SELECT * FROM services ORDER BY sort_order, id')->fetchAll();
$edit = null;
if (isset($_GET['edit'])) {
    $eid = (int) $_GET['edit'];
    $st = $pdo->prepare('SELECT * FROM services WHERE id = ?');
    $st->execute([$eid]);
    $row = $st->fetch();
    $edit = $row !== false ? $row : null;
}
$formPreview = is_array($edit) ? $edit : [];
$pageTitle = 'Услуги — админ-панель';
$NAV_BASE = '..';
$ASSETS = '../assets/';
$ADMIN_ACTIVE = 'services';
$extraCss = ['appointment.css', 'services.css'];
require dirname(__DIR__) . '/includes/partials/public_head.php';
require dirname(__DIR__) . '/includes/partials/public_nav.php';
require dirname(__DIR__) . '/includes/partials/admin_subnav.php';
?>
    <section class="app-page admin-panel">
        <div class="container" style="max-width: 1000px;">
    <?php if ($message): ?><p class="app-msg ok"><?= h($message) ?></p><?php endif; ?>
    <?php if ($error): ?><p class="app-msg err"><?= h($error) ?></p><?php endif; ?>

    <div class="admin-form-block admin-form-block--with-preview">
        <h2 class="section-title" style="font-size: 1.25rem; margin-top: 0;"><?= $edit ? 'Редактировать услугу' : 'Добавить услугу' ?></h2>
        <div class="admin-edit-layout">
            <div class="admin-form-col">
        <form id="serviceAdminForm" method="post" enctype="multipart/form-data" action="<?= h($edit ? 'services.php?edit=' . (int) $edit['id'] : 'services.php') ?>">
            <input type="hidden" name="action" value="<?= $edit ? 'edit' : 'create' ?>">
            <?php if ($edit): ?><input type="hidden" name="id" value="<?= (int) $edit['id'] ?>"><?php endif; ?>
            <label>Название</label>
            <input type="text" id="svcTitle" name="title" required value="<?= h($edit['title'] ?? '') ?>">
            <label>Описание</label>
            <textarea id="svcDesc" name="description"><?= h($edit['description'] ?? '') ?></textarea>
            <label>Изображение <?= $edit ? '(новый файл заменит текущий)' : '' ?></label>
            <?php if ($edit && !empty($edit['image_path'])): ?>
            <p class="admin-current-file" style="margin:0 0 .5rem;font-size:.85rem;color:var(--muted);">
                Файл в базе: <code style="font-size:.8rem;"><?= h($edit['image_path']) ?></code>
            </p>
            <p style="margin:0 0 .75rem;">
                <img src="<?= h(public_upload_path($edit['image_path'], $config)) ?>" alt="" class="admin-current-thumb" style="max-height:100px;border-radius:10px;border:1px solid var(--line);">
            </p>
            <?php endif; ?>
            <input type="file" id="svcImage" name="image" accept="image/jpeg,image/png,image/webp,image/gif">
            <label>Порядок</label>
            <input type="number" name="sort_order" value="<?= (int) ($edit['sort_order'] ?? 0) ?>">
            <label><input type="checkbox" name="is_active" value="1" <?= ($edit['is_active'] ?? 1) ? 'checked' : '' ?>> На сайте</label>
            <p style="margin-top:1rem;"><button type="submit" class="btn-admin"><?= $edit ? 'Сохранить' : 'Добавить' ?></button>
            <?php if ($edit): ?> <a class="btn-admin btn-admin--muted" href="services.php">Отмена</a><?php endif; ?></p>
        </form>
            </div>
            <aside class="admin-preview-col">
                <p class="admin-preview-label">Как на сайте</p>
                <div class="service-card admin-preview-card" id="servicePreviewCard">
                    <div class="service-icon">
                        <?php
                        $previewImgUrl = !empty($formPreview['image_path'])
                            ? public_upload_path($formPreview['image_path'], $config)
                            : '';
                        ?>
                        <img id="servicePreviewImg" class="doctor-photo" alt=""
                            data-original-src="<?= h($previewImgUrl) ?>"<?php
                            if ($previewImgUrl !== ''):
                                ?> src="<?= h($previewImgUrl) ?>" style="width:100%;height:100%;object-fit:cover;"<?php
                            else:
                                ?> style="display:none;width:100%;height:100%;object-fit:cover;"<?php
                            endif; ?>>
                    </div>
                    <h3 id="servicePreviewTitle"><?= h(trim((string) ($formPreview['title'] ?? '')) !== '' ? $formPreview['title'] : 'Название услуги') ?></h3>
                    <p class="admin-preview-service-text" id="servicePreviewText"><?= h(trim((string) ($formPreview['description'] ?? '')) !== '' ? $formPreview['description'] : 'Описание услуги') ?></p>
                </div>
            </aside>
        </div>
    </div>

    <h2 class="section-title" style="font-size: 1.25rem;">Список</h2>
    <table class="admin-data">
        <thead><tr><th>ID</th><th>Картинка</th><th>Название</th><th>Активна</th><th></th></tr></thead>
        <tbody>
        <?php foreach ($list as $r): ?>
            <tr>
                <td><?= (int) $r['id'] ?></td>
                <td><?php if (!empty($r['image_path'])): ?><img class="thumb" src="<?= h(public_upload_path($r['image_path'], $config)) ?>" alt=""><?php endif; ?></td>
                <td><?= h($r['title']) ?></td>
                <td><?= $r['is_active'] ? 'да' : 'нет' ?></td>
                <td>
                    <a class="btn-admin" href="services.php?edit=<?= (int) $r['id'] ?>">Изменить</a>
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
        var title = document.getElementById('svcTitle');
        var desc = document.getElementById('svcDesc');
        var file = document.getElementById('svcImage');
        var img = document.getElementById('servicePreviewImg');
        var elTitle = document.getElementById('servicePreviewTitle');
        var elText = document.getElementById('servicePreviewText');
        if (!title || !elTitle) return;
        function sync() {
            elTitle.textContent = (title.value && title.value.trim()) ? title.value.trim() : 'Название услуги';
            var t = (desc && desc.value && desc.value.trim()) ? desc.value.trim() : 'Описание услуги';
            elText.textContent = t;
        }
        title.addEventListener('input', sync);
        if (desc) desc.addEventListener('input', sync);
        if (file && img) {
            file.addEventListener('change', function () {
                var f = file.files && file.files[0];
                var orig = img.getAttribute('data-original-src') || '';
                if (!f) {
                    if (orig) {
                        img.src = orig;
                        img.style.display = 'block';
                    } else {
                        img.removeAttribute('src');
                        img.style.display = 'none';
                    }
                    return;
                }
                var reader = new FileReader();
                reader.onload = function () {
                    img.src = reader.result;
                    img.style.display = 'block';
                    img.style.width = '100%';
                    img.style.height = '100%';
                    img.style.objectFit = 'cover';
                };
                reader.readAsDataURL(f);
            });
        }
        sync();
    })();
    </script>
</body>
</html>
