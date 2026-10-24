<!DOCTYPE html><html lang="tr"><head>
    <link rel="icon" href="/favicon.ico" type="image/x-icon">
    <meta property="og:image" content="https://worksitem.com/brand/og-image.png">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:image" content="https://worksitem.com/brand/og-image.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="manifest" href="/site.webmanifest">
    <meta name="theme-color" content="#ffffff">
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Profil Kesim Planı ve Fire Hesabı | WORKSİTEM</title>
    <meta name="description" content="Profil, boru ve ahşap kesim listesine göre gereken stok boy sayısını, kesim planını ve fire oranını hesaplayın.">
    <link rel="canonical" href="https://worksitem.com/araclar/kesim-optimizasyonu"><link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet"><style>*,*::before,*::after{box-sizing:border-box} .wc-warn{color:#b91c1c;font-size:14px;font-weight:700} body{font-family:'Inter',sans-serif;background:#fdfdfd;color:#111827;margin:0;padding-bottom:60px;} .container{max-width:800px;margin:40px auto;padding:30px;background:#fff;border:1px solid #e5e7eb;border-radius:16px;box-shadow:0 4px 6px rgba(0,0,0,0.05);} h1{font-size:24px;font-weight:800;margin-bottom:10px;color:#101318;} .form-group{margin-bottom:20px;} label{display:block;font-size:13px;font-weight:700;color:#6b7280;margin-bottom:8px;text-transform:uppercase;} input,select{width:100%;padding:14px;border:1px solid #e5e7eb;border-radius:8px;font-family:inherit;font-size:15px;} .grid-2{display:grid;grid-template-columns:1fr 1fr;gap:15px;} @media(max-width:600px){.grid-2{grid-template-columns:1fr}} textarea{width:100%;padding:14px;border:1px solid #e5e7eb;border-radius:8px;font-family:inherit;font-size:15px;min-height:96px} .plan{font-size:12px;text-align:left;margin-top:10px;color:#374151;max-height:240px;overflow:auto} .result-box{background:#fff8e1;border:2px solid #fde68a;padding:20px;border-radius:12px;margin-top:20px;text-align:center;} .result-val{font-size:32px;font-weight:800;color:#101318;} .cta{display:block;width:100%;padding:16px;background:#101318;color:#fff;text-align:center;font-weight:800;text-decoration:none;border-radius:8px;margin-top:20px; transition:all 0.2s;} .cta:hover{opacity:0.9; transform:translateY(-2px);}</style>    <meta name="csrf-token" content="{{ csrf_token() }}">
    <x-calc.head />
</head>
<body class="wc" data-tool-slug="cutting-optimization">
    <x-site.contech-header /><div class="container"><x-calc.hero title="Profil kesim fire hesaplama" crumb="Kesim fire">Profil, boru veya ahşap karkas alımlarında boy standardına göre ne kadar profil sipariş etmeniz gerektiğini hesaplayın.</x-calc.hero>
