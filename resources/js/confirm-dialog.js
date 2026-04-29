function iconSvg(type) {
    if (type === 'danger') {
        return `<svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
        </svg>`;
    }

    if (type === 'success') {
        return `<svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>`;
    }

    return `<svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
    </svg>`;
}

function iconClasses(type) {
    if (type === 'danger') {
        return 'bg-rose-50 text-rose-600 dark:bg-rose-500/10 dark:text-rose-400';
    }

    if (type === 'success') {
        return 'bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400';
    }

    return 'bg-brand-gold/10 text-brand-navy dark:bg-white/10 dark:text-white';
}

function confirmButtonClasses(type) {
    if (type === 'danger') {
        return 'bg-rose-600 text-white shadow-rose-200 dark:shadow-none';
    }

    if (type === 'success') {
        return 'bg-emerald-600 text-white shadow-emerald-200 dark:shadow-none';
    }

    return 'bg-brand-navy text-white shadow-slate-200 dark:shadow-none';
}

function getConfirmDialogElements() {
    const root = document.getElementById('confirm-dialog');
    if (!root) {
        return null;
    }

    return {
        root,
        backdrop: document.getElementById('confirm-dialog-backdrop'),
        title: document.getElementById('confirm-dialog-title'),
        message: document.getElementById('confirm-dialog-message'),
        icon: document.getElementById('confirm-dialog-icon'),
        confirm: document.getElementById('confirm-dialog-confirm'),
        cancel: document.getElementById('confirm-dialog-cancel'),
    };
}

function showDialog(detail = {}) {
    const elements = getConfirmDialogElements();
    if (!elements) {
        return;
    }

    const title = detail.title || 'Are you sure?';
    const message = detail.message || '';
    const confirmText = detail.confirmText || 'Confirm';
    const cancelText = detail.cancelText || 'Cancel';
    const type = detail.type || 'info';
    const onConfirm = detail.onConfirm || null;

    elements.title.textContent = title;
    elements.message.textContent = message;
    elements.confirm.textContent = confirmText;
    elements.cancel.textContent = cancelText;

    elements.icon.className = `flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl ${iconClasses(type)}`;
    elements.icon.innerHTML = iconSvg(type);

    elements.confirm.className = `rounded-2xl px-8 py-3.5 text-sm font-black uppercase tracking-widest transition hover:scale-105 active:scale-95 shadow-xl ${confirmButtonClasses(type)}`;
    elements.confirm.dataset.onConfirm = typeof onConfirm === 'string' ? onConfirm : '';

    elements.root.classList.remove('hidden');
    elements.root.setAttribute('aria-hidden', 'false');
    elements.confirm.focus();
}

function hideDialog() {
    const elements = getConfirmDialogElements();
    if (!elements) {
        return;
    }

    elements.root.classList.add('hidden');
    elements.root.setAttribute('aria-hidden', 'true');
    elements.confirm.dataset.onConfirm = '';
}

function executeConfirm() {
    const elements = getConfirmDialogElements();
    if (!elements) {
        return;
    }

    const selector = elements.confirm.dataset.onConfirm;
    if (selector) {
        const form = document.querySelector(selector);
        if (form && typeof form.submit === 'function') {
            form.submit();
        }
    }

    hideDialog();
}

function openFromDataset(trigger) {
    let message = trigger.dataset.confirmMessage;

    const amountSelector = trigger.dataset.confirmAmountSelector;
    if (amountSelector) {
        const amountInput = document.querySelector(amountSelector);
        const amountRaw = amountInput && 'value' in amountInput ? amountInput.value : null;
        const amount = Number.parseInt(String(amountRaw || '0'), 10);
        const formatted = Number.isFinite(amount) ? amount.toLocaleString() : '0';
        message = (message || '').replaceAll('{amount}', formatted);
    }

    showDialog({
        title: trigger.dataset.confirmTitle,
        message,
        confirmText: trigger.dataset.confirmText,
        cancelText: trigger.dataset.confirmCancelText,
        type: trigger.dataset.confirmType,
        onConfirm: trigger.dataset.confirmOnConfirm,
    });
}

document.addEventListener('DOMContentLoaded', () => {
    const elements = getConfirmDialogElements();
    if (!elements) {
        return;
    }

    elements.backdrop?.addEventListener('click', hideDialog);
    elements.cancel.addEventListener('click', hideDialog);
    elements.confirm.addEventListener('click', executeConfirm);

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            hideDialog();
        }
    });

    window.addEventListener('confirm', (event) => {
        showDialog(event.detail || {});
    });

    document.addEventListener('click', (event) => {
        const trigger = event.target instanceof Element ? event.target.closest('[data-confirm]') : null;
        if (!trigger) {
            return;
        }

        event.preventDefault();
        openFromDataset(trigger);
    });
});
