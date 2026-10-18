<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mutfak Tadilat Maliyeti Hesaplayıcı | WORKSİTEM</title>
    <link rel="canonical" href="https://worksitem.com/araclar/mutfak-maliyet-hesaplayici">
    <meta name="description" content="Mutfak dolabı, tezgah, zemin kırım ve kaplama kalemlerini birlikte değerlendirerek yaklaşık mutfak tadilat bütçenizi hesaplayın.">
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
<body class="wc" data-tool-slug="mutfak-calculator">

<x-site.contech-header />

<main class="wc-wrap">
    <x-calc.hero title="Mutfak tadilatı maliyeti hesaplama" crumb="Mutfak tadilatı" :facts="['Dolap, tezgah, zemin, tesisat', 'Kalem kalem döküm']">
        10 m² zeminli, 4 metre MDFLam dolaplı ve granit tezgahlı bir mutfağın zemin yenileme dahil anahtar teslim tadilatı yaklaşık 73.500 TL tutar (olası aralık 62.500–88.200 TL). Ölçüleri ve malzeme kalitesini seçin; dolap, tezgah, zemin ve tesisat kalemlerini ayrı ayrı görün.
    </x-calc.hero>

    <div class="wc-grid">
    <div class="card wc-panel">
        <div class="form-grid">
            <div class="form-group">
                <label>Zemin Alanı (m²)</label>
                <input type="number" id="floorArea" class="form-control" placeholder="Örn: 10" value="10" min="2" step="1">
            </div>
            <div class="form-group">
                <label>Mutfak Dolabı Uzunluğu (m)</label>
                <input type="number" id="cabinetLength" class="form-control" placeholder="Alt ve üst dolap uzunluğu (Örn: 4)" value="4" min="1" step="0.5">
            </div>
        </div>

        <div class="form-group">
            <label>Zemin Durumu</label>
            <select id="floorCondition" class="form-control">
                <option value="1350" selected>Mevcut Zemin Kırılacak ve Yeni Seramik Yapılacak</option>
                <option value="0">Kırım Yok, Sadece Dolap Değişecek</option>
            </select>
        </div>

        <div class="form-grid">
            <div class="form-group">
                <label>Dolap Kalitesi (Gövde + Kapak)</label>
                <select id="cabinetQuality" class="form-control">
                    <option value="4500">Suntalam / Ekonomik</option>
                    <option value="7500" selected>MDFLam / Membran Kapak</option>
                    <option value="12000">Akrilik / Lake Boyalı Premium</option>
                </select>
            </div>
            <div class="form-group">
                <label>Tezgah Malzemesi</label>
                <select id="counterTop" class="form-control">
                    <option value="2000">Laminat Tezgah</option>
                    <option value="5500" selected>Granit Tezgah</option>
                    <option value="7500">Çimstone / Kuvars Porselen</option>
                </select>
            </div>
        </div>

        <button type="button" class="btn btn-calc" id="calculateBtn">Tadilat Bütçesini Hesapla →</button>
    </div>

    <aside class="wc-aside">
    <div class="wc-empty" data-wc-empty>
        <strong>Sonuç burada görünecek</strong>
        Zemin alanını, dolap uzunluğunu ve malzemeleri seçip hesaplayın: toplam bütçe, olası aralık ve kalem kalem döküm.
    </div>
    <div class="result-box wc-result" data-wc-result id="resultBox">
        <div class="result-label">ANAHTAR TESLİM BÜTÇE ANALİZİ</div>
        <div class="result-price" id="totalPrice" data-wc-price>0 TL</div>
        
        <p style="font-size: 11px; color: #6b7280; margin-bottom:20px;">
            ⚠️ Bu değerler montaj, söküm, tesisat revizyonları dahil yaklaşık maliyettir. Kesin fiyat için yerinde rölöve ve tasarım gerekir.
        </p>

        <div class="breakdown-grid">
            <div class="break-item">
                <span>Zemin Seramik & Kırım</span>
                <strong id="floorCost">0 TL</strong>
            </div>
            <div class="break-item">
                <span>Mutfak Dolabı Üretim & Montaj</span>
                <strong id="cabinetCost">0 TL</strong>
            </div>
            <div class="break-item">
                <span>Tezgah Malzeme & Montaj</span>
                <strong id="counterCost">0 TL</strong>
            </div>
            <div class="break-item highlight">
                <span>Mekanik & Elektrik Tesisat Revizyonu</span>
                <strong id="plumbingCost">0 TL</strong>
            </div>
        </div>

        <div class="cross-sell">
            <div class="cross-sell-title">💡 Mutfaktan Sonra Banyo?</div>
            <div class="cross-sell-text">Islak hacim tadilatlarına başlamışken banyonuzu da yenilemek ister misiniz? Seramik, vitrifiye ve dolap maliyetinizi hesaplayın.</div>
            <div class="cross-sell-links">
                <a href="/araclar/banyo-maliyet-hesaplayici">🚿 Banyo Maliyeti Hesapla</a>
            </div>
        </div>
    </div>
    </aside>
    </div>{{-- /wc-grid --}}

    <div class="wc-below">

    {{-- ELEME KAPISI --}}
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

    {{-- ONAY MESAJI --}}
    <div class="qualified-message" id="qualifiedMessage">
        <strong>Projeniz Kainta İnşaat değerlendirmesine uygun görünüyor.</strong>
        <span>Aşağıdaki formu doldurduğunuzda iç mimari tasarım ve teklif süreci için sizinle iletişime geçilecektir.</span>
    </div>

    {{-- B2B LEAD FORM --}}
    <div class="lead-box" id="leadBox">
        <h3>İç Mimari ve Tadilat Teklifi Alın</h3>
        <p>Hesaplanan bu veriler ışığında Kainta İnşaat uzmanlarından yerinde rölöve ve anahtar teslim mutfak teklifi talep edin.</p>

        <form action="{{ route('lead.store') }}" method="POST">
            @csrf
            <input type="hidden" name="calculated_service" value="Mutfak Tadilatı">
            <input type="hidden" name="calculated_area" id="leadCalculatedData" value="">
            
            <div class="form-group">
                <label>Adınız Soyadınız / Firma Ünvanı</label>
                <input type="text" name="name" class="form-control" required placeholder="Adınız Soyadınız">
            </div>

            <div class="form-group">
                <label>Telefon Numaranız</label>
                <input type="tel" name="phone" class="form-control" required placeholder="05XX XXX XX XX">
            </div>

            <div class="form-group">
                <label>Projenin Yapılacağı Şehir/İlçe</label>
                <input type="text" name="location" class="form-control" required placeholder="Örn: İstanbul, Üsküdar">
            </div>

            <div class="form-group">
                <label>Proje Notunuz (Opsiyonel)</label>
                <textarea name="notes" class="form-control" style="height: 80px; padding-top:10px;" placeholder="Beyaz eşyalar ankastre mi olacak? Ada mutfak talebi var mı?"></textarea>
            </div>

            <x-lead-consent-fields />
            <button type="submit" class="btn btn-submit">Bütçeniz uygunsa Kainta İnşaat'tan net teklif alın →</button>
        </form>
    </div>

    </div>{{-- /wc-below --}}
