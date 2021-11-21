importScripts('https://www.gstatic.com/firebasejs/8.2.9/firebase-app.js');
importScripts('https://www.gstatic.com/firebasejs/8.2.9/firebase-messaging.js');

firebase.initializeApp({
  apiKey: "AIzaSyC50URuPt2twB2PPnVL9EnNfmTutnfzRz8",
  authDomain: "beaming-inn-300511.firebaseapp.com",
  projectId: "beaming-inn-300511",
  storageBucket: "beaming-inn-300511.appspot.com",
  messagingSenderId: "1026419611530",
  appId: "1:1026419611530:web:0d434f6cc08fae077f9a73",
  measurementId: "G-4S57JLB9YP"
});

const messaging = firebase.messaging();

messaging.setBackgroundMessageHandler(function(payload) {
  console.log('[firebase-messaging-sw.js] Received background message ', payload);
  const notificationTitle = 'Background Message Title';
  const notificationOptions = {
    body: 'Background Message body.',
    icon: 'https://images.theconversation.com/files/93616/original/image-20150902-6700-t2axrz.jpg' //your logo here
  };

  return self.registration.showNotification(notificationTitle,
      notificationOptions);
});