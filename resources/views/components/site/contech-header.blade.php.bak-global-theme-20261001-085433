@php
    $wksToolConfig = config('worksitem_tools.tools', []);

    $wksSearchTools = [];

    foreach ($wksToolConfig as $slug => $tool) {

        if ($slug === 'havalandirma-kanali') {
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

    $wksOnTools =
        request()->is('araclar') ||
        request()->is('araclar/*');

    $wksOnCost =
        request()->is('maliyet-hesapla') ||
        request()->is('maliyet-hesapla/*');
@endphp

<header
    class="wks-header"
    data-wks-tool-count="{{ count($wksSearchTools) }}"
>
<style id="wks-header-v2-css">

.wks-header{
    --wks-bg:#fff;
    --wks-panel:#fff;
    --wks-panel-2:#f7f8fa;
    --wks-text:#111827;
    --wks-muted:#667085;
    --wks-line:#e5e7eb;
    --wks-yellow:#f5b400;
    --wks-yellow-hover:#dda300;

    position:relative;
    z-index:1000;
    background:var(--wks-bg);
    border-bottom:1px solid var(--wks-line);
    font-family:inherit;
}

.wks-header *{
    box-sizing:border-box;
}

html[data-theme="dark"] .wks-header{
    --wks-bg:#18212c;
    --wks-panel:#18212c;
    --wks-panel-2:#202c3a;
    --wks-text:#f3f6fa;
    --wks-muted:#b4c0ce;
    --wks-line:rgba(230,238,250,.18);
}

.wks-head-inner{
    width:min(calc(100% - 40px),1180px);
    min-height:68px;
    margin:0 auto;
    display:flex;
    align-items:center;
    gap:8px;
}

.wks-brand{
    display:flex;
    align-items:center;
    flex:0 0 auto;
    margin-right:auto;
}

.wks-brand img{
    display:block;
    height:36px;
    width:auto;
}

.wks-nav{
    display:flex;
    align-items:center;
    gap:4px;
}

.wks-nav a{
    min-height:40px;
    padding:0 13px;
    display:inline-flex;
    align-items:center;
    border-radius:10px;
    text-decoration:none;
    color:var(--wks-muted);
    font-size:13px;
    font-weight:800;
    white-space:nowrap;
    transition:
        background .16s ease,
        color .16s ease;
}

.wks-nav a:hover{
    background:var(--wks-panel-2);
    color:var(--wks-text);
}

.wks-nav a.wks-active{
    background:var(--wks-panel-2);
    color:var(--wks-text);
}

.wks-head-actions{
    display:flex;
    align-items:center;
    gap:8px;
    flex:0 0 auto;
}

.wks-search-open{
    min-height:42px;
    min-width:132px;
    padding:0 10px 0 13px;
    border:1px solid var(--wks-line);
    border-radius:11px;
    background:var(--wks-panel);
    color:var(--wks-muted);
    display:inline-flex;
    align-items:center;
    justify-content:space-between;
    gap:16px;
    cursor:pointer;
    font:inherit;
    transition:
        border-color .16s ease,
        background .16s ease,
        color .16s ease;
}

.wks-search-open:hover{
    border-color:#c5cbd3;
    background:var(--wks-panel-2);
    color:var(--wks-text);
}

html[data-theme="dark"] .wks-search-open:hover{
    border-color:rgba(255,255,255,.28);
}

.wks-search-open-main{
    display:inline-flex;
    align-items:center;
    gap:7px;
    font-size:13px;
    font-weight:800;
}

.wks-search-open-icon{
    font-size:16px;
    line-height:1;
}

.wks-search-key{
    min-width:25px;
    height:25px;
    padding:0 7px;
    display:inline-flex;
    align-items:center;
    justify-content:center;
    border:1px solid var(--wks-line);
    border-radius:7px;
    background:var(--wks-panel-2);
    color:var(--wks-muted);
    font-size:12px;
    font-weight:900;
}

.wks-head-actions .theme-toggle{
    flex:0 0 auto;
}

.wks-menu{
    display:none;
    width:42px;
    height:42px;
    padding:0;
    border:1px solid var(--wks-line);
    background:var(--wks-panel);
    color:var(--wks-text);
    border-radius:10px;
    font-size:20px;
    cursor:pointer;
}


/* =========================================================
   GLOBAL SEARCH
========================================================= */

.wks-search-overlay{
    position:fixed;
    inset:0;
    z-index:9999;
    display:none;
    align-items:flex-start;
    justify-content:center;
    padding:11vh 20px 30px;
    background:rgba(8,15,24,.54);
    backdrop-filter:blur(5px);
}

.wks-search-overlay.wks-open{
    display:flex;
}

.wks-search-dialog{
    width:min(680px,100%);
    max-height:min(690px,78vh);
    overflow:hidden;
    display:flex;
    flex-direction:column;
    background:var(--wks-panel);
    color:var(--wks-text);
    border:1px solid var(--wks-line);
    border-radius:18px;
    box-shadow:0 30px 90px rgba(0,0,0,.28);
}

.wks-search-top{
    padding:15px;
    display:flex;
    align-items:center;
    gap:10px;
    border-bottom:1px solid var(--wks-line);
}

.wks-search-input-wrap{
    min-height:50px;
    flex:1 1 auto;
    display:flex;
    align-items:center;
    gap:10px;
    padding:0 15px;
    border:1px solid var(--wks-line);
    border-radius:12px;
    background:var(--wks-panel-2);
}

.wks-search-input-wrap span{
    color:var(--wks-muted);
    font-size:18px;
}

#wksGlobalSearchInput{
    width:100%;
    border:0;
    outline:0;
    background:transparent;
    color:var(--wks-text);
    font:inherit;
    font-size:15px;
    font-weight:700;
}

#wksGlobalSearchInput::placeholder{
    color:var(--wks-muted);
    font-weight:600;
}

