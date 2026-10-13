<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bina İnşaat Maliyeti Motoru (ÇŞB 2026) | WORKSİTEM</title>
    <meta name="description" content="ÇŞB yapı sınıflarına göre inşaat maliyetini, resmi harçları, genel giderleri, kâr marjını hesaplayın ve aylık nakit akışı grafiğini çıkarın.">
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
        
        /* YENİ: İzinler ve Şantiye Kurulum Box */
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
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
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
        .risk-toggle { display: flex; align-items: center; gap: 8px; cursor: pointer; }
        .risk-toggle input { width: 18px; height: 18px; cursor: pointer; }
        .risk-toggle span { font-size: 12px; font-weight: 700; color: #ef4444; }

        .ai-insight { background: rgba(255, 184, 0, 0.1); border: 1px solid rgba(255, 184, 0, 0.3); padding: 15px; border-radius: 12px; text-align: left; display: flex; gap: 12px; margin-top: 25px; }
        .ai-insight-text strong { color: var(--accent); display: block; font-size: 14px; margin-bottom: 4px; }
        .ai-insight-text p { color: #e4e4e7; font-size: 12px; margin: 0; line-height: 1.5; }

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
    <x-calc.head />
</head>
<body class="wc" data-tool-slug="building-cost-calculator">

<x-site.contech-header />

<main class="wc-wrap">
    <x-calc.hero title="Bina inşaat maliyeti ve nakit akışı hesaplama" crumb="Bina maliyeti">
        Bakanlığın güncel ÇŞB yapı sınıflarına göre projenizin net yapım maliyetini, yasal harçlarını, kârını hesaplayın ve aylık ödeme planını simüle edin.
    </x-calc.hero>

    <div class="wc-grid">
    <div class="card wc-panel">
        <div class="form-grid">
            <div class="form-group">
                <label><span class="tooltip-word" data-tooltip="Otopark, sığınak ve balkonlar dahil yapının tüm alanıdır.">Toplam Brüt İnşaat Alanı (m²)</span></label>
                <input type="number" id="area" class="form-control" value="1200" min="50">
            </div>
            <div class="form-group">
                <label>Malzeme Kalitesi</label>
                <select id="quality" class="form-control">
                    <option value="0.85">Ekonomik Sınıf (-%15)</option>
                    <option value="1.0" selected>Standart Sınıf (Rayiç)</option>
                    <option value="1.25">Lüks Sınıf (+%25)</option>
                </select>
            </div>
        </div>

        <div class="form-grid">
            <div class="form-group">
                <label>Yapı Sınıfı (ÇŞB 2026)</label>
                <select id="buildingClass" class="form-control">
                    <option value="15100">Köy Evi / Bungalov - (15.100 TL)</option>
                    <option value="21050" selected>Apartman / Müstakil Ev - (21.050 TL)</option>
                    <option value="26450">Rezidans / AVM / Otel - (26.450 TL)</option>
                    <option value="42350">Stadyum / Üniversite Kampüsü - (42.350 TL)</option>
                    <option value="48750">Büyük Hastane / Tarihi Eser - (48.750 TL)</option>
                    <option value="53500">Şehir Hastanesi / 5 Yıldız Otel - (53.500 TL)</option>
                </select>
            </div>
            <div class="form-group">
                <label>Tahmini Proje Süresi</label>
                <select id="duration" class="form-control">
                    <option value="6">6 Ay (Hızlı Teslim)</option>
                    <option value="12" selected>12 Ay (Standart Süre)</option>
                    <option value="18">18 Ay (Büyük Ölçekli)</option>
                    <option value="24">24 Ay (Mega Proje)</option>
                </select>
            </div>
        </div>

        <div class="commercial-box">
            <div class="commercial-title">💼 Ticari Müteahhitlik Modülü</div>
            <div class="form-grid" style="margin-bottom:0;">
                <div class="form-group slider-group" style="margin-bottom:0;">
                    <label>Genel Gider <span class="slider-val" id="overheadVal">%8</span></label>
                    <input type="range" id="overhead" min="0" max="20" value="8" step="1" oninput="document.getElementById('overheadVal').innerText='%'+this.value">
                </div>
                <div class="form-group slider-group" style="margin-bottom:0;">
                    <label><span class="tooltip-word" data-tooltip="Yüklenici firmanın organizasyon ve risk primidir">Kâr Marjı</span> <span class="slider-val" id="profitVal">%15</span></label>
                    <input type="range" id="profit" min="0" max="30" value="15" step="1" oninput="document.getElementById('profitVal').innerText='%'+this.value">
                </div>
            </div>
        </div>

        <!-- YENİ: İZİNLER, HARÇLAR VE ŞANTİYE KURULUMU -->
        <div class="permits-box">
            <div class="permits-title">📜 Yasal İzinler & Ek Maliyetler (Opsiyonel)</div>
            <div class="checkbox-grid">
                <label class="checkbox-label"><input type="checkbox" id="chkZemin" checked> Zemin Etüdü & Harita</label>
                <label class="checkbox-label"><input type="checkbox" id="chkRuhsat" checked> Yapı Ruhsatı & İskan Harçları</label>
                <label class="checkbox-label"><input type="checkbox" id="chkSantiye" checked> Mobilizasyon (Şantiye Kurulumu)</label>
                <label class="checkbox-label"><input type="checkbox" id="chkYapiDenetim" checked> Yapı Denetim Hizmet Bedeli</label>
            </div>
        </div>

        <button type="button" class="btn btn-calc" id="calculateBtn">Maliyet ve Nakit Akışını Çıkar →</button>
    </div>

    <aside class="wc-aside">
    <div class="wc-empty" data-wc-empty>
        <strong>Sonuç burada görünecek</strong>
        Bilgileri girip hesaplayın; sonuç, döküm ve yaklaşık bütçe burada görünür.
    </div>
    <div class="result-box wc-result" data-wc-result id="resultBox">
        <div class="result-label">ANAHTAR TESLİM TOPLAM BÜTÇE</div>
        <div class="result-price" id="totalPrice" data-wc-price>0 TL</div>
        <p style="font-size: 11px; color: #6b7280; margin-bottom:20px;">⚠️ KDV, Genel Giderler ve Kâr Dâhildir. Arsa payı hariçtir.</p>

        <span class="formula-toggle" onclick="document.getElementById('formulaBox').classList.toggle('active')">Bu Rakam Nasıl Hesaplanıyor? (Şeffaflık Raporu) ▼</span>
        <div class="formula-box" id="formulaBox">
            <code id="formulaCode1">1. Taban: Alan x Rayiç</code>
            <code id="formulaCode2">2. Net İnşaat: Taban x Kalite Çarpanı</code>
            <code id="formulaCode3">3. Alt Toplam: Net + Genel Gider</code>
            <code id="formulaCode4">4. Yasal İzinler: Ruhsat, Yapı Denetim, Şantiye Kurulumu Eklenir</code>
            <code id="formulaCode5">5. Bütçe: Alt Toplam + Yasal İzinler + Kâr + KDV + Proje Bedeli</code>
        </div>

        <div class="breakdown-grid">
            <div class="break-item"><span>1. Net İnşaat Maliyeti</span><strong id="netCost">0 TL</strong></div>
            <div class="break-item permit"><span>2. İzinler ve Ek Giderler Toplamı</span><strong id="permitsCost">0 TL</strong></div>
            <div class="break-item highlight"><span>3. Şantiye Genel Giderleri</span><strong id="overheadCost">0 TL</strong></div>
            <div class="break-item profit"><span>4. Müteahhit Kâr Marjı</span><strong id="profitCost">0 TL</strong></div>
        </div>
        
        <div class="chart-container" id="chartBox">
            <div class="chart-header">
                <span style="font-weight:900; font-size:16px;">📊 Aylık Nakit Akışı (S-Curve)</span>
                <label class="risk-toggle"><input type="checkbox" id="riskToggle"><span>Enflasyon/Risk Analizi</span></label>
            </div>
            <canvas id="cashFlowChart" height="120"></canvas>
        </div>

        <button class="export-btn" id="exportExcelBtn" style="margin-top: 25px;">
            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
            Kapsamlı Keşif ve BOQ Raporunu Excel Olarak İndir
        </button>
        
        <div class="ai-insight" style="margin-top: 25px;">
            <div class="ai-insight-icon">💡</div>
            <div class="ai-insight-text">
                <strong>Yatırımcı / Müteahhit Notu</strong>
                <p id="insightText">Hesaplanıyor...</p>
            </div>
        </div>
    </div>
    </aside>
    </div>{{-- /wc-grid --}}

    <div class="wc-below">

    <!-- ... (Aşağıdaki Lead form html'i aynı) ... -->
    <div class="budget-confirm-box" id="budgetConfirmBox">
        <div style="font-size: 18px; font-weight: 900;">Projeniz hazır mı? Teklif alın.</div>
        <div class="budget-confirm-actions">
            <button class="budget-btn budget-yes" id="btnYes">✓ Evet, teklif istiyorum.</button>
            <button class="budget-btn budget-no" id="btnNo">Sadece maliyete baktım.</button>
        </div>
    </div>

    <div class="lead-box" id="leadBox">
        <h3 style="font-size: 18px; font-weight: 900;">Kainta İnşaat'tan Teklif Alın</h3>
        <form action="{{ route('lead.store') }}" method="POST">
            @csrf
            <input type="hidden" name="calculated_service" value="Bina İnşaat (Kapsamlı)">
            <input type="hidden" name="calculated_area" id="leadCalculatedData" value="">
            <div class="form-group" style="margin-top:15px;"><input type="text" name="name" class="form-control" required placeholder="Adınız Soyadınız"></div>
            <div class="form-group"><input type="tel" name="phone" class="form-control" required placeholder="Telefonunuz"></div>
            <x-lead-consent-fields />
            <button type="submit" class="btn btn-submit">Gönder →</button>
        </form>
    </div>
    </div>{{-- /wc-below --}}
</main>

<script>
document.addEventListener('DOMContentLoaded', () => {
    ['area', 'quality', 'buildingClass', 'duration', 'overhead', 'profit'].forEach(id => {
        if(localStorage.getItem('ws_' + id)) document.getElementById(id).value = localStorage.getItem('ws_' + id);
        document.getElementById(id).addEventListener('change', function() { localStorage.setItem('ws_' + id, this.value); });
    });
});

let globalExcelData = [];
let cashFlowChartInstance = null;
let currentMonthlyData = [];
const moneyFormatter = new Intl.NumberFormat('tr-TR', { maximumFractionDigits: 0 });

function generateBellCurveDistribution(months) {
    let distribution = []; let sum = 0;
    for(let i=0; i<months; i++) {
        let x = -3 + (6 * (i / (months-1)));
        let y = Math.exp(-Math.pow(x, 2) / 2); 
        distribution.push(y); sum += y;
    }
    return distribution.map(val => val / sum);
}

document.getElementById('calculateBtn').addEventListener('click', calculateAll);
document.getElementById('riskToggle').addEventListener('change', calculateAll);

function calculateAll() {
    const area = parseFloat(document.getElementById('area').value) || 0;
    const basePrice = parseFloat(document.getElementById('buildingClass').value) || 0;
    const quality = parseFloat(document.getElementById('quality').value) || 1.0;
    const overheadRate = parseFloat(document.getElementById('overhead').value) || 0;
    const profitRate = parseFloat(document.getElementById('profit').value) || 0;
    const months = parseInt(document.getElementById('duration').value) || 12;
    const applyRisk = document.getElementById('riskToggle').checked;
    
    if (area <= 0) { alert('Geçerli bir metrekare giriniz.'); return; }

    const officialCost = area * basePrice;
    const netCost = officialCost * quality;
    
    // İZİNLER VE EKLENTİLER MATEMATİĞİ
    let zeminEtud = document.getElementById('chkZemin').checked ? (area * 30) + 15000 : 0; // m2 başına + sabit
    let ruhsatHarc = document.getElementById('chkRuhsat').checked ? (officialCost * 0.015) : 0; // %1.5 ortalama ruhsat/iskan
    let santiyeKurulum = document.getElementById('chkSantiye').checked ? (netCost * 0.02) : 0; // %2 mobilizasyon
    let yapiDenetim = document.getElementById('chkYapiDenetim').checked ? (officialCost * 0.015) : 0; // %1.5 yapı denetim payı
    
    let totalPermits = zeminEtud + ruhsatHarc + santiyeKurulum + yapiDenetim;

    const overheadCost = netCost * (overheadRate / 100);
    const subTotal = netCost + overheadCost + totalPermits;
    const profitCost = subTotal * (profitRate / 100);
    const preTax = subTotal + profitCost;
    const projectFee = officialCost * 0.04; 
    const vat = preTax * 0.20; 
    
    const grandTotal = preTax + vat + projectFee;

    document.getElementById('totalPrice').innerText = moneyFormatter.format(grandTotal) + ' TL';
    document.getElementById('netCost').innerText = moneyFormatter.format(netCost) + ' TL';
    document.getElementById('permitsCost').innerText = '+ ' + moneyFormatter.format(totalPermits) + ' TL';
    document.getElementById('overheadCost').innerText = '+ ' + moneyFormatter.format(overheadCost) + ' TL';
    document.getElementById('profitCost').innerText = '+ ' + moneyFormatter.format(profitCost) + ' TL';

    document.getElementById('formulaCode1').innerText = `1. ${area} m² x ${moneyFormatter.format(basePrice)} = ${moneyFormatter.format(officialCost)} TL`;
    document.getElementById('formulaCode2').innerText = `2. ${moneyFormatter.format(officialCost)} x ${quality} = ${moneyFormatter.format(netCost)} TL`;
    document.getElementById('formulaCode3').innerText = `3. ${moneyFormatter.format(netCost)} + %${overheadRate} = ${moneyFormatter.format(netCost+overheadCost)} TL`;
    document.getElementById('formulaCode4').innerText = `4. Yasal İzinler (${moneyFormatter.format(totalPermits)} TL) Eklenir = ${moneyFormatter.format(subTotal)} TL`;
    document.getElementById('formulaCode5').innerText = `5. ${moneyFormatter.format(subTotal)} + %${profitRate} Kâr + KDV = ${moneyFormatter.format(grandTotal)} TL`;

    const curveArr = generateBellCurveDistribution(months);
    let labels = [], dataPoints = [], cumulativeData = [], cumulativeSum = 0;
    currentMonthlyData = [];
    let inflationRate = applyRisk ? 0.03 : 0; 

    for(let i=0; i<months; i++) {
        labels.push((i+1) + ". Ay");
        let rawMonthCost = grandTotal * curveArr[i];
        let finalMonthCost = rawMonthCost * Math.pow(1 + inflationRate, i);
        dataPoints.push(finalMonthCost);
        cumulativeSum += finalMonthCost;
        cumulativeData.push(cumulativeSum);
        currentMonthlyData.push({ "Dönem": (i+1)+". Ay", "Aylık Gider (TL)": Math.round(finalMonthCost), "Kümülatif Toplam (TL)": Math.round(cumulativeSum) });
    }

    document.getElementById('chartBox').classList.add('active');
    renderChart(labels, dataPoints, cumulativeData, applyRisk);

    const insight = document.getElementById('insightText');
    if(applyRisk) {
        insight.innerHTML = `<strong>Enflasyon Risk Modu Aktif:</strong> %3'lük öngörülen maliyet artışlarıyla <strong>ilk baştaki ${moneyFormatter.format(grandTotal)} TL bütçe, ${months} ayın sonunda ${moneyFormatter.format(cumulativeSum)} TL'ye</strong> çıkabilir. Demir ve çimento bağlantısını peşin yapmanız riski azaltır.`;
    } else {
        insight.innerHTML = `Projeye dahil edilen Yasal İzinler ve Harçlar (${moneyFormatter.format(totalPermits)} TL) ilk 3 ay içerisinde nakden ödenecektir. S-Eğrisi (Nakik akışı) modelini finansman planlamanızda baz alabilirsiniz.`;
    }

    document.getElementById('leadCalculatedData').value = `Alan: ${area} | Süre: ${months} | Bütçe: ${moneyFormatter.format(grandTotal)} TL`;
    
    // DEV EXCEL BOQ
    globalExcelData = [
        { "Proje Kalemi": "Toplam İnşaat Alanı", "Miktar": area, "Birim": "m²", "Toplam (TL)": "-" },
        { "Proje Kalemi": "Resmi ÇŞB Rayici", "Miktar": "-", "Birim": "-", "Toplam (TL)": moneyFormatter.format(basePrice) },
        { "Proje Kalemi": "NET İNŞAAT YAPIM MALİYETİ", "Miktar": "-", "Birim": "-", "Toplam (TL)": moneyFormatter.format(netCost) },
        { "Proje Kalemi": "------------------", "Miktar": "-", "Birim": "-", "Toplam (TL)": "-----------------" },
        { "Proje Kalemi": "Zemin Etüdü ve Harita İşleri", "Miktar": "-", "Birim": "Adet", "Toplam (TL)": document.getElementById('chkZemin').checked ? moneyFormatter.format(zeminEtud) : "0" },
        { "Proje Kalemi": "Ruhsat ve İskan Harçları", "Miktar": "-", "Birim": "Paket", "Toplam (TL)": document.getElementById('chkRuhsat').checked ? moneyFormatter.format(ruhsatHarc) : "0" },
        { "Proje Kalemi": "Şantiye Kurulumu (Mobilizasyon)", "Miktar": "-", "Birim": "Paket", "Toplam (TL)": document.getElementById('chkSantiye').checked ? moneyFormatter.format(santiyeKurulum) : "0" },
        { "Proje Kalemi": "Yapı Denetim Hizmet Bedeli", "Miktar": area, "Birim": "m²", "Toplam (TL)": document.getElementById('chkYapiDenetim').checked ? moneyFormatter.format(yapiDenetim) : "0" },
        { "Proje Kalemi": "------------------", "Miktar": "-", "Birim": "-", "Toplam (TL)": "-----------------" },
        { "Proje Kalemi": "Şantiye Genel Giderleri", "Miktar": "%" + overheadRate, "Birim": "Oran", "Toplam (TL)": moneyFormatter.format(overheadCost) },
        { "Proje Kalemi": "Yüklenici Kâr Marjı", "Miktar": "%" + profitRate, "Birim": "Oran", "Toplam (TL)": moneyFormatter.format(profitCost) },
        { "Proje Kalemi": "KDV Tutarı", "Miktar": "%20", "Birim": "Vergi", "Toplam (TL)": moneyFormatter.format(vat) },
        { "Proje Kalemi": "Mimari, Statik, Mekanik Projeler", "Miktar": "1", "Birim": "Takım", "Toplam (TL)": moneyFormatter.format(projectFee) },
        { "Proje Kalemi": "==================", "Miktar": "=", "Birim": "=", "Toplam (TL)": "=================" },
        { "Proje Kalemi": "ANAHTAR TESLİM TOPLAM BÜTÇE", "Miktar": "-", "Birim": "-", "Toplam (TL)": moneyFormatter.format(grandTotal) }
    ];

    document.getElementById('resultBox').classList.add('active');
    document.getElementById('budgetConfirmBox').style.display = 'block';
}

function renderChart(labels, monthlyData, cumulativeData, isRisk) {
    const ctx = document.getElementById('cashFlowChart').getContext('2d');
    if(cashFlowChartInstance) cashFlowChartInstance.destroy();
    cashFlowChartInstance = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [
                { label: 'Kümülatif Maliyet', data: cumulativeData, type: 'line', borderColor: '#ffb800', backgroundColor: 'rgba(255, 184, 0, 0.1)', fill: true, tension: 0.4 },
                { label: 'Aylık Nakit Çıkışı', data: monthlyData, backgroundColor: isRisk ? 'rgba(239, 68, 68, 0.7)' : 'rgba(16,19,24,0.7)' }
            ]
        },
        options: { responsive: true, interaction: { mode: 'index', intersect: false } }
    });
}

