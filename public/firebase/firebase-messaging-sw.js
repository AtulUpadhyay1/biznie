// importScripts('https://www.gstatic.com/firebasejs/10.0.0/firebase-app.js');
// importScripts('https://www.gstatic.com/firebasejs/10.0.0/firebase-messaging.js');

importScripts("https://www.gstatic.com/firebasejs/9.6.10/firebase-app-compat.js");
importScripts("https://www.gstatic.com/firebasejs/9.6.10/firebase-messaging-compat.js");

firebase.initializeApp({
    apiKey: "AIzaSyBQJIT7KZ2E__RbcDJc6tap8yjOL5Skxsc",
    authDomain: "biznie-b2952.firebaseapp.com",
    projectId: "biznie-b2952",
    storageBucket: "biznie-b2952.firebasestorage.app",
    messagingSenderId: "1087293995265",
    appId: "1:1087293995265:web:3be0345ef32a62dd098300",
    measurementId: "G-5L32SNW05P"
});

const messaging = firebase.messaging();

messaging.onBackgroundMessage((payload) => {
    console.log('Received background message ', payload);
    self.registration.showNotification(payload.notification.title, {
        body: payload.notification.body,
        icon: '/admin_css/assets/images/favicon.png' // Change to your icon
    });
});
