<!doctype html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,viewport-fit=cover">

    <title>İnşaat ve Tadilat Maliyet Hesaplama | WORKSİTEM</title>

    <meta name="description" content="İnşaat ve tadilat maliyetinizi hızlıca hesaplayın. Yapı sınıfı, metraj ve proje bilgilerinize göre yaklaşık maliyet oluşturun ve Kainta İnşaat'tan teklif alın.">

    <meta name="robots" content="index,follow,max-image-preview:large">
    <meta name="theme-color" content="#f5b400">

    <link rel="canonical" href="https://worksitem.com/">

    <link rel="icon" href="/favicon.ico" type="image/x-icon">

    <meta property="og:type" content="website">
    <meta property="og:title" content="İnşaat ve Tadilat Maliyet Hesaplama | WORKSİTEM">
    <meta property="og:description" content="Projenizin yaklaşık maliyetini hesaplayın, keşif oluşturun ve profesyonel teklif alın.">
    <meta property="og:url" content="https://worksitem.com/">
    <meta property="og:site_name" content="WORKSİTEM">

    <script>
        (function () {
            const saved = localStorage.getItem('worksitem-theme');
            const systemDark = window.matchMedia &&
                window.matchMedia('(prefers-color-scheme: dark)').matches;

            document.documentElement.dataset.theme =
                saved || (systemDark ? 'dark' : 'light');
        })();
    </script>

    <style>
        :root {
            --bg: #f5f5f5;
            --surface: #ffffff;
            --surface-2: #f0f0f0;
            --text: #111111;
            --muted: #626262;
            --border: #e2e2e2;
            --accent: #f5b400;
            --accent-strong: #e2a500;
            --accent-soft: rgba(245,180,0,.13);
            --shadow: 0 20px 60px rgba(0,0,0,.08);
            --radius: 22px;
        }

        html[data-theme="dark"] {
            --bg: #050505;
            --surface: #101010;
            --surface-2: #171717;
            --text: #ffffff;
            --muted: #a1a1aa;
            --border: rgba(255,255,255,.10);
            --accent: #ffb800;
            --accent-strong: #ffc52c;
            --accent-soft: rgba(255,184,0,.12);
            --shadow: 0 25px 70px rgba(0,0,0,.35);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            min-height: 100vh;
            background: var(--bg);
            color: var(--text);
            font-family:
                Inter,
                ui-sans-serif,
                system-ui,
                -apple-system,
                BlinkMacSystemFont,
                "Segoe UI",
                sans-serif;
            -webkit-font-smoothing: antialiased;
            text-rendering: optimizeLegibility;
        }

        a {
            color: inherit;
            text-decoration: none;
        }

        button {
            font: inherit;
        }

        img {
            max-width: 100%;
            display: block;
        }

        .container {
            width: min(1180px, calc(100% - 32px));
            margin-inline: auto;
        }

        /* NAV */

        .nav {
            position: sticky;
            top: 0;
            z-index: 100;
            border-bottom: 1px solid var(--border);
            background: color-mix(in srgb, var(--bg) 90%, transparent);
            backdrop-filter: blur(18px);
        }

        .nav-inner {
            min-height: 76px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 18px;
        }

        .brand {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            font-size: 20px;
            font-weight: 900;
            letter-spacing: -.5px;
        }

        .brand img {
            width: 40px;
            height: 40px;
            object-fit: cover;
            border-radius: 9px;
        }

        .brand span {
            color: var(--accent);
        }

        .nav-actions {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .nav-link {
            display: none;
            font-size: 14px;
            font-weight: 800;
            color: var(--muted);
        }

        .theme-btn {
            width: 42px;
            height: 42px;
            border: 1px solid var(--border);
            border-radius: 12px;
            background: var(--surface);
            color: var(--text);
            cursor: pointer;
            display: grid;
            place-items: center;
            font-size: 18px;
        }

        .nav-cta {
            display: none;
            background: var(--accent);
            color: #000;
            padding: 12px 17px;
            border-radius: 12px;
            font-size: 13px;
            font-weight: 900;
        }

        /* HERO */

        .hero {
            padding: 48px 0 28px;
        }

        .hero-grid {
            display: grid;
            gap: 30px;
            align-items: center;
        }

        .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 12px;
            border-radius: 100px;
            background: var(--accent-soft);
            color: var(--accent-strong);
            border: 1px solid rgba(245,180,0,.2);
            font-size: 11px;
            font-weight: 900;
            letter-spacing: 1px;
            text-transform: uppercase;
            margin-bottom: 18px;
        }

        .eyebrow-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: var(--accent);
            box-shadow: 0 0 0 5px var(--accent-soft);
        }

        .hero h1 {
            max-width: 780px;
            font-size: clamp(38px, 8vw, 76px);
            line-height: .98;
            letter-spacing: -3px;
            font-weight: 950;
        }

        .hero h1 span {
            color: var(--accent);
        }

        .hero-text {
            max-width: 650px;
            margin-top: 20px;
            color: var(--muted);
            font-size: clamp(16px, 2vw, 20px);
            line-height: 1.65;
        }

        .hero-actions {
            display: grid;
            gap: 12px;
            margin-top: 28px;
        }

        .btn {
            min-height: 56px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 15px 22px;
            border-radius: 14px;
            font-size: 15px;
            font-weight: 900;
            border: 1px solid transparent;
            transition: transform .2s ease, box-shadow .2s ease, background .2s ease;
        }

        .btn-primary {
            background: var(--accent);
            color: #000;
            box-shadow: 0 12px 35px rgba(245,180,0,.20);
        }

        .btn-secondary {
            background: var(--surface);
            border-color: var(--border);
            color: var(--text);
        }

        .btn:hover {
            transform: translateY(-2px);
        }

        .trust-row {
            display: flex;
            flex-wrap: wrap;
            gap: 10px 18px;
            margin-top: 20px;
            color: var(--muted);
            font-size: 12px;
            font-weight: 700;
        }

        .trust-row span::before {
            content: "✓";
            color: var(--accent);
            margin-right: 6px;
            font-weight: 900;
        }

        /* HERO CARD */

        .hero-card {
            position: relative;
            overflow: hidden;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 28px;
            padding: 24px;
            box-shadow: var(--shadow);
        }

        .hero-card::before {
            content: "";
            position: absolute;
            width: 220px;
            height: 220px;
            right: -90px;
            top: -90px;
            border-radius: 50%;
            background: var(--accent-soft);
        }

        .card-label {
            color: var(--accent-strong);
            font-size: 11px;
            font-weight: 900;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .estimate-title {
            margin-top: 10px;
            font-size: 27px;
            font-weight: 950;
            letter-spacing: -1px;
        }

        .estimate-price {
            margin-top: 22px;
            font-size: clamp(36px, 7vw, 58px);
            line-height: 1;
            font-weight: 950;
            letter-spacing: -2px;
        }

        .estimate-note {
            margin-top: 8px;
            color: var(--muted);
            font-size: 13px;
        }

        .estimate-line {
            height: 1px;
            background: var(--border);
            margin: 22px 0;
        }

        .mini-row {
            display: flex;
            justify-content: space-between;
            gap: 15px;
            padding: 10px 0;
            font-size: 14px;
        }

        .mini-row span {
            color: var(--muted);
        }

        .mini-row strong {
            font-weight: 900;
        }

        .card-button {
            width: 100%;
            margin-top: 15px;
        }

        /* SECTION */

        .section {
            padding: 58px 0;
        }

        .section-head {
            max-width: 720px;
            margin-bottom: 28px;
        }

        .section-kicker {
            color: var(--accent-strong);
            font-size: 11px;
            font-weight: 900;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            margin-bottom: 9px;
        }

        .section-title {
            font-size: clamp(28px, 5vw, 46px);
            line-height: 1.05;
            letter-spacing: -1.7px;
            font-weight: 950;
        }

        .section-text {
            color: var(--muted);
            line-height: 1.65;
            margin-top: 12px;
            font-size: 15px;
        }

        /* CALCULATOR CTA */

        .calculator-banner {
            background:
                linear-gradient(
                    135deg,
                    var(--surface),
                    var(--surface-2)
                );
            border: 1px solid var(--border);
            border-radius: 28px;
            padding: 28px;
            display: grid;
            gap: 24px;
            box-shadow: var(--shadow);
        }

        .calculator-banner h2 {
            font-size: clamp(27px, 5vw, 42px);
            line-height: 1.05;
            letter-spacing: -1.5px;
            font-weight: 950;
        }

        .calculator-banner h2 span {
            color: var(--accent);
        }

        .calculator-banner p {
            color: var(--muted);
            line-height: 1.6;
            margin-top: 10px;
            font-size: 15px;
        }

        .calculator-points {
            display: grid;
            gap: 10px;
            margin-top: 18px;
        }

        .calculator-points div {
            display: flex;
            align-items: flex-start;
            gap: 9px;
            color: var(--text);
            font-size: 14px;
            font-weight: 700;
        }

        .calculator-points b {
            color: var(--accent);
        }

        /* FEATURES */

        .feature-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 14px;
        }

        .feature {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 23px;
        }

        .feature-icon {
            width: 46px;
            height: 46px;
            display: grid;
            place-items: center;
            border-radius: 13px;
            background: var(--accent-soft);
            color: var(--accent-strong);
            font-size: 21px;
            margin-bottom: 16px;
        }

        .feature h3 {
            font-size: 18px;
            font-weight: 900;
            margin-bottom: 8px;
        }

        .feature p {
            color: var(--muted);
            line-height: 1.6;
            font-size: 14px;
        }

        /* TOOLS */

        .tools-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
        }

        .tool {
            min-height: 115px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 16px;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 17px;
            transition: transform .2s ease, border-color .2s ease;
        }

        .tool:hover {
            transform: translateY(-3px);
            border-color: rgba(245,180,0,.55);
        }

        .tool-icon {
            font-size: 21px;
        }

        .tool strong {
            font-size: 13px;
            line-height: 1.25;
        }

        .tool small {
            color: var(--muted);
            font-size: 11px;
            margin-top: 5px;
        }

        /* HOW */

        .steps {
            display: grid;
            gap: 12px;
            counter-reset: steps;
        }

        .step {
            counter-increment: steps;
            display: grid;
            grid-template-columns: 48px 1fr;
            gap: 14px;
            padding: 20px;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 19px;
        }

        .step-number {
            width: 48px;
            height: 48px;
            display: grid;
            place-items: center;
            background: var(--accent);
            color: #000;
            border-radius: 13px;
            font-weight: 950;
        }

        .step h3 {
            font-size: 16px;
            font-weight: 900;
            margin-bottom: 5px;
        }

        .step p {
            color: var(--muted);
            font-size: 13px;
            line-height: 1.55;
        }

        /* FINAL CTA */

        .final-cta {
            text-align: center;
            background: var(--accent);
            color: #000;
            border-radius: 28px;
            padding: 38px 24px;
        }

        .final-cta h2 {
            font-size: clamp(30px, 6vw, 52px);
            line-height: 1;
            letter-spacing: -2px;
            font-weight: 950;
        }

        .final-cta p {
            max-width: 650px;
            margin: 14px auto 0;
            font-size: 15px;
            line-height: 1.55;
            opacity: .8;
        }

        .final-cta .btn {
            margin-top: 23px;
            background: #000;
            color: #fff;
        }

        /* FOOTER */

        footer {
            padding: 35px 0 45px;
            border-top: 1px solid var(--border);
            margin-top: 30px;
        }

        .footer-inner {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .footer-brand {
            font-size: 18px;
            font-weight: 950;
        }

        .footer-brand span {
            color: var(--accent);
        }

        footer p {
            color: var(--muted);
            font-size: 12px;
            line-height: 1.6;
        }

        /* DESKTOP */

        @media (min-width: 700px) {

            .container {
                width: min(1180px, calc(100% - 48px));
            }

            .nav-link,
            .nav-cta {
                display: inline-flex;
            }

            .hero {
                padding: 80px 0 50px;
            }

            .hero-grid {
                grid-template-columns: minmax(0, 1.15fr) minmax(360px, .85fr);
                gap: 55px;
            }

            .hero-actions {
                display: flex;
            }

            .btn {
                min-width: 210px;
            }

            .calculator-banner {
                grid-template-columns: 1.2fr .8fr;
                align-items: center;
                padding: 40px;
            }

            .feature-grid {
                grid-template-columns: repeat(3, 1fr);
            }

            .tools-grid {
                grid-template-columns: repeat(4, 1fr);
                gap: 14px;
            }

            .steps {
                grid-template-columns: repeat(3, 1fr);
            }

            .step {
                display: block;
            }

            .step-number {
                margin-bottom: 18px;
            }

            .footer-inner {
                flex-direction: row;
                justify-content: space-between;
                align-items: center;
            }
        }

        @media (min-width: 1000px) {

            .section {
                padding: 80px 0;
            }

            .tools-grid {
                grid-template-columns: repeat(5, 1fr);
            }

            .hero-card {
                padding: 32px;
            }
        }

        /* MOBILE */

        @media (max-width: 699px) {

            .container {
                width: min(100% - 22px, 1180px);
            }

            .nav-inner {
                min-height: 68px;
            }

            .brand {
                font-size: 17px;
            }

            .brand img {
                width: 35px;
                height: 35px;
            }

            .nav-link {
                display: none;
            }

            .nav-cta {
                display: inline-flex;
                padding: 10px 12px;
                font-size: 11px;
            }

            .hero {
                padding: 36px 0 20px;
            }

            .hero h1 {
                letter-spacing: -2px;
            }

            .hero-text {
                font-size: 16px;
            }

            .hero-actions .btn {
                width: 100%;
            }

            .hero-card {
                border-radius: 22px;
                padding: 22px;
            }

            .section {
                padding: 42px 0;
            }

            .section-title {
                letter-spacing: -1px;
            }

            .calculator-banner {
                padding: 23px;
                border-radius: 22px;
            }

            .calculator-banner .btn {
                width: 100%;
            }

            .feature {
                padding: 20px;
            }

            .tools-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .final-cta {
                border-radius: 22px;
                padding: 34px 20px;
            }
        }

        /* REDUCED MOTION */

        @media (prefers-reduced-motion: reduce) {
            html {
                scroll-behavior: auto;
            }

            * {
                transition: none !important;
            }
        }
    </style>

    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "WebApplication",
        "name": "WORKSİTEM İnşaat ve Tadilat Maliyet Hesaplama",
        "url": "https://worksitem.com/",
        "applicationCategory": "BusinessApplication",
        "operatingSystem": "Web",
        "description": "İnşaat ve tadilat projeleri için yaklaşık maliyet hesaplama ve keşif aracı.",
        "provider": {
            "@type": "Organization",
            "name": "Kainta İnşaat",
            "url": "https://kainta.com"
        }
    }
    </script>
