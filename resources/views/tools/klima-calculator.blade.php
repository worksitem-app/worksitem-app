<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akıllı Klima & BTU Hesaplayıcı | WORKSİTEM</title>
    <link rel="canonical" href="https://worksitem.com/araclar/klima-btu-hesaplayici">
    <meta name="description" content="Kişi sayısı, yalıtım ve cephe durumuna göre mekanınızın ihtiyacı olan doğru klima (BTU) kapasitesini profesyonelce bulun.">
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
        .break-item.highlight { background: rgba(56, 189, 248, 0.1); border-color: rgba(56, 189, 248, 0.3); }
        .break-item.highlight strong { color: #38bdf8; }

        .ai-insight { background: rgba(255, 184, 0, 0.1); border: 1px solid rgba(255, 184, 0, 0.3); padding: 15px; border-radius: 12px; margin-top: 15px; text-align: left; display: flex; gap: 12px; align-items: flex-start; }
        .ai-insight-icon { font-size: 24px; }
        .ai-insight-text strong { color: var(--accent); display: block; font-size: 14px; margin-bottom: 4px; }
        .ai-insight-text p { color: #e4e4e7; font-size: 12px; margin: 0; line-height: 1.5; }

        .cross-sell { background: #1c1f26; border-radius: 12px; padding: 20px; margin-top: 20px; text-align: left; border: 1px solid rgba(255,255,255,0.1); }
        .cross-sell-title { font-size: 14px; font-weight: 800; color: #fff; margin-bottom: 5px; }
        .cross-sell-text { font-size: 12px; color: #a1a1aa; margin-bottom: 15px; }
        .cross-sell-links { display: flex; gap: 10px; }
        .cross-sell-links a { flex: 1; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); padding: 10px; border-radius: 8px; font-size: 12px; font-weight: 700; color: #fff; text-align: center; transition: .2s; }
        .cross-sell-links a:hover { background: rgba(56,189,248,0.1); border-color: #38bdf8; color: #38bdf8; }

        .budget-confirm-box { margin-top: 25px; padding: 25px; border: 1px solid var(--line); border-radius: var(--radius); background: #fff; display: none; text-align: center; }
        .budget-confirm-title { font-size: 18px; font-weight: 900; margin-bottom: 8px; letter-spacing: -0.5px;}
        .budget-confirm-text { color: var(--muted); font-size: 13px; line-height: 1.55; margin-bottom: 20px; }
        .budget-confirm-actions { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
        .budget-btn { min-height: 52px; border-radius: 13px; padding: 10px 14px; font-size: 13px; font-weight: 800; border: 1.5px solid var(--line); cursor: pointer; transition: .2s; }
        .budget-no { background: #fff; color: #555; }
        .budget-no:hover { background: #f8f9fb; border-color: #d1d5db; }
        .budget-yes { background: #fff; color: #111; }
        .budget-yes:hover { border-color: #38bdf8; background: #f0f9ff; box-shadow: 0 0 0 3px rgba(56,189,248,.10); }

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
        <h1>❄️ İklimlendirme & BTU Motoru</h1>
        <p>Kişi sayısı, yalıtım ve cephe durumuna göre mekanınızın ihtiyacı olan doğru iklimlendirme (klima) kapasitesini bilimsel olarak hesaplayın.</p>
    </div>

    <div class="card">
        <div class="form-grid">
            <div class="form-group">
                <label>Oda / Mekan Alanı (m²)</label>
                <input type="number" id="area" class="form-control" placeholder="Örn: 25" value="25" min="5" step="1">
            </div>
            <div class="form-group">
                <label>Ortalama Bulunacak Kişi Sayısı</label>
                <input type="number" id="people" class="form-control" placeholder="Örn: 3" value="3" min="1" step="1">
            </div>
        </div>

        <div class="form-grid">
            <div class="form-group">
                <label>Cephe Durumu (Güneş Etkisi)</label>
                <select id="facade" class="form-control">
                    <option value="0.9">Kuzey Cephe (Az Güneş)</option>
                    <option value="1.0" selected>Doğu / Batı Cephe</option>
                    <option value="1.15">Güney Cephe (Çok Güneş / Cam Cephe)</option>
                </select>
            </div>
            <div class="form-group">
                <label>Yapı Yalıtım Durumu</label>
                <select id="insulation" class="form-control">
                    <option value="0.9">Yeni / Isı Yalıtımlı</option>
                    <option value="1.0" selected>Standart Yalıtım</option>
                    <option value="1.2">Yalıtımsız / Çatı Katı</option>
                </select>
            </div>
        </div>

        <button type="button" class="btn btn-calc" id="calculateBtn">Isı Yükünü ve Kapasiteyi Hesapla →</button>
    </div>

    <div class="result-box" id="resultBox">
        <div class="result-label">İKLİMLENDİRME ANALİZİ</div>
        <div class="result-price" id="recommendedDevice">0 BTU</div>
        
        <p style="font-size: 11px; color: #6b7280; margin-bottom:20px;">
            ⚠️ Bu sonuç, bölgesel ortalamalar ve yalıtım katsayıları baz alınarak hazırlanan mekanik mühendislik tavsiyesidir.
        </p>

        <div class="breakdown-grid">
            <div class="break-item">
                <span>Temel Alan Isı Yükü</span>
                <strong id="baseLoad">0 BTU</strong>
            </div>
            <div class="break-item">
                <span>İnsan (Kişi) Isı Yükü</span>
                <strong id="personLoad">+ 0 BTU</strong>
            </div>
            <div class="break-item highlight">
                <span>Mekanik Çarpanlar Sonrası</span>
                <strong id="totalLoad">0 BTU</strong>
            </div>
            <div class="break-item highlight">
                <span>Önerilen Cihaz Standardı</span>
                <strong id="deviceStandard">0 BTU/h</strong>
            </div>
        </div>

        <div class="ai-insight" id="aiInsight">
            <div class="ai-insight-icon">🤖</div>
            <div class="ai-insight-text">
                <strong>Mekanik Uzmanı Notu</strong>
                <p id="insightText">Hesaplamalar tamamlandı.</p>
            </div>
        </div>

        <div class="cross-sell">
            <div class="cross-sell-title">💡 Klimaları gizlemek ister misiniz?</div>
            <div class="cross-sell-text">Duvar tipi klimalar yerine, asma tavan içerisine gizlenmiş kanallı tip (VRF) sistemler mekana çok daha estetik bir görünüm katar.</div>
            <div class="cross-sell-links">
                <a href="/araclar/alcipan-hesaplayici">🏗️ Asma Tavan Maliyetini Hesapla</a>
            </div>
        </div>
    </div>

    <!-- ELEME KAPISI -->
    <div class="budget-confirm-box" id="budgetConfirmBox">
        <div class="budget-confirm-title">Mekanik tesisatınız için profesyonel projelendirme ister misiniz?</div>
        <div class="budget-confirm-text">
            Kainta İnşaat mühendisleri; doğru cihaz seçimi, elektrik altyapısı ve bakır borulama hatlarının projelendirilmesini anahtar teslim olarak yönetir.
        </div>
        <div class="budget-confirm-actions">
            <button type="button" class="budget-btn budget-yes" id="btnYes">✓ Evet, keşif ve proje desteği istiyorum.</button>
            <button type="button" class="budget-btn budget-no" id="btnNo">Sadece BTU değerini öğrenmek istedim.</button>
        </div>
    </div>

    <!-- ONAY MESAJI -->
    <div class="qualified-message" id="qualifiedMessage">
        <strong>Harika. Doğru iklimlendirme verimi artırır.</strong>
        <span>Aşağıdaki formu doldurduğunuzda Kainta İnşaat mekanik birimi sistem çözümleri için iletişime geçecektir.</span>
    </div>

    <!-- B2B LEAD FORM -->
    <div class="lead-box" id="leadBox">
        <h3>Profesyonel Mekanik ve HVAC Teklifi</h3>
        <p>Projeniz için doğru klima, havalandırma (VRF/Split) ve tesisat çözümlerini Kainta İnşaat güvencesiyle planlayın.</p>

        <form action="{{ route('lead.store') }}" method="POST">
            @csrf
            <input type="hidden" name="calculated_service" value="İklimlendirme ve Klima">
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
                <input type="text" name="location" class="form-control" required placeholder="Örn: İzmir, Çeşme">
            </div>

            <div class="form-group">
                <label>Saha Notunuz (Opsiyonel)</label>
                <textarea name="notes" class="form-control" style="height: 80px; padding-top:10px;" placeholder="Tesisat çekili mi? Duvar tipi mi, asma tavan içi kanallı tip mi düşünüyorsunuz?"></textarea>
            </div>

            <button type="submit" class="btn btn-submit">Bütçeniz uygunsa Kainta İnşaat'tan net teklif alın →</button>
        </form>
    </div>

</div>

<script>
document.getElementById('calculateBtn').addEventListener('click', function() {
    const area = parseFloat(document.getElementById('area').value) || 0;
    const people = parseInt(document.getElementById('people').value) || 0;
    const facade = parseFloat(document.getElementById('facade').value) || 1;
    const insulation = parseFloat(document.getElementById('insulation').value) || 1;
    
    if (area <= 0) {
        alert('Lütfen geçerli bir alan giriniz.');
        return;
    }

    // 1. Temel Yük (Türkiye ortalaması bölge katsayısı ~350 BTU/m2 alınmıştır)
    const baseLoad = area * 350;

    // 2. Kişi Yükü (Kişi başı ortalama 600 BTU)
    const personLoad = people * 600;

    // 3. Brüt Yük ve Çarpanlar
    let totalLoad = (baseLoad + personLoad) * facade * insulation;

    // 4. Standart Cihaz Önerisi Algoritması
    let recommended = 9000;
    if (totalLoad > 9000 && totalLoad <= 12500) recommended = 12000;
    else if (totalLoad > 12500 && totalLoad <= 19000) recommended = 18000;
    else if (totalLoad > 19000 && totalLoad <= 25000) recommended = 24000;
    else if (totalLoad > 25000 && totalLoad <= 38000) recommended = 36000;
    else if (totalLoad > 38000) recommended = 48000;

    // Ekrana Basma
    const formatBtu = (num) => new Intl.NumberFormat('tr-TR').format(Math.ceil(num));

    document.getElementById('recommendedDevice').innerText = formatBtu(recommended) + ' BTU';
    document.getElementById('baseLoad').innerText = formatBtu(baseLoad) + ' BTU';
    document.getElementById('personLoad').innerText = '+' + formatBtu(personLoad) + ' BTU';
    document.getElementById('totalLoad').innerText = formatBtu(totalLoad) + ' BTU';
    document.getElementById('deviceStandard').innerText = formatBtu(recommended) + ' BTU/h';

    // AI Insight Mantığı
    let insightStr = '';
    if (recommended >= 24000 && recommended < 48000) {
        insightStr = `Alanınızın ısı yükü oldukça yüksek. Tek bir büyük kapasiteli duvar tipi cihaz (Örn: 24.000 BTU) kullanmak yerine, konforu eşit dağıtmak için <strong>iki adet 12.000 BTU cihaz</strong> veya <strong>Salon Tipi</strong> klimaları değerlendirebilirsiniz.`;
    } else if (recommended >= 48000) {
        insightStr = `Hesaplanan ısı yükü standart split klimaların (duvar tipi) kapasitesini aşmaktadır. Bu alan için <strong>VRF (Merkezi İklimlendirme)</strong> sistemleri veya <strong>Kanallı / Kaset Tipi</strong> ticari cihazlar projelendirilmelidir. Mekanik ekibimizden destek almanızı öneririz.`;
    } else {
        insightStr = `Seçtiğiniz yalıtım ve cephe verilerine göre en ideal konforu ve düşük elektrik tüketimini <strong>${formatBtu(recommended)} BTU</strong> kapasiteli inverter bir cihaz sağlayacaktır. Cihazı doğrudan üzerinize üflemeyecek şekilde konumlandırınız.`;
    }
    document.getElementById('insightText').innerHTML = insightStr;

    document.getElementById('leadCalculatedData').value = `Alan: ${area}m2 | Yük: ${formatBtu(totalLoad)} BTU | Önerilen Cihaz: ${formatBtu(recommended)} BTU`;

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
            Bu aşamada iletişim bilgilerinizi almıyoruz. Mekanik proje veya anahtar teslim tadilat desteğine ihtiyaç duyduğunuzda tekrar bize ulaşabilirsiniz.
        </div>
    `;
    document.getElementById('leadBox').style.display = 'none';
    document.getElementById('qualifiedMessage').style.display = 'none';
});
</script>

    <x-site.contech-footer />
</body>
</html>
