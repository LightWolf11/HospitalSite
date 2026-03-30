

const API = 'api/index.php';

document.addEventListener('DOMContentLoaded', function() {
    const appointmentForm = document.getElementById('appointmentForm');
    const doctorSelect = document.getElementById('doctor_profile_id');
    const timeSelect = document.getElementById('time');
    const dateInput = document.getElementById('date');
    const guestHint = document.getElementById('appointmentGuestHint');

    if (doctorSelect) {
        fetch(API + '?action=doctors_options', { credentials: 'same-origin' })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                if (!data.ok || !data.doctors) return;
                doctorSelect.innerHTML = '<option value="">Выберите врача</option>' +
                    data.doctors.map(function (d) {
                        return '<option value="' + d.id + '">' +
                            escapeHtml(d.full_name) + ' — ' + escapeHtml(d.specialty || '') +
                            '</option>';
                    }).join('');
            })
            .catch(function () {});
    }

    fetch(API + '?action=me', { credentials: 'same-origin' })
        .then(function (r) { return r.json(); })
        .then(function (data) {
            var u = data.user;
            var canBook = u && u.role !== 'doctor' && (
                u.role === 'patient' || u.role === 'admin' || Number(u.is_admin) === 1
            );
            if (data.ok && canBook) {
                if (guestHint) guestHint.style.display = 'none';
                if (appointmentForm) appointmentForm.style.display = '';
            } else {
                if (guestHint) guestHint.style.display = 'block';
                if (appointmentForm) appointmentForm.style.display = 'none';
            }
        })
        .catch(function () {});

    if (appointmentForm) {
        if (dateInput) {
            const today = new Date();
            const dd = String(today.getDate()).padStart(2, '0');
            const mm = String(today.getMonth() + 1).padStart(2, '0');
            const yyyy = today.getFullYear();

            dateInput.min = yyyy + '-' + mm + '-' + dd;

            const maxDate = new Date(today);
            maxDate.setMonth(maxDate.getMonth() + 3);
            const dd_max = String(maxDate.getDate()).padStart(2, '0');
            const mm_max = String(maxDate.getMonth() + 1).padStart(2, '0');
            const yyyy_max = maxDate.getFullYear();

            dateInput.max = yyyy_max + '-' + mm_max + '-' + dd_max;
        }

        function setTimeOptionsBusy(busyTimes) {
            if (!timeSelect) return;
            const busySet = new Set((busyTimes || []).map(String));
            Array.from(timeSelect.options).forEach(opt => {
                const v = (opt.value || '').trim();
                if (!v) return;
                const baseText = opt.getAttribute('data-base-text') || opt.textContent || v;
                if (!opt.getAttribute('data-base-text')) opt.setAttribute('data-base-text', baseText);
                const isBusy = busySet.has(v);
                opt.disabled = isBusy;
                opt.textContent = isBusy ? (baseText + ' — занято') : baseText;
                if (opt.disabled && timeSelect.value === v) {
                    timeSelect.value = '';
                }
            });
        }

        function refreshBusyTimes() {
            if (!doctorSelect || !dateInput || !timeSelect) return;
            const doctorId = (doctorSelect.value || '').trim();
            const date = (dateInput.value || '').trim();
            if (!doctorId || !date) {
                setTimeOptionsBusy([]);
                return;
            }
            fetch(API + '?action=doctor_busy_times&doctor_profile_id=' + encodeURIComponent(doctorId) + '&date=' + encodeURIComponent(date), {
                credentials: 'same-origin'
            })
                .then(r => r.json())
                .then(data => {
                    if (!data || !data.ok) return;
                    setTimeOptionsBusy(data.busy_times || []);
                })
                .catch(() => {});
        }

        if (doctorSelect) doctorSelect.addEventListener('change', refreshBusyTimes);
        if (dateInput) dateInput.addEventListener('change', refreshBusyTimes);

        const phoneInput = document.getElementById('phone');
        if (phoneInput) {
            phoneInput.addEventListener('input', function(e) {
                let value = e.target.value.replace(/\D/g, '');
                if (value.length > 0) {
                    if (value.length <= 2) {
                        value = value;
                    } else if (value.length <= 5) {
                        value = '+' + value.slice(0, 3) + '(' + value.slice(3);
                    } else if (value.length <= 8) {
                        value = '+' + value.slice(0, 3) + '(' + value.slice(3, 5) + ') ' + value.slice(5);
                    } else {
                        value = '+' + value.slice(0, 3) + '(' + value.slice(3, 5) + ') ' + value.slice(5, 8) + '-' + value.slice(8, 10) + '-' + value.slice(10, 12);
                    }
                }
                e.target.value = value;
            });
        }

        const formFields = document.querySelectorAll('#appointmentForm input, #appointmentForm select, #appointmentForm textarea');
        formFields.forEach(field => {
            field.addEventListener('blur', function() {
                validateField(this);
            });

            field.addEventListener('focus', function() {
                this.style.borderColor = 'white';
            });
        });

        appointmentForm.addEventListener('submit', function(event) {
            event.preventDefault();

            const formData = {
                doctor_profile_id: document.getElementById('doctor_profile_id').value,
                date: document.getElementById('date').value,
                time: document.getElementById('time').value,
                message: document.getElementById('message').value
            };

            if (!validateForm(formData)) {
                showMessage('Пожалуйста, заполните все поля корректно', 'error');
                return;
            }

            fetch(API + '?action=appointment_create', {
                method: 'POST',
                credentials: 'same-origin',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(formData)
            })
                .then(function (r) { return r.json().then(function (body) { return { status: r.status, body: body }; }); })
                .then(function (res) {
                    if (res.body.ok) {
                        showMessage('✓ Запись создана. Проверьте уведомления в личном кабинете.', 'success');
                        appointmentForm.reset();
                        refreshBusyTimes();
                    } else if (res.status === 401) {
                        showMessage('Войдите в личный кабинет пациента, чтобы записаться.', 'error');
                    } else {
                        showMessage(res.body.error || 'Ошибка', 'error');
                    }
                })
                .catch(function () {
                    showMessage('Ошибка сети', 'error');
                });

            setTimeout(() => {
                const formMessage = document.getElementById('formMessage');
                if (formMessage) formMessage.style.display = 'none';
            }, 6000);
        });
    }
});

function escapeHtml(s) {
    const d = document.createElement('div');
    d.textContent = s;
    return d.innerHTML;
}

function validateForm(data) {
    if (!data.doctor_profile_id) return false;
    if (!data.date) return false;
    if (!data.time) return false;
    return true;
}

function showMessage(text, type) {
    const formMessage = document.getElementById('formMessage');
    if (formMessage) {
        formMessage.textContent = text;
        formMessage.className = 'form-message ' + type;
        formMessage.style.display = 'block';
    }
}

function validateField(field) {
    switch(field.id) {
        case 'name':
            if (field.value.trim().length < 2) {
                field.style.borderColor = '#ff6b6b';
            }
            break;
        case 'email':
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(field.value)) {
                field.style.borderColor = '#ff6b6b';
            }
            break;
        case 'phone':
            if (field.value.length < 10) {
                field.style.borderColor = '#ff6b6b';
            }
            break;
    }
}
