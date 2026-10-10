const CACHE_NAME = 'syafa-pos-v2';
const BASE_PATH = self.location.pathname.replace(/\/sw\.js$/, '');
const getAssetUrl = (path) => `${BASE_PATH}${path.startsWith('/') ? '' : '/'}${path}`;

const RELATIVE_ASSETS = [
  'manifest.json',
  'offline.html',
  'favicon.png',
  'kasirku.png',
  'icons/icon-72.png',
  'icons/icon-96.png',
  'icons/icon-128.png',
  'icons/icon-144.png',
  'icons/icon-152.png',
  'icons/icon-192.png',
  'icons/icon-384.png',
  'icons/icon-512.png',
  'icons/icon-maskable-512.png',
  'icons/apple-touch-icon.png'
];

const STATIC_ASSETS = RELATIVE_ASSETS.map(getAssetUrl);

// Install Event
self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME).then((cache) => {
      return cache.addAll(STATIC_ASSETS);
    })
  );
  self.skipWaiting();
});

// Activate Event
self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys().then((cacheNames) => {
      return Promise.all(
        cacheNames
          .filter((name) => name !== CACHE_NAME)
          .map((name) => caches.delete(name))
      );
    })
  );
  self.clients.claim();
});

// Fetch Event
self.addEventListener('fetch', (event) => {
  const url = new URL(event.request.url);

  // Skip non-GET requests or Livewire/API POST updates
  if (event.request.method !== 'GET') {
    return;
  }

  // Handle static assets (cache first)
  if (url.pathname.includes('/icons/') || url.pathname.endsWith('.png') || url.pathname.endsWith('.ico')) {
    event.respondWith(
      caches.match(event.request).then((cachedResponse) => {
        return cachedResponse || fetch(event.request).then((networkResponse) => {
          if (networkResponse && networkResponse.status === 200) {
            const responseClone = networkResponse.clone();
            caches.open(CACHE_NAME).then((cache) => cache.put(event.request, responseClone));
          }
          return networkResponse;
        });
      })
    );
    return;
  }

  // Handle HTML navigation and dynamic pages (network first)
  event.respondWith(
    fetch(event.request).catch(() => {
      return caches.match(event.request).then((res) => {
        if (res) return res;
        if (event.request.mode === 'navigate') {
          return caches.match(getAssetUrl('offline.html'));
        }
      });
    })
  );
});
