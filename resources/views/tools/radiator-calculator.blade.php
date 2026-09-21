<!DOCTYPE html>
<html lang="tr">
<head>
    <link rel="icon" href="/favicon.ico" type="image/x-icon">

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Radyatör Boyu Hesaplama | WORKSİTEM</title>
    <meta name="description" content="Panel radyatör için gerekli petek boyunu hesaplayın ve mekanınızın ısıtma ihtiyacına uygun yaklaşık radyatör ölçüsünü belirleyin.">
    <link rel="canonical" href="https://worksitem.com/araclar/radyator-petek-hesaplayici">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root { --bg: #0b0f17; --card-bg: #151c28; --border: #21262d; --yellow: #ffb700; --text: #f0f6fc; --muted: #8b949e; --cyan: #38bdf8; }
        body { font-family: 'Inter', sans-serif; background: var(--bg); color: var(--text); margin: 0; padding: 0; overflow-x: hidden;}
        .container { max-width: 1200px; margin: 40px auto; padding: 0 4%; display: grid; grid-template-columns: 1fr 1fr; gap: 40px; }
        @media(max-width: 992px) { .container { grid-template-columns: 1fr; } }
        .form-card { background: var(--card-bg); border: 1px solid var(--border); border-radius: 12px; padding: 30px; margin-bottom: 20px; }
        h1 { font-family: 'Bebas Neue'; font-size: 38px; color: #fff; margin-bottom: 10px; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; font-size: 13px; font-weight: 700; color: var(--muted); margin-bottom: 8px; text-transform: uppercase; }
        .form-control { width: 100%; padding: 12px; border-radius: 6px; background: #0d1117; border: 1px solid var(--border); color: white; font-family: inherit; }
        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
        .res-item { display: flex; justify-content: space-between; padding: 12px 0; border-bottom: 1px dashed var(--border); font-size: 14px; }
        .res-item.total { border-bottom: none; font-size: 24px; font-weight: 800; color: var(--cyan); margin-top: 15px; }
        .ai-box { background: rgba(56, 189, 248, 0.05); border: 1px solid var(--cyan); border-radius: 8px; padding: 20px; margin-top: 20px; }
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
</head>
<body data-tool-slug="radiator-calculator">
    <x-site.contech-header />
    <div class="container">
        <div>
            <h1>♨️ Radyatör Petek Boyu Hesabı</h1>
            <div class="form-card">
                <div class="grid-2">
                    <div class="form-group"><label>Oda Alanı (m²)</label><input type="number" id="area" class="form-control" value="20" oninput="calc()"></div>
                    <div class="form-group"><label>Tavan Yüksekliği (m)</label><input type="number" id="h" class="form-control" value="2.8" step="0.1" oninput="calc()"></div>
                </div>
                <div class="form-group">
                    <label>Binanın Isı Yalıtım Durumu</label>
                    <select id="ins" class="form-control" onchange="calc()">
                        <option value="30">Çok İyi Yalıtımlı (Yeni Bina)</option>
                        <option value="40" selected>Standart Yalıtımlı</option>
                        <option value="50">Yalıtımsız / Eski Bina</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Kullanılacak Petek Tipi</label>
                    <select id="type" class="form-control" onchange="calc()">
                        <option value="1700" selected>Tip 22 (PKKP) 60cm Yükseklik (~1700 kcal/m)</option>
                        <option value="1300">Tip 22 (PKKP) 50cm Yükseklik (~1300 kcal/m)</option>
                    </select>
                </div>
            </div>
        </div>
        <div>
            <div class="form-card" style="border-color:var(--cyan); position:sticky; top:20px;">
                <div style="font-family:'Bebas Neue'; font-size:24px; color:var(--cyan); margin-bottom:15px;">📊 TERMODİNAMİK ANALİZ</div>
                <div class="res-item"><span>Oda Hacmi</span> <strong id="r_vol">0 m³</strong></div>
                <div class="res-item"><span>Gereken Isı Enerjisi (Yaklaşık)</span> <strong id="r_kcal">0 Kcal/h</strong></div>
                <div class="res-item total"><span>ÖNERİLEN PETEK BOYU</span> <span id="r_len">0 Metre</span></div>
                
                <div class="ai-box">
                    <div style="color:var(--cyan); font-family:'Bebas Neue'; font-size:22px; margin-bottom:10px;">🤖 Mühendis Yorumu</div>
                    <ul id="ai_notes" style="font-size:13px; margin:0; padding-left:15px;"></ul>
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
            let h = parseFloat(document.getElementById('h').value) || 0;
            let insFactor = parseFloat(document.getElementById('ins').value); // kcal/m3 ihtiyacı
            let pkkpPower = parseFloat(document.getElementById('type').value); // kcal/metre

            let vol = area * h;
            // Kuzey cephe veya çok pencereli durumlar için %10 emniyet payı
            let reqKcal = vol * insFactor * 1.1; 
            
            let reqLen = reqKcal / pkkpPower;

            document.getElementById('r_vol').innerText = vol.toFixed(1) + ' m³';
            document.getElementById('r_kcal').innerText = Math.round(reqKcal) + ' Kcal/h';
            
            // Metreyi mantıklı bir şekle yuvarla (örn: 1.2m, 1.5m, 2m)
            let roundedLen = Math.ceil(reqLen * 10) / 10;
            document.getElementById('r_len').innerText = roundedLen.toFixed(1) + ' Metre';

            let notes = [];
            notes.push("Bu değerler 90/70°C kazan çıkış sıcaklığına göre standart panel radyatörler için hesaplanmıştır.");
            if(roundedLen > 2.0) notes.push("2 metreden uzun petekler ısınma verimini düşürebilir. " + roundedLen.toFixed(1) + " metrelik ihtiyacı iki ayrı petek (örn: 1m + 1m) olarak bölmeniz önerilir.");
            if(insFactor === 50) notes.push("Binanız yalıtımsız olduğu için ısı ihtiyacı maksimum seviyede. Mantolama yaptırmak petek boyu ihtiyacını %40 azaltacaktır.");
            
            let notesBox = document.getElementById('ai_notes');
            notesBox.innerHTML = '';
            notes.forEach(n => { let li = document.createElement('li'); li.innerText = n; li.style.marginBottom = "5px"; notesBox.appendChild(li); });
        }
        document.addEventListener('DOMContentLoaded', calc);
    </script>
    <x-site.contech-footer />
    <script src="/js/tool-events.js" defer></script>
</body>
</html>
