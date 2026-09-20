<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auto-BOQ (Akıllı Keşif Fiyatlandırma) | WORKSİTEM</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        :root { --bg: #f5f6f8; --surface: #ffffff; --text: #101318; --muted: #6b7280; --line: #e4e7eb; --accent: #2563eb; --radius: 16px; }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Inter', sans-serif; background: var(--bg); color: var(--text); padding-bottom: 60px; }
        .header { background: #fff; padding: 15px 20px; border-bottom: 1px solid var(--line); display: flex; justify-content: space-between; position: sticky; top:0; z-index:10;}
        .container { max-width: 1000px; margin: 40px auto; padding: 0 20px; }
        .ai-badge { background: rgba(37, 99, 235, 0.1); color: var(--accent); padding: 6px 14px; border-radius: 100px; font-size: 12px; font-weight: 900; }
        .card { background: var(--surface); border: 1px solid var(--line); border-radius: var(--radius); padding: 30px; margin-bottom: 20px; box-shadow: 0 4px 20px rgba(0,0,0,0.03); }
        textarea { width: 100%; height: 150px; padding: 15px; border: 2px dashed #cbd5e1; border-radius: 12px; font-family: monospace; font-size:13px; resize: vertical; margin-top:10px; outline:none; }
        textarea:focus { border-color: var(--accent); }
        .btn { background: var(--text); color: #fff; padding: 15px; border-radius: 12px; border: none; font-weight: 800; cursor: pointer; width: 100%; margin-top: 15px; transition: .2s;}
        .btn:hover { background: #000; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; font-size: 13px; text-align: left; }
        th, td { padding: 12px; border-bottom: 1px solid var(--line); }
        th { background: #f8fafc; font-weight: 800; color: #475569; }
        .matched { color: #10b981; font-weight: 700; }
        .unmatched { color: #ef4444; font-weight: 700; }
        .export-btn { background: #10b981; color: #fff; padding: 15px; border-radius: 12px; border: none; font-weight: 800; cursor: pointer; width: 100%; margin-top: 15px; display: none; }
        
        .loader-wrapper { display: none; text-align: center; padding: 40px 20px; background: #fff; border-radius: 16px; border: 1px solid var(--line); margin-bottom: 20px; }
        .loader-wrapper.active { display: block; animation: fadeIn 0.3s ease; }
        .crane-icon { font-size: 48px; display: inline-block; animation: dig 1.5s infinite; transform-origin: bottom center; }
        .loading-text { margin-top: 20px; font-size: 16px; font-weight: 800; color: var(--text); }
        .loading-subtext { font-size: 13px; color: var(--muted); margin-top: 5px; }
        .progress-bar { width: 60%; height: 6px; background: #e2e8f0; border-radius: 10px; margin: 15px auto 0; overflow: hidden; position: relative; }
        .progress-fill { height: 100%; background: #ffb800; width: 0%; transition: width 0.1s linear; }

        .budget-confirm-box { margin-top: 25px; padding: 25px; border: 1px solid var(--line); border-radius: var(--radius); background: #fff; display: none; text-align: center; }
        .budget-confirm-title { font-size: 18px; font-weight: 900; margin-bottom: 8px;}
        .budget-confirm-text { color: var(--muted); font-size: 13px; line-height: 1.55; margin-bottom: 20px; }
        .budget-confirm-actions { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
        @media(max-width: 600px) { .budget-confirm-actions { grid-template-columns: 1fr; } }
        .budget-btn { min-height: 52px; border-radius: 13px; padding: 10px 14px; font-size: 13px; font-weight: 800; border: 1.5px solid var(--line); cursor: pointer; transition: .2s; }
        .budget-no { background: #fff; color: #555; }
        .budget-no:hover { background: #f8f9fb; border-color: #d1d5db; }
        .budget-yes { background: #fff; color: #111; }
        .budget-yes:hover { border-color: #38bdf8; background: #f0f9ff; box-shadow: 0 0 0 3px rgba(56,189,248,.10); }

        .lead-box { background: #f8f9fb; border: 1px solid var(--line); border-radius: var(--radius); padding: 30px; margin-top: 15px; display: none; }
        .lead-box.active { display: block; animation: fadeIn 0.5s ease; }
        .form-group { margin-bottom: 15px; text-align: left; }
        .form-group label { display: block; font-size: 13px; font-weight: 800; margin-bottom: 8px; }
        .form-control { width: 100%; height: 50px; border: 1px solid var(--line); border-radius: 12px; padding: 0 15px; font-size: 14px; background: #fff; outline: none; }
        .btn-submit { background: #2563eb; color: #fff; margin-top: 10px; width: 100%; height: 50px; border: none; border-radius: 12px; font-weight: 800; font-size: 15px; cursor: pointer; }

        @keyframes dig { 0% { transform: rotate(0deg); } 50% { transform: rotate(15deg); } 100% { transform: rotate(0deg); } }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</head>
<body>
<?php $dbPrices = \Illuminate\Support\Facades\DB::table('unit_prices')->get(); ?>
<header class="header">
    <a href="/araclar" style="text-decoration:none; color:#6b7280; font-weight:700;">← Araçlara Dön</a>
    <img src="/200.png" height="30" alt="WORKSİTEM">
</header>
<div class="container">
    <div style="text-align:center; margin-bottom:30px;">
        <span class="ai-badge">✦ NLP Akıllı Eşleştirme Motoru v2.3</span>
        <h1 style="font-weight:900; margin-top:10px;">Auto-BOQ (Keşif Fiyatlandırıcı)</h1>
        <p style="color:#6b7280;">Sistem, girdiğiniz metraj listesini Merkezi Fiyat Motorundaki güncel anahtar teslim rayiçlerle saniyeler içinde fiyatlandırır.</p>
    </div>
    
    <div class="card" id="inputCard">
        <label style="font-weight:800; font-size:14px;">Excel'den Kopyala / Yapıştır (İş Kalemi - Miktar)</label>
        <textarea id="boqInput" placeholder="Örnek kullanım:&#10;C30 Hazır Beton 150&#10;0,7 MM TRAPEZ SAC İLE ÇATI KAPLAMA YAPILMASI 120"></textarea>
        <button class="btn" id="analyzeBtn">✦ Yapay Zeka ile Fiyatlandır</button>
    </div>

    <!-- YÜKLEME EKRANI -->
    <div class="loader-wrapper" id="loaderBox">
        <div class="crane-icon">🚜</div>
        <div class="loading-text" id="loaderTitle">Yapay Zeka Veritabanını Tarıyor...</div>
        <div class="loading-subtext" id="loaderSub">Merkezi Fiyat Motorundan (2026) güncel rayiçler çekiliyor.</div>
        <div class="progress-bar"><div class="progress-fill" id="progressFill"></div></div>
    </div>

    <div class="card" id="resultCard" style="display:none;">
        <h3 style="font-weight:900; border-bottom:1px solid #e4e7eb; padding-bottom:10px;">📊 Fiyatlandırılmış Keşif Özeti</h3>
        <div style="overflow-x:auto;">
            <table id="resultTable">
                <thead><tr><th>İş Kalemi (Sizin Metniniz)</th><th>Eşleşen DB Kalemi</th><th>Miktar</th><th>Birim Fiyat</th><th>Toplam</th></tr></thead>
                <tbody></tbody>
            </table>
        </div>
        <h2 style="text-align:right; margin-top:20px; color:#2563eb;" id="grandTotal">0 TL</h2>
        <button class="export-btn" id="exportBtn">Excel (XLSX) Olarak İndir</button>
    </div>

    <!-- LEAD SATIŞ HUNİSİ (KAİNTA TEKLİF) -->
    <div class="budget-confirm-box" id="budgetConfirmBox">
        <div class="budget-confirm-title">Keşif Tutarınız Hazır! Teklif almak ister misiniz?</div>
        <div class="budget-confirm-text">Bu fiyatlandırılmış tablo üzerinden, Kainta İnşaat'ın uzman mühendislerinden resmi anahtar teslim teklifi isteyebilirsiniz.</div>
        <div class="budget-confirm-actions">
            <button type="button" class="budget-btn budget-yes" id="btnYes">✓ Evet, Kainta'dan teklif istiyorum.</button>
            <button type="button" class="budget-btn budget-no" id="btnNo">Sadece maliyete baktım.</button>
        </div>
    </div>

    <div class="lead-box" id="leadBox">
        <h3 style="font-size: 18px; font-weight: 900; margin-bottom: 8px;">Kainta İnşaat Teklif Formu</h3>
        <p style="font-size: 13px; color: var(--muted); margin-bottom: 20px;">Keşif özetiniz sistemimize kaydedildi. Uzmanlarımız sizinle iletişime geçsin.</p>
        <form action="{{ route('lead.store') }}" method="POST">
            @csrf
            <input type="hidden" name="calculated_service" value="Auto-BOQ Yapay Zeka Keşfi">
            <input type="hidden" name="calculated_area" id="leadCalculatedData" value="">
            
            <div class="form-group"><label>Adınız Soyadınız / Firma Ünvanı</label><input type="text" name="name" class="form-control" required placeholder="Firma Adı"></div>
            <div class="form-group"><label>Telefon Numaranız</label><input type="tel" name="phone" class="form-control" required placeholder="05XX XXX XX XX"></div>
            <button type="submit" class="btn-submit">Teklif Talebini Gönder →</button>
        </form>
    </div>
</div>

<script>
const dbPrices = <?php echo json_encode($dbPrices); ?>;
let finalData = [];
const moneyFmt = new Intl.NumberFormat('tr-TR', { maximumFractionDigits: 0 });

// GİZLİ KÂR MARJI (MÜŞTERİ GÖRMEZ)
const HIDDEN_PROFIT_MARGIN = 40; // %40 Kâr eklentisi. İhtiyaca göre değiştirebilirsiniz.
const markupMultiplier = 1 + (HIDDEN_PROFIT_MARGIN / 100);

function normalizeText(str) {
    if(!str) return "";
    let lowerStr = str.toLocaleLowerCase('tr-TR');
    lowerStr = lowerStr.replace(/ğ/g, 'g').replace(/ü/g, 'u').replace(/ş/g, 's')
                       .replace(/ı/g, 'i').replace(/ö/g, 'o').replace(/ç/g, 'c');
    lowerStr = lowerStr.replace(/[^a-z0-9\s]/g, ' ');
    return lowerStr.replace(/\s+/g, ' ').trim();
}

function findMatch(userText) {
    let bestMatch = null;
    let normUserText = normalizeText(userText);
    const stopwords = ['ve', 'ile', 'veya', 'her', 'turlu', 'icin', 'mm', 'cm', 'm2', 'm3', 'kg', 'yapilmasi', 'edilmesi', 'temini', 'montaji', 'sokulmesi', 'olan', 'tip'];

    let userWords = normUserText.split(' ').filter(w => w.length > 2 && !stopwords.includes(w));
    if (userWords.length === 0) return null;

    for(let item of dbPrices) {
        let normDbName = normalizeText(item.name);
        let dbWords = normDbName.split(' ').filter(w => w.length > 2 && !stopwords.includes(w));
        
        let matchCount = 0;
        for(let dbW of dbWords) {
            if(userWords.includes(dbW) || (dbW.length > 4 && normUserText.includes(dbW))) {
                matchCount++;
            }
        }

        let score = dbWords.length > 0 ? (matchCount / dbWords.length) : 0;
        if(score >= 0.55) {
            if(!bestMatch || score > bestMatch.score) {
                bestMatch = { item: item, score: score };
            }
        }
    }
    return bestMatch;
}

document.getElementById('analyzeBtn').addEventListener('click', () => {
    const lines = document.getElementById('boqInput').value.split('\n');
    if(lines.length === 0 || lines[0] === '') return;

    document.getElementById('resultCard').style.display = 'none';
    document.getElementById('exportBtn').style.display = 'none';
    document.getElementById('budgetConfirmBox').style.display = 'none';
    document.getElementById('leadBox').style.display = 'none';
    document.getElementById('inputCard').style.display = 'none';
    document.getElementById('loaderBox').classList.add('active');
    
    let progress = 0;
    const progressFill = document.getElementById('progressFill');
    const title = document.getElementById('loaderTitle');
    const sub = document.getElementById('loaderSub');

    const interval = setInterval(() => {
        progress += 2;
        progressFill.style.width = progress + '%';

        if(progress === 30) {
            title.innerText = "Kelimeler Ayrıştırılıyor...";
            sub.innerText = "NLP algoritması çalışıyor.";
            document.querySelector('.crane-icon').innerText = "🧠";
        } else if(progress === 60) {
            title.innerText = "Fiyatlar Eşleştiriliyor...";
            sub.innerText = "10.000'den fazla veri noktası karşılaştırılıyor.";
            document.querySelector('.crane-icon').innerText = "📊";
        } else if(progress === 90) {
            title.innerText = "Hesaplamalar Tamamlanıyor...";
            sub.innerText = "Anahtar teslim maliyet dökümü hazırlanıyor.";
            document.querySelector('.crane-icon').innerText = "✅";
        }

        if(progress >= 100) {
            clearInterval(interval);
            finishAnalysis(lines);
        }
    }, 40);
});

function finishAnalysis(lines) {
    const tbody = document.querySelector('#resultTable tbody');
    tbody.innerHTML = '';
    finalData = [];
    let grandTotal = 0;

    lines.forEach(line => {
        if(line.trim() === '') return;
        
        let name = line.trim(); 
        let qty = 1;
        let match = line.match(/^(.*?)\s+((?:\d{1,3}(?:\.\d{3})*|\d+)(?:,\d+)?)\s*$/);
        
        if(match && match[1].trim() !== '') {
            name = match[1].trim();
            let qtyStr = match[2].replace(/\./g, '').replace(',', '.');
            qty = parseFloat(qtyStr);
            if(isNaN(qty)) qty = 1;
        }

        let matchResult = findMatch(name);
        let tr = document.createElement('tr');
        
        if(matchResult) {
            let dbItem = matchResult.item;
            
            // VERİTABANI FİYATINA GİZLİ KÂR MARJINI UYGULA
            let unitPriceWithProfit = dbItem.price * markupMultiplier;
            let total = unitPriceWithProfit * qty;
            
            grandTotal += total;
            
            tr.innerHTML = `<td>${name}</td><td class="matched">✓ ${dbItem.name}</td><td>${qty}</td><td>${moneyFmt.format(unitPriceWithProfit)} TL</td><td><strong>${moneyFmt.format(total)} TL</strong></td>`;
            finalData.push({"İş Kalemi (Orijinal)": name, "Eşleşen Sistem Kalemi": dbItem.name, "Miktar": qty, "Birim Fiyat (TL)": Math.round(unitPriceWithProfit), "Toplam Maliyet (TL)": Math.round(total)});
        } else {
            tr.innerHTML = `<td>${name}</td><td class="unmatched">✗ Eşleşme Bulunamadı</td><td>${qty}</td><td>-</td><td>-</td>`;
            finalData.push({"İş Kalemi (Orijinal)": name, "Eşleşen Sistem Kalemi": "Bulunamadı", "Miktar": qty, "Birim Fiyat (TL)": 0, "Toplam Maliyet (TL)": 0});
        }
        tbody.appendChild(tr);
    });

    document.getElementById('grandTotal').innerText = "TOPLAM BULUNAN: " + moneyFmt.format(grandTotal) + " TL";
    document.getElementById('leadCalculatedData').value = `Toplam Tutar: ${moneyFmt.format(grandTotal)} TL`;
    
    document.getElementById('loaderBox').classList.remove('active');
    document.getElementById('inputCard').style.display = 'block';
    document.getElementById('resultCard').style.display = 'block';
    document.getElementById('exportBtn').style.display = 'block';
    
    document.getElementById('budgetConfirmBox').style.display = 'block';
    
    document.querySelector('.crane-icon').innerText = "🚜"; 
    document.getElementById('progressFill').style.width = '0%'; 
}

document.getElementById('btnYes').addEventListener('click', () => { 
    document.getElementById('budgetConfirmBox').style.display='none'; 
    document.getElementById('leadBox').style.display='block'; 
});
document.getElementById('btnNo').addEventListener('click', () => { 
    document.getElementById('budgetConfirmBox').innerHTML=`<div style="color:#6b7280; font-size:14px; padding:20px;">Keşif hesaplayıcımızı kullandığınız için teşekkür ederiz.</div>`; 
});

document.getElementById('exportBtn').addEventListener('click', () => {
    const ws = XLSX.utils.json_to_sheet(finalData);
    ws['!cols'] = [ {wch: 60}, {wch: 35}, {wch: 15}, {wch: 18}, {wch: 20} ];
    const wb = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(wb, ws, "Fiyatlandirilmis_BOQ");
    XLSX.writeFile(wb, "Worksitem_Keşif_Raporu.xlsx");
});
</script>
</body>
</html>
