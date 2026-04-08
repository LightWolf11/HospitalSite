<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/bootstrap.php';

$cu = current_user($pdo);
if ($cu) {
    $role = (string) ($cu['role'] ?? '');
    if ($role === 'doctor' && !empty($cu['doctor_profile_id'])) {
        header('Location: ../doctor/index.php');
        exit;
    }
    if (user_can_access_patient_area($cu)) {
        header('Location: index.php');
        exit;
    }
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim((string) ($_POST['email'] ?? ''));
    $pass = (string) ($_POST['password'] ?? '');
    $st = $pdo->prepare('SELECT id, password_hash, role, is_admin, doctor_profile_id FROM users WHERE email = ?');
    $st->execute([$email]);
    $u = $st->fetch();
    if ($u && password_verify($pass, (string) $u['password_hash'])) {
        $role = (string) ($u['role'] ?? '');
        if ($role === 'doctor' && !empty($u['doctor_profile_id'])) {
            login_user((int) $u['id']);
            header('Location: ../doctor/index.php');
            exit;
        }
        if (user_can_access_patient_area($u)) {
            login_user((int) $u['id']);
            header('Location: index.php');
            exit;
        }
    }
    $error = 'Неверный email или пароль';
}

$pageTitle = 'Вход — личный кабинет';
$NAV_BASE = '..';
$ASSETS = '../assets/';
$extraCss = ['appointment.css'];
require dirname(__DIR__) . '/includes/partials/public_head.php';
require dirname(__DIR__) . '/includes/partials/public_nav.php';
?>
    <section class="app-page">
        <div class="container">
            <div class="app-card" style="max-width: 440px; margin: 0 auto;">
                <h1 class="section-title" style="text-align:center;margin-bottom:0.5rem;">Личный кабинет</h1>
                <p class="section-subtitle" style="text-align:center;">Войдите, чтобы видеть записи и уведомления</p>
                <?php if ($error): ?><p class="app-msg err"><?= h($error) ?></p><?php endif; ?>
                <form method="post" class="appointment-form" style="margin-top:1rem;">
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" required autocomplete="username">
                    </div>
                    <div class="form-group">
                        <label for="password">Пароль</label>
                        <input type="password" id="password" name="password" required autocomplete="current-password">
                    </div>
                    <button type="submit" class="btn btn-primary" style="width:100%;">Войти</button>
                </form>
                <p style="text-align:center;margin-top:1.25rem;font-size:0.95rem;">
                    <a href="forgot_password.php">Забыли пароль?</a><br>
                    <a href="register.php">Регистрация</a> · <a href="../index.php">На главную</a>
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
