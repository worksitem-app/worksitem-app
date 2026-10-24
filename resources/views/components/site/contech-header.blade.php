@php
    // v117 ortak kabuk: tek üst menü. Stiller BrandShell'in gömdüğü css/wk-shell.css'te (her sayfada aynı).
    $wksSearchTools = [];
    foreach (config('worksitem_tools.tools', []) as $slug => $tool) {
        // Aramada yalnız kullanılabilen araçlar (incelemedeki araca götürüp "kapalı" penceresi göstermeyelim)
        if ($slug === 'havalandirma-kanali' || ! \App\Support\Tools::isActive($slug)) {
            continue;
        }
        $wksSearchTools[] = [
            'slug'  => $slug,
            'title' => (string) ($tool['title'] ?? ''),
            'desc'  => (string) ($tool['desc'] ?? ''),
            'cat'   => (string) ($tool['cat'] ?? ''),
            'url'   => '/araclar/'.$slug,
        ];
    }
    $wksNav = [
        ['href' => '/araclar', 'label' => 'Araçlar', 'on' => request()->is('araclar', 'araclar/*')],
        ['href' => '/maliyet-hesapla', 'label' => 'Maliyet', 'on' => request()->is('maliyet-hesapla', 'maliyet-hesapla/*')],
        ['href' => '/pozlar', 'label' => 'Pozlar', 'on' => request()->is('pozlar', 'pozlar/*', 'poz/*')],
    ];
@endphp
<header class="wk-header" data-wks-tool-count="{{ count($wksSearchTools) }}">
<div class="wk-header-in">
    <a class="wk-logo" href="/" aria-label="Worksitem ana sayfa">
        <img src="/brand/worksitem-logo.png" alt="Worksitem" width="200" height="58" fetchpriority="high">
    </a>
    <nav class="wk-nav" id="wksMainNav" aria-label="Ana menü">
        @foreach ($wksNav as $item)
        <a href="{{ $item['href'] }}"@if ($item['on']) aria-current="page"@endif>{{ $item['label'] }}</a>
        @endforeach
    </nav>
    <div class="wk-header-tools">
        <button type="button" class="wk-iconbtn wk-search-open" id="wksGlobalSearchOpen" aria-haspopup="dialog" aria-controls="wksGlobalSearch" aria-expanded="false" aria-label="Araçlarda ve pozlarda ara">
            <svg class="wk-i" viewBox="0 0 24 24" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
            <span class="wk-search-open-label" aria-hidden="true">Ara</span>
            <kbd class="wk-search-key" aria-hidden="true">/</kbd>
        </button>
        @isset($actions)
            {{ $actions }}
        @endisset
        <button type="button" class="wk-iconbtn wk-theme-btn" id="wksThemeToggle" data-wk-theme-toggle aria-label="Koyu temaya geç" aria-pressed="false">
            <svg class="wk-i wk-i-moon" viewBox="0 0 24 24" aria-hidden="true"><path d="M20 14.5A8 8 0 0 1 9.5 4a8 8 0 1 0 10.5 10.5z"/></svg>
            <svg class="wk-i wk-i-sun" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.9 4.9l1.4 1.4M17.7 17.7l1.4 1.4M2 12h2M20 12h2M4.9 19.1l1.4-1.4M17.7 6.3l1.4-1.4"/></svg>
        </button>
        <button type="button" class="wk-iconbtn wk-menu-btn" id="wksMenuButton" aria-controls="wksMenuPanel" aria-expanded="false" aria-label="Menüyü aç">
            <svg class="wk-i wk-i-menu" viewBox="0 0 24 24" aria-hidden="true"><path d="M4 7h16M4 12h16M4 17h16"/></svg>
            <svg class="wk-i wk-i-close" viewBox="0 0 24 24" aria-hidden="true"><path d="M6 6l12 12M18 6 6 18"/></svg>
        </button>
    </div>
</div>
<nav class="wk-subnav" aria-label="Ana menü (mobil)">
    @foreach ($wksNav as $item)
    <a href="{{ $item['href'] }}"@if ($item['on']) aria-current="page"@endif>{{ $item['label'] }}</a>
    @endforeach
</nav>
<div class="wk-menu-panel" id="wksMenuPanel" hidden>
    <ul>
        <li><a href="/">Ana sayfa</a></li>
        <li><a href="/hakkimizda">Hakkımızda</a></li>
        <li><a href="/uygulama">Uygulamayı indir</a></li>
        @if (\App\Support\Pilot::firmSignupOpen())<li><a href="/firma-uyeligi">Firma üyeliği</a></li>@endif
        <li><a href="/#sss">Sık sorulanlar</a></li>
    </ul>
    <button type="button" class="wk-menu-theme" data-wk-theme-toggle aria-pressed="false">
        <svg class="wk-i" viewBox="0 0 24 24" aria-hidden="true"><path d="M20 14.5A8 8 0 0 1 9.5 4a8 8 0 1 0 10.5 10.5z"/></svg>
        <span data-wk-theme-label>Koyu tema</span>
    </button>
