<!DOCTYPE html>
<html lang="tr">
<head>
    <link rel="icon" href="/favicon.ico" type="image/x-icon">

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aylık Enerji Tüketim Hesaplayıcı | WORKSİTEM</title>
    <meta name="description" content="Elektrikli cihazların kullanım süresi ve gücüne göre aylık kWh enerji tüketimini ve elektrik faturasına yaklaşık etkisini hesaplayın.">
    <link rel="canonical" href="https://worksitem.com/araclar/enerji-tuketim-hesaplama">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>:root { --bg:#f8fafc; --surface:#ffffff; --border:#e2e8f0; --primary:#101318; --primary-light:#fff8e1; --text-main:#0f172a; --text-muted:#64748b; --green:#059669; --transition:all 0.25s ease;} body { font-family:'Inter',sans-serif; background:var(--bg); color:var(--text-main); margin:0; padding-bottom:80px; } .navbar { display:flex; justify-content:space-between; align-items:center; padding:1rem 5%; background:var(--surface); border-bottom:1px solid var(--border); position:sticky; top:0; z-index:50;} .logo-wrapper { display:flex; align-items:center; gap:8px; text-decoration:none; } .logo-mark { background:var(--primary); color:#fff; font-weight:800; font-size:14px; padding:4px 10px; border-radius:6px; } .logo-text { font-weight:800; font-size:20px; color:var(--text-main); } .container { max-width:1200px; margin:40px auto; padding:0 4%; display:grid; grid-template-columns:1fr 1fr; gap:40px; } @media(max-width:992px) { .container { grid-template-columns:1fr; } } .form-card { background:var(--surface); border:1px solid var(--border); border-radius:16px; padding:30px; margin-bottom:20px; box-shadow:0 4px 6px -1px rgba(0,0,0,0.05); } h1 { font-size:2rem; font-weight:800; margin-bottom:10px; letter-spacing:-0.5px; } .subtitle { color:var(--text-muted); font-size:0.95rem; margin-bottom:30px; } .form-group { margin-bottom:20px; } .form-group label { display:block; font-size:0.85rem; font-weight:700; margin-bottom:8px; text-transform:uppercase; letter-spacing:0.5px; } .form-control { width:100%; padding:14px; border-radius:8px; background:var(--bg); border:1px solid var(--border); font-family:inherit; font-size:1rem; } .grid-2 { display:grid; grid-template-columns:1fr 1fr; gap:15px; } .result-header { font-size:1.25rem; font-weight:800; color:var(--primary); margin-bottom:20px;} .res-item { display:flex; justify-content:space-between; padding:14px 0; border-bottom:1px dashed var(--border); font-size:0.95rem; font-weight:500; } .res-item strong { font-weight:800; font-size:1.1rem; } .res-item.total { border-bottom:none; margin-top:10px; padding-top:20px; border-top:2px solid var(--border); } .res-item.total strong { font-size:1.75rem; color:var(--primary); }</style>
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
<body class="wc" data-tool-slug="energy-calculator">
    <x-site.contech-header />
    <div class="container">
        <div>
            <x-calc.hero title="Enerji tüketimi ve elektrik maliyeti hesaplama" crumb="Enerji tüketimi">Elektrikli cihazlarınızın kWh tüketimini ve faturanıza olan etkisini hesaplayın.</x-calc.hero>
            <div class="form-card">
                <div class="form-group">
                    <label>Hazır Cihaz Seçimi</label>
                    <select id="preset" class="form-control" onchange="setWatt()">
                        <option value="0" selected>Özel Watt Gireceğim</option>
                        <option value="1500">Klima (12000 BTU Inverter - 1500W)</option>
                        <option value="2500">Elektrikli Isıtıcı / Şofben (2500W)</option>
                        <option value="150">Buzdolabı (A++ - Ortalama 150W)</option>
                        <option value="2000">Çamaşır/Bulaşık Makinesi (2000W)</option>
                        <option value="100">LED Aydınlatma Seti (100W)</option>
                    </select>
                </div>
                <div class="form-group"><label>Cihazın Gücü (Watt)</label><input type="number" id="watt" class="form-control" value="1500" oninput="calc()"></div>
                <div class="grid-2">
                    <div class="form-group"><label>Günlük Çalışma (Saat)</label><input type="number" id="hours" class="form-control" value="8" step="0.5" oninput="calc()"></div>
                    <div class="form-group"><label>Kullanım Günü (Ayda)</label><input type="number" id="days" class="form-control" value="30" oninput="calc()"></div>
                </div>
            </div>
        </div>
        <div>
            <div class="form-card" style="border-color:var(--primary); position:sticky; top:100px;" data-wc-result>
                <div class="result-header">FATURA ETKİSİ</div>
                <div class="res-item"><span>Günlük Tüketim</span> <strong id="r_daily">0 kWh</strong></div>
                <div class="res-item"><span>Aylık Tüketim</span> <strong id="r_monthly">0 kWh</strong></div>
                <div class="res-item total"><span>AYLIK FATURA YÜKÜ</span> <strong id="r_cost" data-wc-price>0 TL</strong></div>
                <x-tools.lead-cta service="enerji" />
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
        const P_ENERJI = @json($pricing['enerji'] ?? []);

        function setWatt() {
            let p = parseInt(document.getElementById('preset').value);
            if(p > 0) { document.getElementById('watt').value = p; calc(); }
        }

        function calc() {
            let watt = parseFloat(document.getElementById('watt').value)||0;
            let hours = parseFloat(document.getElementById('hours').value)||0;
            let days = parseFloat(document.getElementById('days').value)||0;
            
            let kw = watt / 1000;
            let dailyKwh = kw * hours;
            let monthlyKwh = dailyKwh * days;
            
            let kwhPrice = P_ENERJI['elektrik_kwh_fiyati'] || 2.25;
            let totalCost = monthlyKwh * kwhPrice;

            document.getElementById('r_daily').innerText = dailyKwh.toFixed(2) + ' kWh';
            document.getElementById('r_monthly').innerText = monthlyKwh.toFixed(2) + ' kWh';
            document.getElementById('r_cost').innerText = new Intl.NumberFormat('tr-TR').format(Math.round(totalCost)) + ' TL';

            document.querySelector('.cta-btn').setAttribute('href', `/maliyet-hesapla?service=enerji&kwh=${monthlyKwh.toFixed(1)}`);
        } document.addEventListener('DOMContentLoaded', calc);
    </script>
<x-calc.kit />
    <x-site.contech-footer />
    <script src="/js/tool-events.js" defer></script>
</body>
</html>
