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
            <h1 class="hero-title">Лицензия медицинской деятельности</h1>
            <p class="hero-subtitle">Документ подтверждает направления клиники, представленные на сайте</p>
        </div>
        <div class="hero-image">
            <div class="hero-slider" id="heroSlider">
                <div class="hero-slide active" style="background-image: url('../assets/images/4.jpg');"></div>
                <div class="hero-slide" style="background-image: url('../assets/images/5.jpg');"></div>
                <div class="hero-slider-overlay"></div>
                <div class="hero-dots">
                    <button class="hero-dot active" type="button" aria-label="Слайд 1"></button>
                    <button class="hero-dot" type="button" aria-label="Слайд 2"></button>
                </div>
            </div>
        </div>
    </section>

    <section class="services" style="padding: 80px 0 60px;">
        <div class="container">
            <h2 class="section-title">Сведения о лицензии</h2>
            <p class="section-subtitle">Описание лицензии под услуги и адрес нашей клиники</p>

            <div style="display: grid; grid-template-columns: repeat(2, minmax(280px, 1fr)); gap: 32px; margin-top: 32px;">
                <div style="background: var(--bg-card); border: 1px solid var(--line); border-radius: 24px; padding: 32px; box-shadow: var(--shadow);">
                    <h3>Основные данные</h3>
                    <dl style="margin-top: 20px; color: var(--text); line-height: 1.8;">
                        <dt><strong>Наименование органа, предоставившего лицензию:</strong></dt>
                        <dd>Министерство здравоохранения Республики Беларусь</dd>
                        <dt><strong>Вид деятельности:</strong></dt>
                        <dd>Медицинская деятельность</dd>
                        <dt><strong>Статус лицензии:</strong></dt>
                        <dd>Действующая</dd>
                        <dt><strong>Тип лицензиата:</strong></dt>
                        <dd>Юридическое лицо Республики Беларусь</dd>
                        <dt><strong>Наименование лицензиата:</strong></dt>
                        <dd>УЗ "В последний путь"</dd>
                        <dt><strong>УНП:</strong></dt>
                        <dd>193298975</dd>
                    </dl>
                </div>
                <div style="background: var(--bg-card); border: 1px solid var(--line); border-radius: 24px; padding: 32px; box-shadow: var(--shadow);">
                    <h3>Реквизиты</h3>
                    <dl style="margin-top: 20px; color: var(--text); line-height: 1.8;">
                        <dt><strong>Номер лицензии:</strong></dt>
                        <dd>М-8416</dd>
                        <dt><strong>Номер лицензии в ЕРЛ:</strong></dt>
                        <dd>123456789012</dd>
                        <dt><strong>Номер принятия решения:</strong></dt>
                        <dd>27.1</dd>
                        <dt><strong>Дата принятия решения:</strong></dt>
                        <dd>26.03.2026</dd>
                        <dt><strong>Изменение лицензии на основании:</strong></dt>
                        <dd>постановление коллегии</dd>
                        <dt><strong>Дата добавления в ЕРЛ:</strong></dt>
                        <dd>26.03.2026</dd>
                    </dl>
                </div>
            </div>

            <div style="margin-top: 40px; background: var(--bg-card); border: 1px solid var(--line); border-radius: 24px; padding: 32px; box-shadow: var(--shadow);">
                <h3>Места оказания работ / услуг</h3>
                <p style="margin-top: 16px; color: var(--muted);">Филиал клиники и лицензируемые направления деятельности соответствуют услугам сайта.</p>
                <div style="overflow-x: auto; margin-top: 24px;">
                    <table style="width: 100%; border-collapse: collapse; min-width: 720px;">
                        <thead style="background: var(--primary-color); color: #fff;">
                            <tr>
                                <th style="padding: 18px 16px; text-align: left; font-weight: 600;">№</th>
                                <th style="padding: 18px 16px; text-align: left; font-weight: 600;">Адрес / место работ</th>
                                <th style="padding: 18px 16px; text-align: left; font-weight: 600;">Работы и услуги</th>
                                <th style="padding: 18px 16px; text-align: left; font-weight: 600;">Статус услуги</th>
                            </tr>
                        </thead>
                        <tbody style="color: var(--text);">
                            <tr style="border-bottom: 1px solid var(--line);">
                                <td style="padding: 18px 16px; vertical-align: top;">1</td>
                                <td style="padding: 18px 16px; vertical-align: top;">г. Брест, пл. Свободы 13</td>
                                <td style="padding: 18px 16px; vertical-align: top;">
                                    Кардиология<br>
                                    Пульмонология<br>
                                    Стоматология<br>
                                    Офтальмология<br>
                                    Неврология<br>
                                    Ортопедия<br>
                                    Лабораторные анализы<br>
                                    УЗИ диагностика<br>
                                    Фармакотерапия<br>
                                    Физиотерапия<br>
                                    Профилактика<br>
                                    Телемедицина
                                </td>
                                <td style="padding: 18px 16px; vertical-align: top;">
                                    Действующая<br>
                                    Действующая<br>
                                    Действующая<br>
                                    Действующая<br>
                                    Действующая<br>
                                    Действующая<br>
                                    Действующая<br>
                                    Действующая<br>
                                    Действующая<br>
                                    Действующая<br>
                                    Действующая<br>
                                    Действующая
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div style="margin-top: 28px; color: var(--text);">
                    <h4>Информация об изменениях или дополнениях</h4>
                    <p style="margin-top: 12px; color: var(--muted);">Изменения лицензии зарегистрированы на основании постановления коллегии.</p>
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
