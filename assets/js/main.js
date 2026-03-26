window.addEventListener('DOMContentLoaded', function() {
    const hamburgerBtn = document.getElementById('hamburgerBtn');
    const navMenu = document.getElementById('navMenu');

    if (hamburgerBtn && navMenu) {
        hamburgerBtn.addEventListener('click', function() {
            hamburgerBtn.classList.toggle('active');
            navMenu.classList.toggle('active');
        });

        document.querySelectorAll('.nav-link').forEach(link => {
            link.addEventListener('click', function() {
                hamburgerBtn.classList.remove('active');
                navMenu.classList.remove('active');
            });
        });
    }

    window.addEventListener('message', function(event) {
        if (event.data.type === 'resize') {
            const iframeElement = document.querySelector(`iframe[src*="${event.data.section}"]`);
            if (iframeElement) {
                iframeElement.style.height = event.data.height + 'px';
            }
        }
    });

    const iframes = document.querySelectorAll('iframe');
    iframes.forEach((iframe, index) => {
        iframe.addEventListener('load', function() {
            setTimeout(() => {
                try {
                    if (iframe.contentDocument && iframe.contentDocument.body) {
                        const body = iframe.contentDocument.body;
                        const section = iframe.contentDocument.querySelector('section');
                        
                        // Get the natural height of section content
                        const height = section ? section.scrollHeight : body.scrollHeight;
                        iframe.style.height = (height + 10) + 'px';
                    }
                } catch (e) {
                    iframe.style.height = 'auto';
                }
            }, 150);
        });
    });



    const scrollToTopBtn = document.getElementById('scrollToTop');
    if (scrollToTopBtn) {
        window.addEventListener('scroll', function() {
            if (window.pageYOffset > 400) {
                scrollToTopBtn.classList.add('show');
            } else {
                scrollToTopBtn.classList.remove('show');
            }
        });

        scrollToTopBtn.addEventListener('click', function() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }

    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    window.addEventListener('scroll', function() {
        let current = '';
        const sections = document.querySelectorAll('section');

        sections.forEach(section => {
            const sectionTop = section.offsetTop;
            if (pageYOffset >= sectionTop - 100) {
                current = section.getAttribute('id');
            }
        });

        document.querySelectorAll('.nav-link').forEach(link => {
            link.classList.remove('active');
            if (link.getAttribute('href').slice(1) === current) {
                link.classList.add('active');
            }
        });
    });
});

console.log('%cБольница "В последний путь" - Сайт больницы', 'color: #0066cc; font-size: 20px; font-weight: bold');
console.log('%cВерсия: 2.0.0 (iframe архитектура)', 'color: #666');
console.log('%cСекции загружаются в отдельные фреймы', 'color: #00b4d8');
