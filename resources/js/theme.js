function applyTheme(theme) {
    const shouldBeDark = theme === 'dark';
    document.documentElement.classList.toggle('dark', shouldBeDark);
}

function getStoredTheme() {
    const stored = localStorage.getItem('theme');

    if (stored === 'light' || stored === 'dark') {
        return stored;
    }

    return null;
}

function getPreferredTheme() {
    const stored = getStoredTheme();
    if (stored !== null) {
        return stored;
    }

    return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
}

function setTheme(theme) {
    localStorage.setItem('theme', theme);
    applyTheme(theme);
    updateToggleLabels(theme);
}

function updateToggleLabels(theme) {
    const toggles = document.querySelectorAll('[data-theme-toggle]');
    for (const toggle of toggles) {
        toggle.setAttribute('aria-pressed', theme === 'dark' ? 'true' : 'false');
        const label = toggle.querySelector('[data-theme-label]');
        if (label) {
            label.textContent = theme === 'dark' ? 'Dark' : 'Light';
        }
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const theme = getPreferredTheme();
    applyTheme(theme);
    updateToggleLabels(theme);

    document.addEventListener('click', (event) => {
        const button = event.target instanceof Element ? event.target.closest('[data-theme-toggle]') : null;
        if (!button) {
            return;
        }

        const current = document.documentElement.classList.contains('dark') ? 'dark' : 'light';
        setTheme(current === 'dark' ? 'light' : 'dark');
    });
});

