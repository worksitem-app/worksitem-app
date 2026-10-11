<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Talebiniz Alındı | WORKSİTEM</title>
    <meta name="robots" content="noindex, nofollow">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #f5f6f8; color: #101318; margin: 0; }
        .thanks-wrap { min-height: 60vh; display: flex; align-items: center; justify-content: center; padding: 56px 16px; text-align: center; }
        .box { max-width: 520px; padding: 44px 32px; border: 1px solid #e5e7eb; border-radius: 20px; background: #fff; box-shadow: 0 12px 32px rgba(16,19,24,0.08); }
        .icon { font-size: 50px; margin-bottom: 20px; }
        h1 { font-size: 28px; font-weight: 800; margin: 0 0 12px; color: #101318; letter-spacing: -0.02em; }
        p { font-size: 15px; color: #5b6472; line-height: 1.6; margin: 0 0 28px; }
        html[data-theme="dark"] body { background: #0b0f17; color: #f1f5f9; }
        html[data-theme="dark"] .box { background: #151c28; border-color: #1e293b; }
        html[data-theme="dark"] h1 { color: #f1f5f9; }
        html[data-theme="dark"] p { color: #94a3b8; }
        .btn { display: inline-block; background: #ffb800; color: #000; padding: 15px 28px; border-radius: 12px; font-weight: 800; text-decoration: none; transition: 0.2s; font-size: 14px;}
        .btn:hover { background: #e5a500; transform: translateY(-2px); }
    </style>

    @php
        $googleAdsId = config('lead_conversion.google_ads.conversion_id');
        $googleAdsLabel = config('lead_conversion.google_ads.conversion_label');
        $shouldTrackGoogleAds = session('google_ads_conversion', false)
            && filled($googleAdsId)
            && filled($googleAdsLabel);
    @endphp

    @if($shouldTrackGoogleAds)
        {{-- KVKK: Google Ads etiketi yalnızca "pazarlama" çerez onayı varsa yüklenir (bkz. partials/cookie-consent). --}}
        <script>
            (window.__wsConsentQueue = window.__wsConsentQueue || []).push(['marketing', function (ensureGtag) {
                ensureGtag(@json($googleAdsId));
                window.gtag('config', @json($googleAdsId));
                window.gtag('event', 'conversion', {
                    'send_to': @json($googleAdsId . '/' . $googleAdsLabel),
                    'transaction_id': @json(session('lead_public_id'))
                });
            }]);
        </script>
    @endif
</head>
<body>
    <x-site.contech-header />
    <main class="thanks-wrap">
    <div class="box">
        <div class="icon">✅</div>
        <h1>Talebiniz Başarıyla Alındı</h1>
        <p>Proje bilgileriniz Kainta İnşaat mühendislerine iletilmiştir. Ön değerlendirme yapıldıktan sonra detaylı teklif için sizinle iletişime geçeceğiz.</p>
        <a href="/araclar" class="btn">Araçlara dön</a>
    </div>
    </main>
    <x-site.contech-footer />
<script src="/js/p1-product-events.js" defer></script>
</body>
</html>
