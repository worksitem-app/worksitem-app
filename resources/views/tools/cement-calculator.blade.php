<!DOCTYPE html>
<html lang="tr">
<head>
    <link rel="icon" href="/favicon.ico" type="image/x-icon">

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Çimento ve Kum Harç Hesaplayıcı | WORKSİTEM</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>:root { --bg:#f8fafc; --surface:#ffffff; --border:#e2e8f0; --primary:#101318; --primary-light:#fff8e1; --text-main:#0f172a; --text-muted:#64748b; --green:#059669; --transition:all 0.25s ease;} body { font-family:'Inter',sans-serif; background:var(--bg); color:var(--text-main); margin:0; padding-bottom:80px; } .navbar { display:flex; justify-content:space-between; align-items:center; padding:1rem 5%; background:var(--surface); border-bottom:1px solid var(--border); position:sticky; top:0; z-index:50;} .logo-wrapper { display:flex; align-items:center; gap:8px; text-decoration:none; } .logo-mark { background:var(--primary); color:#fff; font-weight:800; font-size:14px; padding:4px 10px; border-radius:6px; } .logo-text { font-weight:800; font-size:20px; color:var(--text-main); } .container { max-width:1200px; margin:40px auto; padding:0 4%; display:grid; grid-template-columns:1fr 1fr; gap:40px; } @media(max-width:992px) { .container { grid-template-columns:1fr; } } .form-card { background:var(--surface); border:1px solid var(--border); border-radius:16px; padding:30px; margin-bottom:20px; box-shadow:0 4px 6px -1px rgba(0,0,0,0.05); } h1 { font-size:2rem; font-weight:800; margin-bottom:10px; letter-spacing:-0.5px; } .subtitle { color:var(--text-muted); font-size:0.95rem; margin-bottom:30px; } .form-group { margin-bottom:20px; } .form-group label { display:block; font-size:0.85rem; font-weight:700; margin-bottom:8px; text-transform:uppercase; letter-spacing:0.5px; } .form-control { width:100%; padding:14px; border-radius:8px; background:var(--bg); border:1px solid var(--border); font-family:inherit; font-size:1rem; } .grid-2 { display:grid; grid-template-columns:1fr 1fr; gap:15px; } .result-header { font-size:1.25rem; font-weight:800; color:var(--primary); margin-bottom:20px;} .res-item { display:flex; justify-content:space-between; padding:14px 0; border-bottom:1px dashed var(--border); font-size:0.95rem; font-weight:500; } .res-item strong { font-weight:800; font-size:1.1rem; } .res-item.total { border-bottom:none; margin-top:10px; padding-top:20px; border-top:2px solid var(--border); } .res-item.total strong { font-size:1.75rem; color:var(--primary); }</style>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body data-tool-slug="cement-calculator">
    <nav class="navbar"><a href="/" class="logo-wrapper"><img src="/brand/worksitem-logo.png" alt="Worksitem Logo" style="height: 35px; width: auto;"></a><a href="/araclar" style="font-weight: 800; color: var(--text-muted); text-decoration: none; font-size: 0.95rem; transition: color 0.2s;" onmouseover="this.style.color='var(--primary)'" onmouseout="this.style.color='var(--text-muted)'">◀ Tüm Araçlar</a></nav>
    <div class="container">
        <div>
            <h1>Çimento & Kum Hesabı</h1>
            <p class="subtitle">Kara sıva veya şap için gereken torba çimento ve kum tonajını belirleyin.</p>
            <div class="form-card">
                <div class="form-group"><label>Uygulama Alanı (m²)</label><input type="number" id="area" class="form-control" value="100" oninput="calc()"></div>
                <div class="form-group"><label>Harç Kalınlığı (cm)</label><input type="number" id="t" class="form-control" value="3" oninput="calc()"></div>
                <div class="form-group">
                    <label>Harç Dozu (Karışım Oranı)</label>
                    <select id="doz" class="form-control" onchange="calc()">
                        <option value="250" selected>250 Doz (Kara Sıva ve Normal Şap İçin İdeal)</option>
                        <option value="350">350 Doz (Daha Sert/Betonumsu Yüzeyler İçin)</option>
                    </select>
                </div>
            </div>
        </div>
        <div>
            <div class="form-card" style="border-color:var(--primary); position:sticky; top:100px;">
                <div class="result-header">MALZEME METRAJI</div>
                <div class="res-item"><span>Toplam Harç Hacmi</span> <strong id="r_vol">0 m³</strong></div>
                <div class="res-item"><span>Çimento Torba Sayısı (50 Kg)</span> <strong id="r_cem">0 Torba</strong></div>
                <div class="res-item"><span>Gereken İnşaat Kumu</span> <strong id="r_sand">0 Ton</strong></div>
                <div class="res-item total"><span>TAHMİNİ MALZEME MALİYETİ</span> <strong id="r_cost">0 TL</strong></div>
                <x-tools.lead-cta service="kaba-insaat" />
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
        const P_HARC = @json($pricing['harc'] ?? []);

        function calc() {
            let area = parseFloat(document.getElementById('area').value)||0;
            let t = parseFloat(document.getElementById('t').value)||0;
            let doz = parseInt(document.getElementById('doz').value);
            
            let vol = area * (t / 100);
            
            // Çimento Kg = Hacim x Dozaj
            let cemKg = vol * doz;
            let cemBags = Math.ceil(cemKg / 50);
            
            // Kum Ton = Hacim x 1.6 (Ortalama kum özgül ağırlığı)
            let sandTon = vol * 1.6;
            
            let p_cem = P_HARC['cimento_50kg_torba'] || 220;
            let p_sand = P_HARC['kum_ton'] || 450;
            
            let totalCost = (cemBags * p_cem) + (sandTon * p_sand);

            document.getElementById('r_vol').innerText = vol.toFixed(2) + ' m³';
            document.getElementById('r_cem').innerText = cemBags + ' Torba';
            document.getElementById('r_sand').innerText = sandTon.toFixed(2) + ' Ton';
            document.getElementById('r_cost').innerText = new Intl.NumberFormat('tr-TR').format(Math.round(totalCost)) + ' TL';

            document.querySelector('.cta-btn').setAttribute('href', `/maliyet-hesapla?service=kaba-insaat&area=${area}`);
        } document.addEventListener('DOMContentLoaded', calc);
    </script>
    <script src="/js/tool-events.js" defer></script>
</body>
</html>
