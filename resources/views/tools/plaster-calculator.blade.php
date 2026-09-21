<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alçı & Sıva Hesaplayıcı | WORKSİTEM</title>
    <link rel="canonical" href="https://worksitem.com/araclar/alci-siva-hesaplama">
    <meta name="description" content="Duvar ve tavanlar için gerekli perlitli sıva veya saten alçı miktarını, sarfiyatı ve yaklaşık uygulama maliyetini metrajınıza göre hesaplayın.">
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
        .break-item.highlight strong { color: var(--accent); }

        .cross-sell { background: #1c1f26; border-radius: 12px; padding: 20px; margin-top: 20px; text-align: left; border: 1px solid rgba(255,255,255,0.1); }
        .cross-sell-title { font-size: 14px; font-weight: 800; color: #fff; margin-bottom: 5px; }
        .cross-sell-text { font-size: 12px; color: #a1a1aa; margin-bottom: 15px; }
        .cross-sell-links { display: flex; gap: 10px; }
        .cross-sell-links a { flex: 1; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); padding: 10px; border-radius: 8px; font-size: 12px; font-weight: 700; color: #fff; text-align: center; transition: .2s; }
        .cross-sell-links a:hover { background: rgba(255,184,0,0.1); border-color: var(--accent); color: var(--accent); }

        .budget-confirm-box { margin-top: 25px; padding: 25px; border: 1px solid var(--line); border-radius: var(--radius); background: #fff; display: none; text-align: center; }
        .budget-confirm-title { font-size: 18px; font-weight: 900; margin-bottom: 8px; letter-spacing: -0.5px;}
        .budget-confirm-text { color: var(--muted); font-size: 13px; line-height: 1.55; margin-bottom: 20px; }
        .budget-confirm-actions { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
        .budget-btn { min-height: 52px; border-radius: 13px; padding: 10px 14px; font-size: 13px; font-weight: 800; border: 1.5px solid var(--line); cursor: pointer; transition: .2s; }
        .budget-no { background: #fff; color: #555; }
        .budget-no:hover { background: #f8f9fb; border-color: #d1d5db; }
        .budget-yes { background: #fff; color: #111; }
        .budget-yes:hover { border-color: var(--accent); background: #fffaf0; box-shadow: 0 0 0 3px rgba(255,184,0,.10); }

        .qualified-message { display: none; margin-top: 25px; padding: 20px; border-radius: 16px; background: #f0fdf4; border: 1px solid #bbf7d0; text-align: center;}
        .qualified-message strong { display: block; font-size: 15px; margin-bottom: 5px; color: #166534; }
        .qualified-message span { display: block; color: #15803d; font-size: 12px; line-height: 1.5; }

        .lead-box { background: #f8f9fb; border: 1px solid var(--line); border-radius: var(--radius); padding: 30px; margin-top: 15px; display: none; }
        .lead-box.active { display: block; animation: fadeIn 0.5s ease; }
        .lead-box h3 { font-size: 18px; font-weight: 900; margin-bottom: 8px; }
        .lead-box p { font-size: 13px; color: var(--muted); margin-bottom: 20px; line-height: 1.5; }
        
        .btn { width: 100%; height: 50px; border: none; border-radius: 12px; font-weight: 800; font-size: 15px; cursor: pointer; transition: .2s; }
        .btn-calc { background: var(--text); color: #fff; margin-top: 10px; }
        .btn-calc:hover { background: #000; }
        .btn-submit { background: var(--accent); color: #000; margin-top: 10px; }
        .btn-submit:hover { background: #e5a400; }

        @media(max-width: 600px) {
            .breakdown-grid, .budget-confirm-actions { grid-template-columns: 1fr; }
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
</head>
<body data-tool-slug="plaster-calculator">

<x-site.contech-header />

<div class="container">
    <div class="tool-header">
        <h1>🪣 Alçı Sarfiyat & Maliyet Hesaplayıcı</h1>
        <p>Duvar ve tavanlarınız için gereken perlitli sıva veya saten alçı miktarını hesaplayın.</p>
    </div>

    <div class="card">
        <div class="form-group">
            <label>Uygulama Alanı (m²)</label>
            <input type="number" id="area" class="form-control" placeholder="Örn: 100" value="100" min="5" step="1">
        </div>

        <div class="form-group">
            <label>Alçı Tipi</label>
            <select id="plasterType" class="form-control">
                <option value="perlitli" selected>Perlitli Sıva Alçısı (Kaba/Dolgu - 1cm kalınlık)</option>
                <option value="saten">Saten Alçı (Boya Altı Yoklama - 2mm kalınlık)</option>
            </select>
        </div>

        <button type="button" class="btn btn-calc" id="calculateBtn">Maliyet ve Metrajı Hesapla →</button>
    </div>

    <div class="result-box" id="resultBox">
        <div class="result-label">TAHMİNİ BÜTÇE (MALZEME + İŞÇİLİK)</div>
        <div class="result-price" id="totalPrice">0 TL</div>
        
        <p style="font-size: 11px; color: #6b7280; margin-bottom:20px;">
            ⚠️ Bu değerler güncel rayiçleriyle hazırlanmış yaklaşık maliyettir. Kesin metraj ve fiyat, Kainta İnşaat uzmanlarının saha keşfiyle belirlenir.
        </p>

        <div class="breakdown-grid">
            <div class="break-item highlight">
                <span>Gereken Torba Sayısı (25 Kg)</span>
                <strong id="bagCount">0 Torba</strong>
            </div>
            <div class="break-item">
                <span>Toplam İhtiyaç (Fire Dahil)</span>
                <strong id="totalKg">0 Kg</strong>
            </div>
            <div class="break-item">
                <span>Tahmini Malzeme Maliyeti</span>
                <strong id="materialCost">0 TL</strong>
            </div>
            <div class="break-item">
                <span>Tahmini İşçilik Maliyeti</span>
                <strong id="laborCost">0 TL</strong>
            </div>
        </div>

        <div class="cross-sell">
            <div class="cross-sell-title">💡 Alçı İşlemi Bittikten Sonra: Boya</div>
            <div class="cross-sell-text">Alçı ve zımpara işlemi tamamlandığında pürüzsüz yüzeyleriniz boyaya hazır hale gelir.</div>
            <div class="cross-sell-links">
                <a href="/araclar/boya-hesaplayici">🎨 Boya Sarfiyatını Hesapla</a>
            </div>
        </div>
    </div>

    <!-- ELEME KAPISI -->
    <div class="budget-confirm-box" id="budgetConfirmBox">
        <div class="budget-confirm-title">Bu bütçe aralığı projeniz için gerçekçi mi?</div>
        <div class="budget-confirm-text">
            Kainta İnşaat'a başvurmadan önce bütçenizin bu seviyede olduğunu onaylayın. Böylece sadece gerçekten uygulanabilir projeleri değerlendirmeye alabiliriz.
        </div>
        <div class="budget-confirm-actions">
            <button type="button" class="budget-btn budget-yes" id="btnYes">✓ Evet, bu bütçeyi karşılayabilirim.</button>
            <button type="button" class="budget-btn budget-no" id="btnNo">Henüz bütçemi netleştirmedim.</button>
        </div>
    </div>

    <!-- ONAY MESAJI -->
    <div class="qualified-message" id="qualifiedMessage">
        <strong>Projeniz Kainta İnşaat değerlendirmesine uygun görünüyor.</strong>
        <span>Aşağıdaki formu doldurduğunuzda detaylı teklif süreci için sizinle iletişime geçilecektir.</span>
    </div>

    <!-- B2B LEAD FORM -->
    <div class="lead-box" id="leadBox">
        <h3>İnce İşler ve Tadilat Teklifi Alın</h3>
        <p>Hesaplanan bu veriler ışığında Kainta İnşaat uzmanlarından yerinde keşif ve sıva/boya teklifi talep edin.</p>

        <form action="{{ route('lead.store') }}" method="POST">
            @csrf
            <input type="hidden" name="calculated_service" value="Alçı Sıva Hesaplayıcı">
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
                <input type="text" name="location" class="form-control" required placeholder="Örn: İstanbul, Pendik">
            </div>

            <div class="form-group">
                <label>Proje Notunuz (Opsiyonel)</label>
                <textarea name="notes" class="form-control" style="height: 80px; padding-top:10px;" placeholder="Tavan veya duvar detayları, ek talepleriniz..."></textarea>
            </div>

            <button type="submit" class="btn btn-submit">Bütçeniz uygunsa Kainta İnşaat'tan net teklif alın →</button>
        </form>
    </div>

</div>

<script>
document.getElementById('calculateBtn').addEventListener('click', function() {
    const area = parseFloat(document.getElementById('area').value) || 0;
    const type = document.getElementById('plasterType').value;
    
    if (area <= 0) {
        alert('Lütfen geçerli bir alan giriniz.');
        return;
    }

    // Sarfiyat (kg/m2)
    const kgPerM2 = type === 'perlitli' ? 10 : 2;
    // Fiyatlandırma
    const pricePerBag = type === 'perlitli' ? 120 : 150;
    const laborPerM2 = type === 'perlitli' ? 90 : 60; // İşçilik maliyeti tahmini

    // Metraj hesabı
    let totalKg = area * kgPerM2;
    totalKg = totalKg * 1.05; // %5 fire payı
    const bags = Math.ceil(totalKg / 25);

    const materialTotal = bags * pricePerBag;
    const laborTotal = area * laborPerM2;
    const grandTotal = materialTotal + laborTotal;

    const money = new Intl.NumberFormat('tr-TR', { maximumFractionDigits: 0 });

    document.getElementById('totalPrice').innerText = money.format(grandTotal) + ' TL';
    document.getElementById('bagCount').innerText = bags + ' Torba';
    document.getElementById('totalKg').innerText = Math.ceil(totalKg) + ' Kg';
    document.getElementById('materialCost').innerText = money.format(materialTotal) + ' TL';
    document.getElementById('laborCost').innerText = money.format(laborTotal) + ' TL';

    const typeLabel = type === 'perlitli' ? 'Perlitli Sıva' : 'Saten Alçı';
    document.getElementById('leadCalculatedData').value = `Tip: ${typeLabel} | Alan: ${area} m2 | Toplam Bütçe: ${money.format(grandTotal)} TL`;

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
        <div class="budget-confirm-title" style="color: #101318;">Sorun değil.</div>
        <div class="budget-confirm-text" style="margin-bottom: 0;">
            Bu aşamada iletişim bilgilerinizi almıyoruz. Tadilat bütçeniz netleştiğinde tekrar teklif isteyebilirsiniz.
        </div>
    `;
    document.getElementById('leadBox').style.display = 'none';
    document.getElementById('qualifiedMessage').style.display = 'none';
});
</script>

    <x-site.contech-footer />
    <script src="/js/tool-events.js" defer></script>
</body>
</html>
