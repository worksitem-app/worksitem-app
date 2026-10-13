<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Su Tesisatı Bütçe Motoru | WORKSİTEM</title>
    <link rel="canonical" href="https://worksitem.com/araclar/su-tesisat-hesaplama">
    <meta name="description" content="Islak hacim sayısına göre tesisat boru metrajınızı, bağlantı parçalarını ve anahtar teslim işçilik bütçenizi hesaplayın.">
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
<body class="wc" data-tool-slug="su-tesisat-calculator">

<x-site.contech-header />

<main class="wc-wrap">
    <x-calc.hero title="Su tesisatı maliyeti hesaplama" crumb="Su tesisatı" :facts="['Temiz su ve pis su boru metrajı', 'Malzeme + işçilik']">
        120 m², 2 banyolu ve 1 mutfaklı bir dairede standart PPRC ve PVC pimaş ile temiz ve pis su tesisatının yenilenmesi yaklaşık 24.810 TL tutar (64 m temiz su, 40 m pis su borusu; vitrifiye montajı hariç). Daire alanını, ıslak hacim sayısını ve boru tipini girin.
    </x-calc.hero>

    <div class="wc-grid">
    <div class="card wc-panel">
        <div class="form-group">
            <label>Daire / Ev Büyüklüğü (m²)</label>
            <input type="number" id="area" class="form-control" placeholder="Örn: 120" value="120" min="20" step="5">
        </div>

        <div class="form-grid">
            <div class="form-group">
                <label>Banyo / Tuvalet Sayısı</label>
                <input type="number" id="bathrooms" class="form-control" placeholder="Örn: 2" value="2" min="1" step="1">
            </div>
            <div class="form-group">
                <label>Mutfak Sayısı</label>
                <input type="number" id="kitchens" class="form-control" placeholder="Örn: 1" value="1" min="1" step="1">
            </div>
        </div>

        <div class="form-group">
            <label>Boru & Malzeme Kalitesi</label>
            <select id="pipeType" class="form-control">
                <option value="standard" selected>Standart PPRC ve PVC Pimaş</option>
                <option value="silent">Sessiz Boru (Pis Su) ve Kompozit Temiz Su</option>
            </select>
        </div>

        <button type="button" class="btn btn-calc" id="calculateBtn">Metraj ve Tesisat Bütçesini Çıkar →</button>
    </div>

    <aside class="wc-aside">
    <div class="wc-empty" data-wc-empty>
        <strong>Sonuç burada görünecek</strong>
        Daire alanını, banyo ve mutfak sayısını girip hesaplayın: boru metrajı, malzeme ve işçilik bütçesi.
    </div>
    <div class="result-box wc-result" data-wc-result id="resultBox">
        <div class="result-label">ANAHTAR TESLİM MEKANİK BÜTÇE</div>
        <div class="result-price" id="totalPrice" data-wc-price>0 TL</div>
        
        <p style="font-size: 11px; color: #6b7280; margin-bottom:20px;">
            ⚠️ Bu değerler malzeme, kırım ve boru döşeme işçilikleri dahil yaklaşık rayiçlerdir. Vitrifiye (klozet, lavabo) montajları hariçtir.
        </p>

        <div class="breakdown-grid">
            <div class="break-item">
                <span>Temiz Su Borusu (PPRC)</span>
                <strong id="cleanPipe">0 Metre</strong>
            </div>
            <div class="break-item">
                <span>Pis Su Borusu (Pimaş)</span>
                <strong id="wastePipe">0 Metre</strong>
            </div>
            <div class="break-item highlight">
                <span>Malzeme Bedeli (Fitting Dahil)</span>
                <strong id="materialCost">0 TL</strong>
            </div>
            <div class="break-item highlight">
                <span>Tesisat İşçilik & Kırım Gideri</span>
                <strong id="laborCost">0 TL</strong>
            </div>
        </div>

        <div class="ai-insight" id="aiInsight">
            <div class="ai-insight-icon">🤖</div>
            <div class="ai-insight-text">
                <strong>Mekanik Uzmanı Notu</strong>
                <p id="insightText">Hesaplamalar yapılıyor...</p>
            </div>
        </div>

        <div class="cross-sell">
            <div class="cross-sell-title">💡 Tesisat çekilmişken fayansları yenilemeyelim mi?</div>
            <div class="cross-sell-text">Su tesisatı değişimi duvarlarda kırım gerektirir. Banyo ve mutfağınızın seramik ve dolaplarını yenilemenin tam zamanı!</div>
            <div class="cross-sell-links">
                <a href="/araclar/banyo-maliyet-hesaplayici">🚿 Banyo Yenileme</a>
                <a href="/araclar/mutfak-maliyet-hesaplayici">🍳 Mutfak Tadilatı</a>
            </div>
        </div>
    </div>
    </aside>
    </div>{{-- /wc-grid --}}

    <div class="wc-below">

    <!-- ELEME KAPISI -->
    <div class="budget-confirm-box" id="budgetConfirmBox">
        <div class="budget-confirm-title">Mekanik tesisatınız için profesyonel destek ister misiniz?</div>
        <div class="budget-confirm-text">
            Kainta İnşaat mühendisleri; doğru boru çapı seçimi, su basıncı hesaplamaları ve garantili sızdırmazlık testleriyle anahtar teslim hizmet verir.
        </div>
        <div class="budget-confirm-actions">
            <button type="button" class="budget-btn budget-yes" id="btnYes">✓ Evet, keşif ve teklif istiyorum.</button>
            <button type="button" class="budget-btn budget-no" id="btnNo">Sadece boru metrajına baktım.</button>
        </div>
    </div>

    <!-- ONAY MESAJI -->
    <div class="qualified-message" id="qualifiedMessage">
        <strong>Harika. Doğru tesisat, yapının ömrünü uzatır.</strong>
        <span>Aşağıdaki formu doldurduğunuzda Kainta İnşaat mekanik birimi sistem çözümleri için iletişime geçecektir.</span>
    </div>

    <!-- B2B LEAD FORM -->
    <div class="lead-box" id="leadBox">
        <h3>Profesyonel Tesisat ve Mekanik Teklifi</h3>
        <p>Projeniz için doğru borulama, vitrifiye altyapısı ve kaçak testli tesisat hizmetini Kainta İnşaat güvencesiyle alın.</p>

        <form action="{{ route('lead.store') }}" method="POST">
            @csrf
            <input type="hidden" name="calculated_service" value="Su Tesisatı Hesabı">
            <input type="hidden" name="calculated_area" id="leadCalculatedData" value="">
            
            <div class="form-group">
                <label>Adınız Soyadınız / Firma Ünvanı</label>
                <input type="text" name="name" class="form-control" required placeholder="Firma veya Kişi Adı">
            </div>

            <div class="form-group">
                <label>Telefon Numaranız</label>
                <input type="tel" name="phone" class="form-control" required placeholder="05XX XXX XX XX">
            </div>

            <div class="form-group">
                <label>Projenin Yapılacağı Şehir/İlçe</label>
                <input type="text" name="location" class="form-control" required placeholder="Örn: İstanbul, Şişli">
            </div>

            <div class="form-group">
                <label>Tesisat Notunuz (Opsiyonel)</label>
                <textarea name="notes" class="form-control" style="height: 80px; padding-top:10px;" placeholder="Gömme rezervuar tesisatı var mı? Ev sıfır mı, tadilat mı görecek?"></textarea>
            </div>

            <x-lead-consent-fields />
            <button type="submit" class="btn btn-submit">Bütçeniz uygunsa Kainta İnşaat'tan net teklif alın →</button>
        </form>
    </div>

    </div>{{-- /wc-below --}}
