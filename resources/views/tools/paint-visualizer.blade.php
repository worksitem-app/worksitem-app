<!DOCTYPE html>
<html lang="tr">
<head>
    <link rel="icon" href="/favicon.ico" type="image/x-icon">

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Duvar Rengi Deneme: Boya Rengini Odanızın Fotoğrafında Görün</title>
    <script type="application/ld+json">{!! json_encode(['@context' => 'https://schema.org', '@type' => 'WebApplication', 'name' => 'Duvar Rengi Görselleştirici', 'url' => 'https://worksitem.com/araclar/duvar-rengi-gorsellestirici', 'applicationCategory' => 'DesignApplication', 'operatingSystem' => 'Web', 'inLanguage' => 'tr-TR', 'offers' => ['@type' => 'Offer', 'price' => '0', 'priceCurrency' => 'TRY'], 'publisher' => ['@type' => 'Organization', 'name' => 'Worksitem', 'url' => 'https://worksitem.com']], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG) !!}</script>
    <meta name="description" content="Doğrulanmış renk kataloglarındaki renkleri inceleyin ve seçtiğiniz tonları oda veya cephe fotoğrafınız üzerinde yaklaşık olarak görselleştirin.">
    <link rel="canonical" href="https://worksitem.com/araclar/duvar-rengi-gorsellestirici">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #f8fafc;
            --surface: #ffffff;
            --border: #cbd5e1;
            --primary: #101318;
            --primary-hover: #101318;
            --text-main: #0f172a;
            --text-muted: #475569;
            --pill-bg: #f1f5f9;
            --pill-text: #334155;
            --pill-active-bg: #101318;
            --pill-active-text: #ffffff;
            --green: #059669;
            --amber: #d97706;
            --red: #dc2626;
            --radius-full: 9999px;
            --transition: all 0.2s ease;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Inter', sans-serif; background: var(--bg); color: var(--text-main); line-height: 1.5; padding-bottom: 80px; }

        .navbar { display: flex; justify-content: space-between; align-items: center; padding: 1rem 5%; background: var(--surface); border-bottom: 1px solid var(--border); position: sticky; top: 0; z-index: 50; }
        .logo-wrapper { display: flex; align-items: center; gap: 8px; text-decoration: none; }
        .logo-mark { background: var(--primary); color: #fff; font-weight: 800; font-size: 14px; padding: 4px 10px; border-radius: 6px; }
        .logo-text { font-weight: 800; font-size: 20px; color: var(--text-main); }

        .container { max-width: 1200px; margin: 30px auto; padding: 0 20px; }

        .page-header { text-align: center; margin-bottom: 30px; }
        .page-header h1 { font-size: 2.25rem; font-weight: 800; color: var(--text-main); letter-spacing: -0.5px; margin-bottom: 8px; }
        .page-header p { color: var(--text-muted); font-size: 1rem; max-width: 750px; margin: 0 auto; }

        .quick-action-btn { display: inline-flex; align-items: center; gap: 8px; background: #fff8e1; color: var(--primary); border: 1px solid #fde68a; font-weight: 700; padding: 10px 22px; border-radius: var(--radius-full); margin-top: 15px; cursor: pointer; transition: var(--transition); }
        .quick-action-btn:hover { background: #fff3c4; }

        /* Filter Section */
        .filter-section { background: var(--surface); border: 1px solid var(--border); border-radius: 16px; padding: 25px; margin-bottom: 25px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); }
        
        .filter-row { display: flex; align-items: center; gap: 12px; flex-wrap: wrap; margin-bottom: 18px; }
        .filter-label { font-size: 0.875rem; font-weight: 800; color: var(--text-muted); min-width: 110px; text-transform: uppercase; letter-spacing: 0.5px; }

        .pill-group { display: flex; gap: 8px; flex-wrap: wrap; flex: 1; }
        .pill { background: var(--pill-bg); color: var(--pill-text); border: 1px solid transparent; font-size: 0.85rem; font-weight: 600; padding: 8px 16px; border-radius: var(--radius-full); cursor: pointer; transition: var(--transition); user-select: none; }
        .pill:hover { background: #e2e8f0; }
        .pill.active { background: var(--pill-active-bg); color: var(--pill-active-text); font-weight: 700; }

        .search-box { position: relative; margin-bottom: 20px; }
        .search-input { width: 100%; padding: 12px 20px 12px 42px; border-radius: var(--radius-full); border: 1px solid var(--border); background: var(--bg); font-family: inherit; font-size: 0.95rem; font-weight: 500; }
        .search-input:focus { outline: none; border-color: var(--primary); background: #fff; }
        .search-icon { position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: var(--text-muted); }

        /* Swatches Grid */
        .swatch-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(130px, 1fr)); gap: 12px; margin-bottom: 10px; max-height: 360px; overflow-y: auto; padding-right: 5px; }
        .swatch-card { background: #fff; border: 2px solid var(--border); border-radius: 10px; padding: 8px; cursor: pointer; transition: var(--transition); text-align: center; }
        .swatch-card:hover { transform: translateY(-2px); border-color: #94a3b8; }
        .swatch-card.active { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(255,184,0,.35); }
        .swatch-color { height: 55px; border-radius: 6px; margin-bottom: 6px; border: 1px solid rgba(0,0,0,0.05); }
        .swatch-brand { font-size: 0.65rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase; }
        .swatch-title { font-size: 0.75rem; font-weight: 700; color: var(--text-main); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }

        /* Selected Banner */
        .selected-banner { display: flex; align-items: center; justify-content: space-between; background: var(--surface); border: 1px solid var(--border); border-radius: 12px; padding: 16px 20px; margin-bottom: 30px; }
        .selected-info { display: flex; align-items: center; gap: 16px; }
        .selected-preview { width: 50px; height: 50px; border-radius: 8px; border: 1px solid var(--border); }
        .selected-details { display: flex; flex-direction: column; }
        .selected-brand-tag { font-size: 0.7rem; font-weight: 800; color: var(--primary); text-transform: uppercase; letter-spacing: 0.5px; }
        .selected-name { font-size: 1.1rem; font-weight: 800; color: var(--text-main); }
        .selected-hex { font-size: 0.85rem; font-weight: 600; color: var(--text-muted); font-family: monospace; }
        .icon-btn { background: var(--bg); border: 1px solid var(--border); width: 40px; height: 40px; border-radius: 8px; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: var(--transition); font-weight: 700; }
        .icon-btn:hover { background: #e2e8f0; }

        /* Visualizer Box */
        .visualizer-card { background: var(--surface); border: 1px solid var(--border); border-radius: 16px; padding: 25px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); }
        .visualizer-title { font-size: 1.25rem; font-weight: 800; margin-bottom: 15px; display: flex; align-items: center; gap: 10px; }

        /* Action Toolbar */
        .toolbar { display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 20px; }
        .tool-btn { display: inline-flex; align-items: center; gap: 6px; padding: 10px 18px; border-radius: var(--radius-full); font-size: 0.85rem; font-weight: 700; cursor: pointer; border: 1px solid var(--border); background: var(--surface); color: var(--text-main); transition: var(--transition); }
        .tool-btn:hover { background: var(--bg); }
        .tool-btn.active { background: #fff8e1; border-color: var(--primary); color: var(--primary); }
        .tool-btn.btn-primary { background: var(--primary); color: #fff; border-color: var(--primary); }
        .tool-btn.btn-primary:hover { background: var(--primary-hover); }
        .tool-btn.btn-success { background:#101318; color:#fff; border-color: var(--green); }
        .tool-btn.btn-amber { background: var(--amber); color: #fff; border-color: var(--amber); }
        .tool-btn.btn-danger { background: var(--red); color: #fff; border-color: var(--red); }
        .badge-ai { background: #101318; color: #fff; font-size: 0.65rem; padding: 2px 6px; border-radius: 4px; text-transform: uppercase; margin-left: 4px; }

        /* Settings Bar */
        .settings-bar { display: flex; align-items: center; gap: 20px; flex-wrap: wrap; background: var(--bg); padding: 12px 20px; border-radius: 10px; margin-bottom: 20px; font-size: 0.85rem; font-weight: 700; }
        .setting-item { display: flex; align-items: center; gap: 10px; }
        .range-slider { accent-color: var(--primary); cursor: pointer; }

        /* Canvas Wrapper */
        .editor-container { position: relative; width: 100%; border-radius: 10px; overflow: hidden; background: #e2e8f0; min-height: 420px; display: flex; align-items: center; justify-content: center; }
        #canvasWrapper { position: relative; display: inline-block; max-width: 100%; }
        #mainCanvas { display: block; max-width: 100%; height: auto; }
        #maskCanvas { position: absolute; top: 0; left: 0; width: 100%; height: 100%; pointer-events: none; mix-blend-mode: multiply; opacity: 0.75; }

        .drop-placeholder { text-align: center; padding: 60px 20px; color: var(--text-muted); cursor: pointer; }
        .drop-placeholder h3 { font-size: 1.1rem; font-weight: 800; color: var(--text-main); margin: 10px 0 5px 0; }

        /* Lead CTA Banner */
        .lead-banner { background: #fff8e1; border: 1px solid #fde68a; border-radius: 12px; padding: 20px; margin-top: 25px; display: flex; align-items: center; justify-content: space-between; gap: 20px; flex-wrap: wrap; }
        .lead-text h4 { font-size: 1.05rem; font-weight: 800; color: #101318; margin-bottom: 4px; }
        .lead-text p { font-size: 0.85rem; color: #101318; }
        .lead-btn { background:#101318; color:#fff; font-weight: 800; padding: 12px 24px; border-radius: 8px; text-decoration: none; transition: var(--transition); }
        .lead-btn:hover { background: #047857; }
    </style>
    <meta property="og:image" content="https://worksitem.com/brand/og-image.png">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:image" content="https://worksitem.com/brand/og-image.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="manifest" href="/site.webmanifest">
    <meta name="theme-color" content="#ffffff">
    <meta name="csrf-token" content="{{ csrf_token() }}">

<style id="paint-catalog-db-ui">
/* hidden özniteliği display kurallarıyla ezilmesin (boş sponsor rozeti, '#' giden bağlantılar) */
.sponsor-badge[hidden],.catalog-actions[hidden],.catalog-action-link[hidden]{display:none!important}
.sponsor-inline{
    margin-left:5px;
    font-size:10px;
    font-weight:800;
    opacity:.75;
}

.sponsor-badge{
    display:inline-flex;
    width:max-content;
    margin-top:4px;
    padding:4px 8px;
    border-radius:999px;
    background:#fff4d6;
    color:#8a5a00;
    font-size:10px;
    font-weight:800;
}

.catalog-empty{
    grid-column:1/-1;
    padding:28px 18px;
    border:1px dashed var(--border);
    border-radius:12px;
    background:var(--bg);
    text-align:center;
    color:var(--text-muted);
    line-height:1.6;
}

.catalog-actions{
    display:flex;
    flex-wrap:wrap;
    gap:10px;
    margin:-16px 0 24px;
}

.catalog-action-link{
    display:inline-flex;
    align-items:center;
    min-height:42px;
    padding:0 14px;
    border:1px solid var(--border);
    border-radius:10px;
    background:var(--surface);
    color:var(--text-main);
    font-size:13px;
    font-weight:800;
    text-decoration:none;
}

.catalog-action-link:hover{
    border-color:var(--primary);
}
</style>

<style id="visualizer-safe-mvp-v2">
.visualizer-safety-note{
    margin:0 0 14px;
    padding:12px 14px;
    border:1px solid #fde68a;
    border-radius:10px;
    background:#fffbeb;
    color:#78350f;
    font-size:13px;
    line-height:1.55;
}
[data-theme="dark"] .visualizer-safety-note{
    background:rgba(120,53,15,.18);
    border-color:rgba(253,230,138,.25);
    color:#fde68a;
}
</style>

<style id="visualizer-hidden-fix">
#smartToleranceSetting[hidden]{
    display:none !important;
}
</style>

@if(request()->boolean('embed'))
<style id="visualizer-embed-mode">
body{
    padding-bottom:0 !important;
}

.container{
    max-width:none !important;
    margin:0 auto !important;
    padding:14px !important;
}

.page-header{
    display:none !important;
}

.lead-banner{
    display:none !important;
}
</style>
@endif
</head>
<body data-tool-slug="paint-visualizer">

    @unless(request()->boolean('embed'))
        <x-site.contech-header />
    @endunless

    <div class="container">
        
        <div class="page-header">
            <h1>Duvar Rengi Görselleştirici</h1>
            <p>Doğrulanmış renk kataloglarını inceleyin ve seçtiğiniz tonları oda veya cephe fotoğrafınız üzerinde yaklaşık olarak önizleyin.</p>
            <button class="quick-action-btn" onclick="scrollToVisualizer()">
                📷 Fotoğraf Görselleştirme ▾
            </button>
        </div>

        <!-- FILTERS & SWATCHES -->
        <div class="filter-section">
            
            <!-- Brand Selection -->
            <div class="filter-row">
                <span class="filter-label">Üretici / Standart:</span>
                <div class="pill-group" id="brandPills">
    <span
        class="pill active"
        data-brand="Tüm"
        onclick="setBrand(this.dataset.brand, this)"
    >
        Tüm Renkler
    </span>

    @foreach(($paintColorBrands ?? collect()) as $brand)
        <span
            class="pill"
            data-brand="{{ $brand->name }}"
            onclick="setBrand(this.dataset.brand, this)"
        >
            {{ $brand->name }}
            @if($brand->is_sponsored)
                <small class="sponsor-inline">✦ Sponsorlu</small>
            @endif
        </span>
    @endforeach
</div>
            </div>

            <!-- Category Selection -->
            <div class="filter-row">
                <span class="filter-label">Renk Grubu:</span>
                <div class="pill-group" id="categoryPills">
    <span
        class="pill active"
        data-category="Tüm"
        onclick="setCategory(this.dataset.category, this)"
    >
        Tüm Renkler
    </span>

    @foreach(
        ($paintColorCatalog ?? collect())
            ->pluck('category')
            ->filter()
            ->unique()
            ->values()
        as $category
    )
        <span
            class="pill"
            data-category="{{ $category }}"
            onclick="setCategory(this.dataset.category, this)"
        >
            {{ $category }}
        </span>
    @endforeach
</div>
            </div>

            <!-- Search Bar -->
            <div class="search-box">
                <span class="search-icon">🔍</span>
                <input type="text" id="searchInput" class="search-input" placeholder="Renk adı veya kodu ara..." oninput="renderSwatches()">
            </div>

            <!-- Color Swatches Grid -->
            <div class="swatch-grid" id="swatchGrid">
                <!-- Dynamic Swatches -->
            </div>

        </div>

        <!-- SELECTED COLOR BANNER -->
        <div class="selected-banner" id="selectedBanner" hidden>
            <div class="selected-info">
                <div class="selected-preview" id="bannerPreview" style="background-color: #383E42;"></div>
                <div class="selected-details">
                    <span class="selected-brand-tag" id="bannerBrand">RENK KATALOĞU</span><span class="sponsor-badge" id="bannerSponsor" hidden></span>
                    <span class="selected-name" id="bannerName">Renk seçin</span>
                    <span class="selected-hex" id="bannerHex">—</span>
                </div>
            </div>
            <button class="icon-btn" title="Tam Ekran Görüntüle" onclick="toggleFullScreenColor()">
                ⛶
            </button>
        </div>

        
<div class="catalog-actions" id="catalogActions" hidden>
    <a
        href="#"
        id="catalogProductLink"
        class="catalog-action-link"
        target="_blank"
        rel="noopener noreferrer"
        hidden
    >
        Ürünü incele →
    </a>

    <a
        href="#"
        id="catalogDealerLink"
        class="catalog-action-link"
        target="_blank"
        rel="noopener noreferrer"
        hidden
    >
        Bayi bul →
    </a>
</div>
<!-- VISUALIZER CARD -->
        <div class="visualizer-card" id="visualizerSection">
            <h2 class="visualizer-title">
                <span>Fotoğraf Üzerinde Görselleştirme</span>
            </h2>

            <!-- TOOLBAR -->
            <div class="toolbar">
                <button class="tool-btn btn-primary" onclick="triggerUpload()">
                    📤 Fotoğraf Yükle
                </button>
                <button class="tool-btn" id="btnModeAI" onclick="setMode('ai')">
                    🧪 Akıllı Alan Seçimi <span class="badge-ai">Deneysel</span>
                </button>
                <button class="tool-btn" id="btnModeRect" onclick="setMode('rect')">
                    ⬛ Dikdörtgen
                </button>
                <button class="tool-btn active" id="btnModeBrush" onclick="setMode('brush')">
                    🖌️ Fırça
                </button>
                <button class="tool-btn" id="btnModeEraser" onclick="setMode('eraser')">
                    🧹 Silgi
                </button>
                <button class="tool-btn btn-amber" onclick="undoCanvas()">
                    ↩ Geri Al
                </button>
                <button class="tool-btn btn-danger" onclick="resetCanvas()">
                    🔄 Sıfırla
                </button>
                <button class="tool-btn btn-success" onclick="saveResult()">
                    📥 Görseli İndir
                </button>
            </div>

            <div class="visualizer-safety-note">
                <strong>Önerilen yöntem:</strong>
                Boyanacak yüzeyi Fırça ile işaretleyin ve Silgi ile kenarları düzeltin.
                Akıllı Alan Seçimi deneysel bir yardımcıdır; bazı fotoğraflarda
                tavan, zemin veya diğer benzer yüzeylere taşabilir.
            </div>
            <!-- SETTINGS BAR -->
            <div class="settings-bar">
                <div class="setting-item" id="smartToleranceSetting" hidden>
                    <span>Hassasiyet (Tolerans):</span>
                    <input type="range" id="toleranceSlider" class="range-slider" min="1" max="20" value="6" onchange="document.getElementById('toleranceVal').innerText = this.value">
                    <span id="toleranceVal">6</span>
                </div>
                <div class="setting-item">
                    <span>Fırça Boyutu:</span>
                    <input
                        type="range"
                        id="brushSizeSlider"
                        class="range-slider"
                        min="8"
                        max="80"
                        value="28"
                        oninput="document.getElementById('brushSizeVal').innerText = this.value"
                    >
                    <span id="brushSizeVal">28</span>
                </div>

                <div class="setting-item">
                    <span>Renk Yoğunluğu:</span>
                    <div class="pill-group">
                        <span class="pill" onclick="setIntensity(0.5, this)">Hafif</span>
                        <span class="pill active" onclick="setIntensity(0.75, this)">Standart</span>
                        <span class="pill" onclick="setIntensity(0.95, this)">Yoğun</span>
                    </div>
                </div>
            </div>

            <!-- CANVAS EDITOR -->
            <div class="editor-container" id="editorContainer">
                <div class="drop-placeholder" id="dropPlaceholder" onclick="triggerUpload()">
                    <div style="font-size: 3rem; margin-bottom: 10px;">📸</div>
                    <h3>Oda veya cephe fotoğrafınızı sürükleyip bırakın</h3>
                    <p>veya dosya seçmek için tıklayın.</p>
                </div>
                <div id="canvasWrapper" style="display: none;">
                    <canvas id="mainCanvas"></canvas>
                    <canvas id="maskCanvas"></canvas>
                </div>
            </div>

            <!-- LEAD CONVERSION BANNER -->
            <div class="lead-banner" id="leadBanner" style="display: none;">
                <div class="lead-text">
                    <h4>Bu Rengi Odanızda Çok Beğendiniz Mi?</h4>
                    <p>Seçtiğiniz bu tonun malzeme ihtiyacını ve usta işçilik bütçesini güncel piyasa ve saha referanslarıyla hesaplayın.</p>
                </div>
                <a href="/araclar/boya-hesaplayici" class="lead-btn">Boya Maliyetini Hesapla →</a>
            </div>

        </div>

    </div>

    <input type="file" id="imageUploader" accept="image/*" style="display: none;" onchange="handleImageUpload(event)">

    <script>
        // DB tabanlı, kullanım hakkı doğrulanmış renk kataloğu
        const COLOR_DATABASE = @json($paintColorCatalog ?? collect());
let currentBrand = 'Tüm';
        let currentCategory = 'Tüm';
        let selectedColor = COLOR_DATABASE.length ? COLOR_DATABASE[0] : null;

        // Canvas Elements
        let mainCanvas = document.getElementById('mainCanvas');
        let maskCanvas = document.getElementById('maskCanvas');
        let ctxMain = mainCanvas.getContext('2d');
        let ctxMask = maskCanvas.getContext('2d');
        let loadedImg = null;

        let activeTool = 'brush';
        let currentOpacity = 0.75;
        let isDrawing = false;
        let startX = 0, startY = 0;
        let lastX = 0, lastY = 0;
        let undoStack = [];

        document.addEventListener('DOMContentLoaded', () => {
            renderSwatches();

            if (selectedColor) {
                selectColor(selectedColor);
            }

            setupDragAndDrop();
        });

        function setBrand(brand, el) {
            currentBrand = brand;
            document.querySelectorAll('#brandPills .pill').forEach(p => p.classList.remove('active'));
            el.classList.add('active');
            renderSwatches();
        }

        function setCategory(cat, el) {
            currentCategory = cat;
            document.querySelectorAll('#categoryPills .pill').forEach(p => p.classList.remove('active'));
            el.classList.add('active');
            renderSwatches();
        }

        function normalizeHex(value) {
            const hex = String(value || '').trim();

            return /^#[0-9a-fA-F]{6}$/.test(hex)
                ? hex.toUpperCase()
                : null;
        }

        function safeExternalUrl(value) {
            if (!value) return null;

            try {
                const url = new URL(value, window.location.origin);

                return ['http:', 'https:'].includes(url.protocol)
                    ? url.href
                    : null;
            } catch (e) {
                return null;
            }
        }

        function renderSwatches() {
            const input = document.getElementById('searchInput');
            const query = (input?.value || '').toLowerCase();
            const grid = document.getElementById('swatchGrid');

            grid.innerHTML = '';

            if (!COLOR_DATABASE.length) {
                const empty = document.createElement('div');

                empty.className = 'catalog-empty';
                empty.textContent =
                    'Doğrulanmış renk kataloğu henüz eklenmedi. ' +
                    'Yeni kataloglar kullanım hakkı doğrulandıktan sonra yayınlanacaktır.';

                grid.appendChild(empty);
                return;
            }

            const filtered = COLOR_DATABASE.filter(c => {
                const name = String(c.name || '').toLowerCase();
                const brand = String(c.brand || '').toLowerCase();
                const code = String(c.code || '').toLowerCase();
                const hex = String(c.hex || '').toLowerCase();

                const matchBrand =
                    currentBrand === 'Tüm' ||
                    c.brand === currentBrand;

                const matchCat =
                    currentCategory === 'Tüm' ||
                    c.category === currentCategory;

                const matchSearch =
                    !query ||
                    name.includes(query) ||
                    brand.includes(query) ||
                    code.includes(query) ||
                    hex.includes(query);

                return matchBrand && matchCat && matchSearch;
            });

            if (!filtered.length) {
                const empty = document.createElement('div');

                empty.className = 'catalog-empty';
                empty.textContent = 'Bu filtreye uygun renk bulunamadı.';

                grid.appendChild(empty);
                return;
            }

            filtered.forEach(c => {
                const hex = normalizeHex(c.hex);

                if (!hex) return;

                const card = document.createElement('div');
                const colorBox = document.createElement('div');
                const brand = document.createElement('div');
                const title = document.createElement('div');

                card.className =
                    'swatch-card ' +
                    (
                        selectedColor &&
                        normalizeHex(selectedColor.hex) === hex
                            ? 'active'
                            : ''
                    );

                card.onclick = () => selectColor(c);

                colorBox.className = 'swatch-color';
                colorBox.style.backgroundColor = hex;

                brand.className = 'swatch-brand';
                brand.textContent =
                    String(c.brand || 'Renk kataloğu') +
                    (Number(c.is_sponsored) === 1 ? ' · Sponsorlu' : '');

                title.className = 'swatch-title';
                title.textContent =
                    [c.code, c.name]
                        .filter(Boolean)
                        .join(' · ');

                card.appendChild(colorBox);
                card.appendChild(brand);
                card.appendChild(title);

                grid.appendChild(card);
            });
        }

        function selectColor(color) {
            if (!color) return;

            const hex = normalizeHex(color.hex);

            if (!hex) return;

            selectedColor = {
                ...color,
                hex: hex
            };

            document.getElementById('selectedBanner').hidden = false;

            document.getElementById('bannerPreview').style.backgroundColor = hex;
            document.getElementById('bannerBrand').innerText =
                color.brand || 'Renk kataloğu';

            document.getElementById('bannerName').innerText =
                [color.code, color.name]
                    .filter(Boolean)
                    .join(' · ');

            document.getElementById('bannerHex').innerText = hex;

            const sponsor = document.getElementById('bannerSponsor');

            if (Number(color.is_sponsored) === 1) {
                sponsor.hidden = false;
                sponsor.innerText =
                    color.sponsor_label || 'Sponsorlu katalog';
            } else {
                sponsor.hidden = true;
                sponsor.innerText = '';
            }

            const actions = document.getElementById('catalogActions');
            const product = document.getElementById('catalogProductLink');
            const dealer = document.getElementById('catalogDealerLink');

            const productUrl = safeExternalUrl(
                color.product_url ||
                color.brand_product_url
            );

            const dealerUrl = safeExternalUrl(
                color.dealer_url ||
                color.brand_dealer_url
            );

            product.hidden = !productUrl;
            dealer.hidden = !dealerUrl;

            if (productUrl) {
                product.href = productUrl;
            }

            if (dealerUrl) {
                dealer.href = dealerUrl;
            }

            const rel = Number(color.is_sponsored) === 1
                ? 'noopener noreferrer sponsored'
                : 'noopener noreferrer';

            product.rel = rel;
            dealer.rel = rel;

            actions.hidden = !(productUrl || dealerUrl);

            renderSwatches();
        }

        function toggleFullScreenColor() {
            if (!selectedColor) return;

            const div = document.createElement('div');
            div.style.position = 'fixed';
            div.style.top = '0'; div.style.left = '0'; div.style.width = '100vw'; div.style.height = '100vh';
            div.style.backgroundColor = selectedColor.hex;
            div.style.zIndex = '99999';
            div.style.display = 'flex'; div.style.alignItems = 'center'; div.style.justifyContent = 'center';
            div.style.color = getContrastYIQ(selectedColor.hex);
            div.style.fontFamily = 'sans-serif'; div.style.fontSize = '2rem'; div.style.fontWeight = '800';
            div.innerText = `${selectedColor.brand} - ${selectedColor.name} (${selectedColor.hex})\nKapatmak için tıklayın`;
            div.onclick = () => div.remove();
            document.body.appendChild(div);
        }

        function getContrastYIQ(hexcolor){
            hexcolor = hexcolor.replace("#", "");
            var r = parseInt(hexcolor.substr(0,2),16);
            var g = parseInt(hexcolor.substr(2,2),16);
            var b = parseInt(hexcolor.substr(4,2),16);
            var yiq = ((r*299)+(g*587)+(b*114))/1000;
            return (yiq >= 128) ? '#0f172a' : '#ffffff';
        }

        function scrollToVisualizer() {
            document.getElementById('visualizerSection').scrollIntoView({ behavior: 'smooth' });
        }

        // Image Canvas & Controls
        function triggerUpload() { document.getElementById('imageUploader').click(); }

        function handleImageUpload(e) {
            const file = e.target.files[0];
            if(!file) return;
            const reader = new FileReader();
            reader.onload = (event) => {
                loadedImg = new Image();
                loadedImg.onload = () => { initCanvas(); };
                loadedImg.src = event.target.result;
            };
            reader.readAsDataURL(file);
        }

        function setupDragAndDrop() {
            const container = document.getElementById('editorContainer');
            container.addEventListener('dragover', (e) => { e.preventDefault(); });
            container.addEventListener('drop', (e) => {
                e.preventDefault();
                if (e.dataTransfer.files && e.dataTransfer.files[0]) {
                    handleImageUpload({ target: { files: e.dataTransfer.files } });
                }
            });
        }

        function initCanvas() {
            document.getElementById('dropPlaceholder').style.display = 'none';
            document.getElementById('canvasWrapper').style.display = 'inline-block';
            document.getElementById('leadBanner').style.display = 'flex';

            const maxW = document.getElementById('editorContainer').clientWidth;
            let w = loadedImg.width;
            let h = loadedImg.height;

            if (w > maxW) {
                h = Math.round((maxW / w) * h);
                w = maxW;
            }

            mainCanvas.width = w;
            mainCanvas.height = h;
            maskCanvas.width = w;
            maskCanvas.height = h;

            ctxMain.drawImage(loadedImg, 0, 0, w, h);
            ctxMask.clearRect(0, 0, w, h);

            saveUndoState();
            attachCanvasEvents();
        }

        function setMode(mode) {
            activeTool = mode;

            document.querySelectorAll('.toolbar .tool-btn')
                .forEach(b => b.classList.remove('active'));

            if(mode === 'ai') {
                document.getElementById('btnModeAI').classList.add('active');
            }

            if(mode === 'rect') {
                document.getElementById('btnModeRect').classList.add('active');
            }

            if(mode === 'brush') {
                document.getElementById('btnModeBrush').classList.add('active');
            }

            if(mode === 'eraser') {
                document.getElementById('btnModeEraser').classList.add('active');
            }

            const tolerance =
                document.getElementById('smartToleranceSetting');

            if (tolerance) {
                tolerance.hidden = mode !== 'ai';
            }
        }

        function setIntensity(val, el) {
            currentOpacity = val;
            maskCanvas.style.opacity = val;
            document.querySelectorAll('.settings-bar .pill').forEach(p => p.classList.remove('active'));
            el.classList.add('active');
        }

        function attachCanvasEvents() {
            maskCanvas.style.pointerEvents = 'auto';
            maskCanvas.style.touchAction = 'none';

            maskCanvas.onpointerdown = onMouseDown;
            maskCanvas.onpointermove = onMouseMove;
            maskCanvas.onpointerup = onMouseUp;
            maskCanvas.onpointercancel = onMouseUp;
            maskCanvas.onpointerleave = onMouseUp;
        }


        function getCanvasPoint(e) {
            const rect = maskCanvas.getBoundingClientRect();

            const scaleX = maskCanvas.width / rect.width;
            const scaleY = maskCanvas.height / rect.height;

            return {
                x: Math.max(
                    0,
                    Math.min(
                        maskCanvas.width - 1,
                        Math.round((e.clientX - rect.left) * scaleX)
                    )
                ),
                y: Math.max(
                    0,
                    Math.min(
                        maskCanvas.height - 1,
                        Math.round((e.clientY - rect.top) * scaleY)
                    )
                )
            };
        }
        function getBrushDiameter() {
            return parseInt(
                document.getElementById('brushSizeSlider')?.value || '28',
                10
            );
        }

        function drawBrushDot(x, y, erase = false) {
            const diameter = getBrushDiameter();
            const radius = diameter / 2;

            ctxMask.save();
            ctxMask.globalCompositeOperation =
                erase ? 'destination-out' : 'source-over';

            ctxMask.fillStyle =
                erase ? '#000000' : selectedColor.hex;

            ctxMask.beginPath();
            ctxMask.arc(x, y, radius, 0, Math.PI * 2);
            ctxMask.fill();
            ctxMask.restore();
        }

        function drawBrushLine(x1, y1, x2, y2, erase = false) {
            const diameter = getBrushDiameter();

            ctxMask.save();
            ctxMask.globalCompositeOperation =
                erase ? 'destination-out' : 'source-over';

            ctxMask.strokeStyle =
                erase ? '#000000' : selectedColor.hex;

            ctxMask.lineWidth = diameter;
            ctxMask.lineCap = 'round';
            ctxMask.lineJoin = 'round';

            ctxMask.beginPath();
            ctxMask.moveTo(x1, y1);
            ctxMask.lineTo(x2, y2);
            ctxMask.stroke();

            ctxMask.restore();
        }
        function onMouseDown(e) {
            if (!selectedColor) return;

            if (e.cancelable) {
                e.preventDefault();
            }

            const point = getCanvasPoint(e);

            startX = point.x;
            startY = point.y;
            lastX = point.x;
            lastY = point.y;
            isDrawing = true;

            if (activeTool === 'ai') {
                smartAIWallFill(
                    Math.round(startX),
                    Math.round(startY)
                );

                saveUndoState();
                isDrawing = false;
                return;
            }

            if (activeTool === 'brush') {
                drawBrushDot(point.x, point.y, false);
            }

            if (activeTool === 'eraser') {
                drawBrushDot(point.x, point.y, true);
            }
        }

        function onMouseMove(e) {
            if (!selectedColor) return;
            if (!isDrawing) return;

            if (e.cancelable) {
                e.preventDefault();
            }

            const point = getCanvasPoint(e);
            const x = point.x;
            const y = point.y;

            if (activeTool === 'brush') {
                drawBrushLine(
                    lastX,
                    lastY,
                    x,
                    y,
                    false
                );

                lastX = x;
                lastY = y;
            }

            if (activeTool === 'eraser') {
                drawBrushLine(
                    lastX,
                    lastY,
                    x,
                    y,
                    true
                );

                lastX = x;
                lastY = y;
            }
        }

        function onMouseUp(e) {
            if (!selectedColor) return;
            if(!isDrawing) return;
            isDrawing = false;
            const point = getCanvasPoint(e);
            const x = point.x;
            const y = point.y;

            if (activeTool === 'rect') {
                ctxMask.fillStyle = selectedColor.hex;
                ctxMask.fillRect(startX, startY, x - startX, y - startY);
                saveUndoState();
            } else if (activeTool === 'brush' || activeTool === 'eraser') {
                saveUndoState();
            }
        }

        function smartAIWallFill(startX, startY) {
            if (!selectedColor) return;
            const w = mainCanvas.width;
            const h = mainCanvas.height;
            const imgData = ctxMain.getImageData(0, 0, w, h);
            const maskData = ctxMask.getImageData(0, 0, w, h);
            
            const tol = parseInt(document.getElementById('toleranceSlider').value) * 3;
            const targetRGB = getPixelRGB(imgData, startX, startY);
            const fillColor = hexToRgb(selectedColor.hex);

            const visited = new Uint8Array(w * h);
            const queue = [[startX, startY]];

            while (queue.length > 0) {
                const [x, y] = queue.pop();
                const idx = (y * w + x);

                if (visited[idx]) continue;
                visited[idx] = 1;

                const currentRGB = getPixelRGB(imgData, x, y);
                if (colorDistance(targetRGB, currentRGB) <= tol) {
                    const pIdx = idx * 4;
                    maskData.data[pIdx] = fillColor.r;
                    maskData.data[pIdx + 1] = fillColor.g;
                    maskData.data[pIdx + 2] = fillColor.b;
                    maskData.data[pIdx + 3] = 255;

                    if (x > 0) queue.push([x - 1, y]);
                    if (x < w - 1) queue.push([x + 1, y]);
                    if (y > 0) queue.push([x, y - 1]);
                    if (y < h - 1) queue.push([x, y + 1]);
                }
            }

            ctxMask.putImageData(maskData, 0, 0);
        }

        function getPixelRGB(imgData, x, y) {
            const idx = (y * imgData.width + x) * 4;
            return {
                r: imgData.data[idx],
                g: imgData.data[idx + 1],
                b: imgData.data[idx + 2]
            };
        }

        function colorDistance(c1, c2) {
            return Math.sqrt(
                Math.pow(c1.r - c2.r, 2) +
                Math.pow(c1.g - c2.g, 2) +
                Math.pow(c1.b - c2.b, 2)
            );
        }

        function hexToRgb(hex) {
            var result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
            return result ? {
                r: parseInt(result[1], 16),
                g: parseInt(result[2], 16),
                b: parseInt(result[3], 16)
            } : { r: 0, g: 0, b: 0 };
        }

        function saveUndoState() {
            if(undoStack.length >= 10) undoStack.shift();
            undoStack.push(ctxMask.getImageData(0, 0, maskCanvas.width, maskCanvas.height));
        }

        function undoCanvas() {
            if(undoStack.length > 1) {
                undoStack.pop();
                ctxMask.putImageData(undoStack[undoStack.length - 1], 0, 0);
            } else if(undoStack.length === 1) {
                ctxMask.clearRect(0, 0, maskCanvas.width, maskCanvas.height);
            }
        }

        function resetCanvas() {
            if(loadedImg) {
                ctxMask.clearRect(0, 0, maskCanvas.width, maskCanvas.height);
                undoStack = [];
                saveUndoState();
            }
        }

        function saveResult() {
            if(!loadedImg || !selectedColor) return;
            const tempCanvas = document.createElement('canvas');
            tempCanvas.width = mainCanvas.width;
            tempCanvas.height = mainCanvas.height;
            const tCtx = tempCanvas.getContext('2d');

            tCtx.drawImage(mainCanvas, 0, 0);
            tCtx.globalAlpha = currentOpacity;
            tCtx.globalCompositeOperation = 'multiply';
            tCtx.drawImage(maskCanvas, 0, 0);

            const link = document.createElement('a');
            link.download = `worksitem-${selectedColor.brand}-${selectedColor.name}.png`;
            link.href = tempCanvas.toDataURL();
            link.click();
        }
    </script>
    @unless(request()->boolean('embed'))
        <div style="max-width:1180px;margin:0 auto;padding:0 20px"><x-sponsor.brand-cta tool="duvar-rengi-gorsellestirici" /></div>
        <x-site.contech-footer />
    @endunless
    <script src="/js/tool-events.js" defer></script>
</body>
</html>
