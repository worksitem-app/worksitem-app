<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Talebiniz Alındı | WORKSİTEM</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background: #050505; color: #fff; display: flex; align-items: center; justify-content: center; height: 100vh; margin: 0; text-align: center; }
        .box { max-width: 500px; padding: 45px 30px; border: 1px solid rgba(255,184,0,0.2); border-radius: 24px; background: #111; box-shadow: 0 20px 50px rgba(0,0,0,0.5); }
        .icon { font-size: 50px; margin-bottom: 20px; }
        h1 { font-size: 28px; font-weight: 900; margin-bottom: 15px; color: #ffb800; letter-spacing: -1px; }
        p { font-size: 15px; color: #a1a1aa; line-height: 1.6; margin-bottom: 35px; }
        .btn { display: inline-block; background: #ffb800; color: #000; padding: 15px 28px; border-radius: 12px; font-weight: 800; text-decoration: none; transition: 0.2s; font-size: 14px;}
        .btn:hover { background: #e5a400; transform: translateY(-2px); }
    </style>

    @php
        $googleAdsId = config('lead_conversion.google_ads.conversion_id');
        $googleAdsLabel = config('lead_conversion.google_ads.conversion_label');
        $shouldTrackGoogleAds = session('google_ads_conversion', false)
            && filled($googleAdsId)
            && filled($googleAdsLabel);
    @endphp

    @if($shouldTrackGoogleAds)
        <script async src="https://www.googletagmanager.com/gtag/js?id={{ $googleAdsId }}"></script>
        <script>
            window.dataLayer = window.dataLayer || [];

            function gtag() {
                dataLayer.push(arguments);
            }

            gtag('js', new Date());
            gtag('config', @json($googleAdsId));

            gtag('event', 'conversion', {
                'send_to': @json($googleAdsId . '/' . $googleAdsLabel),
                'transaction_id': @json(session('lead_public_id'))
            });
        </script>
    @endif
</head>
<body>
    <div class="box">
        <div class="icon">✅</div>
        <h1>Talebiniz Başarıyla Alındı</h1>
        <p>Proje bilgileriniz Kainta İnşaat mühendislerine iletilmiştir. Ön değerlendirme yapıldıktan sonra detaylı teklif için sizinle iletişime geçeceğiz.</p>
        <a href="/araclar" class="btn">← Araçlara Dön</a>
    </div>
</body>
</html>
