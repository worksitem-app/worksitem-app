{{--
    KVKK / Çerez Politikası uyumu.
    InjectGa4Tag middleware'i tarafından tüm public HTML yanıtlarına eklenir.

    - Analitik (GA4) ve pazarlama (Google Ads) etiketleri ONAY VERİLMEDEN YÜKLENMEZ.
    - "Kabul et" ve "Reddet" eşit ağırlıkta (KVKK Çerez Rehberi: reddetmek kabul etmek kadar kolay olmalı).
    - Sayfalar pazarlama/analitik kodu çalıştırmak isterse:
        (window.__wsConsentQueue = window.__wsConsentQueue || []).push(['marketing', function () { ... }]);
    - Çerez Politikası sayfasındaki "Çerez Ayarlarını Aç" butonu window.openCookieSettings() çağırır.
--}}
<script>
(function () {
    var NAME = 'worksitem_cookie_consent';
    var VERSION = '1';
    var GA_ID = @json($gaId);
    var MAX_AGE = 60 * 60 * 24 * 365; // 12 ay (Çerez Politikası ile aynı)

    function read() {
        var m = document.cookie.match(new RegExp('(?:^|; )' + NAME + '=([^;]*)'));
        if (!m) return null;
        var v = decodeURIComponent(m[1]).split('|');
        if (v[0] !== VERSION) return null; // politika değişince yeniden sor
        return { analytics: v[1] === '1', marketing: v[2] === '1' };
    }

    function write(c) {
        var secure = location.protocol === 'https:' ? '; Secure' : '';
        document.cookie = NAME + '=' + encodeURIComponent([VERSION, c.analytics ? 1 : 0, c.marketing ? 1 : 0].join('|'))
            + '; Max-Age=' + MAX_AGE + '; Path=/; SameSite=Lax' + secure;
    }

    var gtagLoaded = false;
    function ensureGtag(id) {
        window.dataLayer = window.dataLayer || [];
        window.gtag = window.gtag || function () { window.dataLayer.push(arguments); };
        if (!gtagLoaded) {
            gtagLoaded = true;
            window.gtag('js', new Date());
            var s = document.createElement('script');
            s.async = true;
            s.src = 'https://www.googletagmanager.com/gtag/js?id=' + encodeURIComponent(id);
            document.head.appendChild(s);
        }
    }

    var analyticsStarted = false;
    function apply(c) {
        if (c.analytics && GA_ID && !analyticsStarted) {
            analyticsStarted = true;
            ensureGtag(GA_ID);
            window.gtag('config', GA_ID, { anonymize_ip: true, send_page_view: true });
        }
        var q = window.__wsConsentQueue || [];
        var rest = [];
        for (var i = 0; i < q.length; i++) {
            var cat = q[i][0], fn = q[i][1];
            if (c[cat]) { try { fn(ensureGtag); } catch (e) {} } else { rest.push(q[i]); }
        }
        window.__wsConsentQueue = rest;
    }

    window.WorksitemConsent = { get: read, ensureGtag: ensureGtag };

    function banner() { return document.getElementById('ws-cookie-banner'); }

    function show(withSettings) {
        var b = banner();
        if (!b) return;
        var c = read() || { analytics: false, marketing: false };
        b.querySelector('#ws-ck-analytics').checked = c.analytics;
        b.querySelector('#ws-ck-marketing').checked = c.marketing;
        b.querySelector('.ws-ck-settings').hidden = !withSettings;
        b.querySelector('[data-ws-ck="save"]').hidden = !withSettings;
        b.querySelector('[data-ws-ck="settings"]').hidden = !!withSettings;
        b.hidden = false;
    }

    function decide(c) {
        write(c);
        banner().hidden = true;
        apply(c);
    }

    window.openCookieSettings = function () { show(true); };

    document.addEventListener('DOMContentLoaded', function () {
        var b = banner();
        if (!b) return;
        b.addEventListener('click', function (e) {
            var a = e.target.getAttribute && e.target.getAttribute('data-ws-ck');
            if (!a) return;
            if (a === 'accept') decide({ analytics: true, marketing: true });
            if (a === 'reject') decide({ analytics: false, marketing: false });
            if (a === 'settings') show(true);
            if (a === 'save') decide({
                analytics: b.querySelector('#ws-ck-analytics').checked,
                marketing: b.querySelector('#ws-ck-marketing').checked
            });
        });
        var c = read();
        if (c) { apply(c); } else { show(false); }
    });
})();
</script>
<style>
#ws-cookie-banner{position:fixed;left:16px;right:16px;bottom:16px;z-index:2147483000;max-width:560px;margin:0 auto;background:#111;color:#e4e4e7;border:1px solid rgba(255,184,0,.25);border-radius:14px;box-shadow:0 18px 48px rgba(0,0,0,.45);padding:18px 18px 16px;font:14px/1.5 Inter,system-ui,-apple-system,"Segoe UI",sans-serif}
#ws-cookie-banner[hidden]{display:none}
#ws-cookie-banner p{margin:0 0 12px}
#ws-cookie-banner a{color:#ffb800;text-decoration:underline}
#ws-cookie-banner .ws-ck-actions{display:flex;flex-wrap:wrap;gap:8px}
#ws-cookie-banner button{flex:1 1 auto;min-width:120px;border:1px solid #ffb800;border-radius:10px;padding:10px 14px;font:inherit;font-weight:700;cursor:pointer;background:#ffb800;color:#000}
#ws-cookie-banner button.ws-ck-link{background:transparent;color:#e4e4e7;border-color:#3f3f46;font-weight:600}
#ws-cookie-banner .ws-ck-settings{margin:0 0 12px;padding:10px 12px;border:1px solid #27272a;border-radius:10px}
#ws-cookie-banner .ws-ck-settings[hidden]{display:none}
#ws-cookie-banner label{display:flex;gap:10px;align-items:flex-start;margin:6px 0}
#ws-cookie-banner label small{display:block;color:#a1a1aa}
/* v115: mobilde bant ekranın üçte birini kaplıyor, poz fiyatını ve form alanlarını örtüyordu → tek satır düğme, sıkı boşluk */
@media(max-width:520px){
#ws-cookie-banner{left:8px;right:8px;bottom:8px;padding:12px 12px 10px;font-size:13px;line-height:1.45}
#ws-cookie-banner p{margin:0 0 9px}
#ws-cookie-banner .ws-ck-actions{flex-wrap:nowrap;gap:6px}
#ws-cookie-banner button{flex:1 1 0;min-width:0;padding:10px 6px;min-height:44px}
}
</style>
<div id="ws-cookie-banner" role="dialog" aria-live="polite" aria-label="Çerez tercihleri" hidden>
    <p>Sitenin çalışması için zorunlu çerezleri kullanıyoruz. Ziyaret istatistikleri ve reklam ölçümü için çerezleri ise yalnızca onay verirseniz kullanırız. Ayrıntılar: <a href="/legal/cerez-politikasi">Çerez Politikası</a></p>
    <div class="ws-ck-settings" hidden>
        <label><input type="checkbox" checked disabled> <span>Zorunlu çerezler<small>Oturum ve güvenlik. Kapatılamaz.</small></span></label>
        <label><input type="checkbox" id="ws-ck-analytics"> <span>Analitik çerezler<small>Google Analytics 4, anonim ziyaret istatistikleri.</small></span></label>
        <label><input type="checkbox" id="ws-ck-marketing"> <span>Pazarlama çerezleri<small>Google Ads dönüşüm ölçümü.</small></span></label>
    </div>
    <div class="ws-ck-actions">
        <button type="button" data-ws-ck="reject">Reddet</button>
        <button type="button" data-ws-ck="accept">Kabul et</button>
        <button type="button" class="ws-ck-link" data-ws-ck="settings">Ayarlar</button>
        <button type="button" class="ws-ck-link" data-ws-ck="save" hidden>Seçimi kaydet</button>
    </div>
</div>
