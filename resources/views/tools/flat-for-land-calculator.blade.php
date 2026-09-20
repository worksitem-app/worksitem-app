<!DOCTYPE html>
<html lang="tr">
<head>
    <link rel="icon" href="/favicon.ico" type="image/x-icon">

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kat Karşılığı İnşaat Fizibilitesi | WORKSİTEM</title>
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
            <h1>🤝 Kat Karşılığı Analizi</h1>
            <p class="subtitle">Arsa sahibi ile müteahhit arasındaki paylaşıma göre kârlılığı simüle edin.</p>
            <div class="form-card">
                <div class="grid-2">
                    <div class="form-group"><label>Arsa Alanı (m²)</label><input type="number" id="area" class="form-control" value="1000" oninput="calc()"></div>
                    <div class="form-group"><label>Emsal (KAKS)</label><input type="number" id="kaks" class="form-control" value="1.5" step="0.1" oninput="calc()"></div>
                </div>
                <div class="form-group"><label>Arsa Sahibi Payı (%)</label><input type="number" id="share" class="form-control" value="50" oninput="calc()"></div>
                <div class="grid-2">
                    <div class="form-group">
                        <label>İnşaat Maliyeti (ÇŞB)</label>
                        <select id="cls" class="form-control" onchange="calc()">
                            <option value="sinif_3b_m2">3B Sınıfı (Standart)</option>
                            <option value="sinif_4a_m2" selected>4A Sınıfı (Lüks)</option>
                        </select>
                    </div>
                    <div class="form-group"><label>Bölge Satış Fiyatı (TL/m²)</label><input type="number" id="sell" class="form-control" value="45000" oninput="calc()"></div>
                </div>
            </div>
        </div>
        <div>
            <div class="form-card" style="border-color:var(--primary); position:sticky; top:100px;">
                <div class="result-header">FİZİBİLİTE SONUCU</div>
                <div class="res-item"><span>Müteahhit Payı (Satılabilir)</span> <strong id="r_mut">0 m²</strong></div>
                <div class="res-item"><span>Toplam İnşaat Maliyeti</span> <strong id="r_cost" style="color:#ef4444;">0 TL</strong></div>
                <div class="res-item"><span>Beklenen Satış Geliri</span> <strong id="r_rev">0 TL</strong></div>
                <div class="res-item total"><span>MÜTEAHHİT NET KÂR</span> <strong id="r_profit">0 TL</strong></div>
                <div style="text-align:right; font-size:0.875rem; color:var(--text-muted);">Kârlılık Oranı: <strong id="r_margin">0</strong>%</div>
                
                <x-tools.lead-cta service="kat-karsiligi" />
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
        const P_BINA = @json($pricing['bina_maliyeti'] ?? []);

        function calc() {
            let area = parseFloat(document.getElementById('area').value)||0; 
            let kaks = parseFloat(document.getElementById('kaks').value)||0; 
            let share = parseFloat(document.getElementById('share').value)||0;
            let clsKey = document.getElementById('cls').value;
            let sell = parseFloat(document.getElementById('sell').value)||0;

            let cost = P_BINA[clsKey] || 21050; // Central Cost Engine'den al

            let grossArea = (area * kaks) * 1.25; // %25 ortak alan payı
            let mutM2 = grossArea * (1 - (share/100));
            
            let totalCost = grossArea * cost;
            let totalRev = mutM2 * sell;
            let profit = totalRev - totalCost;
            let margin = totalCost > 0 ? (profit/totalCost)*100 : 0;

            document.getElementById('r_mut').innerText = Math.round(mutM2) + ' m²';
            document.getElementById('r_cost').innerText = new Intl.NumberFormat('tr-TR').format(Math.round(totalCost)) + ' TL';
            document.getElementById('r_rev').innerText = new Intl.NumberFormat('tr-TR').format(Math.round(totalRev)) + ' TL';
            
            let pEl = document.getElementById('r_profit');
            pEl.innerText = new Intl.NumberFormat('tr-TR').format(Math.round(profit)) + ' TL';
            pEl.style.color = profit > 0 ? 'var(--green)' : '#ef4444';
            document.getElementById('r_margin').innerText = margin.toFixed(1);
            
            let ctaUrl = `/maliyet-hesapla?service=kat-karsiligi&area=${area}`;
            document.querySelector('.cta-btn').setAttribute('href', ctaUrl);
        } document.addEventListener('DOMContentLoaded', calc);
    </script>
</body>
</html>
