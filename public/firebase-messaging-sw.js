// importScripts('https://www.gstatic.com/firebasejs/10.0.0/firebase-app.js');
// importScripts('https://www.gstatic.com/firebasejs/10.0.0/firebase-messaging.js');

importScripts("https://www.gstatic.com/firebasejs/9.6.10/firebase-app-compat.js");
importScripts("https://www.gstatic.com/firebasejs/9.6.10/firebase-messaging-compat.js");

firebase.initializeApp({
    apiKey: "AIzaSyBnA3kOxvpc6j1lbZkfkWTxbSAuAZEptZ4",
    authDomain: "biznie-60aff.firebaseapp.com",
    projectId: "biznie-60aff",
    storageBucket: "biznie-60aff.firebasestorage.app",
    messagingSenderId: "469414290605",
    appId: "1:469414290605:web:d68133bdc2ae70ed949224",
    measurementId: "G-XRHEZLNH7J"
});

const messaging = firebase.messaging();

messaging.onBackgroundMessage((payload) => {
    console.log('Received background message ', payload);
    self.registration.showNotification(payload.notification.title, {
        body: payload.notification.body,
        icon: '/admin_css/assets/images/favicon.png' // Change to your icon
    });
});
