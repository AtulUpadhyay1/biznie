import { initializeApp } from "https://www.gstatic.com/firebasejs/10.0.0/firebase-app.js";
import { getMessaging, getToken, onMessage } from "https://www.gstatic.com/firebasejs/10.0.0/firebase-messaging.js";

const firebaseConfig = {
    apiKey: "AIzaSyBnA3kOxvpc6j1lbZkfkWTxbSAuAZEptZ4",
    authDomain: "biznie-60aff.firebaseapp.com",
    projectId: "biznie-60aff",
    storageBucket: "biznie-60aff.firebasestorage.app",
    messagingSenderId: "469414290605",
    appId: "1:469414290605:web:d68133bdc2ae70ed949224",
    measurementId: "G-XRHEZLNH7J"
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

                getToken(messaging, { vapidKey: "BAvpVJZ7hBbLg4Xuq08uu5NiFVWoM_JNj06OzrUWY78ujLGNkIYF2ekyVnudm-BZY5sS9AiX6xOqS4qGREM7QL8" })
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