</div>
<div class="wk-search-overlay" id="wksGlobalSearch" role="dialog" aria-modal="true" aria-labelledby="wksGlobalSearchTitle" hidden>
    <div class="wk-search-dialog">
        <div class="wk-search-top">
            <svg class="wk-i" viewBox="0 0 24 24" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
            <input type="search" id="wksGlobalSearchInput" autocomplete="off" placeholder="Beton, boya, çatı ya da poz no" aria-label="Araçlarda ve pozlarda ara" aria-controls="wksGlobalSearchResults">
            <button type="button" class="wk-iconbtn" id="wksGlobalSearchClose" aria-label="Aramayı kapat">
                <svg class="wk-i" viewBox="0 0 24 24" aria-hidden="true"><path d="M6 6l12 12M18 6 6 18"/></svg>
            </button>
        </div>
        <p class="wk-search-meta" id="wksGlobalSearchTitle">{{ count($wksSearchTools) }} araç ve resmî pozlar içinde ara</p>
        <div class="wk-search-results" id="wksGlobalSearchResults" role="listbox" aria-label="Arama sonuçları"></div>
        <div class="wk-search-footer" aria-hidden="true"><span><kbd>↑</kbd><kbd>↓</kbd> Seç</span><span><kbd>Enter</kbd> Aç</span><span><kbd>Esc</kbd> Kapat</span></div>
    </div>
