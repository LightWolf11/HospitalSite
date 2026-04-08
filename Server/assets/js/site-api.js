(function () {
    const path = (window.location && window.location.pathname) ? window.location.pathname : '';
    const API = (/\/(pages|cabinet|doctor|admin)\//.test(path) ? '../api/index.php' : 'api/index.php');

    function esc(s) {
        const d = document.createElement('div');
        d.textContent = s;
        return d.innerHTML;
    }

    function loadServices() {
        const root = document.getElementById('servicesGrid');
        if (!root) return;
        fetch(API + '?action=services_public', { credentials: 'same-origin' })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                if (!data.ok || !data.services || !data.services.length) {
                    root.innerHTML = '<p class="section-subtitle">На данный момент услуги отсутствуют.</p>';
                    return;
                }
                root.innerHTML = data.services.map(function (s) {
                    var img = s.image_url || s.image_path || '';
                    var title = esc(s.title || '');
                    var desc = s.description ? '<p style="margin-top:10px;font-size:15px;color:var(--muted);">' + esc(s.description) + '</p>' : '';
                    return (
                        '<div class="service-card">' +
                        '<div class="service-icon">' +
                        (img ? '<img src="' + esc(img) + '" alt="' + title + '" class="doctor-photo">' : '') +
                        '</div><h3>' + title + '</h3>' + desc + '</div>'
                    );
                }).join('');
            })
            .catch(function () {
                root.innerHTML = '<p class="section-subtitle">Не удалось загрузить услуги (проверьте PHP и БД).</p>';
            });
    }

    function loadDoctors() {
        const root = document.getElementById('doctorsGrid');
        if (!root) return;
        fetch(API + '?action=doctors_public', { credentials: 'same-origin' })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                if (!data.ok || !data.doctors || !data.doctors.length) {
                    root.innerHTML = '<p class="section-subtitle">Список врачей пуст. Добавьте врачей в админ-панели.</p>';
                    return;
                }
                root.innerHTML = data.doctors.map(function (d) {
                    var photo = d.photo_url || d.photo_path || '';
                    var mail = d.contact_email ? 'mailto:' + esc(d.contact_email) : '#';
                    var tel = d.contact_phone ? 'tel:' + esc(d.contact_phone.replace(/\s/g, '')) : '#';
                    return (
                        '<div class="doctor-card">' +
                        '<div class="doctor-image">' +
                        (photo ? '<img src="' + esc(photo) + '" alt="' + esc(d.full_name) + '" class="doctor-photo">' : '') +
                        '</div>' +
                        '<h3>' + esc(d.full_name || '') + '</h3>' +
                        '<p class="specialty">' + esc(d.specialty || '') + '</p>' +
                        '<p class="bio">' + esc(d.bio || '') + '</p>' +
                        '<div class="social-links">' +
                        '<a href="' + mail + '" class="social-icon" title="Email"><i class="fas fa-envelope"></i></a>' +
                        '<a href="' + tel + '" class="social-icon" title="Телефон"><i class="fas fa-phone"></i></a>' +
                        '</div></div>'
                    );
                }).join('');
            })
            .catch(function () {
                root.innerHTML = '<p class="section-subtitle">Не удалось загрузить врачей.</p>';
            });
    }

    function initFeedbackForm() {
        var form = document.getElementById('contactFeedbackForm');
        if (!form) return;
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            var name = document.getElementById('contactName').value.trim();
            var email = document.getElementById('contactEmail').value.trim();
            var message = document.getElementById('contactMessage').value.trim();
            if (name.length < 2 || !email || message.length < 5) {
                alert('Заполните имя, email и текст сообщения.');
                return;
            }
            fetch(API + '?action=feedback', {
                method: 'POST',
                credentials: 'same-origin',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ name: name, email: email, message: message })
            })
                .then(function (r) { return r.json(); })
                .then(function (data) {
                    if (data.ok) {
                        alert('Спасибо! Сообщение отправлено.');
                        form.reset();
                    } else {
                        alert(data.error || 'Ошибка отправки');
                    }
                })
                .catch(function () { alert('Ошибка сети'); });
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        loadServices();
        loadDoctors();
        initFeedbackForm();
    });
})();
