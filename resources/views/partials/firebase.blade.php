<script type="module">
    import {
        initializeApp
    } from "https://www.gstatic.com/firebasejs/10.7.0/firebase-app.js";
    import {
        getMessaging,
        getToken,
        onMessage
    } from "https://www.gstatic.com/firebasejs/10.7.0/firebase-messaging.js";

    const firebaseConfig = {
        apiKey: "AIzaSyC-ZgcYemM5Mcej8wFFJRZg4-UlhgE0zB8",
        authDomain: "real-estate-service-732b2.firebaseapp.com",
        projectId: "real-estate-service-732b2",
        storageBucket: "real-estate-service-732b2.firebasestorage.app",
        messagingSenderId: "368237445538",
        appId: "1:368237445538:web:ba29a50a3850a875ccb2fb"
    };

    const app = initializeApp(firebaseConfig);
    const messaging = getMessaging(app);

    const notificationTitle = payload => payload.notification?.title || payload.data?.title || document.title;
    const notificationOptions = payload => ({
        body: payload.notification?.body || payload.data?.body || payload.data?.message || '',
        icon: payload.notification?.icon || payload.data?.icon || '/logo.png',
        data: payload.data || {},
    });

    navigator.serviceWorker.register('/firebase-messaging-sw.js')
        .then(async registration => {
            const permission = await Notification.requestPermission();
            if (permission !== 'granted') {
                console.log('Permission not granted for notifications');
                return;
            }

            const token = await getToken(messaging, {
                vapidKey: "BEBlUzakGwZxXIIWku8FKWGuA2GDCR-WdEzOb_U7ZEJfGSngCs58Jnww7Gu1C9OSKEGwhZYEOXHxh5lRve9CkbQ",
                serviceWorkerRegistration: registration,
            });

            if (! token) {
                console.log('No FCM token returned');
                return;
            }

            await fetch('/fcm/register-token', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    token
                })
            });

            console.log('FCM token registered');
        })
        .catch(err => console.error('Firebase messaging setup failed', err));

    onMessage(messaging, payload => {
        console.log('FCM foreground message:', payload);

        if (Notification.permission === 'granted') {
            navigator.serviceWorker.ready.then(registration => {
                registration.showNotification(notificationTitle(payload), notificationOptions(payload));
            });
        }

        window.dispatchEvent(new CustomEvent('fcm-message', {
            detail: payload
        }));
    });
</script>
