<!DOCTYPE html>
<html lang="tr" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>İnşaat ve Tadilat Maliyeti Hesaplama | WORKSİTEM</title>
    <meta name="description" content="İnşaat ve tadilat maliyetinizi m² üzerinden hesaplayın. ÇŞİDB resmi işçilik analizleri, 168 birim fiyat, AI destekli ön keşif. Türkiye'nin güvenilir inşaat maliyet platformu.">
    <meta name="robots" content="index,follow,max-image-preview:large">
    <link rel="canonical" href="https://worksitem.com/">
    
    <meta property="og:type" content="website">
    <meta property="og:title" content="İnşaat ve Tadilat Maliyeti Hesaplama | WORKSİTEM">
    <meta property="og:description" content="ÇŞİDB verisi ile hızlı, şeffaf ve akıllı ön keşif. 74+ hesaplayıcı, AI destekli fiyat motoru.">
    <meta property="og:image" content="https://worksitem.com/brand/og-image.png">
    <meta property="og:url" content="https://worksitem.com/">
    
    <link rel="icon" href="/favicon.ico">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/worksitem-design.css">
    
    <style>
        .hidden { display: none !important; }
        
        .hero {
            position: relative;
            padding: 64px 0 48px;
            background: linear-gradient(180deg, var(--ws-bg) 0%, var(--ws-surface) 100%);
        }
        .hero::before {
            content: "";
            position: absolute;
            width: 600px; height: 600px;
            right: -300px; top: -300px;
            border-radius: 50%;
            background: rgba(255, 184, 0, 0.1);
            filter: blur(80px);
            pointer-events: none;
        }
        
        .trust-badges {
            display: flex; gap: 16px; flex-wrap: wrap;
            margin-top: 24px; padding: 16px 20px;
            background: var(--ws-surface-2);
            border-radius: var(--ws-radius-md);
            border: 1px solid var(--ws-border);
        }
        .trust-badges span {
            font-size: 12px; font-weight: 700;
            color: var(--ws-text-muted);
            display: flex; align-items: center; gap: 6px;
        }
        .trust-badges span::before {
            content: "✓"; color: var(--ws-success); font-weight: 900;
        }
        
        .mini-calc {
            background: var(--ws-surface);
            border: 1px solid var(--ws-border);
            border-radius: var(--ws-radius-lg);
            padding: 24px;
            box-shadow: var(--ws-shadow-md);
        }
        
        .trend-chart {
            background: var(--ws-surface-2);
            border-radius: var(--ws-radius-md);
            padding: 16px;
            margin-top: 16px;
        }
        .trend-bar {
            height: 8px; background: var(--ws-border);
            border-radius: 4px; overflow: hidden;
            margin-top: 8px;
        }
        .trend-bar-fill {
            height: 100%;
            background: linear-gradient(90deg, var(--ws-yellow), var(--ws-success));
            border-radius: 4px;
        }
        
        .trust-logos {
            display: flex; gap: 32px;
            align-items: center; justify-content: center;
            flex-wrap: wrap; padding: 24px 0; opacity: 0.7;
        }
        .trust-logos span {
            font-size: 14px; font-weight: 800;
            color: var(--ws-text-muted); letter-spacing: 1px;
        }
        
        .benchmark {
            display: grid; grid-template-columns: 1fr 1fr;
            gap: 24px; margin-top: 24px;
        }
        .benchmark-item {
            padding: 20px;
            background: var(--ws-surface);
            border: 1px solid var(--ws-border);
            border-radius: var(--ws-radius-md);
            text-align: center;
        }
        .benchmark-item strong {
            font-size: 32px; font-weight: 900;
            color: var(--ws-yellow); display: block;
        }
        
        .ws-tab-btn {
            flex: 1; padding: 10px;
            text-align: center;
            border: 1px solid var(--ws-border);
            background: var(--ws-surface-2);
            font-size: 12px; font-weight: 700;
            cursor: pointer;
            border-radius: var(--ws-radius-sm);
            transition: 0.2s;
        }
        .ws-tab-btn.active {
            background: rgba(255, 184, 0, 0.1);
            border-color: var(--ws-yellow);
            color: var(--ws-yellow);
        }
        .ws-upload-zone {
            border: 2px dashed var(--ws-border);
            padding: 16px;
            text-align: center;
            border-radius: var(--ws-radius-md);
            cursor: pointer;
            transition: 0.2s;
            font-size: 13px;
            font-weight: 600;
            color: var(--ws-text-muted);
        }
        .ws-upload-zone:hover {
            border-color: var(--ws-yellow);
            background: rgba(255, 184, 0, 0.05);
        }
    </style>
