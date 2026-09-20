<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>İşçilik & Taşeron Maliyet Hesaplayıcı | WORKSİTEM</title>
    <link rel="canonical" href="https://worksitem.com/araclar/iscilik-maliyeti">
    <meta name="description" content="Yevmiyeli işçi çalıştırmanın SGK, vergi, yol ve yemek dahil işverene olan gerçek (gizli) maliyetini hesaplayın.">
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
        .break-item.highlight strong { color: var(--accent); }

        .ai-insight { background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.3); padding: 15px; border-radius: 12px; margin-top: 15px; text-align: left; display: flex; gap: 12px; align-items: flex-start; }
        .ai-insight-icon { font-size: 24px; }
        .ai-insight-text strong { color: #f87171; display: block; font-size: 14px; margin-bottom: 4px; }
        .ai-insight-text p { color: #e4e4e7; font-size: 12px; margin: 0; line-height: 1.5; }

        .cross-sell { background: #1c1f26; border-radius: 12px; padding: 20px; margin-top: 20px; text-align: left; border: 1px solid rgba(255,255,255,0.1); }
        .cross-sell-title { font-size: 14px; font-weight: 800; color: #fff; margin-bottom: 5px; }
        .cross-sell-text { font-size: 12px; color: #a1a1aa; margin-bottom: 15px; }
        .cross-sell-links { display: flex; gap: 10px; }
        .cross-sell-links button { flex: 1; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); padding: 10px; border-radius: 8px; font-size: 12px; font-weight: 700; color: #fff; text-align: center; transition: .2s; cursor: pointer; }
        .cross-sell-links button:hover { background: rgba(255,184,0,0.1); border-color: var(--accent); color: var(--accent); }

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
</head>
<body>

<x-site.contech-header />

<div class="container">
    <div class="tool-header">
        <h1>👷 İşçilik & Taşeron Maliyet Motoru</h1>
        <p>Taşeron veya yevmiyeli işçi çalıştırmanın SGK, vergi, yol ve yemek dahil işverene yansıyan gerçek (gizli) maliyetini hesaplayın.</p>
    </div>

    <div class="card">
        <div class="form-grid">
            <div class="form-group">
                <label>İşçi Sayısı (Kişi)</label>
                <input type="number" id="workers" class="form-control" placeholder="Örn: 2" value="1" min="1" step="1">
            </div>
            <div class="form-group">
                <label>Net Günlük Yevmiye (Kişi Başı TL)</label>
                <input type="number" id="dailyWage" class="form-control" placeholder="Örn: 1500" value="1500" min="500" step="50">
            </div>
        </div>

        <div class="form-grid">
            <div class="form-group">
                <label>Çalışılacak Gün Sayısı</label>
                <input type="number" id="days" class="form-control" placeholder="Örn: 30" value="30" min="1" step="1">
            </div>
            <div class="form-group">
                <label>Günlük Yemek + Yol (Kişi Başı TL)</label>
                <input type="number" id="foodTravel" class="form-control" placeholder="Örn: 350" value="350" min="0" step="10">
            </div>
        </div>

        <button type="button" class="btn btn-calc" id="calculateBtn">İşveren Maliyetini Analiz Et →</button>
    </div>

    <div class="result-box" id="resultBox">
        <div class="result-label">İŞVERENE TOPLAM GİDER ANALİZİ</div>
        <div class="result-price" id="totalPrice">0 TL</div>
        
        <p style="font-size: 11px; color: #6b7280; margin-bottom:20px;">
            ⚠️ Bu tablo işçi yevmiyesinin üzerine binen resmi yükleri (Brütleşme) ve günlük giderleri hesaplar.
        </p>

        <div class="breakdown-grid">
            <div class="break-item">
                <span>Ödenecek Net Yevmiye Toplamı</span>
                <strong id="netTotal">0 TL</strong>
            </div>
            <div class="break-item highlight">
                <span>Yemek & Yol Gideri (Toplam)</span>
                <strong id="foodTotal">0 TL</strong>
            </div>
            <div class="break-item highlight">
                <span>SGK ve Vergi Yükü (%37.5 Ort.)</span>
                <strong id="taxTotal">0 TL</strong>
            </div>
            <div class="break-item">
                <span>Kişi Başı Günlük Maliyet</span>
                <strong id="costPerWorker">0 TL/Gün</strong>
            </div>
        </div>

        <div class="ai-insight">
            <div class="ai-insight-icon">⚠️</div>
            <div class="ai-insight-text">
                <strong>İş Güvenliği ve Yasal Risk Notu</strong>
                <p>Bir işçinin net yevmiyesinin üzerinde; işverene SGK, stopaj ve işsizlik fonu gibi ciddi bir yasal maliyet biner. Bu tabloya olası iş kazası riskleri ve zorunlu İSG (Baret, Yelek, Emniyet Kemeri) ekipmanı giderleri dahil edilmemiştir.</p>
            </div>
        </div>

        <div class="cross-sell">
            <div class="cross-sell-title">💡 Neden taşeron ve yasal risklerle uğraşıyorsunuz?</div>
            <div class="cross-sell-text">Kainta İnşaat, kendi sigortalı ve İSG eğitimli profesyonel ekipleriyle anahtar teslim hizmet verir. Yasal riskler ve gizli maliyetler bizim sorumluluğumuzdadır.</div>
            <div class="cross-sell-links">
                <button type="button" id="triggerLeadBoxBtn">Anahtar Teslim Teklif Alın</button>
            </div>
        </div>
    </div>

    <!-- ELEME KAPISI -->
    <div class="budget-confirm-box" id="budgetConfirmBox">
        <div class="budget-confirm-title">Hesaplanan maliyet bütçenize uygun mu?</div>
        <div class="budget-confirm-text">
            Bireysel yevmiyeci çalıştırmanın risklerini gördünüz. Projenizi riske atmadan Kainta İnşaat'tan profesyonel ve sigortalı ekip desteği almak ister misiniz?
        </div>
        <div class="budget-confirm-actions">
            <button type="button" class="budget-btn budget-yes" id="btnYes">✓ Evet, anahtar teslim teklif istiyorum.</button>
            <button type="button" class="budget-btn budget-no" id="btnNo">Hayır, sadece maliyete baktım.</button>
        </div>
    </div>

    <!-- ONAY MESAJI -->
    <div class="qualified-message" id="qualifiedMessage">
        <strong>Harika. Bireysel risklerden kurtuldunuz!</strong>
        <span>Aşağıdaki formu doldurduğunuzda teknik ekibimiz projenize özel anahtar teslim teklif için iletişime geçecektir.</span>
    </div>

    <!-- B2B LEAD FORM -->
    <div class="lead-box" id="leadBox">
        <h3>Profesyonel Taşeron & Uygulama Teklifi</h3>
        <p>Yasal yükümlülükleri ve SGK risklerini Kainta İnşaat'a devredin, anahtar teslim sözleşme ile rahat edin.</p>

        <form action="{{ route('lead.store') }}" method="POST">
            @csrf
            <input type="hidden" name="calculated_service" value="İşçilik ve Taşeron Maliyeti">
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
                <input type="text" name="location" class="form-control" required placeholder="Örn: Ankara, Yenimahalle">
            </div>

            <div class="form-group">
                <label>Hangi alanda ekibe ihtiyacınız var?</label>
                <textarea name="notes" class="form-control" style="height: 80px; padding-top:10px;" placeholder="Örn: Kalıp ustası, alçıpan ekibi veya kaba inşaat taşeronu..."></textarea>
            </div>

            <button type="submit" class="btn btn-submit">Kainta İnşaat'tan Güvenli Teklif Al →</button>
        </form>
    </div>

</div>

<script>
document.getElementById('calculateBtn').addEventListener('click', function() {
    const workers = parseInt(document.getElementById('workers').value) || 0;
    const dailyWage = parseFloat(document.getElementById('dailyWage').value) || 0;
    const days = parseInt(document.getElementById('days').value) || 0;
    const foodTravel = parseFloat(document.getElementById('foodTravel').value) || 0;
    
    if (workers <= 0 || dailyWage <= 0 || days <= 0) {
        alert('Lütfen geçerli veriler giriniz.');
        return;
    }

    // 1. Net Yevmiye Toplamı
    const netTotal = workers * dailyWage * days;
    
    // 2. SGK ve Vergi Yükü (Net üzerinden ortalama brütleşme oranı ~%37.5)
    // Brüt = Net / 0.714. SGK+İşsizlik İşveren = Brüt * 0.225 vs.. Pratik hesapla Net'in yaklaşık %37.5'i.
    const taxTotal = netTotal * 0.375;

    // 3. Yemek ve Yol Toplamı
    const foodTotal = workers * foodTravel * days;

    // 4. Genel Toplam
    const grandTotal = netTotal + taxTotal + foodTotal;

    // 5. Kişi Başı Günlük Gerçek Maliyet
    const costPerWorkerPerDay = grandTotal / (workers * days);

    const money = new Intl.NumberFormat('tr-TR', { maximumFractionDigits: 0 });

    document.getElementById('totalPrice').innerText = money.format(grandTotal) + ' TL';
    document.getElementById('netTotal').innerText = money.format(netTotal) + ' TL';
    document.getElementById('taxTotal').innerText = money.format(taxTotal) + ' TL';
    document.getElementById('foodTotal').innerText = money.format(foodTotal) + ' TL';
    document.getElementById('costPerWorker').innerText = money.format(costPerWorkerPerDay) + ' TL/Gün';

    document.getElementById('leadCalculatedData').value = `İşçi: ${workers} Kişi | Gün: ${days} | Net Yevmiye: ${dailyWage} TL | Risk Dahil Toplam Maliyet: ${money.format(grandTotal)} TL`;

    document.getElementById('resultBox').classList.add('active');
    document.getElementById('budgetConfirmBox').style.display = 'block';
    document.getElementById('leadBox').style.display = 'none';
    document.getElementById('leadBox').classList.remove('active');
    document.getElementById('qualifiedMessage').style.display = 'none';
    
    setTimeout(() => { document.getElementById('resultBox').scrollIntoView({ behavior: 'smooth', block: 'start' }); }, 150);
});

// Cross-Sell butonunu Lead Box'a yönlendir
document.getElementById('triggerLeadBoxBtn').addEventListener('click', function() {
    document.getElementById('budgetConfirmBox').style.display = 'none';
    document.getElementById('qualifiedMessage').style.display = 'block';
    document.getElementById('leadBox').style.display = 'block';
    document.getElementById('leadBox').classList.add('active');
    setTimeout(() => { document.getElementById('qualifiedMessage').scrollIntoView({ behavior: 'smooth', block: 'center' }); }, 100);
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
            Bu aşamada iletişim bilgilerinizi almıyoruz. Yasal ve operasyonel riskleri devretmek istediğinizde tekrar bize ulaşabilirsiniz.
        </div>
    `;
    document.getElementById('leadBox').style.display = 'none';
    document.getElementById('qualifiedMessage').style.display = 'none';
});
</script>

    <x-site.contech-footer />
</body>
</html>
