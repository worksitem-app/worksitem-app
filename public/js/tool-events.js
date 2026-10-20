/**
 * Worksitem Tool Events Tracker
 * 
 * Kullanıcı davranışını takip eder ve backend'e gönderir.
 * Tüm araçlarda ortak kullanılır.
 */

(function() {
    'use strict';

    // Session key oluştur (sayfa başına değil, oturum başına)
    function getSessionKey() {
        let key = sessionStorage.getItem('ws_session_key');
        if (!key) {
            // Oturum anahtarı: kriptografik rastgele (Math.random tahmin edilebilir)
            const c = window.crypto || {};
            let rnd = '';
            if (typeof c.randomUUID === 'function') rnd = c.randomUUID().replace(/-/g, '');
            else if (typeof c.getRandomValues === 'function') rnd = Array.from(c.getRandomValues(new Uint8Array(16)), (b) => b.toString(16).padStart(2, '0')).join('');
            else rnd = Date.now().toString(36);
            key = 'sess_' + Date.now() + '_' + rnd;
            sessionStorage.setItem('ws_session_key', key);
        }
        return key;
    }

    // Backend'e event gönder
    function sendEvent(toolSlug, eventType, extra = {}) {
        if (!toolSlug) {
            console.warn('[ToolEvents] tool_slug gerekli');
            return;
        }

        const payload = {
            tool_slug: toolSlug,
            event_type: eventType,
            session_key: getSessionKey(),
            ...extra
        };

        // CSRF token al
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

        fetch('/api/tool-events', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken || '',
            },
            body: JSON.stringify(payload),
            keepalive: true, // Sayfa kapanırken bile gönder
        }).catch(err => {
            console.warn('[ToolEvents] Gönderilemedi:', err);
        });
    }

    // Global API
    window.WorksitemEvents = {
        /**
         * Araç sayfası açıldığında
         */
        trackToolView: function(toolSlug) {
            sendEvent(toolSlug, 'tool_view');
        },

        /**
         * Hesaplama başladığında
         */
        trackCalculatorStart: function(toolSlug, metadata = {}) {
            sendEvent(toolSlug, 'calculator_start', { metadata });
        },

        /**
         * Hesaplama tamamlandığında
         */
        trackCalculatorComplete: function(toolSlug, metadata = {}) {
            sendEvent(toolSlug, 'calculator_complete', { metadata });
        },

        /**
         * Genel maliyet CTA'sı tıklandığında
         */
        trackCostCta: function(toolSlug, targetUrl = null) {
            sendEvent(toolSlug, 'cost_cta_click', { target_url: targetUrl });
        },

        /**
         * Kainta İnşaat CTA'sı tıklandığında
         */
        trackKaintaCta: function(toolSlug, targetUrl) {
            sendEvent(toolSlug, 'kainta_cta_click', { target_url: targetUrl });
        },

        /**
         * Sponsor CTA'sı tıklandığında
         */
        trackSponsorClick: function(toolSlug, sponsorKey, targetUrl) {
            sendEvent(toolSlug, 'sponsor_click', {
                sponsor_key: sponsorKey,
                target_url: targetUrl,
            });
        },

        /**
         * Otomatik tool_view — <body data-tool-slug="..."> varsa
         */
        autoTrackView: function() {
            const body = document.body;
            const toolSlug = body.dataset.toolSlug;
            if (toolSlug) {
                sendEvent(toolSlug, 'tool_view');
            }
        }
    };

    // DOM hazır olduğunda otomatik tool view
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', window.WorksitemEvents.autoTrackView);
    } else {
        window.WorksitemEvents.autoTrackView();
    }

    // Global Kainta CTA tıklama yakalayıcı
    document.addEventListener('click', function(e) {
        const target = e.target.closest('[data-event]');
        if (!target) return;

        const eventType = target.dataset.event;
        const toolSlug = target.dataset.toolSlug || document.body.dataset.toolSlug;
        const targetUrl = target.href || target.dataset.url;

        if (eventType === 'kainta_cta' && toolSlug) {
            window.WorksitemEvents.trackKaintaCta(toolSlug, targetUrl);
        } else if (eventType === 'cost_cta' && toolSlug) {
            window.WorksitemEvents.trackCostCta(toolSlug, targetUrl);
        } else if (eventType === 'sponsor_click' && toolSlug) {
            window.WorksitemEvents.trackSponsorClick(toolSlug, target.dataset.sponsorKey, targetUrl);
        }
    });

})();

