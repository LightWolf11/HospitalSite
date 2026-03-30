<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/paths.php';

if (!isset($NAV_BASE)) {
    $NAV_BASE = '';
}
$home = public_href('index.php', $NAV_BASE);
$services = public_href('pages/services.html', $NAV_BASE);
$license = public_href('pages/license.html', $NAV_BASE);
$privacy = public_href('pages/privacy.html', $NAV_BASE);
$terms = public_href('pages/terms.html', $NAV_BASE);
$faq = public_href('pages/faq.php', $NAV_BASE);
?>
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>Больница "В последний путь"</h3>
                    <p>Ваше здоровье - наш приоритет</p>
                    <div class="social-icons">
                        <a href="#" class="social-icon" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="social-icon" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="social-icon" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="social-icon" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
                <div class="footer-section">
                    <h4>Быстрые ссылки</h4>
                    <ul>
                        <li><a href="<?= h($home) ?>#home">Главная</a></li>
                        <li><a href="<?= h($services) ?>">Услуги</a></li>
                        <li><a href="<?= h($home) ?>#doctors">Врачи</a></li>
                        <li><a href="<?= h($home) ?>#contact">Контакты</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>Услуги</h4>
                    <ul>
                        <li><a href="<?= h($home) ?>#services">Консультация</a></li>
                        <li><a href="<?= h($home) ?>#services">Диагностика</a></li>
                        <li><a href="<?= h($home) ?>#services">Лечение</a></li>
                        <li><a href="<?= h($home) ?>#services">Профилактика</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>Информация</h4>
                    <ul>
                        <li><a href="<?= h($license) ?>">Лицензия</a></li>
                        <li><a href="<?= h($privacy) ?>">Политика конфиденциальности</a></li>
                        <li><a href="<?= h($terms) ?>">Условия использования</a></li>
                        <li><a href="<?= h($faq) ?>">FAQ</a></li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; <?= (int) date('Y') ?> Больница "В последний путь". Разработана на конкурс ПрофМастерства.</p>
            </div>
        </div>
    </footer>

    <div id="toastStack" class="toast-stack" aria-live="polite" aria-atomic="false"></div>

    <button class="scroll-to-top" id="scrollToTop" type="button" aria-label="Наверх">
        <i class="fas fa-arrow-up"></i>
    </button>
<?php
$__assets = isset($ASSETS) ? $ASSETS : 'assets/';
?>
    <script src="<?= h($__assets) ?>js/nav-notifications.js" defer></script>
