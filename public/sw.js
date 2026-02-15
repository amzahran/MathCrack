// Service Worker لدعم الإشعارات والعمل في الخلفية
const CACHE_NAME = 'tickets-notifications-cache-v1';
const urlsToCache = [
  '/assets/themes/default/sounds/notification.mp3'
];

// تثبيت Service Worker وتخزين الملفات المطلوبة
self.addEventListener('install', event => {
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then(cache => {
        console.log('تم فتح ذاكرة التخزين المؤقت');
        return cache.addAll(urlsToCache);
      })
  );
});

// الاستماع لطلبات جلب الموارد
self.addEventListener('fetch', event => {
  event.respondWith(
    caches.match(event.request)
      .then(response => {
        // استخدام النسخة المخزنة إن وجدت
        if (response) {
          return response;
        }
        return fetch(event.request);
      }
    )
  );
});

// الاستماع لرسائل من الصفحة الرئيسية
self.addEventListener('message', event => {
  if (event.data && event.data.type === 'NOTIFICATION') {
    console.log('استلام طلب إشعار من الصفحة', event.data);

    // إرسال إشعار
    self.registration.showNotification('تذكرة جديدة', {
      body: event.data.message || 'لديك رسالة جديدة في التذكرة',
      icon: '/assets/logo.png',
      badge: '/assets/logo.png',
      vibrate: [200, 100, 200],
      tag: 'ticket-notification',
      renotify: true,
      data: {
        url: event.data.url || '/dashboard/admins/tickets-show'
      }
    });
  }
});

// فتح الصفحة المناسبة عند النقر على الإشعار
self.addEventListener('notificationclick', event => {
  event.notification.close();

  // فتح URL محدد عند النقر على الإشعار
  if (event.notification.data && event.notification.data.url) {
    event.waitUntil(
      clients.matchAll({type: 'window'}).then(windowClients => {
        // التحقق من وجود نافذة مفتوحة بالفعل
        for (let client of windowClients) {
          if (client.url.indexOf(event.notification.data.url) >= 0 && 'focus' in client) {
            return client.focus();
          }
        }
        // فتح نافذة جديدة إذا لم تكن هناك نافذة مفتوحة
        if (clients.openWindow) {
          return clients.openWindow(event.notification.data.url);
        }
      })
    );
  }
});