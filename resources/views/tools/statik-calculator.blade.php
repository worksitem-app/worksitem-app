<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statik Proje Bedeli Hesaplayıcı (TMMOB & ) | WORKSİTEM</title>
    <link rel="canonical" href="https://worksitem.com/araclar/statik-proje-bedeli">
    <meta name="description" content="TMMOB katsayıları ve araçtaki güncel maliyet endeksine göre yaklaşık minimum statik proje mühendislik hizmet bedelini hesaplayın ve bütçenizi planlayın.">
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
        .ai-badge { display: inline-block; background: rgba(16,19,24,0.15); color: #101318; padding: 6px 14px; border-radius: 100px; font-size: 12px; font-weight: 900; margin-bottom: 15px; letter-spacing: 0.5px; text-transform: uppercase; }

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
        .break-item.highlight { background: rgba(16,19,24,0.1); border-color: rgba(16,19,24,0.3); }
        .break-item.highlight strong { color: #ffb800; }

        .ai-insight { background: rgba(255, 184, 0, 0.1); border: 1px solid rgba(255, 184, 0, 0.3); padding: 15px; border-radius: 12px; margin-top: 15px; text-align: left; display: flex; gap: 12px; align-items: flex-start; }
        .ai-insight-icon { font-size: 24px; }
        .ai-insight-text strong { color: var(--accent); display: block; font-size: 14px; margin-bottom: 4px; }
        .ai-insight-text p { color: #e4e4e7; font-size: 12px; margin: 0; line-height: 1.5; }

        .cross-sell { background: #1c1f26; border-radius: 12px; padding: 20px; margin-top: 20px; text-align: left; border: 1px solid rgba(255,255,255,0.1); }
        .cross-sell-title { font-size: 14px; font-weight: 800; color: #fff; margin-bottom: 5px; }
        .cross-sell-text { font-size: 12px; color: #a1a1aa; margin-bottom: 15px; }
        .cross-sell-links { display: flex; gap: 10px; }
        .cross-sell-links a { flex: 1; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); padding: 10px; border-radius: 8px; font-size: 12px; font-weight: 700; color: #fff; text-align: center; transition: .2s; }
        .cross-sell-links a:hover { background: rgba(16,19,24,0.1); border-color: #ffb800; color: #ffb800; }

        .budget-confirm-box { margin-top: 25px; padding: 25px; border: 1px solid var(--line); border-radius: var(--radius); background: #fff; display: none; text-align: center; }
        .budget-confirm-title { font-size: 18px; font-weight: 900; margin-bottom: 8px; letter-spacing: -0.5px;}
        .budget-confirm-text { color: var(--muted); font-size: 13px; line-height: 1.55; margin-bottom: 20px; }
        .budget-confirm-actions { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
        .budget-btn { min-height: 52px; border-radius: 13px; padding: 10px 14px; font-size: 13px; font-weight: 800; border: 1.5px solid var(--line); cursor: pointer; transition: .2s; }
        .budget-no { background: #fff; color: #555; }
        .budget-no:hover { background: #f8f9fb; border-color: #d1d5db; }
        .budget-yes { background: #fff; color: #111; }
        .budget-yes:hover { border-color: #ffb800; background: #fff8e1; box-shadow: 0 0 0 3px rgba(16,19,24,.10); }

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
        .btn-submit { background: #101318; color: #fff; margin-top: 10px; }
        .btn-submit:hover { background: #101318; }

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
<body class="wc" data-tool-slug="statik-calculator">

<x-site.contech-header />

<main class="wc-wrap">
    <x-calc.hero title="Statik proje bedeli hesaplama" crumb="Statik proje bedeli">
        İMO katsayılarına ve deprem yönetmeliği çarpanlarına göre statik mühendislik tasarım bedelini hesaplayın.
    </x-calc.hero>

    <div class="wc-grid">
    <div class="card wc-panel">
        <div class="form-grid">
            <div class="form-group">
                <label>Toplam Brüt İnşaat Alanı (m²)</label>
                <input type="number" id="area" class="form-control" placeholder="Örn: 2500" value="2500" min="50" step="10">
            </div>
            <div class="form-group">
                <label>Proje Türü (Mühendislik Kapsamı)</label>
                <select id="projectType" class="form-control">
                    <option value="1.0" selected>Yeni Bina (Sıfırdan Projelendirme)</option>
                    <option value="1.5">Performans Analizi / Güçlendirme</option>
                </select>
            </div>
        </div>

        <div class="form-grid">
            <div class="form-group">
                <label>Bina Sınıfı (ÇŞB / İMO Ortalaması)</label>
                <select id="buildingClass" class="form-control">
                    <option value="0.60">1. Sınıf (Tarım, Sundurma vb.)</option>
                    <option value="0.80">2. Sınıf (Endüstriyel, Depo)</option>
                    <option value="1.00" selected>3. Sınıf (Konut, Standart Ofis)</option>
                    <option value="1.20">4. Sınıf (AVM, Lüks Konut, Hastane)</option>
                </select>
            </div>
            <div class="form-group">
                <label>Tahmini 2026 birim maliyet (TL/m²)</label>
                <input type="number" id="unitPrice" class="form-control" value="16000" min="1000" step="500">
                <p style="font-size: 11px; color: #6b7280; margin-top: 5px;">Varsayılan taban değer 16.000 TL/m²'dir; projenize göre değiştirebilirsiniz. Değiştirebilirsiniz.</p>
            </div>
        </div>

        <button type="button" class="btn btn-calc" id="calculateBtn">İMO Taban Proje Bedelini Hesapla →</button>
    </div>

    <aside class="wc-aside">
    <div class="wc-empty" data-wc-empty>
        <strong>Sonuç burada görünecek</strong>
        Bilgileri girip hesaplayın; sonuç, döküm ve yaklaşık bütçe burada görünür.
    </div>
    <div class="result-box wc-result" data-wc-result id="resultBox">
        <div class="result-label">TMMOB ASGARİ MÜHENDİSLİK (STATİK) BEDELİ</div>
        <div class="result-price" id="totalPrice" data-wc-price>0 TL</div>
        
        <p style="font-size: 11px; color: #6b7280; margin-bottom:20px;">
            ⚠️ Bu değer TMMOB asgari fiyat tarifesine göre hesaplanmış olup piyasa serbest piyasa koşullarına göre değişkenlik gösterebilir.
        </p>

        <div class="breakdown-grid">
            <div class="break-item">
                <span>Temel İMO Proje Çarpanı</span>
                <strong id="multiplierShow">% 1.5</strong>
            </div>
            <div class="break-item">
                <span>Bina Sınıf Katsayısı</span>
                <strong id="classValueShow">0.00</strong>
            </div>
            <div class="break-item highlight">
                <span>Resmi İnşaat Maliyeti Tahmini</span>
                <strong id="officialCost">0 TL</strong>
            </div>
            <div class="break-item highlight">
                <span>Proje Mühendislik Payı</span>
                <strong id="engRatio">% 0.00</strong>
            </div>
        </div>

        <div class="ai-insight" id="aiInsight">
            <div class="ai-insight-icon">💡</div>
            <div class="ai-insight-text">
                <strong>Deprem Yönetmeliği (TBDY-2018) Notu</strong>
                <p id="insightText">Hesaplanıyor...</p>
            </div>
        </div>

        <div class="cross-sell">
            <div class="cross-sell-title">💡 Mimari proje bedelini de merak ediyor musunuz?</div>
            <div class="cross-sell-text">İmar durumunuz belli ve statik projeye geçiyorsanız, Mimarlar Odası asgari proje çizim bedelini veya zemin etüdü gereksinimlerini de değerlendirmelisiniz.</div>
            <div class="cross-sell-links">
                <a href="/araclar/taks-kaks-hesaplayici">🏢 İmar & KAKS Hesapla</a>
            </div>
        </div>
    </div>
    </aside>
    </div>{{-- /wc-grid --}}

    <div class="wc-below">

    {{-- ELEME KAPISI --}}
    <div class="budget-confirm-box" id="budgetConfirmBox">
        <div class="budget-confirm-title">Mühendislik hesaplamalarınızı Kainta İnşaat güvencesine almak ister misiniz?</div>
        <div class="budget-confirm-text">
            Kainta İnşaat Uzmanları; depreme dayanıklı, ekonomik ve şantiyede uygulanabilir statik projeleri 3D yapısal analiz programlarıyla çözümler.
        </div>
        <div class="budget-confirm-actions">
            <button type="button" class="budget-btn budget-yes" id="btnYes">✓ Evet, statik proje tasarımı teklifi istiyorum.</button>
            <button type="button" class="budget-btn budget-no" id="btnNo">Sadece resmi asgari bedele baktım.</button>
        </div>
    </div>

    {{-- ONAY MESAJI --}}
    <div class="qualified-message" id="qualifiedMessage">
        <strong>Son bir adım kaldı.</strong>
        <span>Aşağıdaki formu doldurduğunuzda Kainta İnşaat Proje Ofisi detaylı teknik teklif için sizinle iletişime geçecektir.</span>
    </div>

    {{-- B2B LEAD FORM --}}
    <div class="lead-box" id="leadBox">
        <h3>Profesyonel Statik Tasarım Teklifi</h3>
        <p>Mimari projenizi sisteme uygun hale getirecek, demir ve beton optimizasyonunu sağlayacak Kainta İnşaat statik tasarım teklifini alın.</p>

        <form action="{{ route('lead.store') }}" method="POST">
            @csrf
            <input type="hidden" name="calculated_service" value="Statik Proje Tasarım Hesabı">
            <input type="hidden" name="calculated_area" id="leadCalculatedData" value="">
            
            <div class="form-group">
                <label>Adınız Soyadınız / Firma Ünvanı</label>
                <input type="text" name="name" class="form-control" required placeholder="Firma, Müteahhit veya Mimar Adı">
            </div>

            <div class="form-group">
                <label>Telefon Numaranız</label>
                <input type="tel" name="phone" class="form-control" required placeholder="05XX XXX XX XX">
            </div>

            <div class="form-group">
                <label>Projenin Bulunduğu Şehir/İlçe</label>
                <input type="text" name="location" class="form-control" required placeholder="Örn: Bursa, Osmangazi">
            </div>

            <div class="form-group">
                <label>Proje Detayları (Opsiyonel)</label>
                <textarea name="notes" class="form-control" style="height: 80px; padding-top:10px;" placeholder="Mimari proje (DWG) hazır mı? Bina kaç katlı? Çelik mi, betonarme mi?"></textarea>
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
    const unitPrice = parseFloat(document.getElementById('unitPrice').value) || 0;
    const bClass = parseFloat(document.getElementById('buildingClass').value) || 1;
    const pType = parseFloat(document.getElementById('projectType').value) || 1;
    
    if (area <= 0 || unitPrice <= 0) {
        alert('Lütfen geçerli alan ve rayiç değerleri giriniz.');
        return;
    }

    // 1. Resmi İnşaat Maliyeti (ÇŞB'ye göre taban maliyet)
    const officialCost = area * unitPrice * bClass;

    // 2. İMO Temel Oranı (Genelde yapı yaklaşık maliyetinin %1.5'i statik projedir)
    const baseEngineeringRatio = 0.015;

    // 3. Proje Türü (Güçlendirme vb. çarpanlar)
    const finalRatio = baseEngineeringRatio * pType;
    
    // 4. Asgari Statik Proje Bedeli
    const grandTotal = officialCost * finalRatio;

    const money = new Intl.NumberFormat('tr-TR', { maximumFractionDigits: 0 });

    document.getElementById('totalPrice').innerText = money.format(grandTotal) + ' TL';
    document.getElementById('officialCost').innerText = money.format(officialCost) + ' TL';
    document.getElementById('classValueShow').innerText = bClass.toFixed(2);
    document.getElementById('engRatio').innerText = '%' + (finalRatio * 100).toFixed(2);

    // Uzman notu / Mühendislik Notu Dinamik Değişimi
    let insightStr = `TBDY-2018 kurallarına göre projelendirme maliyetleri belirlenirken yapının resmi maliyeti (<strong>${money.format(officialCost)} TL</strong>) baz alınmıştır.<br><br>`;
    
    if (pType > 1) {
        insightStr += `<strong>Performans/Güçlendirme Uyarı:</strong> Mevcut yapıların rölöve çıkarımı, malzeme testleri (karot vb.) ve doğrusal olmayan itme (pushover) analizleri çok detaylı ve yasal sorumluluğu yüksek işlemlerdir. Bu nedenle proje hizmet bedeline <strong>%50 güçlendirme katsayısı</strong> (Zorluk Çarpanı) uygulanmıştır.`;
    } else if (area > 3000) {
        insightStr += `<strong>Büyük Yapı Uyarı:</strong> Proje alanınız büyük olduğu için TBDY-2018 kapsamında <strong>Zemin-Yapı Etkileşimi</strong> ve detaylı dinamik mod birleştirme analizleri gerekecektir. Hesaplamada yapı katsayıları optimize edilmiştir.`;
    } else {
        insightStr += `Yeni binaların tasarımında İMO asgari katsayısı olan <strong>%1.5</strong> mühendislik payı kullanılmıştır. Bu fiyata Geoteknik rapor (Zemin Etüdü) dahil değildir.`;
    }

    document.getElementById('insightText').innerHTML = insightStr;

    document.getElementById('leadCalculatedData').value = `Alan: ${area}m2 | Rayiç: ${unitPrice} | Sınıf: ${bClass} | Tür Çarpanı: ${pType} | Bütçe: ${money.format(grandTotal)} TL`;

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
            Bu aşamada iletişim bilgilerinizi almıyoruz. Mimari projeniz tamamlanıp kaba inşaata giden yolda statik tasarıma ihtiyaç duyduğunuzda bizi arayabilirsiniz.
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
