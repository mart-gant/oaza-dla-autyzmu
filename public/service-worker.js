const STATIC_CACHE = 'oaza-static-v2';
const DYNAMIC_CACHE = 'oaza-dynamic-v2';

// Assets to cache immediately
const STATIC_ASSETS = [
  '/',
  '/build/assets/app.css',
  '/build/assets/app.js',
  '/manifest.json',
  '/offline.html'
];

const AUTH_PATH_PREFIXES = [
  '/login',
  '/register',
  '/logout',
  '/forgot-password',
  '/reset-password',
  '/two-factor',
  '/email/verify',
  '/sanctum/csrf-cookie'
];

function isAuthPath(pathname) {
  return AUTH_PATH_PREFIXES.some((prefix) => pathname.startsWith(prefix));
}

function isHtmlRequest(request) {
  return request.mode === 'navigate' || (request.headers.get('accept') || '').includes('text/html');
}

function isStaticAsset(pathname) {
  return (
    pathname.startsWith('/build/') ||
    pathname.startsWith('/images/') ||
    pathname === '/manifest.json' ||
    pathname === '/favicon.ico' ||
    /\.(?:css|js|png|jpg|jpeg|svg|webp|gif|ico|woff2?)$/i.test(pathname)
  );
}

// Install event - cache static assets
self.addEventListener('install', (event) => {
  event.waitUntil(
    caches
      .open(STATIC_CACHE)
      .then((cache) => cache.addAll(STATIC_ASSETS.map((url) => new Request(url, { cache: 'reload' }))))
      .catch(() => undefined)
  );

  self.skipWaiting();
});

// Activate event - clean up old caches
self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys().then((cacheNames) =>
      Promise.all(
        cacheNames
          .filter((cacheName) => cacheName.startsWith('oaza-') && cacheName !== STATIC_CACHE && cacheName !== DYNAMIC_CACHE)
          .map((cacheName) => caches.delete(cacheName))
      )
    )
  );

  self.clients.claim();
});

self.addEventListener('fetch', (event) => {
  const { request } = event;
  const url = new URL(request.url);

  if (url.origin !== location.origin || request.method !== 'GET') {
    return;
  }

  // Auth and HTML pages must be network-first and never cached to avoid stale CSRF/session tokens (419 errors).
  if (isAuthPath(url.pathname) || isHtmlRequest(request)) {
    event.respondWith(
      fetch(request).catch(() => {
        if (isHtmlRequest(request)) {
          return caches.match('/offline.html');
        }
        return new Response(null, { status: 503, statusText: 'Offline' });
      })
    );
    return;
  }

  // API calls: network-first with fallback to cache
  if (url.pathname.startsWith('/api/')) {
    event.respondWith(
      fetch(request)
        .then((response) => {
          if (response.status === 200) {
            const responseClone = response.clone();
            caches.open(DYNAMIC_CACHE).then((cache) => cache.put(request, responseClone));
          }
          return response;
        })
        .catch(() => caches.match(request))
    );
    return;
  }

  // Static assets: cache-first
  if (isStaticAsset(url.pathname)) {
    event.respondWith(
      caches.match(request).then((cachedResponse) => {
        if (cachedResponse) {
          return cachedResponse;
        }

        return fetch(request).then((response) => {
          if (response.status === 200) {
            const responseClone = response.clone();
            caches.open(DYNAMIC_CACHE).then((cache) => cache.put(request, responseClone));
          }
          return response;
        });
      })
    );
  }
});

self.addEventListener('sync', (event) => {
  if (event.tag === 'sync-posts') {
    event.waitUntil(syncPosts());
  }
});

async function syncPosts() {
  return Promise.resolve();
}

self.addEventListener('push', (event) => {
  const data = event.data ? event.data.json() : {};
  const title = data.title || 'Oaza dla Autyzmu';
  const options = {
    body: data.body || 'Nowa wiadomość',
    icon: '/images/icons/icon-192x192.png',
    badge: '/images/icons/icon-72x72.png',
    vibrate: [200, 100, 200],
    data: {
      url: data.url || '/'
    },
    actions: [
      {
        action: 'open',
        title: 'Otwórz'
      },
      {
        action: 'close',
        title: 'Zamknij'
      }
    ]
  };

  event.waitUntil(self.registration.showNotification(title, options));
});

self.addEventListener('notificationclick', (event) => {
  event.notification.close();

  if (event.action === 'open') {
    const urlToOpen = event.notification.data.url;
    event.waitUntil(clients.openWindow(urlToOpen));
  }
});