</div>
<script id="wk-header-js">
(function () {
    var root = document.documentElement;
    var KEY = 'worksitem-theme';

    // ── Tema (başlangıç teması <head>'deki wk-theme-init'te uygulanır; burada düğmeler bağlanır) ──
    function applyTheme(theme) {
        var dark = theme === 'dark';
        root.setAttribute('data-theme', dark ? 'dark' : 'light');
        document.querySelectorAll('[data-wk-theme-toggle]').forEach(function (b) {
            b.setAttribute('aria-pressed', String(dark));
            if (b.id === 'wksThemeToggle') b.setAttribute('aria-label', dark ? 'Açık temaya geç' : 'Koyu temaya geç');
            var l = b.querySelector('[data-wk-theme-label]');
            if (l) l.textContent = dark ? 'Açık tema' : 'Koyu tema';
        });
        var meta = document.querySelector('meta[name="theme-color"]');
        if (meta) meta.setAttribute('content', dark ? '#141a24' : '#ffffff');
    }
    var saved = null;
    try { saved = localStorage.getItem(KEY); } catch (e) {}
    applyTheme(saved === 'dark' ? 'dark' : 'light'); // varsayılan açık; koyu yalnız kullanıcı seçince
    document.querySelectorAll('[data-wk-theme-toggle]').forEach(function (b) {
        b.addEventListener('click', function () {
            var next = root.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
            applyTheme(next);
            try { localStorage.setItem(KEY, next); } catch (e) { /* tema yine çalışır */ }
        });
    });

    // ── Mobil menü ──
    var menuBtn = document.getElementById('wksMenuButton');
    var panel = document.getElementById('wksMenuPanel');
    function setMenu(open) {
        if (!menuBtn || !panel) return;
        panel.hidden = !open;
        menuBtn.setAttribute('aria-expanded', String(open));
        menuBtn.setAttribute('aria-label', open ? 'Menüyü kapat' : 'Menüyü aç');
    }
    if (menuBtn && panel) {
        menuBtn.addEventListener('click', function () { setMenu(panel.hidden); });
        panel.addEventListener('click', function (e) { if (e.target.closest('a')) setMenu(false); });
        document.addEventListener('click', function (e) {
            if (!panel.hidden && !panel.contains(e.target) && !menuBtn.contains(e.target)) setMenu(false);
        });
    }

    // ── Arama ──
    var tools = {!! json_encode($wksSearchTools, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!};
    var openButton = document.getElementById('wksGlobalSearchOpen');
    var overlay = document.getElementById('wksGlobalSearch');
    var input = document.getElementById('wksGlobalSearchInput');
    var results = document.getElementById('wksGlobalSearchResults');
    var closeButton = document.getElementById('wksGlobalSearchClose');
    if (!openButton || !overlay || !input || !results) return;

    var selectedIndex = 0;
    var current = [];

    function normalize(v) {
        return String(v || '').toLocaleLowerCase('tr-TR').normalize('NFD').replace(/[̀-ͯ]/g, '')
            .replace(/ı/g, 'i').replace(/ğ/g, 'g').replace(/ü/g, 'u').replace(/ş/g, 's').replace(/ö/g, 'o').replace(/ç/g, 'c').trim();
    }
    function matches(query) {
        var q = normalize(query);
        if (!q) return tools.slice(0, 8);
        return tools.map(function (t) {
            var title = normalize(t.title), desc = normalize(t.desc), s = 0;
            if (title === q) s += 100;
            if (title.indexOf(q) === 0) s += 60;
            if (title.indexOf(q) !== -1) s += 35;
            if (desc.indexOf(q) !== -1) s += 10;
            return { t: t, s: s };
        }).filter(function (x) { return x.s > 0; })
          .sort(function (a, b) { return b.s - a.s || a.t.title.localeCompare(b.t.title, 'tr-TR'); })
          .slice(0, 8).map(function (x) { return x.t; });
    }
    function row(item, index) {
        var b = document.createElement('a');
        b.href = item.url;
        b.className = 'wk-search-result' + (item.poz ? ' wk-search-poz' : '');
        b.id = 'wksSearchOpt' + index;
        b.setAttribute('role', 'option');
        var copy = document.createElement('span');
        var title = document.createElement('b');
        title.textContent = item.title;
        var desc = document.createElement('span');
        desc.textContent = item.desc;
        copy.appendChild(title);
        copy.appendChild(desc);
        b.appendChild(copy);
        b.addEventListener('mouseenter', function () { selectedIndex = index; update(); });
        return b;
    }
    function render() {
        var q = input.value.trim();
        current = matches(q);
        if (q) {
            current = current.concat([{ poz: true, url: '/pozlar?q=' + encodeURIComponent(q), title: '“' + q + '” pozlarda ara', desc: 'Resmî birim fiyat pozlarında poz no veya tanımla arar' }]);
        }
        selectedIndex = Math.min(selectedIndex, Math.max(0, current.length - 1));
        results.replaceChildren();
        if (q && current.length === 1) {
            var empty = document.createElement('p');
            empty.className = 'wk-search-empty';
            empty.textContent = 'Bu aramayla eşleşen araç yok.';
            results.appendChild(empty);
        }
        current.forEach(function (item, i) { results.appendChild(row(item, i)); });
        update();
    }
    function update() {
        var rows = results.querySelectorAll('.wk-search-result');
        rows.forEach(function (r, i) { r.setAttribute('aria-selected', i === selectedIndex ? 'true' : 'false'); });
        var sel = rows[selectedIndex];
        if (sel) { input.setAttribute('aria-activedescendant', sel.id); sel.scrollIntoView({ block: 'nearest' }); }
    }
    function openSearch() {
        setMenu(false);
        overlay.hidden = false;
        openButton.setAttribute('aria-expanded', 'true');
        document.body.style.overflow = 'hidden';
        input.value = '';
        selectedIndex = 0;
        render();
        setTimeout(function () { input.focus(); }, 10);
    }
    function closeSearch() {
        overlay.hidden = true;
        openButton.setAttribute('aria-expanded', 'false');
        document.body.style.overflow = '';
        openButton.focus();
    }
    openButton.addEventListener('click', openSearch);
    if (closeButton) closeButton.addEventListener('click', closeSearch);
    overlay.addEventListener('click', function (e) { if (e.target === overlay) closeSearch(); });
    input.addEventListener('input', function () { selectedIndex = 0; render(); });
    document.addEventListener('keydown', function (e) {
        var el = document.activeElement;
        var tag = el && el.tagName ? el.tagName.toUpperCase() : '';
        var typing = tag === 'INPUT' || tag === 'TEXTAREA' || tag === 'SELECT' || (el && el.isContentEditable);
        if (e.key === '/' && !typing && overlay.hidden) { e.preventDefault(); openSearch(); return; }
        if (e.key === 'Escape' && panel && !panel.hidden) { setMenu(false); menuBtn.focus(); return; }
        if (overlay.hidden) return;
        if (e.key === 'Escape') { e.preventDefault(); closeSearch(); return; }
        if (e.key === 'Tab') { // odak diyalogda kalsın
            var f = [input, closeButton].filter(Boolean);
            if (e.shiftKey && el === f[0]) { e.preventDefault(); f[f.length - 1].focus(); }
            else if (!e.shiftKey && el === f[f.length - 1]) { e.preventDefault(); f[0].focus(); }
            return;
        }
        if (!current.length) return;
        if (e.key === 'ArrowDown') { e.preventDefault(); selectedIndex = (selectedIndex + 1) % current.length; update(); }
        else if (e.key === 'ArrowUp') { e.preventDefault(); selectedIndex = (selectedIndex - 1 + current.length) % current.length; update(); }
        else if (e.key === 'Enter' && el === input) { e.preventDefault(); window.location.href = current[selectedIndex].url; }
    });
})();
</script>
</header>
<x-site.kainta-linker />
