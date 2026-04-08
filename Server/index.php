<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';

$pageTitle = 'Больница "В последний путь"';
$NAV_BASE = '';
$ASSETS = 'assets/';
$extraCss = ['hero.css', 'services.css', 'doctors.css', 'appointment.css', 'stats.css', 'contact.css'];

require __DIR__ . '/includes/partials/public_head.php';
require __DIR__ . '/includes/partials/public_nav.php';
?>

    <section class="hero" id="home">
        <div class="hero-content">
            <h1 class="hero-title">Ваше здоровье - наш приоритет</h1>
            <p class="hero-subtitle"><br>Современное медицинское обслуживание высочайшего качества</p>
            <div class="hero-buttons">
                <a href="#appointment" class="btn btn-primary">Записаться на приём</a>
                <a href="pages/services.php" class="btn btn-secondary">Узнать о услугах</a>
            </div>
        </div>
        <div class="hero-image">
            <div class="hero-slider" id="heroSlider">
                <div class="hero-slide active" style="background-image: url('assets/images/1.jpg');"></div>
                <div class="hero-slide" style="background-image: url('assets/images/2.jpg');"></div>
                <div class="hero-slide" style="background-image: url('assets/images/3.jpg');"></div>
                <div class="hero-slider-overlay"></div>
                <div class="hero-dots">
                    <button class="hero-dot active" type="button" aria-label="Слайд 1"></button>
                    <button class="hero-dot" type="button" aria-label="Слайд 2"></button>
                    <button class="hero-dot" type="button" aria-label="Слайд 3"></button>
                </div>
            </div>
        </div>
    </section>

    <section class="services" id="services">
        <div class="container">
            <h2 class="section-title">Наши Услуги</h2>
            <p class="section-subtitle">Полный спектр медицинских услуг для вашего здоровья</p>
            <div class="services-grid" id="servicesGrid">
                <p class="section-subtitle" style="grid-column:1/-1;">Загрузка услуг…</p>
            </div>
        </div>
    </section>

    <section class="doctors" id="doctors">
        <div class="container">
            <h2 class="section-title">Наши Врачи</h2>
            <p class="section-subtitle">Опытные специалисты с высокой квалификацией</p>
            <div class="doctors-grid" id="doctorsGrid">
                <p class="section-subtitle" style="grid-column:1/-1;">Загрузка врачей…</p>
            </div>
        </div>
    </section>

    <section class="appointment" id="appointment">
        <div class="container">
            <h2 class="section-title">Записаться на приём</h2>
            <p class="section-subtitle">Онлайн бронирование (нужна регистрация пациента)</p>
            <p id="appointmentGuestHint" class="section-subtitle" style="display:none;">
                <a href="cabinet/register.php">Зарегистрируйтесь</a> или
                <a href="cabinet/login.php">войдите</a>, чтобы выбрать врача и время.
            </p>
            <form class="appointment-form" id="appointmentForm" style="display:none;">
                <div class="form-row">
                    <div class="form-group">
                        <label for="doctor_profile_id">Врач</label>
                        <select id="doctor_profile_id" name="doctor_profile_id" required>
                            <option value="">Загрузка списка…</option>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="date">Желаемая дата</label>
                        <input type="date" id="date" name="date" required>
                    </div>
                    <div class="form-group">
                        <label for="time">Время приёма</label>
                        <select id="time" name="time" required>
                            <option value="">Выберите время</option>
                            <option value="09:00">09:00</option>
                            <option value="10:00">10:00</option>
                            <option value="11:00">11:00</option>
                            <option value="12:00">12:00</option>
                            <option value="13:00">13:00</option>
                            <option value="14:00">14:00</option>
                            <option value="15:00">15:00</option>
                            <option value="16:00">16:00</option>
                            <option value="17:00">17:00</option>
                        </select>
                    </div>
                </div>
                <div class="form-group full-width">
                    <label for="message">Дополнительная информация</label>
                    <textarea id="message" name="message" rows="5" placeholder="Опишите симптомы или вопросы..."></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Записаться</button>
            </form>
            <div id="formMessage" class="form-message" style="display: none;"></div>
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

    <section class="contact" id="contact">
        <div class="container">
            <h2 class="section-title">Свяжитесь с нами</h2>
            <div class="contact-grid">
                <div class="contact-item">
                    <i class="fas fa-map-marker-alt"></i>
                    <h3>Адрес</h3>
                    <p>пл. Свободы, 13<br>Брест, 224030</p>
                </div>
                <div class="contact-item">
                    <i class="fas fa-phone"></i>
                    <h3>Телефон</h3>
                    <p><a href="tel:+375336226727">+375 33 622 67 27</a><br>Пн-Пт: 8:00-17:00</p>
                </div>
                <div class="contact-item">
                    <i class="fas fa-envelope"></i>
                    <h3>Email</h3>
                    <p><a href="mailto:brest@kbp.by">brest@kbp.by</a><br>Ответ в течение 24 часов</p>
                </div>
                <div class="contact-item">
                    <i class="fas fa-clock"></i>
                    <h3>График работы</h3>
                    <p>Пн-Пт: 8:00-17:00<br>Сб-Вс: Выходной</p>
                </div>
            </div>
            <br>
            </section>
    <section class="contact" id="contact-feedback">  
            <h2 class="section-title">Обратная связь</h2>
            <br>
            <form class="appointment-form" id="contactFeedbackForm">
                <div class="form-row">
                    <div class="form-group">
                        <label for="contactName">Ваше имя</label>
                        <input type="text" id="contactName" name="name" placeholder="Дарья Новик" required>
                    </div>
                    <div class="form-group">
                        <label for="contactEmail">Email</label>
                        <input type="email" id="contactEmail" name="email" placeholder="example@mail.com" required>
                    </div>
                </div>
                <div class="form-group full-width">
                    <label for="contactMessage">Описание</label>
                    <textarea id="contactMessage" name="message" rows="5" placeholder="Опишите максимально подробно ваш вопрос..."></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Отправить</button>
            </form>
        </div>
    </section>

<?php
require __DIR__ . '/includes/partials/public_footer.php';
?>
    <script src="assets/js/main.js"></script>
    <script src="assets/js/site-api.js"></script>
    <script src="assets/js/appointment.js"></script>
</body>
</html>
