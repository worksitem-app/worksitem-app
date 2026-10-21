@php($play = config('worksitem_app.twa.play_url'))
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Worksitem Uygulaması: Tadilat Maliyeti Telefonunuzda | WORKSİTEM</title>
    <meta name="description" content="Worksitem'i telefonunuza ücretsiz ekleyin: banyo, mutfak, boya ve inşaat maliyeti hesaplayıcıları tek dokunuşla. Android{{ $play ? ' (Google Play)' : '' }} ve iPhone.">
    <meta name="robots" content="index,follow">
    <link rel="canonical" href="https://worksitem.com/uygulama">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/worksitem-design.css">
    <style>
        .app-hero{padding:64px 0 36px}
        .app-hero-grid{display:grid;grid-template-columns:minmax(0,1.2fr) minmax(0,.8fr);gap:40px;align-items:center}
        .app-lead{max-width:640px;margin-top:18px;font-size:18px;line-height:1.7}
        .app-icon{width:132px;height:132px;border-radius:30px;box-shadow:0 18px 40px rgba(16,19,24,.15);justify-self:center}
        .app-cta{display:flex;gap:10px;flex-wrap:wrap;margin-top:26px;align-items:center}
        .app-cta small{color:var(--ws-text-muted)}
        .app-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:16px}
        .app-steps{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:16px}
        .app-steps ol{margin:10px 0 0;padding-left:20px;line-height:1.8}
        .app-card p{margin-top:8px;line-height:1.65}
        #appMsg{font-weight:700;font-size:14px}
        .app-cta [hidden]{display:none!important}
        @media(max-width:800px){.app-hero{padding:40px 0 24px}.app-hero-grid,.app-grid,.app-steps{grid-template-columns:1fr}.app-icon{width:72px;height:72px;border-radius:18px;grid-row:1;justify-self:start}}
    </style>
</head>
<body data-tool-slug="uygulama">
<x-site.header />
<main>
<section class="app-hero">
    <div class="ws-container app-hero-grid">
        <div>
            <div class="ws-badge ws-badge-yellow">ÜCRETSİZ UYGULAMA</div>
            <h1 class="ws-h1" style="margin-top:16px">Tadilat maliyeti artık<br><span style="color:var(--ws-highlight,#9A6E00)">telefonunuzun ana ekranında.</span></h1>
            <p class="ws-body ws-muted app-lead">Worksitem'i telefonunuza ekleyin: banyo, mutfak, boya, seramik ve inşaat maliyeti hesaplayıcıları tek dokunuşla açılsın. Üyelik gerekmez, fiyatlar her açılışta günceldir.</p>
            <div class="app-cta">
                @if ($play)<a class="ws-btn ws-btn-primary" href="{{ $play }}" rel="noopener" data-app="play">Google Play'den indir</a>@endif
                <button type="button" class="ws-btn {{ $play ? 'ws-btn-secondary' : 'ws-btn-primary' }}" id="appInstall" hidden>Bu cihaza yükle</button>
                <span id="appMsg" role="status"></span>
            </div>
        </div>
        <img class="app-icon" src="/icons/icon-512.png" alt="Worksitem uygulama simgesi" width="132" height="132">
    </div>
</section>

<section class="ws-section" style="padding-top:8px">
    <div class="ws-container">
        <h2 class="ws-h2">Nasıl yüklenir?</h2>
        <div class="app-steps" style="margin-top:18px">
            <article class="ws-card app-card">
                <h3 class="ws-h3">Android</h3>
                <ol>
                    @if ($play)<li><b>Google Play'den indir</b> düğmesine dokunun ve <b>Yükle</b>'ye basın.</li><li>Ya da Chrome'da bu sayfadayken menüden <b>Uygulamayı yükle</b>'yi seçin.</li>
                    @else<li>Bu sayfayı <b>Chrome</b> ile açın.</li><li>Yukarıdaki <b>Bu cihaza yükle</b> düğmesine ya da sağ üstteki menüden <b>Uygulamayı yükle</b>'ye dokunun.</li>@endif
                    <li>Worksitem simgesi ana ekranınıza eklenir.</li>
                </ol>
            </article>
            <article class="ws-card app-card">
                <h3 class="ws-h3">iPhone ve iPad</h3>
                <ol>
                    <li>Bu sayfayı <b>Safari</b> ile açın.</li>
                    <li>Alttaki <b>Paylaş</b> simgesine dokunun.</li>
                    <li><b>Ana Ekrana Ekle</b>'yi seçip <b>Ekle</b>'ye basın.</li>
                </ol>
            </article>
        </div>
    </div>
</section>

<section class="ws-section" style="padding-top:8px">
    <div class="ws-container">
        <h2 class="ws-h2">Uygulamada neler var?</h2>
        <div class="app-grid" style="margin-top:18px">
            <article class="ws-card app-card"><h3 class="ws-h3">Maliyet hesaplayıcılar</h3><p class="ws-muted">Banyo, mutfak, boya, seramik, parke, çatı ve inşaat maliyeti; malzeme ve işçilik kalem kalem, PDF rapor.</p></article>
            <article class="ws-card app-card"><h3 class="ws-h3">Metraj ve planlayıcılar</h3><p class="ws-muted">Beton, sıva, şap ve boya miktarı; banyo ve mutfağınızı 2D/3D yerleştirin.</p></article>
            @if (\App\Support\Pilot::firmSignupOpen())
            <article class="ws-card app-card"><h3 class="ws-h3">Firmalar için iş bildirimi</h3><p class="ws-muted">Kayıtlı usta ve firmalar, bölgelerine yeni iş geldiğinde telefonlarına bildirim alır. <a href="{{ route('firms.page') }}">Ücretsiz firma kaydı →</a></p></article>
            @endif
        </div>
        <p class="ws-muted" style="margin-top:18px;font-size:14px">Hesaplamalar internet bağlantısı gerektirir: fiyatlar her zaman güncel gösterilir, eski fiyat önbellekten sunulmaz.</p>
    </div>
</section>
</main>
<script>
(function () {
    var btn = document.getElementById('appInstall'), msg = document.getElementById('appMsg'), d = null;
    var ev = function (n) { try { if (window.gtag) gtag('event', n, { event_category: 'app' }); } catch (e) {} };
    var standalone = matchMedia('(display-mode: standalone)').matches || navigator.standalone === true;
    if (standalone) { msg.textContent = 'Worksitem bu cihazda uygulama olarak açık.'; return; }
    addEventListener('beforeinstallprompt', function (e) { e.preventDefault(); d = e; btn.hidden = false; });
    btn.addEventListener('click', function () { if (!d) return; ev('app_page_click'); d.prompt(); d.userChoice.then(function (c) { if (c.outcome === 'accepted') { btn.hidden = true; msg.textContent = 'Yüklendi. Ana ekranınızda Worksitem simgesini bulabilirsiniz.'; ev('app_installed'); } d = null; }); });
    document.querySelectorAll('[data-app=play]').forEach(function (a) { a.addEventListener('click', function () { ev('app_play_click'); }); });
})();
</script>
<x-site.footer />
</body>
</html>
