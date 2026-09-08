const root = document.documentElement;

let theme = localStorage.getItem('theme');

if (!theme) {
    theme = window.matchMedia('(prefers-color-scheme: dark)').matches
        ? 'dark'
        : 'light';
}

root.setAttribute('data-theme', theme);

document.addEventListener('DOMContentLoaded', () => {

    const button = document.getElementById('theme-toggle');

    if (!button) {
        return;
    }

    function updateButton() {
        const currentTheme = root.getAttribute('data-theme');

        button.textContent =
            currentTheme === 'dark'
                ? 'Light theme'
                : 'Dark theme';
    }

    updateButton();

    button.addEventListener('click', () => {

        const currentTheme = root.getAttribute('data-theme');

        const newTheme =
            currentTheme === 'dark'
                ? 'light'
                : 'dark';

        root.setAttribute('data-theme', newTheme);

        localStorage.setItem('theme', newTheme);

        updateButton();
    });

});