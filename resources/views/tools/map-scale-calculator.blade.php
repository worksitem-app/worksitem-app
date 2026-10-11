<!DOCTYPE html>
<html lang="tr">
<head>
    <link rel="icon" href="/favicon.ico" type="image/x-icon">

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pafta & Harita Ölçek Hesabı | WORKSİTEM</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        :root { --bg: #f5f6f8; --card-bg: #ffffff; --border: #e5e7eb; --yellow: #ffb800; --text: #101318; --muted: #5b6472; --cyan: #8a6200; }
        body { font-family: 'Inter', sans-serif; background: var(--bg); color: var(--text); margin: 0; padding: 0; overflow-x: hidden; }
        .container { max-width: 1200px; margin: 40px auto; padding: 0 4%; display: grid; grid-template-columns: 1fr 1fr; gap: 40px; }
        @media(max-width: 992px) { .container { grid-template-columns: 1fr; } }
        .form-card { background: var(--card-bg); border: 1px solid var(--border); border-radius: 12px; padding: 30px; margin-bottom: 20px; }
        h1 { font-family:'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; font-size: 38px; color: #fff; margin-bottom: 10px; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; font-size: 13px; font-weight: 700; color: var(--muted); margin-bottom: 8px; text-transform: uppercase; }
        .form-control { width: 100%; padding: 12px; border-radius: 6px; background: #0d1117; border: 1px solid var(--border); color: white; font-family: inherit; }
        .res-item { display: flex; justify-content: space-between; padding: 12px 0; border-bottom: 1px dashed var(--border); font-size: 14px; }
        .res-item.total { border-bottom: none; font-size: 20px; font-weight: 800; color: var(--cyan); margin-top: 15px; }
        .ai-box { background: rgba(255,184,0,0.05); border: 1px solid var(--cyan); border-radius: 8px; padding: 20px; margin-top: 20px; }
    </style>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body data-tool-slug="map-scale-calculator">
    <div style="padding:15px 4%; border-bottom:1px solid var(--border); background:#151c28;"><a href="/araclar" style="color:#fff; text-decoration:none; font-family:'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; font-size:24px;">◀ WORKSİTEM ARAÇLAR</a></div>
    <div class="container">
        <div>
            <h1>Pafta Ölçü & Ölçek Hesabı</h1>
            <p style="color:var(--muted); font-size:14px; margin-bottom:30px;">Proje veya harita üzerinde ölçtüğünüz uzunluğun, ilgili ölçekte gerçekte kaç metreye denk geldiğini bulun.</p>
            <div class="form-card">
                <div class="form-group">
                    <label>Harita / Proje Ölçeği</label>
                    <select id="scale" class="form-control" onchange="calc()">
                        <option value="50">1/50 (Detay Proje)</option>
                        <option value="100">1/100 (Mimari Proje)</option>
                        <option value="200">1/200 (Vaziyet Planı)</option>
                        <option value="500">1/500 (Kadastral Pafta)</option>
                        <option value="1000" selected>1/1000 (Uygulama İmar Planı)</option>
                        <option value="5000">1/5000 (Nazım İmar Planı)</option>
                    </select>
                </div>
                <div class="form-group"><label>Harita Üzerindeki Ölçüm (cm)</label><input type="number" id="len" class="form-control" value="5" step="0.1" oninput="calc()"></div>
            </div>
        </div>
        <div>
            <div class="form-card" style="border-color:var(--cyan); position:sticky; top:20px;">
                <div style="font-family:'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; font-size:24px; color:var(--cyan); margin-bottom:15px;">📊 GERÇEK ÖLÇÜLER</div>
                <div class="res-item"><span>Proje Üzerindeki Ölçü</span> <strong id="r_cm">0 cm</strong></div>
                <div class="res-item total"><span>GERÇEK UZUNLUK (Metre)</span> <span id="r_m">0 m</span></div>
                
                <div class="ai-box">
                    <div style="color:var(--cyan); font-family:'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; font-size:22px; margin-bottom:10px;">🤖 Haritacı Notu</div>
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
            let scale = parseFloat(document.getElementById('scale').value) || 1000;
            let lenCm = parseFloat(document.getElementById('len').value) || 0;

            // 1 cm haritada = scale cm gerçekte. Bunu metreye bölüyoruz (/100).
            let realMeters = (lenCm * scale) / 100;

            document.getElementById('r_cm').innerText = lenCm + ' cm';
            document.getElementById('r_m').innerText = realMeters.toFixed(2) + ' Metre';

            let notes = [];
            notes.push("1/" + scale + " ölçekli bir planda ölçtüğünüz her 1 milimetre, gerçek arazide " + (scale/1000).toFixed(2) + " metreye denk gelir.");
            if(scale >= 1000) notes.push("İmar paftalarında kurşun kalem ucunun (0.5 mm) kalınlığı bile arazide yaklaşık yarım metrelik kaymalara neden olabilir. Hassas ölçümler için CAD verisi kullanın.");
            
            let notesBox = document.getElementById('ai_notes');
            notesBox.innerHTML = '';
            notes.forEach(n => { let li = document.createElement('li'); li.innerText = n; li.style.marginBottom = "5px"; notesBox.appendChild(li); });
        }
        document.addEventListener('DOMContentLoaded', calc);
    </script>
    <script src="/js/tool-events.js" defer></script>
</body>
</html>
