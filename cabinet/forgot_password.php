<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/bootstrap.php';
require_once dirname(__DIR__) . '/includes/password_reset.php';

$cu = current_user($pdo);
if ($cu) {
    header('Location: index.php');
    exit;
}

$message = '';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim((string) ($_POST['email'] ?? ''));
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Укажите корректный email';
    } else {
        password_reset_request_for_email($pdo, $config, $email);
        $message = 'Если указанный email зарегистрирован, мы отправили на него ссылку для сброса пароля. Проверьте почту (и папку «Спам»).';
    }
}

$pageTitle = 'Восстановление пароля';
$NAV_BASE = '..';
$ASSETS = '../assets/';
$extraCss = ['appointment.css'];
require dirname(__DIR__) . '/includes/partials/public_head.php';
require dirname(__DIR__) . '/includes/partials/public_nav.php';
?>
    <section class="app-page">
        <div class="container">
            <div class="app-card" style="max-width: 440px; margin: 0 auto;">
                <h1 class="section-title" style="text-align:center;margin-bottom:0.5rem;">Забыли пароль?</h1>
                <p class="section-subtitle" style="text-align:center;">Введите email — пришлём ссылку для нового пароля</p>
                <?php if ($message): ?><p class="app-msg" style="background:rgba(45,90,74,.12);color:#1e3d32;border:none;"><?= h($message) ?></p><?php endif; ?>
                <?php if ($error): ?><p class="app-msg err"><?= h($error) ?></p><?php endif; ?>
                <?php if (!$message): ?>
                <form method="post" class="appointment-form" style="margin-top:1rem;">
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" required autocomplete="email" value="<?= h(trim((string) ($_POST['email'] ?? ''))) ?>">
                    </div>
                    <button type="submit" class="btn btn-primary" style="width:100%;">Отправить ссылку</button>
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
