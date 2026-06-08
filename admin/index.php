<?php
declare(strict_types=1);
require __DIR__ . '/_auth.php';

$pageTitle = 'Админ-панель';
$NAV_BASE = '..';
$ASSETS = '../assets/';
$ADMIN_ACTIVE = 'home';
$extraCss = ['appointment.css'];
require dirname(__DIR__) . '/includes/partials/public_head.php';
require dirname(__DIR__) . '/includes/partials/public_nav.php';
require dirname(__DIR__) . '/includes/partials/admin_subnav.php';
?>
    <section class="app-page admin-panel">
        <div class="container">
            <h1 class="section-title" style="margin-bottom: 0.5rem;">Здравствуйте, <?= h($user['full_name'] ?: $user['email']) ?></h1>
            <p class="section-subtitle" style="margin-bottom: 1.5rem;">Управление контентом сайта, обращениями и анкетами.</p>
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); gap: 1.25rem;">
                <div class="app-card">
                    <h2 style="font-size: 1.1rem; margin-top: 0;">Врачи</h2>
                    <p style="font-size: 0.95rem; color: var(--muted);">ФИО, специальность, фото, контакты — карточки на сайте.</p>
                    <p style="margin-top: 1rem;"><a href="doctors.php" class="btn btn-primary">Перейти</a></p>
                </div>
                <div class="app-card">
                    <h2 style="font-size: 1.1rem; margin-top: 0;">Услуги</h2>
                    <p style="font-size: 0.95rem; color: var(--muted);">Название, описание, изображение.</p>
                    <p style="margin-top: 1rem;"><a href="services.php" class="btn btn-primary">Перейти</a></p>
                </div>
                <div class="app-card">
                    <h2 style="font-size: 1.1rem; margin-top: 0;">Уведомления</h2>
                    <p style="font-size: 0.95rem; color: var(--muted);">Обратная связь и анкеты в команду.</p>
                    <?php if ($adminNoteBadge > 0): ?>
                        <p><span class="admin-badge"><?= (int) $adminNoteBadge ?> новых</span></p>
                    <?php endif; ?>
                    <p style="margin-top: 1rem;"><a href="notifications.php" class="btn btn-primary">Перейти</a></p>
                </div>
            </div>
        </div>
    </section>
<?php
require dirname(__DIR__) . '/includes/partials/public_footer.php';
?>
    <script src="../assets/js/main.js"></script>
</body>
</html>
