<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Boya Maliyet & Sarfiyat Hesaplayıcı | WORKSİTEM</title>
    <link rel="canonical" href="https://worksitem.com/araclar/boya-hesaplayici">
    <meta name="description" content="Duvar metrajınıza göre gerekli boya sarfiyatını, uygulama miktarını ve yaklaşık anahtar teslim boya maliyetini hızlıca hesaplayın.">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #f5f6f8; --surface: #ffffff; --text: #101318; --muted: #6b7280;
            --line: #e4e7eb; --accent: #ffb800; --radius: 16px;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Inter', sans-serif; background: var(--bg); color: var(--text); line-height: 1.5; padding-bottom: 60px; }
        a { text-decoration: none; color: inherit; }
        
        /* HEADER */
        .header { background: #fff; border-bottom: 1px solid var(--line); padding: 15px 20px; position: sticky; top: 0; z-index: 10; display: flex; align-items: center; justify-content: space-between; }
        .back-btn { font-size: 14px; font-weight: 700; color: var(--muted); display: flex; align-items: center; gap: 5px; }
        .back-btn:hover { color: var(--text); }
        .brand img { height: 30px; }

        /* CONTAINER */
        .container { max-width: 800px; margin: 40px auto; padding: 0 20px; }
        .tool-header { margin-bottom: 30px; text-align: center; }
        .tool-header h1 { font-size: 28px; font-weight: 900; letter-spacing: -1px; margin-bottom: 10px; }
        .tool-header p { color: var(--muted); font-size: 15px; }

        /* CARD */
        .card { background: var(--surface); border: 1px solid var(--line); border-radius: var(--radius); padding: 30px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); margin-bottom: 30px; }
        
        /* FORM ELEMENTS */
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px; }
        .form-group { margin-bottom: 20px; }
        .form-group.full { grid-column: span 2; margin-bottom: 0; }
        .form-group label { display: block; font-size: 13px; font-weight: 800; margin-bottom: 8px; }
        .form-control { width: 100%; height: 50px; border: 1px solid var(--line); border-radius: 12px; padding: 0 15px; font-size: 15px; font-family: inherit; background: #fff; transition: .2s; outline: none; }
        .form-control:focus { border-color: var(--accent); box-shadow: 0 0 0 3px rgba(255,184,0,0.15); }
        
        /* RESULT SECTION */
        .result-box { background: #111; color: #fff; border-radius: var(--radius); padding: 30px; text-align: center; margin-top: 10px; display: none; }
        .result-box.active { display: block; animation: fadeIn 0.4s ease; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        .result-label { font-size: 12px; color: #a1a1aa; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; }
        .result-price { font-size: 38px; font-weight: 900; color: var(--accent); margin: 5px 0 15px; letter-spacing: -1px; }
        
        /* BREAKDOWN */
        .breakdown-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-top: 20px; text-align: left; }
        .break-item { background: rgba(255,255,255,0.05); padding: 15px; border-radius: 12px; border: 1px solid rgba(255,255,255,0.1); }
        .break-item span { display: block; font-size: 12px; color: #a1a1aa; margin-bottom: 3px; }
        .break-item strong { display: block; font-size: 18px; color: #fff; font-weight: 800; }
        .break-item.highlight { background: rgba(255,184,0,0.1); border-color: rgba(255,184,0,0.3); }
        .break-item.highlight strong { color: var(--accent); }

        /* CROSS-SELL (Sıradaki Adım) */
        .cross-sell { background: #1c1f26; border-radius: 12px; padding: 20px; margin-top: 20px; text-align: left; border: 1px solid rgba(255,255,255,0.1); }
        .cross-sell-title { font-size: 14px; font-weight: 800; color: #fff; margin-bottom: 5px; display: flex; align-items: center; gap: 8px; }
        .cross-sell-text { font-size: 12px; color: #a1a1aa; margin-bottom: 15px; }
        .cross-sell-links { display: flex; gap: 10px; }
        .cross-sell-links a { flex: 1; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); padding: 10px; border-radius: 8px; font-size: 12px; font-weight: 700; color: #fff; text-align: center; transition: .2s; }
        .cross-sell-links a:hover { background: rgba(255,184,0,0.1); border-color: var(--accent); color: var(--accent); }

        /* BUDGET QUALIFICATION GATE */
        .budget-confirm-box { margin-top: 25px; padding: 25px; border: 1px solid var(--line); border-radius: var(--radius); background: #fff; display: none; text-align: center; }
        .budget-confirm-title { font-size: 18px; font-weight: 900; margin-bottom: 8px; letter-spacing: -0.5px;}
        .budget-confirm-text { color: var(--muted); font-size: 13px; line-height: 1.55; margin-bottom: 20px; }
        .budget-confirm-actions { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
        .budget-btn { min-height: 52px; border-radius: 13px; padding: 10px 14px; font-size: 13px; font-weight: 800; border: 1.5px solid var(--line); cursor: pointer; transition: .2s; }
        .budget-no { background: #fff; color: #555; }
        .budget-no:hover { background: #f8f9fb; border-color: #d1d5db; }
        .budget-yes { background: #fff; color: #111; }
        .budget-yes:hover { border-color: var(--accent); background: #fffaf0; box-shadow: 0 0 0 3px rgba(255,184,0,.10); }

        /* QUALIFIED MESSAGE */
        .qualified-message { display: none; margin-top: 25px; padding: 20px; border-radius: 16px; background: #f0fdf4; border: 1px solid #bbf7d0; text-align: center;}
        .qualified-message strong { display: block; font-size: 15px; margin-bottom: 5px; color: #166534; }
        .qualified-message span { display: block; color: #15803d; font-size: 12px; line-height: 1.5; }

        /* LEAD FORM */
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
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body data-tool-slug="paint-calculator">

<x-site.contech-header />

<div class="container">
    <div class="tool-header">
        <h1>🎨 Boya Hesaplayıcı</h1>
        <p>Duvar metrajınıza göre net sarfiyatı ve anahtar teslim maliyeti çıkarın.</p>
    </div>

    <div class="card">
        <div class="form-group">
            <label>Boya Uygulanacak Alan (Zemin veya Toplam Yüzey m²)</label>
            <input type="number" id="area" class="form-control" placeholder="Örn: 120" value="120" min="10">
        </div>

        <div class="form-grid">
            <div class="form-group">
                <label>Tavan Boyası Dahil Mi?</label>
                <select id="includeCeiling" class="form-control">
                    <option value="yes" selected>Evet, Dahil Et</option>
                    <option value="no">Hayır, Sadece Duvarlar</option>
                </select>
            </div>

            <div class="form-group">
                <label>Kaç Kat Uygulanacak?</label>
                <select id="coats" class="form-control">
                    <option value="1">Tek Kat (Astar + Rötuş)</option>
                    <option value="2" selected>Çift Kat (Standart)</option>
                    <option value="3">Üç Kat (Koyu Renk Değişimi)</option>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label>Boya Kalitesi (DB Entegreli)</label>
            <select id="quality" class="form-control">
                <option value="standard" selected>Standart Sınıf</option>
                <option value="premium">Premium Sınıf (Silinebilir/Antibakteriyel)</option>
                <option value="luxury">Lüks Sınıf (İtalyan/Özel Doku)</option>
            </select>
        </div>

        <button type="button" class="btn btn-calc" id="calculateBtn">Maliyet ve Sarfiyatı Hesapla →</button>
    </div>

    <!-- SONUÇ EKRANI -->
    <div class="result-box" id="resultBox">
        <div class="result-label">TOPLAM UYGULAMA MALİYETİ</div>
        <div class="result-price" id="totalPrice">0 TL</div>
        
        <p style="font-size: 11px; color: #6b7280; margin-bottom:20px;">
            ⚠️ Bu değer Merkezi Fiyat Motoruna dayalı bir yaklaşık tahmindir. Nihai bütçe saha kontrolüyle kesinleştirilir.
        </p>

        <div class="breakdown-grid">
            <div class="break-item highlight">
                <span>Gerekli Boya Miktarı</span>
                <strong id="paintVolume">~0 Litre</strong>
            </div>
            <div class="break-item">
                <span>Toplam Boyanacak Yüzey</span>
                <strong id="surfaceArea">0 m²</strong>
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
            <div class="cross-sell-title">💡 Sıradaki Adım: Zeminler</div>
            <div class="cross-sell-text">Duvarları hesapladık. Peki odanızın zeminini yenilemek ister misiniz?</div>
            <div class="cross-sell-links">
                <a href="/araclar/parke-maliyet-hesaplayici">🪵 Parke Hesapla</a>
                <a href="/araclar/seramik-maliyet-hesaplayici">🧩 Seramik Hesapla</a>
            </div>
        </div>
    </div>

    <!-- ELEME KAPISI (FİLTRE) -->
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
        <h3>Boya ve Tadilat Teklifi Alın</h3>
        <p>Hesaplanan bu veriler ışığında Kainta İnşaat uzmanlarından yerinde keşif talep edin.</p>

        <form action="{{ route('lead.store') }}" method="POST">
            @csrf
            <input type="hidden" name="calculated_service" value="Boya Hesaplayıcı">
            <input type="hidden" name="calculated_area" id="leadCalculatedData" value="">
            
            <div class="form-group">
                <label>Adınız Soyadınız</label>
                <input type="text" name="name" class="form-control" required placeholder="Ad Soyad">
            </div>

            <div class="form-group">
                <label>Telefon Numaranız</label>
                <input type="tel" name="phone" class="form-control" required placeholder="05XX XXX XX XX">
            </div>

            <div class="form-group">
                <label>Projenin Yapılacağı Şehir/İlçe</label>
                <input type="text" name="location" class="form-control" required placeholder="Örn: İstanbul, Kadıköy">
            </div>

            <div class="form-group">
                <label>Proje Notunuz (Opsiyonel)</label>
                <textarea name="notes" class="form-control" style="height: 80px; padding-top:10px;" placeholder="Eklemek istediğiniz detaylar (örn: Tavanlarda alçı tamiratı var)"></textarea>
            </div>

            <button type="submit" class="btn btn-submit">Bütçeniz uygunsa Kainta İnşaat'tan net teklif alın →</button>
        </form>
    </div>

</div>

<script>
document.getElementById('calculateBtn').addEventListener('click', function() {
    const baseArea = parseFloat(document.getElementById('area').value) || 0;
    const includeCeiling = document.getElementById('includeCeiling').value === 'yes';
    const coats = parseInt(document.getElementById('coats').value) || 2;
    const quality = document.getElementById('quality').value;

    if (baseArea < 10) {
        alert('Lütfen en az 10 m² alan giriniz.');
        return;
    }

    // Yüzey Alanı Hesabı (Ortalama formül: Taban alanının ~2.5 katı duvardır)
    let wallArea = baseArea * 2.5; 
    let ceilingArea = includeCeiling ? baseArea : 0;
    let totalSurfaceArea = wallArea + ceilingArea;
    let totalPaintedArea = totalSurfaceArea * coats;

    // Boya Sarfiyatı (1 Litre boya ortalama 14 m² tek kat yüzey boyar)
    let paintVolumeLiters = Math.ceil(totalPaintedArea / 14);

    // Fiyatlandırma Çarpanları
    let materialPricePerLiter = 180; // Standart
    let laborPricePerM2 = 90; // Standart
    
    if (quality === 'premium') {
        materialPricePerLiter = 280;
        laborPricePerM2 = 130;
    } else if (quality === 'luxury') {
        materialPricePerLiter = 450;
        laborPricePerM2 = 220;
    }

    let materialTotal = paintVolumeLiters * materialPricePerLiter;
    let laborTotal = totalSurfaceArea * laborPricePerM2; // İşçilik kat sayısından ziyade yüzey alanından hesaplanır (2 kat standart kabul edilir)
    
    // Eğer 3 katsa işçiliği %30 artır
    if(coats === 3) laborTotal *= 1.3;

    let grandTotal = materialTotal + laborTotal;

    const money = new Intl.NumberFormat('tr-TR', { maximumFractionDigits: 0 });

    document.getElementById('totalPrice').innerText = money.format(grandTotal) + ' TL';
    document.getElementById('paintVolume').innerText = '~' + paintVolumeLiters + ' Litre';
    document.getElementById('surfaceArea').innerText = money.format(totalSurfaceArea) + ' m²';
    document.getElementById('materialCost').innerText = money.format(materialTotal) + ' TL';
    document.getElementById('laborCost').innerText = money.format(laborTotal) + ' TL';

    document.getElementById('leadCalculatedData').value = `Zemin: ${baseArea} m2 | Yüzey: ${totalSurfaceArea} m2 | Bütçe: ${money.format(grandTotal)} TL`;

    // UI State
    document.getElementById('resultBox').classList.add('active');
    document.getElementById('budgetConfirmBox').style.display = 'block';
    document.getElementById('leadBox').style.display = 'none';
    document.getElementById('leadBox').classList.remove('active');
    document.getElementById('qualifiedMessage').style.display = 'none';
    
    setTimeout(() => { document.getElementById('resultBox').scrollIntoView({ behavior: 'smooth', block: 'start' }); }, 150);
});

// ELEME KAPISI: EVET DİYENLER
document.getElementById('btnYes').addEventListener('click', function() {
    document.getElementById('budgetConfirmBox').style.display = 'none';
    document.getElementById('qualifiedMessage').style.display = 'block';
    document.getElementById('leadBox').style.display = 'block';
    document.getElementById('leadBox').classList.add('active');
    setTimeout(() => { document.getElementById('qualifiedMessage').scrollIntoView({ behavior: 'smooth', block: 'center' }); }, 100);
});

// ELEME KAPISI: HAYIR DİYENLER
document.getElementById('btnNo').addEventListener('click', function() {
    document.getElementById('budgetConfirmBox').innerHTML = `
        <div class="budget-confirm-title" style="color: #101318;">Sorun değil.</div>
        <div class="budget-confirm-text" style="margin-bottom: 0;">
            Bu aşamada iletişim bilgilerinizi almıyoruz. Boya ve tadilat bütçeniz netleştiğinde tekrar teklif isteyebilirsiniz.
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
