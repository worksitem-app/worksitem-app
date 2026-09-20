<!DOCTYPE html>
<html lang="tr">
<head>
    <link rel="icon" href="/favicon.ico" type="image/x-icon">

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TAKS / KAKS Emsal Hesaplama | WORKSİTEM</title>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root { --bg: #0b0f17; --card-bg: #151c28; --border: #21262d; --yellow: #ffb700; --text: #f0f6fc; --muted: #8b949e; --green: #10b981; --cyan: #38bdf8; }
        body { font-family: 'Inter', sans-serif; background: var(--bg); color: var(--text); margin: 0; padding: 0; overflow-x: hidden; }
        .container { max-width: 1200px; margin: 40px auto; padding: 0 4%; display: grid; grid-template-columns: 1fr 1fr; gap: 40px; }
        @media(max-width: 992px) { .container { grid-template-columns: 1fr; } }
        .form-card { background: var(--card-bg); border: 1px solid var(--border); border-radius: 12px; padding: 30px; margin-bottom: 20px; }
        h1 { font-family: 'Bebas Neue'; font-size: 38px; color: #fff; margin-bottom: 10px; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; font-size: 13px; font-weight: 700; color: var(--muted); margin-bottom: 8px; text-transform: uppercase; }
        .form-control { width: 100%; padding: 12px; border-radius: 6px; background: #0d1117; border: 1px solid var(--border); color: white; font-family: inherit; }
        .res-item { display: flex; justify-content: space-between; padding: 12px 0; border-bottom: 1px dashed var(--border); font-size: 14px; }
        .res-item.total { border-bottom: none; font-size: 20px; font-weight: 800; color: var(--yellow); margin-top: 15px; }
        .ai-box { background: rgba(56, 189, 248, 0.05); border: 1px solid var(--cyan); border-radius: 8px; padding: 20px; margin-top: 20px; }
    </style>
</head>
<body>
    <div style="padding:15px 4%; border-bottom:1px solid var(--border); background:#151c28;"><a href="/araclar" style="color:#fff; text-decoration:none; font-family:'Bebas Neue'; font-size:24px;">◀ WORKSİTEM ARAÇLAR</a></div>
    <div class="container">
        <div>
            <h1>🏢 TAKS / KAKS (Emsal) Hesabı</h1>
            <p style="color:var(--muted); font-size:14px; margin-bottom:30px;">Arsanızın imar durum belgesindeki değerleri girerek kaç m² ve kaç kat bina yapabileceğinizi hesaplayın.</p>
            <div class="form-card">
                <div class="form-group"><label>Arsa Alanı (Net m²)</label><input type="number" id="area" class="form-control" value="1000" oninput="calc()"></div>
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:15px;">
                    <div class="form-group"><label>TAKS (Örn: 0.30)</label><input type="number" id="taks" class="form-control" value="0.30" step="0.05" oninput="calc()"></div>
                    <div class="form-group"><label>KAKS / Emsal (Örn: 1.50)</label><input type="number" id="kaks" class="form-control" value="1.50" step="0.10" oninput="calc()"></div>
                </div>
            </div>
        </div>
        <div>
            <div class="form-card" style="border-color:var(--yellow); position:sticky; top:20px;">
                <div style="font-family:'Bebas Neue'; font-size:24px; color:var(--yellow); margin-bottom:15px;">📊 İMAR VE YAPI ANALİZİ</div>
                <div class="res-item"><span>Maksimum Taban Alanı (TAKS)</span> <strong id="r_base">0 m²</strong></div>
                <div class="res-item"><span>Emsale Dahil İnşaat Alanı (KAKS)</span> <strong id="r_total">0 m²</strong></div>
                <div class="res-item total"><span>MAKSİMUM KAT SAYISI</span> <span id="r_floors">0 Kat</span></div>
                
                <div class="ai-box">
                    <div style="color:var(--cyan); font-family:'Bebas Neue'; font-size:22px; margin-bottom:10px;">🤖 Mimari Uyarılar</div>
                    <ul id="ai_notes" style="font-size:13px; margin:0; padding-left:15px; line-height:1.6;"></ul>
                </div>
            </div>
        </div>
    </div>

        <!-- STANDART KAİNTA LEAD HUNİSİ (CTA) -->
        <div class="no-print" style="margin-top: 40px; padding: 30px; background: #f0fdf4; border: 2px solid #bbf7d0; border-radius: 16px; text-align: center;">
            <h3 style="color: #166534; font-size: 20px; font-weight: 800; margin-bottom: 10px;">Hesaplamayı Yaptınız, Şimdi İşi Uzmanına Bırakın</h3>
            <p style="color: #15803d; font-size: 14px; margin-bottom: 20px;">Kainta İnşaat mühendislerinden projenize özel resmi keşif ve bütçe teklifi alın.</p>
            <a href="/maliyet-hesapla" style="display: inline-block; background: #166534; color: #fff; padding: 16px 32px; font-size: 16px; font-weight: 800; border-radius: 8px; text-decoration: none; box-shadow: 0 4px 10px rgba(22,101,52,0.3); transition: all 0.2s;">Ön maliyet hesabına geç →</a>
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
            let area = parseFloat(document.getElementById('area').value) || 0;
            let taks = parseFloat(document.getElementById('taks').value) || 0;
            let kaks = parseFloat(document.getElementById('kaks').value) || 0;

            let baseArea = area * taks;
            let totalArea = area * kaks;
            let floors = (taks > 0) ? (kaks / taks) : 0;

            document.getElementById('r_base').innerText = baseArea.toFixed(2) + ' m²';
            document.getElementById('r_total').innerText = totalArea.toFixed(2) + ' m²';
            document.getElementById('r_floors').innerText = Math.floor(floors) + ' Kat';

            let notes = [];
            notes.push("Emsal (KAKS) hesabına asansör boşlukları, yangın merdivenleri, şaftlar ve açık çıkmalar (%30 kuralı dahilinde) girmez. Brüt inşaat alanınız %20-30 daha büyük çıkacaktır.");
            if(taks > 0.40) notes.push("TAKS değeriniz oldukça yüksek. Arsanızda yeşil alan ve otopark için kısıtlı yer kalabilir, kapalı otopark çözümü gerekebilir.");
            if(floors % 1 !== 0) notes.push("Kat sayısı küsuratlı çıktı (" + floors.toFixed(1) + "). Çatı piyesi veya asma kat ile tam emsali kullanabilirsiniz.");
            
            let notesBox = document.getElementById('ai_notes');
            notesBox.innerHTML = '';
            notes.forEach(n => { let li = document.createElement('li'); li.innerText = n; li.style.marginBottom = "5px"; notesBox.appendChild(li); });
        }
        document.addEventListener('DOMContentLoaded', calc);
    </script>
</body>
</html>
