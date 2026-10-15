{{-- A/B ölçümü: [data-ab] öğesi ekranda görününce bir kez "view", tıklanınca "click" bildirir. Sayfada bir kez eklenir. --}}
@once
<script>
(function () {
    var url = @json(route('ab.track'));
    function send(ab, e) {
        try {
            var fd = new FormData(); fd.append('ab', ab); fd.append('e', e);
            if (navigator.sendBeacon) { navigator.sendBeacon(url, fd); } else { fetch(url, { method: 'POST', body: fd, keepalive: true }); }
            if (window.wsTrack) wsTrack('ab_' + e, { ab: ab });
        } catch (x) {}
    }
    var seen = {};
    function init() {
        var els = document.querySelectorAll('[data-ab]');
        if (!els.length) return;
        var io = 'IntersectionObserver' in window ? new IntersectionObserver(function (es) {
            es.forEach(function (en) { var ab = en.target.getAttribute('data-ab'); if (en.isIntersecting && !seen[ab]) { seen[ab] = 1; send(ab, 'view'); io.unobserve(en.target); } });
        }, { threshold: 0.5 }) : null;
        els.forEach(function (el) {
            if (io) io.observe(el); else if (!seen[el.getAttribute('data-ab')]) { seen[el.getAttribute('data-ab')] = 1; send(el.getAttribute('data-ab'), 'view'); }
            el.addEventListener('click', function () { send(el.getAttribute('data-ab'), 'click'); });
        });
    }
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init); else init();
})();
</script>
@endonce
