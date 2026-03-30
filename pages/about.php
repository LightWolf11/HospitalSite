<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/bootstrap.php';
require_once dirname(__DIR__) . '/includes/paths.php';

$pageTitle = 'О Нас - Больница "В последний путь"';
$NAV_BASE = '..';
$ASSETS = '../assets/';
$extraCss = ['hero.css', 'services.css', 'stats.css', 'contact.css'];
$home_url = public_href('index.php', $NAV_BASE);
$careers_url = public_href('pages/careers.php', $NAV_BASE);

require_once dirname(__DIR__) . '/includes/public_head.php';

?>


<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="О нашей больнице - история, миссия, ценности">
    <title>О Нас - Больница В последний путь</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@500;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/variables.css">
    <link rel="stylesheet" href="../assets/css/animations.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/responsive.css">
    <link rel="stylesheet" href="../assets/css/hero.css">
    <link rel="stylesheet" href="../assets/css/services.css">
    <link rel="stylesheet" href="../assets/css/stats.css">
    <link rel="stylesheet" href="../assets/css/contact.css">
    <link rel="stylesheet" href="../assets/css/navbar.css">
    <link rel="stylesheet" href="../assets/css/buttons.css">
    <link rel="stylesheet" href="../assets/css/footer.css">
    <link rel="stylesheet" href="../assets/css/theme.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="icon" href="../assets/images/logo.png" type="image/png">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <div class="navbar-brand">
                <a href="../index.html" class="logo">
                    <img src="../assets/images/logo.png" width="50" height="50"> Больница "В последний путь"
                </a>
                <button class="hamburger" id="hamburgerBtn">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
            </div>
            <ul class="nav-menu" id="navMenu">
                <li><a href="../index.html#home" class="nav-link">Главная</a></li>
                <li><a href="services.html" class="nav-link">Услуги</a></li>
                <li><a href="../index.html#doctors" class="nav-link">Врачи</a></li>
                <li><a href="../index.html#contact" class="nav-link">Контакты</a></li>
                <li><a href="faq.html" class="nav-link">FAQ</a></li>
                <li><a href="about.html" class="nav-link">О нас</a></li>
                <li><a href="../index.html#appointment" class="btn btn-primary">Записать приём</a></li>
            </ul>
        </div>
    </nav>

    <section class="hero">
        <div class="hero-content">
            <h1 class="hero-title">О больнице <br>"В последний путь"</h1>
            <p class="hero-subtitle">История успеха и служения здравоохранению</p>
            <p style="font-size: 18px; color: var(--muted); margin-top: 20px;">
                Больница "В последний путь" - это современное медицинское учреждение с более чем 20-летней историей оказания качественного медицинского обслуживания населению.
            </p>
        </div>
        <div class="hero-image">
            <div class="hero-slider" id="heroSlider">
                <div class="hero-slide active" style="background-image: url('../assets/images/1.jpg');"></div>
                <div class="hero-slide" style="background-image: url('../assets/images/2.jpg');"></div>
                <div class="hero-slide" style="background-image: url('../assets/images/3.jpg');"></div>
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
            <h2 class="section-title">Наша Миссия и Ценности</h2>
            
            <div class="services-grid">
                <div class="service-card">
                    <div class="service-icon">
                        <img src="../assets/images/about/1.jpg" alt="Забота о Пациентах" class="doctor-photo">
                    </div>
                    <h3>Забота о Пациентах</h3>
                    <p>Каждый пациент для нас является приоритетом. Мы стремимся оказать лучший уход и внимание в каждом контакте.</p>
                </div>

                <div class="service-card">
                    <div class="service-icon">
                        <img src="../assets/images/about/2.jpg" alt="Постоянное Развитие" class="doctor-photo">
                    </div>
                    <h3>Постоянное Развитие</h3>
                    <p>Инвестирование в образование и развитие нашей команды - это залог успеха и качества обслуживания.</p>
                </div>

                <div class="service-card">
                    <div class="service-icon">
                        <img src="../assets/images/about/3.jpg" alt="Инновации" class="doctor-photo">
                    </div>
                    <h3>Инновации</h3>
                    <p>Использование последних технологий и методик в медицине для достижения лучших результатов лечения.</p>
                </div>

                <div class="service-card">
                    <div class="service-icon">
                        <img src="../assets/images/about/4.jpg" alt="Командная Работа" class="doctor-photo">
                    </div>
                    <h3>Командная Работа</h3>
                    <p>Сотрудничество между специалистами разных направлений для комплексного колечения пациентов.</p>
                </div>

                <div class="service-card">
                    <div class="service-icon">
                        <img src="../assets/images/about/5.jpg" alt="Качество" class="doctor-photo">
                    </div>
                    <h3>Качество</h3>
                    <p>Высокие стандарты качества во всех аспектах нашей деятельности и обслуживания.</p>
                </div>

                <div class="service-card">
                    <div class="service-icon">
                        <img src="../assets/images/about/6.jpg" alt="Безопасность" class="doctor-photo">
                    </div>
                    <h3>Безопасность</h3>
                    <p>Строгое соблюдение всех протоколов безопасности и конфиденциальности пациентов.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="contact" style="padding: 80px 0; background: var(--light-color);">
        <div class="container">
            <h2 class="section-title" style="color: fff;">История Развития</h2>
            
            <div style="margin-top: 50px; max-width: 800px; margin-left: auto; margin-right: auto;">
                <div style="position: relative; padding-left: 40px; margin-bottom: 40px;">
                    <div style="position: absolute; left: 0; top: 0; width: 30px; height: 30px; background: var(--primary-color); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;">2004</div>
                    <h3 style="margin-bottom: 10px;">Основание больницы</h3>
                    <p style="color: #666;">Открытие первого медицинского центра с узкими специалистами</p>
                </div>

                <div style="position: relative; padding-left: 40px; margin-bottom: 40px;">
                    <div style="position: absolute; left: 0; top: 0; width: 30px; height: 30px; background: var(--primary-color); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;">2010</div>
                    <h3 style="margin-bottom: 10px;">Расширение Услуг</h3>
                    <p style="color: #666;">Добавлены новые отделения диагностики и хирургии</p>
                </div>

                <div style="position: relative; padding-left: 40px; margin-bottom: 40px;">
                    <div style="position: absolute; left: 0; top: 0; width: 30px; height: 30px; background: var(--primary-color); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;">2015</div>
                    <h3 style="margin-bottom: 10px;">Современные Технологии</h3>
                    <p style="color: #666;">Внедрение современного медицинского оборудования</p>
                </div>

                <div style="position: relative; padding-left: 40px; margin-bottom: 40px;">
                    <div style="position: absolute; left: 0; top: 0; width: 30px; height: 30px; background: var(--primary-color); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;">2026</div>
                    <h3 style="margin-bottom: 10px;">Конкурс ПрофМастерства</h3>
                    <p style="color: #666;">Создания сайта больницы для Колледжа Бизнеса и Права.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="stats">
        <div class="container">
            <h2 class="section-title">Статистика</h2>
            <div class="stat-item">
                <h3 class="stat-number">5000+</h3>
                <p class="stat-label">Недовольных пациентов</p>
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
            <h2 class="section-title">Наши Сертификации и Аккредитации</h2>
            
            <div class="contact-grid" style="margin-top: 50px;">
                <div class="contact-item">
                    <i class="fas fa-certificate"></i>
                    <h3>ISO 9001:2015</h3>
                    <p>Сертификат качества управления</p>
                </div>

                <div class="contact-item">
                    <i class="fas fa-certificate"></i>
                    <h3>Лицензия Минздрава Республики Беларусь</h3>
                    <p>Официальная лицензия на ведение медицинской деятельности</p>
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
                <h2 style="font-size: 42px; margin-bottom: 20px; color: var(--text);">Присоединитесь к Нашей Команде</h2>
                <p style="font-size: 18px; margin-bottom: 30px; color: var(--muted);">Доверьте своё здоровье профессионалам</p>
                <a href="../index.html#appointment" class="btn btn-primary">Записаться на приём</a>
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

    <script src="../assets/js/main.js"></script>
</body>
</html>