.wks-search-close{
    width:44px;
    height:44px;
    flex:0 0 44px;
    border:1px solid var(--wks-line);
    border-radius:11px;
    background:var(--wks-panel);
    color:var(--wks-text);
    cursor:pointer;
    font-size:20px;
}

.wks-search-meta{
    padding:10px 17px 4px;
    color:var(--wks-muted);
    font-size:11px;
    font-weight:800;
    text-transform:uppercase;
    letter-spacing:.06em;
}

.wks-search-results{
    padding:8px;
    overflow-y:auto;
    overscroll-behavior:contain;
}

.wks-search-result{
    width:100%;
    min-height:66px;
    padding:11px 13px;
    border:0;
    border-radius:11px;
    background:transparent;
    color:var(--wks-text);
    text-align:left;
    cursor:pointer;
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:16px;
    font:inherit;
}

.wks-search-result:hover,
.wks-search-result.wks-selected{
    background:var(--wks-panel-2);
}

.wks-search-result-copy{
    min-width:0;
}

.wks-search-result-title{
    display:block;
    color:var(--wks-text);
    font-size:14px;
    font-weight:850;
    line-height:1.3;
}

.wks-search-result-desc{
    display:block;
    margin-top:4px;
    color:var(--wks-muted);
    font-size:12px;
    line-height:1.4;
    white-space:nowrap;
    overflow:hidden;
    text-overflow:ellipsis;
}

.wks-search-result-arrow{
    flex:0 0 auto;
    color:var(--wks-muted);
    font-weight:900;
}

.wks-search-empty{
    padding:30px 18px 34px;
    color:var(--wks-muted);
    text-align:center;
    font-size:14px;
}

.wks-search-footer{
    padding:10px 15px;
    border-top:1px solid var(--wks-line);
    display:flex;
    gap:18px;
    flex-wrap:wrap;
    color:var(--wks-muted);
    font-size:11px;
    font-weight:700;
}

.wks-search-footer kbd{
    min-width:22px;
    height:22px;
    padding:0 5px;
    display:inline-flex;
    align-items:center;
    justify-content:center;
    margin-right:4px;
    border:1px solid var(--wks-line);
    border-radius:5px;
    background:var(--wks-panel-2);
    color:var(--wks-text);
    font:inherit;
    font-size:10px;
    font-weight:900;
}


/* =========================================================
   MOBILE
========================================================= */

