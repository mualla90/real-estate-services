import './bootstrap';
import 'bootstrap/dist/css/bootstrap.min.css';
import * as coreui from '@coreui/coreui';

window.coreui = coreui;
document.documentElement.classList.add('js-enabled');

const numberFormatter = new Intl.NumberFormat(document.documentElement.lang || 'en');

function setupLiveDashboardStats() {
    const statSourceNode = document.querySelector('[data-dashboard-stats-url]');
    if (!statSourceNode) {
        return;
    }

    const url = statSourceNode.getAttribute('data-dashboard-stats-url');
    const interval = Number(statSourceNode.getAttribute('data-dashboard-stats-interval') || 30000);

    if (!url) {
        return;
    }

    const applyStats = (stats) => {
        Object.entries(stats || {}).forEach(([key, value]) => {
            const nodes = document.querySelectorAll(`[data-live-stat="${key}"]`);
            const numericValue = Number(value || 0);

            nodes.forEach((node) => {
                node.textContent = numberFormatter.format(numericValue);

                if (node.classList.contains('badge')) {
                    node.classList.toggle('d-none', numericValue <= 0);
                }
            });
        });
    };

    const refresh = async () => {
        try {
            const response = await window.axios.get(url, {
                headers: {
                    Accept: 'application/json',
                },
            });

            applyStats(response?.data?.stats || {});
        } catch (error) {
            // Keep dashboard usable even if polling fails temporarily.
            console.error('Live dashboard stats refresh failed', error);
        }
    };

    refresh();
    window.setInterval(refresh, Math.max(interval, 10000));
}

function setupAdminToasts() {
    const container = document.getElementById('adminToastContainer');
    if (!container) {
        return;
    }

    const showToast = (message, variant = 'success') => {
        if (!message) {
            return;
        }

        const toast = document.createElement('div');
        toast.className = `admin-toast admin-toast-${variant}`;
        toast.textContent = message;
        container.appendChild(toast);

        window.requestAnimationFrame(() => {
            toast.classList.add('is-visible');
        });

        window.setTimeout(() => {
            toast.classList.remove('is-visible');
            window.setTimeout(() => toast.remove(), 200);
        }, 4200);
    };

    const flashAlerts = document.querySelectorAll('.flash-stack .alert');
    flashAlerts.forEach((alert) => {
        const message = alert.textContent?.trim() || '';
        const variant = alert.classList.contains('alert-danger') ? 'danger' : 'success';
        showToast(message, variant);
    });
}

function setupAdminConfirmModal() {
    const bodyDataset = document.body?.dataset || {};
    const backdrop = document.getElementById('adminConfirmBackdrop');
    const messageNode = document.getElementById('adminConfirmMessage');
    const titleNode = document.getElementById('adminConfirmTitle');
    const cancelButton = document.getElementById('adminConfirmCancel');
    const approveButton = document.getElementById('adminConfirmApprove');

    if (!backdrop || !messageNode || !cancelButton || !approveButton || !titleNode) {
        return;
    }

    titleNode.textContent = bodyDataset.uiConfirmTitle || titleNode.textContent;
    cancelButton.textContent = bodyDataset.uiCancel || cancelButton.textContent;
    approveButton.textContent = bodyDataset.uiConfirmApprove || approveButton.textContent;

    let activeAction = null;

    const closeModal = () => {
        backdrop.hidden = true;
        activeAction = null;
    };

    const openModal = (message, onApprove) => {
        messageNode.textContent = message;
        activeAction = onApprove;
        backdrop.hidden = false;
        approveButton.focus();
    };

    cancelButton.addEventListener('click', closeModal);

    approveButton.addEventListener('click', () => {
        if (typeof activeAction === 'function') {
            activeAction();
        }
        closeModal();
    });

    backdrop.addEventListener('click', (event) => {
        if (event.target === backdrop) {
            closeModal();
        }
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && !backdrop.hidden) {
            closeModal();
        }
    });

    document.addEventListener('submit', (event) => {
        const form = event.target;
        if (!(form instanceof HTMLFormElement)) {
            return;
        }

        if (form.dataset.confirmed === '1') {
            return;
        }

        const submitter = event.submitter instanceof HTMLElement ? event.submitter : null;
        const message = submitter?.getAttribute('data-confirm') || form.getAttribute('data-confirm');

        if (!message) {
            return;
        }

        event.preventDefault();
        openModal(message, () => {
            form.dataset.confirmed = '1';
            form.requestSubmit(submitter || undefined);
        });
    });
}

document.addEventListener('DOMContentLoaded', () => {
    setupLiveDashboardStats();
    setupAdminToasts();
    setupAdminConfirmModal();
});
