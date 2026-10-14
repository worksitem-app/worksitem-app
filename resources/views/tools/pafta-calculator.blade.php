<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pafta Ölçü ve Harita Ölçek Hesaplayıcı | WORKSİTEM</title>
    <link rel="canonical" href="https://worksitem.com/araclar/pafta-olcu-hesaplayici">
    <meta name="description" content="Proje, imar planı veya pafta üzerinde ölçtüğünüz uzunluğun gerçek arazide kaç metreye denk geldiğini profesyonelce hesaplayın.">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #f5f6f8; --surface: #ffffff; --text: #101318; --muted: #6b7280;
            --line: #e4e7eb; --accent: #ffb800; --radius: 16px;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Inter', sans-serif; background: var(--bg); color: var(--text); line-height: 1.5; padding-bottom: 60px; }
        a { text-decoration: none; color: inherit; }
        
        .header { background: #fff; border-bottom: 1px solid var(--line); padding: 15px 20px; position: sticky; top: 0; z-index: 10; display: flex; align-items: center; justify-content: space-between; }
        .back-btn { font-size: 14px; font-weight: 700; color: var(--muted); display: flex; align-items: center; gap: 5px; }
        .back-btn:hover { color: var(--text); }
        .brand img { height: 30px; }

        .container { max-width: 800px; margin: 40px auto; padding: 0 20px; }
        .tool-header { margin-bottom: 30px; text-align: center; }
        .tool-header h1 { font-size: 28px; font-weight: 900; letter-spacing: -1px; margin-bottom: 10px; }
        .tool-header p { color: var(--muted); font-size: 15px; }

        .card { background: var(--surface); border: 1px solid var(--line); border-radius: var(--radius); padding: 30px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); margin-bottom: 30px; }
        
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; font-size: 13px; font-weight: 800; margin-bottom: 8px; }
        .form-control { width: 100%; height: 50px; border: 1px solid var(--line); border-radius: 12px; padding: 0 15px; font-size: 15px; font-family: inherit; background: #fff; transition: .2s; outline: none; }
        .form-control:focus { border-color: var(--accent); box-shadow: 0 0 0 3px rgba(255,184,0,0.15); }
        
        .result-box { background: #111; color: #fff; border-radius: var(--radius); padding: 30px; text-align: center; margin-top: 10px; display: none; }
        .result-box.active { display: block; animation: fadeIn 0.4s ease; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        .result-label { font-size: 12px; color: #a1a1aa; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; }
        .result-price { font-size: 38px; font-weight: 900; color: var(--accent); margin: 5px 0 15px; letter-spacing: -1px; }
        
        .breakdown-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-top: 20px; text-align: left; }
        .break-item { background: rgba(255,255,255,0.05); padding: 15px; border-radius: 12px; border: 1px solid rgba(255,255,255,0.1); }
        .break-item span { display: block; font-size: 12px; color: #a1a1aa; margin-bottom: 3px; }
        .break-item strong { display: block; font-size: 18px; color: #fff; font-weight: 800; }
        .break-item.highlight { background: rgba(255,184,0,0.1); border-color: rgba(255,184,0,0.3); }
        .break-item.highlight strong { color: #8a6200; }

        .ai-insight { background: rgba(255, 184, 0, 0.1); border: 1px solid rgba(255, 184, 0, 0.3); padding: 15px; border-radius: 12px; margin-top: 15px; text-align: left; display: flex; gap: 12px; align-items: flex-start; }
        .ai-insight-icon { font-size: 24px; }
        .ai-insight-text strong { color: var(--accent); display: block; font-size: 14px; margin-bottom: 4px; }
        .ai-insight-text p { color: #e4e4e7; font-size: 12px; margin: 0; line-height: 1.5; }

        .cross-sell { background: #1c1f26; border-radius: 12px; padding: 20px; margin-top: 20px; text-align: left; border: 1px solid rgba(255,255,255,0.1); }
        .cross-sell-title { font-size: 14px; font-weight: 800; color: #fff; margin-bottom: 5px; }
        .cross-sell-text { font-size: 12px; color: #a1a1aa; margin-bottom: 15px; }
        .cross-sell-links { display: flex; gap: 10px; }
        .cross-sell-links a { flex: 1; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); padding: 10px; border-radius: 8px; font-size: 12px; font-weight: 700; color: #fff; text-align: center; transition: .2s; }
        .cross-sell-links a:hover { background: rgba(255,184,0,0.1); border-color: #8a6200; color: #8a6200; }

        .budget-confirm-box { margin-top: 25px; padding: 25px; border: 1px solid var(--line); border-radius: var(--radius); background: #fff; display: none; text-align: center; }
        .budget-confirm-title { font-size: 18px; font-weight: 900; margin-bottom: 8px; letter-spacing: -0.5px;}
        .budget-confirm-text { color: var(--muted); font-size: 13px; line-height: 1.55; margin-bottom: 20px; }
        .budget-confirm-actions { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
        .budget-btn { min-height: 52px; border-radius: 13px; padding: 10px 14px; font-size: 13px; font-weight: 800; border: 1.5px solid var(--line); cursor: pointer; transition: .2s; }
        .budget-no { background: #fff; color: #555; }
        .budget-no:hover { background: #f8f9fb; border-color: #d1d5db; }
        .budget-yes { background: #fff; color: #111; }
        .budget-yes:hover { border-color: #8a6200; background: #fff8e1; box-shadow: 0 0 0 3px rgba(255,184,0,.10); }

        .qualified-message { display: none; margin-top: 25px; padding: 20px; border-radius: 16px; background: #fff8e1; border: 1px solid #fde68a; text-align: center;}
        .qualified-message strong { display: block; font-size: 15px; margin-bottom: 5px; color: #101318; }
        .qualified-message span { display: block; color: #101318; font-size: 12px; line-height: 1.5; }

        .lead-box { background: #f8f9fb; border: 1px solid var(--line); border-radius: var(--radius); padding: 30px; margin-top: 15px; display: none; }
        .lead-box.active { display: block; animation: fadeIn 0.5s ease; }
        .lead-box h3 { font-size: 18px; font-weight: 900; margin-bottom: 8px; }
        .lead-box p { font-size: 13px; color: var(--muted); margin-bottom: 20px; line-height: 1.5; }
        
        .btn { width: 100%; height: 50px; border: none; border-radius: 12px; font-weight: 800; font-size: 15px; cursor: pointer; transition: .2s; }
        .btn-calc { background: var(--text); color: #fff; margin-top: 10px; }
        .btn-calc:hover { background: #000; }
        .btn-submit { background: var(--accent); color: #000; margin-top: 10px; }
        .btn-submit:hover { background: #e5a500; }

        @media(max-width: 600px) {
            .form-grid, .breakdown-grid, .budget-confirm-actions { grid-template-columns: 1fr; }
            .card, .result-box, .lead-box, .budget-confirm-box { padding: 20px; }
            .result-price { font-size: 32px; }
            .cross-sell-links { flex-direction: column; }
        }
    </style>
    <link rel="icon" href="/favicon.ico" type="image/x-icon">
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
<body class="wc" data-tool-slug="pafta-calculator">

<x-site.contech-header />

<main class="wc-wrap">
    <x-calc.hero title="Pafta ölçeği ve gerçek ölçü hesaplama" crumb="Pafta ölçek">
        Proje, harita veya imar paftası üzerinde cetvelle ölçtüğünüz mesafenin gerçek arazide kaç metreye denk geldiğini hassas şekilde hesaplayın.
    </x-calc.hero>

    <div class="wc-grid">
    <div class="card wc-panel">
        <div class="form-grid">
            <div class="form-group">
                <label>Harita / Proje Ölçeği</label>
                <select id="scale" class="form-control">
                    <option value="50">1/50 (Detay Proje)</option>
                    <option value="100">1/100 (Mimari Proje)</option>
                    <option value="200">1/200 (Vaziyet Planı)</option>
                    <option value="500">1/500 (Kadastral Pafta)</option>
                    <option value="1000" selected>1/1000 (Uygulama İmar Planı)</option>
                    <option value="5000">1/5000 (Nazım İmar Planı)</option>
                </select>
            </div>
            <div class="form-group">
                <label>Kağıt Üzerindeki Ölçüm (Santimetre)</label>
                <input type="number" id="measurement" class="form-control" placeholder="Örn: 5.5" value="5" min="0.1" step="0.1">
            </div>
        </div>

        <button type="button" class="btn btn-calc" id="calculateBtn">Gerçek Arazi Uzunluğunu Bul →</button>
    </div>

    <aside class="wc-aside">
    <div class="wc-empty" data-wc-empty>
        <strong>Sonuç burada görünecek</strong>
        Bilgileri girip hesaplayın; sonuç, döküm ve yaklaşık bütçe burada görünür.
    </div>
    <div class="result-box wc-result" data-wc-result id="resultBox">
        <div class="result-label">GERÇEK ARAZİ ÖLÇÜLERİ</div>
        <div class="result-price" id="realMeters" data-wc-price>0.00 Metre</div>
        
        <p style="font-size: 11px; color: #6b7280; margin-bottom:20px;">
            ⚠️ Bu değerler matematiksel orantıdır. Kağıt genleşmeleri veya çizim hataları sonucu gerçek arazide farklar çıkabilir.
        </p>

        <div class="breakdown-grid">
            <div class="break-item">
                <span>Proje Üzerindeki Ölçü</span>
                <strong id="paperCm">0 cm</strong>
            </div>
            <div class="break-item highlight">
                <span>Santimetre Karşılığı</span>
                <strong id="realCm">0 cm</strong>
            </div>
        </div>

        <div class="ai-insight" id="aiInsight">
            <div class="ai-insight-icon">🎯</div>
            <div class="ai-insight-text">
                <strong>Harita Mühendisi Notu</strong>
                <p id="insightText">Hesaplanıyor...</p>
            </div>
        </div>

        <div class="cross-sell">
            <div class="cross-sell-title">💡 Arsanızın ölçülerini çıkardınız, peki imar durumu ne diyor?</div>
            <div class="cross-sell-text">Arazinizin boyutlarını tespit ettiyseniz, üzerine kaç metrekare bina yapabileceğinizi İmar (Emsal) Hesaplayıcı aracımızla öğrenebilirsiniz.</div>
            <div class="cross-sell-links">
                <a href="/araclar/taks-kaks-hesaplayici">🏢 TAKS / KAKS (Emsal) Hesapla</a>
            </div>
        </div>
    </div>
    </aside>
    </div>{{-- /wc-grid --}}

    <div class="wc-below">

    <!-- ELEME KAPISI -->
    <div class="budget-confirm-box" id="budgetConfirmBox">
        <div class="budget-confirm-title">Mülkünüz için resmi harita ve aplikasyon desteği ister misiniz?</div>
        <div class="budget-confirm-text">
            Arsa sınırlarınızın belirlenmesi (aplikasyon krokisi), tevhid/ifraz işlemleri veya plankote (kot) haritaları için Kainta İnşaat Uzmanlarından destek alabilirsiniz.
        </div>
        <div class="budget-confirm-actions">
            <button type="button" class="budget-btn budget-yes" id="btnYes">✓ Evet, harita mühendisliği teklifi istiyorum.</button>
            <button type="button" class="budget-btn budget-no" id="btnNo">Sadece ölçüme baktım.</button>
        </div>
    </div>

    <!-- ONAY MESAJI -->
    <div class="qualified-message" id="qualifiedMessage">
        <strong>Harika. Doğru koordinatlarla sınır ihlallerini önlüyoruz.</strong>
        <span>Aşağıdaki formu doldurduğunuzda Harita departmanımız yasal süreçler için sizinle iletişime geçecektir.</span>
    </div>

    <!-- B2B LEAD FORM -->
    <div class="lead-box" id="leadBox">
        <h3>Profesyonel Harita & LİHKAB Desteği</h3>
        <p>Arazinize özel total station ve GPS ile hassas ölçüm, plankote ve sınır tespiti için Kainta İnşaat'tan teklif alın.</p>

        <form action="{{ route('lead.store') }}" method="POST">
            @csrf
            <input type="hidden" name="calculated_service" value="Harita ve Ölçek Hesabı">
            <input type="hidden" name="calculated_area" id="leadCalculatedData" value="">
            
            <div class="form-group">
                <label>Adınız Soyadınız / Firma Ünvanı</label>
                <input type="text" name="name" class="form-control" required placeholder="Firma veya Arsa Sahibi Adı">
            </div>

            <div class="form-group">
                <label>Telefon Numaranız</label>
                <input type="tel" name="phone" class="form-control" required placeholder="05XX XXX XX XX">
            </div>

            <div class="form-group">
                <label>Arsanın Bulunduğu Şehir/İlçe</label>
                <input type="text" name="location" class="form-control" required placeholder="Örn: Balıkesir, Ayvalık">
            </div>

            <div class="form-group">
                <label>Hizmet Talebiniz (Opsiyonel)</label>
                <textarea name="notes" class="form-control" style="height: 80px; padding-top:10px;" placeholder="Aplikasyon krokisi mi lazım? Sınır tespiti mi yapılacak?"></textarea>
            </div>

            <x-lead-consent-fields />
            <button type="submit" class="btn btn-submit">Bütçeniz uygunsa Kainta İnşaat'tan net teklif alın →</button>
        </form>
    </div>

    </div>{{-- /wc-below --}}
</main>

<script>
document.getElementById('calculateBtn').addEventListener('click', function() {
    const scale = parseInt(document.getElementById('scale').value) || 1000;
    const measurementCm = parseFloat(document.getElementById('measurement').value) || 0;
    
    if (measurementCm <= 0) {
        alert('Lütfen geçerli bir ölçüm değeri (cm) giriniz.');
        return;
    }

    // Gerçek uzunluk hesaplama (cm cinsinden)
    const realCm = measurementCm * scale;
    // Metreye çevirme
    let realMeters = realCm / 100;
    
    // Formatlama
    const money = new Intl.NumberFormat('tr-TR', { maximumFractionDigits: 2 });
    
    // Ekrana büyük yazılacak değer (Eğer 1000 metreden büyükse Kilometre de yazalım)
    let displayMain = money.format(realMeters) + ' Metre';
    if (realMeters >= 1000) {
        displayMain = money.format(realMeters / 1000) + ' Kilometre';
    }

    document.getElementById('realMeters').innerText = displayMain;
    document.getElementById('paperCm').innerText = measurementCm + ' cm';
    document.getElementById('realCm').innerText = money.format(realCm) + ' cm';

    // AI Insight (Mühendis Notu) Dinamik Hesaplama
    // 1 mm kağıt üzerinde gerçekte kaç metredir?
    const mmToMeter = (1 * scale) / 1000;
    
    let insightStr = `Seçmiş olduğunuz <strong>1/${scale}</strong> ölçekli bir planda ölçtüğünüz her <strong>1 milimetre (mm)</strong>, gerçek arazide tam olarak <strong>${mmToMeter} metreye</strong> denk gelir.<br><br>`;
    insightStr += `İmar paftalarında kurşun kalem ucunun (0.5 mm) kalınlığı bile arazide yaklaşık <strong>${(mmToMeter / 2).toFixed(2)} metrelik</strong> kaymalara neden olabilir. Bu sebeple yasal işlemlerinizde dijital (CAD/NCZ) veri veya uzman ölçümü (Total Station) kullanın.`;

    document.getElementById('insightText').innerHTML = insightStr;

    document.getElementById('leadCalculatedData').value = `Ölçek: 1/${scale} | Kağıt: ${measurementCm}cm | Gerçek: ${realMeters}m`;

    document.getElementById('resultBox').classList.add('active');
    document.getElementById('budgetConfirmBox').style.display = 'block';
    document.getElementById('leadBox').style.display = 'none';
    document.getElementById('leadBox').classList.remove('active');
    document.getElementById('qualifiedMessage').style.display = 'none';
    
    setTimeout(() => { document.getElementById('resultBox').scrollIntoView({ behavior: 'smooth', block: 'start' }); }, 150);
});

document.getElementById('btnYes').addEventListener('click', function() {
    document.getElementById('budgetConfirmBox').style.display = 'none';
    document.getElementById('qualifiedMessage').style.display = 'block';
    document.getElementById('leadBox').style.display = 'block';
    document.getElementById('leadBox').classList.add('active');
    setTimeout(() => { document.getElementById('qualifiedMessage').scrollIntoView({ behavior: 'smooth', block: 'center' }); }, 100);
});

document.getElementById('btnNo').addEventListener('click', function() {
    document.getElementById('budgetConfirmBox').innerHTML = `
        <div class="budget-confirm-title" style="color: #101318;">Anlıyoruz.</div>
        <div class="budget-confirm-text" style="margin-bottom: 0;">
            Bu aşamada iletişim bilgilerinizi almıyoruz. Arsa alım satımında veya inşaat ruhsatı aşamasında resmi ölçüm desteği için buradayız.
        </div>
    `;
    document.getElementById('leadBox').style.display = 'none';
    document.getElementById('qualifiedMessage').style.display = 'none';
});
</script>

    <x-calc.kit />
<x-site.contech-footer />
    <script src="/js/tool-events.js" defer></script>
</body>
</html>
