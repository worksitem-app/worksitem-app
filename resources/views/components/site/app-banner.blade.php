{{--
    "Uygulamayı yükle" bandı — BrandShell her public sayfanın sonuna ekler.
    Kurallar (rahatsız etmesin): yalnız telefonda, ikinci sayfa görüntülemesinden sonra, 8 sn gecikmeyle;
    kapatılırsa 30 gün gösterilmez; uygulama olarak açıkken (ana ekran / Google Play) hiç gösterilmez.
    Android: Google Play adresi tanımlıysa Play'e, değilse tarayıcının kurulum penceresine · iPhone: Ana Ekrana Ekle talimatı.
--}}
<div id="wsAppBanner" hidden data-play="{{ config('worksitem_app.twa.play_url') }}" role="dialog" aria-label="Worksitem uygulaması">
    <img src="/icons/icon-192.png" alt="" width="44" height="44">
    <div class="t"><b>Worksitem uygulaması</b><span data-txt>Hesaplayıcılar telefonunuzda, tek dokunuşla.</span></div>
    <button type="button" class="go" data-go>Yükle</button>
    <button type="button" class="x" data-x aria-label="Kapat">×</button>
    <div class="ios" data-ios hidden>Safari'de alttaki <b>Paylaş</b> <svg width="14" height="16" viewBox="0 0 14 16" aria-hidden="true"><path d="M7 1v9M3.5 4.5 7 1l3.5 3.5M2 7.5v7h10v-7" fill="none" stroke="currentColor" stroke-width="1.6"/></svg> simgesine, sonra <b>Ana Ekrana Ekle</b>'ye dokunun.</div>
</div>
<style>
#wsAppBanner{position:fixed;left:12px;right:12px;bottom:12px;z-index:2147482000;max-width:520px;margin:0 auto;display:grid;grid-template-columns:44px 1fr auto auto;gap:4px 10px;align-items:center;background:#fff;color:#101318;border:1px solid #E3E6EA;border-radius:16px;box-shadow:0 12px 36px rgba(16,19,24,.18);padding:10px 10px 10px 12px;font:14px/1.4 Inter,system-ui,-apple-system,'Segoe UI',sans-serif}
#wsAppBanner[hidden]{display:none}
#wsAppBanner img{border-radius:10px}
#wsAppBanner .t{display:flex;flex-direction:column;min-width:0}
#wsAppBanner .t span{color:#5B6472;font-size:12.5px}
#wsAppBanner .go{background:#FFB800;color:#101318;border:0;border-radius:10px;font-weight:800;font-size:14px;padding:9px 14px;cursor:pointer}
#wsAppBanner .x{background:none;border:0;font-size:22px;line-height:1;color:#5B6472;padding:4px 6px;cursor:pointer}
#wsAppBanner .ios{grid-column:1/-1;background:#FFF8E1;border-radius:10px;padding:8px 10px;font-size:13px}
#wsAppBanner .ios svg{vertical-align:-2px}
html[data-theme="dark"] #wsAppBanner{background:#141A24;color:#E6E9EE;border-color:#232C3A}
html[data-theme="dark"] #wsAppBanner .t span{color:#C9CFD8}
html[data-theme="dark"] #wsAppBanner .ios{background:#2A2410}
@media print{#wsAppBanner{display:none!important}}
</style>
<script>
(function () {
    var b = document.getElementById('wsAppBanner'); if (!b) return;
    var ua = navigator.userAgent, K = 'ws_app_banner', st = {};
    var standalone = false;
    try { standalone = matchMedia('(display-mode: standalone)').matches || navigator.standalone === true || document.referrer.indexOf('android-app://') === 0; } catch (e) {}
    var mobile = /android|iphone|ipad|ipod/i.test(ua) || (navigator.platform === 'MacIntel' && navigator.maxTouchPoints > 1);
    if (standalone || !mobile) return;
    try { st = JSON.parse(localStorage.getItem(K) || '{}') || {}; } catch (e) {}
    var save = function () { try { localStorage.setItem(K, JSON.stringify(st)); } catch (e) {} };
    if (st.no && Date.now() - st.no < 30 * 864e5) return;
    st.v = (st.v || 0) + 1; save();
    var ev = function (n) { try { if (window.gtag) gtag('event', n, { event_category: 'app' }); } catch (e) {} };
    var android = /android/i.test(ua), ios = !android && /iphone|ipad|ipod|macintosh/i.test(ua);
    var safari = ios && !/crios|fxios|edgios|opios/i.test(ua);
    var play = b.dataset.play, deferred = null;
    addEventListener('beforeinstallprompt', function (e) { e.preventDefault(); deferred = e; });
    var hide = function () { b.hidden = true; };
    b.querySelector('[data-x]').addEventListener('click', function () { st.no = Date.now(); save(); hide(); ev('app_banner_close'); });
    b.querySelector('[data-go]').addEventListener('click', function () {
        ev('app_banner_click');
        if (android && play) { location.href = play; return; }
        if (deferred) { deferred.prompt(); deferred.userChoice.then(function (c) { if (c.outcome === 'accepted') { ev('app_installed'); st.no = Date.now(); save(); hide(); } deferred = null; }); return; }
        if (ios) { b.querySelector('[data-ios]').hidden = false; }
    });
    addEventListener('appinstalled', function () { ev('app_installed'); st.no = Date.now(); save(); hide(); });
    if (st.v < 2 || /^\/(cevrimdisi|uygulama|firma-paneli|odeme)/.test(location.pathname)) return;
    setTimeout(function () {
        var ck = document.getElementById('ws-cookie-' + 'banner');
        if (ck && !ck.hidden) return;                       // çerez bandı açıkken üstüne binmesin
        if (!(android && play) && !deferred && !safari) return; // yükleme yolu yoksa gösterme
        if (safari) b.querySelector('[data-go]').textContent = 'Nasıl?';
        b.hidden = false; ev('app_banner_shown');
    }, 8000);
})();
</script>
