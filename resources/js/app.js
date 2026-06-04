import './bootstrap';
import 'bootstrap/dist/css/bootstrap.min.css';
import * as bootstrap from 'bootstrap';
import * as coreui from '@coreui/coreui';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.bootstrap = bootstrap;
window.coreui = coreui;
window.Pusher = Pusher;
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

    window.showAdminToast = (message, variant = 'success') => {
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
        window.showAdminToast(message, variant);
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

function setupAdminFormLoading() {
    document.addEventListener('submit', (event) => {
        const form = event.target;
        if (!(form instanceof HTMLFormElement) || form.dataset.noLoading === '1') {
            return;
        }

        window.setTimeout(() => {
            if (event.defaultPrevented) {
                return;
            }

            const submitter = event.submitter instanceof HTMLButtonElement ? event.submitter : form.querySelector('[type="submit"]');
            if (!submitter || form.dataset.loading === '1') {
                return;
            }

            form.dataset.loading = '1';
            submitter.dataset.originalText = submitter.innerHTML;
            submitter.disabled = true;
            submitter.innerHTML = '<span class="spinner-border spinner-border-sm me-1" aria-hidden="true"></span><span>Working</span>';
        }, 0);
    });
}

function setupAdminRealtimeNotifications() {
    const body = document.body;
    const adminId = body?.dataset.adminId;
    const authEndpoint = body?.dataset.adminRealtimeAuthEndpoint;
    const pusherKey = body?.dataset.pusherKey;

    if (!adminId || !authEndpoint || !pusherKey) {
        return;
    }

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    const echo = new Echo({
        broadcaster: 'pusher',
        key: pusherKey,
        cluster: body.dataset.pusherCluster || 'mt1',
        forceTLS: (body.dataset.pusherScheme || 'https') === 'https',
        encrypted: true,
        authEndpoint,
        auth: {
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                Accept: 'application/json',
            },
        },
    });

    const incrementNotificationBadges = () => {
        document.querySelectorAll('[data-live-stat="unread_notifications"]').forEach((badge) => {
            const currentValue = Number((badge.textContent || '0').replace(/,/g, '')) || 0;
            badge.textContent = numberFormatter.format(currentValue + 1);
            badge.classList.remove('d-none');
        });
    };

    const prependNotificationPreview = (notification) => {
        const list = document.querySelector('.admin-notification-dropdown-list');
        if (!list) {
            return;
        }

        list.querySelector('[data-admin-notification-empty]')?.remove();

        const link = document.createElement('a');
        link.href = notification.url || '/admin/notifications';
        link.className = 'admin-notification-preview is-unread';

        const dot = document.createElement('span');
        dot.className = 'admin-notification-dot';
        dot.setAttribute('aria-hidden', 'true');

        const bodyNode = document.createElement('span');
        bodyNode.className = 'admin-notification-preview-body';

        const title = document.createElement('span');
        title.className = 'admin-notification-preview-title';
        title.textContent = notification.title || 'Notification';

        const message = document.createElement('span');
        message.className = 'admin-notification-preview-text';
        message.textContent = notification.message || '';

        const time = document.createElement('span');
        time.className = 'admin-notification-preview-time';
        time.textContent = notification.created_at || 'Just now';

        bodyNode.append(title, message, time);
        link.append(dot, bodyNode);
        list.prepend(link);

        Array.from(list.querySelectorAll('.admin-notification-preview'))
            .slice(5)
            .forEach((node) => node.remove());
    };

    echo.private(`admin.${adminId}`)
        .listen('.notification.created', (notification) => {
            incrementNotificationBadges();
            prependNotificationPreview(notification);
            window.showAdminToast?.(notification.title || 'New notification', 'success');
        });
}

