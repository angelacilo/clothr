/**
 * CLOTHR Homepage - Newsletter subscription and interactions
 */
document.addEventListener('DOMContentLoaded', function () {
    const newsletterForm = document.getElementById('home-newsletter-form');
    const messageEl = document.getElementById('home-newsletter-message');

    if (newsletterForm && messageEl) {
        newsletterForm.addEventListener('submit', function (e) {
            e.preventDefault();
            const input = newsletterForm.querySelector('input[type="email"]');
            const email = input ? input.value.trim() : '';

            if (!email) {
                messageEl.textContent = 'Please enter your email address.';
                messageEl.style.color = '#c0392b';
                return;
            }

            // Simulate subscription (replace with actual API call)
            messageEl.textContent = 'Thank you for subscribing!';
            messageEl.style.color = '#198754';
            input.value = '';
        });
    }
});
