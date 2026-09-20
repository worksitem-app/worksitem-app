<!DOCTYPE html>
<html lang="tr">
<head>
    <link rel="icon" href="/favicon.ico" type="image/x-icon">

    <meta charset="UTF-8">
    <title>İnşaat Malzeme Ağırlığı | WORKSİTEM</title>
    <meta name="description" content="Malzeme yoğunluğu ve hacmine göre inşaat malzemelerinin yaklaşık ağırlığını ve nakliye tonajını hesaplayarak lojistik planınızı hazırlayın.">
    <link rel="canonical" href="https://worksitem.com/araclar/malzeme-agirligi">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
    <style>
        body { font-family:'Inter', sans-serif; background:#fdfdfd; color:#111827; margin:0; padding-bottom:60px;}
        .container { max-width:800px; margin:40px auto; padding:30px; background:#fff; border:1px solid #e5e7eb; border-radius:16px; box-shadow:0 4px 6px rgba(0,0,0,0.05); }
        h1 { font-size:24px; font-weight:800; margin-bottom:10px; color:#1e3a8a;}
        .form-group { margin-bottom:20px; }
        label { display:block; font-size:13px; font-weight:700; color:#6b7280; margin-bottom:8px; text-transform:uppercase;}
        input, select { width:100%; padding:14px; border:1px solid #e5e7eb; border-radius:8px; font-family:inherit; font-size:15px; }
        .result-box { background:#f5f3ff; border:2px solid #ddd6fe; padding:20px; border-radius:12px; margin-top:20px; text-align:center;}
        .result-val { font-size:32px; font-weight:800; color:#6d28d9; }
        .cta { display:block; width:100%; padding:16px; background:#7c3aed; color:#fff; text-align:center; font-weight:800; text-decoration:none; border-radius:8px; margin-top:20px; }
    </style>
    <meta property="og:image" content="https://worksitem.com/brand/og-image.png">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:image" content="https://worksitem.com/brand/og-image.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="manifest" href="/site.webmanifest">
    <meta name="theme-color" content="#ffffff">
</head>
<body>
    <x-site.contech-header />
    <div class="container">
        <h1>⚖️ İnşaat Malzeme Ağırlığı</h1>
        <p style="color:#6b7280; margin-bottom:30px;">Özgül ağırlıklara (Yoğunluk) göre hacimsel malzemelerin lojistik nakliye tonajını hesaplayın.</p>
        
        <div class="form-group">
            <label>Malzeme Cinsi</label>
            <select id="material" onchange="calc()">
                <option value="2400">Betonarme Betonu (2400 kg/m³)</option>
                <option value="7850">Yapı Çeliği / Demir (7850 kg/m³)</option>
                <option value="1600">Kuru Kum (1600 kg/m³)</option>
                <option value="1400">Çimento (1400 kg/m³)</option>
                <option value="600">Gazbeton (600 kg/m³)</option>
                <option value="2700">Mermer / Granit (2700 kg/m³)</option>
            </select>
        </div>
        <div class="form-group">
            <label>Toplam Hacim (m³)</label>
            <input type="number" id="vol" value="10" oninput="calc()">
        </div>

        <div class="result-box">
            <div style="font-size:14px; color:#5b21b6; font-weight:600; margin-bottom:5px;">TOPLAM AĞIRLIK (TONAJ)</div>
            <div class="result-val" id="res">0 Ton</div>
        </div>
        <a href="/maliyet-hesapla?service=nakliye-lojistik" class="cta">Lojistik ve Nakliye İçin Keşif Al →</a>
    </div>

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
        // ------------------------------------------------
        function calc() {
            let density = parseFloat(document.getElementById('material').value);
            let vol = parseFloat(document.getElementById('vol').value) || 0;
            
            let kg = density * vol;
            let ton = kg / 1000;
            
            document.getElementById('res').innerText = ton.toFixed(2) + ' Ton (' + kg.toLocaleString('tr-TR') + ' Kg)';
        }
        document.addEventListener('DOMContentLoaded', calc);
    </script>
    <x-site.contech-footer />
</body>
</html>