function setupChatDemo() {
    const root = document.querySelector('[data-chat-demo]');
    if (!root) {
        return;
    }

    const conversationId = root.dataset.conversationId;
    const messagesNode = root.querySelector('[data-chat-messages]');
    const sendForms = root.querySelectorAll('[data-chat-send-form]');
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    const currentBusinessAccountId = messagesNode?.dataset.currentBusinessAccountId || '';

    const seenMessageIds = new Set(
        Array.from(messagesNode?.querySelectorAll('[data-message-id]') || [])
            .map((node) => String(node.getAttribute('data-message-id')))
            .filter(Boolean)
    );

    const scrollToBottom = () => {
        if (!messagesNode) {
            return;
        }

        messagesNode.scrollTop = messagesNode.scrollHeight;
    };

    const appendMessage = (message) => {
        if (!messagesNode || !message?.id || seenMessageIds.has(String(message.id))) {
            return;
        }

        seenMessageIds.add(String(message.id));

        const wrapper = document.createElement('div');
        wrapper.className = `chat-demo-message ${String(message.sender_business_account_id) === String(currentBusinessAccountId) ? 'is-outgoing' : 'is-incoming'}`;
        wrapper.dataset.messageId = message.id;
        wrapper.dataset.senderBusinessAccountId = message.sender_business_account_id || '';

        const bubble = document.createElement('div');
        bubble.className = 'chat-demo-bubble';

        const meta = document.createElement('div');
        meta.className = 'chat-demo-meta';
        meta.textContent = `${message.sender_name || 'Business account'} #${message.sender_business_account_id || ''}`;

        const body = document.createElement('div');
        body.className = 'chat-demo-text';
        body.textContent = message.body || '';

        const time = document.createElement('div');
        time.className = 'chat-demo-time';
        time.textContent = message.created_at || '';

        bubble.append(meta, body, time);
        wrapper.appendChild(bubble);
        messagesNode.appendChild(wrapper);
        scrollToBottom();
    };

    const echo = new Echo({
        broadcaster: 'pusher',
        key: root.dataset.pusherKey,
        cluster: root.dataset.pusherCluster || 'mt1',
        forceTLS: (root.dataset.pusherScheme || 'https') === 'https',
        encrypted: true,
        authEndpoint: root.dataset.authEndpoint,
        auth: {
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                Accept: 'application/json',
            },
        },
    });

    if (conversationId && messagesNode) {
        echo.private(`conversation.${conversationId}`)
            .listen('.message.sent', (message) => {
                appendMessage(message);
            });
    }

    const incrementBadge = (selector) => {
        const badge = document.querySelector(selector);
        if (!badge) {
            return;
        }

        const nextValue = Number(badge.textContent || 0) + 1;
        badge.textContent = numberFormatter.format(nextValue);
        badge.classList.remove('d-none');
    };

    const inboxBusinessAccountIds = (root.dataset.inboxBusinessAccountIds || '')
        .split(',')
        .map((id) => id.trim())
        .filter(Boolean);

    inboxBusinessAccountIds.forEach((businessAccountId) => {
        echo.private(`business-account.${businessAccountId}`)
            .listen('.message.sent', (message) => {
                incrementBadge(`[data-business-account-inbox-count="${businessAccountId}"]`);

                if (String(message.conversation_id) !== String(conversationId)) {
                    incrementBadge(`[data-conversation-alert-count="${message.conversation_id}"]`);
                }
            });
    });

    sendForms.forEach((form) => {
        form.addEventListener('submit', async (event) => {
            event.preventDefault();

            const submitButton = form.querySelector('[type="submit"]');
            const formData = new FormData(form);
            const bodyNode = form.querySelector('[name="body"]');

            submitButton?.setAttribute('disabled', 'disabled');

            try {
                const response = await window.axios.post(root.dataset.sendEndpoint, {
                    sender_business_account_id: formData.get('sender_business_account_id'),
                    body: formData.get('body'),
                }, {
                    headers: {
                        Accept: 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                });

                appendMessage(response.data?.data);
                window.showAdminToast?.('Message sent', 'success');
                if (bodyNode) {
                    bodyNode.value = '';
                    bodyNode.focus();
                }
            } catch (error) {
                console.error('Chat demo message send failed', error);
                window.alert(error.response?.data?.message || 'Message could not be sent.');
            } finally {
                submitButton?.removeAttribute('disabled');
            }
        });
    });

    scrollToBottom();
}

document.addEventListener('DOMContentLoaded', () => {
    setupLiveDashboardStats();
    setupAdminToasts();
    setupAdminConfirmModal();
    setupAdminFormLoading();
    setupAdminRealtimeNotifications();
    setupChatDemo();
});
