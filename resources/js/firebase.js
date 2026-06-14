importScripts('https://www.gstatic.com/firebasejs/9.0.0/firebase-app-compat.js');
importScripts('https://www.gstatic.com/firebasejs/9.0.0/firebase-messaging-compat.js');

firebase.initializeApp({
    apiKey: "AIzaSyCFnob2jZ-o_JSpMlVB1i7qY4TiE7c66Mg",
    authDomain: "notification-push-a9015.firebaseapp.com",
    projectId: "notification-push-a9015",
    storageBucket: "notification-push-a9015.firebasestorage.app",
    messagingSenderId: "835479016714",
    appId: "1:835479016714:web:1437ed5f83df7f109116f3",
    measurementId: "G-FZB0C79WTE"
});

const messaging = firebase.messaging();

messaging.onBackgroundMessage((payload) => {
    console.log('Received background message ', payload);
    
    const notificationTitle = payload.notification.title;
    const notificationOptions = {
        body: payload.notification.body,
        icon: '/firebase-logo.png'
    };

    self.registration.showNotification(notificationTitle, notificationOptions);
});