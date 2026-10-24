<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kat Karşılığı İnşaat Fizibilite Motoru | WORKSİTEM</title>
    <link rel="canonical" href="https://worksitem.com/araclar/kat-karsiligi-analiz">
    <meta name="description" content="Arsa sahibi ile müteahhit arasındaki paylaşıma göre inşaat kârlılığını, risk oranını ve net geliri simüle edin.">
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
        .ai-insight.danger { background: rgba(239, 68, 68, 0.1); border-color: rgba(239, 68, 68, 0.3); }
        .ai-insight.success { background: rgba(74, 222, 128, 0.1); border-color: rgba(74, 222, 128, 0.3); }
        .ai-insight-icon { font-size: 24px; }
        .ai-insight-text strong { color: var(--accent); display: block; font-size: 14px; margin-bottom: 4px; }
        .ai-insight.danger .ai-insight-text strong { color: #f87171; }
        .ai-insight.success .ai-insight-text strong { color: #4ade80; }
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

        .wc-warn { color:#b91c1c; font-size:13px; font-weight:700; margin-top:10px; }
        @media(max-width: 600px) {
            .form-grid, .breakdown-grid, .budget-confirm-actions { grid-template-columns: 1fr; }
            .card, .result-box, .lead-box, .budget-confirm-box { padding: 20px; }
            .result-price { font-size: 32px; }
            .cross-sell-links { flex-direction: column; }
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
<body class="wc" data-tool-slug="kat-karsiligi">

<x-site.contech-header />

<main class="wc-wrap">
    <x-calc.hero title="Kat karşılığı fizibilite hesaplama" crumb="Kat karşılığı">
        Arsa sahibi ile müteahhit arasındaki paylaşıma göre projenin toplam maliyetini, satış gelirini ve net kârlılığını (risk oranını) simüle edin.
    </x-calc.hero>

    <div class="wc-grid">
    <div class="card wc-panel">
        <div class="form-grid">
            <div class="form-group">
                <label>Arsa Alanı (Net m²)</label>
                <input type="number" id="area" class="form-control" placeholder="Örn: 1000" value="1000" min="100" step="50">
            </div>
            <div class="form-group">
                <label>KAKS / Emsal Değeri</label>
                <input type="number" id="kaks" class="form-control" placeholder="Örn: 1.5" value="1.5" min="0.1" step="0.1">
            </div>
        </div>

        <div class="form-group">
            <label>Arsa Sahibi Payı (%)</label>
            <input type="number" id="ownerShare" class="form-control" placeholder="Örn: 50" value="50" min="10" max="90" step="1">
            <p style="font-size: 11px; color: #6b7280; margin-top: 5px;">Kalan % pay müteahhidin satışa sunacağı net inşaat payı olacaktır.</p>
        </div>

@php
    // Maliyet kaynağı tahmini-bina-maliyeti ile aynı: PriceService::buildingClass (resmî ÇŞB 2026) × building.market_factor
    $kkResmi = [];
    foreach (['3B', '4A'] as $kkC) {
        $kkV = null;
        try {
            $kkR = app(\App\Services\PriceService::class)->buildingClass($kkC, '2026');
            $kkV = is_array($kkR) && ($kkR['price'] ?? 0) > 0 ? (float) $kkR['price'] : null;
        } catch (\Throwable) {
        }
        // Yedek: ÇŞB 2026 tebliği (RG 03.02.2026, 33157) — config/worksitem_mevzuat_2026.php
        $kkResmi[$kkC] = $kkV ?? (float) config('worksitem_mevzuat_2026.yapi_sinif.'.$kkC);
    }
    $kkFactor = 1.6;
    try {
        $kkF = \Illuminate\Support\Facades\Schema::hasTable('pricing_settings')
            ? \Illuminate\Support\Facades\DB::table('pricing_settings')->where('key', 'building.market_factor')->value('value') : null;
        if (is_numeric($kkF) && (float) $kkF > 0) $kkFactor = (float) $kkF;
    } catch (\Throwable) {
    }
    $kkNf = fn ($v) => number_format($v, 0, ',', '.');
    $kkPiyasa3B = round($kkResmi['3B'] * $kkFactor);
@endphp
        <div class="form-grid">
            <div class="form-group">
                <label>İnşaat Maliyeti Varsayımı</label>
                <select id="costPreset" class="form-control">
                    <option value="{{ $kkPiyasa3B }}" selected>3B konut, piyasa tahmini (~{{ $kkNf($kkPiyasa3B) }} TL/m²)</option>
                    <option value="{{ round($kkResmi['4A'] * $kkFactor) }}">4A lüks konut, piyasa tahmini (~{{ $kkNf(round($kkResmi['4A'] * $kkFactor)) }} TL/m²)</option>
                    <option value="{{ round($kkResmi['3B']) }}">3B resmî ÇŞB 2026 değeri ({{ $kkNf($kkResmi['3B']) }} TL/m²)</option>
                    <option value="{{ round($kkResmi['4A']) }}">4A resmî ÇŞB 2026 değeri ({{ $kkNf($kkResmi['4A']) }} TL/m²)</option>
                </select>
            </div>
            <div class="form-group">
                <label>İnşaat Maliyeti (TL/m², brüt)</label>
                <input type="number" id="costPerM2" class="form-control" value="{{ $kkPiyasa3B }}" min="0" step="100">
            </div>
        </div>
        <p style="font-size: 11px; color: #6b7280; margin: -10px 0 20px;">Piyasa tahmini = ÇŞB 2026 yapı sınıfı birim maliyeti × {{ str_replace('.', ',', (string) $kkFactor) }} piyasa katsayısı (Bina maliyeti aracıyla aynı kaynak). Resmî değer KDV hariçtir ve harç/proje bedeli hesabı içindir; gerçek yapım maliyeti genellikle daha yüksektir.</p>

        <div class="form-grid">
            <div class="form-group">
                <label>Bölge Satış Fiyatı (TL/m²)</label>
                <input type="number" id="salePrice" class="form-control" placeholder="Örn: 45000" value="45000" min="0" step="1000">
            </div>
            <div class="form-group">
                <label>Emsal Harici Alan Oranı (%)</label>
                <select id="exRate" class="form-control">
                    <option value="0">%0</option>
                    <option value="10">%10</option>
                    <option value="20" selected>%20</option>
                    <option value="30">%30 (üst sınır)</option>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label>Ek Giderler (% maliyete)</label>
            <input type="number" id="extraRate" class="form-control" value="10" min="0" max="100" step="1">
            <p style="font-size: 11px; color: #6b7280; margin-top: 5px;">Proje, ruhsat ve harçlar, arsa sahibine kira yardımı, finansman ve satış giderleri gibi kalemler için. KDV ve vergiler dahil değildir.</p>
        </div>

        <button type="button" class="btn btn-calc" id="calculateBtn">Müteahhit Kârını ve Riski Hesapla →</button>
        <p class="wc-warn" id="formErr" role="alert" hidden></p>
    </div>

    <aside class="wc-aside">
    <div class="wc-empty" data-wc-empty>
        <strong>Sonuç burada görünecek</strong>
        Bilgileri girip hesaplayın; sonuç, döküm ve yaklaşık bütçe burada görünür.
    </div>
    <div class="result-box wc-result" data-wc-result id="resultBox">
        <div class="result-label">MÜTEAHHİT NET KÂR BEKLENTİSİ</div>
        <div class="result-price" id="netProfit" data-wc-price>0 TL</div>
        
        <p style="font-size: 11px; color: #6b7280; margin-bottom:20px;">
            Müteahhit brüt alanın tamamını inşa eder, ancak yalnız kendi payına düşen satılabilir alanı (emsale dahil alan × pay) satar. Emsal harici alanlar (merdiven, asansör, şaft) maliyete girer, satışa girmez.
        </p>

        <div class="breakdown-grid">
            <div class="break-item">
                <span>Toplam Brüt İnşaat Alanı</span>
                <strong id="grossArea">0 m²</strong>
            </div>
            <div class="break-item highlight">
                <span>Müteahhit Payı (Satılabilir, emsale dahil)</span>
                <strong id="contractorArea">0 m²</strong>
            </div>
            <div class="break-item">
                <span>Toplam Maliyet (ek giderler dahil)</span>
                <strong id="totalCost">0 TL</strong>
            </div>
            <div class="break-item highlight">
                <span>Beklenen Satış Geliri</span>
                <strong id="totalRevenue">0 TL</strong>
            </div>
        </div>

        <div class="ai-insight" id="aiInsight">
            <div class="ai-insight-icon" id="insightIcon">💡</div>
            <div class="ai-insight-text">
                <strong id="insightTitle">Yatırım Risk Analizi</strong>
                <p id="insightText">Hesaplanıyor...</p>
            </div>
        </div>

        <div class="cross-sell">
            <div class="cross-sell-title">💡 Karar vermeden önce emsal detaylarını gördünüz mü?</div>
            <div class="cross-sell-text">Projeye girmeden önce TAKS taban oturumunu ve mimari sınırlarınızı netleştirin.</div>
            <div class="cross-sell-links">
                <a href="/araclar/taks-kaks-hesaplayici">🏢 İmar & TAKS/KAKS Analizi</a>
            </div>
        </div>
    </div>
    </aside>
    </div>{{-- /wc-grid --}}

    <div class="wc-below">

    {{-- ELEME KAPISI --}}
    <div class="budget-confirm-box" id="budgetConfirmBox">
        <div class="budget-confirm-title">Bu projeyi hayata geçirmek için teklif ister misiniz?</div>
        <div class="budget-confirm-text">
            Arsa sahipleri ile anlaşmaya vardıysanız, Kainta İnşaat olarak mimari projelendirmeden anahtar teslim kaba/ince inşaata kadar tüm aşamalarda taşeronluk yapıyoruz.
        </div>
        <div class="budget-confirm-actions">
            <button type="button" class="budget-btn budget-yes" id="btnYes">✓ Evet, proje taahhüt teklifi istiyorum.</button>
            <button type="button" class="budget-btn budget-no" id="btnNo">Sadece kârlılık simülasyonu yaptım.</button>
        </div>
    </div>

    {{-- ONAY MESAJI --}}
    <div class="qualified-message" id="qualifiedMessage">
        <strong>Son bir adım kaldı.</strong>
        <span>Aşağıdaki formu doldurduğunuzda teknik ofisimiz proje teklifleri için sizinle iletişime geçecektir.</span>
    </div>

    {{-- B2B LEAD FORM --}}
    <div class="lead-box" id="leadBox">
        <h3>Profesyonel Taahhüt ve Proje Teklifi</h3>
        <p>Arsanızın mimari fizibilitesi, kaba ve ince inşaat maliyetleri için Kainta İnşaat'tan profesyonel destek alın.</p>

        <form action="{{ route('lead.store') }}" method="POST">
            @csrf
            <input type="hidden" name="calculated_service" value="Kat Karşılığı Fizibilite">
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
                <input type="text" name="location" class="form-control" required placeholder="Örn: İzmir, Karşıyaka">
            </div>

            <div class="form-group">
                <label>Proje Durumu (Opsiyonel)</label>
                <textarea name="notes" class="form-control" style="height: 80px; padding-top:10px;" placeholder="Arsa sahibi ile sözleşme imzalandı mı? Mimari proje çizildi mi?"></textarea>
            </div>

            <x-lead-consent-fields />
            <button type="submit" class="btn btn-submit">Kainta İnşaat'tan Müteahhitlik Teklifi Al →</button>
        </form>
    </div>

    </div>{{-- /wc-below --}}
</main>

<script>
document.getElementById('calculateBtn').addEventListener('click', function() {
    const num = id => { const v = document.getElementById(id).value; return v === '' ? NaN : parseFloat(v); };
    const area = num('area'), kaks = num('kaks'), ownerShare = num('ownerShare');
    const costPerM2 = num('costPerM2'), salePrice = num('salePrice'), extraRate = num('extraRate');
    const exRate = (parseFloat(document.getElementById('exRate').value) || 0) / 100;
    const formErr = document.getElementById('formErr');
    const fail = msg => { formErr.textContent = msg; formErr.hidden = false; document.getElementById('resultBox').classList.remove('active'); };
    formErr.hidden = true;

    if (!(area > 0) || !(kaks > 0) || !(costPerM2 > 0) || !(salePrice > 0)) return fail('Arsa alanı, emsal, maliyet ve satış fiyatı için pozitif bir değer girin.');
    if (!(ownerShare >= 0) || ownerShare >= 100) return fail('Arsa sahibi payı 0 ile 99 arasında olmalı.');
    if (!(extraRate >= 0) || extraRate > 100) return fail('Ek gider oranı 0 ile 100 arasında olmalı.');

    // 1. Emsale dahil alan (satılabilir alanın üst sınırı) ve brüt inşaat alanı
    //    PAİY: emsal harici alanlar en çok %30; oran kullanıcı seçimi, satışa girmez
    const emsalArea = area * kaks;
    const grossArea = emsalArea * (1 + exRate);

    // 2. Müteahhit satılabilir payı: yalnız emsale dahil alan üzerinden
    const contractorPercentage = (100 - ownerShare) / 100;
    const contractorArea = emsalArea * contractorPercentage;

    // 3. Maliyet: brüt alanın tamamı × birim maliyet + ek giderler
    const buildCost = grossArea * costPerM2;
    const totalCost = buildCost * (1 + extraRate / 100);

    // 4. Gelir: müteahhit yalnız kendi payını satar
    const totalRevenue = contractorArea * salePrice;

    // 5. Net kâr ve maliyete göre kâr oranı
    const netProfit = totalRevenue - totalCost;
    const profitMargin = (netProfit / totalCost) * 100;

    const money = new Intl.NumberFormat('tr-TR', { maximumFractionDigits: 0 });
    const pct = v => v.toLocaleString('tr-TR', { minimumFractionDigits: 1, maximumFractionDigits: 1 });

    document.getElementById('grossArea').innerText = money.format(grossArea) + ' m²';
    document.getElementById('contractorArea').innerText = money.format(contractorArea) + ' m²';
    document.getElementById('totalCost').innerText = money.format(totalCost) + ' TL';
    document.getElementById('totalRevenue').innerText = money.format(totalRevenue) + ' TL';

    const netProfitEl = document.getElementById('netProfit');
    if(netProfit < 0) {
        netProfitEl.innerText = '-' + money.format(Math.abs(netProfit)) + ' TL (ZARAR)';
        netProfitEl.style.color = '#f87171';
    } else {
        netProfitEl.innerText = money.format(netProfit) + ' TL';
        netProfitEl.style.color = 'var(--accent)';
    }

    // Uzman notu / Risk Analizi
    const insightBox = document.getElementById('aiInsight');
    const insightTitle = document.getElementById('insightTitle');
    const insightText = document.getElementById('insightText');
    const insightIcon = document.getElementById('insightIcon');

    if (profitMargin < 10) {
        insightBox.className = 'ai-insight danger';
        insightTitle.innerText = `Yüksek risk (maliyete göre kâr: %${pct(profitMargin)})`;
        insightText.innerHTML = `Bu projede kârlılık oranınız <strong>%${pct(profitMargin)}</strong> olarak çıkıyor. Enflasyon ve beklenmedik inşaat giderleri (harfiyat şoku, demir zamları) göz önüne alındığında bu oran çok tehlikelidir. Arsa sahibi payını düşürmeli veya satış rakamları yüksek bir bölgeye odaklanmalısınız.`;
        insightIcon.innerText = '⚠️';
    } else if (profitMargin >= 10 && profitMargin <= 25) {
        insightBox.className = 'ai-insight';
        insightTitle.innerText = `Orta kârlılık (maliyete göre kâr: %${pct(profitMargin)})`;
        insightText.innerHTML = `Kârlılık oranınız <strong>%${pct(profitMargin)}</strong> seviyesinde. Finansmanı nakit olarak çözebilirseniz uygulanabilir bir proje, ancak kredi faizleriyle ilerlenecekse dikkatli bütçe yönetimi gerekir.`;
        insightIcon.innerText = '⚖️';
    } else {
        insightBox.className = 'ai-insight success';
        insightTitle.innerText = `Kârlı görünüyor (maliyete göre kâr: %${pct(profitMargin)})`;
        insightText.innerHTML = `Maliyete göre kâr <strong>%${pct(profitMargin)}</strong>. Varsayımlarınız (satış fiyatı, maliyet, ek giderler) doğruysa proje kârlı görünüyor; karar öncesi avan proje ve güncel teklif ile doğrulayın.`;
        insightIcon.innerText = '📈';
    }

    insightText.innerHTML += ' <br><small>KDV, vergiler ve fiyat artışı dahil değildir.</small>';

    document.getElementById('leadCalculatedData').value = `Toplam Brüt: ${money.format(grossArea)}m2 (emsal harici %${Math.round(exRate*100)}) | Maliyete göre kâr: %${pct(profitMargin)} | Satış Geliri: ${money.format(totalRevenue)} TL | Maliyet: ${money.format(totalCost)} TL`;

    document.getElementById('resultBox').classList.add('active');
    document.getElementById('budgetConfirmBox').style.display = 'block';
    document.getElementById('leadBox').style.display = 'none';
    document.getElementById('leadBox').classList.remove('active');
    document.getElementById('qualifiedMessage').style.display = 'none';
    
    setTimeout(() => { document.getElementById('resultBox').scrollIntoView({ behavior: 'smooth', block: 'start' }); }, 150);
});

document.getElementById('costPreset').addEventListener('change', function() {
    document.getElementById('costPerM2').value = this.value;
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
            Bu aşamada iletişim bilgilerinizi almıyoruz. Kârlı bir arsa bulduğunuzda ve inşaata başlamaya karar verdiğinizde bize ulaşabilirsiniz.
        </div>
    `;
    document.getElementById('leadBox').style.display = 'none';
    document.getElementById('qualifiedMessage').style.display = 'none';
});
</script>

    <x-calc.kit />
<x-tools.tool-guide slug="kat-karsiligi-analiz" />

<x-site.contech-footer />
    <script src="/js/tool-events.js" defer></script>
</body>
</html>
