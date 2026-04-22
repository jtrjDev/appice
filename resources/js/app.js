// Dark Mode Manager
window.themeManager = {
    current() {
        return document.documentElement.classList.contains('dark') ? 'dark' : 'light';
    },
    toggle() {
        const isDark = document.documentElement.classList.toggle('dark');
        localStorage.setItem('theme', isDark ? 'dark' : 'light');
        window.dispatchEvent(new CustomEvent('theme-changed', { detail: { theme: isDark ? 'dark' : 'light' } }));
    },
    set(theme) {
        if (theme === 'dark') {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
        localStorage.setItem('theme', theme);
    },
};

// Remova a parte do DOMContentLoaded que adiciona .loaded