@media(max-width:820px){

    .wks-head-inner{
        width:100%;
        min-height:64px;
        padding:0 12px;
        gap:7px;
    }

    .wks-brand{
        margin-right:auto;
    }

    .wks-brand img{
        height:33px;
    }

    .wks-nav{
        display:none;
        position:absolute;
        top:64px;
        left:12px;
        right:12px;
        padding:8px;
        background:var(--wks-panel);
        border:1px solid var(--wks-line);
        border-radius:14px;
        box-shadow:0 18px 45px rgba(0,0,0,.14);
        flex-direction:column;
        align-items:stretch;
    }

    .wks-nav.wks-open{
        display:flex;
    }

    .wks-nav a{
        min-height:44px;
        padding:0 13px;
    }

    .wks-menu{
        display:inline-flex;
        align-items:center;
        justify-content:center;
    }

    .wks-search-open{
        width:42px;
        height:42px;
        min-width:42px;
        min-height:42px;
        padding:0;
        justify-content:center;
    }

    .wks-search-open-main{
        font-size:0;
        gap:0;
    }

    .wks-search-open-icon{
        font-size:18px;
    }

    .wks-search-key{
        display:none;
    }

    .wks-search-overlay{
        padding:72px 10px 16px;
    }

    .wks-search-dialog{
        max-height:calc(100vh - 88px);
        border-radius:15px;
    }

    .wks-search-top{
        padding:10px;
    }

    .wks-search-result{
        min-height:64px;
    }

    .wks-search-footer{
        display:none;
    }

}

</style>


<div class="wks-head-inner">

    <a
        href="/"
        class="wks-brand"
        aria-label="WORKSİTEM ana sayfa"
    >
        <img
            src="/brand/worksitem-logo.png"
            alt="WORKSİTEM"
        >
    </a>


    <nav
        class="wks-nav"
        id="wksMainNav"
        aria-label="Ana navigasyon"
    >
        <a
            href="/araclar"
            @class(['wks-active' => $wksOnTools])
            @if($wksOnTools) aria-current="page" @endif
        >
            Araçlar
        </a>

        <a
            href="/maliyet-hesapla"
            @class(['wks-active' => $wksOnCost])
            @if($wksOnCost) aria-current="page" @endif
        >
            Maliyet
        </a>
    </nav>


    <div class="wks-head-actions">

        <button
            type="button"
            class="wks-search-open"
            id="wksGlobalSearchOpen"
            aria-haspopup="dialog"
            aria-controls="wksGlobalSearch"
            aria-expanded="false"
            title="Araç ara"
        >
            <span class="wks-search-open-main">
                <span
                    class="wks-search-open-icon"
                    aria-hidden="true"
                >⌕</span>

                <span>Ara</span>
            </span>

            <span
                class="wks-search-key"
                aria-hidden="true"
            >/</span>
        </button>


        @isset($actions)
            {{ $actions }}
        @endisset


        <button
            class="wks-menu"
            id="wksMenuButton"
            type="button"
            aria-label="Menüyü aç"
            aria-controls="wksMainNav"
            aria-expanded="false"
        >
            ☰
        </button>

    </div>

</div>


<div
    class="wks-search-overlay"
    id="wksGlobalSearch"
    role="dialog"
    aria-modal="true"
    aria-labelledby="wksGlobalSearchTitle"
    hidden
>
    <div class="wks-search-dialog">

        <div class="wks-search-top">

            <div class="wks-search-input-wrap">
                <span aria-hidden="true">⌕</span>

                <input
                    type="search"
                    id="wksGlobalSearchInput"
                    autocomplete="off"
                    placeholder="Beton, demir, boya, çatı..."
                    aria-label="WORKSİTEM araçlarında ara"
                >
            </div>

            <button
                type="button"
                class="wks-search-close"
                id="wksGlobalSearchClose"
                aria-label="Aramayı kapat"
            >
                ×
            </button>

        </div>


        <div
            class="wks-search-meta"
            id="wksGlobalSearchTitle"
        >
            74 araç içinde ara
        </div>


        <div
            class="wks-search-results"
            id="wksGlobalSearchResults"
            role="listbox"
            aria-label="Araç arama sonuçları"
        ></div>


        <div class="wks-search-footer">
            <span><kbd>↑</kbd><kbd>↓</kbd> Seç</span>
            <span><kbd>Enter</kbd> Aç</span>
            <span><kbd>Esc</kbd> Kapat</span>
        </div>

    </div>
