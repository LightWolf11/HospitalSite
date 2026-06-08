
document.addEventListener('DOMContentLoaded', function() {
    const section = document.querySelector('section');
    if (section && window.parent !== window) {
        const resizeObserver = new ResizeObserver(() => {
            const height = document.body.scrollHeight || section.scrollHeight;
            window.parent.postMessage({
                type: 'resize',
                section: 'services',
                height: height
            }, '*');
        });
        resizeObserver.observe(section);
        const height = document.body.scrollHeight || section.scrollHeight;
        window.parent.postMessage({
            type: 'resize',
            section: 'services',
            height: height
        }, '*');
    }

    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.animation = 'slideUp 0.6s ease forwards';
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);

    document.querySelectorAll('.service-card').forEach(el => {
        observer.observe(el);
    });
});
