<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kablo Kesiti ve Gerilim Düşümü Motoru | WORKSİTEM</title>
    <link rel="canonical" href="https://worksitem.com/araclar/kablo-kesiti-hesabi">
    <meta name="description" content="Çekilen güce, mesafeye ve sistem gerilimine göre en güvenli kablo kesitini ve elektrik altyapı bütçenizi hesaplayın.">
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
        .break-item.warning { background: rgba(239, 68, 68, 0.1); border-color: rgba(239, 68, 68, 0.3); }
        .break-item.warning strong { color: #f87171; }

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
<body class="wc" data-tool-slug="kablo-calculator">

<x-site.contech-header />

<main class="wc-wrap">
    <x-calc.hero title="Kablo kesiti hesaplama ve gerilim düşümü" crumb="Kablo kesiti hesabı" :facts="['Akım ve gerilim düşümü kontrolü', 'Monofaze / trifaze']">
        Monofaze 220 V'ta 5,5 kW'lık bir yük 50 metre mesafeye taşınırken 25 A akım çeker; %3 gerilim düşümü sınırında 10 mm² bakır kablo gerekir (gerçekleşen düşüm yaklaşık %2). Gücü, mesafeyi, gerilimi ve yük tipini girin; önerilen kesiti ve yaklaşık kablo bütçesini görün.
    </x-calc.hero>

    <div class="wc-grid">
    <div class="card wc-panel">
        <div class="form-grid">
            <div class="form-group">
                <label>Çekilen Toplam Güç (kW)</label>
                <input type="number" id="power" class="form-control" placeholder="Örn: 5.5" value="5.5" min="0.1" step="0.1">
            </div>
            <div class="form-group">
                <label>Kablo Mesafesi (Metre)</label>
                <input type="number" id="distance" class="form-control" placeholder="Örn: 50" value="50" min="1" step="1">
            </div>
        </div>

        <div class="form-grid">
            <div class="form-group">
                <label>Sistem Gerilimi</label>
                <select id="voltage" class="form-control">
                    <option value="220" selected>Monofaze (220V - Ev Tipi)</option>
                    <option value="380">Trifaze (380V - Sanayi Tipi)</option>
                </select>
            </div>
            <div class="form-group">
                <label>Kablo Tipi & Çekim Yöntemi</label>
                <select id="installation" class="form-control">
                    <option value="{{ (float) data_get($pricing ?? [], 'kablo.siva_alti_m', 40) }}" selected>Sıva Altı (NYM / Boru İçi)</option>
                    <option value="{{ (float) data_get($pricing ?? [], 'kablo.siva_ustu_m', 60) }}">Sıva Üstü / Tavada (Halogen Free)</option>
                    <option value="{{ (float) data_get($pricing ?? [], 'kablo.yeralti_m', 90) }}">Yer Altı (NYY Zırhlı)</option>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label for="loadType">Yük Tipi (izin verilen gerilim düşümü)</label>
            <select id="loadType" class="form-control">
                <option value="3" selected>Priz, kuvvet ve motor hattı (%3)</option>
                <option value="1.5">Aydınlatma hattı (%1,5)</option>
            </select>
        </div>

        <div class="form-group">
            <label for="cosphi">Güç Faktörü (cos φ)</label>
            <select id="cosphi" class="form-control">
                <option value="1" selected>Rezistif yük: ısıtıcı, aydınlatma, priz (1,0)</option>
                <option value="0.9">Karma yük: konut / ofis genel (0,9)</option>
                <option value="0.8">Motorlu yük: pompa, kompresör, klima (0,8)</option>
            </select>
        </div>

        <button type="button" class="btn btn-calc" id="calculateBtn">Kablo kesitini ve bütçeyi hesapla</button>
        <div id="inputWarn" role="alert" style="display:none; color:#b91c1c; font-size:13px; font-weight:700; margin-top:10px;"></div>
    </div>

    <aside class="wc-aside">
    <div class="wc-empty" data-wc-empty>
        <strong>Sonuç burada görünecek</strong>
        Gücü, mesafeyi ve gerilimi girip hesaplayın: çekilen akım, gerilim düşümü, önerilen kablo kesiti ve bütçe.
    </div>
    <div class="result-box wc-result" data-wc-result id="resultBox">
        <div class="result-label">GÜVENLİ KABLO VE ALTYAPI BÜTÇESİ</div>
        <div class="result-price" id="totalPrice" data-wc-price>0 TL</div>
        
        <p style="font-size: 11px; color: #6b7280; margin-bottom:20px;">
            ⚠️ Bu değerler kablo bedeli, kroşe/boru/tava sarfiyatı ve çekim işçiliği dahil tahmini yaklaşık maliyettir.
        </p>

        <div class="breakdown-grid">
            <div class="break-item">
                <span>Çekilen Tahmini Akım</span>
                <strong id="currentAmps">0.0 Amper</strong>
            </div>
            <div class="break-item highlight" id="dropBox">
                <span>Gerilim Düşümü Oranı (%e)</span>
                <strong id="voltageDrop">%0.00</strong>
            </div>
            <div class="break-item highlight">
                <span>ÖNERİLEN KABLO KESİTİ</span>
                <strong id="cableSize">0 mm²</strong>
            </div>
            <div class="break-item">
                <span>Malzeme ve İşçilik Birim Fiyatı</span>
                <strong id="unitPrice">0 TL/m</strong>
            </div>
        </div>

        <div id="limitWarn" role="alert" style="display:none; background:rgba(239,68,68,0.15); border:1px solid rgba(239,68,68,0.5); color:#fecaca; border-radius:12px; padding:12px 15px; margin-top:15px; text-align:left; font-size:13px; line-height:1.5;"></div>

        <div class="ai-insight" id="aiInsight">
            <div class="ai-insight-icon">💡</div>
            <div class="ai-insight-text">
                <strong>Elektrik Mühendisi Notu</strong>
                <p id="insightText">Hesaplamalar yapılıyor...</p>
            </div>
        </div>

        <div class="cross-sell">
            <div class="cross-sell-title">💡 Yeni Tesisat Çekilmişken Akıllı Ev?</div>
            <div class="cross-sell-text">Elektrik altyapınız yenilenirken veya sıfırdan kurulurken, klasik şalt malzemeler yerine akıllı otomasyon sistemlerine geçiş yapmayı düşündünüz mü?</div>
        </div>
    </div>
    </aside>
    </div>{{-- /wc-grid --}}

    <div class="wc-below">

    <div class="budget-confirm-box" id="budgetConfirmBox">
        <div class="budget-confirm-title">Elektrik taahhüt işleriniz için destek ister misiniz?</div>
        <div class="budget-confirm-text">
            Kainta İnşaat Uzmanları, aydınlatma otomasyonundan ana pano kurulumlarına kadar tüm elektrik projelerinizi güvenlik standartlarına uygun olarak anahtar teslim yönetir.
        </div>
        <div class="budget-confirm-actions">
            <button type="button" class="budget-btn budget-yes" id="btnYes">✓ Evet, keşif ve elektrik projesi istiyorum.</button>
            <button type="button" class="budget-btn budget-no" id="btnNo">Sadece kablo kesitine baktım.</button>
        </div>
    </div>

    <div class="qualified-message" id="qualifiedMessage">
        <strong>Son bir adım kaldı.</strong>
        <span>Aşağıdaki formu doldurduğunuzda teknik ekibimiz elektrik projelendirmesi için sizinle iletişime geçecektir.</span>
    </div>

    <div class="lead-box" id="leadBox">
        <h3>Profesyonel Elektrik & Otomasyon Teklifi</h3>
        <p>Hesapladığınız altyapı verilerine göre Kainta İnşaat Uzmanlarından profesyonel destek alın.</p>

        <form action="{{ route('lead.store') }}" method="POST">
            @csrf
            <input type="hidden" name="calculated_service" value="Elektrik ve Kablo Hesabı">
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
                <input type="text" name="location" class="form-control" required placeholder="Örn: Kocaeli, Gebze">
            </div>

            <div class="form-group">
                <label>Saha Notunuz (Opsiyonel)</label>
                <textarea name="notes" class="form-control" style="height: 80px; padding-top:10px;" placeholder="Yeni inşaat mı, yoksa fabrika içi makine hattı mı çekilecek?"></textarea>
            </div>

            <x-lead-consent-fields />
            <button type="submit" class="btn btn-submit">Bütçeniz uygunsa Kainta İnşaat'tan net teklif alın →</button>
        </form>
    </div>

    </div>{{-- /wc-below --}}
</main>

<script>
document.getElementById('calculateBtn').addEventListener('click', function() {
    const p = parseFloat(document.getElementById('power').value) || 0;
    const l = parseFloat(document.getElementById('distance').value) || 0;
    const vType = parseInt(document.getElementById('voltage').value);
    const installMultiplier = parseFloat(document.getElementById('installation').value);
    
    const inputWarn = document.getElementById('inputWarn');
    // v119: alert yerine alanın altında uyarı; negatif/0/boş girdide hesap yapılmaz
    if (!(p > 0) || !(l > 0)) {
        inputWarn.textContent = 'Güç ve mesafe için pozitif bir değer girin.';
        inputWarn.style.display = 'block';
        document.getElementById('resultBox').classList.remove('active');
        document.getElementById('budgetConfirmBox').style.display = 'none';
        return;
    }
    inputWarn.style.display = 'none';
    const cosPhi = parseFloat((document.getElementById('cosphi') || {}).value) || 1;

    // DÜZELTME (12.10.2026): güç kW girilir; formüller watt ister (eski sürümde 1000 kat düşük gerilim düşümü çıkıyordu).
    const pW = p * 1000;
    const limitE = parseFloat((document.getElementById('loadType') || {}).value) || 3; // %: aydınlatma 1,5 · kuvvet 3
    const k = 56; // bakır iletkenlik (m/Ω·mm²)
    let current, s_voltage;

    if (vType === 220) {
        current = pW / (220 * cosPhi);                             // I = P / (U·cos φ)
        s_voltage = (200 * pW * l) / (k * limitE * 220 * 220);     // S = 200·P·L / (k·%e·U²)
    } else {
        current = pW / (1.732 * 380 * cosPhi);                     // I = P / (√3·U·cos φ)
        s_voltage = (100 * pW * l) / (k * limitE * 380 * 380);     // S = 100·P·L / (k·%e·U²)
    }

    // Akım taşıma kapasitesine göre en küçük kesit — sıva altı boru içi bakır, ihtiyatlı değerler (ön kontrol amaçlı)
    const ampacity = [[1.5, 13], [2.5, 20], [4, 25], [6, 32], [10, 40], [16, 63], [25, 80], [35, 100], [50, 125], [70, 160], [95, 200], [120, 225], [150, 250], [240, 330]];
    const fit = ampacity.find(([, a]) => a >= current);
    const s_current = fit ? fit[0] : 240;

    const sizes = ampacity.map(([s]) => s);
    const required_s = Math.max(s_current, s_voltage);
    let chosen_s = sizes.find(s => s >= required_s);
    if (!chosen_s) chosen_s = 240;

    const actual_e = vType === 220
        ? (200 * pW * l) / (k * chosen_s * 220 * 220)
        : (100 * pW * l) / (k * chosen_s * 380 * 380);

    // Bütçe Hesaplama (Birim fiyat kablo kalınlaştıkça ve tesisat tipine göre artar)
    // Örn: Base (İşçilik) + (Kesit * Çarpan)
    const unitPrice = installMultiplier + (chosen_s * {{ (float) data_get($pricing ?? [], 'kablo.bakir_mm2_m', 15) }});
    const grandTotal = unitPrice * l;

    const money = new Intl.NumberFormat('tr-TR', { maximumFractionDigits: 0 });
    const nf = (x, d) => x.toLocaleString('tr-TR', { minimumFractionDigits: d, maximumFractionDigits: d });

    // v119: sınır aşımı — en büyük kesit (240 mm²) akımı taşımıyor ya da gerilim düşümü sınırı aşılıyor
    const overCurrent = current > 330;
    const overDrop = actual_e > limitE;
    const limitWarn = document.getElementById('limitWarn');
    const lw = [];
    if (overCurrent) lw.push(`Çekilen akım (${nf(current, 1)} A) tek kablonun taşıyabileceği değeri (240 mm² için yaklaşık 330 A) aşıyor.`);
    if (overDrop) lw.push(`Gerilim düşümü %${nf(actual_e, 2)}; izin verilen %${nf(limitE, 1)} sınırını aşıyor.`);
    if (vType === 220 && p > 10) lw.push('Monofaze hatta 10 kW üzeri yük için trifaze besleme değerlendirilmelidir.');
    if (overCurrent || overDrop) lw.push('Paralel kablo, trifaze besleme ya da daha kısa hat gerekir; aşağıdaki kesit ve bütçe bu hat için geçerli değildir.');
    limitWarn.innerHTML = lw.length ? '<strong>⚠️ Sınır aşımı</strong><br>' + lw.join('<br>') : '';
    limitWarn.style.display = lw.length ? 'block' : 'none';

    document.getElementById('totalPrice').innerText = (overCurrent || overDrop) ? 'Hesaplanamadı' : money.format(grandTotal) + ' TL';
    document.getElementById('currentAmps').innerText = nf(current, 1) + ' Amper';
    document.getElementById('voltageDrop').innerText = '%' + nf(actual_e, 2);
    document.getElementById('dropBox').classList.toggle('warning', overDrop);
    document.getElementById('cableSize').innerText = chosen_s + (chosen_s >= 240 ? ' mm² veya üzeri' : ' mm²');
    document.getElementById('unitPrice').innerText = money.format(unitPrice) + ' TL/m';

    // Uzman notu / Mühendislik Notu
    const insightText = document.getElementById('insightText');
    if (overCurrent || overDrop) {
        insightText.innerHTML = `Bu güç ve mesafe tek bir bakır kabloyla karşılanamıyor. Hattı yetkili bir elektrik mühendisine projelendirin.`;
    } else if (s_voltage > s_current) {
        insightText.innerHTML = `Dikkat: Girdiğiniz hat mesafesi (${l}m) çok uzun olduğu için, kablo kesiti cihazın çektiği akımdan ziyade <strong>gerilim düşümü (voltaj kaybı)</strong> sınırlarına göre büyütülerek <strong>${chosen_s} mm²</strong> seçilmiştir. Aksi halde cihazlarınız düşük voltajdan dolayı zarar görebilirdi.`;
    } else {
        insightText.innerHTML = `Elektrik İç Tesisleri Yönetmeliğine göre aydınlatma hatlarında %1,5, kuvvet/priz hatlarında %3 gerilim düşümüne izin verilir. Bu hesapta kesiti belirleyen, hattın çektiği <strong>${current.toFixed(1)} Amper</strong> akımdır; gerilim düşümü sınırın altındadır. Sigorta değeri ve kesin kesit, yetkili elektrik mühendisince onaylanmalıdır.`;
    }

    document.getElementById('leadCalculatedData').value = `Güç: ${p}kW | Mesafe: ${l}m | Gerilim: ${vType}V | cos φ: ${cosPhi} | Önerilen Kesit: ${chosen_s}mm2 | Bütçe: ${money.format(grandTotal)} TL`;

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
            Bu aşamada iletişim bilgilerinizi almıyoruz. Elektrik taahhüt ve otomasyon ihtiyacınız olduğunda tekrar bize ulaşabilirsiniz.
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
