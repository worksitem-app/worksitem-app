<!DOCTYPE html>
<html lang="tr">
<head>
    <link rel="icon" href="/favicon.ico" type="image/x-icon">

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Banyo Tadilat Maliyeti 2026 | WORKSİTEM</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root { --bg:#f8fafc; --surface:#ffffff; --border:#e2e8f0; --primary:#2563eb; --primary-light:#eff6ff; --text-main:#0f172a; --text-muted:#64748b; --green:#059669; --transition:all 0.25s ease;}
        body { font-family:'Plus Jakarta Sans',sans-serif; background:var(--bg); color:var(--text-main); margin:0; padding-bottom:80px; }
        .navbar { display:flex; justify-content:space-between; align-items:center; padding:1rem 5%; background:var(--surface); border-bottom:1px solid var(--border); position:sticky; top:0; z-index:50;}
        .logo-wrapper { display:flex; align-items:center; gap:8px; text-decoration:none; }
        .logo-mark { background:var(--primary); color:#fff; font-weight:800; font-size:14px; padding:4px 10px; border-radius:6px; }
        .logo-text { font-weight:800; font-size:20px; color:var(--text-main); }
        .container { max-width:1200px; margin:40px auto; padding:0 4%; display:grid; grid-template-columns:1fr 1fr; gap:40px; }
        @media(max-width:992px) { .container { grid-template-columns:1fr; } }
        .form-card { background:var(--surface); border:1px solid var(--border); border-radius:16px; padding:30px; margin-bottom:20px; box-shadow:0 4px 6px -1px rgba(0,0,0,0.05); }
        h1 { font-size:2rem; font-weight:800; margin-bottom:10px; letter-spacing:-0.5px; }
        .subtitle { color:var(--text-muted); font-size:0.95rem; margin-bottom:30px; }
        .form-group { margin-bottom:20px; }
        .form-group label { display:block; font-size:0.85rem; font-weight:700; margin-bottom:8px; text-transform:uppercase; letter-spacing:0.5px; }
        .form-control { width:100%; padding:14px; border-radius:8px; background:var(--bg); border:1px solid var(--border); font-family:inherit; font-size:1rem; }
        .grid-2 { display:grid; grid-template-columns:1fr 1fr; gap:15px; }
        .result-header { font-size:1.25rem; font-weight:800; color:var(--primary); margin-bottom:20px;}
        .res-item { display:flex; justify-content:space-between; padding:14px 0; border-bottom:1px dashed var(--border); font-size:0.95rem; font-weight:500; }
        .res-item strong { font-weight:800; font-size:1.1rem; }
        .res-item.total { border-bottom:none; margin-top:10px; padding-top:20px; border-top:2px solid var(--border); }
        .res-item.total strong { font-size:1.75rem; color:var(--primary); }
    </style>
</head>
<body>
    <nav class="navbar"><a href="/" class="logo-wrapper"><img src="/brand/worksitem-logo.png" alt="Worksitem Logo" style="height: 35px; width: auto;"></a><a href="/araclar" style="font-weight: 800; color: var(--text-muted); text-decoration: none; font-size: 0.95rem; transition: color 0.2s;" onmouseover="this.style.color='var(--primary)'" onmouseout="this.style.color='var(--text-muted)'">◀ Tüm Araçlar</a></nav>
    <div class="container">
        <div>
            <h1>🚿 Banyo Tadilat Maliyeti</h1>
            <p class="subtitle">Kırım, su yalıtımı, seramik ve vitrifiye montajı dahil bütçenizi çıkarın.</p>
            <div class="form-card">
                <div class="grid-2">
                    <div class="form-group"><label>Banyo Eni (m)</label><input type="number" id="w" class="form-control" value="2" step="0.1" oninput="calc()"></div>
                    <div class="form-group"><label>Banyo Boyu (m)</label><input type="number" id="l" class="form-control" value="2.5" step="0.1" oninput="calc()"></div>
                </div>
                <div class="form-group"><label>Tavan Yüksekliği (m)</label><input type="number" id="h" class="form-control" value="2.8" step="0.1" oninput="calc()"></div>
                <div class="form-group">
                    <label>Eski Fayanslar Kırılacak Mı?</label>
                    <select id="demolition" class="form-control" onchange="calc()">
                        <option value="1" selected>Evet, Fayanslar Kırılarak Sökülecek</option>
                        <option value="0">Hayır, Üzerine Yapıştırılacak</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Su Yalıtımı ve Tesisat</label>
                    <select id="plumbing" class="form-control" onchange="calc()">
                        <option value="komple_tesisat_degisimi" selected>Komple Yeni Yalıtım ve Tesisat</option>
                        <option value="yuzeysel_tesisat">Sadece Yüzeysel Yenileme</option>
                    </select>
                </div>
            </div>
        </div>
        <div>
            <div class="form-card" style="border-color:var(--primary); position:sticky; top:100px;">
                <div class="result-header">BÜTÇE ANALİZİ</div>
                <div class="res-item"><span>Yıkım & Moloz</span> <strong id="r_demo">0 TL</strong></div>
                <div class="res-item"><span>Su Yalıtımı</span> <strong id="r_water">0 TL</strong></div>
                <div class="res-item"><span>Seramik (Zemin+Duvar)</span> <strong id="r_tile">0 TL</strong></div>
                <div class="res-item"><span>Tesisat & Vitrifiye</span> <strong id="r_plumb">0 TL</strong></div>
                <div class="res-item total"><span>ANAHTAR TESLİM</span> <strong id="r_total">0 TL</strong></div>
                
                <x-tools.lead-cta service="banyo-tadilat" />
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
        const P_BANYO = @json($pricing['banyo'] ?? []);
        const P_SERAMIK = @json($pricing['seramik'] ?? []);

        function calc() {
            let w = parseFloat(document.getElementById('w').value)||0; 
            let l = parseFloat(document.getElementById('l').value)||0; 
            let h = parseFloat(document.getElementById('h').value)||0;
            let demo = parseInt(document.getElementById('demolition').value); 
            let plumbKey = document.getElementById('plumbing').value;

            let floorArea = w * l; 
            let wallArea = 2 * h * (w + l) - 1.5; // kapı payı
            let totalArea = floorArea + wallArea;
            
            let p_demo = demo ? (P_SERAMIK['kirim_sokum_m2'] || 347.26) : 0; 
            
            // Yalıtım Sadece Zemine ve Duvarın bir kısmına (1.5 çarpan)
            let p_water = (plumbKey === 'komple_tesisat_degisimi') ? (P_BANYO['su_yalitimi_ciftkat_m2'] || 605.69) : 0;
            let c_water = floorArea * p_water * 1.5; 
            
            let c_demo = totalArea * p_demo;
            let c_tile = (wallArea * (P_SERAMIK['standart_duvar_m2']||956.65)) + (floorArea * (P_SERAMIK['silli_porselen_m2']||1041.36));
            
            let vitrifiyeMontaj = floorArea * (P_BANYO['vitrifiye_montaj_m2'] || 2500);
            let c_plumb = (P_BANYO[plumbKey] || 15000) + vitrifiyeMontaj;

            let total = c_demo + c_water + c_tile + c_plumb;

            document.getElementById('r_demo').innerText = new Intl.NumberFormat('tr-TR').format(Math.round(c_demo)) + ' TL';
            document.getElementById('r_water').innerText = new Intl.NumberFormat('tr-TR').format(Math.round(c_water)) + ' TL';
            document.getElementById('r_tile').innerText = new Intl.NumberFormat('tr-TR').format(Math.round(c_tile)) + ' TL';
            document.getElementById('r_plumb').innerText = new Intl.NumberFormat('tr-TR').format(Math.round(c_plumb)) + ' TL';
            document.getElementById('r_total').innerText = new Intl.NumberFormat('tr-TR').format(Math.round(total)) + ' TL';
            
            let ctaUrl = `/maliyet-hesapla?service=banyo-tadilat&area=${floorArea}`;
            document.querySelector('.cta-btn').setAttribute('href', ctaUrl);
        } document.addEventListener('DOMContentLoaded', calc);
    </script>
</body>
</html>
