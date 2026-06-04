importScripts('https://www.gstatic.com/firebasejs/10.7.0/firebase-app-compat.js');
importScripts('https://www.gstatic.com/firebasejs/10.7.0/firebase-messaging-compat.js');

firebase.initializeApp({
    apiKey: "AIzaSyC-ZgcYemM5Mcej8wFFJRZg4-UlhgE0zB8",
    authDomain: "real-estate-service-732b2.firebaseapp.com",
    projectId: "real-estate-service-732b2",
    storageBucket: "real-estate-service-732b2.firebasestorage.app",
    messagingSenderId: "368237445538",
    appId: "1:368237445538:web:ba29a50a3850a875ccb2fb"
});

const messaging = firebase.messaging();

messaging.onBackgroundMessage(payload => {
    const notification = payload.notification || {};
    const data = payload.data || {};

    self.registration.showNotification(
        notification.title || data.title || 'Notification',
        {
            body: notification.body || data.body || data.message || '',
            icon: notification.icon || data.icon || '/logo.png',
            data
        }
    );
});

self.addEventListener('notificationclick', event => {
    event.notification.close();

    event.waitUntil(
        clients.matchAll({
            type: 'window',
            includeUncontrolled: true
        }).then(clientList => {
            for (const client of clientList) {
                if ('focus' in client) {
                    return client.focus();
                }
            }

            if (clients.openWindow) {
                return clients.openWindow('/admin/dashboard');
            }
        })
    );
});
