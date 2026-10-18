<!DOCTYPE html>
<html lang="tr">
<head>
    <link rel="icon" href="/favicon.ico" type="image/x-icon">

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deprem İzolatör Maliyeti | WORKSİTEM</title>
    <meta name="description" content="2026 ÇŞB Poz 15.570.1001 ve türevlerini baz alarak binanız için sismik deprem izolatörü sistemlerinin yaklaşık ön bütçesini hesaplayın.">
    <link rel="canonical" href="https://worksitem.com/araclar/deprem-izolator-maliyeti">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        :root { --bg: #f5f6f8; --card-bg: #ffffff; --border: #e5e7eb; --yellow: #9a6e00; --text: #101318; --muted: #5b6472; --cyan: #8a6200; --red:#ef4444; }
        body { font-family: 'Inter', sans-serif; background: var(--bg); color: var(--text); margin: 0; padding: 0; overflow-x: hidden; }
        .container { max-width: 1200px; margin: 40px auto; padding: 0 4%; display: grid; grid-template-columns: 1fr 1fr; gap: 40px; }
        @media(max-width: 992px) { .container { grid-template-columns: 1fr; } }
        .form-card { background: var(--card-bg); border: 1px solid var(--border); border-radius: 12px; padding: 30px; margin-bottom: 20px; }
        h1 { font-family:'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; font-size: 38px; font-weight: 800; letter-spacing: -0.02em; color: var(--text); margin-bottom: 10px; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; font-size: 13px; font-weight: 700; color: var(--muted); margin-bottom: 8px; text-transform: uppercase; }
        .form-control { width: 100%; padding: 12px; border-radius: 6px; background: #ffffff; border: 1px solid var(--border); color: var(--text); font-family: inherit; }
        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
        .res-item { display: flex; justify-content: space-between; padding: 12px 0; border-bottom: 1px dashed var(--border); font-size: 14px; }
        .res-item.total { border-bottom: none; font-size: 20px; font-weight: 800; color: var(--yellow); margin-top: 15px; }
        .ai-box { background: rgba(255,184,0,0.05); border: 1px solid var(--cyan); border-radius: 8px; padding: 20px; margin-top: 20px; }
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
    <x-calc.head />
</head>
<body class="wc" data-tool-slug="isolator-calculator">
    <x-site.contech-header />
    <div class="container">
        <div>
            <x-calc.hero title="Deprem izolatörü maliyeti hesaplama" crumb="Deprem izolatörü">2026 ÇŞB Poz 15.570.1001 ve türevleri baz alınarak binanıza eklenecek sismik koruma sistemlerinin ön bütçesini çıkarın.</x-calc.hero>
            <div class="form-card">
                <div class="grid-2">
                    <div class="form-group"><label>Bina Kat Sayısı</label><input type="number" id="floors" class="form-control" value="5" oninput="calc()"></div>
                    <div class="form-group"><label>Zemindeki Taşıyıcı Kolon Sayısı</label><input type="number" id="cols" class="form-control" value="12" oninput="calc()"></div>
                </div>
                <div class="form-group">
                    <label>Binanın Düşey Yük / Büyüklük Sınıfı</label>
                    <select id="load" class="form-control" onchange="calc()">
                        <option value="175035" selected>Standart Konut (Taşıma Kapasitesi: 2501-5000 kN)</option>
                        <option value="208199">Ağır Yapı / Yüksek Kat (Taşıma Kap. 5001-7500 kN)</option>
                        <option value="240644">Hastane / Devasa Yapı (Taşıma Kap. 7501-10000 kN)</option>
                    </select>
                </div>
            </div>
        </div>
        <div>
            <div class="form-card" style="border-color:var(--cyan); position:sticky; top:20px;" data-wc-result>
                <div style="font-family:'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; font-size:24px; color:var(--cyan); margin-bottom:15px;">📊 2026 ÇŞB BÜTÇE ANALİZİ</div>
                <div class="res-item"><span>Gereken İzolatör Adedi</span> <strong id="r_count">0 Adet</strong></div>
                <div class="res-item"><span>Birim İzolatör Maliyeti</span> <strong id="r_unit">0 TL</strong></div>
                <div class="res-item total"><span>TOPLAM İZOLATÖR CİHAZ MALİYETİ</span> <span id="r_total" data-wc-price>0 TL</span></div>
                
                <div class="ai-box">
                    <div style="color:var(--cyan); font-family:'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; font-size:22px; margin-bottom:10px;">💡 Statik Proje Uyarıları</div>
                    <ul id="ai_notes" style="font-size:13px; margin:0; padding-left:15px; line-height:1.6;"></ul>
                </div>
            </div>
        </div>
    </div>

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
        function calc() {
            let cols = parseInt(document.getElementById('cols').value) || 0;
            let price = parseFloat(document.getElementById('load').value);

            // Her kolona (veya perde altlarına) 1 adet izolatör gerekir
            let total = cols * price;

            document.getElementById('r_count').innerText = cols + ' Adet';
            document.getElementById('r_unit').innerText = new Intl.NumberFormat('tr-TR').format(Math.round(price)) + ' TL';
            document.getElementById('r_total').innerText = new Intl.NumberFormat('tr-TR').format(Math.round(total)) + ' TL';

            let notes = [];
            notes.push("Hesaplanan rakam SADECE Çift Sürtünme Yüzeyli Deprem Yalıtım Biriminin temini ve yerine konması maliyetidir (ÇŞB Poz: 15.570.1001-1003).");
            notes.push("İzolatör uygulaması için bina temelinde yaratılacak ekstra çift temel (radye üstü radye) kalıp ve demir maliyetleri bu hesaba DAHİL DEĞİLDİR.");
            notes.push("Sonradan mevcut binaya (güçlendirme olarak) izolatör eklemek, sıfırdan inşaata eklemekten statik kesim ve kriko kaldırma işlemleri nedeniyle %50-60 daha maliyetlidir.");
            
            let notesBox = document.getElementById('ai_notes');
            notesBox.innerHTML = '';
            notes.forEach(n => { let li = document.createElement('li'); li.innerText = n; li.style.marginBottom = "5px"; notesBox.appendChild(li); });
        }
        document.addEventListener('DOMContentLoaded', calc);
    </script>
<x-calc.kit />
    <x-site.contech-footer />
    <script src="/js/tool-events.js" defer></script>
</body>
</html>
