document.querySelectorAll('.faq-question').forEach(question => {
    question.addEventListener('click', function() {
        const faqItem = this.parentElement;
        faqItem.classList.toggle('active');
        // Закрытие других элементов (опционально)
        // document.querySelectorAll('.faq-item').forEach(item => {
        //     if (item !== faqItem) {
        //         item.classList.remove('active');
        //     }
        // });
    });
});