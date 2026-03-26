

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
