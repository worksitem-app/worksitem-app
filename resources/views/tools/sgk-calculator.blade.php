<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SGK Asgari İşçilik & İlişiksizlik Belgesi Hesabı | WORKSİTEM</title>
    <link rel="canonical" href="https://worksitem.com/araclar/sgk-asgari-iscilik">
    <meta name="description" content="İskan ve ilişiksizlik süreçleri için SGK asgari işçilik hesabını yapın; eksik prim ve fark SPEK tutarını yaklaşık olarak hesaplayın.">
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
        .result-price { font-size: 38px; font-weight: 900; color: #ef4444; margin: 5px 0 15px; letter-spacing: -1px; } /* Kırmızı Ceza Vurgusu */
        
        .breakdown-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-top: 20px; text-align: left; }
        .break-item { background: rgba(255,255,255,0.05); padding: 15px; border-radius: 12px; border: 1px solid rgba(255,255,255,0.1); }
        .break-item span { display: block; font-size: 12px; color: #a1a1aa; margin-bottom: 3px; }
        .break-item strong { display: block; font-size: 18px; color: #fff; font-weight: 800; }
        .break-item.highlight { background: rgba(239, 68, 68, 0.1); border-color: rgba(239, 68, 68, 0.3); }
        .break-item.highlight strong { color: #f87171; }

        .ai-insight { background: rgba(16,19,24,0.1); border: 1px solid rgba(16,19,24,0.3); padding: 15px; border-radius: 12px; margin-top: 20px; text-align: left; display: flex; gap: 12px; align-items: flex-start; }
        .ai-insight-icon { font-size: 24px; }
        .ai-insight-text strong { color: #ffb800; display: block; font-size: 14px; margin-bottom: 4px; }
        .ai-insight-text p { color: #e4e4e7; font-size: 12px; margin: 0; line-height: 1.5; }

        .budget-confirm-box { margin-top: 25px; padding: 25px; border: 1px solid var(--line); border-radius: var(--radius); background: #fff; display: none; text-align: center; }
        .budget-confirm-title { font-size: 18px; font-weight: 900; margin-bottom: 8px; letter-spacing: -0.5px;}
        .budget-confirm-text { color: var(--muted); font-size: 13px; line-height: 1.55; margin-bottom: 20px; }
        .budget-confirm-actions { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
        .budget-btn { min-height: 52px; border-radius: 13px; padding: 10px 14px; font-size: 13px; font-weight: 800; border: 1.5px solid var(--line); cursor: pointer; transition: .2s; }
        .budget-no { background: #fff; color: #555; }
        .budget-no:hover { background: #f8f9fb; border-color: #d1d5db; }
        .budget-yes { background: #fff; color: #111; }
        .budget-yes:hover { border-color: var(--accent); background: #fffaf0; box-shadow: 0 0 0 3px rgba(255,184,0,.10); }

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
<body class="wc" data-tool-slug="sgk-calculator">

<x-site.contech-header />

<main class="wc-wrap">
    <x-calc.hero title="SGK asgari işçilik hesaplama" crumb="SGK asgari işçilik">
        Resmi yapı yaklaşık birim maliyetleri üzerinden, iskan ruhsatı (ilişiksizlik belgesi) alabilmeniz için ödemeniz gereken eksik prim tutarını hesaplayın.
    </x-calc.hero>

    <div class="wc-grid">
    <div class="card wc-panel">
        <h3 style="font-size: 14px; margin-bottom: 15px; color: var(--text);">1. Yapı Bilgileri ve Rayiç Bedel</h3>
        
        <div class="form-grid">
            <div class="form-group">
                <label>İnşaat Türü (Asgari İşçilik Oranı)</label>
                <select id="constType" class="form-control">
                    <option value="9" selected>Karkas / Betonarme Bina (%9.00)</option>
                    <option value="12">Yığma İnşaat (%12.00)</option>
                    <option value="7">Prefabrik Yapı (%7.00)</option>
                </select>
            </div>
            <div class="form-group">
                <label>Toplam İnşaat Alanı (m²)</label>
                <input type="number" id="area" class="form-control" placeholder="Örn: 1000" value="1000" min="1" step="1">
            </div>
        </div>

        <div class="form-group">
            <label>ÇŞB Yapı Yaklaşık Birim Maliyeti (TL/m²)</label>
            <input type="number" id="unitPrice" class="form-control" value="16000" min="1" step="100">
            <p style="font-size: 11px; color: #6b7280; margin-top: 5px;">* 2026 Yılı III. Sınıf B Grubu (Standart Konut) rayici ortalama 16.000 TL olarak varsayılmıştır. Kendi yapı sınıfınıza göre güncelleyebilirsiniz.</p>
        </div>

        <h3 style="font-size: 14px; margin-top: 25px; margin-bottom: 15px; color: var(--text);">2. SGK Bildirim Matrahı</h3>
        
        <div class="form-group">
            <label>Kuruma Önceden Bildirilen İşçilik Matrahı (TL)</label>
            <input type="number" id="declared" class="form-control" placeholder="Örn: 500000" value="0" min="0" step="1000">
            <p style="font-size: 11px; color: #6b7280; margin-top: 5px;">* Bugüne kadar SGK'ya yasal olarak bildirdiğiniz toplam brüt işçilik tutarını giriniz. Bildirim yoksa 0 bırakınız.</p>
        </div>

        <button type="button" class="btn btn-calc" id="calculateBtn">Mevzuat Analizini ve Cezayı Hesapla →</button>
    </div>

    <aside class="wc-aside">
    <div class="wc-empty" data-wc-empty>
        <strong>Sonuç burada görünecek</strong>
        Bilgileri girip hesaplayın; sonuç, döküm ve yaklaşık bütçe burada görünür.
    </div>
    <div class="result-box wc-result" data-wc-result id="resultBox">
        <div class="result-label">ÖDENMESİ GEREKEN FARK SGK PRİMİ (CEZA)</div>
        <div class="result-price" id="penaltyPrice" data-wc-price>0 TL</div>
        
        <p style="font-size: 11px; color: #6b7280; margin-bottom:20px;">
            ⚠️ Bu tablo %37.5 prim oranı (Kısa vadeli sigorta, MYÖ, GSS, İşsizlik vb.) üzerinden hesaplanmıştır.
        </p>

        <div class="breakdown-grid">
            <div class="break-item">
                <span>Resmi İnşaat Maliyeti</span>
                <strong id="officialCost">0 TL</strong>
            </div>
            <div class="break-item">
                <span>İndirimli Asgari İşçilik Oranı</span>
                <strong id="discountedRate">%0.00</strong>
            </div>
            <div class="break-item">
                <span>Bildirilmesi Gereken Toplam İşçilik</span>
                <strong id="requiredLabor">0 TL</strong>
            </div>
            <div class="break-item highlight">
                <span>Fark SPEK (Eksik Bildirim)</span>
                <strong id="diffLabor">0 TL</strong>
            </div>
        </div>

        <div class="ai-insight">
            <div class="ai-insight-icon">⚖️</div>
            <div class="ai-insight-text">
                <strong>Hukuk ve SGK Mevzuat Notu</strong>
                <p>Hesaplamada yasa gereği Asgari İşçilik Oranı üzerinden <strong>%25 eksiltme (sistematik indirim)</strong> uygulanmıştır. Çıkan Fark SPEK (Eksik Bildirim) tutarı üzerinden %37.5 oranında prim borcu (gecikme zammı hariç) tahakkuk ettirilir.</p>
            </div>
        </div>
    </div>
    </aside>
    </div>{{-- /wc-grid --}}

    <div class="wc-below">

    <!-- ELEME KAPISI -->
    <div class="budget-confirm-box" id="budgetConfirmBox">
        <div class="budget-confirm-title">İskan sürecinizi riske atmak istemezsiniz, değil mi?</div>
        <div class="budget-confirm-text">
            SGK ilişiksizlik belgesi ve iskan (yapı kullanma izin belgesi) süreçleri uzmanlık gerektirir. Yüksek cezalarla karşılaşmamak için Kainta İnşaat'ın Hukuk ve Müşavirlik departmanından danışmanlık almak ister misiniz?
        </div>
        <div class="budget-confirm-actions">
            <button type="button" class="budget-btn budget-yes" id="btnYes">✓ Evet, SGK Danışmanlığı İstiyorum.</button>
            <button type="button" class="budget-btn budget-no" id="btnNo">Sadece borcumu hesapladım.</button>
        </div>
    </div>

    <!-- ONAY MESAJI -->
    <div class="qualified-message" id="qualifiedMessage">
        <strong>Harika. Resmi evrak süreçleriniz güvende!</strong>
        <span>Aşağıdaki formu doldurduğunuzda SGK uzmanlarımız ve mühendislerimiz iskan takibi için sizinle iletişime geçecektir.</span>
    </div>

    <!-- B2B LEAD FORM -->
    <div class="lead-box" id="leadBox">
        <h3>SGK Danışmanlığı ve İskan Takibi Teklifi</h3>
        <p>Bürokratik engellere takılmadan, iskan ruhsatınızı en uygun maliyetle ve hızla almanız için profesyonel destek sunuyoruz.</p>

        <form action="{{ route('lead.store') }}" method="POST">
            @csrf
            <input type="hidden" name="calculated_service" value="SGK Asgari İşçilik ve İskan">
            <input type="hidden" name="calculated_area" id="leadCalculatedData" value="">
            
            <div class="form-group">
                <label>Adınız Soyadınız / Firma Ünvanı</label>
                <input type="text" name="name" class="form-control" required placeholder="Müteahhit veya Firma Adı">
            </div>

            <div class="form-group">
                <label>Telefon Numaranız</label>
                <input type="tel" name="phone" class="form-control" required placeholder="05XX XXX XX XX">
            </div>

            <div class="form-group">
                <label>Projenin Bulunduğu Şehir/İlçe</label>
                <input type="text" name="location" class="form-control" required placeholder="Örn: İstanbul, Ataşehir">
            </div>

            <div class="form-group">
                <label>Projenin Durumu (Opsiyonel)</label>
                <textarea name="notes" class="form-control" style="height: 80px; padding-top:10px;" placeholder="İnşaat tamamlandı mı? Belediye başvurusu yapıldı mı?"></textarea>
            </div>

            <x-lead-consent-fields />
            <button type="submit" class="btn btn-submit">Kainta İnşaat'tan Danışmanlık Al →</button>
        </form>
    </div>

    </div>{{-- /wc-below --}}
</main>

<script>
document.getElementById('calculateBtn').addEventListener('click', function() {
    const area = parseFloat(document.getElementById('area').value) || 0;
    const unitPrice = parseFloat(document.getElementById('unitPrice').value) || 0;
    const baseRate = parseFloat(document.getElementById('constType').value) || 0; // % olarak
    const declared = parseFloat(document.getElementById('declared').value) || 0;
    
    if (area <= 0 || unitPrice <= 0) {
        alert('Lütfen geçerli alan ve birim maliyeti giriniz.');
        return;
    }

    // 1. Resmi İnşaat Maliyeti
    const officialCost = area * unitPrice;

    // 2. İndirimli İşçilik Oranı (%25 yasal eksiltme)
    const discountRate = baseRate * 0.75; // Örn: %9 -> %6.75

    // 3. Bildirilmesi Gereken Toplam İşçilik (Asgari Matrah)
    const requiredLabor = officialCost * (discountRate / 100);

    // 4. Fark SPEK (Eksik Bildirim)
    let diffLabor = requiredLabor - declared;
    if(diffLabor < 0) diffLabor = 0; // Bildirim fazlaysa ceza çıkmaz

    // 5. Ceza Tutarı (Fark Primi - %37.5)
    const penalty = diffLabor * 0.375;

    const money = new Intl.NumberFormat('tr-TR', { maximumFractionDigits: 0 });

    document.getElementById('penaltyPrice').innerText = money.format(penalty) + ' TL';
    document.getElementById('officialCost').innerText = money.format(officialCost) + ' TL';
    document.getElementById('discountedRate').innerText = '%' + discountRate.toFixed(2);
    document.getElementById('requiredLabor').innerText = money.format(requiredLabor) + ' TL';
    document.getElementById('diffLabor').innerText = money.format(diffLabor) + ' TL';

    document.getElementById('leadCalculatedData').value = `Resmi Maliyet: ${money.format(officialCost)} TL | Gerekli İşçilik: ${money.format(requiredLabor)} TL | Fark SPEK: ${money.format(diffLabor)} TL | Tahmini Prim: ${money.format(penalty)} TL`;

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
            İskan ve SGK işlemlerinizde profesyonel bir desteğe ihtiyaç duyduğunuzda Kainta İnşaat her zaman yanınızdadır.
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
