/*
 * Worksitem hesaplayıcı kiti
 *  - [data-wc-result]: sonuç kutusu. Görününce boş durum ([data-wc-empty]) gizlenir.
 *  - [data-wc-price]: TL tutarın yazıldığı öğe. Altına $/€/£ karşılığı (TCMB) yazılır,
 *    mobil sabit çubukta gösterilir.
 * Aracın kendi hesap kodu ve id'leri değişmez; kit yalnızca izler.
 */
(function () {
    'use strict';

    var cfgEl = document.getElementById('wc-config');
    var cfg = {};
    try { cfg = cfgEl ? JSON.parse(cfgEl.textContent) : {}; } catch (e) { cfg = {}; }

    var nf = new Intl.NumberFormat('tr-TR', { maximumFractionDigits: 0 });

    function visible(el) {
        if (!el) return false;
        var cs = window.getComputedStyle(el);
        return cs.display !== 'none' && cs.visibility !== 'hidden' && el.offsetHeight > 0;
    }

    // "1.234.567 TL – 1.500.000 TL", "₺106.000 – ₺150.000", "12.500 TL" → [sayılar]
    function parseTl(text) {
        if (!/TL|₺/.test(text || '')) return [];
        var m = (text || '').match(/\d{1,3}(?:\.\d{3})+(?:,\d+)?|\d+(?:,\d+)?/g) || [];
        return m.map(function (s) { return parseFloat(s.replace(/\./g, '').replace(',', '.')); })
            .filter(function (n) { return isFinite(n) && n >= 100; })
            .slice(0, 2);
    }

    function esc(s) {
        return String(s == null ? '' : s).replace(/[&<>"']/g, function (c) {
            return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
        });
    }

    function round10(v) { return Math.round(v / 10) * 10; }

    function fxHtml(nums) {
        if (!cfg.fx || !cfg.fx.rates || !nums.length) return '';
        var out = '';
        Object.keys(cfg.symbols || {}).forEach(function (code) {
            var rate = cfg.fx.rates[code], sym = cfg.symbols[code];
            if (!rate) return;
            var parts = nums.map(function (n) { return esc(sym) + nf.format(round10(n / rate)); });
            out += '<span>≈ ' + parts.join(' – ') + '</span>';
        });
        return out + '<span><em>TCMB satış kuru, ' + esc(cfg.fx.date) + '</em></span>';
    }

    var result = document.querySelector('[data-wc-result]');
    var empty = document.querySelector('[data-wc-empty]');
    var price = document.querySelector('[data-wc-price]');
    var dock = document.getElementById('wcDock');
    var dockText = document.getElementById('wcDockText');
    var fxBox = null;

    if (price) {
        fxBox = document.createElement('div');
        fxBox.className = 'wc-fx';
        fxBox.hidden = true;
        // Fiyat yan yana dizilmiş bir satırın içindeyse (flex), döviz satırını satırın altına koy
        var anchor = price;
        var pd = price.parentElement ? window.getComputedStyle(price.parentElement).display : '';
        if (/flex|grid/.test(pd) && price.parentElement !== result) anchor = price.parentElement;
        anchor.insertAdjacentElement('afterend', fxBox);
    }

    // Hukuki kısa uyarı: sonuç kutusunun en altına (config/worksitem_legal.php → result_short)
    if (result && cfg.legal && !result.querySelector('.ws-result-legal')) {
        var legal = document.createElement('p');
        legal.className = 'ws-result-legal';
        legal.setAttribute('role', 'note');
        legal.appendChild(document.createTextNode(cfg.legal + ' '));
        var a = document.createElement('a');
        a.href = '/legal/kullanim-kosullari';
        a.textContent = 'Koşullar';
        legal.appendChild(a);
        result.appendChild(legal);
    }

    var resultInView = false;

    function sync() {
        var shown = visible(result);
        if (empty) empty.hidden = shown;

        var text = price ? (price.textContent || '').trim() : '';
        var nums = parseTl(text);
        if (fxBox) {
            var html = shown ? fxHtml(nums) : '';
            fxBox.innerHTML = html;
            fxBox.hidden = html === '';
        }

        if (dock) {
            var useful = shown && /\d/.test(text) && !/^0\s*TL$/.test(text);
            if (useful) dockText.textContent = text;
            document.body.classList.toggle('wc-has-dock', useful);
            dock.classList.toggle('is-hidden', !useful || resultInView);
        }
    }

    if (result) {
        new MutationObserver(sync).observe(result, { attributes: true, attributeFilter: ['style', 'class', 'hidden'] });
        if (price) new MutationObserver(sync).observe(price, { childList: true, characterData: true, subtree: true });

        if ('IntersectionObserver' in window) {
            new IntersectionObserver(function (entries) {
                resultInView = entries[0].isIntersecting;
                sync();
            }, { threshold: 0.2 }).observe(result);
        }
    }

    var dockBtn = document.getElementById('wcDockBtn');
    if (dockBtn && result) {
        dockBtn.addEventListener('click', function () {
            result.scrollIntoView({ behavior: 'smooth', block: 'start' });
        });
    }

    sync();
})();
