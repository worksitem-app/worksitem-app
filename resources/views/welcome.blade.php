<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover, maximum-scale=1.0, user-scalable=no">
    <title>WORKSİTEM | İnşaat & Tadilat Maliyet Hesaplama</title>
    <meta name="description" content="İnşaat ve tadilat projenizin yaklaşık maliyetini saniyeler içinde hesaplayın. Yapı tipi, alan ve proje detaylarına göre tahmini bütçenizi öğrenin ve Kainta İnşaat'tan teklif alın.">
    <meta name="robots" content="index,follow,max-image-preview:large">
    <link rel="canonical" href="https://worksitem.com/">

    <meta property="og:type" content="website">
    <meta property="og:title" content="WORKSİTEM | İnşaat & Tadilat Maliyet Hesaplama">
    <meta property="og:description" content="İnşaat ve tadilat projenizin yaklaşık maliyetini hesaplayın, bütçenizi öğrenin ve profesyonel teklif alın.">
    <meta property="og:url" content="https://worksitem.com/">
    <meta property="og:site_name" content="WORKSİTEM">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="WORKSİTEM | İnşaat & Tadilat Maliyet Hesaplama">
    <meta name="twitter:description" content="İnşaat ve tadilat maliyetinizi saniyeler içinde hesaplayın.">

    <link rel="icon" href="/favicon.ico" type="image/x-icon">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    <style>
        :root {
            --bg: #f6f7f9;
            --surface: #ffffff;
            --surface-2: #f1f3f5;
            --text: #111111;
            --muted: #666b73;
            --border: rgba(0,0,0,.09);
            --yellow: #ffb800;
            --yellow-dark: #e49f00;
            --black: #050505;
            --shadow: 0 18px 60px rgba(0,0,0,.08);
            --radius: 22px;
            --max: 1180px;
        }

        html[data-theme="dark"] {
            --bg: #050505;
            --surface: #101010;
            --surface-2: #171717;
            --text: #ffffff;
            --muted: #a1a1aa;
            --border: rgba(255,255,255,.10);
            --shadow: 0 20px 70px rgba(0,0,0,.35);
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }
        html { scroll-behavior: smooth; }
        body { font-family: 'Inter', sans-serif; background: var(--bg); color: var(--text); line-height: 1.5; -webkit-font-smoothing: antialiased; overflow-x: hidden; transition: background .3s, color .3s; }
        a { color: inherit; text-decoration: none; }
        button, input, select { font: inherit; }
        img { max-width: 100%; display: block; }

        .container { width: min(calc(100% - 32px), var(--max)); margin: 0 auto; }

        /* NAVBAR */
        .navbar { position: sticky; top: 0; z-index: 1000; backdrop-filter: blur(18px); -webkit-backdrop-filter: blur(18px); background: color-mix(in srgb, var(--bg) 88%, transparent); border-bottom: 1px solid var(--border); }
        .nav-inner { min-height: 74px; display: flex; align-items: center; justify-content: space-between; gap: 20px; }
        .brand { display: flex; align-items: center; gap: 10px; font-size: 19px; font-weight: 900; letter-spacing: -.6px; }
        .brand img {
    width: 38px;
    height: 38px;
    object-fit: cover;
    border-radius: 8px;
}
        .brand strong { color: var(--yellow); }
        .nav-links { display: flex; align-items: center; gap: 8px; }
        .nav-link { padding: 10px 13px; color: var(--muted); font-size: 13px; font-weight: 700; border-radius: 10px; transition:.2s; }
        .nav-link:hover { color: var(--text); background: var(--surface-2); }
        .theme-btn { width: 42px; height: 42px; border: 1px solid var(--border); background: var(--surface); color: var(--text); border-radius: 12px; cursor: pointer; font-size: 18px; transition:.2s; }
        .theme-btn:hover { border-color: var(--yellow); }
        .mobile-nav-btn { display: none; }

        /* HERO */
        .hero { position: relative; padding: 76px 0 55px; overflow: hidden; }
        .hero::before { content: ""; position: absolute; width: 520px; height: 520px; right: -220px; top: -250px; border-radius: 50%; background: rgba(255,184,0,.13); filter: blur(70px); pointer-events: none; }
        .hero-grid { display: grid; grid-template-columns: 1.15fr .85fr; align-items: center; gap: 55px; }
        .eyebrow { display: inline-flex; align-items: center; gap: 8px; padding: 8px 12px; border: 1px solid rgba(255,184,0,.35); background: rgba(255,184,0,.10); color: var(--yellow-dark); border-radius: 100px; font-size: 11px; font-weight: 900; letter-spacing: 1px; text-transform: uppercase; margin-bottom: 20px; }
        html[data-theme="dark"] .eyebrow { color: var(--yellow); }
        .eyebrow-dot { width: 7px; height: 7px; border-radius: 50%; background: var(--yellow); box-shadow: 0 0 0 5px rgba(255,184,0,.12); }
        .hero h1 { font-size: clamp(38px, 6vw, 72px); line-height: .98; letter-spacing: -3.5px; font-weight: 900; max-width: 800px; }
        .hero h1 span { color: var(--yellow); }
        .hero-description { margin-top: 22px; max-width: 650px; color: var(--muted); font-size: clamp(16px, 2vw, 19px); line-height: 1.7; }
        .hero-actions { display: flex; flex-wrap: wrap; gap: 12px; margin-top: 30px; }
        
        .btn { min-height: 54px; display: inline-flex; justify-content: center; align-items: center; padding: 0 23px; border-radius: 13px; border: 1px solid transparent; font-weight: 900; font-size: 14px; cursor: pointer; transition: .25s ease; }
        .btn-primary { background: var(--yellow); color: #000; box-shadow: 0 10px 30px rgba(255,184,0,.18); }
        .btn-primary:hover { background: #fff; transform: translateY(-2px); }
        .btn-secondary { background: var(--surface); color: var(--text); border-color: var(--border); }
        .btn-secondary:hover { transform: translateY(-2px); border-color: rgba(255,184,0,.45); }
        
        .trust-row { display: flex; flex-wrap: wrap; gap: 18px; margin-top: 25px; color: var(--muted); font-size: 12px; font-weight: 600; }
        .trust-row span { display: flex; align-items: center; gap: 7px; }
        .trust-check { color: var(--yellow); font-weight: 900; }

        /* QUICK CALCULATOR (HERO) */
        .hero-card { position: relative; background: var(--surface); border: 1px solid var(--border); border-radius: 25px; padding: 28px; box-shadow: var(--shadow); }
        .hero-card::after { content: ""; position: absolute; inset: 0; border-radius: inherit; pointer-events: none; box-shadow: inset 0 0 0 1px rgba(255,184,0,.05); }
        .card-top { display: flex; align-items: center; justify-content: space-between; gap: 15px; margin-bottom: 24px; }
        .card-title { font-size: 18px; font-weight: 900; }
        .card-mini { color: var(--yellow); font-size: 11px; font-weight: 900; text-transform: uppercase; letter-spacing: .7px; }
        .mini-option { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 14px; }
        .mini-btn { min-height: 58px; border: 1px solid var(--border); border-radius: 13px; background: var(--surface-2); color: var(--muted); cursor: pointer; font-weight: 800; transition:.2s; }
        .mini-btn.active { background: var(--yellow); border-color: var(--yellow); color: #000; }
        .mini-label { display: block; margin-bottom: 8px; color: var(--muted); font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: .7px; }
        .mini-input, .mini-select { width: 100%; min-height: 54px; padding: 0 15px; border-radius: 12px; border: 1px solid var(--border); background: var(--surface-2); color: var(--text); outline: none; font-weight: 700; transition:.2s; }
        .mini-input:focus, .mini-select:focus { border-color: var(--yellow); }
        
        .quick-result { display: none; margin-top: 18px; padding: 18px; border: 1px solid rgba(255,184,0,.4); border-radius: 14px; background: rgba(255,184,0,.07); cursor:pointer; transition:.2s; text-align: left; }
        .quick-result:hover { background: rgba(255,184,0,.12); transform:translateY(-2px); }
        .quick-result small { color: var(--muted); font-weight: 600; }
        .quick-price { margin-top: 4px; font-size: 27px; font-weight: 900; }
        .quick-price span { color: var(--yellow); }

        /* QUICK BREAKDOWN BARS */
        .quick-bd-item { margin-bottom: 10px; }
        .quick-bd-labels { display: flex; justify-content: space-between; font-size: 10px; color: var(--muted); font-weight: 600; margin-bottom: 4px; }
        .quick-bd-labels strong { color: var(--text); }
        .quick-bd-track { width: 100%; height: 4px; background: rgba(0,0,0,0.08); border-radius: 4px; overflow: hidden; }
        html[data-theme="dark"] .quick-bd-track { background: rgba(255,255,255,0.1); }
        .quick-bd-fill { height: 100%; background: var(--yellow); border-radius: 4px; }

        /* STATS */
        .stats { padding: 12px 0 60px; }
        .stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); border: 1px solid var(--border); border-radius: 18px; overflow: hidden; background: var(--surface); }
        .stat { padding: 24px; text-align: center; border-right: 1px solid var(--border); }
        .stat:last-child { border-right: 0; }
        .stat strong { display: block; font-size: 28px; font-weight: 900; }
        .stat span { color: var(--muted); font-size: 12px; font-weight: 600; }

        /* SECTION HEADERS */
        section { padding: 80px 0; }
        .section-head { max-width: 750px; margin-bottom: 35px; }
        .section-kicker { color: var(--yellow-dark); font-size: 11px; font-weight: 900; text-transform: uppercase; letter-spacing: 1.5px; margin-bottom: 9px; }
        html[data-theme="dark"] .section-kicker { color: var(--yellow); }
        .section-head h2 { font-size: clamp(30px, 5vw, 48px); line-height: 1.05; letter-spacing: -2px; font-weight: 900; }
        .section-head p { margin-top: 13px; color: var(--muted); line-height: 1.7; }

        /* SERVICES GRID */
        .service-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; }
        .service-card { background: var(--surface); border: 1px solid var(--border); border-radius: 18px; padding: 25px; transition: .25s ease; }
        .service-card:hover { transform: translateY(-4px); border-color: rgba(255,184,0,.35); box-shadow: var(--shadow); }
        .service-icon { width: 45px; height: 45px; display: grid; place-items: center; border-radius: 12px; background: rgba(255,184,0,.11); color: var(--yellow); font-size: 20px; margin-bottom: 18px; }
        .service-card h3 { font-size: 17px; font-weight: 900; margin-bottom: 8px; }
        .service-card p { color: var(--muted); font-size: 13px; line-height: 1.7; }

        /* HOW IT WORKS */
        .steps { display: grid; grid-template-columns: repeat(3, 1fr); gap: 18px; }
        .step { position: relative; background: var(--surface); border: 1px solid var(--border); border-radius: 18px; padding: 27px; }
        .step-number { width: 42px; height: 42px; display: grid; place-items: center; background: var(--yellow); color: #000; border-radius: 12px; font-weight: 900; margin-bottom: 18px; }
        .step h3 { font-size: 17px; font-weight: 900; margin-bottom: 8px; }
        .step p { color: var(--muted); font-size: 13px; line-height: 1.7; }

        /* MAIN CTA BOX */
        .cta-box { position: relative; overflow: hidden; background: #050505; color: #fff; border-radius: 25px; padding: 48px; border: 1px solid rgba(255,255,255,.08); }
        .cta-box::before { content: ""; position: absolute; width: 450px; height: 450px; right: -200px; top: -260px; background: rgba(255,184,0,.17); filter: blur(65px); border-radius: 50%; pointer-events: none;}
        .cta-content { position: relative; z-index: 2; max-width: 720px; }
        .cta-box h2 { font-size: clamp(30px, 5vw, 48px); line-height: 1.05; letter-spacing: -2px; font-weight: 900; }
        .cta-box h2 span { color: var(--yellow); }
        .cta-box p { color: #a1a1aa; margin-top: 15px; line-height: 1.7; }
        .cta-actions { display: flex; flex-wrap: wrap; gap: 11px; margin-top: 25px; }

        /* FAQ */
        .faq { max-width: 850px; }
        .faq-item { border-bottom: 1px solid var(--border); }
        .faq-question { width: 100%; display: flex; align-items: center; justify-content: space-between; gap: 20px; padding: 22px 0; background: none; border: 0; color: var(--text); text-align: left; cursor: pointer; font-size: 15px; font-weight: 800; }
        .faq-question span:last-child { color: var(--yellow); font-size: 21px; }
        .faq-answer { display: none; padding: 0 0 22px; color: var(--muted); font-size: 14px; line-height: 1.8; }
        .faq-item.open .faq-answer { display: block; }

        /* FOOTER */
        footer { padding: 35px 0; border-top: 1px solid var(--border); }
        .footer-inner { display: flex; align-items: center; justify-content: space-between; gap: 20px; }
        .footer-copy { color: var(--muted); font-size: 12px; }
        .footer-brand { font-weight: 900; }
        .footer-brand span { color: var(--yellow); }

        .mobile-cta { display: none; }

        /* RESPONSIVE */
        @media (max-width: 950px) {
            .hero-grid { grid-template-columns: 1fr; }
            .hero-card { max-width: 650px; }
            .service-grid { grid-template-columns: 1fr 1fr; }
            .steps { grid-template-columns: 1fr; }
        }

        @media (max-width: 720px) {
            .container { width: min(calc(100% - 24px), var(--max)); }
            .nav-inner { min-height: 65px; }
            .nav-links { display: none; }
            .mobile-nav-btn { display: block; }
            .hero { padding: 46px 0 35px; }
            .hero h1 { font-size: clamp(38px, 11vw, 54px); letter-spacing: -2.5px; }
            .hero-description { font-size: 15px; line-height: 1.65; }
            .hero-actions { display: grid; grid-template-columns: 1fr; }
            .btn { width: 100%; min-height: 56px; }
            .trust-row { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
            .hero-card { padding: 19px; border-radius: 19px; }
            .card-title { font-size: 17px; }
            .stats { padding-bottom: 35px; }
            .stats-grid { grid-template-columns: 1fr 1fr; }
            .stat { padding: 20px 10px; border-right: 1px solid var(--border); border-bottom: 1px solid var(--border); }
            .stat:nth-child(2) { border-right: 0; }
            .stat:nth-child(3), .stat:nth-child(4) { border-bottom: 0; }
            .stat strong { font-size: 23px; }
            section { padding: 58px 0; }
            .section-head h2 { font-size: 31px; letter-spacing: -1.5px; }
            .service-grid { grid-template-columns: 1fr; }
            .service-card { padding: 22px; }
            .cta-box { padding: 30px 22px; border-radius: 20px; }
            .cta-box h2 { font-size: 31px; }
            .footer-inner { flex-direction: column; align-items: flex-start; }
            
            .mobile-cta { display: block; position: fixed; left: 12px; right: 12px; bottom: 12px; z-index: 999; }
            .mobile-cta a { display: flex; align-items: center; justify-content: center; min-height: 56px; border-radius: 14px; background: var(--yellow); color: #000; font-size: 14px; font-weight: 900; box-shadow: 0 12px 30px rgba(0,0,0,.25); }
            body { padding-bottom: 75px; }
        }

        @media (max-width: 420px) {
            .mini-option { grid-template-columns: 1fr; }
            .trust-row { grid-template-columns: 1fr; }
            .hero-card { padding: 16px; }
            .hero h1 { font-size: 37px; }
        }
    </style>

    <!-- STRUCTURED DATA (ESCAPED) -->
    <script type="application/ld+json">
    {
        "@@context": "https://schema.org",
        "@@type": "WebSite",
        "name": "WORKSİTEM",
        "url": "https://worksitem.com/",
        "description": "İnşaat ve tadilat maliyet hesaplama ve proje talep platformu.",
        "publisher": {
            "@@type": "Organization",
            "name": "Kainta İnşaat",
            "url": "https://kainta.com"
        }
    }
    </script>

    <script type="application/ld+json">
    {
        "@@context": "https://schema.org",
        "@@type": "SoftwareApplication",
        "name": "WORKSİTEM Maliyet Hesaplama",
        "applicationCategory": "BusinessApplication",
        "operatingSystem": "Web",
        "url": "https://worksitem.com/maliyet-hesapla",
        "description": "İnşaat ve tadilat projeleri için yaklaşık maliyet hesaplama aracı.",
        "provider": {
            "@@type": "Organization",
            "name": "Kainta İnşaat",
            "url": "https://kainta.com"
        }
    }
    </script>

    <script type="application/ld+json">
    {
        "@@context": "https://schema.org",
        "@@type": "FAQPage",
        "mainEntity": [
            {
                "@@type": "Question",
                "name": "İnşaat maliyeti nasıl hesaplanır?",
                "acceptedAnswer": {
                    "@@type": "Answer",
                    "text": "İnşaat maliyeti; yapı türü, toplam alan, yapı sınıfı, kalite seviyesi, mekanik ve elektrik tesisatları gibi birçok değişkene göre yaklaşık olarak hesaplanabilir."
                }
            },
            {
                "@@type": "Question",
                "name": "Tadilat maliyeti nasıl hesaplanır?",
                "acceptedAnswer": {
                    "@@type": "Answer",
                    "text": "Tadilat maliyeti yapılacak işlerin kapsamı ve alanına göre değişir. Mutfak, banyo, zemin, boya, elektrik, su tesisatı, bölme duvar ve diğer imalatlar toplam maliyeti etkiler."
                }
            },
            {
                "@@type": "Question",
                "name": "WORKSİTEM hesaplama sonucu kesin fiyat mıdır?",
                "acceptedAnswer": {
                    "@@type": "Answer",
                    "text": "Hesaplama sonucu yaklaşık bütçe göstergesidir. Kesin teklif için proje, metraj, mahal ve malzeme detaylarının incelenmesi gerekir."
                }
            }
        ]
    }
    </script>
</head>

<body>

<header class="navbar">
    <div class="container nav-inner">

        <a href="/" class="brand" aria-label="WORKSİTEM ana sayfa">
            <img src="/brand/worksitem-logo.png" alt="WORKSİTEM">
        </a>

        <nav class="nav-links" aria-label="Ana navigasyon">
            <a class="nav-link" href="/araclar">Araçlar</a>
            <a class="nav-link" href="/maliyet-hesapla">Maliyet Hesapla</a>
            <a class="nav-link" href="#nasil-calisir">Nasıl Çalışır?</a>
            <a class="nav-link" href="#sss">SSS</a>
            <a class="nav-link" href="https://kainta.com" target="_blank">Kainta İnşaat</a>
        </nav>

        <div style="display:flex;gap:8px;align-items:center;">
            <button class="theme-btn" id="themeBtn" type="button" aria-label="Tema değiştir">☾</button>
        </div>

    </div>
</header>


<main>

    <!-- HERO -->
    <section class="hero">
        <div class="container hero-grid">

            <div>

                <div class="eyebrow">
                    <span class="eyebrow-dot"></span>
                    2026 İnşaat & Tadilat Maliyet Hesaplama
                </div>

                <h1>
                    Projenizin gerçekçi
                    <span>maliyetini</span>
                    daha başlamadan görün.
                </h1>

                <p class="hero-description">
                    Ev, villa, iş yeri, ofis, mağaza veya komple tadilat projeniz için
                    yaklaşık bütçenizi saniyeler içinde hesaplayın.
                    Sonrasında projenizi profesyonel teklife dönüştürün.
                </p>

                <div class="hero-actions">
                    <a href="/maliyet-hesapla" class="btn btn-primary">
                        Maliyeti Hemen Hesapla →
                    </a>

                    <a href="#nasil-calisir" class="btn btn-secondary">
                        Nasıl Çalışıyor?
                    </a>
                </div>

                <div class="trust-row">
                    <span><b class="trust-check">✓</b> Ücretsiz ön hesaplama</span>
                    <span><b class="trust-check">✓</b> İnşaat + tadilat</span>
                    <span><b class="trust-check">✓</b> KDV ayrı gösterilir</span>
                    <span><b class="trust-check">✓</b> Profesyonel teklif talebi</span>
                </div>

            </div>


            <!-- QUICK CALCULATOR -->
            <div class="hero-card">

                <div class="card-top">
                    <div>
                        <div class="card-mini">Hızlı Hesaplama</div>
                        <div class="card-title">Yaklaşık bütçenizi öğrenin</div>
                    </div>
                    <div style="font-size:25px; color:var(--yellow); font-weight:900;">₺</div>
                </div>

                <div class="mini-option">
                    <button class="mini-btn active" id="quickInsaat" type="button">Sıfırdan İnşaat</button>
                    <button class="mini-btn" id="quickTadilat" type="button">Komple Tadilat</button>
                </div>

                <label class="mini-label" for="quickArea">Toplam alan (m²)</label>
                <input id="quickArea" class="mini-input" type="number" min="1" placeholder="Örn. 150">

                <div id="quickInsaatFields">
                    <label class="mini-label" for="quickClass" style="margin-top:14px;">Yapı sınıfı (2026 ÇŞB)</label>
                    <select id="quickClass" class="mini-select">
                        <option value="3A">3A — Konutlar / düşük katlı</option>
                        <option value="3B">3B — Konutlar (21.5m)</option>
                        <option value="4A">4A — Üst segment konut</option>
                        <option value="4B">4B — İş merkezi / banka</option>
                        <option value="4C">4C — Büyük yapılar</option>
                    </select>
                </div>

                <div id="quickTadilatFields" style="display:none;">
                    <label class="mini-label" for="quickTadilatType" style="margin-top:14px;">Tadilat Kalitesi</label>
                    <select id="quickTadilatType" class="mini-select">
                        <option value="T1">Ekonomik (Boya, temizlik, ufak tamir)</option>
                        <option value="T2">Standart (Mutfak, Banyo, Tesisat, Zemin)</option>
                        <option value="T3">Premium (A'dan Z'ye mimari lüks yenileme)</option>
                    </select>
                </div>

                <button id="quickCalculate" type="button" class="btn btn-primary" style="width:100%;margin-top:15px;">
                    Yaklaşık Maliyeti Gör →
                </button>

                <div id="quickResult" class="quick-result">
                    <small style="margin-bottom:4px; display:block;">Tahmini anahtar teslim bütçe</small>
                    <div class="quick-price"><span id="quickTotal">0 ₺</span></div>
                    
                    <!-- KIRILIMLAR BURADA (HIZLI HESAPLAMA) -->
                    <div id="quickBreakdown" style="display:none; margin-top:15px; border-top: 1px dashed rgba(255,184,0,.3); padding-top: 15px;">
                        <div style="font-size:10px; color:var(--yellow); font-weight:800; text-transform:uppercase; letter-spacing:1px; margin-bottom:12px;">Maliyet Kırılımı (Yaklaşık)</div>
                        
                        <div class="quick-bd-item">
                            <div class="quick-bd-labels"><span>Kaba İnşaat (%40)</span><strong id="qKaba">0 ₺</strong></div>
                            <div class="quick-bd-track"><div class="quick-bd-fill" style="width: 40%; background: #71717a;"></div></div>
                        </div>
                        <div class="quick-bd-item">
                            <div class="quick-bd-labels"><span>İnce İşler (%30)</span><strong id="qInce">0 ₺</strong></div>
                            <div class="quick-bd-track"><div class="quick-bd-fill" style="width: 30%;"></div></div>
                        </div>
                        <div class="quick-bd-item">
                            <div class="quick-bd-labels"><span>Mekanik (%15)</span><strong id="qMekanik">0 ₺</strong></div>
                            <div class="quick-bd-track"><div class="quick-bd-fill" style="width: 15%;"></div></div>
                        </div>
                        <div class="quick-bd-item">
                            <div class="quick-bd-labels"><span>Elektrik (%10)</span><strong id="qElektrik">0 ₺</strong></div>
                            <div class="quick-bd-track"><div class="quick-bd-fill" style="width: 10%;"></div></div>
                        </div>
                        <div class="quick-bd-item">
                            <div class="quick-bd-labels"><span>Çatı & Diğer (%5)</span><strong id="qCati">0 ₺</strong></div>
                            <div class="quick-bd-track"><div class="quick-bd-fill" style="width: 5%; background: #71717a;"></div></div>
                        </div>
                    </div>

                    <small style="display:block; margin-top:15px; color:var(--text); font-weight:700;">Detaylı hesaplama ve teklif için tıklayın.</small>
                </div>

            </div>

        </div>
    </section>


    <!-- STATS -->
    <section class="stats">
        <div class="container">
            <div class="stats-grid">
                <div class="stat">
                    <strong>m²</strong>
                    <span>Alan bazlı hesaplama</span>
                </div>
                <div class="stat">
                    <strong>3+</strong>
                    <span>Tadilat seviyesi</span>
                </div>
                <div class="stat">
                    <strong>5+</strong>
                    <span>ÇŞB Sınıfı seçeneği</span>
                </div>
                <div class="stat">
                    <strong>7/24</strong>
                    <span>Otonom erişim</span>
                </div>
            </div>
        </div>
    </section>


    <!-- WHAT IS WORKSITEM -->
    <section>
        <div class="container">

            <div class="section-head">
                <div class="section-kicker">WORKSİTEM nedir?</div>
                <h2>İnşaat ve tadilat kararını rakamlarla verin.</h2>
                <p>
                    WORKSİTEM, inşaat ve tadilat projelerinde ilk bütçe kararını
                    kolaylaştırmak için tasarlanmış dijital maliyet hesaplama platformudur.
                </p>
            </div>

            <div class="service-grid">

                <article class="service-card">
                    <div class="service-icon">₺</div>
                    <h3>İnşaat Maliyeti</h3>
                    <p>
                        Sıfırdan konut, villa, iş yeri ve farklı yapı türleri için
                        2026 ÇŞB rayiçleriyle yaklaşık anahtar teslim bütçenizi hesaplayın.
                    </p>
                </article>

                <article class="service-card">
                    <div class="service-icon">⌂</div>
                    <h3>Komple Tadilat</h3>
                    <p>
                        Mutfak, banyo, zemin, boya, tesisat, bölme duvar ve diğer
                        tadilat kalemlerini kapsayan yaklaşık bütçeyi görün.
                    </p>
                </article>

                <article class="service-card">
                    <div class="service-icon">✓</div>
                    <h3>Profesyonel Teklif</h3>
                    <p>
                        Ön bütçenizi oluşturduktan sonra projenizi Kainta İnşaat
                        ekibine ileterek yerinde keşif ve detaylı teklif sürecini başlatın.
                    </p>
                </article>

            </div>

        </div>
    </section>


    <!-- HOW IT WORKS -->
    <section id="nasil-calisir">
        <div class="container">

            <div class="section-head">
                <div class="section-kicker">Nasıl çalışır?</div>
                <h2>Üç adımda projenizin bütçesini öğrenin.</h2>
                <p>
                    Gereksiz uzun formlar yerine önce yaklaşık bütçenizi görün,
                    ardından gerçekten teklif almak istediğinizde projenizi detaylandırın.
                </p>
            </div>

            <div class="steps">

                <article class="step">
                    <div class="step-number">01</div>
                    <h3>Proje tipinizi seçin</h3>
                    <p>Sıfırdan inşaat veya komple tadilat seçeneklerinden projenize uygun olanı belirleyin.</p>
                </article>

                <article class="step">
                    <div class="step-number">02</div>
                    <h3>Alan ve detayları girin</h3>
                    <p>Yapı alanı, yapı sınıfı veya tadilat kapsamı gibi temel bilgileri girerek yaklaşık bütçeyi hesaplayın.</p>
                </article>

                <article class="step">
                    <div class="step-number">03</div>
                    <h3>Teklif sürecini başlatın</h3>
                    <p>Hesaplama sonucunuzdan sonra WhatsApp veya Form ile Kainta İnşaat'tan profesyonel teklif isteyin.</p>
                </article>

            </div>

        </div>
    </section>


    <!-- MAIN CTA -->
    <section>
        <div class="container">

            <div class="cta-box">
                <div class="cta-content">
                    <div class="section-kicker">Ücretsiz ön hesaplama</div>
                    <h2>Projeniz için <span>kaç TL gerekir?</span></h2>
                    <p>
                        Alanınızı ve projenizin temel özelliklerini girin.
                        WORKSİTEM yaklaşık bütçenizi saniyeler içinde hesaplasın.
                        Uygun görürseniz Kainta İnşaat'tan teklif isteyin.
                    </p>

                    <div class="cta-actions">
                        <a href="/maliyet-hesapla" class="btn btn-primary">
                            Maliyetimi Hesapla →
                        </a>
                        <a href="https://kainta.com" target="_blank" class="btn btn-secondary" style="background:#111;color:#fff;border-color:#333;">
                            Kainta İnşaat
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </section>


    <!-- SEO CONTENT -->
    <section>
        <div class="container">

            <div class="section-head">
                <div class="section-kicker">İnşaat maliyeti rehberi</div>
                <h2>İnşaat maliyetini etkileyen temel faktörler</h2>
                <p>
                    Bir yapının toplam maliyeti yalnızca metrekare ile belirlenmez.
                    Yapının kullanım amacı, yapı sınıfı, kat sayısı, mimari özellikleri,
                    malzeme kalitesi, mekanik ve elektrik tesisatları ile proje kapsamı
                    toplam bütçeyi doğrudan etkiler.
                </p>
            </div>

            <div class="service-grid">
                <article class="service-card">
                    <h3>Yapı türü</h3>
                    <p>Betonarme, çelik, ahşap veya prefabrik yapıların imalat yöntemleri ve maliyet bileşenleri farklıdır.</p>
                </article>
                <article class="service-card">
                    <h3>Toplam alan</h3>
                    <p>Brüt inşaat alanı büyüdükçe beton, demir, kalıp, duvar, kaplama ve tesisat metrajları da değişir.</p>
                </article>
                <article class="service-card">
                    <h3>Malzeme kalitesi</h3>
                    <p>Ekonomik, standart, premium ve lüks malzeme seçimleri toplam anahtar teslim bütçeyi önemli ölçüde etkileyebilir.</p>
                </article>
                <article class="service-card">
                    <h3>Mekanik tesisat</h3>
                    <p>Isıtma, soğutma, sıhhi tesisat, havalandırma ve diğer mekanik sistemler proje kapsamına göre değişir.</p>
                </article>
                <article class="service-card">
                    <h3>Elektrik tesisatı</h3>
                    <p>Elektrik altyapısı, aydınlatma, zayıf akım ve otomasyon ihtiyaçları bütçeye dahil edilmelidir.</p>
                </article>
                <article class="service-card">
                    <h3>Proje kapsamı</h3>
                    <p>Peyzaj, havuz, otopark, özel imalatlar ve çevre düzenlemesi gibi işler ayrıca maliyet oluşturabilir.</p>
                </article>
            </div>

        </div>
    </section>


    <!-- FAQ -->
    <section id="sss">
        <div class="container">

            <div class="section-head">
                <div class="section-kicker">Sık sorulan sorular</div>
                <h2>Maliyet hesaplama hakkında merak edilenler</h2>
            </div>

            <div class="faq">

                <div class="faq-item">
                    <button class="faq-question" type="button">
                        <span>İnşaat maliyeti nasıl hesaplanır?</span>
                        <span>+</span>
                    </button>
                    <div class="faq-answer">
                        Yaklaşık inşaat maliyeti; toplam yapı alanı,
                        yapı türü, yapı sınıfı, kalite seviyesi ve proje
                        kapsamı gibi değişkenler kullanılarak hesaplanabilir.
                    </div>
                </div>

                <div class="faq-item">
                    <button class="faq-question" type="button">
                        <span>Tadilat maliyeti nasıl hesaplanır?</span>
                        <span>+</span>
                    </button>
                    <div class="faq-answer">
                        Tadilat maliyeti yapılacak işlerin kapsamına ve
                        alanına göre değişir. Mutfak, banyo, zemin,
                        boya, tesisat, elektrik, bölme duvar ve diğer
                        imalatların kapsamı toplam bütçeyi etkiler.
                    </div>
                </div>

                <div class="faq-item">
                    <button class="faq-question" type="button">
                        <span>Hesaplama sonucu kesin teklif midir?</span>
                        <span>+</span>
                    </button>
                    <div class="faq-answer">
                        Hayır. WORKSİTEM üzerindeki sonuç yaklaşık bütçe
                        göstergesidir. Kesin teklif için proje, mahal,
                        metraj, malzeme seçimi ve uygulama koşullarının
                        profesyonel olarak incelenmesi gerekir.
                    </div>
                </div>

                <div class="faq-item">
                    <button class="faq-question" type="button">
                        <span>Hesaplama ücretsiz mi?</span>
                        <span>+</span>
                    </button>
                    <div class="faq-answer">
                        Evet. İlk yaklaşık maliyet hesabını tamamen ücretsiz
                        olarak yapabilirsiniz. Üyelik gerektirmez.
                    </div>
                </div>

                <div class="faq-item">
                    <button class="faq-question" type="button">
                        <span>Teklif almak için ne yapmalıyım?</span>
                        <span>+</span>
                    </button>
                    <div class="faq-answer">
                        Öncelikle maliyet hesaplama aracını kullanın.
                        Sonuç ekranından iletişim bilgilerinizi bırakarak veya doğrudan WhatsApp butonuna tıklayarak
                        Kainta İnşaat'tan proje değerlendirmesi ve teklif talep edebilirsiniz.
                    </div>
                </div>

            </div>

        </div>
    </section>

</main>


<footer>
    <div class="container footer-inner">

        <div class="footer-copy">
            © 2026 WORKSİTEM — İnşaat & Tadilat Maliyet Hesaplama Platformu
        </div>

        <div class="footer-brand">
            Kainta İnşaat <span>İnşaat</span>
        </div>

    </div>
</footer>


<!-- MOBILE CTA -->
<div class="mobile-cta">
    <a href="/maliyet-hesapla">
        ₺ Maliyeti Hemen Hesapla →
    </a>
</div>


<script>
(function () {

    /* =========================
       THEME
    ========================= */
    const html = document.documentElement;
    const themeBtn = document.getElementById('themeBtn');
    const savedTheme = localStorage.getItem('worksitem-theme');

    if (savedTheme === 'dark') {
        html.setAttribute('data-theme', 'dark');
        themeBtn.textContent = '☀';
    } else {
        html.setAttribute('data-theme', 'light');
        themeBtn.textContent = '☾';
    }

    themeBtn.addEventListener('click', function () {
        const current = html.getAttribute('data-theme');
        if (current === 'dark') {
            html.setAttribute('data-theme', 'light');
            localStorage.setItem('worksitem-theme', 'light');
            themeBtn.textContent = '☾';
        } else {
            html.setAttribute('data-theme', 'dark');
            localStorage.setItem('worksitem-theme', 'dark');
            themeBtn.textContent = '☀';
        }
    });


    /* =========================
       QUICK CALCULATOR (Ana Sayfa)
    ========================= */
    let quickMode = 'insaat';
    const quickInsaat = document.getElementById('quickInsaat');
    const quickTadilat = document.getElementById('quickTadilat');
    const quickInsaatFields = document.getElementById('quickInsaatFields');
    const quickTadilatFields = document.getElementById('quickTadilatFields');

    quickInsaat.addEventListener('click', function () {
        quickMode = 'insaat';
        quickInsaat.classList.add('active');
        quickTadilat.classList.remove('active');
        quickInsaatFields.style.display = 'block';
        quickTadilatFields.style.display = 'none';
    });

    quickTadilat.addEventListener('click', function () {
        quickMode = 'tadilat';
        quickTadilat.classList.add('active');
        quickInsaat.classList.remove('active');
        quickInsaatFields.style.display = 'none';
        quickTadilatFields.style.display = 'block';
    });

    // 2026 ÇŞB ve GİZLİ ÇARPAN MATEMATİĞİ (Ana Sayfadaki Hızlı Hesaplayıcı için de geçerli)
    const CSB_PRICES = {
        "3A": 19800,
        "3B": 21050,
        "4A": 26450,
        "4B": 33900,
        "4C": 40500
    };

    const TADILAT_PRICES = {
        "T1": 3200, // Ekonomik
        "T2": 6800, // Standart
        "T3": 12500 // Premium
    };

    const STEALTH_MULTIPLIER = 1.40;

    document.getElementById('quickCalculate').addEventListener('click', function () {
        const area = parseFloat(document.getElementById('quickArea').value);
        if (!area || area <= 0) {
            document.getElementById('quickArea').focus();
            return;
        }

        let basePrice = 0;
        if (quickMode === 'insaat') {
            const cls = document.getElementById('quickClass').value;
            basePrice = CSB_PRICES[cls] || CSB_PRICES["3A"];
        } else {
            const type = document.getElementById('quickTadilatType').value;
            basePrice = TADILAT_PRICES[type] || TADILAT_PRICES["T2"];
        }

        const unitPrice = basePrice * STEALTH_MULTIPLIER;
        const total = unitPrice * area;

        // Kırılımlar
        const valKaba = total * 0.40;
        const valInce = total * 0.30;
        const valMekanik = total * 0.15;
        const valElektrik = total * 0.10;
        const valCati = total * 0.05;

        const fmt = new Intl.NumberFormat('tr-TR', { maximumFractionDigits: 0 });

        document.getElementById('quickTotal').textContent = fmt.format(total) + ' ₺';
        
        document.getElementById('qKaba').textContent = fmt.format(valKaba) + ' ₺';
        document.getElementById('qInce').textContent = fmt.format(valInce) + ' ₺';
        document.getElementById('qMekanik').textContent = fmt.format(valMekanik) + ' ₺';
        document.getElementById('qElektrik').textContent = fmt.format(valElektrik) + ' ₺';
        document.getElementById('qCati').textContent = fmt.format(valCati) + ' ₺';

        document.getElementById('quickBreakdown').style.display = 'block';
        document.getElementById('quickResult').style.display = 'block';
    });

    /* =========================
       FAQ (Sıkça Sorulan Sorular)
    ========================= */
    document.querySelectorAll('.faq-question').forEach(function (button) {
        button.addEventListener('click', function () {
            const item = button.closest('.faq-item');
            const isOpen = item.classList.contains('open');

            document.querySelectorAll('.faq-item').forEach(function (other) {
                other.classList.remove('open');
                const icon = other.querySelector('.faq-question span:last-child');
                if (icon) icon.textContent = '+';
            });

            if (!isOpen) {
                item.classList.add('open');
                const icon = button.querySelector('span:last-child');
                if (icon) icon.textContent = '−';
            }
        });
    });

    /* =========================
       QUICK RESULT -> YÖNLENDİRME
    ========================= */
    document.getElementById('quickResult').addEventListener('click', function () {
        window.location.href = '/maliyet-hesapla';
    });

})();
</script>

</body>
</html>
