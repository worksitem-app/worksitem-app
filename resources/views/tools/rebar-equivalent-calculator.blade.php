<!DOCTYPE html>
<html lang="tr">
<head>
    <link rel="icon" href="/favicon.ico" type="image/x-icon">

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donatı Çap/Aralık Tahvil Hesabı | WORKSİTEM</title>
    <meta name="description" content="Projede istenen demir çapı bulunmadığında mevcut donatının eşdeğer aralığını hesaplayın ve şantiyedeki donatı tahvilini planlayın.">
    <link rel="canonical" href="https://worksitem.com/araclar/donati-tahvil-hesaplayici">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        :root { --bg: #f5f6f8; --card-bg: #ffffff; --border: #e5e7eb; --yellow: #9a6e00; --text: #101318; --muted: #5b6472; --cyan: #8a6200; --red: #ef4444; }
        *,*::before,*::after{box-sizing:border-box}
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
        .res-item.total { border-bottom: none; font-size: 24px; font-weight: 800; color: var(--cyan); margin-top: 15px; }
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
<body class="wc" data-tool-slug="rebar-equivalent-calculator">
    <x-site.contech-header />
    <div class="container">
        <div>
            <x-calc.hero title="Donatı tahvili (eşdeğer demir) hesaplama" crumb="Donatı tahvili">Şantiyede projede istenen demir çapı bulunmadığında, mevcut demiri hangi aralıkla (cm) döşemeniz gerektiğini bulun.</x-calc.hero>
            <div class="form-card">
                <div style="border-bottom:1px solid var(--border); padding-bottom:10px; margin-bottom:15px; font-weight:700; color:var(--yellow);">PROJEDE İSTENEN (GEREKEN)</div>
                <div class="grid-2">
                    <div class="form-group"><label>Projedeki Demir Çapı (Ø)</label><input type="number" id="d1" class="form-control" value="12" min="6" max="40" oninput="calc()"></div>
                    <div class="form-group"><label>Projedeki Döşeme Aralığı (cm)</label><input type="number" id="s1" class="form-control" value="15" min="1" oninput="calc()"></div>
                </div>
                
                <div style="border-bottom:1px solid var(--border); padding-bottom:10px; margin-bottom:15px; font-weight:700; color:var(--cyan); margin-top:10px;">ŞANTİYEDE BULUNAN (MEVCUT)</div>
                <div class="form-group"><label>Eleman Tipi</label><select id="elem" class="form-control" onchange="calc()"><option value="20" selected>Döşeme ana donatısı, kısa doğrultu (≤ 20 cm)</option><option value="25">Döşeme dağıtma / uzun doğrultu donatısı (≤ 25 cm)</option><option value="25">Perde gövde donatısı (≤ 25 cm)</option><option value="30">Temel / diğer (≤ 30 cm)</option></select></div>
                <div class="form-group"><label>Elinizdeki Demir Çapı (Ø)</label><input type="number" id="d2" class="form-control" value="14" min="6" max="40" oninput="calc()"></div>
            </div>
        </div>
        <div>
            <div class="form-card" style="border-color:var(--cyan); position:sticky; top:20px;" data-wc-result>
                <div style="font-family:'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; font-size:24px; color:var(--cyan); margin-bottom:15px;">📊 STATİK ÇEVRİM SONUCU</div>
                <div class="res-item"><span>Gereken Donatı Alanı</span> <strong id="r_area_req">0 cm²/m</strong></div>
                <div class="res-item"><span>Mevcut Demirin Tekil Alanı</span> <strong id="r_area_new">0 cm²</strong></div>
                <div class="res-item total"><span>YENİ DÖŞEME ARALIĞI</span> <span id="r_s2" data-wc-price>0 cm</span></div>
                
                <div class="ai-box">
                    <div style="color:var(--cyan); font-family:'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; font-size:22px; margin-bottom:10px;">💡 Statik Uyarıları</div>
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
            let d1 = parseFloat(document.getElementById('d1').value); // mm
            let s1 = parseFloat(document.getElementById('s1').value); // cm
            let d2 = parseFloat(document.getElementById('d2').value); // mm
            const sMax = parseFloat(document.getElementById('elem').value) || 20; // cm
            let notesBox = document.getElementById('ai_notes');
            const addNotes = (arr) => { notesBox.innerHTML = ''; arr.forEach(n => { let li = document.createElement('li'); li.innerText = n; li.style.marginBottom = "5px"; notesBox.appendChild(li); }); };

            // v119: negatif/0/boş girdide hesap yapılmaz (eskiden Infinity / Ø0 çıkıyordu)
            if (!(d1 > 0) || !(s1 > 0) || !(d2 > 0)) {
                ['r_area_req', 'r_area_new', 'r_s2'].forEach(id => document.getElementById(id).innerText = '—');
                addNotes(['Çap ve aralık için pozitif bir değer girin.']);
                return;
            }
            const f2 = (x) => x.toLocaleString('tr-TR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

            // Çapı cm'ye çevirip alanını buluyoruz (A = pi * d^2 / 4)
            let area1 = Math.PI * Math.pow((d1/10), 2) / 4; 
            let area2 = Math.PI * Math.pow((d2/10), 2) / 4;

            // 1 metredeki (100 cm) toplam donatı alanı gereksinimi
            let reqAreaPerMeter = (100 / s1) * area1;

            // Yeni aralık = (Yeni Tekil Alan * 100) / Gereken Toplam Alan
            let s2 = (area2 * 100) / reqAreaPerMeter;

            document.getElementById('r_area_req').innerText = f2(reqAreaPerMeter) + ' cm²/m';
            document.getElementById('r_area_new').innerText = f2(area2) + ' cm²';
            
            // Güvenli tarafta kalmak için aşağı yuvarlama yapıyoruz (Örn: 20.4 cm çıktıysa şantiyede 20 cm döşenir)
            let safeSpacing = Math.floor(s2);
            document.getElementById('r_s2').innerText = 'Ø' + d2 + ' / ' + safeSpacing + ' cm';

            let notes = [];
            notes.push("Matematiksel karşılık " + f2(s2) + " cm olsa da, güvenli tarafta kalmak için şantiyede aralık aşağıya (" + safeSpacing + " cm) yuvarlanmıştır.");
            
            // TS 500 11.4 (plak): kısa doğrultu ana donatı aralığı ≤ 1,5h ve ≤ 20 cm, uzun doğrultu ≤ 25 cm; TBDY 2018 7.6: perde gövdesi ≤ 25 cm
            if (safeSpacing > sMax) {
                notes.push("⚠️ DİKKAT: Bu eleman için azami donatı aralığı " + sMax + " cm (TS 500 / TBDY 2018; döşemede ayrıca 1,5 × döşeme kalınlığını aşamaz). " + safeSpacing + " cm uygulanamaz: daha ince çap kullanın ya da aralığı " + sMax + " cm alıp donatı alanını kontrol ettirin.");
            }
            if (safeSpacing < 5) {
                notes.push("⚠️ Aralık 5 cm'nin altında; beton yerleşimi zorlaşır. Daha kalın çap kullanmayı değerlendirin.");
            }
            if(d2 < d1) {
                notes.push("Daha ince çaplı demire geçiş yaptınız. Bu durum aralığı sıklaştırarak beton yerleşimini (agregaların geçişini) zorlaştırabilir. Şantiyede vibratör kullanımına ekstra dikkat ediniz.");
            }
            notes.push("Tahvil işlemi statik müellif (proje mühendisi) onayı olmadan kesinlikle uygulanmamalıdır.");
            addNotes(notes);
        }
        document.addEventListener('DOMContentLoaded', calc);
    </script>
<x-calc.kit />
    <x-site.contech-footer />
    <script src="/js/tool-events.js" defer></script>
</body>
</html>
