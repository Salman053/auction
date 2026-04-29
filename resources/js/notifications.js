document.addEventListener('DOMContentLoaded', () => {
    const trigger = document.getElementById('notification-trigger');
    const dropdown = document.getElementById('notification-dropdown-menu');

    if (!trigger || !dropdown) return;

    trigger.addEventListener('click', (e) => {
        e.stopPropagation();
        const isOpen = !dropdown.classList.contains('hidden');
        if (isOpen) {
            dropdown.classList.add('hidden');
        } else {
            dropdown.classList.remove('hidden');
        }
    });

    document.addEventListener('click', (e) => {
        if (!dropdown.contains(e.target) && !trigger.contains(e.target)) {
            dropdown.classList.add('hidden');
        }
    });

    // Close on Escape
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            dropdown.classList.add('hidden');
        }
    });
});
