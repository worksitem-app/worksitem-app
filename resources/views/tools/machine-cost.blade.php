<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>İş Makinesi & Ekipman Kiralama Maliyeti | WORKSİTEM</title>
    <meta name="description" content="Ekskavatör, kepçe, vinç ve diğer iş makinelerinin saatlik ve günlük kiralama maliyetini, yakıt ve operatör dahil hesaplayın. Ücretsiz.">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.js"></script>
    <style>
        :root { --bg: #f5f6f8; --surface: #ffffff; --text: #101318; --muted: #6b7280; --line: #e4e7eb; --accent: #f97316; --radius: 16px; }
        body { font-family: 'Inter', sans-serif; background: var(--bg); color: var(--text); padding-bottom: 60px; margin:0; }
        .header { background: #fff; padding: 15px 20px; border-bottom: 1px solid var(--line); display: flex; justify-content: space-between; }
        .container { max-width: 900px; margin: 40px auto; padding: 0 20px; }
        .card { background: var(--surface); border: 1px solid var(--line); border-radius: var(--radius); padding: 30px; margin-bottom: 20px; }
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px; }
        label { display: block; font-size: 13px; font-weight: 800; margin-bottom: 8px; }
        .form-control { width: 100%; height: 50px; border: 1px solid var(--line); border-radius: 12px; padding: 0 15px; font-size: 14px; }
        .form-control:focus { border-color: var(--accent); outline: none; }
        .btn { background: var(--text); color: #fff; padding: 15px; border-radius: 12px; border: none; font-weight: 800; cursor: pointer; width: 100%; }
        .result-box { background: #111; color: #fff; border-radius: var(--radius); padding: 30px; text-align: center; display: none; }
        .result-price { font-size: 42px; font-weight: 900; color: var(--accent); margin: 5px 0 15px; }
        .chart-box { background: rgba(255,255,255,0.05); border-radius: 12px; padding: 20px; margin-top:20px; }
    </style>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <x-calc.head />
</head>
<body class="wc" data-tool-slug="machine-cost">
<x-site.contech-header />
<div class="container">
    <div style="text-align:center; margin-bottom:30px;">
        <div style="background: rgba(249, 115, 22, 0.1); color: var(--accent); padding: 6px 14px; border-radius: 100px; font-size: 12px; font-weight: 900; display:inline-block;">🚜 B2B EKİPMAN MODÜLÜ</div>
        <x-calc.hero title="İş makinesi maliyeti hesaplama" crumb="İş makinesi">Kira, yakıt ve operatör giderlerini birleştirerek gerçek saatlik ve aylık şantiye ekipman maliyetinizi hesaplayın.</x-calc.hero>
    </div>

    <div class="card">
        <div class="form-grid">
            <div><label>Makine Türü</label>
            <select id="machineType" class="form-control">
                <option value="8000" data-fuel="12">Paletli Ekskavatör (30 Ton) - 8.000 TL/Gün</option>
                <option value="5000" data-fuel="8">Beko Loder (Kazıcı Yükleyici) - 5.000 TL/Gün</option>
                <option value="12000" data-fuel="15">Mobil Vinç (35 Ton) - 12.000 TL/Gün</option>
                <option value="250000" data-fuel="0">Kule Vinç (Aylık Sabit) - 250.000 TL/Ay</option>
            </select></div>
            <div><label>Çalışma Süresi (Gün)</label><input type="number" id="days" class="form-control" value="30"></div>
            <div><label>Motorin (Mazot) Litre Fiyatı (TL)</label><input type="number" id="fuelPrice" class="form-control" value="45"></div>
            <div><label>Operatör Günlük Yevmiye (TL)</label><input type="number" id="operator" class="form-control" value="2500"></div>
        </div>
        <button class="btn" id="calcBtn">Toplam Makine Amortismanını Hesapla</button>
    </div>

    <div class="result-box" id="resBox" data-wc-result>
        <div style="font-size:12px; color:#a1a1aa; font-weight:700;">TOPLAM EKİPMAN BÜTÇESİ</div>
        <div class="result-price" id="totalCost" data-wc-price>0 TL</div>
        <div style="display:flex; justify-content:space-between; margin-top:20px; border-top:1px solid #333; padding-top:15px; text-align:left;">
            <div><span style="color:#a1a1aa; font-size:12px;">Makine Kirası</span><br><strong id="rentCost">0 TL</strong></div>
            <div><span style="color:#a1a1aa; font-size:12px;">Yakıt Gideri</span><br><strong id="fuelCost">0 TL</strong></div>
            <div><span style="color:#a1a1aa; font-size:12px;">Operatör Gideri</span><br><strong id="opCost">0 TL</strong></div>
        </div>
        <div class="chart-box"><canvas id="costChart" height="100"></canvas></div>
    </div>
</div>
<script>
let chartInst = null;
document.getElementById('calcBtn').addEventListener('click', () => {
    const sel = document.getElementById('machineType');
    const rentVal = parseFloat(sel.value);
    const fuelCons = parseFloat(sel.options[sel.selectedIndex].dataset.fuel);
    const days = parseFloat(document.getElementById('days').value);
    const fuelPrice = parseFloat(document.getElementById('fuelPrice').value);
    const opPrice = parseFloat(document.getElementById('operator').value);

    // Kule vinç aylık girilmişse günü aya çevir
    let isMonthly = rentVal > 100000; 
    let rentCost = isMonthly ? (rentVal * (days/30)) : (rentVal * days);
    
    // Günde 8 saat çalışma varsayımı
    let fuelCost = (fuelCons * 8 * days) * fuelPrice;
    let opCost = opPrice * days;
    let total = rentCost + fuelCost + opCost;

    const fmt = new Intl.NumberFormat('tr-TR', { maximumFractionDigits: 0 });
    document.getElementById('totalCost').innerText = fmt.format(total) + " TL";
    document.getElementById('rentCost').innerText = fmt.format(rentCost) + " TL";
    document.getElementById('fuelCost').innerText = fmt.format(fuelCost) + " TL";
    document.getElementById('opCost').innerText = fmt.format(opCost) + " TL";
    
    document.getElementById('resBox').style.display = 'block';

    if(chartInst) chartInst.destroy();
    chartInst = new Chart(document.getElementById('costChart'), {
        type: 'doughnut',
        data: {
            labels: ['Makine Kirası', 'Yakıt Gideri', 'Operatör Gideri'],
            datasets: [{ data: [rentCost, fuelCost, opCost], backgroundColor: ['#f97316', '#ef4444', '#101318'], borderWidth: 0 }]
        },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'right', labels: {color:'#fff'} } } }
    });
});
</script>
    <script src="/js/tool-events.js" defer></script>
<x-calc.kit />
    <x-site.contech-footer />
</body>
</html>
