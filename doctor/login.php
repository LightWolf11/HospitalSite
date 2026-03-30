<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/bootstrap.php';

$u = current_user($pdo);
if ($u && ($u['role'] ?? '') === 'doctor') {
    header('Location: index.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim((string) ($_POST['email'] ?? ''));
    $pass = (string) ($_POST['password'] ?? '');
    $st = $pdo->prepare('SELECT id, password_hash, role, doctor_profile_id FROM users WHERE email = ?');
    $st->execute([$email]);
    $row = $st->fetch();
    if ($row && password_verify($pass, $row['password_hash']) && $row['role'] === 'doctor' && !empty($row['doctor_profile_id'])) {
        login_user((int) $row['id']);
        header('Location: index.php');
        exit;
    }
    $error = 'Неверные данные или аккаунт не врача';
}

$pageTitle = 'Вход — кабинет врача';
$NAV_BASE = '..';
$ASSETS = '../assets/';
$extraCss = ['appointment.css'];
require dirname(__DIR__) . '/includes/partials/public_head.php';
require dirname(__DIR__) . '/includes/partials/public_nav.php';
?>
    <section class="app-page">
        <div class="container">
            <div class="app-card" style="max-width: 420px; margin: 0 auto;">
                <h1 class="section-title" style="text-align: center;">Кабинет врача</h1>
                <?php if ($error): ?><p class="app-msg err"><?= h($error) ?></p><?php endif; ?>
                <form method="post" class="appointment-form" style="margin-top: 1rem;">
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Пароль</label>
                        <input type="password" id="password" name="password" required>
                    </div>
                    <button type="submit" class="btn btn-primary" style="width: 100%;">Войти</button>
                </form>
                <p style="text-align: center; margin-top: 1.25rem; font-size: 0.95rem;">
                    <a href="../cabinet/forgot_password.php">Забыли пароль?</a><br>
                    <a href="../index.php">На главную</a>
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
