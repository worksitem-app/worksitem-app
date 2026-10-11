<!DOCTYPE html>
<html lang="tr">
<head>
    <link rel="icon" href="/favicon.ico" type="image/x-icon">

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bahçe ve İstinat Duvarı Maliyeti 2026 | WORKSİTEM</title>
    <meta name="description" content="2026 ÇŞB rayiçleriyle taş duvar veya betonarme bahçe ve istinat duvarı imalatının yaklaşık bedelini ve proje bütçesini hesaplayın.">
    <link rel="canonical" href="https://worksitem.com/araclar/bahce-duvari-maliyeti">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        :root { --bg: #f5f6f8; --card-bg: #ffffff; --border: #e5e7eb; --yellow: #9a6e00; --text: #101318; --muted: #5b6472; --green: #10b981; --cyan: #8a6200; }
        body { font-family: 'Inter', sans-serif; background: var(--bg); color: var(--text); margin: 0; padding: 0; overflow-x: hidden; }
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
</head>
<body data-tool-slug="garden-wall-calculator">
    <x-site.contech-header />
    <div class="container">
        <div>
            <h1>Bahçe Duvarı Maliyeti</h1>
            <p style="color:var(--muted); font-size:14px; margin-bottom:30px;">2026 ÇŞB rayiçleriyle taş duvar veya betonarme duvar imalat bedelini hesaplayın.</p>
            <div class="form-card">
                <div class="grid-2">
                    <div class="form-group"><label>Duvar Uzunluğu (m)</label><input type="number" id="l" class="form-control" value="50" oninput="calc()"></div>
                    <div class="form-group"><label>Ortalama Yükseklik (m)</label><input type="number" id="h" class="form-control" value="1.5" step="0.1" oninput="calc()"></div>
                </div>
                <div class="form-group">
                    <label>Duvar Tipi ve Kalınlığı</label>
                    <select id="type" class="form-control" onchange="calc()">
                        <option value="stone" selected>Taş Duvar (Ort. 50 cm kalınlık - Harçlı)</option>
                        <option value="concrete">Betonarme Duvar (Ort. 25 cm kalınlık + Demir/Kalıp)</option>
                    </select>
                </div>
            </div>
        </div>
        <div>
            <div class="form-card" style="border-color:var(--yellow); position:sticky; top:20px;">
                <div style="font-family:'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; font-size:24px; color:var(--yellow); margin-bottom:15px;">📊 ÇŞB BÜTÇE ANALİZİ</div>
                <div class="res-item"><span>Toplam Hacim (m³)</span> <strong id="r_vol">0 m³</strong></div>
                <div class="res-item"><span>Birim Maliyet Şekli</span> <strong id="r_method">0 TL</strong></div>
                <div class="res-item total"><span>TAHMİNİ MALİYET</span> <span id="r_total">0 TL</span></div>
                
                <div class="ai-box">
                    <div style="color:var(--cyan); font-family:'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; font-size:22px; margin-bottom:10px;">🤖 Statik ve Estetik Notu</div>
                    <ul id="ai_notes" style="font-size:13px; margin:0; padding-left:15px; line-height:1.6;"></ul>
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
            let l = parseFloat(document.getElementById('l').value) || 0;
            let h = parseFloat(document.getElementById('h').value) || 0;
            let type = document.getElementById('type').value;

            let vol = 0;
            let cost = 0;
            let methodStr = "";
            let notes = [];

            if (type === 'stone') {
                // Taş duvar: Kalınlık genelde 0.5m. ÇŞB 15.210.1005 (2.800,51 TL/m3) + Derz (304 TL/m2)
                vol = l * h * 0.5;
                let stoneCost = vol * 2800.51;
                let derzCost = l * h * 304.00;
                cost = stoneCost + derzCost;
                methodStr = "Taş + Harç: 2.800 TL/m³ + Derz İşçiliği";
                notes.push("Ocak taşı ve 200 dozlu çimento kullanılmıştır. Estetik bir görünüme sahiptir ancak yanal toprak yükü (istinat) varsa arka tarafına drenaj çakılı ve barbakan (su tahliye borusu) konulması şarttır.");
            } else {
                // Betonarme duvar: Kalınlık 0.25m. ÇŞB Beton (4046.71), Demir (~90 kg/m3 * 51.32), Kalıp (2 yüzey: 2 * l * h * 997.21)
                vol = l * h * 0.25;
                let concCost = vol * 4046.71;
                let rebarCost = vol * 90 * 51.32;
                let formCost = (2 * l * h) * 997.21;
                cost = concCost + rebarCost + formCost;
                methodStr = "Beton: 4046 TL/m³, Demir: 51 TL/kg, Kalıp: 997 TL/m²";
                notes.push("Betonarme duvarlar eğilme ve toprak itkisine karşı en güvenli sistemdir. Hesaba çift yüzlü plywood kalıp ve C25/30 beton fiyatları dahil edilmiştir.");
            }

            document.getElementById('r_vol').innerText = vol.toFixed(2) + ' m³';
            document.getElementById('r_method').innerText = methodStr;
            document.getElementById('r_total').innerText = new Intl.NumberFormat('tr-TR').format(Math.round(cost)) + ' TL';

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
