<!DOCTYPE html>
<html lang="tr" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>İnşaat ve Tadilat Maliyeti Hesaplama | WORKSİTEM</title>
    <meta name="description" content="İnşaat ve tadilat maliyetinizi m² üzerinden hesaplayın. ÇŞİDB resmi işçilik analizleri, 168 birim fiyat, AI destekli ön keşif. Türkiye'nin güvenilir inşaat maliyet platformu.">
    <meta name="robots" content="index,follow,max-image-preview:large">
    <link rel="canonical" href="https://worksitem.com/">
    
    <!-- OG -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="İnşaat ve Tadilat Maliyeti Hesaplama | WORKSİTEM">
    <meta property="og:description" content="ÇŞİDB verisi ile hızlı, şeffaf ve akıllı ön keşif. 74+ hesaplayıcı, AI destekli fiyat motoru.">
    <meta property="og:image" content="https://worksitem.com/brand/og-image.png">
    <meta property="og:url" content="https://worksitem.com/">
    
    <link rel="icon" href="/favicon.ico">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/worksitem-design.css">
    
    <style>
        /* Hero özel */
        .hero {
            position: relative;
            padding: 64px 0 48px;
            background: linear-gradient(180deg, var(--ws-bg) 0%, var(--ws-surface) 100%);
        }
        .hero::before {
            content: "";
            position: absolute;
            width: 600px;
            height: 600px;
            right: -300px;
            top: -300px;
            border-radius: 50%;
            background: rgba(255, 184, 0, 0.1);
            filter: blur(80px);
            pointer-events: none;
        }
        
        /* Trust badges */
        .trust-badges {
            display: flex;
            gap: 16px;
            flex-wrap: wrap;
            margin-top: 24px;
            padding: 16px 20px;
            background: var(--ws-surface-2);
            border-radius: var(--ws-radius-md);
            border: 1px solid var(--ws-border);
        }
        .trust-badges span {
            font-size: 12px;
            font-weight: 700;
            color: var(--ws-text-muted);
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .trust-badges span::before {
            content: "✓";
            color: var(--ws-success);
            font-weight: 900;
        }
        
        /* Mini hesaplama widget */
        .mini-calc {
            background: var(--ws-surface);
            border: 1px solid var(--ws-border);
            border-radius: var(--ws-radius-lg);
            padding: 24px;
            box-shadow: var(--ws-shadow-md);
        }
        
        /* Trend grafiği */
        .trend-chart {
            background: var(--ws-surface-2);
            border-radius: var(--ws-radius-md);
            padding: 16px;
            margin-top: 16px;
        }
        .trend-bar {
            height: 8px;
            background: var(--ws-border);
            border-radius: 4px;
            overflow: hidden;
            margin-top: 8px;
        }
        .trend-bar-fill {
            height: 100%;
            background: linear-gradient(90deg, var(--ws-yellow), var(--ws-success));
            border-radius: 4px;
        }
        
        /* Trust logos */
        .trust-logos {
            display: flex;
            gap: 32px;
            align-items: center;
            justify-content: center;
            flex-wrap: wrap;
            padding: 24px 0;
            opacity: 0.7;
        }
        .trust-logos span {
            font-size: 14px;
            font-weight: 800;
            color: var(--ws-text-muted);
            letter-spacing: 1px;
        }
        
        /* Benchmark */
        .benchmark {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
            margin-top: 24px;
        }
        .benchmark-item {
            padding: 20px;
            background: var(--ws-surface);
            border: 1px solid var(--ws-border);
            border-radius: var(--ws-radius-md);
            text-align: center;
        }
        .benchmark-item strong {
            font-size: 32px;
            font-weight: 900;
            color: var(--ws-yellow);
            display: block;
        }
        .benchmark-item small {
            font-size: 12px;
            color: var(--ws-text-muted);
            text-transform: uppercase;
            letter-spacing: 1px;
        }
    </style>
</head>
<body data-tool-slug="homepage">

<x-site.header />

<!-- HERO -->
<section class="hero">
    <div class="ws-container">
        <div class="ws-grid-2" style="gap: 64px; align-items: center;">
            
            <!-- SOL: MESAJ -->
            <div>
                <div class="ws-badge ws-badge-yellow">
                    🏗️ TÜRKİYE'NİN CONTECH PLATFORMU
                </div>
                
                <h1 class="ws-h1" style="margin-top: 20px;">
                    Türkiye'nin<br>
                    <span style="color: var(--ws-yellow);">güvenilir</span><br>
                    inşaat maliyet<br>
                    platformu.
                </h1>
                
                <p class="ws-body ws-muted" style="margin-top: 24px; font-size: 19px; max-width: 620px;">
                    <strong style="color: var(--ws-text);">ÇŞİDB resmi işçilik analizleri</strong> ve 168 güncel birim fiyat ile hızlı, şeffaf ve akıllı ön keşif. 74+ hesaplayıcı, AI destekli fiyat motoru ve müteahhit iş akışı.
                </p>
                
                <div style="display: flex; gap: 12px; margin-top: 32px; flex-wrap: wrap;">
                    <a href="/maliyet-hesapla?tip=insaat" class="ws-btn ws-btn-primary ws-btn-lg">
                        Hemen Maliyet Hesapla →
                    </a>
                    <a href="/araclar" class="ws-btn ws-btn-secondary ws-btn-lg">
                        Tüm Araçlar (74)
                    </a>
                </div>
                
                <!-- TRUST BADGES -->
                <div class="trust-badges">
                    <span>Resmi Analizlerle Uyumlu</span>
                    <span>Türkiye'ye Özel</span>
                    <span>Şeffaf Hesaplama</span>
                    <span>AI Destekli</span>
                </div>
            </div>
            
            <!-- SAĞ: MİNİ HESAPLAMA -->
            <div class="mini-calc">
                <div style="font-size: 11px; font-weight: 900; color: var(--ws-yellow); text-transform: uppercase; letter-spacing: 1px;">
                    ⚡ HIZLI MALİYET HESABI
                </div>
                <h3 class="ws-h3" style="margin-top: 8px; font-size: 22px;">
                    Anında yaklaşık bütçenizi görün
                </h3>
                <p class="ws-muted" style="font-size: 13px; margin-top: 6px;">
                    Birkaç saniyede ön bütçe. Kayıt gerekmez.
                </p>
                
                <form action="/maliyet-hesapla" method="GET" style="margin-top: 20px;">
                    <div style="display: grid; gap: 12px;">
                        <div>
                            <label style="display: block; font-size: 12px; font-weight: 700; color: var(--ws-text-muted); margin-bottom: 6px;">Proje Türü</label>
                            <select name="tip" style="width: 100%; height: 48px; border: 1px solid var(--ws-border); border-radius: var(--ws-radius-md); padding: 0 14px; font-size: 14px; background: var(--ws-surface); color: var(--ws-text);">
                                <option value="insaat">Sıfırdan İnşaat</option>
                                <option value="tadilat">Tadilat</option>
                                <option value="kentsel">Kentsel Dönüşüm</option>
                            </select>
                        </div>
                        
                        <div>
                            <label style="display: block; font-size: 12px; font-weight: 700; color: var(--ws-text-muted); margin-bottom: 6px;">Alan (m²)</label>
                            <input type="number" name="alan" placeholder="200" value="200" min="1" max="100000" style="width: 100%; height: 48px; border: 1px solid var(--ws-border); border-radius: var(--ws-radius-md); padding: 0 14px; font-size: 14px; background: var(--ws-surface); color: var(--ws-text);">
                        </div>
                        
                        <button type="submit" class="ws-btn ws-btn-primary" style="width: 100%;">
                            Maliyet Hesapla →
                        </button>
                    </div>
                </form>
                
                <!-- FİYAT TREND -->
                <div class="trend-chart">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="font-size: 11px; font-weight: 800; color: var(--ws-text-muted); text-transform: uppercase;">📈 Piyasa Trendi (2026)</span>
                        <span style="font-size: 11px; color: var(--ws-success); font-weight: 700;">+8.4%</span>
                    </div>
                    
                    <div style="margin-top: 12px;">
                        <div style="display: flex; justify-content: space-between; font-size: 12px; margin-bottom: 4px;">
                            <span>Çimento</span>
                            <span style="font-weight: 700;">₺220/50kg</span>
                        </div>
                        <div class="trend-bar"><div class="trend-bar-fill" style="width: 75%;"></div></div>
                    </div>
                    
                    <div style="margin-top: 10px;">
                        <div style="display: flex; justify-content: space-between; font-size: 12px; margin-bottom: 4px;">
                            <span>Demir</span>
                            <span style="font-weight: 700;">₺51.322/ton</span>
                        </div>
                        <div class="trend-bar"><div class="trend-bar-fill" style="width: 92%; background: linear-gradient(90deg, #FFB800, #EF4444);"></div></div>
                    </div>
                    
                    <div style="margin-top: 10px;">
                        <div style="display: flex; justify-content: space-between; font-size: 12px; margin-bottom: 4px;">
                            <span>Beton C25</span>
                            <span style="font-weight: 700;">₺4.047/m³</span>
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
                    <small>Türkiye Ort.</small>
                    <strong>₺21.050</strong>
                    <span style="font-size: 12px; color: var(--ws-text-muted);">/m² (3B sınıf)</span>
                </div>
                <div class="benchmark-item">
                    <small>Global Ort.</small>
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
            Kainta İnşaat mühendisleri ile ücretsiz ön görüşme. Projenizi birlikte değerlendirelim.
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
</body>
</html>
