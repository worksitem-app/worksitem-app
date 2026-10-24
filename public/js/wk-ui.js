/* Worksitem UI (wk-) davranışları — v117. BrandShell, içinde .wk-root olan sayfalara defer ile ekler.
   Kabuk (menü, arama, tema) bu dosyada değil, ortak başlıkta. */
(function () {
  'use strict';

  // Türkçe sayı biçimi: wkNum(106500) → "106.500", wkNum(3650, 2) → "3.650,00"
  window.wkNum = function (n, digits) {
    var d = digits || 0;
    return Number(n || 0).toLocaleString('tr-TR', { minimumFractionDigits: d, maximumFractionDigits: d });
  };
  // "1.250,5" / "1250.5" → 1250.5
  window.wkParse = function (v) {
    var s = String(v == null ? '' : v).trim().replace(/\s/g, '');
    if (s.indexOf(',') !== -1) s = s.replace(/\./g, '').replace(',', '.');
    var n = parseFloat(s);
    return isFinite(n) ? n : 0;
  };

  // Sonuç değişince ekran okuyucuya tek duyuru (yazarken her tuşta değil)
  var timers = new WeakMap();
  window.wkAnnounce = function (el, text, delay) {
    if (!el) return;
    clearTimeout(timers.get(el));
    timers.set(el, setTimeout(function () { el.textContent = text; }, delay == null ? 600 : delay));
  };

  function ready(fn) {
    if (document.readyState !== 'loading') fn(); else document.addEventListener('DOMContentLoaded', fn);
  }

  ready(function () {
    // :has() desteklemeyen tarayıcılar: seçili seçenek kartı / segment
    var hasHas = window.CSS && CSS.supports && CSS.supports('selector(:has(*))');
    if (!hasHas) {
      var sync = function () {
        document.querySelectorAll('.wk-option, .wk-segment label').forEach(function (l) {
          var i = l.querySelector('input');
          if (i) l.classList.toggle('is-checked', i.checked);
        });
      };
      document.addEventListener('change', sync);
      document.addEventListener('focusin', function (e) {
        var l = e.target.closest && e.target.closest('.wk-option');
        if (l) l.classList.add('is-focused');
      });
      document.addEventListener('focusout', function (e) {
        var l = e.target.closest && e.target.closest('.wk-option');
        if (l) l.classList.remove('is-focused');
      });
      sync();
    }

    // Mobil alt çubuk: sonuç paneli ekrandayken gizlenir (data-wk-target="#sonuc")
    document.querySelectorAll('.wk-mobile-bar').forEach(function (bar) {
      var target = document.querySelector(bar.getAttribute('data-wk-target') || '.wk-result');
      if (!target || !('IntersectionObserver' in window)) return;
      document.body.classList.add('wk-has-mobile-bar');
      new IntersectionObserver(function (entries) {
        bar.hidden = entries[0].isIntersecting;
      }, { threshold: 0.15 }).observe(target);
    });
  });
})();
