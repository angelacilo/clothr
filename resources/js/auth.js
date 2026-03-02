/**
 * CLOTHR Auth Page - Tab switching and form interactions
 */
document.addEventListener('DOMContentLoaded', function () {
    const loginTab = document.querySelector('[data-tab="login"]');
    const registerTab = document.querySelector('[data-tab="register"]');
    const loginForm = document.getElementById('auth-login-form');
    const registerForm = document.getElementById('auth-register-form');

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
    const initialTab = document.body.dataset.activeTab || 'login';
    activateTab(initialTab);
});