</head>
<body data-tool-slug="homepage">

<x-site.header />

<!-- HERO -->
<section class="hero">
    <div class="ws-container">
        <div class="ws-grid-2" style="gap: 64px; align-items: flex-start;">
            
            <!-- SOL -->
            <div>
                <div class="ws-badge ws-badge-yellow">🏗️ TÜRKİYE'NİN CONTECH PLATFORMU</div>
                
                <h1 class="ws-h1" style="margin-top: 20px;">
                    Türkiye'nin<br>
                    <span style="color: var(--ws-yellow);">güvenilir</span><br>
                    inşaat maliyet<br>
                    platformu.
                </h1>
                
                <p class="ws-body ws-muted" style="margin-top: 24px; font-size: 19px; max-width: 620px;">
                    <strong style="color: var(--ws-text);">ÇŞİDB resmi işçilik analizleri</strong> ve 168 güncel birim fiyat ile hızlı, şeffaf ve akıllı ön keşif. AI destekli dosya analizi ve kentsel dönüşüm maliyet motoru.
                </p>
                
                <div class="trust-badges">
                    <span>Resmi Analizlerle Uyumlu</span>
                    <span>Türkiye'ye Özel</span>
                    <span>Şeffaf Hesaplama</span>
                    <span>AI Destekli</span>
                </div>
            </div>
            
            <!-- SAĞ: HESAPLAMA MOTORU -->
            <div>
                <div class="mini-calc">
                    <div style="font-size: 11px; font-weight: 900; color: var(--ws-yellow); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px;">
                        ⚡ AKILLI ÖN KEŞİF
                    </div>
                    
                    <form id="wsLeadForm" action="{{ route('lead.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="submission_id" id="ws_submission_id">
                        <input type="hidden" name="project_intent" id="ws_project_intent" value="insaat">
                        <input type="hidden" name="calculated_service" id="ws_calculated_service" value="İnşaat Ön Bütçe">
                        <input type="hidden" name="estimated_cost" id="ws_estimated_cost" value="0">
                        <input type="hidden" name="source" value="homepage_estimator">

                        <!-- SEKMELER -->
                        <div style="display: flex; gap: 8px; margin-bottom: 16px;">
                            <div class="ws-tab-btn active" data-intent="insaat" onclick="wsSetIntent('insaat')">🏗️ İnşaat</div>
                            <div class="ws-tab-btn" data-intent="tadilat" onclick="wsSetIntent('tadilat')">🛠️ Tadilat</div>
                            <div class="ws-tab-btn" data-intent="kentsel_donusum" onclick="wsSetIntent('kentsel_donusum')">🏙️ Dönüşüm</div>
                        </div>

                        <!-- GİRDİLER -->
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 16px;">
                            <div>
                                <label style="display: block; font-size: 11px; font-weight: 700; color: var(--ws-text-muted); margin-bottom: 4px;">Alan (m²)</label>
                                <input type="number" name="calculated_area" id="ws_area" value="200" min="20" max="100000" style="width: 100%; height: 42px; border: 1px solid var(--ws-border); border-radius: var(--ws-radius-sm); padding: 0 12px; font-size: 14px; background: var(--ws-surface); color: var(--ws-text);">
                            </div>
                            <div>
                                <label style="display: block; font-size: 11px; font-weight: 700; color: var(--ws-text-muted); margin-bottom: 4px;">Şehir</label>
                                <input type="text" name="city" id="ws_city" value="İstanbul" style="width: 100%; height: 42px; border: 1px solid var(--ws-border); border-radius: var(--ws-radius-sm); padding: 0 12px; font-size: 14px; background: var(--ws-surface); color: var(--ws-text);">
                            </div>
                        </div>

                        <!-- KALİTE -->
                        <div style="margin-bottom: 16px;">
                            <label style="display: block; font-size: 11px; font-weight: 700; color: var(--ws-text-muted); margin-bottom: 4px;">Kalite</label>
                            <select id="ws_quality" style="width: 100%; height: 42px; border: 1px solid var(--ws-border); border-radius: var(--ws-radius-sm); padding: 0 12px; font-size: 14px; background: var(--ws-surface); color: var(--ws-text);">
                                <option value="ekonomik">Ekonomik</option>
                                <option value="standart" selected>Standart</option>
                                <option value="premium">Premium</option>
                                <option value="luks">Lüks</option>
                            </select>
                        </div>

                        <!-- AI UPLOAD -->
                        <div class="ws-upload-zone" onclick="document.getElementById('ws_file').click()">
                            📄 PDF, JPG veya CSV dosyasından AI Metraj (Opsiyonel)
                            <input type="file" id="ws_file" name="project_file" class="hidden" accept=".pdf,.jpg,.jpeg,.png,.csv">
                        </div>
                        <div id="ws_file_name" style="font-size: 11px; color: var(--ws-text-muted); margin-top: 6px; display: none;"></div>

                        <button type="button" id="ws_calc_btn" class="ws-btn ws-btn-primary" style="width: 100%; margin-top: 16px;">
                            Bütçeyi Oluştur →
                        </button>

                        <!-- SONUÇ -->
                        <div id="ws_result_box" style="display: none; margin-top: 24px; padding-top: 24px; border-top: 1px solid var(--ws-border);">
                            <h3 id="ws_res_title" style="font-size: 16px; font-weight: 800;">Yaklaşık Ön Bütçe</h3>
                            <div id="ws_res_total" style="font-size: 32px; font-weight: 900; color: var(--ws-yellow); margin: 8px 0;">-- TL</div>
                            <p id="ws_res_note" style="font-size: 12px; color: var(--ws-text-muted);"></p>
                            
                            <div style="margin-top: 16px;">
                                <input type="text" name="name" placeholder="Ad Soyad" required style="width:100%; margin-bottom:8px; height:42px; padding:0 12px; border: 1px solid var(--ws-border); border-radius: 8px;">
                                <input type="tel" name="phone" placeholder="Telefon (05XX...)" required style="width:100%; margin-bottom:12px; height:42px; padding:0 12px; border: 1px solid var(--ws-border); border-radius: 8px;">
                                <label style="font-size: 11px; color: var(--ws-text-muted); display: flex; gap: 8px; align-items: flex-start;">
                                    <input type="checkbox" name="contact_consent" value="1" required>
                                    <span>Profesyonel teklif için iletişime geçilmesini kabul ediyorum (KVKK).</span>
                                </label>
                                <button type="submit" id="ws_submit_btn" class="ws-btn ws-btn-success" style="width: 100%; margin-top: 12px;">Net Teklif İste</button>
                            </div>
                        </div>
                    </form>
                </div>
                
                <!-- TREND -->
                <div class="trend-chart">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="font-size: 11px; font-weight: 800; color: var(--ws-text-muted); text-transform: uppercase;">📈 Piyasa Trendi (2026)</span>
                        <span style="font-size: 11px; color: var(--ws-success); font-weight: 700;">+8.4%</span>
                    </div>
                    <div style="margin-top: 12px;">
                        <div style="display: flex; justify-content: space-between; font-size: 12px; margin-bottom: 4px;">
                            <span>Çimento</span><span style="font-weight: 700;">₺220/50kg</span>
                        </div>
                        <div class="trend-bar"><div class="trend-bar-fill" style="width: 75%;"></div></div>
                    </div>
                    <div style="margin-top: 10px;">
                        <div style="display: flex; justify-content: space-between; font-size: 12px; margin-bottom: 4px;">
                            <span>Demir</span><span style="font-weight: 700;">₺51.322/ton</span>
                        </div>
                        <div class="trend-bar"><div class="trend-bar-fill" style="width: 92%; background: linear-gradient(90deg, #FFB800, #EF4444);"></div></div>
                    </div>
                    <div style="margin-top: 10px;">
                        <div style="display: flex; justify-content: space-between; font-size: 12px; margin-bottom: 4px;">
                            <span>Beton C25</span><span style="font-weight: 700;">₺4.047/m³</span>
                        </div>
                        <div class="trend-bar"><div class="trend-bar-fill" style="width: 65%;"></div></div>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</section>

