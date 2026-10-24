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
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Poz Birim Fiyat Analizi | WORKSİTEM</title>
    <meta name="description" content="İnşaat pozları için birim fiyat analizini inceleyin, keşif ve teklif çalışmalarında kullanılacak yaklaşık imalat maliyetini değerlendirin.">
    <link rel="canonical" href="https://worksitem.com/araclar/birim-fiyat-analiz"><link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet"><style>*,*::before,*::after{box-sizing:border-box} .wc-warn{color:#b91c1c;font-size:12px;font-weight:600;margin-top:6px;display:block} :root { --bg: #f8fafc; --card-bg: #ffffff; --border: #e2e8f0; --primary: #101318; --text: #0f172a; --muted: #64748b; --cyan: #8a6200; --green: #059669; } body { font-family: 'Inter', sans-serif; background: var(--bg); color: var(--text); margin: 0; padding: 0; } .container { max-width: 1200px; margin: 40px auto; padding: 0 4%; display: grid; grid-template-columns: 1fr 1fr; gap: 40px; } @media(max-width: 992px) { .container { grid-template-columns: 1fr; } } .form-card { background: var(--card-bg); border: 1px solid var(--border); border-radius: 12px; padding: 30px; margin-bottom: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);} h1 { font-family:'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; font-size: 38px; color: var(--text); margin-bottom: 10px; } .form-group { margin-bottom: 20px; } .form-group label { display: block; font-size: 13px; font-weight: 700; color: var(--muted); margin-bottom: 8px; text-transform: uppercase; } .form-control { width: 100%; padding: 12px; border-radius: 6px; background: #f8fafc; border: 1px solid var(--border); color: var(--text); font-family: inherit; } .res-item { display: flex; justify-content: space-between; padding: 12px 0; border-bottom: 1px dashed var(--border); font-size: 14px; } .res-item.total { border-bottom: none; font-size: 22px; font-weight: 800; color: var(--green); margin-top: 15px; } .ai-box { background: #fff8e1; border: 1px solid #fde68a; border-radius: 8px; padding: 20px; margin-top: 20px; }</style>    <meta name="csrf-token" content="{{ csrf_token() }}">
    <x-calc.head />
</head>
<body class="wc" data-tool-slug="unit-price-calculator">
    <x-site.contech-header />
    <div class="container">
        <div><x-calc.hero title="Birim fiyat analizi hesaplama" crumb="Birim fiyat analizi">Malzeme, işçilik ve makine tutarlarına genel gider ve kârı ekleyerek bir imalatın analizli birim fiyatını (KDV hariç) bulun.</x-calc.hero>
            <div class="form-card">
                <div class="form-group"><label>1 Birim (m², m³) İçin Malzeme Tutarı (TL)</label><input type="number" id="mat" class="form-control" min="0" step="0.01" value="1000" oninput="calc()"></div>
                <div class="form-group"><label>1 Birim İçin İşçilik Tutarı (TL)</label><input type="number" id="lab" class="form-control" min="0" step="0.01" value="500" oninput="calc()"></div>
                <div class="form-group"><label>1 Birim İçin Makine/Ekipman Tutarı (TL)</label><input type="number" id="mac" class="form-control" min="0" step="0.01" value="100" oninput="calc()"></div>
                <span class="wc-warn" id="inErr" hidden></span>
                <div class="form-group">
                    <label>Müteahhit Kârı ve Genel Giderler (%)</label>
                    <select id="prof" class="form-control" onchange="calc()">
                        <option value="1.25" selected>ÇŞB Resmi Oranı (%25)</option>
                        <option value="1.15">Özel Sektör / Rekabetçi (%15)</option>
                    </select>
                </div>
            </div>
        </div>
        <div>
            <div class="form-card" style="border-color:var(--green); position:sticky; top:20px;" data-wc-result>
                <div style="font-family:'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; font-size:24px; color:var(--green); margin-bottom:15px;">📊 ANALİZ ÇIKTISI</div>
                <div class="res-item"><span>Ara Toplam (Çıplak Maliyet)</span> <strong id="r_raw">0 TL</strong></div>
                <div class="res-item"><span>Müteahhit Kârı ve Genel Gider</span> <strong id="r_prof">0 TL</strong></div>
                <div class="res-item total"><span>ANALİZLİ BİRİM FİYAT (KDV hariç)</span> <span id="r_total" data-wc-price>0 TL</span></div>
                <div class="ai-box"><div style="color:#8a6200; font-family:'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; font-size:22px;">💡 İhale Notu</div><ul id="ai_notes" style="font-size:13px; color:#0f172a; margin:0; padding-left:15px;"></ul></div>
            </div>
        </div>
    </div>

    <script>
        const tl = v => new Intl.NumberFormat('tr-TR', {minimumFractionDigits: 2, maximumFractionDigits: 2}).format(v) + ' TL';
        function calc() {
            const vals = ['mat','lab','mac'].map(id => document.getElementById(id).value);
            const [mat, lab, mac] = vals.map(v => v === '' ? 0 : parseFloat(v));
            const profRate = parseFloat(document.getElementById('prof').value);
            const err = document.getElementById('inErr');
            const notesBox = document.getElementById('ai_notes');
            if ([mat, lab, mac].some(v => !(v >= 0)) || mat + lab + mac <= 0) {
                err.textContent = [mat, lab, mac].some(v => v < 0) ? 'Tutarlar negatif olamaz.' : 'En az bir kalem için pozitif bir tutar girin.';
                err.hidden = false;
                ['r_raw','r_prof','r_total'].forEach(i => document.getElementById(i).innerText = '—'); notesBox.innerHTML = ''; return;
            }
            err.hidden = true;
            const raw = mat + lab + mac;
            const total = raw * profRate;
            const profValue = total - raw;
            document.getElementById('r_raw').innerText = tl(raw); document.getElementById('r_prof').innerText = '+ ' + tl(profValue); document.getElementById('r_total').innerText = tl(total);
            notesBox.innerHTML = '';
            notesBox.innerHTML += '<li>ÇŞB birim fiyat analizlerinde müteahhit kârı ve genel giderler %25 alınır (%15 genel gider + %10 kâr).</li>';
            notesBox.innerHTML += '<li>Genel giderler; merkez ofis, şantiye yönetimi, sigorta ve finansman giderlerini kapsar. Tamamı net kâr değildir.</li>';
            notesBox.innerHTML += '<li>Tutarlar KDV hariçtir.</li>';
        } document.addEventListener('DOMContentLoaded', calc);
    </script>
<x-calc.kit />
    <x-site.contech-footer />
    <script src="/js/tool-events.js" defer></script>
</body>
</html>
