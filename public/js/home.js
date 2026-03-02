/******/ (() => { // webpackBootstrap
/*!******************************!*\
  !*** ./resources/js/home.js ***!
  \******************************/
/**
 * CLOTHR Homepage - Newsletter subscription and interactions
 */
document.addEventListener('DOMContentLoaded', function () {
  var newsletterForm = document.getElementById('home-newsletter-form');
  var messageEl = document.getElementById('home-newsletter-message');
  if (newsletterForm && messageEl) {
    newsletterForm.addEventListener('submit', function (e) {
      e.preventDefault();
      var input = newsletterForm.querySelector('input[type="email"]');
      var email = input ? input.value.trim() : '';
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
/******/ })()
;