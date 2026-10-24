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
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Azami Konut Kredisi Hesabı | WORKSİTEM</title>
    <meta name="description" content="Konut için kullanılabilecek azami kredi tutarını hesaplayın ve satın alma öncesinde ulaşabileceğiniz yaklaşık finansman limitini görün.">
    <link rel="canonical" href="https://worksitem.com/araclar/azami-konut-kredisi"><link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet"><style>*,*::before,*::after{box-sizing:border-box} .wc-warn{color:#b91c1c;font-size:12px;font-weight:600;margin-top:6px;display:block} :root { --bg: #f8fafc; --card-bg: #ffffff; --border: #e2e8f0; --primary: #101318; --text: #0f172a; --muted: #64748b; --cyan: #8a6200; --green: #059669; } body { font-family: 'Inter', sans-serif; background: var(--bg); color: var(--text); margin: 0; padding: 0; } .container { max-width: 1200px; margin: 40px auto; padding: 0 4%; display: grid; grid-template-columns: 1fr 1fr; gap: 40px; } @media(max-width: 992px) { .container { grid-template-columns: 1fr; } } .form-card { background: var(--card-bg); border: 1px solid var(--border); border-radius: 12px; padding: 30px; margin-bottom: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);} h1 { font-family:'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; font-size: 38px; color: var(--text); margin-bottom: 10px; } .form-group { margin-bottom: 20px; } .form-group label { display: block; font-size: 13px; font-weight: 700; color: var(--muted); margin-bottom: 8px; text-transform: uppercase; } .form-control { width: 100%; padding: 12px; border-radius: 6px; background: #f8fafc; border: 1px solid var(--border); color: var(--text); font-family: inherit; } .res-item { display: flex; justify-content: space-between; padding: 12px 0; border-bottom: 1px dashed var(--border); font-size: 14px; } .res-item.total { border-bottom: none; font-size: 22px; font-weight: 800; color: var(--primary); margin-top: 15px; } .ai-box { background: #fff8e1; border: 1px solid #fde68a; border-radius: 8px; padding: 20px; margin-top: 20px; }</style>    <meta name="csrf-token" content="{{ csrf_token() }}">
    <x-calc.head />
