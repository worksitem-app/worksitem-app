/*
 * Worksitem araç ekleri (Dalga 1)
 * - Paylaş: hesap girdilerini URL'ye yazar; link açılınca girdileri doldurup hesaplar
 * - Yazdır / PDF
 * - Olası fiyat aralığı (min–max)
 * Yapılandırma: <script id="ws-tool-guide-config"> (config/worksitem_tool_guides.php)
 * Kişisel veri (ad, telefon, e-posta) ASLA linke yazılmaz: yalnızca config'teki alanlar.
 */
(function () {
    'use strict';

    var cfgEl = document.getElementById('ws-tool-guide-config');
    if (!cfgEl) return;
    var cfg;
    try { cfg = JSON.parse(cfgEl.textContent); } catch (e) { return; }

    var money = new Intl.NumberFormat('tr-TR', { maximumFractionDigits: 0 });

    function track(name, extra) {
        try {
            if (typeof window.gtag === 'function') {
                var p = { tool_slug: cfg.slug };
                for (var k in extra || {}) p[k] = extra[k];
                window.gtag('event', name, p);
            }
        } catch (e) { /* ölçüm hatası hesaplamayı bozmasın */ }
    }

    function el(id) { return document.getElementById(id); }

    // ── Paylaş linki ───────────────────────────────────────────
    function buildShareUrl() {
        var url = new URL(window.location.href);
        url.hash = '';
        (cfg.fields || []).forEach(function (id) {
            var f = el(id);
            if (!f) return;
            var v = f.type === 'checkbox' ? (f.checked ? '1' : '0') : f.value;
            if (v !== '' && v != null) url.searchParams.set(id, v);
        });
        url.searchParams.set('hesapla', '1');
        return url.toString();
    }

    function restoreFromUrl() {
        var params = new URLSearchParams(window.location.search);
        if (params.get('hesapla') !== '1') return false;
        var any = false;
        (cfg.fields || []).forEach(function (id) {
            if (!params.has(id)) return;
            var f = el(id);
            if (!f) return;
            var v = params.get(id);
            if (f.type === 'checkbox') {
                f.checked = v === '1';
            } else if (f.tagName === 'SELECT') {
                // yalnızca listede olan bir değeri kabul et
                var ok = Array.prototype.some.call(f.options, function (o) { return o.value === v; });
                if (!ok) return;
                f.value = v;
            } else {
                if (f.type === 'number' && !isFinite(parseFloat(v))) return;
                f.value = v.slice(0, 32);
            }
            f.dispatchEvent(new Event('input', { bubbles: true }));
            f.dispatchEvent(new Event('change', { bubbles: true }));
            any = true;
        });
        return any;
    }

    function toast(msg) {
        var t = document.querySelector('[data-ws-toast]');
        if (!t) return;
        t.textContent = msg;
        clearTimeout(toast._t);
        toast._t = setTimeout(function () { t.textContent = ''; }, 3500);
    }

    function copyText(text) {
        if (navigator.clipboard && window.isSecureContext) {
            return navigator.clipboard.writeText(text);
        }
        return new Promise(function (resolve, reject) {
            var ta = document.createElement('textarea');
            ta.value = text;
            ta.setAttribute('readonly', '');
            ta.style.position = 'fixed';
            ta.style.opacity = '0';
            document.body.appendChild(ta);
            ta.select();
            try { document.execCommand('copy') ? resolve() : reject(); } catch (e) { reject(e); }
            document.body.removeChild(ta);
        });
    }

    function sharePanel(link) {
        var tb = document.querySelector('[data-ws-toolbar]');
        var panel = document.querySelector('[data-ws-sharepanel]');
        if (!panel) {
            panel = document.createElement('div');
            panel.className = 'ws-tg-share';
            panel.setAttribute('data-ws-sharepanel', '');
            panel.innerHTML =
                '<label class="ws-tg-share-l">Bu link aynı girdilerle hesabı açar</label>' +
                '<div class="ws-tg-share-row"><input type="text" readonly data-ws-link aria-label="Paylaşım linki">' +
                '<button type="button" class="ws-tg-btn" data-ws-copy>Kopyala</button></div>' +
                '<div class="ws-tg-share-row"><a class="ws-tg-btn" data-ws-wa target="_blank" rel="noopener">WhatsApp ile gönder</a>' +
                '<a class="ws-tg-btn" data-ws-mail>E-posta ile gönder</a></div>' +
                '<span class="ws-tg-share-ok" data-ws-copied role="status" aria-live="polite"></span>';
            tb.insertAdjacentElement('afterend', panel);
            panel.querySelector('[data-ws-copy]').addEventListener('click', function () {
                var inp = panel.querySelector('[data-ws-link]');
                copyText(inp.value).then(
                    function () { panel.querySelector('[data-ws-copied]').textContent = '✓ Link kopyalandı'; },
                    function () { inp.focus(); inp.select(); panel.querySelector('[data-ws-copied]').textContent = 'Linki seçtik; kopyalamak için Ctrl+C (telefonda uzun basın).'; }
                );
            });
        }
        var inp = panel.querySelector('[data-ws-link]');
        inp.value = link;
        var msg = document.title + ' ' + link;
        panel.querySelector('[data-ws-wa]').href = 'https://wa.me/?text=' + encodeURIComponent(msg);
        panel.querySelector('[data-ws-mail]').href = 'mailto:?subject=' + encodeURIComponent(document.title) + '&body=' + encodeURIComponent(link);
        panel.querySelector('[data-ws-copied]').textContent = '';
        panel.hidden = false;
        copyText(link).then(function () { panel.querySelector('[data-ws-copied]').textContent = '✓ Link kopyalandı'; }, function () {});
    }

    function onShare() {
        var link = buildShareUrl();
        track('tool_share');
        if (navigator.share && /Mobi|Android|iPhone/i.test(navigator.userAgent)) {
            navigator.share({ title: document.title, url: link }).catch(function () { sharePanel(link); });
            return;
        }
        sharePanel(link);
    }

    function onPrint() {
        track('tool_print');
        // Sunucuda hazırlanan PDF rapor varsa onu indir (telefonda ve uygulama içi tarayıcılarda da çalışır)
        if (cfg.pdf) {
            var u = new URL(buildShareUrl());
            window.location.href = cfg.pdf + u.search;
            return;
        }
        if (typeof window.print === 'function') {
            window.print();
        } else {
            toast('Bu tarayıcı yazdırmayı desteklemiyor. Sayfayı Chrome veya Safari ile açın.');
        }
    }

    // ── Olası aralık ───────────────────────────────────────────
    function parseTl(text) {
        // "1.234.567 TL", "1.234.567 TL + eksik kalem" → 1234567
        var m = (text || '').match(/-?\d{1,3}(?:\.\d{3})+(?:,\d+)?|-?\d+(?:,\d+)?/);
        if (!m) return null;
        var n = parseFloat(m[0].replace(/\./g, '').replace(',', '.'));
        return isFinite(n) ? n : null;
    }

    function setupRange() {
        if (!cfg.band || !cfg.total) return;
        var totalEl = document.querySelector(cfg.total);
        if (!totalEl) return;
        var rangeEl = document.createElement('span');
        rangeEl.className = 'ws-tg-range';
        rangeEl.hidden = true;
        totalEl.insertAdjacentElement('afterend', rangeEl);

        function update() {
            var n = parseTl(totalEl.textContent);
            if (n === null || n <= 0) { rangeEl.hidden = true; return; }
            var step = n >= 100000 ? 1000 : 100;
            var lo = Math.round(n * cfg.band[0] / step) * step;
            var hi = Math.round(n * cfg.band[1] / step) * step;
            rangeEl.textContent = 'Olası aralık: ' + money.format(lo) + ' – ' + money.format(hi) + ' TL';
            rangeEl.hidden = false;
        }
        new MutationObserver(update).observe(totalEl, { childList: true, characterData: true, subtree: true });
        update();
    }

    // ── Kurulum ────────────────────────────────────────────────
    function init() {
        var toolbar = document.querySelector('[data-ws-toolbar]');
        if (toolbar) {
            var result = cfg.result ? document.querySelector(cfg.result) : null;
            if (result) result.appendChild(toolbar);
            toolbar.hidden = false;
            var s = toolbar.querySelector('[data-ws-share]');
            var p = toolbar.querySelector('[data-ws-print]');
            if (s) s.addEventListener('click', onShare);
            if (p) p.addEventListener('click', onPrint);
        }

        setupRange();

        var calcBtn = cfg.calc ? document.querySelector(cfg.calc) : null;
        if (calcBtn) calcBtn.addEventListener('click', function () { track('tool_calculate'); });

        if (restoreFromUrl() && calcBtn) {
            setTimeout(function () { calcBtn.click(); track('tool_shared_open'); }, 250);
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
