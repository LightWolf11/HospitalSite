<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/bootstrap.php';

$pageTitle = 'FAQ — Часто задаваемые вопросы';
$NAV_BASE = '..';
$ASSETS = '../assets/';
$extraCss = ['hero.css', 'faq.css'];
require dirname(__DIR__) . '/includes/partials/public_head.php';
require dirname(__DIR__) . '/includes/partials/public_nav.php';
?>
    <section class="hero" style="min-height: auto; padding: 50px 0;">
        <div class="hero-content" style="text-align: center; margin: 0 auto; max-width: 900px; padding: 0 16px;">
            <h1 class="hero-title">Часто задаваемые вопросы</h1>
            <p class="hero-subtitle">Найдите ответы на интересующие вас вопросы</p>
        </div>
    </section>

    <section class="services" style="padding: 10px 0;">
        <div class="container">
            <div class="faq-container">
                <h2 class="section-title">Ответы на ваши вопросы</h2>
                <p class="section-subtitle" style="padding-bottom: 20px;">Часто задаваемые вопросы пациентов о нашем медицинском центре</p>

                <div class="faq-item">
                    <div class="faq-question">
                        <h3>Какой график работы у больницы?</h3>
                        <span class="faq-toggle">▼</span>
                    </div>
                    <div class="faq-answer">
                        <p>Больница работает в следующем режиме: понедельник–пятница с 8:00 до 17:00. Ургентные случаи обслуживаются круглосуточно. Рекомендуется предварительно записаться на приём через форму на сайте (нужна регистрация пациента).</p>
                    </div>
                </div>
                <div class="faq-item">
                    <div class="faq-question">
                        <h3>Как записаться на приём к врачу?</h3>
                        <span class="faq-toggle">▼</span>
                    </div>
                    <div class="faq-answer">
                        <p>Зарегистрируйтесь в личном кабинете пациента, войдите и выберите врача, дату и время на главной странице. Также можно позвонить по телефону +375 33 622-67-27 или прийти лично.</p>
                    </div>
                </div>
                <div class="faq-item">
                    <div class="faq-question">
                        <h3>Какие услуги предоставляет больница?</h3>
                        <span class="faq-toggle">▼</span>
                    </div>
                    <div class="faq-answer">
                        <p>Кардиология, пульмонология, стоматология, офтальмология, неврология, ортопедия, диагностика и профилактика. Актуальный список — <a href="services.php">страница «Услуги»</a> и блок на главной.</p>
                    </div>
                </div>
                <div class="faq-item">
                    <div class="faq-question">
                        <h3>Какие документы нужно иметь при визите?</h3>
                        <span class="faq-toggle">▼</span>
                    </div>
                    <div class="faq-answer">
                        <p>Документ, удостоверяющий личность; при наличии — выписки, анализы, направление врача. Это помогает корректно оформить медицинскую карту.</p>
                    </div>
                </div>
                <div class="faq-item">
                    <div class="faq-question">
                        <h3>Сколько стоит консультация врача?</h3>
                        <span class="faq-toggle">▼</span>
                    </div>
                    <div class="faq-answer">
                        <p>Стоимость зависит от специальности. Уточняется при записи. Для отдельных категорий граждан возможны льготы.</p>
                    </div>
                </div>
                <div class="faq-item">
                    <div class="faq-question">
                        <h3>Какие методы диагностики вы используете?</h3>
                        <span class="faq-toggle">▼</span>
                    </div>
                    <div class="faq-answer">
                        <p>УЗИ, МРТ, КТ, ЭКГ, ЭЭГ, лабораторные исследования и др. Оборудование и специалисты соответствуют современным стандартам.</p>
                    </div>
                </div>
                <div class="faq-item">
                    <div class="faq-question">
                        <h3>Сохраняется ли конфиденциальность пациентов?</h3>
                        <span class="faq-toggle">▼</span>
                    </div>
                    <div class="faq-answer">
                        <p>Да. Мы соблюдаем требования законодательства о персональных данных и врачебной тайне.</p>
                    </div>
                </div>
                <div class="faq-item">
                    <div class="faq-question">
                        <h3>Есть ли парковка у больницы?</h3>
                        <span class="faq-toggle">▼</span>
                    </div>
                    <div class="faq-answer">
                        <p>Да, есть парковка для посетителей. Режим уточняйте в регистратуре.</p>
                    </div>
                </div>
                <div class="faq-item">
                    <div class="faq-question">
                        <h3>Проводите ли телемедицинские консультации?</h3>
                        <span class="faq-toggle">▼</span>
                    </div>
                    <div class="faq-answer">
                        <p>Онлайн-форматы доступны по согласованию при записи на приём.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section style="background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); padding: 60px 0; color: white; text-align: center;">
        <div class="container">
            <h2 style="font-family: 'Cormorant Garamond', Georgia, serif; font-size: 42px; margin-bottom: 20px;">Остались вопросы?</h2>
            <p style="font-size: 18px; margin-bottom: 30px;">Свяжитесь с нами через форму обратной связи</p>
            <a href="../index.php#contact-feedback" class="btn btn-primary">Написать нам</a>
        </div>
    </section>

<?php
require dirname(__DIR__) . '/includes/partials/public_footer.php';
?>
    <script src="../assets/js/main.js"></script>
    <script src="../assets/js/faq.js"></script>
</body>
</html>
