<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/bootstrap.php';

$cu = current_user($pdo);
if ($cu && user_can_access_patient_area($cu)) {
    header('Location: index.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim((string) ($_POST['email'] ?? ''));
    $pass = (string) ($_POST['password'] ?? '');
    $name = trim((string) ($_POST['full_name'] ?? ''));
    $phone = trim((string) ($_POST['phone'] ?? ''));
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($pass) < 6 || strlen($name) < 2) {
        $error = 'Проверьте данные: пароль от 6 символов, ФИО';
    } else {
        $st = $pdo->prepare('SELECT id FROM users WHERE email = ?');
        $st->execute([$email]);
        if ($st->fetch()) {
            $error = 'Этот email уже зарегистрирован';
        } else {
            $hash = password_hash($pass, PASSWORD_DEFAULT);
            $pdo->prepare('INSERT INTO users (email, password_hash, full_name, phone, role) VALUES (?,?,?,?,?)')
                ->execute([$email, $hash, $name, $phone, 'patient']);
            login_user((int) $pdo->lastInsertId());
            header('Location: index.php');
            exit;
        }
    }
}

$pageTitle = 'Регистрация пациента';
$NAV_BASE = '..';
$ASSETS = '../assets/';
$extraCss = ['appointment.css'];
require dirname(__DIR__) . '/includes/partials/public_head.php';
require dirname(__DIR__) . '/includes/partials/public_nav.php';
?>
    <section class="app-page">
        <div class="container">
            <div class="app-card" style="max-width: 460px; margin: 0 auto;">
                <h1 class="section-title" style="text-align:center;">Регистрация</h1>
                <?php if ($error): ?><p class="app-msg err"><?= h($error) ?></p><?php endif; ?>
                <form method="post" class="appointment-form">
                    <div class="form-group">
                        <label for="full_name">ФИО</label>
                        <input type="text" id="full_name" name="full_name" required value="<?= h($_POST['full_name'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label for="phone">Телефон</label>
                        <input type="text" id="phone" name="phone" value="<?= h($_POST['phone'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" required value="<?= h($_POST['email'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label for="password">Пароль</label>
                        <input type="password" id="password" name="password" required minlength="6">
                    </div>
                    <button type="submit" class="btn btn-primary" style="width:100%;">Зарегистрироваться</button>
                </form>
                <p style="text-align:center;margin-top:1.25rem;font-size:0.95rem;">
                    <a href="login.php">Уже есть аккаунт</a> · <a href="forgot_password.php">Забыли пароль?</a><br>
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