</main>

<script>
document.getElementById('calculateBtn').addEventListener('click', function() {
    const area = parseFloat(document.getElementById('area').value) || 0;
    const baths = parseInt(document.getElementById('bathrooms').value) || 0;
    const kitchens = parseInt(document.getElementById('kitchens').value) || 0;
    const pipeType = document.getElementById('pipeType').value;
    
    if (area <= 0 || baths < 0 || kitchens < 0) {
        alert('Lütfen geçerli değerler giriniz.');
        return;
    }

    // 1. Metraj Tahminleri (Pratik Mühendislik Yaklaşımı)
    // Temiz su: Banyoda çok hat gezer (sıcak/soğuk), mutfakta nispeten az, alana göre ana hat uzar.
    const cleanPipe = Math.ceil((baths * 18) + (kitchens * 10) + (area * 0.15));
    // Pis su (Pimaş): Temiz suya göre hat daha kısadır ama çapı kalındır.
    const wastePipe = Math.ceil((baths * 12) + (kitchens * 6) + (area * 0.08));
    // Fitting (Dirsek, maşon vb.)
    const fittingsCount = (baths * 35) + (kitchens * 20);

    // 2. Fiyatlandırma Çarpanları
    // Standart fiyatlar
    let pprcPrice = 40;  // TL/mt
    let pvcPrice = 85;   // TL/mt
    let fittingPrice = 15; // TL/adet

    // Sessiz Boru Seçildiyse malzeme maliyeti yaklaşık %60-70 artar
    if (pipeType === 'silent') {
        pprcPrice = 65;
        pvcPrice = 145;
        fittingPrice = 25;
    }

    const materialCost = (cleanPipe * pprcPrice) + (wastePipe * pvcPrice) + (fittingsCount * fittingPrice);
    
    // 3. İşçilik Maliyeti (Kırım ve boru döşeme - Banyo ve Mutfak başına ortalama)
    const laborCost = (baths * 6500) + (kitchens * 4500);

    const grandTotal = materialCost + laborCost;

    const money = new Intl.NumberFormat('tr-TR', { maximumFractionDigits: 0 });

    document.getElementById('totalPrice').innerText = money.format(grandTotal) + ' TL';
    document.getElementById('cleanPipe').innerText = cleanPipe + ' Metre';
    document.getElementById('wastePipe').innerText = wastePipe + ' Metre';
    document.getElementById('materialCost').innerText = money.format(materialCost) + ' TL';
    document.getElementById('laborCost').innerText = money.format(laborCost) + ' TL';

    // AI Insight Mantığı
    const insightText = document.getElementById('insightText');
    if (pipeType === 'silent') {
        insightText.innerHTML = `Mükemmel seçim! <strong>Sessiz pimaş borular</strong> özellikle üst katlardan gelen sifon ve su seslerini yalıtarak yatak odası ve koridorlarda üst düzey akustik konfor sağlar.`;
    } else {
        insightText.innerHTML = `Tesisatta standart malzeme seçimi yapılmıştır. Eğer banyonuz yatak odası gibi sessizlik gerektiren alanlara komşuysa, <strong>Sessiz Boru (Pimaş)</strong> kullanımını değerlendirmenizi tavsiye ederiz.`;
    }

    document.getElementById('leadCalculatedData').value = `Temiz: ${cleanPipe}m | Pis: ${wastePipe}m | Tip: ${pipeType==='silent'?'Sessiz':'Standart'} | Bütçe: ${money.format(grandTotal)} TL`;

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
            Bu aşamada iletişim bilgilerinizi almıyoruz. Profesyonel mekanik projeye ihtiyaç duyduğunuzda tekrar bize ulaşabilirsiniz.
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
