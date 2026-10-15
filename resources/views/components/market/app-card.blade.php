{{--
    Firma paneli: uygulamayı ana ekrana ekle + yeni iş bildirimlerini aç.
    Android/Chrome: kurulum düğmesi · iPhone: Paylaş › Ana Ekrana Ekle talimatı (iOS'ta bildirim yalnız ana ekrandan açılınca çalışır).
--}}
@php
    $vapid = \App\Services\Push\WebPush::enabled() ? \App\Services\Push\WebPush::publicKey() : null;
    $play = config('worksitem_app.twa.play_url');
@endphp
<div class="mk-card ws-app" id="wsApp" data-vapid="{{ $vapid }}" data-sub="{{ route('app.push.subscribe') }}" data-unsub="{{ route('app.push.unsubscribe') }}">
    <div style="display:flex;gap:14px;align-items:flex-start">
        <img src="/icons/icon-192.png" alt="" width="48" height="48" style="border-radius:12px;flex:none">
        <div style="flex:1;min-width:0">
            <h2 style="margin:0 0 4px;font-size:18px">Yeni işleri kaçırmayın</h2>
            <p style="margin:0 0 12px;color:var(--muted);font-size:14px;line-height:1.55">Worksitem'i telefonunuza uygulama olarak ekleyin; bölgenize yeni iş geldiğinde ya da müşteri teklifinizi kabul ettiğinde anında bildirim alın. SMS ile haber vermeye devam ediyoruz.</p>
            <div style="display:flex;gap:8px;flex-wrap:wrap;align-items:center">
                <button type="button" class="fm-btn" data-act="install" hidden>Uygulamayı yükle</button>
                @if ($play)<a class="fm-btn o" href="{{ $play }}" target="_blank" rel="noopener">Google Play'den indir</a>@endif
                @if ($vapid)<button type="button" class="fm-btn o" data-act="push" hidden>Bildirimleri aç</button>@endif
                <span data-msg role="status" style="font-size:13.5px;font-weight:600"></span>
            </div>
            <p data-ios hidden style="margin:12px 0 0;font-size:13.5px;line-height:1.55;background:var(--y-50);border-radius:12px;padding:10px 12px">
                <b>iPhone'da:</b> Safari'de alttaki <b>Paylaş</b> simgesine, ardından <b>Ana Ekrana Ekle</b>'ye dokunun. Bildirimler için Worksitem'i ana ekrandaki simgeden açıp "Bildirimleri aç"a basın.
            </p>
        </div>
    </div>
</div>
<script>
(function () {
    var box = document.getElementById('wsApp'); if (!box) return;
    var msg = box.querySelector('[data-msg]'), inst = box.querySelector('[data-act=install]'), push = box.querySelector('[data-act=push]');
    var standalone = matchMedia('(display-mode: standalone)').matches || navigator.standalone === true;
    var ios = /iphone|ipad|ipod/i.test(navigator.userAgent) || (navigator.platform === 'MacIntel' && navigator.maxTouchPoints > 1);
    var csrf = (document.querySelector('meta[name=csrf-token]') || {}).content;
    var say = function (t) { msg.textContent = t; };
    var deferred = null;
    addEventListener('beforeinstallprompt', function (e) { e.preventDefault(); deferred = e; inst.hidden = false; });
    inst.addEventListener('click', function () {
        if (!deferred) return; deferred.prompt();
        deferred.userChoice.then(function (c) { if (c.outcome === 'accepted') { inst.hidden = true; say('Uygulama yüklendi.'); } deferred = null; });
    });
    if (ios && !standalone) box.querySelector('[data-ios]').hidden = false;
    if (standalone) say('Uygulama olarak açık.');
    var key = box.dataset.vapid;
    if (!push || !key || !('serviceWorker' in navigator) || !('PushManager' in window) || !('Notification' in window)) return;
    var b64 = function (s) { s = (s + '===='.slice((s.length % 4) || 4)).replace(/-/g, '+').replace(/_/g, '/'); var r = atob(s), a = new Uint8Array(r.length); for (var i = 0; i < r.length; i++) a[i] = r.charCodeAt(i); return a; };
    var post = function (url, body) { return fetch(url, { method: 'POST', credentials: 'same-origin', headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf }, body: JSON.stringify(body) }).then(function (r) { return r.json(); }); };
    var on = false;
    var render = function () { push.hidden = false; push.textContent = on ? 'Bildirimleri kapat' : 'Bildirimleri aç'; };
    navigator.serviceWorker.ready.then(function (reg) {
        return reg.pushManager.getSubscription().then(function (s) { on = !!s && Notification.permission === 'granted'; render(); if (on) post(box.dataset.sub, s.toJSON()); });
    });
    push.addEventListener('click', function () {
        push.disabled = true;
        navigator.serviceWorker.ready.then(function (reg) {
            if (on) return reg.pushManager.getSubscription().then(function (s) { if (!s) return {}; return post(box.dataset.unsub, { endpoint: s.endpoint }).then(function () { return s.unsubscribe(); }); }).then(function () { on = false; say('Bu cihazda bildirimler kapatıldı.'); });
            return Notification.requestPermission().then(function (p) {
                if (p !== 'granted') { say('Bildirim izni verilmedi. Tarayıcı ayarlarından izin verebilirsiniz.'); return; }
                return reg.pushManager.subscribe({ userVisibleOnly: true, applicationServerKey: b64(key) }).then(function (s) { return post(box.dataset.sub, s.toJSON()); }).then(function (r) { on = !!r.ok; say(r.message || ''); });
            });
        }).catch(function () { say(ios && !standalone ? 'iPhone\'da önce ana ekrana ekleyin.' : 'Bildirim açılamadı; lütfen tekrar deneyin.'); })
          .then(function () { push.disabled = false; render(); });
    });
})();
</script>
