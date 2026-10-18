<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mutfak Yenileme Maliyeti Motoru | WORKSİTEM</title>
    <meta name="description" content="Mutfak tadilatı, dolap, tezgah ve tesisat yenileme maliyetlerini, genel giderleri ve kâr marjını hesaplayın. Haftalık nakit akışı S-eğrisini çıkarın.">
    
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "SoftwareApplication",
      "name": "WORKSİTEM Mutfak Yenileme Hesaplayıcı",
      "operatingSystem": "Web",
      "applicationCategory": "BusinessApplication",
      "offers": { "@type": "Offer", "price": "0", "priceCurrency": "TRY" }
    }
    </script>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        :root { --bg: #f5f6f8; --surface: #ffffff; --text: #101318; --muted: #6b7280; --line: #e4e7eb; --accent: #ffb800; --radius: 16px; }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Inter', sans-serif; background: var(--bg); color: var(--text); line-height: 1.5; padding-bottom: 60px; }
        a { text-decoration: none; color: inherit; }
        
        .header { background: #fff; border-bottom: 1px solid var(--line); padding: 15px 20px; position: sticky; top: 0; z-index: 10; display: flex; align-items: center; justify-content: space-between; }
        .back-btn { font-size: 14px; font-weight: 700; color: var(--muted); display: flex; align-items: center; gap: 5px; }
        .brand img { height: 30px; }

        .container { max-width: 900px; margin: 40px auto; padding: 0 20px; }
        .tool-header { margin-bottom: 30px; text-align: center; }
        .tool-header h1 { font-size: 28px; font-weight: 900; letter-spacing: -1px; margin-bottom: 10px; }
        .tool-header p { color: var(--muted); font-size: 15px; }
        .ai-badge { display: inline-block; background: rgba(16,19,24,0.15); color: #101318; padding: 6px 14px; border-radius: 100px; font-size: 12px; font-weight: 900; margin-bottom: 15px; text-transform: uppercase; }

        .tooltip-word { border-bottom: 1px dashed #101318; color: #101318; cursor: help; position: relative; font-weight: 600; }
        .tooltip-word::after { content: attr(data-tooltip); position: absolute; bottom: 130%; left: 50%; transform: translateX(-50%); background: #111; color: #fff; padding: 8px 12px; border-radius: 8px; font-size: 11px; white-space: nowrap; opacity: 0; visibility: hidden; transition: 0.2s; pointer-events: none; z-index: 20; box-shadow: 0 4px 10px rgba(0,0,0,0.2); }
        .tooltip-word:hover::after { opacity: 1; visibility: visible; }

        .card { background: var(--surface); border: 1px solid var(--line); border-radius: var(--radius); padding: 30px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); margin-bottom: 30px; }
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px; }
        @media(max-width: 600px) { .form-grid { grid-template-columns: 1fr; } }
        
        .form-group label { display: block; font-size: 13px; font-weight: 800; margin-bottom: 8px; }
        .form-control { width: 100%; height: 50px; border: 1px solid var(--line); border-radius: 12px; padding: 0 15px; font-size: 14px; background: #fff; outline: none; }
        .form-control:focus { border-color: var(--accent); box-shadow: 0 0 0 3px rgba(255,184,0,0.15); }
        select.form-control optgroup { font-weight: 800; color: #111; }

        .commercial-box { grid-column: 1 / -1; background: rgba(16,19,24,0.04); padding: 20px; border-radius: 12px; border: 1px dashed rgba(16,19,24,0.3); margin-top: 10px; }
        .commercial-title { font-size: 13px; font-weight: 900; color: #101318; margin-bottom: 15px; display: flex; align-items: center; gap: 8px; }
        
        .permits-box { grid-column: 1 / -1; background: rgba(16, 185, 129, 0.04); padding: 20px; border-radius: 12px; border: 1px dashed rgba(16, 185, 129, 0.3); margin-top: 20px; }
        .permits-title { font-size: 13px; font-weight: 900; color: #059669; margin-bottom: 15px; display: flex; align-items: center; gap: 8px; }
        .checkbox-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
        @media(max-width: 600px) { .checkbox-grid { grid-template-columns: 1fr; } }
        .checkbox-label { display: flex; align-items: center; gap: 10px; font-size: 13px; font-weight: 600; cursor: pointer; color: #4b5563; }
        .checkbox-label input { width: 18px; height: 18px; cursor: pointer; accent-color: #10b981; }

        .slider-group label { display: flex; justify-content: space-between; align-items: center; }
        .slider-val { font-size: 16px; font-weight: 900; color: #101318; background: #fff; padding: 4px 10px; border-radius: 8px; border: 1px solid #fde68a; }
        input[type="range"] { -webkit-appearance: none; width: 100%; height: 6px; background: #e2e8f0; border-radius: 5px; outline: none; margin-top: 10px; }
        input[type="range"]::-webkit-slider-thumb { -webkit-appearance: none; appearance: none; width: 20px; height: 20px; border-radius: 50%; background: #101318; cursor: pointer; }
        
        .result-box { background: #111; color: #fff; border-radius: var(--radius); padding: 30px; text-align: center; margin-top: 10px; display: none; }
        .result-box.active { display: block; animation: fadeIn 0.4s ease; }
        .result-label { font-size: 12px; color: #a1a1aa; font-weight: 700; text-transform: uppercase; }
        .result-price { font-size: 42px; font-weight: 900; color: var(--accent); margin: 5px 0 15px; letter-spacing: -1.5px; }
        
        .breakdown-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-top: 20px; text-align: left; }
        @media(max-width: 600px) { .breakdown-grid { grid-template-columns: 1fr; } }
        .break-item { background: rgba(255,255,255,0.05); padding: 15px; border-radius: 12px; border: 1px solid rgba(255,255,255,0.1); }
        .break-item span { display: block; font-size: 12px; color: #a1a1aa; margin-bottom: 3px; }
        .break-item strong { display: block; font-size: 18px; color: #fff; font-weight: 800; }
        .break-item.profit strong { color: #34d399; }
        .break-item.permit strong { color: #f87171; }

        .formula-box { background: #1f2937; border: 1px dashed #374151; padding: 15px; border-radius: 12px; margin-top: 15px; text-align: left; display: none; }
        .formula-box.active { display: block; }
        .formula-box code { display: block; color: #10b981; font-family: monospace; font-size: 13px; margin-bottom: 5px; }
        .formula-toggle { cursor: pointer; color: #9ca3af; font-size: 12px; font-weight: 700; text-decoration: underline; margin-top: 15px; display: inline-block; }
        
        .chart-container { background: #fff; border-radius: 12px; padding: 20px; margin-top: 25px; display: none; }
        .chart-container.active { display: block; }
        .chart-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; color: #111; }

        .export-btn { background: #101318; color: #fff; border: none; border-radius: 12px; padding: 12px 20px; font-weight: 800; font-size: 14px; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px; width: 100%; margin-top: 15px; transition: .2s; }
        .export-btn:hover { background: #2a2f38; }

        .budget-confirm-box { margin-top: 25px; padding: 25px; border: 1px solid var(--line); border-radius: var(--radius); background: #fff; display: none; text-align: center; }
        .budget-confirm-actions { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-top:15px;}
        .budget-btn { min-height: 52px; border-radius: 13px; font-weight: 800; border: 1.5px solid var(--line); cursor: pointer; }
        .budget-yes { background: #fff; color: #111; }
        .budget-yes:hover { border-color: #8a6200; background: #fff8e1; }
        .budget-no { background: #fff; color: #555; }

        .lead-box { background: #f8f9fb; border: 1px solid var(--line); border-radius: var(--radius); padding: 30px; margin-top: 15px; display: none; }
        .btn { width: 100%; height: 50px; border: none; border-radius: 12px; font-weight: 800; font-size: 15px; cursor: pointer; }
        .btn-calc { background: var(--text); color: #fff; margin-top: 10px; }
        .btn-submit { background: #101318; color: #fff; margin-top: 10px; }
    </style>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body data-tool-slug="mutfak-maliyet-hesaplayici">

<header class="header">
    <a href="/araclar" class="back-btn">← Araçlara Dön</a>
    <a href="/" class="brand"><img src="/200.png" alt="WORKSİTEM"></a>
</header>

<div class="container">
    <div class="tool-header">
        <div class="ai-badge">🛠️ B2B TADİLAT & YENİLEME</div>
        <h1>Mutfak Yenileme Motoru</h1>
        <p>İnce işçilik, ekipman ve malzeme metrajlarına göre profesyonel mutfak bütçenizi ve <span class="tooltip-word" data-tooltip="Tadilat süresince yapılması gereken haftalık harcama grafiği">haftalık nakit akışınızı</span> hesaplayın.</p>
    </div>

    <div class="card">
        {{-- ANA METRAJLAR --}}
        <div class="form-grid">
            <div class="form-group">
                <label>Mutfak Zemin Alanı (m²)</label>
                <input type="number" id="area" class="form-control" value="15" min="4">
            </div>
            <div class="form-group">
                <label>Mutfak Dolabı Uzunluğu (mtul)</label>
                <input type="number" id="cabinetLen" class="form-control" value="6" min="2">
            </div>
        </div>

        {{-- KALİTE SEÇİMLERİ --}}
        <div class="form-grid">
            <div class="form-group">
                <label>Dolap Gövde & Kapak Kalitesi</label>
                <select id="cabinetClass" class="form-control">
                    <option value="6500">MDF Lam Gövde + Membran Kapak (6.500 TL/mtul)</option>
                    <option value="9500" selected>MDF Gövde + Akrilik / High Gloss (9.500 TL/mtul)</option>
                    <option value="14500">MDF Gövde + Lake Boyalı Kapak (14.500 TL/mtul)</option>
                </select>
            </div>
            <div class="form-group">
                <label>Mutfak Tezgahı Kalitesi</label>
                <select id="counterClass" class="form-control">
                    <option value="1800">Laminat Tezgah (1.800 TL/mtul)</option>
                    <option value="4500" selected>Çimstone / Granit Tezgah (4.500 TL/mtul)</option>
                    <option value="8500">Kuvars / Porselen (Belenco vb.) (8.500 TL/mtul)</option>
                </select>
            </div>
        </div>
        
        <div class="form-group">
            <label>Zemin ve Duvar Kaplaması (Seramik)</label>
            <select id="tileClass" class="form-control">
                <option value="650">Standart Seramik Kaplama (650 TL/m²)</option>
                <option value="1200" selected>1. Sınıf Rektifiyeli Granit/Porselen (1.200 TL/m²)</option>
            </select>
        </div>

        {{-- YENİ: İNCE İŞÇİLİK VE EKİPMAN (DEMOLITION) --}}
        <div class="permits-box">
            <div class="permits-title">🚧 Kırım, Ekipman ve İnce İşçilik Katmanı</div>
            <div class="checkbox-grid">
                <label class="checkbox-label"><input type="checkbox" id="chkHafriyat" checked> Eski Mutfak Sökümü & Moloz Atımı (Nakliye)</label>
                <label class="checkbox-label"><input type="checkbox" id="chkTesisat" checked> Su & Elektrik Tesisat Hatları Yenileme</label>
                <label class="checkbox-label"><input type="checkbox" id="chkBoya" checked> Tavan Alçı, Zımpara ve Boya İşleri</label>
                <label class="checkbox-label"><input type="checkbox" id="chkAnkastre"> Yeni Ankastre Set (Ocak, Fırın, Davlumbaz)</label>
            </div>
        </div>

        {{-- TİCARİ MODÜL --}}
        <div class="commercial-box">
            <div class="commercial-title">💼 Ticari Müteahhitlik Modülü (Tadilat)</div>
            <div class="form-grid" style="margin-bottom:0;">
                <div class="form-group slider-group" style="margin-bottom:0;">
                    <label>Genel Gider (Lojistik) <span class="slider-val" id="overheadVal">%5</span></label>
                    <input type="range" id="overhead" min="0" max="20" value="5" step="1" oninput="document.getElementById('overheadVal').innerText='%'+this.value">
                </div>
                <div class="form-group slider-group" style="margin-bottom:0;">
                    <label>Kâr Marjı <span class="slider-val" id="profitVal">%20</span></label>
                    <input type="range" id="profit" min="0" max="30" value="20" step="1" oninput="document.getElementById('profitVal').innerText='%'+this.value">
                </div>
            </div>
            
            <div class="form-group" style="margin-top:15px; margin-bottom:0;">
                <label>Tahmini Tadilat Süresi (Hafta)</label>
                <select id="duration" class="form-control">
                    <option value="2">2 Hafta (Hızlı Teslim)</option>
                    <option value="4" selected>4 Hafta (Standart Komple Yenileme)</option>
                    <option value="8">8 Hafta (Detaylı / Özel İmalat)</option>
                </select>
            </div>
        </div>

        <button type="button" class="btn btn-calc" id="calculateBtn">Detaylı Keşfi ve Nakit Akışını Çıkar →</button>
    </div>

    <div class="result-box" id="resultBox">
        <div class="result-label">ANAHTAR TESLİM MUTFAK BÜTÇESİ</div>
        <div class="result-price" id="totalPrice">0 TL</div>
        <p style="font-size: 11px; color: #6b7280; margin-bottom:20px;">⚠️ KDV, Genel Giderler ve Kâr Dâhildir.</p>

        <span class="formula-toggle" onclick="document.getElementById('formulaBox').classList.toggle('active')">Bu Rakam Nasıl Hesaplanıyor? ▼</span>
        <div class="formula-box" id="formulaBox">
            <code id="formulaCode1">1. Dolap + Tezgah + Seramik Toplamı</code>
            <code id="formulaCode2">2. Kırım/Döküm + Tesisat + Boya Eklenir</code>
            <code id="formulaCode3">3. Alt Toplam üzerinden % Genel Gider hesaplanır</code>
            <code id="formulaCode4">4. Tüm toplama % Kâr Marjı ve %20 KDV yansıtılır</code>
        </div>

        <div class="breakdown-grid">
            <div class="break-item"><span>1. Dolap & Tezgah İmalatı</span><strong id="furnitureCost">0 TL</strong></div>
            <div class="break-item permit"><span>2. Kırım, Hafriyat & Tesisat</span><strong id="civilCost">0 TL</strong></div>
            <div class="break-item highlight"><span>3. Zemin, Tavan & Boya</span><strong id="finishCost">0 TL</strong></div>
            <div class="break-item profit"><span>4. Ticari Kâr + KDV Tutarı</span><strong id="taxCost">0 TL</strong></div>
        </div>
        
        {{-- YENİ: HAFTALIK NAKİT AKIŞI GRAFİĞİ --}}
        <div class="chart-container" id="chartBox">
            <div class="chart-header">
                <span style="font-weight:900; font-size:16px;">📊 Haftalık Nakit Akışı (Tadilat S-Curve)</span>
            </div>
            <canvas id="cashFlowChart" height="120"></canvas>
            <p style="font-size: 10px; color: #6b7280; text-align: left; margin-top: 10px;">
                * Tadilat projelerinde ilk hafta kırım/söküm ve malzeme peşinatı nedeniyle ödeme yüksektir. Son hafta sadece teslimat ve montaj bakiyesi kalır.
            </p>
        </div>

        <button class="export-btn" id="exportExcelBtn" style="margin-top: 25px;">
            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
            Tadilat Keşif Özetini Excel (BOQ) İndir
        </button>
    </div>

    {{-- LEAD FORMU --}}
    <div class="budget-confirm-box" id="budgetConfirmBox">
        <div style="font-size: 18px; font-weight: 900;">Mutfağınızı yenilemeye hazır mısınız?</div>
        <div class="budget-confirm-actions">
            <button class="budget-btn budget-yes" id="btnYes">✓ Evet, keşif ve teklif istiyorum.</button>
            <button class="budget-btn budget-no" id="btnNo">Sadece maliyete baktım.</button>
        </div>
    </div>

    <div class="lead-box" id="leadBox">
        <h3 style="font-size: 18px; font-weight: 900;">Kainta İnşaat'tan Teklif Alın</h3>
        <form action="{{ route('lead.store') }}" method="POST">
            @csrf
            <input type="hidden" name="calculated_service" value="Mutfak Tadilatı (B2B)">
            <input type="hidden" name="calculated_area" id="leadCalculatedData" value="">
            <div class="form-group" style="margin-top:15px;"><input type="text" name="name" class="form-control" required placeholder="Adınız Soyadınız"></div>
            <div class="form-group"><input type="tel" name="phone" class="form-control" required placeholder="Telefonunuz"></div>
            <x-lead-consent-fields />
            <button type="submit" class="btn btn-submit">Hemen Gönder →</button>
        </form>
    </div>
</div>

<script>
// AUTO-SAVE MANTIĞI
document.addEventListener('DOMContentLoaded', () => {
    ['area', 'cabinetLen', 'cabinetClass', 'counterClass', 'tileClass', 'duration', 'overhead', 'profit'].forEach(id => {
        if(localStorage.getItem('mutfak_' + id)) document.getElementById(id).value = localStorage.getItem('mutfak_' + id);
        document.getElementById(id).addEventListener('change', function() { localStorage.setItem('mutfak_' + id, this.value); });
    });
});

let globalExcelData = [];
let cashFlowChartInstance = null;
let currentWeeklyData = [];
const moneyFormatter = new Intl.NumberFormat('tr-TR', { maximumFractionDigits: 0 });

// Tadilat nakit akışı genelde baştan yüklü (peşinat), ortada işçilik, sonda kalan bakiyedir.
function generateRenovationCurve(weeks) {
    let distribution = [];
    if(weeks === 2) return [0.70, 0.30]; // 2 haftalık işte %70 peşin, %30 teslim
    if(weeks === 4) return [0.40, 0.25, 0.20, 0.15]; 
    if(weeks === 8) return [0.30, 0.15, 0.15, 0.10, 0.10, 0.10, 0.05, 0.05];
    return [0.5, 0.5];
}

document.getElementById('calculateBtn').addEventListener('click', () => {
    const area = parseFloat(document.getElementById('area').value) || 0;
    const cabLen = parseFloat(document.getElementById('cabinetLen').value) || 0;
    const cabPrice = parseFloat(document.getElementById('cabinetClass').value) || 0;
    const countPrice = parseFloat(document.getElementById('counterClass').value) || 0;
    const tilePrice = parseFloat(document.getElementById('tileClass').value) || 0;
    
    const overheadRate = parseFloat(document.getElementById('overhead').value) || 0;
    const profitRate = parseFloat(document.getElementById('profit').value) || 0;
    const weeks = parseInt(document.getElementById('duration').value) || 4;
    
    if (area <= 0 || cabLen <= 0) { alert('Alan ve Dolap ölçüleri 0 olamaz.'); return; }

    // MALİYET KALEMLERİ
    const furnitureCost = (cabLen * cabPrice) + (cabLen * countPrice); // Dolap + Tezgah
    
    let civilCost = 0; // Kırım, tesisat vb.
    if(document.getElementById('chkHafriyat').checked) civilCost += 12000 + (area * 300); // Sabit nakliye + m2 kırım
    if(document.getElementById('chkTesisat').checked) civilCost += 18000; // Standart mutfak su/elektrik yenileme
    
    let finishCost = area * tilePrice; // Zemin
    if(document.getElementById('chkBoya').checked) finishCost += (area * 3.5 * 180); // Tavan+Duvar boya (yaklaşık çarpan)
    
    let appliances = document.getElementById('chkAnkastre').checked ? 25000 : 0; // Ankastre seti

    const netCost = furnitureCost + civilCost + finishCost + appliances;
    const overheadCost = netCost * (overheadRate / 100);
    const subTotal = netCost + overheadCost;
    
    const profitCost = subTotal * (profitRate / 100);
    const preTax = subTotal + profitCost;
    const vat = preTax * 0.20; 
    const grandTotal = preTax + vat;

    // UI Güncelleme
    document.getElementById('totalPrice').innerText = moneyFormatter.format(grandTotal) + ' TL';
    document.getElementById('furnitureCost').innerText = moneyFormatter.format(furnitureCost) + ' TL';
    document.getElementById('civilCost').innerText = moneyFormatter.format(civilCost) + ' TL';
    document.getElementById('finishCost').innerText = moneyFormatter.format(finishCost) + ' TL';
    document.getElementById('taxCost').innerText = moneyFormatter.format(profitCost + vat) + ' TL';

    // ŞEFFAF FORMÜL
    document.getElementById('formulaCode1').innerText = `1. Mobilya: ${moneyFormatter.format(furnitureCost)} TL | Seramik+Boya: ${moneyFormatter.format(finishCost)} TL`;
    document.getElementById('formulaCode2').innerText = `2. Tesisat/Hafriyat/Ankastre Eklemeleri: ${moneyFormatter.format(civilCost + appliances)} TL`;
    document.getElementById('formulaCode3').innerText = `3. Alt Toplam (${moneyFormatter.format(netCost)} TL) + %${overheadRate} Genel Gider = ${moneyFormatter.format(subTotal)} TL`;
    document.getElementById('formulaCode4').innerText = `4. ${moneyFormatter.format(subTotal)} TL + %${profitRate} Kâr + %20 KDV = ${moneyFormatter.format(grandTotal)} TL`;

    // NAKİT AKIŞI GRAFİĞİ (Haftalık)
    const curveArr = generateRenovationCurve(weeks);
    let labels = [], dataPoints = [], cumulativeData = [], cumulativeSum = 0;
    currentWeeklyData = [];

    for(let i=0; i<weeks; i++) {
        labels.push((i+1) + ". Hafta");
        let weekCost = grandTotal * curveArr[i];
        dataPoints.push(weekCost);
        cumulativeSum += weekCost;
        cumulativeData.push(cumulativeSum);
        currentWeeklyData.push({ "Dönem": (i+1)+". Hafta", "Haftalık Ödeme (TL)": Math.round(weekCost), "Kümülatif Toplam (TL)": Math.round(cumulativeSum) });
    }

    document.getElementById('chartBox').classList.add('active');
    renderChart(labels, dataPoints, cumulativeData);

    document.getElementById('leadCalculatedData').value = `Mutfak: ${area}m2 | Dolap: ${cabLen}m | Bütçe: ${moneyFormatter.format(grandTotal)} TL`;
    
    // EXCEL BOQ (TADİLAT ÖZEL)
    globalExcelData = [
        { "İş Kalemi": "Mutfak Alanı ve Dolap Metrajı", "Miktar": area + " m² / " + cabLen + " mtul", "Toplam (TL)": "-" },
        { "İş Kalemi": "Mutfak Dolabı (Seçilen Kalite)", "Miktar": "-", "Toplam (TL)": moneyFormatter.format(cabLen * cabPrice) },
        { "İş Kalemi": "Tezgah İmalatı", "Miktar": "-", "Toplam (TL)": moneyFormatter.format(cabLen * countPrice) },
        { "İş Kalemi": "Zemin Seramik / Kaplama", "Miktar": "-", "Toplam (TL)": moneyFormatter.format(area * tilePrice) },
        { "İş Kalemi": "Eski Mutfak Kırım & Moloz Nakliyesi", "Miktar": "-", "Toplam (TL)": document.getElementById('chkHafriyat').checked ? moneyFormatter.format(12000 + (area * 300)) : "0" },
        { "İş Kalemi": "Su ve Elektrik Tesisatı Yenileme", "Miktar": "-", "Toplam (TL)": document.getElementById('chkTesisat').checked ? "18.000" : "0" },
        { "İş Kalemi": "Alçı, Zımpara ve Boya İşleri", "Miktar": "-", "Toplam (TL)": document.getElementById('chkBoya').checked ? moneyFormatter.format(area * 3.5 * 180) : "0" },
        { "İş Kalemi": "------------------", "Miktar": "-", "Toplam (TL)": "-----------------" },
        { "İş Kalemi": "Şantiye & Lojistik Giderleri", "Miktar": "%" + overheadRate, "Toplam (TL)": moneyFormatter.format(overheadCost) },
        { "İş Kalemi": "Yüklenici Kâr Marjı", "Miktar": "%" + profitRate, "Toplam (TL)": moneyFormatter.format(profitCost) },
        { "İş Kalemi": "KDV Tutarı", "Miktar": "%20", "Toplam (TL)": moneyFormatter.format(vat) },
        { "İş Kalemi": "==================", "Miktar": "=", "Toplam (TL)": "=================" },
        { "İş Kalemi": "ANAHTAR TESLİM MUTFAK BÜTÇESİ", "Miktar": "-", "Toplam (TL)": moneyFormatter.format(grandTotal) }
    ];

    document.getElementById('resultBox').classList.add('active');
    document.getElementById('budgetConfirmBox').style.display = 'block';
});

function renderChart(labels, weeklyData, cumulativeData) {
    const ctx = document.getElementById('cashFlowChart').getContext('2d');
    if(cashFlowChartInstance) cashFlowChartInstance.destroy();
    cashFlowChartInstance = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [
                { label: 'Kümülatif Maliyet', data: cumulativeData, type: 'line', borderColor: '#ffb800', backgroundColor: 'rgba(255, 184, 0, 0.1)', fill: true, tension: 0.4 },
                { label: 'Haftalık Nakit Çıkışı', data: weeklyData, backgroundColor: 'rgba(16, 185, 129, 0.7)' }
            ]
        },
        options: { responsive: true, interaction: { mode: 'index', intersect: false } }
    });
}

document.getElementById('exportExcelBtn').addEventListener('click', function() {
    if(globalExcelData.length === 0) return;
    const ws1 = XLSX.utils.json_to_sheet(globalExcelData); ws1['!cols'] = [ {wch: 45}, {wch: 20}, {wch: 25} ];
    const ws2 = XLSX.utils.json_to_sheet(currentWeeklyData); ws2['!cols'] = [ {wch: 15}, {wch: 25}, {wch: 25} ];
    const wb = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(wb, ws1, "Mutfak_Tadilat_BOQ"); XLSX.utils.book_append_sheet(wb, ws2, "Haftalik_Odeme_Plani");
    XLSX.writeFile(wb, "Worksitem_Mutfak_Tadilat_Raporu.xlsx");
});

document.getElementById('btnYes').addEventListener('click', () => { document.getElementById('budgetConfirmBox').style.display='none'; document.getElementById('leadBox').style.display='block'; });
document.getElementById('btnNo').addEventListener('click', () => { document.getElementById('budgetConfirmBox').innerHTML=`<div style="color:#6b7280; font-size:14px;">Teşekkürler.</div>`; });
</script>

    <script src="/js/tool-events.js" defer></script>
</body>
</html>
