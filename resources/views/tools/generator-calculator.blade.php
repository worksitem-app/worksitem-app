<!DOCTYPE html>
<html lang="tr">
<head>
    <link rel="icon" href="/favicon.ico" type="image/x-icon">

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jeneratör Güç Hesaplayıcı | WORKSİTEM</title>
    <meta name="description" content="Eşzamanlı çalışacak elektrikli cihazların toplam gücüne göre ihtiyaç duyulan jeneratör kapasitesini kVA cinsinden hesaplayın.">
    <link rel="canonical" href="https://worksitem.com/araclar/jenerator-hesaplama">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>*,*::before,*::after{box-sizing:border-box} .ws-warn{color:#b91c1c;font-size:13px;font-weight:600;margin-top:10px;} :root { --bg:#f8fafc; --surface:#ffffff; --border:#e2e8f0; --primary:#101318; --primary-light:#fff8e1; --text-main:#0f172a; --text-muted:#64748b; --green:#059669; --transition:all 0.25s ease;} body { font-family:'Inter',sans-serif; background:var(--bg); color:var(--text-main); margin:0; padding-bottom:80px; } .navbar { display:flex; justify-content:space-between; align-items:center; padding:1rem 5%; background:var(--surface); border-bottom:1px solid var(--border); position:sticky; top:0; z-index:50;} .logo-wrapper { display:flex; align-items:center; gap:8px; text-decoration:none; } .logo-mark { background:var(--primary); color:#fff; font-weight:800; font-size:14px; padding:4px 10px; border-radius:6px; } .logo-text { font-weight:800; font-size:20px; color:var(--text-main); } .container { max-width:1200px; margin:40px auto; padding:0 4%; display:grid; grid-template-columns:1fr 1fr; gap:40px; } @media(max-width:992px) { .container { grid-template-columns:1fr; } } .form-card { background:var(--surface); border:1px solid var(--border); border-radius:16px; padding:30px; margin-bottom:20px; box-shadow:0 4px 6px -1px rgba(0,0,0,0.05); } h1 { font-size:2rem; font-weight:800; margin-bottom:10px; letter-spacing:-0.5px; } .subtitle { color:var(--text-muted); font-size:0.95rem; margin-bottom:30px; } .form-group { margin-bottom:20px; } .form-group label { display:block; font-size:0.85rem; font-weight:700; margin-bottom:8px; text-transform:uppercase; letter-spacing:0.5px; } .form-control { width:100%; padding:14px; border-radius:8px; background:var(--bg); border:1px solid var(--border); font-family:inherit; font-size:1rem; } .result-header { font-size:1.25rem; font-weight:800; color:var(--primary); margin-bottom:20px;} .res-item { display:flex; justify-content:space-between; padding:14px 0; border-bottom:1px dashed var(--border); font-size:0.95rem; font-weight:500; } .res-item strong { font-weight:800; font-size:1.1rem; } .res-item.total { border-bottom:none; margin-top:10px; padding-top:20px; border-top:2px solid var(--border); } .res-item.total strong { font-size:1.75rem; color:var(--primary); }</style>
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
<body class="wc" data-tool-slug="generator-calculator">
    <x-site.contech-header />
    <div class="container">
        <div>
            <x-calc.hero title="Jeneratör gücü (kVA) hesaplama" crumb="Jeneratör gücü">Eşzamanlı çalışacak cihazlarınızın toplam gücüne göre gereken kVA kapasitesini bulun.</x-calc.hero>
            <div class="form-card">
                <div class="form-group">
                    <label>Kullanım Tipi (Hızlı Seçim)</label>
                    <select id="preset" class="form-control" onchange="setPower()">
                        <option value="0" selected>Özel Güç Gireceğim</option>
                        <option value="5000">Standart Ev (Buzdolabı, TV, Kombi, Aydınlatma)</option>
                        <option value="12000">Lüks Villa (Klima, Çamaşır Mak., Hidrofor dahil)</option>
                        <option value="25000">Küçük Şantiye (Kaynak makinesi, kırıcı delici, aydınlatma)</option>
                    </select>
                </div>
                <div class="form-group"><label>Aynı Anda Çalışacak Toplam Güç (Watt)</label><input type="number" id="watt" class="form-control" value="6000" min="1" oninput="calc()"></div>
                <div class="form-group"><label>En Büyük Motorlu Cihazın Gücü (Watt, yoksa 0)</label><input type="number" id="motorW" class="form-control" value="0" min="0" oninput="calc()"></div>
            </div>
        </div>
        <div>
            <div class="form-card" style="border-color:var(--primary); position:sticky; top:100px;" data-wc-result>
                <div class="result-header">SİSTEM ANALİZİ</div>
                <div class="res-item"><span>Toplam Aktif Güç</span> <strong id="r_kw">0 kW</strong></div>
                <div class="res-item"><span>Kalkış Akımı (Demeraj) Payı</span> <strong id="r_margin">0 kW</strong></div>
                <div class="res-item total"><span>ÖNERİLEN JENERATÖR</span> <strong id="r_kva" data-wc-price>0 kVA</strong></div>
                <div class="ws-warn" id="warn" role="alert" hidden></div>
                <p style="font-size:12px; color:var(--text-muted); margin-top:8px;">Güç faktörü cos φ = 0,8 alınmıştır. Kalkış payı: toplam gücün %20'si veya en büyük motorun gücü kadar (yol alma akımı), hangisi büyükse.</p>
                <x-tools.lead-cta service="jenerator" />
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

        function setPower() {
            let p = parseInt(document.getElementById('preset').value);
            if(p > 0) { document.getElementById('watt').value = p; calc(); }
        }

        function calc() {
            const watt = parseFloat(document.getElementById('watt').value);
            const motorW = parseFloat(document.getElementById('motorW').value) || 0;
            const warn = document.getElementById('warn');
            // v119: negatif/0/boş girdide hesap yapılmaz
            if (!(watt > 0) || motorW < 0) {
                ['r_kw', 'r_margin', 'r_kva'].forEach(id => document.getElementById(id).innerText = '—');
                warn.innerText = 'Toplam güç için pozitif bir değer girin.'; warn.hidden = false;
                return;
            }
            warn.hidden = true;
            const kw = watt / 1000;
            // Kalkış (demeraj) payı: %20 veya en büyük motor gücü kadar (motor yol alırken 3–6 kat akım çeker) — hangisi büyükse
            const safetyMargin = Math.max(kw * 0.20, motorW / 1000);
            const totalKw = kw + safetyMargin;
            const kva = totalKw / 0.8;   // cos φ = 0,8
            const f1 = (x) => x.toLocaleString('tr-TR', { minimumFractionDigits: 1, maximumFractionDigits: 1 });
            
            document.getElementById('r_kw').innerText = f1(kw) + ' kW';
            document.getElementById('r_margin').innerText = '+' + f1(safetyMargin) + ' kW';
            document.getElementById('r_kva').innerText = Math.ceil(kva).toLocaleString('tr-TR') + ' kVA';
        } document.addEventListener('DOMContentLoaded', calc);
    </script>
<x-calc.kit />
    <x-site.contech-footer />
    <script src="/js/tool-events.js" defer></script>
</body>
</html>