</head>

<body>

<header class="nav">
    <div class="container nav-inner">

        <a href="/" class="brand" aria-label="WORKSİTEM Ana Sayfa">
            <img src="/brand/worksitem-logo.png" alt="WORKSİTEM">
            WORKSİ<span>TEM</span>
        </a>

        <nav class="nav-actions" aria-label="Ana navigasyon">

            <a href="/araclar/birim-fiyat-analiz" class="nav-link">
                Birim Fiyatlar
            </a>

            <a href="/araclar" class="nav-link">
                Hesaplayıcılar
            </a>

            <a href="/maliyet-hesapla" class="nav-cta">
                Maliyet Hesapla
            </a>

            <button
                type="button"
                class="theme-btn"
                id="themeButton"
                aria-label="Tema değiştir"
                title="Tema değiştir">
                ◐
            </button>

        </nav>
    </div>
</header>


<main>

    <!-- HERO -->

    <section class="hero">
        <div class="container hero-grid">

            <div>

                <div class="eyebrow">
                    <span class="eyebrow-dot"></span>
                    Ücretsiz proje ön keşfi
                </div>

                <h1>
                    İnşaat ve tadilat
                    <span>maliyetini hesapla.</span>
                </h1>

                <p class="hero-text">
                    Arsanızdan komple tadilata kadar projenizin yaklaşık
                    maliyetini saniyeler içinde hesaplayın. Projenizi
                    profesyonel teklife dönüştürmek için Kainta İnşaat ekibinden
                    destek alın.
                </p>

                <div class="hero-actions">

                    <a href="/maliyet-hesapla" class="btn btn-primary">
                        Maliyetimi Hesapla →
                    </a>

                    <a href="#nasil-calisir" class="btn btn-secondary">
                        Nasıl Çalışıyor?
                    </a>

                </div>

                <div class="trust-row">
                    <span>Ücretsiz ön hesaplama</span>
                    <span>Mobil uyumlu</span>
                    <span>Profesyonel teklif</span>
                </div>

            </div>


            <!-- ÖN İZLEME KARTI -->

            <div class="hero-card">

                <div class="card-label">
                    WORKSİTEM Maliyet Motoru
                </div>

                <div class="estimate-title">
                    Projenizin bütçesini önceden görün.
                </div>

                <div class="estimate-price">
                    ₺ / m²
                </div>

                <div class="estimate-note">
                    Yapı tipi, kapsam ve proje özelliklerine göre
                    yaklaşık maliyet hesaplanır.
                </div>

                <div class="estimate-line"></div>

                <div class="mini-row">
                    <span>Sıfırdan İnşaat</span>
                    <strong>ÇŞB yapı sınıfı</strong>
                </div>

                <div class="mini-row">
                    <span>Komple Tadilat</span>
                    <strong>İş kapsamı</strong>
                </div>

                <div class="mini-row">
                    <span>Sonraki adım</span>
                    <strong>Teklif / Keşif</strong>
                </div>

                <a href="/maliyet-hesapla" class="btn btn-primary card-button">
                    Hesaplamayı Başlat
                </a>

            </div>

        </div>
    </section>


    <!-- ANA DÖNÜŞÜM -->

    <section class="section">
        <div class="container">

            <div class="calculator-banner">

                <div>

                    <div class="section-kicker">
                        Ana maliyet hesaplayıcı
                    </div>

                    <h2>
                        Projenizi rakama dönüştürün.
                        <span>Sonra teklife.</span>
                    </h2>

                    <p>
                        Sıfırdan inşaat veya komple tadilat seçin.
                        Alan ve proje bilgilerinizi girin. Yaklaşık bütçenizi
                        görün ve isterseniz Kainta İnşaat'tan profesyonel teklif isteyin.
                    </p>

                    <div class="calculator-points">
                        <div>
                            <b>✓</b>
                            Sıfırdan inşaat maliyet hesaplama
                        </div>

                        <div>
                            <b>✓</b>
                            Komple tadilat maliyet hesaplama
                        </div>

                        <div>
                            <b>✓</b>
                            Proje bilgilerini teklif talebine dönüştürme
                        </div>
                    </div>

                </div>

                <div>
                    <a href="/maliyet-hesapla" class="btn btn-primary" style="width:100%;">
                        Ücretsiz Maliyet Hesapla →
                    </a>
                </div>

            </div>

        </div>
    </section>


    <!-- NEDEN -->

    <section class="section" id="neden">

        <div class="container">

            <div class="section-head">

                <div class="section-kicker">
                    Neden WORKSİTEM?
                </div>

                <h2 class="section-title">
                    Sadece hesaplama değil,
                    projeye giden ilk adım.
                </h2>

                <p class="section-text">
                    WORKSİTEM, inşaat ve tadilat projelerinde maliyet
                    araştırmasını daha hızlı ve anlaşılır hale getirmek
                    için tasarlanmıştır.
                </p>

            </div>


            <div class="feature-grid">

                <article class="feature">

                    <div class="feature-icon">₺</div>

                    <h3>
                        Maliyeti önceden görün
                    </h3>

                    <p>
                        Projeye başlamadan önce yaklaşık bütçe seviyenizi
                        öğrenin ve finansal planınızı daha doğru oluşturun.
                    </p>

                </article>


                <article class="feature">

                    <div class="feature-icon">⌂</div>

                    <h3>
                        İnşaat veya tadilat
                    </h3>

                    <p>
                        Yeni yapıdan komple tadilata kadar farklı proje
                        senaryolarını tek noktadan değerlendirin.
                    </p>

                </article>


                <article class="feature">

                    <div class="feature-icon">⚡</div>

                    <h3>
                        Hızlı ön keşif
                    </h3>

                    <p>
                        Uzun telefon görüşmeleriyle başlamadan önce
                        projenizin temel bilgilerini dijital olarak oluşturun.
                    </p>

                </article>

            </div>

        </div>

    </section>


    <!-- NASIL ÇALIŞIR -->

    <section class="section" id="nasil-calisir">

        <div class="container">

            <div class="section-head">

                <div class="section-kicker">
                    3 adımda
                </div>

                <h2 class="section-title">
                    Fikirden keşfe.
                </h2>

                <p class="section-text">
                    Karmaşık formlar yerine projenizin ihtiyacı olan
                    temel bilgileri girin.
                </p>

            </div>


            <div class="steps">

                <article class="step">

                    <div class="step-number">1</div>

                    <div>
                        <h3>Projenizi seçin</h3>

                        <p>
                            Sıfırdan inşaat veya komple tadilat
                            seçeneğini belirleyin.
                        </p>
                    </div>

                </article>


                <article class="step">

                    <div class="step-number">2</div>

                    <div>
                        <h3>Bilgileri girin</h3>

                        <p>
                            Alan, yapı ve proje kapsamı gibi
                            temel bilgileri girerek hesaplamayı oluşturun.
                        </p>
                    </div>

                </article>


                <article class="step">

                    <div class="step-number">3</div>

                    <div>
                        <h3>Teklife dönüştürün</h3>

                        <p>
                            İsterseniz hesaplamanızı Kainta İnşaat ekibine
                            göndererek profesyonel teklif sürecini başlatın.
                        </p>
                    </div>

                </article>

            </div>

        </div>

    </section>


    <!-- HESAPLAYICILAR -->

    <section class="section">

        <div class="container">

            <div class="section-head">

                <div class="section-kicker">
                    İnşaat araçları
                </div>

                <h2 class="section-title">
                    İhtiyacınız olan hesabı bulun.
                </h2>

                <p class="section-text">
                    İnşaat maliyetinden metraja kadar farklı hesaplama
                    araçlarını kullanabilirsiniz.
                </p>

            </div>


            <div class="tools-grid">

                <a class="tool" href="/araclar/tahmini-bina-maliyeti">
                    <span class="tool-icon">🏗️</span>
                    <strong>Tahmini Bina Maliyeti</strong>
                    <small>Yapı maliyeti</small>
                </a>

                <a class="tool" href="/araclar/birim-fiyat-analiz">
                    <span class="tool-icon">📊</span>
                    <strong>Birim Fiyat Analiz</strong>
                    <small>Poz ve fiyat</small>
                </a>

                <a class="tool" href="/araclar/demir-metraji-hesaplayici">
                    <span class="tool-icon">🔩</span>
                    <strong>Demir Metrajı</strong>
                    <small>Donatı hesabı</small>
                </a>

                <a class="tool" href="/araclar/beton-maliyet-hesaplayici">
                    <span class="tool-icon">🧱</span>
                    <strong>Beton Hesaplama</strong>
                    <small>Beton miktarı</small>
                </a>

                <a class="tool" href="/araclar/boya-hesaplayici">
                    <span class="tool-icon">🎨</span>
                    <strong>Boya Hesaplama</strong>
                    <small>Alan ve boya</small>
                </a>

                <a class="tool" href="/araclar/mutfak-maliyet-hesaplayici">
                    <span class="tool-icon">🍳</span>
                    <strong>Mutfak Maliyeti</strong>
                    <small>Mutfak yenileme</small>
                </a>

                <a class="tool" href="/araclar/banyo-maliyet-hesaplayici">
                    <span class="tool-icon">🚿</span>
                    <strong>Banyo Maliyeti</strong>
                    <small>Banyo tadilatı</small>
                </a>

                <a class="tool" href="/araclar/duvar-maliyet-hesaplayici">
                    <span class="tool-icon">🧱</span>
                    <strong>Duvar Maliyeti</strong>
                    <small>Duvar hesabı</small>
                </a>

                <a class="tool" href="/araclar/parke-maliyet-hesaplayici">
                    <span class="tool-icon">🪵</span>
                    <strong>Parke Maliyeti</strong>
                    <small>Zemin hesabı</small>
                </a>

                <a class="tool" href="/araclar/mantolama-maliyeti">
                    <span class="tool-icon">🏠</span>
                    <strong>Mantolama</strong>
                    <small>Yalıtım maliyeti</small>
                </a>

            </div>

        </div>

    </section>


    <!-- FINAL CTA -->

    <section class="section">

        <div class="container">

            <div class="final-cta">

                <h2>
                    Projenizin yaklaşık maliyetini öğrenin.
                </h2>

                <p>
                    Hesaplamayı başlatın. Projenizin bütçesini görün.
                    Sonrasında Kainta İnşaat ekibiyle profesyonel keşif ve teklif
                    sürecine geçebilirsiniz.
                </p>

                <a href="/maliyet-hesapla" class="btn">
                    Maliyeti Hesapla →
                </a>

            </div>

        </div>

    </section>

</main>


<footer>

    <div class="container footer-inner">

        <div class="footer-brand">
            WORKSİ<span>TEM</span>
        </div>

        <p>
            İnşaat ve tadilat maliyet hesaplama platformu.
            © 2026 WORKSİTEM / Kainta İnşaat
        </p>

    </div>

</footer>


<script>
    (function () {

        const button = document.getElementById('themeButton');

        function setTheme(theme) {

            document.documentElement.dataset.theme = theme;

            localStorage.setItem(
                'worksitem-theme',
                theme
            );

            button.textContent =
                theme === 'dark' ? '☀' : '◐';
        }

        const current =
            document.documentElement.dataset.theme || 'light';

        button.textContent =
            current === 'dark' ? '☀' : '◐';

        button.addEventListener('click', function () {

            const active =
                document.documentElement.dataset.theme;

            setTheme(
                active === 'dark'
                    ? 'light'
                    : 'dark'
            );

        });

    })();
</script>


</body>
</html>
