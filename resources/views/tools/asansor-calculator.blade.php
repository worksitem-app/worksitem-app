<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asansör Zorunluluğu ve Maliyet Motoru | WORKSİTEM</title>
    <link rel="canonical" href="https://worksitem.com/araclar/asansor-zorunlulugu">
    <meta name="description" content="İmar yönetmeliğine göre asansör zorunluluğunuzu sorgulayın ve durak sayısına göre tahmini asansör kurulum bütçenizi hesaplayın.">
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
        .result-price { font-size: 38px; font-weight: 900; margin: 5px 0 15px; letter-spacing: -1px; }
        .price-req { color: #ef4444; } /* Zorunlu Kırmızı */
        .price-opt { color: #4ade80; } /* Opsiyonel Yeşil */
        
        .breakdown-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-top: 20px; text-align: left; }
        .break-item { background: rgba(255,255,255,0.05); padding: 15px; border-radius: 12px; border: 1px solid rgba(255,255,255,0.1); }
        .break-item span { display: block; font-size: 12px; color: #a1a1aa; margin-bottom: 3px; }
        .break-item strong { display: block; font-size: 18px; color: #fff; font-weight: 800; }
        .break-item.highlight { background: rgba(255,184,0,0.1); border-color: rgba(255,184,0,0.3); }
        .break-item.highlight strong { color: var(--accent); }

        .ai-insight { background: rgba(16,19,24,0.1); border: 1px solid rgba(16,19,24,0.3); padding: 15px; border-radius: 12px; margin-top: 15px; text-align: left; display: flex; gap: 12px; align-items: flex-start; }
        .ai-insight-icon { font-size: 24px; }
        .ai-insight-text strong { color: #ffb800; display: block; font-size: 14px; margin-bottom: 4px; }
        .ai-insight-text p { color: #e4e4e7; font-size: 12px; margin: 0; line-height: 1.5; }

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
<body class="wc" data-tool-slug="asansor-calculator">

<x-site.contech-header />

<main class="wc-wrap">
    <x-calc.hero title="Asansör zorunluluğu ve maliyet hesaplama" crumb="Asansör">
        İmar yönetmeliğine göre asansör zorunluluğunuzu sorgulayın ve durak sayısına göre tahmini kurulum bütçenizi çıkarın.
    </x-calc.hero>

    <div class="wc-grid">
    <div class="card wc-panel">
        <div class="form-grid">
            <div class="form-group">
                <label>Bina Toplam Kat Sayısı</label>
                <input type="number" id="floors" class="form-control" placeholder="Örn: 4 (Zemin + Katlar)" value="4" min="1" step="1">
            </div>
            <div class="form-group">
                <label>Bina Yüksekliği (m)</label>
                <input type="number" id="height" class="form-control" placeholder="Zeminden en üst döşemeye" value="13.6" min="3" step="0.1">
            </div>
        </div>

        <div class="form-grid">
            <div class="form-group">
                <label>Bina Tipi (Kullanım Amacı)</label>
                <select id="buildingType" class="form-control">
                    <option value="residential" selected>Konut / Mesken</option>
                    <option value="commercial">Ticari / Ofis / Kamu Binası</option>
                </select>
            </div>
            <div class="form-group">
                <label>Asansör Kapasitesi (Tercih)</label>
                <select id="capacity" class="form-control">
                    <option value="1.0" selected>4 Kişilik (320 kg) - Standart</option>
                    <option value="1.3">8 Kişilik (630 kg) - Geniş</option>
                    <option value="1.5">10 Kişilik (800 kg) - Yük/Sedye Uyumlu</option>
                </select>
            </div>
        </div>

        <button type="button" class="btn btn-calc" id="calculateBtn">Mevzuatı Kontrol Et ve Bütçe Çıkar →</button>
    </div>

    <aside class="wc-aside">
    <div class="wc-empty" data-wc-empty>
        <strong>Sonuç burada görünecek</strong>
        Bilgileri girip hesaplayın; sonuç, döküm ve yaklaşık bütçe burada görünür.
    </div>
    <div class="result-box wc-result" data-wc-result id="resultBox">
        <div class="result-label" id="statusLabel">PLANLI ALANLAR İMAR YÖNETMELİĞİ KONTROLÜ</div>
        <div class="result-price" id="statusResult" data-wc-price>SORGULANIYOR...</div>
        
        <p style="font-size: 11px; color: #6b7280; margin-bottom:20px;">
            ⚠️ Aşağıdaki bütçe motor, kabin, ray, kapı ve montaj dahil anahtar teslim ortalama donanım maliyetidir.
        </p>

        <div class="breakdown-grid">
            <div class="break-item">
                <span>Hesaplanan Durak Sayısı</span>
                <strong id="stopCount">0 Durak</strong>
            </div>
            <div class="break-item">
                <span>Ana Makine/Motor Bedeli</span>
                <strong id="engineCost">0 TL</strong>
            </div>
            <div class="break-item highlight">
                <span>Ray, Kapı ve Ekipman (Durak Başı)</span>
                <strong id="stopCost">0 TL</strong>
            </div>
            <div class="break-item highlight">
                <span>Tahmini Toplam Asansör Bütçesi</span>
                <strong id="totalBudget">0 TL</strong>
            </div>
        </div>

        <div class="ai-insight" id="aiInsight">
            <div class="ai-insight-icon">⚖️</div>
            <div class="ai-insight-text">
                <strong>Yönetmelik ve Ruhsat Notu</strong>
                <p id="insightText">Planlı Alanlar İmar Yönetmeliği Madde 34'e göre; tek bağımsız bölümlü konutlar hariç, iskan edilebilir kat sayısı 4 ve daha fazla olan veya bina yüksekliği 13.50 metreyi aşan yapılarda asansör tesisi zorunludur.</p>
            </div>
        </div>

        <div class="cross-sell">
            <div class="cross-sell-title">💡 İskan Alırken Sürpriz Yaşamayın!</div>
            <div class="cross-sell-text">Asansör zorunluluğu gibi maddeler iskan ruhsatı aşamasında denetlenir. Aynı aşamada ödemeniz gereken SGK eksik işçilik (Fark SPEK) cezanızı da önceden hesapladınız mı?</div>
            <div class="cross-sell-links">
                <a href="/araclar/sgk-asgari-iscilik">🏛️ SGK İskan Cezası Hesapla</a>
            </div>
        </div>
    </div>
    </aside>
    </div>{{-- /wc-grid --}}

    <div class="wc-below">

    {{-- ELEME KAPISI --}}
    <div class="budget-confirm-box" id="budgetConfirmBox">
        <div class="budget-confirm-title">Kainta İnşaat güvencesiyle asansör teklifi almak ister misiniz?</div>
        <div class="budget-confirm-text">
            Kainta İnşaat Uzmanları asansör projelendirme, kuyu tasarımı ve CE belgeli anahtar teslim montaj sürecini sizin adınıza yönetir.
        </div>
        <div class="budget-confirm-actions">
            <button type="button" class="budget-btn budget-yes" id="btnYes">✓ Evet, asansör projesi ve teklif istiyorum.</button>
            <button type="button" class="budget-btn budget-no" id="btnNo">Sadece mevzuatı kontrol ettim.</button>
        </div>
    </div>

    {{-- ONAY MESAJI --}}
    <div class="qualified-message" id="qualifiedMessage">
        <strong>Son bir adım kaldı.</strong>
        <span>Aşağıdaki formu doldurduğunuzda teknik ekibimiz asansör kuyu rölövesi ve teklif için sizinle iletişime geçecektir.</span>
    </div>

    {{-- B2B LEAD FORM --}}
    <div class="lead-box" id="leadBox">
        <h3>Profesyonel Asansör ve Taahhüt Teklifi</h3>
        <p>Hesapladığınız durak sayısına göre Kainta İnşaat'ın çözüm ortaklarından TSE ve CE sertifikalı asansör kurulum teklifi alın.</p>

        <form action="{{ route('lead.store') }}" method="POST">
            @csrf
            <input type="hidden" name="calculated_service" value="Asansör Zorunluluğu ve Bütçesi">
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
                <label>Projenin Yapılacağı Şehir/İlçe</label>
                <input type="text" name="location" class="form-control" required placeholder="Örn: İzmir, Bayraklı">
            </div>

            <div class="form-group">
                <label>Kuyu Notunuz (Opsiyonel)</label>
                <textarea name="notes" class="form-control" style="height: 80px; padding-top:10px;" placeholder="Asansör kuyusu betonarme mi döküldü? Makine dairesi var mı (MR/MRL)?"></textarea>
            </div>

            <x-lead-consent-fields />
            <button type="submit" class="btn btn-submit">Kainta İnşaat'tan Taahhüt Teklifi Al →</button>
        </form>
    </div>

    </div>{{-- /wc-below --}}
</main>

<script>
document.getElementById('calculateBtn').addEventListener('click', function() {
    const floors = parseInt(document.getElementById('floors').value) || 0;
    const height = parseFloat(document.getElementById('height').value) || 0;
    const bType = document.getElementById('buildingType').value;
    const capacityMultiplier = parseFloat(document.getElementById('capacity').value) || 1.0;
    
    if (floors <= 0 || height <= 0) {
        alert('Lütfen geçerli bina verileri giriniz.');
        return;
    }

    // 1. MEVZUAT KONTROLÜ (Planlı Alanlar İmar Yönetmeliği Madde 34)
    // Kat sayısı 4 ve üzeri VEYA bina yüksekliği 13.50m'den fazla ise zorunlu.
    let isRequired = false;
    if (floors >= 4 || height > 13.50) {
        isRequired = true;
    }
    
    // Ticari ve kamu binalarında erişilebilirlik (engelli) standartları gereği çoğu zaman daha düşük katlarda bile platform veya asansör istenir.
    let typeInsight = "";
    if (bType === 'commercial' && !isRequired && floors > 1) {
        isRequired = true; 
        typeInsight = "Ticari/Kamu binası seçtiğiniz için, engelli erişilebilirlik standartları gereği TS ISO 21542 normlarında taşıma sistemi/asansör yapılması zorunludur.";
    }

    // Arayüz Rengi ve Metni
    const statusResult = document.getElementById('statusResult');
    if (isRequired) {
        statusResult.innerText = "YASAL OLARAK ZORUNLU";
        statusResult.className = "result-price price-req";
    } else {
        statusResult.innerText = "ZORUNLU DEĞİL (OPSİYONEL)";
        statusResult.className = "result-price price-opt";
    }

    // 2. BÜTÇE HESAPLAMASI (2026 Tahmini Rayiçleri)
    // Temel Paket (Kabin, Makine Motor, Pano): Ortalama 400.000 TL (Kapasite çarpanı etkiler)
    const baseEngineCost = {{ (float) data_get($pricing ?? [], 'asansor.paket', 400000) }} * capacityMultiplier;
    
    // Durak Başına Ek Maliyet (Kat kapısı, kuyu içi raylar, kablolar vb.): Durak başı ~35.000 TL
    const costPerStop = {{ (float) data_get($pricing ?? [], 'asansor.durak', 35000) }} * capacityMultiplier;
    const totalStopCost = floors * costPerStop;

    const grandTotal = baseEngineCost + totalStopCost;

    const money = new Intl.NumberFormat('tr-TR', { maximumFractionDigits: 0 });

    document.getElementById('stopCount').innerText = floors + ' Durak';
    document.getElementById('engineCost').innerText = money.format(baseEngineCost) + ' TL';
    document.getElementById('stopCost').innerText = money.format(totalStopCost) + ' TL';
    document.getElementById('totalBudget').innerText = money.format(grandTotal) + ' TL';

    // Insight Metni
    let insightStr = `Planlı Alanlar İmar Yönetmeliği Madde 34'e göre; kat sayısı 4 ve daha fazla olan (Zemin dahil) veya bina yüksekliği 13.50 metreyi aşan yapılarda asansör tesisi zorunludur. `;
    if (typeInsight !== "") {
        insightStr += `<br><br><strong>Not:</strong> ${typeInsight}`;
    }
    document.getElementById('insightText').innerHTML = insightStr;

    document.getElementById('leadCalculatedData').value = `Durum: ${isRequired?'ZORUNLU':'DEĞİL'} | Durak: ${floors} | Kapasite Çarpanı: ${capacityMultiplier} | Tahmini Bütçe: ${money.format(grandTotal)} TL`;

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
            Bu aşamada iletişim bilgilerinizi almıyoruz. Kaba inşaatınız bitip asansör montajına geçeceğiniz zaman tekrar bize ulaşabilirsiniz.
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
