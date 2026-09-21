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
            key = 'sess_' + Date.now() + '_' + Math.random().toString(36).substring(2, 15);
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
