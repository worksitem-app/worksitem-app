<!DOCTYPE html>
<html lang="tr">
<head>
    <link rel="icon" href="/favicon.ico" type="image/x-icon">
    <meta property="og:image" content="https://worksitem.com/brand/og-image.png">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:image" content="https://worksitem.com/brand/og-image.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="manifest" href="/site.webmanifest">
    <meta name="theme-color" content="#ffffff">
<meta charset="UTF-8"><title>Yağmur Borusu Seçimi | WORKSİTEM</title>
    <meta name="description" content="Çatı yağmur suyu tahliyesi için gerekli yağmur borusu çapını ve yaklaşık maliyeti hesaplayın, uygun iniş borusu seçimini planlayın.">
    <link rel="canonical" href="https://worksitem.com/araclar/yagmur-borusu-hesabi"><link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet"><style>:root { --bg: #f8fafc; --card-bg: #ffffff; --border: #e2e8f0; --primary: #2563eb; --text: #0f172a; --muted: #64748b; --cyan: #0ea5e9; --green: #059669; } body { font-family: 'Inter', sans-serif; background: var(--bg); color: var(--text); margin: 0; padding: 0; } .container { max-width: 1200px; margin: 40px auto; padding: 0 4%; display: grid; grid-template-columns: 1fr 1fr; gap: 40px; } @media(max-width: 992px) { .container { grid-template-columns: 1fr; } } .form-card { background: var(--card-bg); border: 1px solid var(--border); border-radius: 12px; padding: 30px; margin-bottom: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);} h1 { font-family: 'Bebas Neue'; font-size: 38px; color: var(--text); margin-bottom: 10px; } .form-group { margin-bottom: 20px; } .form-group label { display: block; font-size: 13px; font-weight: 700; color: var(--muted); margin-bottom: 8px; text-transform: uppercase; } .form-control { width: 100%; padding: 12px; border-radius: 6px; background: #f8fafc; border: 1px solid var(--border); color: var(--text); font-family: inherit; } .res-item { display: flex; justify-content: space-between; padding: 12px 0; border-bottom: 1px dashed var(--border); font-size: 14px; } .res-item.total { border-bottom: none; font-size: 24px; font-weight: 800; color: var(--cyan); margin-top: 15px; } .ai-box { background: #e0f2fe; border: 1px solid #bae6fd; border-radius: 8px; padding: 20px; margin-top: 20px; }</style>    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body data-tool-slug="rainpipe-calculator">
    <x-site.contech-header />
    <div class="container">
        <div><h1>💧 Yağmur Borusu Çap & Maliyeti</h1>
            <div class="form-card">
                <div class="form-group"><label>Çatı İzdüşüm Alanı (m²)</label><input type="number" id="area" class="form-control" value="150" oninput="calc()"></div>
                <div class="form-group"><label>Bina Yüksekliği (m)</label><input type="number" id="h" class="form-control" value="12" oninput="calc()"></div>
                <div class="form-group"><label>İniş Borusu Adedi</label><input type="number" id="count" class="form-control" value="2" oninput="calc()"></div>
            </div>
        </div>
        <div>
            <div class="form-card" style="border-color:var(--cyan); position:sticky; top:20px;">
                <div style="font-family:'Bebas Neue'; font-size:24px; color:var(--cyan); margin-bottom:15px;">📊 HİDROLİK SONUÇ</div>
                <div class="res-item"><span>Boru Başına Düşen Alan</span> <strong id="r_per">0 m²</strong></div>
                <div class="res-item total"><span>ÖNERİLEN BORU ÇAPI</span> <span id="r_dia">0 mm</span></div>
                <div class="res-item"><span>Boru Birim Fiyatı (2026 ÇŞB)</span> <strong id="r_price">0 TL/m</strong></div>
                <div class="res-item"><span>Toplam Boru Maliyeti</span> <strong id="r_total">0 TL</strong></div>
                <div class="ai-box"><div style="color:#0369a1; font-family:'Bebas Neue'; font-size:22px;">🤖 Tesisat Notu</div><ul id="ai_notes" style="font-size:13px; color:#0f172a; margin:0; padding-left:15px;"></ul></div>
            </div>
        </div>
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
            let a = parseFloat(document.getElementById('area').value)||0; let h = parseFloat(document.getElementById('h').value)||0; let c = parseInt(document.getElementById('count').value)||1;
            let per = a / c;
            let dia = 70; let price = 242.69; // ÇŞB 15.315.1001
            if(per > 40 && per <= 80) { dia = 100; price = 315.65; } // ÇŞB 15.315.1002
            else if(per > 80) { dia = 125; price = 366.40; } // ÇŞB 15.315.1003
            
            let total = price * h * c;
            
            document.getElementById('r_per').innerText = per.toFixed(1) + ' m²'; document.getElementById('r_dia').innerText = 'Ø' + dia + ' mm PVC'; document.getElementById('r_price').innerText = price.toFixed(2) + ' TL/m'; document.getElementById('r_total').innerText = Math.round(total) + ' TL';
            let notesBox = document.getElementById('ai_notes'); notesBox.innerHTML = '';
            notesBox.innerHTML += '<li>Pratik hidrolik kuralı olarak borunun her 1 cm² kesit alanı, 1 m² çatı suyunu tahliye edebilir. Buna göre boru başına '+per.toFixed(1)+' m² düştüğü için Ø'+dia+' boru seçilmiştir.</li>';
            if(per > 120) notesBox.innerHTML += '<li style="color:#b91c1c; font-weight:bold;">Uyarı: Boru sayısı yetersiz! Lütfen binaya daha fazla iniş borusu (oluk) ekleyin, aksi takdirde aşırı yağışlarda taşma yaşanır.</li>';
        } document.addEventListener('DOMContentLoaded', calc);
    </script>
    <x-site.contech-footer />
    <script src="/js/tool-events.js" defer></script>
</body>
</html>
