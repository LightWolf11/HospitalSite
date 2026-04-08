<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/bootstrap.php';
require_once dirname(__DIR__) . '/includes/paths.php';

$pageTitle = 'Наши Услуги — Больница «В последний путь»';
$NAV_BASE = '..';
$ASSETS = '../assets/';
$extraCss = ['hero.css', 'services.css', 'stats.css', 'contact.css'];
$homeUrl = public_href('index.php', $NAV_BASE);
$careersUrl = public_href('pages/careers.php', $NAV_BASE);

require dirname(__DIR__) . '/includes/partials/public_head.php';
require dirname(__DIR__) . '/includes/partials/public_nav.php';
?>
    <section class="hero" style="min-height: auto; padding: 58px 0; grid-template-columns: 1fr; justify-items: center;">
        <div class="hero-content" style="text-align: center; margin: 0 auto; max-width: 900px; width: 100%; padding: 0 16px;">
            <h1 class="hero-title">Наши Услуги</h1>
            <p class="hero-subtitle">Полный спектр современного медицинского обслуживания</p>
        </div>
    </section>

    <section class="services" style="padding: 80px 0 60px;">
        <div class="container">
            <h2 class="section-title">Наши Услуги</h2>
            <p class="section-subtitle">Полный спектр медицинских услуг для вашего здоровья</p>
            <div class="services-grid" id="servicesGrid">
                <p class="section-subtitle" style="grid-column:1/-1;">Загрузка услуг…</p>
            </div>
        </div>
    </section>

    <section style="padding: 60px 0; color: var(--text); text-align: center; background: transparent;">
        <div class="container">
            <div style="max-width: 920px; margin: 0 auto; padding: 40px 24px; border-radius: 24px; border: 1px solid var(--line); background: var(--bg-card); box-shadow: var(--shadow);">
                <h2 style="font-size: 42px; margin-bottom: 20px; color: var(--text);">Запишитесь на приём сегодня</h2>
                <p style="font-size: 18px; margin-bottom: 30px; color: var(--muted);">Профессиональная помощь ждёт вас</p>
                <a href="../index.php#appointment" class="btn btn-primary">Записаться на приём</a>
            </div>
        </div>
    </section>

    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>Больница "В последний путь"</h3>
                    <p>Ваше здоровье - наш приоритет</p>
                    <div class="social-icons">
                        <a href="#" class="social-icon">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="social-icon">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" class="social-icon">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#" class="social-icon">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                    </div>
                </div>

                <div class="footer-section">
                    <h4>Быстрые ссылки</h4>
                    <ul>
                        <li><a href="../index.html#home">Главная</a></li>
                        <li><a href="services.html">Услуги</a></li>
                        <li><a href="../index.html#doctors">Врачи</a></li>
                        <li><a href="../index.html#contact">Контакты</a></li>
                    </ul>
                </div>

                <div class="footer-section">
                    <h4>Услуги</h4>
                    <ul>
                        <li><a href="../index.html#services">Консультация</a></li>
                        <li><a href="../index.html#services">Диагностика</a></li>
                        <li><a href="../index.html#services">Лечение</a></li>
                        <li><a href="../index.html#services">Профилактика</a></li>
                    </ul>
                </div>

                <div class="footer-section">
                    <h4>Информация</h4>
                    <ul>
                        <li><a href="license.html">Лицензия</a></li>
                        <li><a href="privacy.html">Политика конфиденциальности</a></li>
                        <li><a href="terms.html">Условия использования</a></li>
                        <li><a href="faq.html">FAQ</a></li>
                    </ul>
                </div>
            </div>

            <div class="footer-bottom">
                <p>&copy; 2026 Больница "В последний путь". Разработана на конкурс ПрофМастерства. </p>
            </div>
        </div>
    </footer>

<?php
require dirname(__DIR__) . '/includes/partials/public_footer.php';
?>
    <script src="../assets/js/main.js"></script>
    <script src="../assets/js/site-api.js"></script>
</body>
</html>

