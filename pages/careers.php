<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/bootstrap.php';

$pageTitle = 'В команду — Больница «В последний путь»';
$NAV_BASE = '..';
$ASSETS = '../assets/';
$extraCss = ['appointment.css'];
require dirname(__DIR__) . '/includes/partials/public_head.php';
require dirname(__DIR__) . '/includes/partials/public_nav.php';
?>
    <section class="appointment app-page" style="padding-top: 100px; padding-bottom: 80px;">
        <div class="container">
            <h1 class="section-title">Работа в нашей команде</h1>
            <p class="section-subtitle">Заполните анкету — ответ придёт на указанный email. Резюме в формате PDF не обязательно.</p>

            <form class="appointment-form app-card" id="teamForm" action="../api/team_submit.php" method="post" enctype="multipart/form-data" style="margin-top: 2rem;">
                <div class="form-row">
                    <div class="form-group">
                        <label for="full_name">ФИО</label>
                        <input type="text" id="full_name" name="full_name" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="phone">Телефон</label>
                        <input type="tel" id="phone" name="phone">
                    </div>
                    <div class="form-group">
                        <label for="position">Желаемая должность</label>
                        <input type="text" id="position" name="position" placeholder="Например, медсестра">
                    </div>
                </div>
                <div class="form-group full-width">
                    <label for="experience">Опыт и образование</label>
                    <textarea id="experience" name="experience" rows="4" placeholder="Кратко опишите опыт работы…"></textarea>
                </div>
                <div class="form-group full-width">
                    <label for="message">Почему вы хотите к нам</label>
                    <textarea id="message" name="message" rows="4"></textarea>
                </div>
                <div class="form-group full-width">
                    <label for="cv">Резюме (PDF, до 5 МБ)</label>
                    <input type="file" id="cv" name="cv" accept="application/pdf">
                </div>
                <button type="submit" class="btn btn-primary">Отправить анкету</button>
            </form>
            <p id="teamFormMsg" class="form-message" style="display:none;margin-top:1rem;"></p>
        </div>
    </section>

<?php
require dirname(__DIR__) . '/includes/partials/public_footer.php';
?>
    <script src="../assets/js/main.js"></script>
    <script>
    document.getElementById('teamForm').addEventListener('submit', function(e) {
        e.preventDefault();
        var fd = new FormData(this);
        fetch(this.action, { method: 'POST', body: fd, credentials: 'same-origin' })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                var m = document.getElementById('teamFormMsg');
                m.style.display = 'block';
                if (data.ok) {
                    m.className = 'form-message success';
                    m.textContent = 'Анкета отправлена. Спасибо!';
                    document.getElementById('teamForm').reset();
                } else {
                    m.className = 'form-message error';
                    m.textContent = data.error || 'Ошибка';
                }
            })
            .catch(function() {
                var m = document.getElementById('teamFormMsg');
                m.style.display = 'block';
                m.className = 'form-message error';
                m.textContent = 'Ошибка сети';
            });
    });
    </script>
</body>
</html>
