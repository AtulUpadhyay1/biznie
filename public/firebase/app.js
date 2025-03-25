import { initializeApp } from "https://www.gstatic.com/firebasejs/10.0.0/firebase-app.js";
import { getMessaging, getToken, onMessage } from "https://www.gstatic.com/firebasejs/10.0.0/firebase-messaging.js";

const firebaseConfig = {
    apiKey: "AIzaSyBQJIT7KZ2E__RbcDJc6tap8yjOL5Skxsc",
    authDomain: "biznie-b2952.firebaseapp.com",
    projectId: "biznie-b2952",
    storageBucket: "biznie-b2952.firebasestorage.com",
    messagingSenderId: "1087293995265",
    appId: "1:1087293995265:web:3be0345ef32a62dd098300",
    measurementId: "G-5L32SNW05P"
};

const app = initializeApp(firebaseConfig);
const messaging = getMessaging(app);

navigator.serviceWorker.register('/firebase/firebase-messaging-sw.js')
    .then((registration) => {
        console.log("Service Worker Registered:", registration);
        requestPermission();
    })
    .catch((err) => console.error("Service Worker registration failed:", err));

function requestPermission() {
    Notification.requestPermission()
        .then((permission) => {
            if (permission === 'granted') {
                console.log('Notification permission granted.');

                getToken(messaging, { vapidKey: "BDkqBR7Q7wFeviO0T2tFkp6Y0J1QP1zVjnfZSy2Rtg2mUVwS0TLegmCWiaSMTX6XFBkSZPeAVTAFQaXc9tnQfWk" })
                    .then((currentToken) => {
                        if (currentToken) {
                            console.log("FCM Token:", currentToken);
                            sendTokenToServer(currentToken);
                        } else {
                            console.warn("No FCM token available. User may have denied permissions.");
                        }
                    })
                    .catch((err) => console.error("Error retrieving token:", err));
            } else {
                console.warn('User denied notification permissions.');
            }
        })
        .catch((err) => console.error("Error requesting notification permission:", err));
}

function sendTokenToServer(token) {
    console.log('Sending token to server:', token);

    fetch('/admin/save-fcm-token', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ fcm_token: token })
    })
    .then(response => response.json())
    .then(data => console.log('Token stored:', data))
    .catch(error => console.error('Error storing token:', error));
}

// ✅ Listen for Foreground Notifications
onMessage(messaging, (payload) => {
    console.log("Foreground message received:", payload);

    // ✅ Show Notification
    new Notification(payload.notification.title, {
        body: payload.notification.body,
        icon: '/admin_css/assets/images/favicon.png'
    });
});
