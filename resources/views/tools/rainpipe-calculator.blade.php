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
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1"><title>Yağmur Borusu Seçimi | WORKSİTEM</title>
    <meta name="description" content="Çatı yağmur suyu tahliyesi için gerekli yağmur borusu çapını ve yaklaşık maliyeti hesaplayın, uygun iniş borusu seçimini planlayın.">
    <link rel="canonical" href="https://worksitem.com/araclar/yagmur-borusu-hesabi"><link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet"><style>*,*::before,*::after{box-sizing:border-box} .ws-warn{color:#b91c1c;font-size:13px;font-weight:600;margin-top:10px;} :root { --bg: #f8fafc; --card-bg: #ffffff; --border: #e2e8f0; --primary: #101318; --text: #0f172a; --muted: #64748b; --cyan: #8a6200; --green: #059669; } body { font-family: 'Inter', sans-serif; background: var(--bg); color: var(--text); margin: 0; padding: 0; } .container { max-width: 1200px; margin: 40px auto; padding: 0 4%; display: grid; grid-template-columns: 1fr 1fr; gap: 40px; } @media(max-width: 992px) { .container { grid-template-columns: 1fr; } } .form-card { background: var(--card-bg); border: 1px solid var(--border); border-radius: 12px; padding: 30px; margin-bottom: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);} h1 { font-family:'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; font-size: 38px; color: var(--text); margin-bottom: 10px; } .form-group { margin-bottom: 20px; } .form-group label { display: block; font-size: 13px; font-weight: 700; color: var(--muted); margin-bottom: 8px; text-transform: uppercase; } .form-control { width: 100%; padding: 12px; border-radius: 6px; background: #f8fafc; border: 1px solid var(--border); color: var(--text); font-family: inherit; } .res-item { display: flex; justify-content: space-between; padding: 12px 0; border-bottom: 1px dashed var(--border); font-size: 14px; } .res-item.total { border-bottom: none; font-size: 24px; font-weight: 800; color: var(--cyan); margin-top: 15px; } .ai-box { background: #fff8e1; border: 1px solid #fde68a; border-radius: 8px; padding: 20px; margin-top: 20px; }</style>    <meta name="csrf-token" content="{{ csrf_token() }}">
    <x-calc.head />
</head>
<body class="wc" data-tool-slug="rainpipe-calculator">
    <x-site.contech-header />
    <div class="container">
        <div><x-calc.hero title="Yağmur borusu çapı hesaplama" crumb="Yağmur borusu">Çatı izdüşüm alanına ve iniş borusu sayısına göre boru çapını ve yaklaşık iniş borusu maliyetini bulun.</x-calc.hero>
            <div class="form-card">
                <div class="form-group"><label>Çatı İzdüşüm Alanı (m²)</label><input type="number" id="area" class="form-control" value="150" min="1" oninput="calc()"></div>
                <div class="form-group"><label>Bina Yüksekliği (m)</label><input type="number" id="h" class="form-control" value="12" min="1" oninput="calc()"></div>
                <div class="form-group"><label>İniş Borusu Adedi</label><input type="number" id="count" class="form-control" value="2" min="1" step="1" oninput="calc()"></div>
            </div>
        </div>
        <div>
            <div class="form-card" style="border-color:var(--cyan); position:sticky; top:20px;" data-wc-result>
                <div style="font-family:'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; font-size:24px; color:var(--cyan); margin-bottom:15px;">📊 HİDROLİK SONUÇ</div>
                <div class="res-item"><span>Boru Başına Düşen Alan</span> <strong id="r_per">0 m²</strong></div>
                <div class="res-item total"><span>ÖNERİLEN BORU ÇAPI</span> <span id="r_dia" data-wc-price>0 mm</span></div>
                <div class="res-item"><span>Boru Birim Fiyatı (2026, montaj dahil)</span> <strong id="r_price">0 TL/m</strong></div>
                <div class="res-item"><span>Toplam Boru Maliyeti</span> <strong id="r_total">0 TL</strong></div>
                <div class="ws-warn" id="warn" role="alert" hidden></div>
                <div class="ai-box"><div style="color:#8a6200; font-family:'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; font-size:22px;">💡 Tesisat Notu</div><ul id="ai_notes" style="font-size:13px; color:#0f172a; margin:0; padding-left:15px;"></ul></div>
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
        function calc() {
            const a = parseFloat(document.getElementById('area').value), h = parseFloat(document.getElementById('h').value), cRaw = parseFloat(document.getElementById('count').value);
            const warn = document.getElementById('warn'), notesBox = document.getElementById('ai_notes');
            // v119: negatif/0/boş ya da kesirli adette hesap yapılmaz (eskiden 0 adet sessizce 1 sayılıyordu)
            if (!(a > 0) || !(h > 0) || !(cRaw >= 1) || cRaw !== Math.floor(cRaw)) {
                ['r_per', 'r_dia', 'r_price', 'r_total'].forEach(id => document.getElementById(id).innerText = '—');
                notesBox.innerHTML = '';
                warn.innerText = !(cRaw >= 1) || cRaw !== Math.floor(cRaw) ? 'İniş borusu adedi en az 1 olan bir tam sayı olmalı.' : 'Çatı alanı ve bina yüksekliği için pozitif bir değer girin.';
                warn.hidden = false;
                return;
            }
            warn.hidden = true;
            const c = cRaw;
            let per = a / c;
            let dia = 70; let price = {{ (float) data_get($pricing ?? [], 'yagmur.boru_70_m', 242.69) }}; // PVC iniş borusu, montaj dahil (fiyat robotu)
            if(per > 40 && per <= 80) { dia = 100; price = {{ (float) data_get($pricing ?? [], 'yagmur.boru_100_m', 315.65) }}; }
            else if(per > 80) { dia = 125; price = {{ (float) data_get($pricing ?? [], 'yagmur.boru_125_m', 366.4) }}; }
            
            let total = price * h * c;
            const nf1 = (x) => x.toLocaleString('tr-TR', { minimumFractionDigits: 1, maximumFractionDigits: 1 });
            const tl = (x, d) => x.toLocaleString('tr-TR', { minimumFractionDigits: d, maximumFractionDigits: d }) + ' TL';
            
            document.getElementById('r_per').innerText = nf1(per) + ' m²'; document.getElementById('r_dia').innerText = 'Ø' + dia + ' mm PVC'; document.getElementById('r_price').innerText = tl(price, 2) + '/m'; document.getElementById('r_total').innerText = tl(Math.round(total), 0);
            notesBox.innerHTML = '';
            notesBox.innerHTML += '<li>Pratik hidrolik kuralı olarak borunun her 1 cm² kesit alanı, 1 m² çatı suyunu tahliye edebilir (orta şiddette yağış varsayımı). Buna göre boru başına '+nf1(per)+' m² düştüğü için Ø'+dia+' boru seçilmiştir.</li>';
            notesBox.innerHTML += '<li>Maliyet yalnız iniş borusunu (adet × bina yüksekliği) içerir; yatay oluk, dirsek ve kelepçe dahil değildir.</li>';
            if(per > 120) notesBox.innerHTML += '<li style="color:#b91c1c; font-weight:bold;">Uyarı: Boru sayısı yetersiz! Lütfen binaya daha fazla iniş borusu (oluk) ekleyin, aksi takdirde aşırı yağışlarda taşma yaşanır.</li>';
        } document.addEventListener('DOMContentLoaded', calc);
    </script>
<x-calc.kit />
    <x-site.contech-footer />
    <script src="/js/tool-events.js" defer></script>
</body>
</html>
