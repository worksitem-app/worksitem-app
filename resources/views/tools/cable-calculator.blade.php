<!DOCTYPE html>
<html lang="tr">
<head>
    <link rel="icon" href="/favicon.ico" type="image/x-icon">
<meta charset="UTF-8"><title>Kablo Kesit Analizi | WORKSİTEM</title><link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet"><style>:root { --bg: #f8fafc; --card-bg: #ffffff; --border: #e2e8f0; --primary: #101318; --text: #0f172a; --muted: #64748b; --cyan: #8a6200; --green: #059669; --yellow:#d97706;} body { font-family: 'Inter', sans-serif; background: var(--bg); color: var(--text); margin: 0; padding: 0; } .container { max-width: 1200px; margin: 40px auto; padding: 0 4%; display: grid; grid-template-columns: 1fr 1fr; gap: 40px; } @media(max-width: 992px) { .container { grid-template-columns: 1fr; } } .form-card { background: var(--card-bg); border: 1px solid var(--border); border-radius: 12px; padding: 30px; margin-bottom: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);} h1 { font-family:'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; font-size: 38px; color: var(--text); margin-bottom: 10px; } .form-group { margin-bottom: 20px; } .form-group label { display: block; font-size: 13px; font-weight: 700; color: var(--muted); margin-bottom: 8px; text-transform: uppercase; } .form-control { width: 100%; padding: 12px; border-radius: 6px; background: #f8fafc; border: 1px solid var(--border); color: var(--text); font-family: inherit; } .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; } .res-item { display: flex; justify-content: space-between; padding: 12px 0; border-bottom: 1px dashed var(--border); font-size: 14px; } .res-item.total { border-bottom: none; font-size: 24px; font-weight: 800; color: var(--yellow); margin-top: 15px; } .ai-box { background: #fef9c3; border: 1px solid #fde047; border-radius: 8px; padding: 20px; margin-top: 20px; }</style>    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body data-tool-slug="cable-calculator">
    <div style="padding:15px 4%; border-bottom:1px solid var(--border); background:#fff;"><a href="/araclar" style="color:var(--text); text-decoration:none; font-family:'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; font-size:24px;">◀ WORKSİTEM ARAÇLAR</a></div>
    <div class="container">
        <div><h1>Kablo Kesiti & Gerilim Düşümü</h1>
            <div class="form-card">
                <div class="grid-2">
                    <div class="form-group"><label>Çekilen Güç (kW)</label><input type="number" id="p" class="form-control" value="10" step="0.5" oninput="calc()"></div>
                    <div class="form-group"><label>Kablo Mesafesi (m)</label><input type="number" id="l" class="form-control" value="50" oninput="calc()"></div>
                </div>
                <div class="form-group">
                    <label>Sistem Gerilimi</label>
                    <select id="v" class="form-control" onchange="calc()">
                        <option value="220">Monofaze (220V - Ev Tipi)</option>
                        <option value="380" selected>Trifaze (380V - Sanayi Tipi)</option>
                    </select>
                </div>
            </div>
        </div>
        <div>
            <div class="form-card" style="border-color:var(--yellow); position:sticky; top:20px;">
                <div style="font-family:'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; font-size:24px; color:var(--yellow); margin-bottom:15px;">📊 ELEKTRİK ANALİZİ</div>
                <div class="res-item"><span>Çekilen Tahmini Akım</span> <strong id="r_amp">0 Amper</strong></div>
                <div class="res-item"><span>Gerilim Düşümü Oranı (%e)</span> <strong id="r_drop">0 %</strong></div>
                <div class="res-item total"><span>ÖNERİLEN KABLO KESİTİ</span> <span id="r_cable">0 mm² Bakır</span></div>
                <div class="ai-box"><div style="color:#b45309; font-family:'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; font-size:22px;">🤖 Elektrik Mühendisi Notu</div><ul id="ai_notes" style="font-size:13px; color:#0f172a; margin:0; padding-left:15px; line-height:1.6;"></ul></div>
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
            let p = parseFloat(document.getElementById('p').value)||0; let l = parseFloat(document.getElementById('l').value)||0; let v = parseFloat(document.getElementById('v').value)||380;
            
            // P = W (Watt)
            let watt = p * 1000;
            let current = v === 380 ? (watt / (1.732 * v * 0.8)) : (watt / (v * 0.8)); // cos phi = 0.8 varsayımı
            
            // Bakır öziletkenliği k = 56
            // Monofaze e% = (200 * P_kw * L) / (k * S * V^2) => kesit S çekeriz
            // Trifaze e% = (100 * P_kw * L) / (k * S * V^2) 
            // İç tesisat yönetmeliğine göre maks aydınlatma %1.5, motor %3. Biz %1.5 hedefleyelim.
            let maxDrop = 1.5;
            let reqS = 0;
            if(v === 220) reqS = (200 * p * l) / (56 * maxDrop * Math.pow(v, 2));
            else reqS = (100 * p * l) / (56 * maxDrop * Math.pow(v, 2));

            // Piyasada bulunan standart NYM/NYY kesitleri (mm2): 1.5, 2.5, 4, 6, 10, 16, 25, 35, 50
            let standardCables = [1.5, 2.5, 4, 6, 10, 16, 25, 35, 50, 70, 95];
            let actualCable = standardCables.find(c => c >= reqS);
            if(!actualCable) actualCable = ">95"; // Çok yüksek

            // Gerçek gerilim düşümü (Seçilen kabloya göre)
            let actualDrop = 0;
            if(actualCable !== ">95") {
                if(v === 220) actualDrop = (200 * p * l) / (56 * actualCable * Math.pow(v, 2));
                else actualDrop = (100 * p * l) / (56 * actualCable * Math.pow(v, 2));
            }

            document.getElementById('r_amp').innerText = current.toFixed(1) + ' Amper';
            document.getElementById('r_drop').innerText = '%' + actualDrop.toFixed(2);
            document.getElementById('r_cable').innerText = actualCable + ' mm²';

            let notesBox = document.getElementById('ai_notes'); notesBox.innerHTML = '';
            notesBox.innerHTML += '<li>İç Tesisleri Yönetmeliğine göre aydınlatma devrelerinde %1.5, motor devrelerinde %3 gerilim düşümüne izin verilir. Biz güvenli sınır olan %1.5 üzerinden hesapladık.</li>';
            if(current > 40 && v === 220) notesBox.innerHTML += '<li style="color:#b91c1c;">Uyarı: Ev tipi (Monofaze) hatta 40 Amper üzerinde yük çekiyorsunuz. Tesisatı dengelemek için Trifaze (3 Faz) sisteme geçilmesi önerilir.</li>';
        } document.addEventListener('DOMContentLoaded', calc);
    </script>
    <script src="/js/tool-events.js" defer></script>
</body>
</html>
