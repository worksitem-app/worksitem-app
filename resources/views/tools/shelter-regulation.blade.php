<!DOCTYPE html>
<html lang="tr">
<head>
    <link rel="icon" href="/favicon.ico" type="image/x-icon">

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sığınak Yönetmeliği Hesabı | WORKSİTEM</title>
    <meta name="description" content="Sığınak Yönetmeliği (07.11.2025 değişikliği) kapsamında binanızda sığınak zorunluluğunu ve gereken yaklaşık sığınak alanını hesaplayın.">
    <link rel="canonical" href="https://worksitem.com/araclar/siginak-yonetmeligi">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        *,*::before,*::after { box-sizing:border-box; }
        .wc-warn { color:#b91c1c; font-size:13px; font-weight:700; }
        body { font-family:'Inter', sans-serif; background:#fdfdfd; color:#111827; margin:0; padding-bottom:60px;}
        .container { max-width:800px; margin:40px auto; padding:30px; background:#fff; border:1px solid #e5e7eb; border-radius:16px; box-shadow:0 4px 6px rgba(0,0,0,0.05); }
        h1 { font-size:24px; font-weight:800; margin-bottom:10px; color:#101318;}
        .form-group { margin-bottom:20px; }
        label { display:block; font-size:13px; font-weight:700; color:#6b7280; margin-bottom:8px; text-transform:uppercase;}
        input, select { width:100%; padding:14px; border:1px solid #e5e7eb; border-radius:8px; font-family:inherit; font-size:15px; }
        .result-box { background:#fff8e1; border:2px solid #fde68a; padding:20px; border-radius:12px; margin-top:20px; text-align:center;}
        .result-val { font-size:24px; font-weight:800; color:#101318; }
        .cta { display:block; width:100%; padding:16px; background:#101318; color:#fff; text-align:center; font-weight:800; text-decoration:none; border-radius:8px; margin-top:20px; }
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
<body class="wc" data-tool-slug="shelter-regulation">
    <x-site.contech-header />
    <div class="container">
        <x-calc.hero title="Sığınak zorunluluğu ve alanı hesaplama" crumb="Sığınak">Sığınak Yönetmeliği (RG 07.11.2025, sayı 33070) gereği binanızda sığınak zorunluluğu olup olmadığını ve gereken yaklaşık alanı öğrenin.</x-calc.hero>
        
        <div class="form-group">
            <label>Bina Kullanımı</label>
            <select id="bType" onchange="calc()">
                <option value="konut">Yalnız konut</option>
                <option value="karma">Karma (konut + işyeri)</option>
                <option value="isyeri">İşyeri / ticari (konut dışı)</option>
                <option value="resmi">Resmî bina</option>
                <option value="sanayi">İmalat / sanayi tesisi</option>
            </select>
        </div>
        <div class="form-group" id="gAmount">
            <label id="lblAmount">Konut bağımsız bölüm (daire) sayısı</label>
            <input type="number" id="amount" value="15" min="0" step="1" oninput="calc()">
        </div>
        <div class="form-group" id="gRooms">
            <label>Dairelerin oda sayısı</label>
            <select id="rooms" onchange="calc()">
                <option value="1">1 oda (daire başı 2 kişi)</option>
                <option value="2">2 oda (daire başı 3 kişi)</option>
                <option value="3" selected>3 ve daha fazla oda (daire başı 4 kişi)</option>
            </select>
        </div>
        <div class="form-group" id="gArea">
            <label id="lblArea">Binanın toplam emsal alanı (m²)</label>
            <input type="number" id="area" value="1600" min="0" oninput="calc()">
        </div>
        <div class="form-group" id="gAreaNr">
            <label>Konut dışı (işyeri) emsal alanı (m²)</label>
            <input type="number" id="areaNr" value="300" min="0" oninput="calc()">
        </div>

        <div class="result-box" data-wc-result>
            <div style="font-size:14px; color:#101318; font-weight:600; margin-bottom:5px;">YÖNETMELİK SONUCU</div>
            <div class="result-val" id="resText" data-wc-price>Zorunlu Değil</div>
            <div style="font-size:13px; color:#101318; margin-top:10px; font-weight:600;" id="resArea">Gereken Sığınak Alanı: 0 m²</div>
            <div style="font-size:12px; color:#374151; margin-top:10px; text-align:left;" id="resNote"></div>
        </div>
        <a href="/maliyet-hesapla?service=mimari-proje" class="cta">Mimari Çözüm ve Proje İçin Teklif Al →</a>
    </div>

    <script>
        // Sığınak Yönetmeliği m.7 (RG 07.11.2025, sayı 33070) — config/worksitem_mevzuat_2026.php
        const S = @json(config('worksitem_mevzuat_2026.siginak'));
        const fmt = v => new Intl.NumberFormat('tr-TR', {maximumFractionDigits: 1}).format(v);
        const LBL = {
            konut: 'Konut bağımsız bölüm (daire) sayısı', karma: 'Konut bağımsız bölüm (daire) sayısı',
            sanayi: 'Bir vardiyada aynı anda çalışan en fazla kişi'
        };
        function calc() {
            const t = document.getElementById('bType').value;
            const show = (id, on) => document.getElementById(id).style.display = on ? '' : 'none';
            show('gAmount', t === 'konut' || t === 'karma' || t === 'sanayi');
            show('gRooms', t === 'konut' || t === 'karma');
            show('gArea', t !== 'konut');
            show('gAreaNr', t === 'karma');
            if (LBL[t]) document.getElementById('lblAmount').innerText = LBL[t];
            const num = id => { const v = document.getElementById(id).value; return v === '' ? NaN : parseFloat(v); };
            const amount = num('amount'), area = num('area'), areaNr = num('areaNr');
            const rooms = parseInt(document.getElementById('rooms').value);
            const resText = document.getElementById('resText'), resArea = document.getElementById('resArea'), resNote = document.getElementById('resNote');
            const bad = msg => { resText.innerHTML = '<span class="wc-warn">' + msg + '</span>'; resArea.innerText = ''; resNote.innerText = ''; };
            const needA = t === 'konut' || t === 'karma' || t === 'sanayi', needE = t !== 'konut';
            if ((needA && !(amount >= 0)) || (needE && !(area >= 0)) || (t === 'karma' && !(areaNr >= 0))) return bad('Pozitif bir değer girin.');
            if (needA && t !== 'sanayi' && !Number.isInteger(amount)) return bad('Bağımsız bölüm sayısı tam sayı olmalı.');
            if (t === 'karma' && areaNr > area) return bad('Konut dışı emsal alanı toplam emsal alanından büyük olamaz.');

            let zorunlu = false, persons = 0, m2 = 0, note = '';
            const perFlat = S.oda_kisi[rooms];
            if (t === 'konut') {
                zorunlu = amount >= S.konut_bb_esik;
                persons = amount * perFlat;
                note = 'Yalnız konut kullanımlı yapılarda 10\'dan az bağımsız bölüm varsa sığınak aranmaz. Kişi sayısı daire başına ' + perFlat + ' kişi, kişi başı net 1 m².';
            } else if (t === 'karma') {
                zorunlu = !(amount < S.konut_bb_esik && area < S.emsal_esik);
                persons = amount * perFlat + Math.ceil(areaNr / S.emsal_kisi_bolen);
                note = 'Karma yapıda konut bağımsız bölümü 10\'dan az ve toplam emsal alanı 1.500 m²\'den küçükse muaftır. Konut kısmı daire başına ' + perFlat + ' kişi, işyeri kısmı emsal alanı / 20 kişi.';
            } else if (t === 'isyeri' || t === 'resmi') {
                const esik = t === 'resmi' ? S.resmi_esik : S.emsal_esik;
                zorunlu = area >= esik;
                persons = Math.ceil(area / S.emsal_kisi_bolen);
                note = (t === 'resmi' ? 'Resmî binalarda emsal alanı 1.000 m²\'den' : 'Konut dışı yapılarda emsal alanı 1.500 m²\'den') + ' küçükse sığınak aranmaz. Kişi sayısı = emsal alanı / 20, kişi başı net 1 m².';
            } else {
                zorunlu = area >= S.sanayi_esik;
                persons = amount >= 0 ? amount : 0;
                note = 'İmalat ve sanayi tesislerinde emsal alanı 2.000 m²\'den küçükse sığınak aranmaz. Doğrudan üretim yapılarında gerekip gerekmediğine il idare kurulu karar verir; gerekirse vardiyadaki en fazla kişi × 1 m², en az 20 m² ayrılır.';
            }
            m2 = persons * S.kisi_m2;
            if (t === 'sanayi' && zorunlu) m2 = Math.max(m2, S.sanayi_min_m2);
            resText.innerText = zorunlu ? 'SIĞINAK ZORUNLU' : 'SIĞINAK ZORUNLU DEĞİL';
            resText.style.color = zorunlu ? '#b91c1c' : '#101318';
            resArea.innerText = zorunlu ? 'Gereken net sığınak alanı: en az ' + fmt(m2) + ' m² (' + fmt(persons) + ' kişi × 1 m²)' : 'Gereken sığınak alanı: 0 m²';
            resNote.innerText = note + ' Tuvalet, duş ve mutfak nişi bu alana dahil değildir. Kaynak: Sığınak Yönetmeliği m.7 (RG 07.11.2025, sayı 33070). Yatak sayılı tesisler (otel, yurt, hastane) için ayrı kurallar vardır.';
        }
        document.addEventListener('DOMContentLoaded', calc);
    </script>
<x-calc.kit />
    <x-site.contech-footer />
    <script src="/js/tool-events.js" defer></script>
</body>
</html>
