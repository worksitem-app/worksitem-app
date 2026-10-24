<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>İnşaat ve Tadilat Maliyet Hesaplama Araçları | WORKSİTEM</title>

    <meta name="description"
          content="İnşaat ve tadilat maliyetini online hesaplayın. Villa, ev, mutfak, banyo, ofis ve fabrika projeleri için maliyet, metraj ve fizibilite araçlarını kullanın.">

    <meta name="robots" content="index, follow, max-image-preview:large">

    <link rel="canonical" href="https://worksitem.com/araclar">

    <meta property="og:type" content="website">
    <meta property="og:locale" content="tr_TR">
    <meta property="og:title" content="İnşaat ve Tadilat Maliyet Hesaplama Araçları | WORKSİTEM">
    <meta property="og:description"
          content="İnşaat, tadilat, metraj ve fizibilite hesaplamaları için WORKSİTEM araçlarını kullanın. Projeniz uygunsa Kainta İnşaat ile profesyonel teklif sürecine geçin.">
    <meta property="og:url" content="https://worksitem.com/araclar">
    <meta property="og:site_name" content="WORKSİTEM">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="İnşaat ve Tadilat Maliyet Hesaplama Araçları | WORKSİTEM">
    <meta name="twitter:description"
          content="İnşaat maliyeti, tadilat maliyeti, metraj ve fizibilite araçları.">

    <meta property="og:image" content="https://worksitem.com/brand/og-image.png">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta name="twitter:image" content="https://worksitem.com/brand/og-image.png">
    <link rel="icon" href="/favicon.ico" type="image/x-icon">
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="manifest" href="/site.webmanifest">
    <meta name="theme-color" content="#ffffff">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap"
          rel="stylesheet">

    <style>
        :root {
            --bg: #f5f7fa;
            --surface: #ffffff;
            --surface-2: #f8fafc;
            --text: #111827;
            --text-soft: #4b5563;
            --muted: #6b7280;
            --border: #e5e7eb;
            --border-strong: #d1d5db;

            --brand: #111827;
            --brand-soft: #f3f4f6;

            --yellow: #f5b400;
            --yellow-dark: #b77900;
            --yellow-soft: #fff8df;

            --blue: #101318;
            --blue-soft: #fff8e1;

            --green: #16803c;
            --green-soft: #ecfdf3;

            --shadow-sm: 0 2px 8px rgba(15, 23, 42, .05);
            --shadow-md: 0 12px 35px rgba(15, 23, 42, .08);

            --radius-sm: 10px;
            --radius-md: 16px;
            --radius-lg: 22px;

            --container: 1240px;
        }

        html.dark {
            --bg: #0b0f14;
            --surface: #111827;
            --surface-2: #151d29;
            --text: #f3f4f6;
            --text-soft: #cbd5e1;
            --muted: #94a3b8;
            --border: #263241;
            --border-strong: #344154;

            --brand: #f3f4f6;
            --brand-soft: #1a2330;

            --yellow: #f5b400;
            --yellow-dark: #fbbf24;
            --yellow-soft: #30260b;

            --blue: #ffb800;
            --blue-soft: #172554;

            --green: #4ade80;
            --green-soft: #052e16;

            --shadow-sm: 0 2px 8px rgba(0, 0, 0, .18);
            --shadow-md: 0 18px 45px rgba(0, 0, 0, .25);
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
            font-family: "Inter", Arial, sans-serif;
            background: var(--bg);
            color: var(--text);
            line-height: 1.6;
            -webkit-font-smoothing: antialiased;
        }

        a {
            color: inherit;
            text-decoration: none;
        }

        button,
        input {
            font: inherit;
        }

        img {
            max-width: 100%;
            display: block;
        }

        .container {
            width: min(calc(100% - 32px), var(--container));
            margin: 0 auto;
        }

        /* =========================================================
           HEADER
        ========================================================= */

        .site-header {
            position: sticky;
            top: 0;
            z-index: 100;
            background: rgba(255,255,255,.94);
            border-bottom: 1px solid var(--border);
            backdrop-filter: blur(16px);
        }

        html.dark .site-header {
            background: rgba(17,24,39,.94);
        }

        .header-inner {
            min-height: 68px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
        }

        .brand {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            min-width: 130px;
        }

        .brand img {
            width: auto;
            height: 34px;
        }

        .brand-fallback {
            font-weight: 800;
            font-size: 19px;
            letter-spacing: -.7px;
        }

        .main-nav {
            display: flex;
            align-items: center;
            gap: 24px;
        }

        .main-nav a {
            color: var(--text-soft);
            font-size: 14px;
            font-weight: 600;
            transition: .2s ease;
        }

        .main-nav a:hover {
            color: var(--text);
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .theme-toggle {
            width: 40px;
            height: 40px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 1px solid var(--border);
            border-radius: 11px;
            background: var(--surface);
            color: var(--text);
            cursor: pointer;
        }

        .header-cta {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 40px;
            padding: 0 15px;
            border-radius: 10px;
            background: var(--brand);
            color: var(--bg);
            font-size: 13px;
            font-weight: 800;
        }

        /* =========================================================
           HERO
        ========================================================= */

        .hero {
            position: relative;
            overflow: hidden;
            padding: 72px 0 56px;
            background:
                radial-gradient(circle at 50% -20%, rgba(245,180,0,.12), transparent 42%),
                var(--surface);
            border-bottom: 1px solid var(--border);
        }

        .hero-inner {
            max-width: 920px;
            margin: auto;
            text-align: center;
        }

        .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 7px 12px;
            border: 1px solid var(--border);
            border-radius: 999px;
            background: var(--surface-2);
            color: var(--text-soft);
            font-size: 12px;
            font-weight: 700;
            margin-bottom: 20px;
        }

        .eyebrow-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: var(--yellow);
        }

        .hero h1 {
            max-width: 900px;
            margin: auto;
            font-size: clamp(32px, 5vw, 58px);
            line-height: 1.06;
            letter-spacing: -2.8px;
            font-weight: 800;
        }

        .hero h1 span {
            color: var(--ws-highlight, #9A6E00);
        }

        .hero-subtitle {
            max-width: 740px;
            margin: 20px auto 0;
            color: var(--text-soft);
            font-size: clamp(15px, 2vw, 18px);
            line-height: 1.7;
        }

        .hero-actions {
            display: flex;
            align-items: center;
            justify-content: center;
            flex-wrap: wrap;
            gap: 12px;
            margin-top: 28px;
        }

        .btn {
            min-height: 48px;
            padding: 0 20px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 800;
            transition: transform .2s ease, box-shadow .2s ease;
        }

        .btn:hover {
            transform: translateY(-2px);
        }

        .btn-primary {
            background: var(--brand);
            color: var(--bg);
            box-shadow: var(--shadow-sm);
        }

        .btn-secondary {
            background: var(--surface);
            color: var(--text);
            border: 1px solid var(--border-strong);
        }

        /* =========================================================
           QUICK TOOL CARDS
        ========================================================= */

        .featured {
            padding: 42px 0 18px;
        }

        .section-heading {
            margin-bottom: 18px;
        }

        .section-kicker {
            color: var(--yellow-dark);
            font-size: 12px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: .08em;
            margin-bottom: 6px;
        }

        .section-heading h2 {
            font-size: clamp(23px, 3vw, 31px);
            line-height: 1.2;
            letter-spacing: -1px;
        }

        .section-heading p {
            max-width: 700px;
            margin-top: 7px;
            color: var(--muted);
            font-size: 14px;
        }

        .featured-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 16px;
        }

        .featured-card {
            position: relative;
            min-height: 235px;
            padding: 24px;
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            background: var(--surface);
            overflow: hidden;
            box-shadow: var(--shadow-sm);
            display: flex;
            flex-direction: column;
            transition: transform .22s ease, box-shadow .22s ease, border-color .22s ease;
        }

        .featured-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-md);
            border-color: var(--border-strong);
        }

        .featured-card.ai {
            background:
                linear-gradient(145deg, var(--surface), var(--yellow-soft));
            border-color: rgba(245,180,0,.35);
        }

        .featured-icon {
            width: 46px;
            height: 46px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 13px;
            background: var(--surface-2);
            font-size: 23px;
            margin-bottom: 18px;
        }

        .featured-card.ai .featured-icon {
            background: var(--yellow);
        }

        .featured-card h3 {
            font-size: 18px;
            line-height: 1.3;
            letter-spacing: -.5px;
        }

        .featured-card p {
            margin-top: 8px;
            color: var(--muted);
            font-size: 13px;
            line-height: 1.55;
        }

        .featured-link {
            margin-top: auto;
            padding-top: 18px;
            color: var(--blue);
            font-size: 13px;
            font-weight: 800;
        }

        /* =========================================================
           SEARCH
        ========================================================= */

        .tools-section {
            padding: 50px 0 20px;
        }

        .tools-toolbar {
            position: sticky;
            top: 68px;
            z-index: 40;
            padding: 15px 0;
            margin-bottom: 25px;
            background: color-mix(in srgb, var(--bg) 94%, transparent);
            backdrop-filter: blur(12px);
        }

        .toolbar-inner {
            display: grid;
            grid-template-columns: minmax(240px, 1fr) auto;
            gap: 14px;
            align-items: center;
        }

        .search-box {
            position: relative;
        }

        .search-box input {
            width: 100%;
            height: 52px;
            padding: 0 18px 0 48px;
            border: 1px solid var(--border-strong);
            border-radius: 13px;
            outline: none;
            background: var(--surface);
            color: var(--text);
            font-size: 14px;
            box-shadow: var(--shadow-sm);
        }

        .search-box input:focus {
            border-color: var(--yellow);
            box-shadow: 0 0 0 4px rgba(245,180,0,.12);
        }

        .search-icon {
            position: absolute;
            left: 17px;
            top: 14px;
            color: var(--muted);
            font-size: 20px;
            pointer-events: none;
        }

        .tool-count {
            white-space: nowrap;
            color: var(--muted);
            font-size: 13px;
            font-weight: 600;
        }

        .filters {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            margin-top: 12px;
        }

        .filter-btn {
            min-height: 36px;
            padding: 0 13px;
            border: 1px solid var(--border);
            border-radius: 999px;
            background: var(--surface);
            color: var(--text-soft);
            cursor: pointer;
            font-size: 12px;
            font-weight: 700;
            transition: .18s ease;
        }

        .filter-btn:hover {
            border-color: var(--border-strong);
        }

        .filter-btn.active {
            background: var(--brand);
            color: var(--bg);
            border-color: var(--brand);
        }

        /* =========================================================
           TOOL GRID
        ========================================================= */

        .tools-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 14px;
        }

        .tool-card {
            min-width: 0;
            min-height: 205px;
            display: flex;
            flex-direction: column;
            padding: 19px;
            border: 1px solid var(--border);
            border-radius: var(--radius-md);
            background: var(--surface);
            overflow: hidden;
            box-shadow: var(--shadow-sm);
            transition:
                transform .2s ease,
                box-shadow .2s ease,
                border-color .2s ease;
        }

        .tool-card:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-md);
            border-color: var(--border-strong);
        }

        .tool-card.is-premium {
            border-color: rgba(245,180,0,.30);
        }

        .tool-top {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 10px;
            margin-bottom: 15px;
        }

        .tool-icon {
            width: 43px;
            height: 43px;
            flex: 0 0 43px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 11px;
            background: var(--surface-2);
            font-size: 21px;
        }

        .premium-badge {
            padding: 4px 7px;
            border-radius: 7px;
            background: var(--yellow-soft);
            color: var(--yellow-dark);
            font-size: 9px;
            line-height: 1;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: .03em;
        }

        .tool-title {
            font-size: 14px;
            line-height: 1.35;
            font-weight: 800;
            letter-spacing: -.2px;
        }

        .tool-desc {
            margin-top: 7px;
            color: var(--muted);
            font-size: 12px;
            line-height: 1.55;
        }

        .tool-action {
            margin-top: auto;
            padding-top: 16px;
            color: var(--blue);
            font-size: 12px;
            font-weight: 800;
        }

        .no-results {
            display: none;
            grid-column: 1 / -1;
            padding: 50px 20px;
            text-align: center;
            border: 1px dashed var(--border-strong);
            border-radius: var(--radius-md);
            color: var(--muted);
        }

        /* =========================================================
           SEO CONTENT
        ========================================================= */

        .seo-section {
            padding: 70px 0 30px;
        }

        .seo-grid {
            display: grid;
            grid-template-columns: 1.1fr .9fr;
            gap: 30px;
            align-items: start;
        }

        .seo-copy {
            color: var(--text-soft);
            font-size: 14px;
        }

        .seo-copy h2 {
            color: var(--text);
            font-size: 27px;
            line-height: 1.25;
            letter-spacing: -1px;
            margin-bottom: 14px;
        }

        .seo-copy h3 {
            color: var(--text);
            font-size: 17px;
            margin: 23px 0 7px;
        }

        .seo-copy p {
            margin-bottom: 11px;
        }

        .seo-copy strong {
            color: var(--text);
        }

        .intent-list {
            display: grid;
            gap: 10px;
        }

        .intent-item {
            padding: 17px;
            border: 1px solid var(--border);
            border-radius: 14px;
            background: var(--surface);
        }

        .intent-item strong {
            display: block;
            font-size: 14px;
            margin-bottom: 3px;
        }

        .intent-item span {
            color: var(--muted);
            font-size: 12px;
        }

        /* =========================================================
           KAINTA CTA
        ========================================================= */

        .conversion-section {
            padding: 35px 0 70px;
        }

        .conversion-box {
            position: relative;
            overflow: hidden;
            padding: 38px;
            border-radius: 24px;
            background: var(--brand);
            color: var(--bg);
        }

        .conversion-box::after {
            content: "";
            position: absolute;
            width: 240px;
            height: 240px;
            right: -80px;
            top: -100px;
            border-radius: 50%;
            background: rgba(245,180,0,.17);
        }

        .conversion-content {
            position: relative;
            z-index: 2;
            max-width: 780px;
        }

        .conversion-label {
            color: var(--yellow);
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: .1em;
        }

        .conversion-box h2 {
            margin-top: 8px;
            font-size: clamp(25px, 4vw, 36px);
            line-height: 1.18;
            letter-spacing: -1.2px;
        }

        .conversion-box p {
            margin-top: 11px;
            color: color-mix(in srgb, var(--bg) 72%, transparent);
            font-size: 14px;
            max-width: 680px;
        }

        .conversion-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 22px;
        }

        .conversion-primary {
            background: var(--yellow);
            color: #17120a;
        }

        .conversion-secondary {
            border: 1px solid rgba(255,255,255,.2);
            color: var(--bg);
            background: rgba(255,255,255,.06);
        }

        /* =========================================================
           FAQ
        ========================================================= */

        .faq-section {
            padding: 10px 0 70px;
        }

        .faq-list {
            max-width: 900px;
            display: grid;
            gap: 10px;
        }

        .faq-item {
            border: 1px solid var(--border);
            border-radius: 14px;
            background: var(--surface);
            overflow: hidden;
        }

        .faq-item summary {
            cursor: pointer;
            padding: 18px 20px;
            color: var(--text);
            font-size: 14px;
            font-weight: 800;
            list-style: none;
        }

        .faq-item summary::-webkit-details-marker {
            display: none;
        }

        .faq-item summary::after {
            content: "+";
            float: right;
            color: var(--muted);
            font-size: 18px;
        }

        .faq-item[open] summary::after {
            content: "−";
        }

        .faq-answer {
            padding: 0 20px 18px;
            color: var(--muted);
            font-size: 13px;
            line-height: 1.65;
        }

        /* =========================================================
           FOOTER
        ========================================================= */

        .site-footer {
            padding: 28px 0;
            border-top: 1px solid var(--border);
            background: var(--surface);
        }

        .footer-inner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
        }

        .footer-brand {
            font-weight: 800;
            font-size: 14px;
        }

        .footer-copy {
            color: var(--muted);
            font-size: 12px;
        }

        .footer-links {
            display: flex;
            gap: 16px;
            flex-wrap: wrap;
        }

        .footer-links a {
            color: var(--text-soft);
            font-size: 12px;
            font-weight: 600;
        }

        /* =========================================================
           RESPONSIVE
        ========================================================= */

        @media (max-width: 1100px) {
            .tools-grid {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }
        }

        @media (max-width: 850px) {
            .main-nav {
                display: none;
            }

            .featured-grid {
                grid-template-columns: 1fr;
            }

            .featured-card {
                min-height: 190px;
            }

            .tools-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .seo-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 600px) {
            .container {
                width: min(calc(100% - 24px), var(--container));
            }

            .header-inner {
                min-height: 62px;
            }

            .brand img {
                height: 29px;
            }

            .header-cta {
                padding: 0 11px;
                font-size: 11px;
            }

            .theme-toggle {
                width: 36px;
                height: 36px;
            }

            .hero {
                padding: 48px 0 42px;
            }

            .hero h1 {
                letter-spacing: -1.8px;
            }

            .hero-subtitle {
                font-size: 14px;
            }

            .hero-actions {
                flex-direction: column;
            }

            .hero-actions .btn {
                width: 100%;
            }

            .featured {
                padding-top: 32px;
            }

            .featured-card {
                padding: 20px;
            }

            .tools-section {
                padding-top: 35px;
            }

            .tools-toolbar {
                top: 62px;
            }

            .toolbar-inner {
                grid-template-columns: 1fr;
            }

            .tool-count {
                display: none;
            }

            .filters {
                overflow-x: auto;
                flex-wrap: nowrap;
                padding-bottom: 5px;
                scrollbar-width: none;
            }

            .filters::-webkit-scrollbar {
                display: none;
            }

            .filter-btn {
                flex: 0 0 auto;
            }

            .tools-grid {
                grid-template-columns: 1fr;
                gap: 11px;
            }

            .tool-card {
                min-height: 160px;
            }

            .seo-section {
                padding-top: 50px;
            }

            .conversion-box {
                padding: 28px 22px;
                border-radius: 19px;
            }

            .conversion-actions {
                flex-direction: column;
            }

            .conversion-actions .btn {
                width: 100%;
            }

            .footer-inner {
                align-items: flex-start;
                flex-direction: column;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            *,
            *::before,
            *::after {
                scroll-behavior: auto !important;
                transition: none !important;
            }
        }
    </style>

    {{-- =========================================================
         STRUCTURED DATA
    ========================================================== --}}

    @php
        $pageUrl = 'https://worksitem.com/araclar';

        $schemaItems = [
            [
                'name' => 'İnşaat Maliyeti Hesapla',
                'url' => 'https://worksitem.com/maliyet-hesapla'
            ],
            [
                'name' => 'Tadilat Maliyeti Hesapla',
                'url' => 'https://worksitem.com/maliyet-hesapla'
            ],
            [
                'name' => 'AI Ön Keşif ve Ön Fiyatlandırma',
                'url' => 'https://worksitem.com/araclar/otomatik-kesif-fiyatlandirma'
            ]
        ];
    @endphp

    <script type="application/ld+json">
    {
        "@@context": "https://schema.org",
        "@type": "WebSite",
        "name": "WORKSİTEM",
        "url": "https://worksitem.com",
        "description": "İnşaat ve tadilat projeleri için maliyet, metraj, fizibilite ve teknik hesaplama araçları."
    }
    </script>

    <script type="application/ld+json">
    {
        "@@context": "https://schema.org",
        "@type": "ItemList",
        "name": "İnşaat ve Tadilat Hesaplama Araçları",
        "url": "{{ $pageUrl }}",
        "numberOfItems": {{ count($schemaItems) }},
        "itemListElement": [
            <?php
$schemaList = [];

foreach ($schemaItems as $index => $item) {
    $schemaList[] = [
        '@type' => 'ListItem',
        'position' => $index + 1,
        'name' => $item['name'],
        'url' => $item['url'],
    ];
}

echo json_encode(
    $schemaList,
    JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
);
?>
        ]
    }
    </script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
<script id="worksitem-tools-theme-bridge">
(function(){
    var theme=null;
    try{theme=localStorage.getItem("worksitem-theme");}catch(e){}
    if(theme!=="light" && theme!=="dark"){
        theme="light";
    }
    document.documentElement.setAttribute("data-theme",theme);
    document.documentElement.classList.toggle("dark",theme==="dark");
})();
</script>

<style>
/* WORKSITEM_QA_CARD_FINAL */

.qa-status-badge{
    display:inline-flex;
    align-items:center;
    justify-content:center;
    min-height:24px;
    padding:5px 9px;
    border-radius:999px;
    font-size:9px;
    font-weight:900;
    line-height:1;
    letter-spacing:.35px;
    text-transform:uppercase;
}

.qa-status-badge.review{
    background:#fff3cd;
    color:#8a5a00;
    border:1px solid #ffe29a;
}

.qa-status-badge.active{
    background:#dcfce7;
    color:#101318;
    border:1px solid #fde68a;
}

.tool-card.qa-review{
    opacity:.72;
    cursor:not-allowed;
}

.tool-card.qa-review:hover{
    transform:none !important;
    box-shadow:none !important;
}

.tool-card.qa-review .tool-action{
    color:#8a5a00;
}

[data-theme="dark"] .qa-status-badge.active{
    background:rgba(22,101,52,.28);
    color:#86efac;
    border-color:rgba(134,239,172,.25);
}

[data-theme="dark"] .qa-status-badge.review{
    background:rgba(146,94,0,.25);
    color:#ffd166;
    border-color:rgba(255,209,102,.25);
}
</style>

</head>

<body data-tool-slug="araclar">

<x-site.contech-header />

{{-- =========================================================
     DYNAMIC TOOL DATA
========================================================== --}}

@php

    /*
     * PREMIUM / ÖNE ÇIKAN ARAÇLAR
     *
     * Buradaki yapı mevcut sistemle uyumludur.
     * Yeni blade dosyası eklediğinizde sistem otomatik olarak
     * aşağıdaki dinamik taramaya dahil eder.
     */

    $premiums = [

        'kat-karsiligi-analiz' => [
            'icon' => '🤝',
            'title' => 'Kat Karşılığı Analizi',
            'cat' => 'yatirim',
            'desc' => 'Kat karşılığı projelerde arsa, inşaat alanı ve proje ekonomisini değerlendirin.'
        ],

        'taks-kaks-hesaplayici' => [
            'icon' => '🏗️',
            'title' => 'TAKS / KAKS (Emsal)',
            'cat' => 'yatirim',
            'desc' => 'Arsanız için emsal, yapılaşma alanı ve kat hesabını hızlıca yapın.'
        ],

        'daire-kira-degeri' => [
            'icon' => '📈',
            'title' => 'Kira ve Amortisman',
            'cat' => 'yatirim',
            'desc' => 'Kira geliri ve yatırım geri dönüş süresini yaklaşık olarak değerlendirin.'
        ],

        'statik-proje-bedeli' => [
            'icon' => '📐',
            'title' => 'Statik Proje Bedeli',
            'cat' => 'yatirim',
            'desc' => 'Statik proje hizmet bedelini hesaplamak için hızlı bir ön çalışma yapın.'
        ],

        'pafta-olcu-hesaplayici' => [
            'icon' => '🗺️',
            'title' => 'Pafta ve Ölçek Hesabı',
            'cat' => 'yatirim',
            'desc' => 'Pafta üzerindeki ölçüleri gerçek arazi ölçülerine dönüştürün.'
        ],

        'sgk-asgari-iscilik' => [
            'icon' => '🏛️',
            'title' => 'SGK Asgari İşçilik',
            'cat' => 'mevzuat',
            'desc' => 'İnşaat projelerinde asgari işçilik hesabı için ön değerlendirme yapın.'
        ],

        'asansor-zorunlulugu' => [
            'icon' => '🛗',
            'title' => 'Asansör Zorunluluğu',
            'cat' => 'mevzuat',
            'desc' => 'Projenizin özelliklerine göre asansör gerekliliğini değerlendirin.'
        ],

        'iscilik-maliyeti' => [
            'icon' => '👷',
            'title' => 'İşçilik ve Taşeron Maliyeti',
            'cat' => 'mevzuat',
            'desc' => 'İşçilik ve taşeron maliyetleri için ön hesaplama oluşturun.'
        ],

        'beton-hesaplayici' => [
            'icon' => '🏢',
            'title' => 'Hazır Beton Hesabı',
            'cat' => 'kaba',
            'desc' => 'Beton hacmini ve yaklaşık mikser ihtiyacını hesaplayın.'
        ],

        'hafriyat-kazi-hesabi' => [
            'icon' => '🚜',
            'title' => 'Hafriyat ve Kazı',
            'cat' => 'kaba',
            'desc' => 'Kazı hacmi ve taşıma ihtiyacı için ön hesaplama yapın.'
        ],

        'duvar-orgu' => [
            'icon' => '🧱',
            'title' => 'Duvar Örgü ve Sıva',
            'cat' => 'kaba',
            'desc' => 'Duvar imalatı ve sıva için yaklaşık metraj hesabı yapın.'
        ],

        'iskele-metraji-maliyeti' => [
            'icon' => '🪜',
            'title' => 'İskele Metrajı ve Maliyeti',
            'cat' => 'kaba',
            'desc' => 'Cephe alanına göre iskele metrajı ve yaklaşık maliyet hesabı yapın.'
        ],

        'cimento-kum-hesaplama' => [
            'icon' => '🛒',
            'title' => 'Çimento ve Kum Hesabı',
            'cat' => 'kaba',
            'desc' => 'Harç imalatı için gerekli temel malzeme miktarını hesaplayın.'
        ],

        'cati-maliyet-hesaplayici' => [
            'icon' => '🏠',
            'title' => 'Çatı Maliyeti',
            'cat' => 'kaba',
            'desc' => 'Çatı sistemleri için alan ve malzeme bazlı ön maliyet hesabı yapın.'
        ],

        'metrekup-hacim-hesaplama' => [
            'icon' => '📏',
            'title' => 'Hacim (m³) Hesaplama',
            'cat' => 'kaba',
            'desc' => 'Beton, toprak ve farklı geometrik hacimleri hesaplayın.'
        ],

        'klima-btu-hesaplayici' => [
            'icon' => '❄️',
            'title' => 'Klima BTU Hesabı',
            'cat' => 'mekanik',
            'desc' => 'Mekân özelliklerine göre yaklaşık klima kapasitesini değerlendirin.'
        ],

        'havalandirma-hesaplayici' => [
            'icon' => '💨',
            'title' => 'Havalandırma Kanalı',
            'cat' => 'mekanik',
            'desc' => 'Debi ve hava hızına göre kanal kesiti için ön hesaplama yapın.'
        ],

        'su-tesisat-hesaplama' => [
            'icon' => '🚰',
            'title' => 'Su Tesisatı',
            'cat' => 'mekanik',
            'desc' => 'Temiz ve pis su tesisatı için yaklaşık borulama metrajını hesaplayın.'
        ],

        'kablo-kesiti-hesabi' => [
            'icon' => '🔌',
            'title' => 'Kablo Kesiti Hesabı',
            'cat' => 'mekanik',
            'desc' => 'Elektrik yükü ve mesafeye göre kablo kesiti için ön hesaplama yapın.'
        ],

        'gunes-paneli-hesaplama' => [
            'icon' => '☀️',
            'title' => 'Güneş Paneli / GES',
            'cat' => 'mekanik',
            'desc' => 'GES kapasitesi ve yatırım için ön fizibilite hesabı yapın.'
        ],

        'yalitim-mantolama-hesaplayici' => [
            'icon' => '🌡️',
            'title' => 'Yalıtım ve Mantolama',
            'cat' => 'mekanik',
            'desc' => 'Cephe alanına göre yalıtım ve mantolama metrajını değerlendirin.'
        ],

        'alcipan-hesaplayici' => [
            'icon' => '🧩',
            'title' => 'Alçıpan ve Tavan',
            'cat' => 'ince',
            'desc' => 'Alçıpan bölme ve asma tavan için yaklaşık metraj hesabı yapın.'
        ],

        'zemin-kaplama' => [
            'icon' => '📐',
            'title' => 'Zemin, Fayans ve Parke',
            'cat' => 'ince',
            'desc' => 'Kaplama alanı ve fire payını dikkate alarak metraj oluşturun.'
        ],

        'mutfak-maliyet-hesaplayici' => [
            'icon' => '🍳',
            'title' => 'Mutfak Yenileme',
            'cat' => 'ince',
            'desc' => 'Mutfak yenilemesi için kapsam ve kaliteye göre ön bütçe oluşturun.'
        ],

        'banyo-maliyet-hesaplayici' => [
            'icon' => '🚿',
            'title' => 'Banyo Tadilatı',
            'cat' => 'ince',
            'desc' => 'Banyo yenilemesi için seramik, tesisat ve vitrifiye kapsamını değerlendirin.'
        ],

        'wc-maliyet-hesabi' => [
            'icon' => '🚽',
            'title' => 'WC Yenileme',
            'cat' => 'ince',
            'desc' => 'WC yenilemesi için yaklaşık malzeme ve uygulama bütçesi oluşturun.'
        ]
    ];

    /*
     * MEVCUT ARAÇ SÖZLÜĞÜ
     */

    $translations = [

        'ac-calculator' => ['t' => 'Klima Hesaplayıcı', 'c' => 'mekanik'],
        'alcipan-tavan-calculator' => ['t' => 'Alçıpan Tavan Hesaplayıcı', 'c' => 'ince'],
        'asansor-calculator' => ['t' => 'Asansör Hesaplayıcı', 'c' => 'mevzuat'],
        'auto-boq' => ['t' => 'Otomatik Keşif Özeti', 'c' => 'yatirim'],
        'banyo-calculator' => ['t' => 'Banyo Hesaplayıcı', 'c' => 'ince'],
        'bathroom-calculator' => ['t' => 'Banyo Hesaplayıcı', 'c' => 'ince'],
        'beton-calculator' => ['t' => 'Beton Hesaplayıcı', 'c' => 'kaba'],
        'building-cost-calculator' => ['t' => 'Bina İnşaat Maliyeti', 'c' => 'yatirim'],
        'building-weight' => ['t' => 'Bina Ağırlık Hesabı', 'c' => 'kaba'],
        'cable-calculator' => ['t' => 'Kablo Hesaplayıcı', 'c' => 'mekanik'],
        'cati-calculator' => ['t' => 'Çatı Hesaplayıcı', 'c' => 'kaba'],
        'cement-calculator' => ['t' => 'Çimento Hesaplayıcı', 'c' => 'kaba'],
        'cement-sand-calculator' => ['t' => 'Çimento Kum Hesaplayıcı', 'c' => 'kaba'],
        'column-capacity' => ['t' => 'Kolon Taşıma Kapasitesi', 'c' => 'kaba'],
        'column-starter-bar' => ['t' => 'Kolon Filiz Boyu', 'c' => 'kaba'],
        'concrete-calculator' => ['t' => 'Beton Hesaplayıcı', 'c' => 'kaba'],
        'core-strength' => ['t' => 'Karot Dayanımı', 'c' => 'kaba'],
        'cutting-optimization' => ['t' => 'Kesim Optimizasyonu', 'c' => 'yatirim'],
        'document-builder' => ['t' => 'Belge Oluşturucu', 'c' => 'yatirim'],
        'domed-tank' => ['t' => 'Bombeli Tank Hacmi', 'c' => 'kaba'],
        'drywall-calculator' => ['t' => 'Alçıpan Hesaplayıcı', 'c' => 'ince'],
        'duvar-calculator' => ['t' => 'Duvar Hesaplayıcı', 'c' => 'kaba'],
        'elevator-calculator' => ['t' => 'Asansör Hesaplayıcı', 'c' => 'mevzuat'],
        'energy-calculator' => ['t' => 'Enerji Hesaplayıcı', 'c' => 'mekanik'],
        'excavation-calculator' => ['t' => 'Hafriyat Hesaplayıcı', 'c' => 'kaba'],
        'flat-for-land-calculator' => ['t' => 'Kat Karşılığı Hesaplayıcı', 'c' => 'yatirim'],
        'formwork-calculator' => ['t' => 'Kalıp Hesaplayıcı', 'c' => 'kaba'],
        'garden-wall-calculator' => ['t' => 'Bahçe Duvarı Hesaplayıcı', 'c' => 'kaba'],
        'generator-calculator' => ['t' => 'Jeneratör Hesaplayıcı', 'c' => 'mekanik'],
        'ges-calculator' => ['t' => 'GES Hesaplayıcı', 'c' => 'mekanik'],
        'havalandirma-calculator' => ['t' => 'Havalandırma Hesaplayıcı', 'c' => 'mekanik'],
        'installment-vs-cash' => ['t' => 'Taksit - Peşin Kıyaslama', 'c' => 'yatirim'],
        'insulation-calculator' => ['t' => 'Yalıtım Hesaplayıcı', 'c' => 'mekanik'],
        'iscilik-calculator' => ['t' => 'İşçilik Hesaplayıcı', 'c' => 'mevzuat'],
        'iskele-calculator' => ['t' => 'İskele Hesaplayıcı', 'c' => 'kaba'],
        'isolator-calculator' => ['t' => 'İzolatör Hesaplayıcı', 'c' => 'mekanik'],
        'kablo-calculator' => ['t' => 'Kablo Hesaplayıcı', 'c' => 'mekanik'],
        'karo-calculator' => ['t' => 'Karo Hesaplayıcı', 'c' => 'ince'],
        'kat-karsiligi' => ['t' => 'Kat Karşılığı', 'c' => 'yatirim'],
        'kira-calculator' => ['t' => 'Kira Hesaplayıcı', 'c' => 'yatirim'],
        'kitchen-calculator' => ['t' => 'Mutfak Hesaplayıcı', 'c' => 'ince'],
        'klima-calculator' => ['t' => 'Klima Hesaplayıcı', 'c' => 'mekanik'],
        'labor-calculator' => ['t' => 'İşçilik Hesaplayıcı', 'c' => 'mevzuat'],
        'logistics-cost' => ['t' => 'Lojistik Maliyeti', 'c' => 'yatirim'],
        'machine-cost' => ['t' => 'İş Makinesi Maliyeti', 'c' => 'kaba'],
        'maliyet-hesapla' => ['t' => 'Maliyet Hesapla', 'c' => 'yatirim'],
        'mantolama-calculator' => ['t' => 'Mantolama Hesaplayıcı', 'c' => 'mekanik'],
        'map-scale-calculator' => ['t' => 'Harita Ölçek', 'c' => 'yatirim'],
        'material-weight' => ['t' => 'Malzeme Ağırlığı', 'c' => 'kaba'],
        'mortgage-calculator' => ['t' => 'Konut Kredisi', 'c' => 'yatirim'],
        'mutfak-calculator' => ['t' => 'Mutfak Hesaplayıcı', 'c' => 'ince'],
        'pafta-calculator' => ['t' => 'Pafta Hesaplayıcı', 'c' => 'yatirim'],
        'paint-calculator' => ['t' => 'Boya Hesaplayıcı', 'c' => 'ince'],
        'paint-visualizer' => ['t' => 'Boya Görselleştirici', 'c' => 'ince'],
        'parquet-calculator' => ['t' => 'Parke Hesaplayıcı', 'c' => 'ince'],
        'pdf-contract' => ['t' => 'PDF Sözleşme', 'c' => 'yatirim'],
        'plaster-calculator' => ['t' => 'Sıva Hesaplayıcı', 'c' => 'kaba'],
        'plumbing-calculator' => ['t' => 'Tesisat Hesaplayıcı', 'c' => 'mekanik'],
        'pump-power' => ['t' => 'Pompa Gücü', 'c' => 'mekanik'],
        'pvc-calculator' => ['t' => 'PVC Hesaplayıcı', 'c' => 'ince'],
        'radiator-calculator' => ['t' => 'Radyatör Hesaplayıcı', 'c' => 'mekanik'],
        'rainpipe-calculator' => ['t' => 'Yağmur Borusu', 'c' => 'mekanik'],
        'rebar-calculator' => ['t' => 'İnşaat Demiri (Donatı)', 'c' => 'kaba'],
        'rebar-equivalent-calculator' => ['t' => 'Donatı Eşdeğer', 'c' => 'kaba'],
        'rent-value' => ['t' => 'Kira Değeri', 'c' => 'yatirim'],
        'roof-calculator' => ['t' => 'Çatı Hesaplayıcı', 'c' => 'kaba'],
        'room-planner' => ['t' => 'Oda Planlayıcı', 'c' => 'ince'],
        'rough-plaster-calculator' => ['t' => 'Kaba Sıva Hesaplayıcı', 'c' => 'kaba'],
        'salary-calculator' => ['t' => 'Maaş Hesaplayıcı', 'c' => 'mevzuat'],
        'scaffolding-calculator' => ['t' => 'İskele Hesaplayıcı', 'c' => 'kaba'],
        'screed-calculator' => ['t' => 'Şap Hesaplayıcı', 'c' => 'kaba'],
        'sgk-calculator' => ['t' => 'SGK Hesaplayıcı', 'c' => 'mevzuat'],
        'sgk-labor' => ['t' => 'SGK İşçilik', 'c' => 'mevzuat'],
        'shelter-regulation' => ['t' => 'Sığınak Yönetmeliği', 'c' => 'mevzuat'],
        'simple-beam' => ['t' => 'Basit Kiriş Statik', 'c' => 'kaba'],
        'solar-calculator' => ['t' => 'Güneş Paneli (GES)', 'c' => 'mekanik'],
        'stair-calculator' => ['t' => 'Merdiven Hesaplayıcı', 'c' => 'kaba'],
        'stairs-calculator' => ['t' => 'Merdiven Hesaplayıcı', 'c' => 'kaba'],
        'static-project-fee' => ['t' => 'Statik Proje Bedeli', 'c' => 'yatirim'],
        'statik-calculator' => ['t' => 'Statik Hesaplayıcı', 'c' => 'yatirim'],
        'structural-dynamics' => ['t' => 'Yapı Dinamiği', 'c' => 'kaba'],
        'su-tesisat-calculator' => ['t' => 'Su Tesisat Hesaplayıcı', 'c' => 'mekanik'],
        'taks-kaks-calculator' => ['t' => 'TAKS / KAKS', 'c' => 'yatirim'],
        'tender-module' => ['t' => 'İhale Modülü', 'c' => 'yatirim'],
        'tile-calculator' => ['t' => 'Seramik / Fayans Hesaplayıcı', 'c' => 'ince'],
        'unit-price-calculator' => ['t' => 'Birim Fiyat Hesaplayıcı', 'c' => 'yatirim'],
        'ventilation-duct' => ['t' => 'Havalandırma Kanalı', 'c' => 'mekanik'],
        'volume-calculator' => ['t' => 'Hacim Hesaplayıcı', 'c' => 'kaba'],
        'wall-calculator' => ['t' => 'Duvar Hesaplayıcı', 'c' => 'kaba'],
        'wc-calculator' => ['t' => 'WC Hesaplayıcı', 'c' => 'ince'],
        'web-takeoff' => ['t' => 'Web Metraj', 'c' => 'yatirim'],
        'yalitim-calculator' => ['t' => 'Yalıtım Hesaplayıcı', 'c' => 'mekanik'],
        'zemin-calculator' => ['t' => 'Zemin Hesaplayıcı', 'c' => 'ince']
    ];

    /*
     * DOSYALARDAN ARAÇLARI OTOMATİK TOPLA
     */

    $toolConfig = config('worksitem_tools', []);

$categories = $toolConfig['categories'] ?? [];
$featured   = $toolConfig['featured'] ?? [];
$catalog    = $toolConfig['tools'] ?? [];

$allTools = [];

foreach ($catalog as $slug => $meta) {
    if ($slug === 'havalandirma-kanali') continue;

    $cat = $meta['cat'] ?? 'yatirim';

    $categoryMeta = $categories[$cat] ?? [
        'icon' => '🧰',
        'desc' => 'Teknik hesaplama aracı.',
    ];

    $allTools[] = [
        'slug' => $slug,

        'is_premium' => in_array(
            $slug,
            $featured,
            true
        ),

        'icon' => $meta['icon']
            ?? $categoryMeta['icon']
            ?? '🧰',

        'title' => $meta['title']
            ?? mb_convert_case(
                str_replace('-', ' ', $slug),
                MB_CASE_TITLE,
                'UTF-8'
            ),

        'desc' => $meta['desc']
            ?? $categoryMeta['desc']
            ?? 'Teknik hesaplama aracı.',

        'cat' => $cat,
    ];
}

usort($allTools, function ($a, $b) {

    $featuredOrder =
        $b['is_premium'] <=> $a['is_premium'];

    if ($featuredOrder !== 0) {
        return $featuredOrder;
    }

    return strcmp(
        $a['title'],
        $b['title']
    );
});

@endphp


{{-- =========================================================
     HEADER
========================================================== --}}
<main>

{{-- =========================================================
     HERO
========================================================== --}}

<section class="hero">

    <div class="container hero-inner">

        <div class="eyebrow">
            <span class="eyebrow-dot"></span>
            İnşaat ve tadilat projeleri için dijital hesaplama merkezi
        </div>

        <h1>
            İnşaat ve Tadilat
            <span>Maliyet Hesaplama Araçları</span>
        </h1>

        <p class="hero-subtitle">
            İnşaat maliyeti, tadilat bütçesi, metraj ve fizibilite için
            pratik hesaplama araçlarını tek yerde kullanın.
            Projeniz netleştiğinde profesyonel keşif ve teklif sürecine geçebilirsiniz.
        </p>

        <div class="hero-actions">

            <a
                href="/maliyet-hesapla"
                class="btn btn-primary"
            >
                İnşaat Maliyetini Hesapla →
            </a>

            <a
                href="/araclar/otomatik-kesif-fiyatlandirma"
                class="btn btn-secondary"
            >
                AI Ön Keşif →
            </a>

        </div>

    </div>

</section>
<x-site.audience-band />


{{-- =========================================================
     3 ANA TİCARİ ARAÇ
========================================================== --}}

<section class="featured">

    <div class="container">

        <div class="section-heading">

            <div class="section-kicker">
                Başlangıç için
            </div>

            <h2>
                Projeniz için doğru aracı seçin
            </h2>

            <p>
                Projenizin hangi aşamada olduğuna göre aşağıdaki araçlardan başlayabilirsiniz.
            </p>

        </div>


        <div class="featured-grid">

            <a
                href="/maliyet-hesapla"
                class="featured-card"
            >

                <div class="featured-icon">
                    🏗️
                </div>

                <h3>
                    İnşaat Maliyeti Hesapla
                </h3>

                <p>
                    Villa, bina ve farklı yapı projeleri için alan,
                    yapı sınıfı, kalite ve proje bilgilerine göre
                    yaklaşık bütçe oluşturun.
                </p>

                <div class="featured-link">
                    Maliyet hesabını başlat →
                </div>

            </a>


            <a href="/maliyet-hesapla" class="featured-card">

                <div class="featured-icon">
                    🛠️
                </div>

                <h3>
                    Tadilat Maliyeti Hesapla
                </h3>

                <p>
                    Ev, mutfak, banyo, ofis ve diğer tadilat projeleriniz
                    için kapsam ve kaliteye göre yaklaşık bütçe oluşturun.
                </p>

                <div class="featured-link">
                    Tadilat bütçesini hesapla →
                </div>

            </a>


            <a
                href="/araclar/otomatik-kesif-fiyatlandirma"
                class="featured-card ai"
            >

                <div class="featured-icon">
                    ✦
                </div>

                <h3>
                    AI Ön Keşif & Ön Fiyatlandırma
                </h3>

                <p>
                    Projenize ait PDF, görsel veya keşif dosyanızı
                    analiz ettirerek iş kalemleri ve yaklaşık maliyet
                    çalışması oluşturun.
                </p>

                <div class="featured-link">
                    Dosyanı analiz et →
                </div>

            </a>

        </div>

    </div>

</section>


{{-- =========================================================
     ALL TOOLS
========================================================== --}}

<section class="tools-section" id="tum-araclar">

    <div class="container">

        <div class="section-heading">

            <div class="section-kicker">
                WORKSİTEM araç merkezi
            </div>

            <h2>
                Tüm inşaat ve mühendislik araçları
            </h2>

            <p>
                Metraj, kaba yapı, ince işler, mekanik, enerji,
                yatırım ve teknik hesaplamalar için araçları arayın.
            </p>

        </div>


        <div class="tools-toolbar">

            <div class="toolbar-inner">

                <div class="search-box">

                    <span class="search-icon">
                        ⌕
                    </span>

                    <input
                        type="search"
                        id="searchInput"
                        autocomplete="off"
                        placeholder="Araç ara: beton, demir, boya, çatı, GES..."
                        aria-label="Araçlarda ara"
                    >

                </div>

                <div class="tool-count">
                    <span id="visibleCount">{{ count($allTools) }}</span>
                    / {{ count($allTools) }} araç
                </div>

            </div>


            <div
                class="filters"
                role="group"
                aria-label="Araç kategorileri"
            >

                <button
                    type="button"
                    class="filter-btn active"
                    data-cat="all"
                >
                    Tümü
                </button>

                <button
                    type="button"
                    class="filter-btn"
                    data-cat="yatirim"
                >
                    Yatırım & Proje
                </button>

                <button
                    type="button"
                    class="filter-btn"
                    data-cat="kaba"
                >
                    Kaba İnşaat
                </button>

                <button
                    type="button"
                    class="filter-btn"
                    data-cat="ince"
                >
                    İnce İşler
                </button>

                <button
                    type="button"
                    class="filter-btn"
                    data-cat="mekanik"
                >
                    Mekanik & Enerji
                </button>

                <button
                    type="button"
                    class="filter-btn"
                    data-cat="mevzuat"
                >
                    Mevzuat & İşçilik
                </button>

            </div>

        </div>


        <div
            class="tools-grid"
            id="toolsGrid"
        >

            <?php
                $qaConfig = config('worksitem_tools.tool_status', []);
                $qaDefault = $qaConfig['default'] ?? 'review';
                $qaOverrides = $qaConfig['overrides'] ?? [];
                $qaTestMode = !empty($qaConfig['staging_test_mode']);
            ?>

            <?php foreach ($allTools as $tool) { ?>

                <?php
                    $qaStatus = $qaOverrides[$tool['slug']] ?? $qaDefault;
                    $qaActive = $qaTestMode || $qaStatus === 'active';
                    $qaStaging = request()->getHost() === 'app.worksitem.com';
                    $qaClickable = $qaActive || $qaStaging;
                ?>

                <a
                    href="/araclar/{{ $tool['slug'] }}"
                    class="tool-card {{ $tool['is_premium'] ? 'is-premium' : '' }} {{ $qaActive ? 'qa-active' : 'qa-review' }}"
                    data-title="{{ $tool['title'] }}"
                    data-desc="{{ $tool['desc'] }}"
                    data-cat="{{ $tool['cat'] }}"
                    data-qa-status="{{ $qaStatus }}"
                    @if(!$qaClickable)
                        aria-disabled="true"
                        tabindex="-1"
                        title="Bu araç kalite kontrolünden geçiyor"
                        onclick="event.preventDefault(); return false;"
                    @endif
                >

                    <div class="tool-top">

                        <div class="tool-icon">
                            {{ $tool['icon'] }}
                        </div>

                        <span class="qa-status-badge {{ $qaActive ? 'active' : 'review' }}">
                            {{ $qaTestMode ? 'QA TEST' : ($qaActive ? 'Aktif' : 'İncelemede') }}
                        </span>

                    </div>

                    <div class="tool-title">
                        {{ $tool['title'] }}
                    </div>

                    <div class="tool-desc">
                        {{ $tool['desc'] }}
                    </div>

                    <div class="tool-action">
                        {{ $qaActive ? 'Hesaplamayı aç →' : ($qaStaging ? 'QA için aç →' : 'İncelemede') }}
                    </div>

                </a>

            <?php } ?>

            <div
                id="noResults"
                class="no-results"
            >
                <strong>Aradığınız araç bulunamadı.</strong>
                <br>
                Farklı bir kelime veya kategori deneyin.
            </div>

        </div>

    </div>

</section>


@include('components.collaboration-cta')

{{-- =========================================================
     SEO / SEMANTIC CONTENT
========================================================== --}}

<section class="seo-section">

    <div class="container seo-grid">

        <div class="seo-copy">

            <h2>
                İnşaat ve tadilat maliyeti nasıl hesaplanır?
            </h2>

            <p>
                Bir inşaat veya tadilat projesinin bütçesi yalnızca
                metrekare ile belirlenmez. Yapının kullanım amacı,
                toplam alanı, yapı sistemi, malzeme kalitesi,
                iş kapsamı, proje lokasyonu ve uygulama koşulları
                maliyeti etkileyebilir.
            </p>

            <p>
                WORKSİTEM araçları bu nedenle farklı proje türleri
                için ayrı hesaplama yöntemleri sunar. İlk aşamada
                yaklaşık bütçe oluşturabilir, daha detaylı bir
                projede ise metraj ve keşif çalışmalarına geçebilirsiniz.
            </p>

            <h3>
                Villa ve bina yapım maliyeti
            </h3>

            <p>
                Villa veya bina projesinde arsa bilgileri, toplam
                inşaat alanı, yapı sınıfı ve kalite seviyesi gibi
                değişkenler başlangıç bütçesinin değerlendirilmesinde
                önemlidir. İnşaat maliyeti hesaplama aracı bu aşamada
                ön bütçe oluşturmak için kullanılabilir.
            </p>

            <h3>
                Ev tadilat maliyeti
            </h3>

            <p>
                Ev tadilatında maliyet; tadilat kapsamına, alanlara,
                kullanılacak malzemelere ve tesisat gibi ek işlere
                göre değişir. Mutfak ve banyo gibi alanlarda kapsam
                genişledikçe toplam bütçe de değişebilir.
            </p>

            <h3>
                Fabrika ve ofis projeleri
            </h3>

            <p>
                Ticari ve endüstriyel projelerde keşif dosyaları,
                mimari çizimler, metrajlar ve teknik şartnameler
                daha önemli hale gelir. Bu tür projelerde AI Ön Keşif
                aracı başlangıçta dosyanın incelenmesine yardımcı
                olacak şekilde kullanılabilir.
            </p>

        </div>


        <div class="intent-list">

            <div class="intent-item">
                <strong>İnşaat maliyeti hesaplama</strong>
                <span>Yeni yapı ve bina projeleri için başlangıç bütçesi.</span>
            </div>

            <div class="intent-item">
                <strong>Tadilat maliyeti hesaplama</strong>
                <span>Ev, mutfak, banyo, ofis ve ticari alan yenilemeleri.</span>
            </div>

            <div class="intent-item">
                <strong>Metraj ve keşif</strong>
                <span>İş kalemlerini ve proje kapsamını daha detaylı inceleme.</span>
            </div>

            <div class="intent-item">
                <strong>Fizibilite</strong>
                <span>Yatırım öncesinde alan, maliyet ve proje ekonomisini değerlendirme.</span>
            </div>

            <div class="intent-item">
                <strong>Teknik hesaplamalar</strong>
                <span>Beton, demir, duvar, zemin, tesisat, enerji ve diğer hesaplar.</span>
            </div>

        </div>

    </div>

</section>


{{-- =========================================================
     KAINTA CONVERSION
========================================================== --}}

<section class="conversion-section">

    <div class="container">

        <div class="conversion-box">

            <div class="conversion-content">

                <div class="conversion-label">
                    Projeniz hazırsa sonraki adım
                </div>

                <h2>
                    Yaklaşık bütçeden profesyonel teklif sürecine geçin.
                </h2>

                <p>
                    WORKSİTEM ile projenizin kapsamını ve yaklaşık bütçesini
                    değerlendirdikten sonra, bütçeniz ve projeniz uygunsa
                    Kainta İnşaat ekibiyle keşif ve teklif sürecini başlatabilirsiniz.
                </p>

                <div class="conversion-actions">

                    <a
                        href="https://kainta.com"
                        class="btn conversion-primary"
                        target="_blank"
                        rel="noopener"
                    >
                        Bütçeniz uygunsa Kainta İnşaat'tan net teklif alın →
                    </a>

                    <a
                        href="/maliyet-hesapla"
                        class="btn conversion-secondary"
                    >
                        Önce Projeyi Hesapla
                    </a>

                </div>

            </div>

        </div>

    </div>

</section>


{{-- =========================================================
     FAQ
========================================================== --}}

<section class="faq-section">

    <div class="container">

        <div class="section-heading">

            <div class="section-kicker">
                Sık sorulan sorular
            </div>

            <h2>
                Maliyet ve hesaplama araçları hakkında
            </h2>

        </div>


        <div class="faq-list">

            <details class="faq-item">
                <summary>
                    İnşaat maliyeti nasıl hesaplanır?
                </summary>

                <div class="faq-answer">
                    İnşaat maliyeti; proje alanı, yapı türü, yapı sınıfı,
                    kalite seviyesi, iş kapsamı ve diğer proje değişkenleri
                    dikkate alınarak yaklaşık olarak hesaplanabilir.
                    Detaylı teklif için proje ve saha koşullarının ayrıca
                    değerlendirilmesi gerekir.
                </div>
            </details>


            <details class="faq-item">
                <summary>
                    Tadilat maliyeti metrekare üzerinden hesaplanabilir mi?
                </summary>

                <div class="faq-answer">
                    Metrekare, tadilat bütçesinin başlangıç değişkenlerinden
                    biridir ancak tek başına yeterli değildir. Tadilatın
                    kapsamı, malzeme kalitesi, tesisat işleri, söküm,
                    uygulama ve özel imalatlar toplam maliyeti değiştirebilir.
                </div>
            </details>


            <details class="faq-item">
                <summary>
                    AI Ön Keşif aracı ne işe yarar?
                </summary>

                <div class="faq-answer">
                    AI Ön Keşif aracı, proje dosyaları veya görsellerdeki
                    bilgilerin incelenmesine ve ön keşif çalışmasının
                    oluşturulmasına yardımcı olmak için tasarlanmıştır.
                    Teknik doğrulama gerektiren kalemler profesyonel keşif
                    sürecinde ayrıca kontrol edilmelidir.
                </div>
            </details>


            <details class="faq-item">
                <summary>
                    WORKSİTEM'deki hesaplama sonucu kesin teklif midir?
                </summary>

                <div class="faq-answer">
                    Hayır. Hesaplama araçları ön değerlendirme ve yaklaşık
                    bütçe oluşturmak içindir. Kesin teklif; projenin teknik
                    dokümanları, gerçek metraj, saha koşulları, malzeme
                    seçimleri ve uygulama kapsamı incelendikten sonra hazırlanır.
                </div>
            </details>


            <details class="faq-item">
                <summary>
                    Projem için Kainta İnşaat'tan teklif alabilir miyim?
                </summary>

                <div class="faq-answer">
                    Projenizin kapsamını ve bütçesini değerlendirdikten sonra
                    Kainta İnşaat üzerinden profesyonel keşif ve teklif sürecine
                    geçebilirsiniz.
                </div>
            </details>

        </div>

    </div>

</section>

</main>


{{-- =========================================================
     FOOTER
========================================================== --}}
{{-- =========================================================
     FAQ STRUCTURED DATA
========================================================== --}}

<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@type": "FAQPage",
    "mainEntity": [
        {
            "@type": "Question",
            "name": "İnşaat maliyeti nasıl hesaplanır?",
            "acceptedAnswer": {
                "@type": "Answer",
                "text": "İnşaat maliyeti; proje alanı, yapı türü, yapı sınıfı, kalite seviyesi, iş kapsamı ve diğer proje değişkenleri dikkate alınarak yaklaşık olarak hesaplanabilir. Detaylı teklif için proje ve saha koşullarının ayrıca değerlendirilmesi gerekir."
            }
        },
        {
            "@type": "Question",
            "name": "Tadilat maliyeti metrekare üzerinden hesaplanabilir mi?",
            "acceptedAnswer": {
                "@type": "Answer",
                "text": "Metrekare tadilat bütçesinin başlangıç değişkenlerinden biridir ancak tek başına yeterli değildir. Tadilatın kapsamı, malzeme kalitesi, tesisat işleri, söküm, uygulama ve özel imalatlar toplam maliyeti değiştirebilir."
            }
        },
        {
            "@type": "Question",
            "name": "AI Ön Keşif aracı ne işe yarar?",
            "acceptedAnswer": {
                "@type": "Answer",
                "text": "AI Ön Keşif aracı, proje dosyaları veya görsellerdeki bilgilerin incelenmesine ve ön keşif çalışmasının oluşturulmasına yardımcı olmak için tasarlanmıştır."
            }
        },
        {
            "@type": "Question",
            "name": "WORKSİTEM'deki hesaplama sonucu kesin teklif midir?",
            "acceptedAnswer": {
                "@type": "Answer",
                "text": "Hayır. Hesaplama araçları ön değerlendirme ve yaklaşık bütçe oluşturmak içindir. Kesin teklif; teknik dokümanlar, gerçek metraj, saha koşulları, malzeme seçimleri ve uygulama kapsamı incelendikten sonra hazırlanır."
            }
        },
        {
            "@type": "Question",
            "name": "Projem için Kainta İnşaat'tan teklif alabilir miyim?",
            "acceptedAnswer": {
                "@type": "Answer",
                "text": "Projenizin kapsamını ve bütçesini değerlendirdikten sonra Kainta İnşaat üzerinden profesyonel keşif ve teklif sürecine geçebilirsiniz."
            }
        }
    ]
}
</script>