</head>
<body class="wc" data-tool-slug="mortgage-calculator">
    <x-site.contech-header />
    <div class="container">
        <div><x-calc.hero title="Konut kredisi limiti hesaplama" crumb="Konut kredisi"></x-calc.hero>
            <div class="form-card">
                <div class="form-group"><label>Evin Ekspertiz Değeri (TL)</label><input type="number" id="val" class="form-control" value="3000000" min="0" oninput="calc()"></div>
                <div class="form-group"><label>Aylık Belgelenebilir Hane Geliri (TL)</label><input type="number" id="inc" class="form-control" value="100000" min="0" oninput="calc()"></div>
                <div class="form-group"><label>Enerji Kimlik Belgesi Sınıfı</label><select id="nrg" class="form-control" onchange="calc()"><option value="0" selected>A veya B sınıfı</option><option value="1">C sınıfı</option><option value="2">D, E, F veya G sınıfı</option></select></div>
                <div class="form-group" style="display:flex;gap:12px"><div style="flex:1"><label>Aylık Faiz (%)</label><input type="number" id="rate" class="form-control" value="{{ config('worksitem_mevzuat_2026.konut_faiz_aylik', 3) }}" min="0" max="10" step="0.01" oninput="calc()"></div><div style="flex:1"><label>Vade (Ay)</label><input type="number" id="term" class="form-control" value="{{ config('worksitem_mevzuat_2026.konut_vade_ay', 120) }}" min="12" max="240" step="12" oninput="calc()"></div></div>
                <span class="wc-warn" id="inErr" hidden></span>
            </div>
        </div>
        <div>
            <div class="form-card" style="border-color:var(--primary); position:sticky; top:20px;" data-wc-result>
                <div style="font-family:'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; font-size:24px; color:var(--primary); margin-bottom:15px;">📊 KREDİ KAPASİTESİ</div>
                <div class="res-item"><span>BDDK Kredi/Değer Sınırı <small id="r_ltv"></small></span> <strong id="r_bddk">0 TL</strong></div>
                <div class="res-item"><span>Gelire Göre Maks. Taksit (%50)</span> <strong id="r_ins">0 TL/Ay</strong></div>
                <div class="res-item total"><span>ÇEKİLEBİLECEK AZAMİ KREDİ</span> <span id="r_max" data-wc-price>0 TL</span></div>
                <div class="ai-box"><div style="color:#8a6200; font-family:'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; font-size:22px;">💡 Finansal Uyarılar</div><ul id="ai_notes" style="font-size:13px; color:#0f172a; margin:0; padding-left:15px;"></ul></div>
            </div>
        </div>
    </div>

    <script>
        // BDDK 29.01.2026 kararı: değer dilimi × enerji sınıfı (config/worksitem_mevzuat_2026.php)
        const LTV = @json(config('worksitem_mevzuat_2026.bddk_ltv'));
        const tl = v => new Intl.NumberFormat('tr-TR').format(Math.round(v)) + ' TL';
        function calc() {
            const val = parseFloat(document.getElementById('val').value);
            const inc = parseFloat(document.getElementById('inc').value);
            const cls = parseInt(document.getElementById('nrg').value) || 0;
            const rPct = parseFloat(document.getElementById('rate').value);
            const n = parseInt(document.getElementById('term').value);
            const err = document.getElementById('inErr');
            const ids = ['r_bddk','r_ins','r_max'];
            let msg = '';
            if (!(val > 0) || !(inc > 0)) msg = 'Ekspertiz değeri ve gelir için pozitif bir değer girin.';
            else if (!(rPct >= 0) || rPct > 10) msg = 'Aylık faiz 0 ile 10 arasında olmalı.';
            else if (!(n >= 1) || n > 240) msg = 'Vade 1–240 ay arasında olmalı.';
            const notesBox = document.getElementById('ai_notes');
            if (msg) { err.textContent = msg; err.hidden = false; ids.forEach(i => document.getElementById(i).innerText = '—'); document.getElementById('r_ltv').innerText = ''; notesBox.innerHTML = ''; return; }
            err.hidden = true;
            const row = LTV.find(([lim]) => lim === null || val <= lim);
            const ltv = row[1][cls];
            const bddk = val * ltv;
            const maxIns = inc * 0.50; // bankalar genelde gelirin yaklaşık %50'sine kadar taksit kabul eder (varsayım)
            const r = rPct / 100;
            const incomeBasedLoan = r > 0 ? maxIns * (Math.pow(1+r, n) - 1) / (r * Math.pow(1+r, n)) : maxIns * n;
            const finalLoan = Math.min(bddk, incomeBasedLoan);
            document.getElementById('r_ltv').innerText = '(%' + Math.round(ltv*100) + ')';
            document.getElementById('r_bddk').innerText = tl(bddk);
            document.getElementById('r_ins').innerText = new Intl.NumberFormat('tr-TR').format(Math.round(maxIns)) + ' TL/Ay';
            document.getElementById('r_max').innerText = tl(finalLoan);
            notesBox.innerHTML = '';
            const add = (t, c) => { const li = document.createElement('li'); li.textContent = t; if (c) li.style.color = c; notesBox.appendChild(li); };
            if (incomeBasedLoan < bddk) add('Kredi tutarını geliriniz sınırlıyor. Limiti gelir artışı ya da ortak borçlu yükseltebilir; peşinat artışı gelir sınırını değiştirmez.', '#b91c1c');
            add('BDDK 29.01.2026 kararına göre kredi/değer oranı ekspertiz değeri dilimine ve enerji sınıfına göre belirlenir; birinci/ikinci el ayrımı yoktur.');
            add('Hesap aylık %' + rPct.toLocaleString('tr-TR', {maximumFractionDigits: 2}) + ' faiz ve ' + n + ' ay vadeyle yapıldı. Mevcut kredi taksitleriniz gelirden düşülmedi; bankanın değerlendirmesi farklı olabilir.');
        } document.addEventListener('DOMContentLoaded', calc);
    </script>
<x-calc.kit />
    <x-site.contech-footer />
    <script src="/js/tool-events.js" defer></script>
</body>
</html>