// ============================================================
// GLOBAL KAINTA CTA TAKİBİ
// ============================================================
// Herhangi bir "Kainta İnşaat" içeren buton/link tıklamasını yakalar
document.addEventListener('click', function(e) {
    const target = e.target.closest('button, a');
    if (!target) return;

    const text = (target.innerText || target.textContent || '').toLowerCase();
    const href = target.href || '';
    // Alan adını URL'den çöz (ör. "evil.com/?kainta.com" eşleşmesin)
    let host = '';
    try { host = href ? new URL(href, location.href).hostname.toLowerCase() : ''; } catch (err) { host = ''; }
    const isKainta = host === 'kainta.com' || host.endsWith('.kainta.com');

    // "Kainta" kelimesi geçiyor mu?
    if (text.includes('kainta') || isKainta) {
        const toolSlug = document.body.dataset.toolSlug || 'unknown';
        const eventType = isKainta ? 'kainta_cta_click' : 'cost_cta_click';

        if (window.WorksitemEvents) {
            if (eventType === 'kainta_cta_click') {
                window.WorksitemEvents.trackKaintaCta(toolSlug, href);
            } else {
                window.WorksitemEvents.trackCostCta(toolSlug, href);
            }
        }
    }
});

// ============================================================
// CALCULATOR COMPLETE TAKİBİ
// ============================================================
// "Hesapla", "Calculate", "Göster" gibi butonlara tıklanınca
document.addEventListener('click', function(e) {
    const target = e.target.closest('button');
    if (!target) return;

    const text = (target.innerText || target.textContent || '').toLowerCase();
    const calcKeywords = ['hesapla', 'calculate', 'göster', 'sonuç', 'result', 'bütçe'];
    const isCalc = calcKeywords.some(kw => text.includes(kw));

    if (isCalc) {
        const toolSlug = document.body.dataset.toolSlug || 'unknown';

        /* P1_PROTECTED_CALCULATORS */
        if ([
            'tile-calculator',
            'seramik-maliyet-hesaplayici',
            'beton-calculator',
            'beton-hesaplayici',
            'paint-calculator',
            'boya-hesaplayici'
        ].includes(toolSlug)) return;
        if (window.WorksitemEvents) {
            window.WorksitemEvents.trackCalculatorStart(toolSlug, {
                button_text: text.substring(0, 50),
            });
        }
    }
});

// ============================================================
// CALCULATOR COMPLETE TAKİBİ
// ============================================================
// "Hesapla" butonuna basıldıktan 1 saniye sonra complete gönder
document.addEventListener('click', function(e) {
    const target = e.target.closest('button');
    if (!target) return;

    const text = (target.innerText || target.textContent || '').toLowerCase();
    const calcKeywords = ['hesapla', 'calculate', 'göster', 'sonuç', 'result', 'bütçe'];
    const isCalc = calcKeywords.some(kw => text.includes(kw));

    if (isCalc) {
        const toolSlug = document.body.dataset.toolSlug || 'unknown';

        /* P1_PROTECTED_CALCULATORS */
        if ([
            'tile-calculator',
            'seramik-maliyet-hesaplayici',
            'beton-calculator',
            'beton-hesaplayici',
            'paint-calculator',
            'boya-hesaplayici'
        ].includes(toolSlug)) return;

        // Start event (mevcut)
        if (window.WorksitemEvents) {
            window.WorksitemEvents.trackCalculatorStart(toolSlug, {
                button_text: text.substring(0, 50),
            });

            // Complete event (1 saniye sonra — sonuç gösterilmiş olmalı)
            setTimeout(() => {
                window.WorksitemEvents.trackCalculatorComplete(toolSlug, {
                    completed_after_ms: 1000,
                });
            }, 1000);
        }
    }
});