<!-- VERİ KAYNAKLARI -->
<section style="padding: 32px 0; border-top: 1px solid var(--ws-border); border-bottom: 1px solid var(--ws-border); background: var(--ws-surface);">
    <div class="ws-container">
        <div style="text-align: center; margin-bottom: 16px;">
            <span style="font-size: 11px; font-weight: 900; color: var(--ws-text-muted); text-transform: uppercase; letter-spacing: 1.5px;">
                GÜVENİLİR VERİ KAYNAKLARI
            </span>
        </div>
        <div class="trust-logos">
            <span>ÇŞİDB</span>
            <span>YFK</span>
            <span>KOÇTAŞ</span>
            <span>BAUHAUS</span>
            <span>TMB</span>
            <span>İNTES</span>
        </div>
    </div>
</section>

<!-- MODÜLLER -->
<section class="ws-section">
    <div class="ws-container">
        <div style="text-align: center; max-width: 720px; margin: 0 auto 48px;">
            <div class="ws-badge ws-badge-yellow">TÜRKİYE'YE ÖZEL MODÜLLER</div>
            <h2 class="ws-h2" style="margin-top: 16px;">Sadece Worksitem'da</h2>
            <p class="ws-muted" style="margin-top: 12px;">
                Global rakiplerin sunmadığı, Türkiye pazarına özel modüller.
            </p>
        </div>
        
        <div class="ws-grid-3">
            <div class="ws-card ws-card-lg">
                <div style="font-size: 32px;">📋</div>
                <h3 class="ws-h3" style="margin-top: 16px;">Ruhsat & İzin Maliyeti</h3>
                <p class="ws-muted" style="margin-top: 8px; font-size: 14px; line-height: 1.7;">
                    Belediye harçları, yapı denetim, iskan. Türkiye mevzuatına uygun.
                </p>
                <a href="/araclar" class="ws-body" style="display: inline-block; margin-top: 16px; color: var(--ws-yellow); font-weight: 700; font-size: 14px;">Keşfet →</a>
            </div>
            
            <div class="ws-card ws-card-lg">
                <div style="font-size: 32px;">🚜</div>
                <h3 class="ws-h3" style="margin-top: 16px;">Ekipman Kiralama</h3>
                <p class="ws-muted" style="margin-top: 8px; font-size: 14px; line-height: 1.7;">
                    Hafriyat kamyonu, mobil vinç, iskele. Güncel kiralama fiyatları.
                </p>
                <a href="/araclar" class="ws-body" style="display: inline-block; margin-top: 16px; color: var(--ws-yellow); font-weight: 700; font-size: 14px;">Keşfet →</a>
            </div>
            
            <div class="ws-card ws-card-lg">
                <div style="font-size: 32px;">💰</div>
                <h3 class="ws-h3" style="margin-top: 16px;">Nakit Akış Raporu</h3>
                <p class="ws-muted" style="margin-top: 8px; font-size: 14px; line-height: 1.7;">
                    Aylık nakit akışı projeksiyonu. Müteahhit finans yönetimi.
                </p>
                <a href="/araclar" class="ws-body" style="display: inline-block; margin-top: 16px; color: var(--ws-yellow); font-weight: 700; font-size: 14px;">Keşfet →</a>
            </div>
        </div>
    </div>