<div class="form-group"><label>Kesim Listesi (her satıra: boy metre × adet)</label><textarea id="list" oninput="calc()">2.2 x 50</textarea><small style="color:#6b7280">Örnek: "2.2 x 50" ya da birden çok satır: "1.5 x 10", "0.8 x 24". Ondalık için nokta ya da virgül kullanabilirsiniz.</small></div>
<div class="grid-2"><div class="form-group"><label>Fabrika Boy Standardı (Metre)</label><input type="number" id="standard" value="6" min="0" step="0.1" oninput="calc()"></div><div class="form-group"><label>Testere Payı (Bıçak Kalınlığı) - cm</label><input type="number" id="blade" value="0.5" min="0" step="0.1" oninput="calc()"></div></div>
<div class="result-box" data-wc-result><div style="font-size:14px; color:#101318; font-weight:600;">GEREKEN TOPLAM BOY PROFİL ADEDİ</div><div class="result-val" id="res" data-wc-price>0 Adet</div><div style="font-size:13px; color:#101318; margin-top:5px;" id="res2"></div><div class="plan" id="plan"></div></div>
<a href="/maliyet-hesapla?service=malzeme" class="cta">İnşaat Malzemesi ve Çelik Profil Teklifi Al →</a></div>

    <script>
        // Kesim listesi: First-Fit Decreasing (uzundan kısaya, sığdığı ilk boya). n parça için n−1 kesim sayılır.
        const fmt = (v, d=2) => new Intl.NumberFormat('tr-TR', {minimumFractionDigits: d, maximumFractionDigits: d}).format(v);
        function parseList(txt) {
            const out = [];
            for (const raw of txt.split(/\n|;/)) {
                const line = raw.trim(); if (!line) continue;
                const m = line.match(/^(-?[0-9]+(?:[.,][0-9]+)?)\s*(?:[x×*]\s*(-?[0-9]+))?$/i);
                if (!m) return {err: '"' + line + '" satırı okunamadı. Biçim: 2.2 x 50'};
                const len = parseFloat(m[1].replace(',', '.')), qty = m[2] === undefined ? 1 : parseInt(m[2]);
                if (!(len > 0) || !(qty > 0)) return {err: 'Parça boyu ve adedi pozitif olmalı.'};
                out.push({len, qty});
            }
            return out.length ? {items: out} : {err: 'En az bir parça girin.'};
        }
        function ffd(pieces, s, b) {
            const bins = []; // {used, cuts:[]} — used: parçalar + aralarındaki testere payları
            for (const p of pieces) {
                let bin = bins.find(x => x.used + b + p <= s + 1e-9);
                if (bin) { bin.used += b + p; bin.cuts.push(p); } else bins.push({used: p, cuts: [p]});
            }
            return bins;
        }
        function calc() {
            const res = document.getElementById('res'), res2 = document.getElementById('res2'), plan = document.getElementById('plan');
            const s = parseFloat(document.getElementById('standard').value);
            const b = (parseFloat(document.getElementById('blade').value) || 0) / 100;
            const warn = t => { res.innerHTML = '<span class="wc-warn">' + t + '</span>'; res2.innerText = ''; plan.innerHTML = ''; };
            if (!(s > 0)) return warn('Stok boyu için pozitif bir değer girin.');
            if (b < 0) return warn('Testere payı negatif olamaz.');
            const L = parseList(document.getElementById('list').value);
            if (L.err) return warn(L.err);
            const tooLong = L.items.find(i => i.len > s + 1e-9);
            if (tooLong) return warn('Parça boyu (' + fmt(tooLong.len) + ' m) stok boyundan (' + fmt(s) + ' m) büyük olamaz.');
            const total = L.items.reduce((a, i) => a + i.qty, 0);
            if (total > 20000) return warn('En fazla 20.000 parça girilebilir.');
            const pieces = L.items.flatMap(i => Array(i.qty).fill(i.len)).sort((x, y) => y - x);
            const bins = ffd(pieces, s, b);
            const need = pieces.reduce((a, p) => a + p, 0);
            const waste = bins.length * s - need; // testere talaşı dahil
            res.innerText = bins.length + ' Boy (' + fmt(s, s % 1 ? 2 : 0) + ' m)';
            res2.innerText = 'Toplam fire: ' + fmt(waste) + ' m (%' + fmt(waste / (bins.length * s) * 100, 1) + ', testere payı dahil) · Net parça uzunluğu ' + fmt(need) + ' m';
            // Kesim planı: aynı desenli boyları grupla
            const groups = new Map();
            for (const x of bins) { const k = x.cuts.map(c => fmt(c)).join(' + '); const g = groups.get(k) || {n: 0, rest: s - x.used}; g.n++; groups.set(k, g); }
            plan.innerHTML = '';
            const head = document.createElement('div'); head.style.fontWeight = '700'; head.textContent = 'Kesim planı (uzundan kısaya yerleştirme; en az stok garantisi değildir):'; plan.appendChild(head);
            for (const [k, g] of groups) { const d = document.createElement('div'); d.textContent = g.n + ' boy: ' + k + ' m → artan ' + fmt(Math.max(0, g.rest)) + ' m'; plan.appendChild(d); }
        } document.addEventListener('DOMContentLoaded', calc);</script>    <script src="/js/tool-events.js" defer></script>
<x-calc.kit />
    <x-site.contech-footer />
</body></html>
