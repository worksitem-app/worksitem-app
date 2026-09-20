<!DOCTYPE html>
<html lang="tr">
<head>
    <link rel="icon" href="/favicon.ico" type="image/x-icon">

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Su Tesisat Boru & Fitting Hesabı | WORKSİTEM</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>:root { --bg:#f8fafc; --surface:#ffffff; --border:#e2e8f0; --primary:#2563eb; --primary-light:#eff6ff; --text-main:#0f172a; --text-muted:#64748b; --green:#059669; --transition:all 0.25s ease;} body { font-family:'Plus Jakarta Sans',sans-serif; background:var(--bg); color:var(--text-main); margin:0; padding-bottom:80px; } .navbar { display:flex; justify-content:space-between; align-items:center; padding:1rem 5%; background:var(--surface); border-bottom:1px solid var(--border); position:sticky; top:0; z-index:50;} .logo-wrapper { display:flex; align-items:center; gap:8px; text-decoration:none; } .logo-mark { background:var(--primary); color:#fff; font-weight:800; font-size:14px; padding:4px 10px; border-radius:6px; } .logo-text { font-weight:800; font-size:20px; color:var(--text-main); } .container { max-width:1200px; margin:40px auto; padding:0 4%; display:grid; grid-template-columns:1fr 1fr; gap:40px; } @media(max-width:992px) { .container { grid-template-columns:1fr; } } .form-card { background:var(--surface); border:1px solid var(--border); border-radius:16px; padding:30px; margin-bottom:20px; box-shadow:0 4px 6px -1px rgba(0,0,0,0.05); } h1 { font-size:2rem; font-weight:800; margin-bottom:10px; letter-spacing:-0.5px; } .subtitle { color:var(--text-muted); font-size:0.95rem; margin-bottom:30px; } .form-group { margin-bottom:20px; } .form-group label { display:block; font-size:0.85rem; font-weight:700; margin-bottom:8px; text-transform:uppercase; letter-spacing:0.5px; } .form-control { width:100%; padding:14px; border-radius:8px; background:var(--bg); border:1px solid var(--border); font-family:inherit; font-size:1rem; } .grid-2 { display:grid; grid-template-columns:1fr 1fr; gap:15px; } .result-header { font-size:1.25rem; font-weight:800; color:var(--primary); margin-bottom:20px;} .res-item { display:flex; justify-content:space-between; padding:14px 0; border-bottom:1px dashed var(--border); font-size:0.95rem; font-weight:500; } .res-item strong { font-weight:800; font-size:1.1rem; } .res-item.total { border-bottom:none; margin-top:10px; padding-top:20px; border-top:2px solid var(--border); } .res-item.total strong { font-size:1.75rem; color:var(--primary); }</style>
</head>
<body>
    <nav class="navbar"><a href="/" class="logo-wrapper"><img src="/brand/worksitem-logo.png" alt="Worksitem Logo" style="height: 35px; width: auto;"></a><a href="/araclar" style="font-weight: 800; color: var(--text-muted); text-decoration: none; font-size: 0.95rem; transition: color 0.2s;" onmouseover="this.style.color='var(--primary)'" onmouseout="this.style.color='var(--text-muted)'">◀ Tüm Araçlar</a></nav>
    <div class="container">
        <div>
            <h1>🚰 Su Tesisat Sarfiyatı</h1>
            <p class="subtitle">Islak hacim (Banyo/Mutfak) sayısına göre tesisat boru metrajınızı ve fitting (bağlantı parçası) ihtiyacınızı hesaplayın.</p>
            <div class="form-card">
                <div class="form-group"><label>Daire/Ev Büyüklüğü (m²)</label><input type="number" id="area" class="form-control" value="100" oninput="calc()"></div>
                <div class="grid-2">
                    <div class="form-group"><label>Banyo Sayısı</label><input type="number" id="bath" class="form-control" value="1" oninput="calc()"></div>
                    <div class="form-group"><label>Mutfak Sayısı</label><input type="number" id="kit" class="form-control" value="1" oninput="calc()"></div>
                </div>
            </div>
        </div>
        <div>
            <div class="form-card" style="border-color:var(--primary); position:sticky; top:100px;">
                <div class="result-header">MALZEME METRAJI</div>
                <div class="res-item"><span>Temiz Su Borusu (PPRC)</span> <strong id="r_pprc">0 Metre</strong></div>
                <div class="res-item"><span>Pis Su Borusu (PVC Pimaş)</span> <strong id="r_pvc">0 Metre</strong></div>
                <div class="res-item"><span>Bağlantı Elemanı (Fitting)</span> <strong id="r_fit">0 Adet</strong></div>
                <div class="res-item total"><span>TAHMİNİ MALZEME MALİYETİ</span> <strong id="r_cost">0 TL</strong></div>
                <x-tools.lead-cta service="su-tesisati" />
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
        const P_TESISAT = @json($pricing['tesisat'] ?? []);

        function calc() {
            let area = parseFloat(document.getElementById('area').value)||0;
            let bath = parseInt(document.getElementById('bath').value)||0;
            let kit = parseInt(document.getElementById('kit').value)||0;
            
            let wetRooms = bath + kit;
            
            // Pratik Şantiye Kuralı:
            // 1 Islak hacim ortalama 20m temiz su (sıcak/soğuk), 12m pis su borusu ve 35 fitting gerektirir.
            // Daire büyüklüğü (ana hattan çekişler için) metrajı etkiler.
            let pprc = (wetRooms * 20) + (area * 0.1);
            let pvc = (wetRooms * 12) + (area * 0.05);
            let fittings = (wetRooms * 35);
            
            let cost_pprc = pprc * (P_TESISAT['pprc_boru_m'] || 45.50);
            let cost_pvc = pvc * (P_TESISAT['pvc_boru_m'] || 85.00);
            let cost_fit = fittings * (P_TESISAT['fitting_adet'] || 15.00);
            let total = cost_pprc + cost_pvc + cost_fit;

            document.getElementById('r_pprc').innerText = Math.round(pprc) + ' Metre';
            document.getElementById('r_pvc').innerText = Math.round(pvc) + ' Metre';
            document.getElementById('r_fit').innerText = Math.round(fittings) + ' Adet';
            document.getElementById('r_cost').innerText = new Intl.NumberFormat('tr-TR').format(Math.round(total)) + ' TL';

            document.querySelector('.cta-btn').setAttribute('href', `/maliyet-hesapla?service=su-tesisati&area=${area}`);
        } document.addEventListener('DOMContentLoaded', calc);
    </script>
</body>
</html>