</section>

<!-- BENCHMARK -->
<section class="ws-section" style="background: var(--ws-surface-2);">
    <div class="ws-container">
        <div style="text-align: center; max-width: 720px; margin: 0 auto;">
            <div class="ws-badge ws-badge-yellow">GLOBAL KARŞILAŞTIRMA</div>
            <h2 class="ws-h2" style="margin-top: 16px;">Türkiye vs Dünya</h2>
            <p class="ws-muted" style="margin-top: 12px;">
                İnşaat maliyetlerinde Türkiye'nin global konumu.
            </p>
            
            <div class="benchmark">
                <div class="benchmark-item">
                    <small style="font-size: 12px; color: var(--ws-text-muted); text-transform: uppercase;">Türkiye Ort.</small>
                    <strong>₺21.050</strong>
                    <span style="font-size: 12px; color: var(--ws-text-muted);">/m² (3B sınıf)</span>
                </div>
                <div class="benchmark-item">
                    <small style="font-size: 12px; color: var(--ws-text-muted); text-transform: uppercase;">Global Ort.</small>
                    <strong>$1.200</strong>
                    <span style="font-size: 12px; color: var(--ws-text-muted);">/m² (~₺38.400)</span>
                </div>
            </div>
            
            <p class="ws-muted" style="margin-top: 16px; font-size: 12px;">
                Kaynak: ÇŞİDB 2026, RICS Global Construction Survey
            </p>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="ws-section">
    <div class="ws-container" style="text-align: center;">
        <h2 class="ws-h2">Projeniz için profesyonel destek</h2>
        <p class="ws-muted" style="max-width: 600px; margin: 16px auto 32px;">
            Kainta İnşaat mühendisleri ile ücretsiz ön görüşme.
        </p>
        <div style="display: flex; gap: 12px; justify-content: center; flex-wrap: wrap;">
            <a href="https://kainta.com" target="_blank" rel="noopener" class="ws-btn ws-btn-success ws-btn-lg">
                🏗️ Kainta İnşaat ile Görüş
            </a>
            <a href="/araclar" class="ws-btn ws-btn-secondary ws-btn-lg">
                Tüm Araçlar
            </a>
        </div>
    </div>
