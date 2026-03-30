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
    $email = strtolower(trim((string) ($_POST['email'] ?? '')));
    $make = (int) ($_POST['make_admin'] ?? 0) === 1;
    try {
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
    } catch (Throwable $e) {
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
?>
<section class="app-page admin-panel">
    <div class="container" style="max-width: 900px;">
        <h1 class="section-title" style="margin-bottom: .4rem;">Пользователи</h1>
        <p class="section-subtitle" style="margin-bottom: 1.25rem;">Выдача прав администратора по email (доступно только <code>admin@hospital.local</code>).</p>

        <?php if ($message): ?><p class="app-msg ok"><?= h($message) ?></p><?php endif; ?>
        <?php if ($error): ?><p class="app-msg err"><?= h($error) ?></p><?php endif; ?>

        <form method="post" class="admin-detail" style="margin-top: 1rem;">
            <label>Email пользователя</label>
            <input type="email" name="email" required placeholder="user@example.com">
            <label style="margin-top:.75rem;display:block;">
                <input type="checkbox" name="make_admin" value="1"> Выдать права администратора
            </label>
            <p style="margin-top: 1rem;">
                <button type="submit" class="btn-admin">Сохранить</button>
            </p>
        </form>
    </div>
</section>
<?php
require dirname(__DIR__) . '/includes/partials/public_footer.php';
?>
<script src="../assets/js/main.js"></script>
</body>
</html>

