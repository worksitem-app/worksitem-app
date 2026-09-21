<!DOCTYPE html><html lang="tr"><head>
    <link rel="icon" href="/favicon.ico" type="image/x-icon">
    <meta property="og:image" content="https://worksitem.com/brand/og-image.png">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:image" content="https://worksitem.com/brand/og-image.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="manifest" href="/site.webmanifest">
    <meta name="theme-color" content="#ffffff">
<meta charset="UTF-8"><title>1D Kesim Optimizasyonu ve Fire | WORKSİTEM</title>
    <meta name="description" content="Profil, boru ve ahşap karkas kesimlerinde standart boylara göre ihtiyaç miktarını ve fire oranını hesaplayarak siparişi optimize edin.">
    <link rel="canonical" href="https://worksitem.com/araclar/kesim-optimizasyonu"><link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet"><style>body{font-family:'Inter',sans-serif;background:#fdfdfd;color:#111827;margin:0;padding-bottom:60px;} .container{max-width:800px;margin:40px auto;padding:30px;background:#fff;border:1px solid #e5e7eb;border-radius:16px;box-shadow:0 4px 6px rgba(0,0,0,0.05);} h1{font-size:24px;font-weight:800;margin-bottom:10px;color:#1e3a8a;} .form-group{margin-bottom:20px;} label{display:block;font-size:13px;font-weight:700;color:#6b7280;margin-bottom:8px;text-transform:uppercase;} input,select{width:100%;padding:14px;border:1px solid #e5e7eb;border-radius:8px;font-family:inherit;font-size:15px;} .grid-2{display:grid;grid-template-columns:1fr 1fr;gap:15px;} .result-box{background:#f0fdf4;border:2px solid #bbf7d0;padding:20px;border-radius:12px;margin-top:20px;text-align:center;} .result-val{font-size:32px;font-weight:800;color:#166534;} .cta{display:block;width:100%;padding:16px;background:#1d4ed8;color:#fff;text-align:center;font-weight:800;text-decoration:none;border-radius:8px;margin-top:20px; transition:all 0.2s;} .cta:hover{opacity:0.9; transform:translateY(-2px);}</style>    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body data-tool-slug="cutting-optimization">
    <x-site.contech-header /><div class="container"><h1>✂️ 1D Kesim Fire Hesaplayıcı</h1><p style="color:#6b7280; margin-bottom:30px;">Profil, boru veya ahşap karkas alımlarında boy standardına göre ne kadar profil sipariş etmeniz gerektiğini hesaplayın.</p>
<div class="grid-2"><div class="form-group"><label>İstenen Parça Boyu (Metre)</label><input type="number" id="part" value="2.2" oninput="calc()"></div><div class="form-group"><label>Gereken Parça Adedi</label><input type="number" id="qty" value="50" oninput="calc()"></div><div class="form-group"><label>Fabrika Boy Standardı (Metre)</label><input type="number" id="standard" value="6" oninput="calc()"></div><div class="form-group"><label>Testere Payı (Bıçak Kalınlığı) - cm</label><input type="number" id="blade" value="0.5" oninput="calc()"></div></div>
<div class="result-box"><div style="font-size:14px; color:#15803d; font-weight:600;">GEREKEN TOPLAM BOY PROFİL ADEDİ</div><div class="result-val" id="res">0 Adet</div><div style="font-size:13px; color:#166534; margin-top:5px;" id="res2"></div></div>
<a href="/maliyet-hesapla?service=malzeme" class="cta">İnşaat Malzemesi ve Çelik Profil Teklifi Al →</a></div>

        <!-- STANDART KAİNTA LEAD HUNİSİ (CTA) -->
        <div class="no-print" style="margin-top: 40px; padding: 30px; background: #f0fdf4; border: 2px solid #bbf7d0; border-radius: 16px; text-align: center;">
            <h3 style="color: #166534; font-size: 20px; font-weight: 800; margin-bottom: 10px;">Hesaplamayı Yaptınız, Şimdi İşi Uzmanına Bırakın</h3>
            <p style="color: #15803d; font-size: 14px; margin-bottom: 20px;">Kainta İnşaat mühendislerinden projenize özel resmi keşif ve bütçe teklifi alın.</p>
            <a href="/maliyet-hesapla" style="display: inline-block; background: #166534; color: #fff; padding: 16px 32px; font-size: 16px; font-weight: 800; border-radius: 8px; text-decoration: none; box-shadow: 0 4px 10px rgba(22,101,52,0.3); transition: all 0.2s;">Ön maliyet hesabına geç →</a>
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
    <x-site.contech-footer />
        // ------------------------------------------------function calc() { let p = parseFloat(document.getElementById('part').value)||0; let q = parseInt(document.getElementById('qty').value)||0; let s = parseFloat(document.getElementById('standard').value)||1; let b = (parseFloat(document.getElementById('blade').value)||0)/100; let piecesPerStock = Math.floor(s / (p + b)); if(piecesPerStock < 1) piecesPerStock = 1; let stocksNeeded = Math.ceil(q / piecesPerStock); let totalWaste = (stocksNeeded * s) - (q * p); document.getElementById('res').innerText = stocksNeeded + ' Boy (' + s + 'm)'; document.getElementById('res2').innerText = 'Tahmini Çıkan Fire Uzunluğu: ' + totalWaste.toFixed(2) + ' Metre'; } document.addEventListener('DOMContentLoaded', calc);</script>    <script src="/js/tool-events.js" defer></script>
</body></html>
