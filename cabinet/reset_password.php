<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/bootstrap.php';
require_once dirname(__DIR__) . '/includes/password_reset.php';

$cu = current_user($pdo);
if ($cu) {
    header('Location: index.php');
    exit;
}

$token = trim((string) ($_GET['token'] ?? $_POST['token'] ?? ''));
$error = '';
$done = false;

if ($token === '' && ($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    $error = 'Откройте ссылку из письма или запросите восстановление пароля заново.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pass = (string) ($_POST['password'] ?? '');
    $pass2 = (string) ($_POST['password_confirm'] ?? '');
    $uid = password_reset_user_id_by_token($pdo, $token);
    if (!$uid) {
        $error = 'Ссылка недействительна или устарела. Запросите сброс пароля снова.';
    } elseif (strlen($pass) < 6) {
        $error = 'Пароль не короче 6 символов';
    } elseif ($pass !== $pass2) {
        $error = 'Пароли не совпадают';
    } else {
        password_reset_apply($pdo, $uid, $pass);
        $done = true;
    }
} elseif ($token !== '' && !password_reset_user_id_by_token($pdo, $token)) {
    $error = 'Ссылка недействительна или устарела.';
}

$pageTitle = 'Новый пароль';
$NAV_BASE = '..';
$ASSETS = '../assets/';
$extraCss = ['appointment.css'];
require dirname(__DIR__) . '/includes/partials/public_head.php';
require dirname(__DIR__) . '/includes/partials/public_nav.php';
?>
    <section class="app-page">
        <div class="container">
            <div class="app-card" style="max-width: 440px; margin: 0 auto;">
                <h1 class="section-title" style="text-align:center;margin-bottom:0.5rem;">Новый пароль</h1>
                <?php if ($done): ?>
                    <p class="app-msg" style="background:rgba(45,90,74,.12);color:#1e3d32;border:none;">Пароль обновлён. Теперь можно войти.</p>
                    <p style="text-align:center;margin-top:1.25rem;"><a href="login.php" class="btn btn-primary" style="display:inline-block;">Войти</a></p>
                <?php elseif ($error): ?>
                    <p class="app-msg err"><?= h($error) ?></p>
                    <p style="text-align:center;margin-top:1rem;"><a href="forgot_password.php">Запросить ссылку снова</a></p>
                <?php else: ?>
                    <form method="post" class="appointment-form" style="margin-top:1rem;">
                        <input type="hidden" name="token" value="<?= h($token) ?>">
                        <div class="form-group">
                            <label for="password">Новый пароль</label>
                            <input type="password" id="password" name="password" required minlength="6" autocomplete="new-password">
                        </div>
                        <div class="form-group">
                            <label for="password_confirm">Повтор пароля</label>
                            <input type="password" id="password_confirm" name="password_confirm" required minlength="6" autocomplete="new-password">
                        </div>
                        <button type="submit" class="btn btn-primary" style="width:100%;">Сохранить</button>
                    </form>
                <?php endif; ?>
                <p style="text-align:center;margin-top:1.25rem;font-size:0.95rem;">
                    <a href="login.php">Вход</a> · <a href="../index.php">На главную</a>
                </p>
            </div>
        </div>
    </section>
<?php
require dirname(__DIR__) . '/includes/partials/public_footer.php';
?>
    <script src="../assets/js/main.js"></script>
</body>
</html>
