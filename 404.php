<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';

$pageTitle = '404 — Страница не найдена';
$NAV_BASE = '';
$ASSETS = 'assets/';
$extraCss = ['hero.css'];

require __DIR__ . '/includes/partials/public_head.php';
require __DIR__ . '/includes/partials/public_nav.php';
?>
    <section class="hero" style="padding-top: 120px; padding-bottom: 90px;">
        <div class="container" style="max-width: 920px;">
            <div class="app-card" style="margin: 0 auto; max-width: 760px; text-align: center;">
                <div style="font-size: 64px; font-weight: 800; letter-spacing: 0.02em; line-height: 1; color: var(--primary-color);">
                    404
                </div>
                <h1 class="section-title" style="margin-top: 0.75rem; font-size: 1.8rem;">Страница не найдена</h1>
                <p class="section-subtitle" style="margin-top: 0.75rem;">
                    Возможно, ссылка была изменена или страница больше не существует.
                </p>
                <div style="display:flex; gap: 12px; justify-content: center; flex-wrap: wrap; margin-top: 1.25rem;">
                    <a href="index.php" class="btn btn-primary">На главную</a>
                    <a href="pages/services.php" class="btn btn-secondary">Услуги</a>
                    <a href="index.php#appointment" class="btn btn-primary">Записаться</a>
                </div>
            </div>
        </div>
    </section>
<?php
require __DIR__ . '/includes/partials/public_footer.php';
?>
    <script src="assets/js/main.js"></script>
</body>
</html>

