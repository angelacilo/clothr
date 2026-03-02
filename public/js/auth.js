/******/ (() => { // webpackBootstrap
/*!******************************!*\
  !*** ./resources/js/auth.js ***!
  \******************************/
/**
 * CLOTHR Auth Page - Tab switching and form interactions
 */
document.addEventListener('DOMContentLoaded', function () {
  var loginTab = document.querySelector('[data-tab="login"]');
  var registerTab = document.querySelector('[data-tab="register"]');
  var loginForm = document.getElementById('auth-login-form');
  var registerForm = document.getElementById('auth-register-form');
  if (!loginTab || !registerTab || !loginForm || !registerForm) return;
  function activateTab(tab) {
    if (tab === 'login') {
      loginTab.classList.add('active');
      registerTab.classList.remove('active');
      loginForm.classList.add('active');
      registerForm.classList.remove('active');
    } else {
      registerTab.classList.add('active');
      loginTab.classList.remove('active');
      registerForm.classList.add('active');
      loginForm.classList.remove('active');
    }
  }

  // Tab click handlers - prevent navigation and switch forms
  loginTab.addEventListener('click', function (e) {
    e.preventDefault();
    if (loginTab.classList.contains('active')) return;
    activateTab('login');
  });
  registerTab.addEventListener('click', function (e) {
    e.preventDefault();
    if (registerTab.classList.contains('active')) return;
    activateTab('register');
  });

  // Set initial tab from URL or data attribute
  var initialTab = document.body.dataset.activeTab || 'login';
  activateTab(initialTab);
});
/******/ })()
;