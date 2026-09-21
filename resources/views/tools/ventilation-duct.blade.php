<!DOCTYPE html><html lang="tr"><head>
    <link rel="icon" href="/favicon.ico" type="image/x-icon">
<meta charset="UTF-8"><title>Havalandırma Kanalı | WORKSİTEM</title><link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet"><style>body{font-family:'Inter',sans-serif;background:#fdfdfd;color:#111827;margin:0;padding-bottom:60px;} .container{max-width:800px;margin:40px auto;padding:30px;background:#fff;border:1px solid #e5e7eb;border-radius:16px;box-shadow:0 4px 6px rgba(0,0,0,0.05);} h1{font-size:24px;font-weight:800;margin-bottom:10px;color:#1e3a8a;} .form-group{margin-bottom:20px;} label{display:block;font-size:13px;font-weight:700;color:#6b7280;margin-bottom:8px;text-transform:uppercase;} input,select{width:100%;padding:14px;border:1px solid #e5e7eb;border-radius:8px;font-family:inherit;font-size:15px;} .grid-2{display:grid;grid-template-columns:1fr 1fr;gap:15px;} .result-box{background:#f0fdf4;border:2px solid #bbf7d0;padding:20px;border-radius:12px;margin-top:20px;text-align:center;} .result-val{font-size:32px;font-weight:800;color:#166534;} .cta{display:block;width:100%;padding:16px;background:#1d4ed8;color:#fff;text-align:center;font-weight:800;text-decoration:none;border-radius:8px;margin-top:20px; transition:all 0.2s;} .cta:hover{opacity:0.9; transform:translateY(-2px);}</style>    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body data-tool-slug="ventilation-duct"><div class="container"><h1>💨 Havalandırma Kanalı Kesit Alanı</h1><p style="color:#6b7280; margin-bottom:30px;">Sistemin emeceği hava debisine ve menfez hızına göre yapılması gereken hava kanalının kesit alanını (m²) hesaplayın.</p>
<div class="grid-2"><div class="form-group"><label>Hava Debisi (m³/h)</label><input type="number" id="flow" value="5000" oninput="calc()"></div><div class="form-group"><label>Hava Hızı (m/s)</label><input type="number" id="vel" value="6" oninput="calc()"></div></div>
<div class="result-box"><div style="font-size:14px; color:#15803d; font-weight:600;">GEREKEN NET KESİT ALANI</div><div class="result-val" id="res">0 m²</div></div>
<a href="/maliyet-hesapla?service=havalandirma" class="cta">HVAC İklimlendirme İçin Keşif Al →</a></div>

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
        // ------------------------------------------------function calc() { let f = parseFloat(document.getElementById('flow').value)||0; let v = parseFloat(document.getElementById('vel').value)||1; let area = f / (3600 * v); document.getElementById('res').innerText = area.toFixed(3) + ' m²'; } document.addEventListener('DOMContentLoaded', calc);</script>    <script src="/js/tool-events.js" defer></script>
</body></html>
