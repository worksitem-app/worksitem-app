{{-- GA4 olay yardımcısı: yalnızca "analitik" çerez onayı varsa gönderir (onay sonradan verilirse kuyruktan gönderilir) --}}
<script>
window.wsTrack = window.wsTrack || function (name, params) {
    try {
        var c = window.WorksitemConsent && window.WorksitemConsent.get && window.WorksitemConsent.get();
        if (c && c.analytics && typeof window.gtag === 'function') { window.gtag('event', name, params || {}); return; }
        (window.__wsConsentQueue = window.__wsConsentQueue || []).push(['analytics', function () {
            if (typeof window.gtag === 'function') window.gtag('event', name, params || {});
        }]);
    } catch (e) {}
};
</script>
