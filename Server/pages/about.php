<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/bootstrap.php';
require_once dirname(__DIR__) . '/includes/paths.php';

$pageTitle = 'О нас — Больница «В последний путь»';
$NAV_BASE = '..';
$ASSETS = '../assets/';
$extraCss = ['hero.css', 'services.css', 'stats.css', 'contact.css'];
$homeUrl = public_href('index.php', $NAV_BASE);
$careersUrl = public_href('pages/careers.php', $NAV_BASE);

require dirname(__DIR__) . '/includes/partials/public_head.php';
require dirname(__DIR__) . '/includes/partials/public_nav.php';
?>
    <section class="hero">
        <div class="hero-content">
            <h1 class="hero-title">О больнице <br>«В последний путь»</h1>
            <p class="hero-subtitle">История успеха и служения здравоохранению</p>
            <p style="font-size: 18px; color: var(--muted); margin-top: 20px;">
                Больница «В последний путь» — современное медицинское учреждение с более чем 20-летней историей оказания качественной помощи населению.
            </p>
        </div>
        <div class="hero-image">
            <div class="hero-slider" id="heroSlider">
                <div class="hero-slide active" style="background-image: url('<?= h($ASSETS) ?>images/1.jpg');"></div>
                <div class="hero-slide" style="background-image: url('<?= h($ASSETS) ?>images/2.jpg');"></div>
                <div class="hero-slide" style="background-image: url('<?= h($ASSETS) ?>images/3.jpg');"></div>
                <div class="hero-slider-overlay"></div>
                <div class="hero-dots">
                    <button class="hero-dot active" type="button" aria-label="Слайд 1"></button>
                    <button class="hero-dot" type="button" aria-label="Слайд 2"></button>
                    <button class="hero-dot" type="button" aria-label="Слайд 3"></button>
                </div>
            </div>
        </div>
    </section>

    <section class="services">
        <div class="container">
            <h2 class="section-title">Наша миссия и ценности</h2>

            <div class="services-grid">
                <div class="service-card">
                    <div class="service-icon">
                        <img src="<?= h($ASSETS) ?>images/about/1.jpg" alt="" class="doctor-photo">
                    </div>
                    <h3>Забота о пациентах</h3>
                    <p>Каждый пациент для нас в приоритете. Мы стремимся оказать лучший уход и внимание в каждом контакте.</p>
                </div>

                <div class="service-card">
                    <div class="service-icon">
                        <img src="<?= h($ASSETS) ?>images/about/2.jpg" alt="" class="doctor-photo">
                    </div>
                    <h3>Постоянное развитие</h3>
                    <p>Инвестиции в образование и развитие команды — залог качества обслуживания.</p>
                </div>

                <div class="service-card">
                    <div class="service-icon">
                        <img src="<?= h($ASSETS) ?>images/about/3.jpg" alt="" class="doctor-photo">
                    </div>
                    <h3>Инновации</h3>
                    <p>Современные технологии и методики для лучших результатов лечения.</p>
                </div>

                <div class="service-card">
                    <div class="service-icon">
                        <img src="<?= h($ASSETS) ?>images/about/4.jpg" alt="" class="doctor-photo">
                    </div>
                    <h3>Командная работа</h3>
                    <p>Сотрудничество специалистов разных направлений для комплексной помощи пациентам.</p>
                </div>

                <div class="service-card">
                    <div class="service-icon">
                        <img src="<?= h($ASSETS) ?>images/about/5.jpg" alt="" class="doctor-photo">
                    </div>
                    <h3>Качество</h3>
                    <p>Высокие стандарты во всех аспектах деятельности и обслуживания.</p>
                </div>

                <div class="service-card">
                    <div class="service-icon">
                        <img src="<?= h($ASSETS) ?>images/about/6.jpg" alt="" class="doctor-photo">
                    </div>
                    <h3>Безопасность</h3>
                    <p>Соблюдение протоколов безопасности и конфиденциальности пациентов.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="contact" style="padding: 80px 0; background: var(--light-color);">
        <div class="container">
            <h2 class="section-title" style="color: #fff;">История развития</h2>

            <div style="margin-top: 50px; max-width: 800px; margin-left: auto; margin-right: auto;">
                <div style="position: relative; padding-left: 40px; margin-bottom: 40px;">
                    <div style="position: absolute; left: 0; top: 0; width: 30px; height: 30px; background: var(--primary-color); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;">2004</div>
                    <h3 style="margin-bottom: 10px;">Основание больницы</h3>
                    <p style="color: #666;">Открытие первого медицинского центра с узкими специалистами</p>
                </div>

                <div style="position: relative; padding-left: 40px; margin-bottom: 40px;">
                    <div style="position: absolute; left: 0; top: 0; width: 30px; height: 30px; background: var(--primary-color); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;">2010</div>
                    <h3 style="margin-bottom: 10px;">Расширение услуг</h3>
                    <p style="color: #666;">Новые отделения диагностики и хирургии</p>
                </div>

                <div style="position: relative; padding-left: 40px; margin-bottom: 40px;">
                    <div style="position: absolute; left: 0; top: 0; width: 30px; height: 30px; background: var(--primary-color); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;">2015</div>
                    <h3 style="margin-bottom: 10px;">Современные технологии</h3>
                    <p style="color: #666;">Внедрение современного медицинского оборудования</p>
                </div>

                <div style="position: relative; padding-left: 40px; margin-bottom: 40px;">
                    <div style="position: absolute; left: 0; top: 0; width: 30px; height: 30px; background: var(--primary-color); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;">2026</div>
                    <h3 style="margin-bottom: 10px;">Конкурс ПрофМастерства</h3>
                    <p style="color: #666;">Создание сайта больницы для Колледжа бизнеса и права</p>
                </div>
            </div>
        </div>
    </section>

    <section class="stats">
        <div class="container">
            <h2 class="section-title">Статистика</h2>
            <div class="stat-item">
                <h3 class="stat-number">5000+</h3>
                <p class="stat-label">Довольных пациентов</p>
            </div>
            <div class="stat-item">
                <h3 class="stat-number">50+</h3>
                <p class="stat-label">Квалифицированных врачей</p>
            </div>
            <div class="stat-item">
                <h3 class="stat-number">20+</h3>
                <p class="stat-label">Лет опыта</p>
            </div>
            <div class="stat-item">
                <h3 class="stat-number">24/7</h3>
                <p class="stat-label">Неотложная помощь</p>
            </div>
        </div>
    </section>

    <section class="services">
        <div class="container">
            <h2 class="section-title">Сертификации и аккредитации</h2>

            <div class="contact-grid" style="margin-top: 50px;">
                <div class="contact-item">
                    <i class="fas fa-certificate"></i>
                    <h3>ISO 9001:2015</h3>
                    <p>Сертификат системы менеджмента качества</p>
                </div>

                <div class="contact-item">
                    <i class="fas fa-certificate"></i>
                    <h3>Лицензия Минздрава Республики Беларусь</h3>
                    <p>Лицензия на осуществление медицинской деятельности</p>
                </div>

                <div class="contact-item">
                    <i class="fas fa-certificate"></i>
                    <h3>ГОСТ Р</h3>
                    <p>Соответствие государственным стандартам</p>
                </div>

                <div class="contact-item">
                    <i class="fas fa-certificate"></i>
                    <h3>Аккредитация</h3>
                    <p>Аккредитация образовательной деятельности</p>
                </div>
            </div>
        </div>
    </section>

    <section style="padding: 60px 0; color: var(--text); text-align: center; background: transparent;">
        <div class="container">
            <div style="max-width: 920px; margin: 0 auto; padding: 40px 24px; border-radius: 24px; border: 1px solid var(--line); background: var(--bg-card); box-shadow: var(--shadow);">
                <h2 style="font-size: 42px; margin-bottom: 20px; color: var(--text);">Присоединитесь к нашей команде</h2>
                <p style="font-size: 18px; margin-bottom: 30px; color: var(--muted);">Заполните анкету — мы рассмотрим кандидатуру и ответим на указанный email</p>
                <a href="<?= h($careersUrl) ?>" class="btn btn-primary">Заполнить анкету</a>
            </div>
        </div>
    </section>

<?php
require dirname(__DIR__) . '/includes/partials/public_footer.php';
?>
    <script src="<?= h($ASSETS) ?>js/main.js"></script>
</body>
</html>
