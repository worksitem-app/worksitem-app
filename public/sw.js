/*
 * Worksitem servis çalışanı.
 * Kural: sayfalar ve fiyatlar HİÇBİR ZAMAN önbellekten gösterilmez (her zaman ağdan, güncel).
 * Önbellek yalnız simge/stil/betik/yazı tipi içindir; ağ yoksa /cevrimdisi sayfası gösterilir.
 */
const VERSION = 'ws-v66-2';
const STATIC = VERSION + '-static';
const PRECACHE = ['/cevrimdisi', '/icons/icon-192.png', '/icons/icon-512.png', '/site.webmanifest'];

self.addEventListener('install', (e) => {
    e.waitUntil(caches.open(STATIC).then((c) => c.addAll(PRECACHE)).then(() => self.skipWaiting()));
});

self.addEventListener('activate', (e) => {
    e.waitUntil(caches.keys()
        .then((keys) => Promise.all(keys.filter((k) => !k.startsWith(VERSION)).map((k) => caches.delete(k))))
        .then(() => self.clients.claim()));
});

const isStatic = (url) => url.origin === self.location.origin
    ? /^\/(css|js|icons|images|img|fonts|build)\//.test(url.pathname) || /\.(css|js|png|jpg|jpeg|webp|svg|woff2?)$/.test(url.pathname)
    : /^(fonts\.googleapis\.com|fonts\.gstatic\.com)$/.test(url.hostname);

self.addEventListener('fetch', (e) => {
    const req = e.request;
    if (req.method !== 'GET') return;
    const url = new URL(req.url);
    if (url.origin === self.location.origin && /^\/(admin|livewire|odeme|up)(\/|$)/.test(url.pathname)) return;

    if (req.mode === 'navigate') {
        // Sayfa: her zaman ağdan; ağ yoksa çevrimdışı sayfası
        e.respondWith(fetch(req).catch(() => caches.match('/cevrimdisi')));
        return;
    }
    if (isStatic(url)) {
        // Statik dosya: önbellekten hızlı ver, arkada tazele
        e.respondWith(caches.open(STATIC).then((c) => c.match(req).then((hit) => {
            const net = fetch(req).then((res) => { if (res && (res.ok || res.type === 'opaque')) c.put(req, res.clone()); return res; }).catch(() => hit);
            return hit || net;
        })));
    }
});

// Firma paneli bildirimi: içerik sunucudan çekilir (bildirim içeriksiz gelir)
self.addEventListener('push', (e) => {
    e.waitUntil((async () => {
        let msg = { title: 'Worksitem', body: 'Firma panelinizde yeni bir gelişme var.', url: '/firma-paneli' };
        try {
            const sub = await self.registration.pushManager.getSubscription();
            if (sub) {
                const buf = await crypto.subtle.digest('SHA-256', new TextEncoder().encode(sub.endpoint));
                const hex = Array.from(new Uint8Array(buf)).map((b) => b.toString(16).padStart(2, '0')).join('');
                const r = await fetch('/firma-paneli/bildirim?e=' + hex, { credentials: 'same-origin', cache: 'no-store' });
                if (r.ok) msg = Object.assign(msg, await r.json());
            }
        } catch (err) { /* varsayılan mesaj gösterilir */ }
        await self.registration.showNotification(msg.title, {
            body: msg.body, icon: '/icons/icon-192.png', badge: '/icons/icon-192.png',
            data: { url: msg.url }, tag: 'ws-panel', renotify: true, lang: 'tr',
        });
    })());
});

self.addEventListener('notificationclick', (e) => {
    e.notification.close();
    const target = new URL((e.notification.data && e.notification.data.url) || '/firma-paneli', self.location.origin);
    if (target.origin !== self.location.origin) return;
    e.waitUntil(self.clients.matchAll({ type: 'window', includeUncontrolled: true }).then((list) => {
        for (const c of list) { if (c.url.startsWith(self.location.origin) && 'focus' in c) { c.navigate(target.href); return c.focus(); } }
        return self.clients.openWindow(target.href);
    }));
});