</main>

<script>
document.getElementById('calculateBtn').addEventListener('click', function() {
    // Parsing values safely to prevent NaN errors
    const area = parseFloat(document.getElementById('floorArea').value) || 0;
    const cabLength = parseFloat(document.getElementById('cabinetLength').value) || 0;
    const floorPricePerM2 = parseFloat(document.getElementById('floorCondition').value) || 0;
    const cabPricePerMt = parseFloat(document.getElementById('cabinetQuality').value) || 0;
    const counterPricePerMt = parseFloat(document.getElementById('counterTop').value) || 0;
    
    if (cabLength <= 0 || area <= 0) {
        alert('Lütfen geçerli ölçüler giriniz.');
        return;
    }

    // 1. Zemin Maliyeti (Kırım, hafriyat atımı, şap ve seramik yapımı ortalama maliyeti)
    const floorCost = area * floorPricePerM2;

    // 2. Dolap Maliyeti
    const cabinetCost = cabLength * cabPricePerMt;

    // 3. Tezgah Maliyeti (Tezgah uzunluğu dolapla ortalama aynı hesaplanır)
    const counterCost = cabLength * counterPricePerMt;

    // 4. Tesisat ve Montaj (Sabit 5000 TL + Metretül Başına 750 TL Elektrik/Su Revizyonu)
    const plumbingCost = 5000 + (cabLength * 750);

    const grandTotal = floorCost + cabinetCost + counterCost + plumbingCost;

    const money = new Intl.NumberFormat('tr-TR', { maximumFractionDigits: 0 });

    // Güncellemeler
    document.getElementById('totalPrice').innerText = money.format(grandTotal) + ' TL';
    document.getElementById('floorCost').innerText = money.format(floorCost) + ' TL';
    document.getElementById('cabinetCost').innerText = money.format(cabinetCost) + ' TL';
    document.getElementById('counterCost').innerText = money.format(counterCost) + ' TL';
    document.getElementById('plumbingCost').innerText = money.format(plumbingCost) + ' TL';

    document.getElementById('leadCalculatedData').value = `Dolap: ${cabLength} mt | Zemin: ${area} m2 | Tesisat Dahil Bütçe: ${money.format(grandTotal)} TL`;

    // Arayüz Tetiklemeleri
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

    <x-tools.tool-guide slug="mutfak-maliyet-hesaplayici" />

<x-calc.kit />
<x-site.contech-footer />
    <script src="/js/tool-events.js" defer></script>
</body>
</html>
