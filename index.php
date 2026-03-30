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
                <a href="pages/services.html" class="btn btn-secondary">Узнать о услугах</a>
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
            <div class="services-grid">
                <div class="service-card">
                    <div class="service-icon">
                        <img src="assets/images/services/Kardio.jpg" alt="Кардиология" class="doctor-photo">
                    </div>
                    <h3>Кардиология</h3>
                </div>
                <div class="service-card">
                    <div class="service-icon">
                        <img src="assets/images/services/Pyls.jpg" alt="Пульмонология" class="doctor-photo">
                    </div>
                    <h3>Пульмонология</h3>
                </div>
                <div class="service-card">
                    <div class="service-icon">
                        <img src="assets/images/services/Stomat.jpg" alt="Стоматология" class="doctor-photo">
                    </div>
                    <h3>Стоматология</h3>
                </div>
                <div class="service-card">
                    <div class="service-icon">
                        <img src="assets/images/services/Oftalm.jpg" alt="Офтальмология" class="doctor-photo">
                    </div>
                    <h3>Офтальмология</h3>
                </div>
                <div class="service-card">
                    <div class="service-icon">
                        <img src="assets/images/services/Nevro.jpg" alt="Неврология" class="doctor-photo">
                    </div>
                    <h3>Неврология</h3>
                </div>
                <div class="service-card">
                    <div class="service-icon">
                        <img src="assets/images/services/Orto.jpg" alt="Ортопедия" class="doctor-photo">
                    </div>
                    <h3>Ортопедия</h3>
                </div>
            </div>
        </div>
    </section>

    <section class="doctors" id="doctors">
        <div class="container">
            <h2 class="section-title">Наши Врачи</h2>
            <p class="section-subtitle">Опытные специалисты с высокой квалификацией</p>
            <div class="doctors-grid">
                <div class="doctor-card">
                    <div class="doctor-image">
                        <img src="assets/images/doctors/Gorov.jpg" alt="Горов Дмитрий" class="doctor-photo">
                    </div>
                    <h3>Горов Дмитрий</h3>
                    <p class="specialty">Кардиолог</p>
                    <p class="bio">Стаж работы: 5 лет, высшая квалификаия</p>
                    <div class="social-links">
                        <a href="#" class="social-icon" title="Email"><i class="fas fa-envelope"></i></a>
                        <a href="#" class="social-icon" title="Phone"><i class="fas fa-phone"></i></a>
                    </div>
                </div>
                <div class="doctor-card">
                    <div class="doctor-image">
                        <img src="assets/images/doctors/Kasperovich.jpg" alt="Касперович Артем" class="doctor-photo">
                    </div>
                    <h3>Касперович Артем</h3>
                    <p class="specialty">Стоматолог</p>
                    <p class="bio">Стаж работы: 12 лет, специалист по протезированию</p>
                    <div class="social-links">
                        <a href="#" class="social-icon" title="Email"><i class="fas fa-envelope"></i></a>
                        <a href="#" class="social-icon" title="Phone"><i class="fas fa-phone"></i></a>
                    </div>
                </div>
                <div class="doctor-card">
                    <div class="doctor-image">
                        <img src="assets/images/doctors/Prokydin.jpg" alt="Прокудин Арсений" class="doctor-photo">
                    </div>
                    <h3>Прокудин Арсений</h3>
                    <p class="specialty">Невролог</p>
                    <p class="bio">Стаж работы: 7 лет, кандидат медицинских наук</p>
                    <div class="social-links">
                        <a href="#" class="social-icon" title="Email"><i class="fas fa-envelope"></i></a>
                        <a href="#" class="social-icon" title="Phone"><i class="fas fa-phone"></i></a>
                    </div>
                </div>
                <div class="doctor-card">
                    <div class="doctor-image">
                        <img src="assets/images/doctors/Greben.jpg" alt="Гребень Егор" class="doctor-photo">
                    </div>
                    <h3>Гребень Егор</h3>
                    <p class="specialty">Окулист</p>
                    <p class="bio">Стаж работы: 20 лет, специалист по лазерной коррекции</p>
                    <div class="social-links">
                        <a href="#" class="social-icon" title="Email"><i class="fas fa-envelope"></i></a>
                        <a href="#" class="social-icon" title="Phone"><i class="fas fa-phone"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="appointment" id="appointment">
        <div class="container">
            <h2 class="section-title">Записаться на приём</h2>
            <p class="section-subtitle">Онлайн бронирование</p>
            <form class="appointment-form" id="appointmentForm">
                <div class="form-row">
                    <div class="form-group">
                        <label for="name">Ваше имя</label>
                        <input type="text" id="name" name="name" placeholder="Дарья Новик" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" placeholder="example@mail.com" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="phone">Телефон</label>
                        <input type="tel" id="phone" name="phone" placeholder="+375 33 000 00 00" required>
                    </div>
                    <div class="form-group">
                        <label for="specialty">Специальность врача</label>
                        <select id="specialty" name="specialty" required>
                            <option value="">Выберите специальность</option>
                            <option value="cardio">Кардиология</option>
                            <option value="dent">Стоматология</option>
                            <option value="neuro">Неврология</option>
                            <option value="eye">Офтальмология</option>
                            <option value="ortho">Ортопедия</option>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="date">Желаемая дата</label>
                        <input type="date" id="date" name="date" placeholder="дд.мм.гггг" required>
                    </div>
                    <div class="form-group">
                        <label for="time">Время приёма</label>
                        <select id="time" name="time" required>
                            <option value="">Выберите время</option>
                            <option value="09:00">09:00</option>
                            <option value="10:00">10:00</option>
                            <option value="11:00">11:00</option>
                            <option value="14:00">14:00</option>
                            <option value="15:00">15:00</option>
                            <option value="16:00">16:00</option>
                        </select>
                    </div>
                </div>
                <div class="form-group full-width">
                    <label for="message">Дополнительная информация</label>
                    <textarea id="message" name="message" rows="5" placeholder="Опишите ваши симптомы или вопросы..."></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Отправить заявку</button>
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
            <h2 class="section-title">Обратная связь</h2>
            <br>
            <form class="appointment-form" id="appointmentForm">
                <div class="form-row">
                    <div class="form-group">
                        <label for="name">Ваше имя</label>
                        <input type="text" id="name" name="name" placeholder="Дарья Новик" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" placeholder="example@mail.com" required>
                    </div>
                </div>
                <div class="form-group full-width">
                    <label for="message">Описание</label>
                    <textarea id="message" name="message" rows="5" placeholder="Опишите максимально подробно ваш вопрос..."></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Отправить заявку</button>
            </form>
        </div>
    </section>
<?php
require __DIR__ . '/includes/partials/public_footer.php';
?>
    <script src="assets/js/main.js"></script>
    <script src="assets/js/site-api.js"></script>
    <script src="asssets/js/appointment.js"></script>
</body>
</html>