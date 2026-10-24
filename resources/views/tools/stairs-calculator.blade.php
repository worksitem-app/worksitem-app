<!DOCTYPE html>
<html lang="tr">
<head>
    <link rel="icon" href="/favicon.ico" type="image/x-icon">

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Merdiven Ergonomi & Maliyet | WORKSİTEM</title>
    <meta name="description" content="2R+T=63 ergonomi kuralına göre ideal merdiven basamak ölçülerini hesaplayın ve mermer kaplama için yaklaşık bütçenizi planlayın.">
    <link rel="canonical" href="https://worksitem.com/araclar/merdiven-basamak-hesabi">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        :root { --bg:#f8fafc; --surface:#ffffff; --border:#e2e8f0; --primary:#101318; --primary-light:#fff8e1; --text-main:#0f172a; --text-muted:#64748b; --green:#059669; --transition:all 0.25s ease;}
        *,*::before,*::after{box-sizing:border-box}
        .ws-warn { color:#b91c1c; font-size:13px; font-weight:600; margin-top:10px; }
        body { font-family:'Inter',sans-serif; background:var(--bg); color:var(--text-main); margin:0; padding-bottom:80px; }
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
<body class="wc" data-tool-slug="stairs-calculator">
    <x-site.contech-header />
    <div class="container">
        <div>
            <x-calc.hero title="Merdiven basamak hesaplama" crumb="Merdiven">İdeal basamak genişliğini (2R+T=63) ve mermer kaplama bütçesini planlayın.</x-calc.hero>
            <div class="form-card">
                <div class="form-group"><label>Kat Yüksekliği (m)</label><input type="number" id="h" class="form-control" value="3.00" step="0.05" min="0.5" max="10" oninput="calc()"></div>
                <div class="grid-2">
                    <div class="form-group"><label>Hedef Rıht Yüksekliği (cm)</label><input type="number" id="r_tar" class="form-control" value="17" step="0.5" min="12" max="20" oninput="calc()"></div>
                    <div class="form-group"><label>Basamak Genişliği (m)</label><input type="number" id="w" class="form-control" value="1.20" step="0.1" min="0.5" max="10" oninput="calc()"></div>
                </div>
            </div>
        </div>
        <div>
            <div class="form-card" style="border-color:var(--primary); position:sticky; top:100px;" data-wc-result>
                <div class="result-header">MİMARİ ÇIKTI</div>
                <div class="res-item"><span>Rıht Sayısı (basamak = rıht − 1)</span> <strong id="r_steps">0 Adet</strong></div>
                <div class="res-item"><span>Tam Rıht Yüksekliği</span> <strong id="r_riht">0 cm</strong></div>
                <div class="res-item"><span>İdeal Basamak Genişliği (Adım)</span> <strong id="r_tread">0 cm</strong></div>
                <div class="res-item total"><span>MERMER KAPLAMA MALİYETİ</span> <strong id="r_cost" data-wc-price>0 TL</strong></div>
                <div class="ws-warn" id="warn" role="alert" hidden></div>
                
                <x-tools.lead-cta service="ince-isler" />
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
        const P_MERDIVEN = @json($pricing['merdiven'] ?? []);

        function calc() {
            const hm = parseFloat(document.getElementById('h').value);
            const r_tar = parseFloat(document.getElementById('r_tar').value);
            const w = parseFloat(document.getElementById('w').value);
            const warn = document.getElementById('warn');
            const ids = ['r_steps', 'r_riht', 'r_tread', 'r_cost'];

            // v120: negatif/0/boş ya da aralık dışı girdide hesap yapılmaz
            if (!(hm > 0) || !(w > 0) || !(r_tar > 0) || hm > 20 || w > 20) {
                ids.forEach(id => document.getElementById(id).innerText = '—');
                warn.innerText = 'Kat yüksekliği, rıht ve basamak genişliği için pozitif bir değer girin.'; warn.hidden = false;
                return;
            }
            if (r_tar < 12 || r_tar > 20) {
                ids.forEach(id => document.getElementById(id).innerText = '—');
                warn.innerText = 'Hedef rıht yüksekliği 12–20 cm arasında olmalı.'; warn.hidden = false;
                return;
            }
            const h = hm * 100;
            let steps = Math.max(1, Math.round(h / r_tar));
            let actualRiser = h / steps;
            let tread = 63 - (2 * actualRiser); // Blondel: 2R + T = 63
            
            let price = P_MERDIVEN['beyaz_mermer_m'] || 1573.25;
            let cost = steps * w * price;
            const f2 = (x) => x.toLocaleString('tr-TR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

            document.getElementById('r_steps').innerText = steps + ' Adet'; 
            document.getElementById('r_riht').innerText = f2(actualRiser) + ' cm'; 
            document.getElementById('r_tread').innerText = f2(tread) + ' cm'; 
            document.getElementById('r_cost').innerText = new Intl.NumberFormat('tr-TR').format(Math.round(cost)) + ' TL';

            // Planlı Alanlar İmar Yönetmeliği 31. madde: konut ortak merdiveninde rıht ≤ 18 cm, basamak ≥ 27 cm
            const notes = [];
            if (actualRiser > 18 || tread < 27) notes.push('İmar Yönetmeliği (konut ortak merdiveni): rıht en çok 18 cm, basamak en az 27 cm olmalı.');
            warn.innerText = notes.join(' ');
            warn.hidden = notes.length === 0;
        } document.addEventListener('DOMContentLoaded', calc);
    </script>
<x-calc.kit />
    <x-site.contech-footer />
    <script src="/js/tool-events.js" defer></script>
</body>
</html>
