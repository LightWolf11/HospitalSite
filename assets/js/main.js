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
                    const doc = iframe.contentDocument;
                    if (doc) {
                        const section = doc.querySelector('section');
                        const heights = [];
                        if (section && Number.isFinite(section.scrollHeight)) heights.push(section.scrollHeight);
                        if (doc.body && Number.isFinite(doc.body.scrollHeight)) heights.push(doc.body.scrollHeight);
                        if (doc.documentElement && Number.isFinite(doc.documentElement.scrollHeight)) heights.push(doc.documentElement.scrollHeight);
                        const height = heights.length ? Math.max(...heights) : 0;
                        if (height > 0) iframe.style.height = (height + 10) + 'px';
                    }
                } catch (e) {
                    iframe.style.height = 'auto';
                }
            }, 250);
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

    const revealObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
                revealObserver.unobserve(entry.target);
            }
        });
    }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });

    document.querySelectorAll('.reveal').forEach(el => revealObserver.observe(el));

    const navbar = document.querySelector('.navbar');
    if (navbar) {
        window.addEventListener('scroll', () => {
            navbar.classList.toggle('scrolled', window.scrollY > 50);
        });
    }
});
