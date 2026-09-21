<!DOCTYPE html>
<html lang="tr">
<head>
    <link rel="icon" href="/favicon.ico" type="image/x-icon">

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Güneş Paneli (GES) Hesaplayıcı | WORKSİTEM</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>:root { --bg:#f8fafc; --surface:#ffffff; --border:#e2e8f0; --primary:#2563eb; --primary-light:#eff6ff; --text-main:#0f172a; --text-muted:#64748b; --green:#059669; --transition:all 0.25s ease;} body { font-family:'Plus Jakarta Sans',sans-serif; background:var(--bg); color:var(--text-main); margin:0; padding-bottom:80px; } .navbar { display:flex; justify-content:space-between; align-items:center; padding:1rem 5%; background:var(--surface); border-bottom:1px solid var(--border); position:sticky; top:0; z-index:50;} .logo-wrapper { display:flex; align-items:center; gap:8px; text-decoration:none; } .logo-mark { background:var(--primary); color:#fff; font-weight:800; font-size:14px; padding:4px 10px; border-radius:6px; } .logo-text { font-weight:800; font-size:20px; color:var(--text-main); } .container { max-width:1200px; margin:40px auto; padding:0 4%; display:grid; grid-template-columns:1fr 1fr; gap:40px; } @media(max-width:992px) { .container { grid-template-columns:1fr; } } .form-card { background:var(--surface); border:1px solid var(--border); border-radius:16px; padding:30px; margin-bottom:20px; box-shadow:0 4px 6px -1px rgba(0,0,0,0.05); } h1 { font-size:2rem; font-weight:800; margin-bottom:10px; letter-spacing:-0.5px; } .subtitle { color:var(--text-muted); font-size:0.95rem; margin-bottom:30px; } .form-group { margin-bottom:20px; } .form-group label { display:block; font-size:0.85rem; font-weight:700; margin-bottom:8px; text-transform:uppercase; letter-spacing:0.5px; } .form-control { width:100%; padding:14px; border-radius:8px; background:var(--bg); border:1px solid var(--border); font-family:inherit; font-size:1rem; } .result-header { font-size:1.25rem; font-weight:800; color:var(--primary); margin-bottom:20px;} .res-item { display:flex; justify-content:space-between; padding:14px 0; border-bottom:1px dashed var(--border); font-size:0.95rem; font-weight:500; } .res-item strong { font-weight:800; font-size:1.1rem; } .res-item.total { border-bottom:none; margin-top:10px; padding-top:20px; border-top:2px solid var(--border); } .res-item.total strong { font-size:1.75rem; color:var(--primary); }</style>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body data-tool-slug="solar-calculator">
    <nav class="navbar"><a href="/" class="logo-wrapper"><img src="/brand/worksitem-logo.png" alt="Worksitem Logo" style="height: 35px; width: auto;"></a><a href="/araclar" style="font-weight: 800; color: var(--text-muted); text-decoration: none; font-size: 0.95rem; transition: color 0.2s;" onmouseover="this.style.color='var(--primary)'" onmouseout="this.style.color='var(--text-muted)'">◀ Tüm Araçlar</a></nav>
    <div class="container">
        <div>
            <h1>☀️ Güneş Paneli (GES) Hesabı</h1>
            <p class="subtitle">Aylık elektrik faturanızdan yola çıkarak çatı güneş enerjisi (GES) sistem ihtiyacınızı bulun.</p>
            <div class="form-card">
                <div class="form-group"><label>Aylık Ortalama Elektrik Faturası (TL)</label><input type="number" id="bill" class="form-control" value="1500" oninput="calc()"></div>
                <div class="form-group">
                    <label>Bölgesel Güneşlenme Süresi</label>
                    <select id="sun" class="form-control" onchange="calc()">
                        <option value="4.0">Karadeniz / Marmara (Ort. 4 Saat/Gün)</option>
                        <option value="4.5" selected>İç Anadolu / Ege (Ort. 4.5 Saat/Gün)</option>
                        <option value="5.5">Akdeniz / G.Doğu (Ort. 5.5 Saat/Gün)</option>
                    </select>
                </div>
            </div>
        </div>
        <div>
            <div class="form-card" style="border-color:var(--primary); position:sticky; top:100px;">
                <div class="result-header">SİSTEM ANALİZİ</div>
                <div class="res-item"><span>Aylık Tüketim (Tahmini)</span> <strong id="r_kwh">0 kWh</strong></div>
                <div class="res-item"><span>Önerilen Sistem Gücü</span> <strong id="r_kw">0 kW</strong></div>
                <div class="res-item"><span>Gereken Panel (400W)</span> <strong id="r_panel">0 Adet</strong></div>
                <div class="res-item total"><span>TAHMİNİ KURULUM MALİYETİ</span> <strong id="r_total">0 TL</strong></div>
                <x-tools.lead-cta service="gunes-enerjisi" />
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
        const P_ENERJI = @json($pricing['enerji'] ?? []);

        function calc() {
            let bill = parseFloat(document.getElementById('bill').value)||0;
            let sunHours = parseFloat(document.getElementById('sun').value);
            
            let kwhPrice = P_ENERJI['elektrik_kwh_fiyati'] || 2.25;
            let kwCost = P_ENERJI['ges_kw_maliyeti'] || 25000;

            let monthlyKwh = bill / kwhPrice;
            let dailyKwh = monthlyKwh / 30;
            
            // %20 sistem kayıp toleransı
            let reqKw = (dailyKwh / sunHours) * 1.20;
            if(reqKw < 1) reqKw = 1; // Minimum 1 kW sistem
            
            let panels = Math.ceil((reqKw * 1000) / 400); // 400W paneller varsayımı
            let totalCost = reqKw * kwCost;

            document.getElementById('r_kwh').innerText = Math.round(monthlyKwh) + ' kWh';
            document.getElementById('r_kw').innerText = reqKw.toFixed(1) + ' kW';
            document.getElementById('r_panel').innerText = panels + ' Adet';
            document.getElementById('r_total').innerText = new Intl.NumberFormat('tr-TR').format(Math.round(totalCost)) + ' TL';

            document.querySelector('.cta-btn').setAttribute('href', `/maliyet-hesapla?service=gunes-enerjisi&kw=${reqKw.toFixed(1)}`);
        } document.addEventListener('DOMContentLoaded', calc);
    </script>
    <script src="/js/tool-events.js" defer></script>
</body>
</html>
