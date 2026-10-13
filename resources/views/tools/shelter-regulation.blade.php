<!DOCTYPE html>
<html lang="tr">
<head>
    <link rel="icon" href="/favicon.ico" type="image/x-icon">

    <meta charset="UTF-8">
    <title>Sığınak Yönetmeliği Hesabı | WORKSİTEM</title>
    <meta name="description" content="Planlı Alanlar İmar Yönetmeliği kapsamında binanızda sığınak zorunluluğunu ve gerekli yaklaşık sığınak alanını hesaplayın.">
    <link rel="canonical" href="https://worksitem.com/araclar/siginak-yonetmeligi">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body { font-family:'Inter', sans-serif; background:#fdfdfd; color:#111827; margin:0; padding-bottom:60px;}
        .container { max-width:800px; margin:40px auto; padding:30px; background:#fff; border:1px solid #e5e7eb; border-radius:16px; box-shadow:0 4px 6px rgba(0,0,0,0.05); }
        h1 { font-size:24px; font-weight:800; margin-bottom:10px; color:#101318;}
        .form-group { margin-bottom:20px; }
        label { display:block; font-size:13px; font-weight:700; color:#6b7280; margin-bottom:8px; text-transform:uppercase;}
        input, select { width:100%; padding:14px; border:1px solid #e5e7eb; border-radius:8px; font-family:inherit; font-size:15px; }
        .result-box { background:#fff8e1; border:2px solid #fde68a; padding:20px; border-radius:12px; margin-top:20px; text-align:center;}
        .result-val { font-size:24px; font-weight:800; color:#101318; }
        .cta { display:block; width:100%; padding:16px; background:#101318; color:#fff; text-align:center; font-weight:800; text-decoration:none; border-radius:8px; margin-top:20px; }
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
<body class="wc" data-tool-slug="shelter-regulation">
    <x-site.contech-header />
    <div class="container">
        <x-calc.hero title="Sığınak zorunluluğu ve alanı hesaplama" crumb="Sığınak">Planlı Alanlar İmar Yönetmeliği gereği binanızda sığınak zorunluluğu olup olmadığını ve gereken alanı öğrenin.</x-calc.hero>
        
        <div class="form-group">
            <label>Bina Tipi</label>
            <select id="bType" onchange="calc()">
                <option value="konut">Konut (Daire)</option>
                <option value="isyeri">İşyeri / Ticari</option>
            </select>
        </div>
        <div class="form-group">
            <label id="lblAmount">Toplam Bağımsız Bölüm (Daire) Sayısı</label>
            <input type="number" id="amount" value="15" oninput="calc()">
        </div>
        <div class="form-group">
            <label>Toplam Emsal / İnşaat Alanı (m²)</label>
            <input type="number" id="area" value="1600" oninput="calc()">
        </div>

        <div class="result-box" data-wc-result>
            <div style="font-size:14px; color:#101318; font-weight:600; margin-bottom:5px;">YÖNETMELİK SONUCU</div>
            <div class="result-val" id="resText" data-wc-price>Zorunlu Değil</div>
            <div style="font-size:13px; color:#101318; margin-top:10px; font-weight:600;" id="resArea">Gereken Sığınak Alanı: 0 m²</div>
        </div>
        <a href="/maliyet-hesapla?service=mimari-proje" class="cta">Mimari Çözüm ve Proje İçin Teklif Al →</a>
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
        document.getElementById('bType').addEventListener('change', function(){
            document.getElementById('lblAmount').innerText = this.value === 'konut' ? 'Toplam Bağımsız Bölüm (Daire) Sayısı' : 'Toplam Çalışan / Kullanıcı Sayısı';
            calc();
        });

        function calc() {
            let bType = document.getElementById('bType').value;
            let amount = parseInt(document.getElementById('amount').value) || 0;
            let area = parseFloat(document.getElementById('area').value) || 0;
            
            let isZorunlu = false;
            let siginakArea = 0;

            if(bType === 'konut') {
                if(amount >= 12 || area >= 1500) { isZorunlu = true; siginakArea = amount * 1.5; }
            } else {
                if(area >= 1500) { isZorunlu = true; siginakArea = amount * 1.2; }
            }

            document.getElementById('resText').innerText = isZorunlu ? 'SİĞINAK ZORUNLUDUR ⚠️' : 'SIĞINAK ZORUNLU DEĞİL ✅';
            document.getElementById('resText').style.color = isZorunlu ? '#b91c1c' : '#101318';
            document.getElementById('resArea').innerText = isZorunlu ? `Gereken Sığınak Alanı: Min. ${siginakArea} m²` : 'Gereken Sığınak Alanı: 0 m²';
        }
        document.addEventListener('DOMContentLoaded', calc);
    </script>
<x-calc.kit />
    <x-site.contech-footer />
    <script src="/js/tool-events.js" defer></script>
</body>
</html>