<script>
document.addEventListener('DOMContentLoaded', function () {

    /* =========================================================
       SEARCH + CATEGORY FILTER
    ========================================================== */

    const searchInput = document.getElementById('searchInput');
    const cards = Array.from(document.querySelectorAll('.tool-card'));
    const filterButtons = Array.from(document.querySelectorAll('.filter-btn'));
    const noResults = document.getElementById('noResults');
    const visibleCount = document.getElementById('visibleCount');

    let activeCategory = 'all';


    function normalize(value) {

        if (!value) {
            return '';
        }

        return value
            .toLocaleLowerCase('tr-TR')
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .replace(/ı/g, 'i')
            .replace(/ğ/g, 'g')
            .replace(/ü/g, 'u')
            .replace(/ş/g, 's')
            .replace(/ö/g, 'o')
            .replace(/ç/g, 'c')
            .trim();

    }


    function filterTools() {

        const query = normalize(searchInput.value);
        let count = 0;

        cards.forEach(function (card) {

            const title = normalize(card.dataset.title || '');
            const desc = normalize(card.dataset.desc || '');
            const category = card.dataset.cat || '';

            const matchesSearch =
                !query ||
                title.includes(query) ||
                desc.includes(query);

            const matchesCategory =
                activeCategory === 'all' ||
                category === activeCategory;

            const visible =
                matchesSearch &&
                matchesCategory;

            card.style.display = visible ? 'flex' : 'none';

            if (visible) {
                count++;
            }

        });


        visibleCount.textContent = count;

        noResults.style.display =
            count === 0 ? 'block' : 'none';

    }


    searchInput.addEventListener(
        'input',
        filterTools
    );


    filterButtons.forEach(function (button) {

        button.addEventListener('click', function () {

            filterButtons.forEach(function (btn) {
                btn.classList.remove('active');
            });

            button.classList.add('active');

            activeCategory =
                button.dataset.cat || 'all';

            filterTools();

        });

    });


    /* =========================================================
       THEME
    ========================================================== */

    const themeToggle =
        document.getElementById('themeToggle');

    const themeIcon =
        document.getElementById('themeIcon');


    function applyTheme(theme) {

        const normalizedTheme =
            theme === "dark" ? "dark" : "light";

        document.documentElement.setAttribute(
            "data-theme",
            normalizedTheme
        );

        document.documentElement.classList.toggle(
            "dark",
            normalizedTheme === "dark"
        );

        if (themeIcon) {
            themeIcon.textContent =
                normalizedTheme === "dark"
                    ? "☀"
                    : "☾";
        }

    }


    const savedTheme =
        localStorage.getItem('worksitem-theme');


    if (savedTheme) {

        applyTheme(savedTheme);

    } else {

        applyTheme('light');

    }


    // Tema düğmesi artık ortak başlıkta (wk-header); bu sayfada yoksa dinleyici bağlanmaz
    if (themeToggle) themeToggle.addEventListener('click', function () {

        const dark =
            document.documentElement.classList.contains('dark');

        const nextTheme =
            dark ? 'light' : 'dark';

        applyTheme(nextTheme);

        localStorage.setItem(
            'worksitem-theme',
            nextTheme
        );

    });


    /* =========================================================
       KEYBOARD SHORTCUT
    ========================================================== */

    document.addEventListener('keydown', function (event) {

        if (
            event.key === '/' &&
            document.activeElement !== searchInput &&
            !['INPUT', 'TEXTAREA'].includes(
                document.activeElement.tagName
            )
        ) {

            event.preventDefault();
            searchInput.focus();

        }

    });

});
</script>

    <script src="/js/tool-events.js" defer></script>

<x-site.contech-footer />

</body>
</html>