</div>


<script id="wks-header-v2-js">
(function () {

    const tools = {!! json_encode(
        $wksSearchTools,
        JSON_UNESCAPED_UNICODE |
        JSON_UNESCAPED_SLASHES |
        JSON_HEX_TAG |
        JSON_HEX_AMP |
        JSON_HEX_APOS |
        JSON_HEX_QUOT
    ) !!};

    const openButton =
        document.getElementById(
            'wksGlobalSearchOpen'
        );

    const overlay =
        document.getElementById(
            'wksGlobalSearch'
        );

    const input =
        document.getElementById(
            'wksGlobalSearchInput'
        );

    const results =
        document.getElementById(
            'wksGlobalSearchResults'
        );

    const closeButton =
        document.getElementById(
            'wksGlobalSearchClose'
        );

    const menuButton =
        document.getElementById(
            'wksMenuButton'
        );

    const mainNav =
        document.getElementById(
            'wksMainNav'
        );

    if (
        !openButton ||
        !overlay ||
        !input ||
        !results
    ) {
        return;
    }


    let selectedIndex = 0;
    let currentResults = [];


    function normalize(value) {

        return String(value || '')
            .toLocaleLowerCase('tr-TR')
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .replace(/ı/g, 'i')
            .replace(/ğ/g, 'g')
            .replace(/ü/g, 'u')
            .replace(/ş/g, 's')
            .replace(/ö/g, 'o')
            .replace(/ç/g, 'c')
            .trim();

    }


    function getMatches(query) {

        const normalizedQuery =
            normalize(query);

        if (!normalizedQuery) {

            return tools.slice(0, 8);

        }

        return tools
            .map(function (tool) {

                const title =
                    normalize(tool.title);

                const desc =
                    normalize(tool.desc);

                let score = 0;

                if (title === normalizedQuery) {
                    score += 100;
                }

                if (title.startsWith(normalizedQuery)) {
                    score += 60;
                }

                if (title.includes(normalizedQuery)) {
                    score += 35;
                }

                if (desc.includes(normalizedQuery)) {
                    score += 10;
                }

                return {
                    tool: tool,
                    score: score
                };

            })
            .filter(function (item) {
                return item.score > 0;
            })
            .sort(function (a, b) {

                if (b.score !== a.score) {
                    return b.score - a.score;
                }

                return a.tool.title.localeCompare(
                    b.tool.title,
                    'tr-TR'
                );

            })
            .slice(0, 8)
            .map(function (item) {
                return item.tool;
            });

    }


    function render() {

        currentResults =
            getMatches(input.value);

        selectedIndex = Math.min(
            selectedIndex,
            Math.max(
                0,
                currentResults.length - 1
            )
        );

        results.replaceChildren();

        if (!currentResults.length) {

            const empty =
                document.createElement('div');

            empty.className =
                'wks-search-empty';

            empty.textContent =
                'Bu aramayla eşleşen araç bulunamadı.';

            results.appendChild(empty);

            return;
        }


        currentResults.forEach(
            function (tool, index) {

                const button =
                    document.createElement(
                        'button'
                    );

                button.type = 'button';

                button.className =
                    'wks-search-result' +
                    (
                        index === selectedIndex
                            ? ' wks-selected'
                            : ''
                    );

                button.setAttribute(
                    'role',
                    'option'
                );

                button.setAttribute(
                    'aria-selected',
                    index === selectedIndex
                        ? 'true'
                        : 'false'
                );


                const copy =
                    document.createElement(
                        'span'
                    );

                copy.className =
                    'wks-search-result-copy';


                const title =
                    document.createElement(
                        'span'
                    );

                title.className =
                    'wks-search-result-title';

                title.textContent =
                    tool.title;


                const desc =
                    document.createElement(
                        'span'
                    );

                desc.className =
                    'wks-search-result-desc';

                desc.textContent =
                    tool.desc;


                const arrow =
                    document.createElement(
                        'span'
                    );

                arrow.className =
                    'wks-search-result-arrow';

                arrow.setAttribute(
                    'aria-hidden',
                    'true'
                );

                arrow.textContent = '→';


                copy.appendChild(title);
                copy.appendChild(desc);

                button.appendChild(copy);
                button.appendChild(arrow);


                button.addEventListener(
                    'mouseenter',
                    function () {

                        selectedIndex =
                            index;

                        updateSelection();

                    }
                );


                button.addEventListener(
                    'click',
                    function () {

                        window.location.href =
                            tool.url;

                    }
                );


                results.appendChild(
                    button
                );

            }
        );

    }


    function updateSelection() {

        const buttons =
            results.querySelectorAll(
                '.wks-search-result'
            );

        buttons.forEach(
            function (button, index) {

                const selected =
                    index === selectedIndex;

                button.classList.toggle(
                    'wks-selected',
                    selected
                );

                button.setAttribute(
                    'aria-selected',
                    selected
                        ? 'true'
                        : 'false'
                );

            }
        );

        const selected =
            buttons[selectedIndex];

        if (selected) {

            selected.scrollIntoView({
                block: 'nearest'
            });

        }

    }


    function openSearch() {

        overlay.hidden = false;

        requestAnimationFrame(
            function () {
                overlay.classList.add(
                    'wks-open'
                );
            }
        );

        openButton.setAttribute(
            'aria-expanded',
            'true'
        );

        document.body.style.overflow =
            'hidden';

        input.value = '';
        selectedIndex = 0;

        render();

        window.setTimeout(
            function () {
                input.focus();
            },
            10
        );

    }


    function closeSearch() {

        overlay.classList.remove(
            'wks-open'
        );

        overlay.hidden = true;

        openButton.setAttribute(
            'aria-expanded',
            'false'
        );

        document.body.style.overflow =
            '';

        openButton.focus();

    }


    function openSelected() {

        const selected =
            currentResults[selectedIndex];

        if (selected) {

            window.location.href =
                selected.url;

        }

    }


    openButton.addEventListener(
        'click',
        openSearch
    );


    closeButton?.addEventListener(
        'click',
        closeSearch
    );


    overlay.addEventListener(
        'click',
        function (event) {

            if (event.target === overlay) {
                closeSearch();
            }

        }
    );


    input.addEventListener(
        'input',
        function () {

            selectedIndex = 0;
            render();

        }
    );


    document.addEventListener(
        'keydown',
        function (event) {

            const tag =
                document.activeElement
                    ?.tagName
                    ?.toUpperCase();

            const typing =
                tag === 'INPUT' ||
                tag === 'TEXTAREA' ||
                tag === 'SELECT' ||
                document.activeElement
                    ?.isContentEditable;


            if (
                event.key === '/' &&
                !typing &&
                overlay.hidden
            ) {

                event.preventDefault();
                openSearch();
                return;

            }


            if (overlay.hidden) {
                return;
            }


            if (event.key === 'Escape') {

                event.preventDefault();
                closeSearch();
                return;

            }


            if (
                event.key === 'ArrowDown' &&
                currentResults.length
            ) {

                event.preventDefault();

                selectedIndex =
                    (
                        selectedIndex + 1
                    ) %
                    currentResults.length;

                updateSelection();
                return;

            }


            if (
                event.key === 'ArrowUp' &&
                currentResults.length
            ) {

                event.preventDefault();

                selectedIndex =
                    (
                        selectedIndex - 1 +
                        currentResults.length
                    ) %
                    currentResults.length;

                updateSelection();
                return;

            }


            if (
                event.key === 'Enter' &&
                currentResults.length
            ) {

                event.preventDefault();
                openSelected();

            }

        }
    );


    if (
        menuButton &&
        mainNav
    ) {

        menuButton.addEventListener(
            'click',
            function () {

                const open =
                    mainNav.classList.toggle(
                        'wks-open'
                    );

                menuButton.setAttribute(
                    'aria-expanded',
                    String(open)
                );

                menuButton.setAttribute(
                    'aria-label',
                    open
                        ? 'Menüyü kapat'
                        : 'Menüyü aç'
                );

            }
        );


        mainNav.addEventListener(
            'click',
            function (event) {

                if (
                    event.target.closest('a')
                ) {

                    mainNav.classList.remove(
                        'wks-open'
                    );

                    menuButton.setAttribute(
                        'aria-expanded',
                        'false'
                    );

                }

            }
        );

    }

})();
</script>

</header>

<x-site.kainta-linker />
