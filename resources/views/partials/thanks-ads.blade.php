{{-- Teşekkür sayfası: Google Ads dönüşümü (yalnızca pazarlama çerez onayıyla) --}}
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
