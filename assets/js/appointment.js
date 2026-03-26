

document.addEventListener('DOMContentLoaded', function() {
    const section = document.querySelector('section');
    if (section && window.parent !== window) {
        const resizeObserver = new ResizeObserver(() => {
            const height = document.body.scrollHeight || section.scrollHeight;
            window.parent.postMessage({
                type: 'resize',
                section: 'appointment',
                height: height
            }, '*');
        });
        resizeObserver.observe(section);
        const height = document.body.scrollHeight || section.scrollHeight;
        window.parent.postMessage({
            type: 'resize',
            section: 'appointment',
            height: height
        }, '*');
    }
});

document.addEventListener('DOMContentLoaded', function() {
    const appointmentForm = document.getElementById('appointmentForm');
    
    if (appointmentForm) {
        const dateInput = document.getElementById('date');
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


        const formFields = document.querySelectorAll('.appointment-form input, .appointment-form select, .appointment-form textarea');
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
                name: document.getElementById('name').value,
                email: document.getElementById('email').value,
                phone: document.getElementById('phone').value,
                specialty: document.getElementById('specialty').value,
                date: document.getElementById('date').value,
                time: document.getElementById('time').value,
                message: document.getElementById('message').value
            };

            if (!validateForm(formData)) {
                showMessage('Пожалуйста, заполните все поля корректно', 'error');
                return;
            }

            console.log('Данные формы:', formData);
            showMessage('✓ Спасибо! Ваша заявка отправлена. Мы свяжемся с вами в течение 24 часов.', 'success');

            appointmentForm.reset();

            setTimeout(() => {
                const formMessage = document.getElementById('formMessage');
                if (formMessage) formMessage.style.display = 'none';
            }, 4000);
        });
    }
});



function validateForm(data) {
    if (!data.name || data.name.trim().length < 2) return false;
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!data.email || !emailRegex.test(data.email)) return false;
    const phoneRegex = /^[\d\s\-\(\)\+]*$/;
    if (!data.phone || data.phone.length < 10 || !phoneRegex.test(data.phone)) return false;
    if (!data.specialty) return false;
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
