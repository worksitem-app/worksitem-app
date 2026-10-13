<!DOCTYPE html>
<html lang="tr">
<head>
    <link rel="icon" href="/favicon.ico" type="image/x-icon">

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PVC Doğrama ve Cam Hesaplama | WORKSİTEM</title>
    <meta name="description" content="PVC doğrama ve cam ihtiyaçlarınızı hesaplayın, pencere ve kapı metrajınızı planlayın ve uygun projelerde Kainta&#039;dan net teklif alın.">
    <link rel="canonical" href="https://worksitem.com/araclar/pvc-dograma-hesaplayici">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        :root { --bg: #f5f6f8; --card-bg: #ffffff; --border: #e5e7eb; --yellow: #9a6e00; --text: #101318; --muted: #5b6472; --green: #10b981; --cyan: #8a6200; }
        body { font-family: 'Inter', sans-serif; background: var(--bg); color: var(--text); margin: 0; padding: 0; }
        .container { max-width: 1200px; margin: 40px auto; padding: 0 4%; display: grid; grid-template-columns: 1fr 1fr; gap: 40px; }
        @media(max-width: 992px) { .container { grid-template-columns: 1fr; } }
        .form-card { background: var(--card-bg); border: 1px solid var(--border); border-radius: 12px; padding: 30px; margin-bottom: 20px; }
        h1 { font-family:'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; font-size: 38px; font-weight: 800; letter-spacing: -0.02em; color: var(--text); margin-bottom: 10px; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; font-size: 13px; font-weight: 700; color: var(--muted); margin-bottom: 8px; text-transform: uppercase; }
        .form-control { width: 100%; padding: 12px; border-radius: 6px; background: #ffffff; border: 1px solid var(--border); color: var(--text); font-family: inherit; }
        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
        .res-item { display: flex; justify-content: space-between; padding: 12px 0; border-bottom: 1px dashed var(--border); font-size: 14px; }
        .res-item.total { border-bottom: none; font-size: 20px; font-weight: 800; color: var(--yellow); margin-top: 15px; }
        .ai-box { background: rgba(255,184,0,0.05); border: 1px solid var(--cyan); border-radius: 8px; padding: 20px; margin-top: 20px; }
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
<body class="wc" data-tool-slug="pvc-calculator">
    <x-site.contech-header />
    <div class="container">
        <div>
            <x-calc.hero title="PVC doğrama maliyeti hesaplama" crumb="PVC doğrama"></x-calc.hero>
            <div class="form-card">
                <div class="grid-2">
                    <div class="form-group"><label>Genişlik (cm)</label><input type="number" id="w" class="form-control" value="150" oninput="calc()"></div>
                    <div class="form-group"><label>Yükseklik (cm)</label><input type="number" id="h" class="form-control" value="150" oninput="calc()"></div>
                </div>
                <div class="form-group">
                    <label>PVC Rengi (ÇŞB 15.455.1002 - 1003)</label>
                    <select id="color" class="form-control" onchange="calc()">
                        <option value="361.10" selected>Beyaz Profil (361,10 TL/kg)</option>
                        <option value="405.00">Renkli / Ahşap Desenli (405,00 TL/kg)</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Cam Tipi (ÇŞB 15.470.1010 vb.)</label>
                    <select id="glass" class="form-control" onchange="calc()">
                        <option value="1971.18" selected>Standart Çift Cam (4+4 12mm) - 1.971 TL/m²</option>
                        <option value="2114.13">Isı Kontrol Kaplamalı (Sinerji/Konfor) - 2.114 TL/m²</option>
                    </select>
                </div>
            </div>
        </div>
        <div>
            <div class="form-card" style="border-color:var(--yellow); position:sticky; top:20px;" data-wc-result>
                <div style="font-family:'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; font-size:24px; color:var(--yellow); margin-bottom:15px;">📊 MALİYET ANALİZİ</div>
                <div class="res-item"><span>Profil Tahmini Ağırlığı</span> <strong id="r_kg">0 Kg</strong></div>
                <div class="res-item"><span>PVC Profil Maliyeti</span> <strong id="r_prof">0 TL</strong></div>
                <div class="res-item"><span>Isıcam Alanı</span> <strong id="r_m2">0 m²</strong></div>
                <div class="res-item"><span>Cam Maliyeti</span> <strong id="r_glass">0 TL</strong></div>
                <div class="res-item total"><span>TOPLAM PENCERE MALİYETİ</span> <span id="r_total" data-wc-price>0 TL</span></div>
                
                <div class="ai-box">
                    <div style="color:var(--cyan); font-family:'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; font-size:22px; margin-bottom:10px;">🤖 AI Yorumu</div>
                    <ul id="ai_notes" style="font-size:13px; margin:0; padding-left:15px;"></ul>
                </div>
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
            let w = parseFloat(document.getElementById('w').value) / 100 || 0;
            let h = parseFloat(document.getElementById('h').value) / 100 || 0;
            let p_prof = parseFloat(document.getElementById('color').value);
            let p_glass = parseFloat(document.getElementById('glass').value);

            let m2 = w * h;
            // Ortalama bir pvc pencerede m2 başına ~12 kg profil gider (kasalar ve orta kayıt dahil)
            let kg = m2 * 12;

            let c_prof = kg * p_prof;
            let c_glass = m2 * p_glass;
            let total = c_prof + c_glass;

            document.getElementById('r_kg').innerText = Math.round(kg) + ' Kg';
            document.getElementById('r_prof').innerText = new Intl.NumberFormat('tr-TR').format(Math.round(c_prof)) + ' TL';
            document.getElementById('r_m2').innerText = m2.toFixed(2) + ' m²';
            document.getElementById('r_glass').innerText = new Intl.NumberFormat('tr-TR').format(Math.round(c_glass)) + ' TL';
            document.getElementById('r_total').innerText = new Intl.NumberFormat('tr-TR').format(Math.round(total)) + ' TL';

            let notes = [];
            if(w > 2.0) notes.push("Genişlik 2 metreden fazla. Rüzgar yüküne karşı destek sacı (orta kayıt) kalınlığı artırılmalıdır.");
            if(p_glass > 2000) notes.push("Isı kontrol kaplamalı cam seçimi, kışın ısı kaybını %50'ye kadar azaltarak enerji tasarrufu sağlayacaktır.");
            
            let notesBox = document.getElementById('ai_notes');
            notesBox.innerHTML = '';
            notes.forEach(n => { let li = document.createElement('li'); li.innerText = n; li.style.marginBottom = "5px"; notesBox.appendChild(li); });
        }
        document.addEventListener('DOMContentLoaded', calc);
    </script>
<x-calc.kit />
    <x-site.contech-footer />
    <script src="/js/tool-events.js" defer></script>
</body>
</html>
