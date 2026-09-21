<!DOCTYPE html>
<html lang="tr">
<head>
    <link rel="icon" href="/favicon.ico" type="image/x-icon">

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profesyonel Duvar Renk Paleti & RAL Kataloğu | WORKSİTEM</title>
    <meta name="description" content="Jotun, Filli Boya, Dyo, Polisan, Marshall ve RAL K7 Classic renklerini inceleyin, seçtiğiniz duvar renklerini oda fotoğrafınızda simüle edin.">
    <link rel="canonical" href="https://worksitem.com/araclar/duvar-rengi-gorsellestirici">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #f8fafc;
            --surface: #ffffff;
            --border: #cbd5e1;
            --primary: #1e3a8a;
            --primary-hover: #1e40af;
            --text-main: #0f172a;
            --text-muted: #475569;
            --pill-bg: #f1f5f9;
            --pill-text: #334155;
            --pill-active-bg: #1e3a8a;
            --pill-active-text: #ffffff;
            --green: #059669;
            --amber: #d97706;
            --red: #dc2626;
            --radius-full: 9999px;
            --transition: all 0.2s ease;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: var(--bg); color: var(--text-main); line-height: 1.5; padding-bottom: 80px; }

        .navbar { display: flex; justify-content: space-between; align-items: center; padding: 1rem 5%; background: var(--surface); border-bottom: 1px solid var(--border); position: sticky; top: 0; z-index: 50; }
        .logo-wrapper { display: flex; align-items: center; gap: 8px; text-decoration: none; }
        .logo-mark { background: var(--primary); color: #fff; font-weight: 800; font-size: 14px; padding: 4px 10px; border-radius: 6px; }
        .logo-text { font-weight: 800; font-size: 20px; color: var(--text-main); }

        .container { max-width: 1200px; margin: 30px auto; padding: 0 20px; }

        .page-header { text-align: center; margin-bottom: 30px; }
        .page-header h1 { font-size: 2.25rem; font-weight: 800; color: var(--text-main); letter-spacing: -0.5px; margin-bottom: 8px; }
        .page-header p { color: var(--text-muted); font-size: 1rem; max-width: 750px; margin: 0 auto; }

        .quick-action-btn { display: inline-flex; align-items: center; gap: 8px; background: #eff6ff; color: var(--primary); border: 1px solid #bfdbfe; font-weight: 700; padding: 10px 22px; border-radius: var(--radius-full); margin-top: 15px; cursor: pointer; transition: var(--transition); }
        .quick-action-btn:hover { background: #dbeafe; }

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
        .swatch-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(130px, 1fr)); gap: 12px; margin-bottom: 10px; max-height: 250px; overflow-y: auto; padding-right: 5px; }
        .swatch-card { background: #fff; border: 2px solid var(--border); border-radius: 10px; padding: 8px; cursor: pointer; transition: var(--transition); text-align: center; }
        .swatch-card:hover { transform: translateY(-2px); border-color: #94a3b8; }
        .swatch-card.active { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(30,58,138,0.2); }
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
        .tool-btn.active { background: #eff6ff; border-color: var(--primary); color: var(--primary); }
        .tool-btn.btn-primary { background: var(--primary); color: #fff; border-color: var(--primary); }
        .tool-btn.btn-primary:hover { background: var(--primary-hover); }
        .tool-btn.btn-success { background: var(--green); color: #fff; border-color: var(--green); }
        .tool-btn.btn-amber { background: var(--amber); color: #fff; border-color: var(--amber); }
        .tool-btn.btn-danger { background: var(--red); color: #fff; border-color: var(--red); }
        .badge-ai { background: #1e3a8a; color: #fff; font-size: 0.65rem; padding: 2px 6px; border-radius: 4px; text-transform: uppercase; margin-left: 4px; }

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
        .lead-banner { background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 12px; padding: 20px; margin-top: 25px; display: flex; align-items: center; justify-content: space-between; gap: 20px; flex-wrap: wrap; }
        .lead-text h4 { font-size: 1.05rem; font-weight: 800; color: #166534; margin-bottom: 4px; }
        .lead-text p { font-size: 0.85rem; color: #15803d; }
        .lead-btn { background: var(--green); color: #fff; font-weight: 800; padding: 12px 24px; border-radius: 8px; text-decoration: none; transition: var(--transition); }
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
</head>
<body data-tool-slug="paint-visualizer">

    <x-site.contech-header />

    <div class="container">
        
        <div class="page-header">
            <h1>Türkiye Markaları & RAL Duvar Renk Paleti</h1>
            <p>Jotun, Filli Boya, Dyo, Polisan, Marshall ve resmi RAL K7 Classic kataloğundaki renkleri inceleyin, oda fotoğraflarınızda anında simüle edin.</p>
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
                    <span class="pill active" onclick="setBrand('RAL Kataloğu', this)">RAL Kataloğu</span>
                    <span class="pill" onclick="setBrand('Jotun', this)">Jotun Türkiye</span>
                    <span class="pill" onclick="setBrand('Filli Boya', this)">Filli Boya</span>
                    <span class="pill" onclick="setBrand('Dyo', this)">Dyo</span>
                    <span class="pill" onclick="setBrand('Polisan', this)">Polisan</span>
                    <span class="pill" onclick="setBrand('Marshall', this)">Marshall</span>
                    <span class="pill" onclick="setBrand('Tüm', this)">Tüm Listeyi Göster</span>
                </div>
            </div>

            <!-- Category Selection -->
            <div class="filter-row">
                <span class="filter-label">Renk Grubu:</span>
                <div class="pill-group" id="categoryPills">
                    <span class="pill active" onclick="setCategory('Tüm', this)">Tüm Renkler</span>
                    <span class="pill" onclick="setCategory('Beyazlar ve Açık Tonlar', this)">Beyazlar ve Açık Tonlar</span>
                    <span class="pill" onclick="setCategory('Griler ve Antrasit', this)">Griler ve Antrasit</span>
                    <span class="pill" onclick="setCategory('Bejler ve Kum Tonları', this)">Bejler ve Kum Tonları</span>
                    <span class="pill" onclick="setCategory('Yeşil ve Doğa', this)">Yeşil ve Doğa</span>
                    <span class="pill" onclick="setCategory('Mavi ve Pastel', this)">Mavi ve Pastel</span>
                </div>
            </div>

            <!-- Search Bar -->
            <div class="search-box">
                <span class="search-icon">🔍</span>
                <input type="text" id="searchInput" class="search-input" placeholder="Renk adı veya RAL kodu ara (Örn: RAL 7016, Meltem Beji, Egghvit)..." oninput="renderSwatches()">
            </div>

            <!-- Color Swatches Grid -->
            <div class="swatch-grid" id="swatchGrid">
                <!-- Dynamic Swatches -->
            </div>

        </div>

        <!-- SELECTED COLOR BANNER -->
        <div class="selected-banner">
            <div class="selected-info">
                <div class="selected-preview" id="bannerPreview" style="background-color: #383E42;"></div>
                <div class="selected-details">
                    <span class="selected-brand-tag" id="bannerBrand">RAL KATALOĞU</span>
                    <span class="selected-name" id="bannerName">RAL 7016 Antrasit Gri</span>
                    <span class="selected-hex" id="bannerHex">#383E42</span>
                </div>
            </div>
            <button class="icon-btn" title="Tam Ekran Görüntüle" onclick="toggleFullScreenColor()">
                ⛶
            </button>
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
                <button class="tool-btn active" id="btnModeAI" onclick="setMode('ai')">
                    🎨 Akıllı Duvar Boya <span class="badge-ai">AI Mask</span>
                </button>
                <button class="tool-btn" id="btnModeRect" onclick="setMode('rect')">
                    ⬛ Dikdörtgen
                </button>
                <button class="tool-btn" id="btnModeBrush" onclick="setMode('brush')">
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

            <!-- SETTINGS BAR -->
            <div class="settings-bar">
                <div class="setting-item">
                    <span>Hassasiyet (Tolerans):</span>
                    <input type="range" id="toleranceSlider" class="range-slider" min="1" max="30" value="9" onchange="document.getElementById('toleranceVal').innerText = this.value">
                    <span id="toleranceVal">9</span>
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
                    <p>Seçtiğiniz bu tonun malzeme ihtiyacını ve usta işçilik bütçesini 2026 ÇŞB rayiçleriyle anında hesaplayın.</p>
                </div>
                <a href="/araclar/boya-hesaplayici" class="lead-btn">Boya Maliyetini Hesapla →</a>
            </div>

        </div>

    </div>

    <input type="file" id="imageUploader" accept="image/*" style="display: none;" onchange="handleImageUpload(event)">

    <script>
                // --- MERKEZİ ÇŞB FİYAT MOTORU & KAİNTA ÇARPANI ---
        const CSB_PRICES = {!! isset($SYSTEM_PRICES) ? $SYSTEM_PRICES : '{}' !!};
        const KAINTA_MULTIPLIER = {!! isset($KAINTA_MULTIPLIER) ? $KAINTA_MULTIPLIER : 1.40 !!};

        function getDbPrice(key, fallback) {
            let baseCsbPrice = CSB_PRICES[key] !== undefined ? parseFloat(CSB_PRICES[key]) : fallback;
            // ÇŞB Ham Fiyatı x 1.40 Kainta Çarpanı
            return baseCsbPrice * KAINTA_MULTIPLIER; 
        }
        
        function getRawCsbPrice(key, fallback) {
            // Sadece ham ÇŞB maliyeti görmek istenirse (İhale modülü vb. için çarpanı yoksay)
            return CSB_PRICES[key] !== undefined ? parseFloat(CSB_PRICES[key]) : fallback;
        }
        // ------------------------------------------------
        // OFFICIAL TURKISH BRANDS & RAL K7 CLASSIC DATABASE
        const COLOR_DATABASE = [
            // RAL Classic Standards
            { brand: 'RAL Kataloğu', category: 'Griler ve Antrasit', name: 'RAL 7016 Antrasit Gri', hex: '#383E42' },
            { brand: 'RAL Kataloğu', category: 'Beyazlar ve Açık Tonlar', name: 'RAL 9010 Saf Beyaz', hex: '#F7F5EC' },
            { brand: 'RAL Kataloğu', category: 'Beyazlar ve Açık Tonlar', name: 'RAL 9016 Trafik Beyazı', hex: '#F6F6F6' },
            { brand: 'RAL Kataloğu', category: 'Beyazlar ve Açık Tonlar', name: 'RAL 1013 İstiridye Beyazı', hex: '#EBE5CE' },
            { brand: 'RAL Kataloğu', category: 'Griler ve Antrasit', name: 'RAL 7035 Işık Grisi', hex: '#D7D7D7' },
            { brand: 'RAL Kataloğu', category: 'Griler ve Antrasit', name: 'RAL 7040 Pencere Grisi', hex: '#9DA3A6' },
            { brand: 'RAL Kataloğu', category: 'Griler ve Antrasit', name: 'RAL 7012 Bazalt Grisi', hex: '#596067' },

            // Jotun Türkiye
            { brand: 'Jotun', category: 'Beyazlar ve Açık Tonlar', name: 'JOT 1001 Egghvit (Yumurta Kabuğu)', hex: '#F1EBE0' },
            { brand: 'Jotun', category: 'Beyazlar ve Açık Tonlar', name: 'JOT 1453 Bomull (Pamuk Beyazı)', hex: '#F3EFE3' },
            { brand: 'Jotun', category: 'Beyazlar ve Açık Tonlar', name: 'JOT 9918 Klassisk Hvit', hex: '#F3F3EF' },
            { brand: 'Jotun', category: 'Bejler ve Kum Tonları', name: 'JOT 10341 Kalk (Kireç Beji)', hex: '#D3CBBF' },
            { brand: 'Jotun', category: 'Yeşil ve Doğa', name: 'JOT 8493 Green Tea', hex: '#9CA998' },
            { brand: 'Jotun', category: 'Mavi ve Pastel', name: 'JOT 5030 St. Pauls Blue', hex: '#5B6B73' },

            // Filli Boya
            { brand: 'Filli Boya', category: 'Griler ve Antrasit', name: 'FIL Andezit 15', hex: '#A3A7AC' },
            { brand: 'Filli Boya', category: 'Bejler ve Kum Tonları', name: 'FIL Meltem Beji', hex: '#DFD5C6' },
            { brand: 'Filli Boya', category: 'Bejler ve Kum Tonları', name: 'FIL Kumsal', hex: '#E6DBC9' },
            { brand: 'Filli Boya', category: 'Beyazlar ve Açık Tonlar', name: 'FIL Apaydın', hex: '#EFE7D3' },
            { brand: 'Filli Boya', category: 'Mavi ve Pastel', name: 'FIL Okyanus Düşü', hex: '#5B7C99' },

            // Dyo
            { brand: 'Dyo', category: 'Beyazlar ve Açık Tonlar', name: 'DYO Buz Beyazı', hex: '#F4F4EE' },
            { brand: 'Dyo', category: 'Griler ve Antrasit', name: 'DYO Gözdağı', hex: '#8E979B' },
            { brand: 'Dyo', category: 'Bejler ve Kum Tonları', name: 'DYO Aura', hex: '#D8CEB9' },
            { brand: 'Dyo', category: 'Griler ve Antrasit', name: 'DYO Krom', hex: '#A5A8AC' },

            // Polisan
            { brand: 'Polisan', category: 'Griler ve Antrasit', name: 'POL Marjinal Gri', hex: '#B5B8B8' },
            { brand: 'Polisan', category: 'Bejler ve Kum Tonları', name: 'POL Kumsal Beji', hex: '#E1D4C1' },
            { brand: 'Polisan', category: 'Bejler ve Kum Tonları', name: 'POL Şampanya', hex: '#ECE1D0' },
            { brand: 'Polisan', category: 'Beyazlar ve Açık Tonlar', name: 'POL Sakız Beyazı', hex: '#F4F3EA' },

            // Marshall Türkiye
            { brand: 'Marshall', category: 'Griler ve Antrasit', name: 'MAR Tütsü', hex: '#D8D3C8' },
            { brand: 'Marshall', category: 'Griler ve Antrasit', name: 'MAR Çakıl Taşı', hex: '#A39F97' },
            { brand: 'Marshall', category: 'Bejler ve Kum Tonları', name: 'MAR Damla Sakızı', hex: '#EAE4D5' },
            { brand: 'Marshall', category: 'Yeşil ve Doğa', name: 'MAR Isırgan Otualtı', hex: '#A8B29C' }
        ];

        let currentBrand = 'RAL Kataloğu';
        let currentCategory = 'Tüm';
        let selectedColor = COLOR_DATABASE[0];

        // Canvas Elements
        let mainCanvas = document.getElementById('mainCanvas');
        let maskCanvas = document.getElementById('maskCanvas');
        let ctxMain = mainCanvas.getContext('2d');
        let ctxMask = maskCanvas.getContext('2d');
        let loadedImg = null;

        let activeTool = 'ai';
        let currentOpacity = 0.75;
        let isDrawing = false;
        let startX = 0, startY = 0;
        let undoStack = [];

        document.addEventListener('DOMContentLoaded', () => {
            renderSwatches();
            selectColor(COLOR_DATABASE[0]);
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

        function renderSwatches() {
            const query = document.getElementById('searchInput').value.toLowerCase();
            const grid = document.getElementById('swatchGrid');
            grid.innerHTML = '';

            const filtered = COLOR_DATABASE.filter(c => {
                const matchBrand = (currentBrand === 'Tüm' || c.brand === currentBrand);
                const matchCat = (currentCategory === 'Tüm' || c.category === currentCategory);
                const matchSearch = c.name.toLowerCase().includes(query) || c.hex.toLowerCase().includes(query);
                return matchBrand && matchCat && matchSearch;
            });

            filtered.forEach(c => {
                const card = document.createElement('div');
                card.className = `swatch-card ${selectedColor.hex === c.hex ? 'active' : ''}`;
                card.onclick = () => selectColor(c);
                card.innerHTML = `
                    <div class="swatch-color" style="background-color: ${c.hex};"></div>
                    <div class="swatch-brand">${c.brand}</div>
                    <div class="swatch-title">${c.name}</div>
                `;
                grid.appendChild(card);
            });
        }

        function selectColor(color) {
            selectedColor = color;
            document.getElementById('bannerPreview').style.backgroundColor = color.hex;
            document.getElementById('bannerBrand').innerText = color.brand;
            document.getElementById('bannerName').innerText = color.name;
            document.getElementById('bannerHex').innerText = color.hex;

            document.querySelectorAll('.swatch-card').forEach(card => card.classList.remove('active'));
            renderSwatches();
        }

        function toggleFullScreenColor() {
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
            document.querySelectorAll('.toolbar .tool-btn').forEach(b => b.classList.remove('active'));
            if(mode === 'ai') document.getElementById('btnModeAI').classList.add('active');
            if(mode === 'rect') document.getElementById('btnModeRect').classList.add('active');
            if(mode === 'brush') document.getElementById('btnModeBrush').classList.add('active');
            if(mode === 'eraser') document.getElementById('btnModeEraser').classList.add('active');
        }

        function setIntensity(val, el) {
            currentOpacity = val;
            maskCanvas.style.opacity = val;
            document.querySelectorAll('.settings-bar .pill').forEach(p => p.classList.remove('active'));
            el.classList.add('active');
        }

        function attachCanvasEvents() {
            maskCanvas.style.pointerEvents = 'auto';
            maskCanvas.onmousedown = onMouseDown;
            maskCanvas.onmousemove = onMouseMove;
            maskCanvas.onmouseup = onMouseUp;
        }

        function onMouseDown(e) {
            const rect = maskCanvas.getBoundingClientRect();
            startX = e.clientX - rect.left;
            startY = e.clientY - rect.top;
            isDrawing = true;

            if (activeTool === 'ai') {
                smartAIWallFill(Math.round(startX), Math.round(startY));
                saveUndoState();
            }
        }

        function onMouseMove(e) {
            if(!isDrawing) return;
            const rect = maskCanvas.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;

            if (activeTool === 'brush') {
                ctxMask.fillStyle = selectedColor.hex;
                ctxMask.beginPath();
                ctxMask.arc(x, y, 15, 0, Math.PI * 2);
                ctxMask.fill();
            } else if (activeTool === 'eraser') {
                ctxMask.clearRect(x - 15, y - 15, 30, 30);
            }
        }

        function onMouseUp(e) {
            if(!isDrawing) return;
            isDrawing = false;
            const rect = maskCanvas.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;

            if (activeTool === 'rect') {
                ctxMask.fillStyle = selectedColor.hex;
                ctxMask.fillRect(startX, startY, x - startX, y - startY);
                saveUndoState();
            } else if (activeTool === 'brush' || activeTool === 'eraser') {
                saveUndoState();
            }
        }

        function smartAIWallFill(startX, startY) {
            const w = mainCanvas.width;
            const h = mainCanvas.height;
            const imgData = ctxMain.getImageData(0, 0, w, h);
            const maskData = ctxMask.getImageData(0, 0, w, h);
            
            const tol = parseInt(document.getElementById('toleranceSlider').value) * 5;
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
            if(!loadedImg) return;
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
    <x-site.contech-footer />
    <script src="/js/tool-events.js" defer></script>
</body>
</html>