document.getElementById('exportExcelBtn').addEventListener('click', function() {
    if(globalExcelData.length === 0) return;
    const ws1 = XLSX.utils.json_to_sheet(globalExcelData); ws1['!cols'] = [ {wch: 40}, {wch: 15}, {wch: 10}, {wch: 25} ];
    const ws2 = XLSX.utils.json_to_sheet(currentMonthlyData); ws2['!cols'] = [ {wch: 15}, {wch: 25}, {wch: 25} ];
    const wb = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(wb, ws1, "Kapsamli_BOQ_Maliyeti"); XLSX.utils.book_append_sheet(wb, ws2, "Nakit_Akisi");
    XLSX.writeFile(wb, "Worksitem_Kapsamli_Proje_Fizibilitesi.xlsx");
});

document.getElementById('btnYes').addEventListener('click', () => { document.getElementById('budgetConfirmBox').style.display='none'; document.getElementById('leadBox').style.display='block'; });
document.getElementById('btnNo').addEventListener('click', () => { document.getElementById('budgetConfirmBox').innerHTML=`<div style="color:#6b7280; font-size:14px;">Teşekkürler. İhale aşamasında görüşmek üzere.</div>`; });
</script>

    <script src="/js/tool-events.js" defer></script>
    <x-calc.kit />
<x-tools.tool-guide slug="tahmini-bina-maliyeti" />

<x-site.contech-footer />
</body>
</html>
