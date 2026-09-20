<!DOCTYPE html>
<html lang="tr">
<head>
    <link rel="icon" href="/favicon.ico" type="image/x-icon">

    <meta charset="UTF-8">
    <title>Taksit / Peşin Kıyası | WORKSİTEM</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
    <style>
        body { font-family:'Inter', sans-serif; background:#fdfdfd; color:#111827; margin:0; padding-bottom:60px;}
        .container { max-width:800px; margin:40px auto; padding:30px; background:#fff; border:1px solid #e5e7eb; border-radius:16px; box-shadow:0 4px 6px rgba(0,0,0,0.05); }
        h1 { font-size:24px; font-weight:800; margin-bottom:10px; color:#1e3a8a;}
        .form-group { margin-bottom:20px; }
        label { display:block; font-size:13px; font-weight:700; color:#6b7280; margin-bottom:8px; text-transform:uppercase;}
        input { width:100%; padding:14px; border:1px solid #e5e7eb; border-radius:8px; font-family:inherit; font-size:15px; }
        .grid-2 { display:grid; grid-template-columns:1fr 1fr; gap:15px; }
        .result-box { background:#fefce8; border:2px solid #fde047; padding:20px; border-radius:12px; margin-top:20px; text-align:center;}
        .result-val { font-size:24px; font-weight:800; color:#854d0e; }
        .cta { display:block; width:100%; padding:16px; background:#0f172a; color:#fff; text-align:center; font-weight:800; text-decoration:none; border-radius:8px; margin-top:20px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>💳 İnşaat Malzemesi: Taksit mi Peşin mi?</h1>
        <p style="color:#6b7280; margin-bottom:30px;">Mevduat faizi (veya enflasyon) beklentinize göre, bir malzemeyi vadeli (taksitli) almak mı yoksa peşin indirimle almak mı daha kârlı hesaplayın.</p>
        
        <div class="grid-2">
            <div class="form-group">
                <label>Peşin Fiyat (TL)</label>
                <input type="number" id="cashPrice" value="100000" oninput="calc()">
            </div>
            <div class="form-group">
                <label>Taksitli Toplam Fiyat (TL)</label>
                <input type="number" id="installPrice" value="120000" oninput="calc()">
            </div>
        </div>
        <div class="grid-2">
            <div class="form-group">
                <label>Vade Sayısı (Ay)</label>
                <input type="number" id="months" value="12" oninput="calc()">
            </div>
            <div class="form-group">
                <label>Beklenen Aylık Faiz / Getiri (%)</label>
                <input type="number" id="rate" value="4.5" step="0.1" oninput="calc()">
            </div>
        </div>

        <div class="result-box">
            <div style="font-size:14px; color:#a16207; font-weight:600; margin-bottom:5px;">GÜNCEL DEĞER ANALİZİ (NPV)</div>
            <div class="result-val" id="resText">Hesaplanıyor...</div>
            <div style="font-size:14px; color:#854d0e; margin-top:10px; font-weight:600;" id="resDetail"></div>
        </div>

        <a href="/maliyet-hesapla?service=finansman" class="cta">İnşaat Finansmanı ve Proje Yönetimi İçin Danışın →</a>
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
            let cash = parseFloat(document.getElementById('cashPrice').value) || 0;
            let install = parseFloat(document.getElementById('installPrice').value) || 0;
            let m = parseInt(document.getElementById('months').value) || 1;
            let r = (parseFloat(document.getElementById('rate').value) || 0) / 100;
            
            // Aylık taksit tutarı
            let pmt = install / m;
            
            // Taksitlerin Bugünkü Değeri (Net Present Value - NPV)
            let npv = 0;
            for(let i=1; i<=m; i++){
                npv += pmt / Math.pow((1 + r), i);
            }

            let difference = Math.abs(cash - npv);
            let diffFormatted = new Intl.NumberFormat('tr-TR').format(Math.round(difference)) + ' TL';

            if(npv < cash) {
                document.getElementById('resText').innerText = '✅ TAKSİTLE ALMAK DAHA KÂRLI';
                document.getElementById('resText').style.color = '#166534';
                document.getElementById('resDetail').innerText = 'Paranızı faizde tutarak taksit ödemek, peşin ödemeye göre size bugünün parasıyla ' + diffFormatted + ' kazandırır.';
            } else {
                document.getElementById('resText').innerText = '💰 PEŞİN ALMAK DAHA KÂRLI';
                document.getElementById('resText').style.color = '#b91c1c';
                document.getElementById('resDetail').innerText = 'Taksit farkı çok yüksek. Peşin alarak bugünün parasıyla ' + diffFormatted + ' tasarruf edersiniz.';
            }
        }
        document.addEventListener('DOMContentLoaded', calc);
    </script>
</body>
</html>