</section>

<x-site.footer />

<script src="/js/tool-events.js" defer></script>

<script>
// UUID
function wsUUID() {
    return (crypto.randomUUID ? crypto.randomUUID() : Date.now() + '-' + Math.random().toString(36).slice(2));
}

// Init
document.getElementById('ws_submission_id').value = wsUUID();

// Intent
function wsSetIntent(intent) {
    document.getElementById('ws_project_intent').value = intent;
    document.querySelectorAll('.ws-tab-btn').forEach(b => b.classList.remove('active'));
    document.querySelector(`.ws-tab-btn[data-intent="${intent}"]`).classList.add('active');
    document.getElementById('ws_result_box').style.display = 'none';
    document.getElementById('ws_calculated_service').value =
        intent === 'tadilat' ? 'Tadilat Ön Bütçe' :
        intent === 'kentsel_donusum' ? 'Kentsel Dönüşüm Ön Bütçe' :
        'İnşaat Ön Bütçe';
}

// Hesap
document.getElementById('ws_calc_btn').addEventListener('click', function() {
    var area = parseFloat(document.getElementById('ws_area').value) || 0;
    var intent = document.getElementById('ws_project_intent').value;
    var quality = document.getElementById('ws_quality').value;

    if (area < 20) {
        alert('Lütfen en az 20 m² girin.');
        return;
    }

    var basePerM2 = {
        'insaat': 20000,
        'tadilat': 12000,
        'kentsel_donusum': 24000
    }[intent] || 20000;

    var qualityMultiplier = {
        'ekonomik': 0.7,
        'standart': 1.0,
        'premium': 1.35,
        'luks': 1.8
    }[quality] || 1.0;

    var total = area * basePerM2 * qualityMultiplier;

    document.getElementById('ws_res_total').textContent =
        new Intl.NumberFormat('tr-TR', { maximumFractionDigits: 0 }).format(total) + ' TL';
    document.getElementById('ws_res_title').textContent =
        intent === 'kentsel_donusum' ? 'Yeni Yapı Ön Bütçesi' :
        intent === 'tadilat' ? 'Tadilat Ön Bütçesi' :
        'İnşaat Ön Bütçesi';
    document.getElementById('ws_res_note').textContent =
        'ÇŞİDB bazlı şeffaf bütçe. ' + quality + ' kalite. Yıkım, harç ve KDV hariçtir.';
    document.getElementById('ws_estimated_cost').value = total;

    document.getElementById('ws_result_box').style.display = 'block';
});

// Dosya seçimi
var wsFileInput = document.getElementById('ws_file');
if (wsFileInput) {
    wsFileInput.addEventListener('change', function() {
        var label = document.getElementById('ws_file_name');
        if (this.files && this.files[0]) {
            label.textContent = '📎 Seçildi: ' + this.files[0].name;
            label.style.display = 'block';
        }
    });
}

// Form submit
document.getElementById('wsLeadForm').addEventListener('submit', function(e) {
    e.preventDefault();

    var btn = document.getElementById('ws_submit_btn');
    var consent = document.querySelector('input[name="contact_consent"]');

    if (!consent.checked) {
        alert('Lütfen KVKK onayını işaretleyin.');
        return;
    }

    btn.textContent = 'Gönderiliyor...';
    btn.disabled = true;

    fetch(e.target.action, {
        method: 'POST',
        body: new FormData(e.target),
        headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content }
    })
    .then(function(r) {
        if (r.ok || r.status === 201) {
            window.location.href = '/thanks';
        } else {
            return r.json().then(function(data) { throw data; });
        }
    })
    .catch(function(err) {
        alert('Hata: ' + (err.message || 'Lütfen tüm alanları doldurun.'));
        btn.textContent = 'Net Teklif İste';
        btn.disabled = false;
    });
});
</script>
</body>
</html>
