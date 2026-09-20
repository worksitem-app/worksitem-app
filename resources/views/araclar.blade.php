<!DOCTYPE html>
<html lang="tr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>İnşaat Hesaplama Araçları | Maliyet, Metraj ve Tadilat Hesapla | WORKSİTEM</title>

<meta name="description" content="İnşaat maliyeti, tadilat, metraj, betonarme, demir, boya, seramik, çatı, mantolama, elektrik, mekanik ve yatırım hesaplama araçlarını WORKSİTEM'de ücretsiz kullanın.">
<meta name="robots" content="index,follow,max-image-preview:large">
<link rel="canonical" href="https://worksitem.com/araclar">

<meta property="og:type" content="website">
<meta property="og:title" content="İnşaat Hesaplama Araçları | WORKSİTEM">
<meta property="og:description" content="İnşaat ve tadilat maliyetlerini, metrajları ve teknik hesapları saniyeler içinde yapın.">
<meta property="og:url" content="https://worksitem.com/araclar">
<meta property="og:site_name" content="WORKSİTEM">

<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="İnşaat Hesaplama Araçları | WORKSİTEM">
<meta name="twitter:description" content="İnşaat, tadilat, metraj ve mühendislik hesaplama araçları.">

<link rel="icon" href="/favicon.ico" type="image/x-icon">

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

<script>
(function(){
    const saved = localStorage.getItem('worksitem-theme');
    const systemDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
    document.documentElement.dataset.theme = saved || (systemDark ? 'dark' : 'light');
})();
</script>

<style>

/* =========================================================
   WORKSITEM ARAÇLAR SAYFASI
   CSS ISOLATION / GRID FIX
   ========================================================= */

{
    --ws-yellow:#ffb800;
    --ws-yellow-dark:#e3a000;

    --ws-bg:#f6f7f9;
    --ws-surface:#ffffff;
    --ws-surface-2:#f1f3f5;
    --ws-surface-3:#e8ebef;

    --ws-text:#101114;
    --ws-muted:#69707d;
    --ws-muted-2:#89909c;

    --ws-border:#e2e5e9;

    --ws-shadow:0 12px 40px rgba(15,23,42,.08);
    --ws-shadow-hover:0 18px 50px rgba(15,23,42,.14);

    --ws-radius:20px;
    --ws-radius-sm:14px;

    --ws-transition:.22s ease;

    width:100%;
    max-width:100%;
    overflow-x:hidden;
}

[data-theme="dark"]{
    --ws-bg:#070809;
    --ws-surface:#111315;
    --ws-surface-2:#181b1f;
    --ws-surface-3:#20242a;

    --ws-text:#f7f7f8;
    --ws-muted:#a1a7b0;
    --ws-muted-2:#727983;

    --ws-border:rgba(255,255,255,.09);

    --ws-shadow:0 15px 45px rgba(0,0,0,.35);
    --ws-shadow-hover:0 20px 60px rgba(0,0,0,.5);
}


/* ---------------------------------------------------------
   SAFE RESET
   --------------------------------------------------------- */

*,
*::before,
*::after{
    box-sizing:border-box;
}

img{
    max-width:100%;
}

a{
    color:inherit;
    text-decoration:none;
}

button,
input{
    font:inherit;
}


/* ---------------------------------------------------------
   CONTAINER
   --------------------------------------------------------- */

.container{
    width:100%;
    max-width:1240px;
    margin-left:auto;
    margin-right:auto;
    padding-left:16px;
    padding-right:16px;
}


/* ---------------------------------------------------------
   HEADER
   --------------------------------------------------------- */

.site-header{
    position:sticky;
    top:0;
    z-index:1000;

    width:100%;

    background:color-mix(
        in srgb,
        var(--ws-surface) 92%,
        transparent
    );

    backdrop-filter:blur(18px);

    border-bottom:1px solid var(--ws-border);
}

.header-inner{
    min-height:72px;

    display:flex;
    align-items:center;
    justify-content:space-between;

    gap:20px;
}

.logo{
    display:flex;
    align-items:center;

    gap:10px;

    font-weight:900;
    font-size:20px;

    letter-spacing:-.8px;

    flex-shrink:0;
}

.logo-mark{
    width:38px;
    height:38px;

    flex:0 0 38px;

    border-radius:11px;

    background:var(--ws-yellow);
    color:#111;

    display:grid;
    place-items:center;

    font-weight:900;

    box-shadow:0 5px 18px rgba(255,184,0,.25);
}

.header-actions{
    display:flex;
    align-items:center;

    gap:10px;

    flex-shrink:0;
}

.header-btn{
    min-height:42px;

    padding:0 15px;

    border:1px solid var(--ws-border);

    background:var(--ws-surface);
    color:var(--ws-text);

    border-radius:12px;

    cursor:pointer;

    font-weight:800;

    transition:var(--ws-transition);
}

.header-btn:hover{
    border-color:var(--ws-yellow);
}

.header-btn.primary{
    background:var(--ws-yellow);
    border-color:var(--ws-yellow);
    color:#111;
}


/* ---------------------------------------------------------
   HERO
   --------------------------------------------------------- */

.hero{
    width:100%;
    padding:55px 0 30px;
}

.hero-grid{
    width:100%;

    display:grid;

    grid-template-columns:
        minmax(0, 1.25fr)
        minmax(300px, .75fr);

    gap:30px;

    align-items:stretch;
}

.hero-main{
    min-width:0;

    background:
        radial-gradient(
            circle at 80% 10%,
            rgba(255,184,0,.18),
            transparent 35%
        ),
        var(--ws-surface);

    border:1px solid var(--ws-border);

    border-radius:28px;

    padding:46px;

    box-shadow:var(--ws-shadow);
}

.eyebrow{
    display:inline-flex;
    align-items:center;

    gap:8px;

    background:var(--ws-surface-2);

    border:1px solid var(--ws-border);

    padding:8px 12px;

    border-radius:999px;

    color:var(--ws-muted);

    font-size:12px;
    font-weight:900;

    text-transform:uppercase;
    letter-spacing:.8px;

    margin-bottom:20px;
}

.eyebrow span{
    width:7px;
    height:7px;

    flex:0 0 7px;

    border-radius:50%;

    background:#26c281;

    box-shadow:0 0 0 5px rgba(38,194,129,.1);
}

.hero h1{
    margin:0;

    font-size:clamp(34px,5vw,62px);

    line-height:1.02;

    letter-spacing:-2.8px;

    font-weight:900;

    max-width:800px;
}

.hero h1 strong{
    color:var(--ws-yellow);
}

.hero-description{
    margin:20px 0 28px;

    color:var(--ws-muted);

    font-size:17px;

    line-height:1.7;

    max-width:720px;
}

.hero-search{
    position:relative;
    width:100%;
}

.hero-search input{
    display:block;

    width:100%;

    min-width:0;

    min-height:64px;

    border:1px solid var(--ws-border);

    background:var(--ws-bg);

    color:var(--ws-text);

    border-radius:16px;

    padding:0 22px 0 55px;

    outline:none;

    font-size:16px;
    font-weight:600;

    transition:var(--ws-transition);
}

.hero-search input:focus{
    border-color:var(--ws-yellow);

    box-shadow:
        0 0 0 4px rgba(255,184,0,.12);
}

.search-icon{
    position:absolute;

    left:20px;
    top:50%;

    transform:translateY(-50%);

    font-size:21px;

    pointer-events:none;
}

.search-hint{
    margin-top:10px;

    color:var(--ws-muted-2);

    font-size:12px;
}


/* ---------------------------------------------------------
   HERO SIDE
   --------------------------------------------------------- */

.hero-side{
    min-width:0;

    display:flex;
    flex-direction:column;

    gap:14px;
}

.hero-side-card{
    min-width:0;

    flex:1;

    background:var(--ws-surface);

    border:1px solid var(--ws-border);

    border-radius:22px;

    padding:25px;

    box-shadow:var(--ws-shadow);

    overflow:hidden;
}

.hero-side-card.featured{
    background:#111;
    color:#fff;

    border-color:#272727;

    position:relative;
}

.hero-side-card.featured::after{
    content:"";

    position:absolute;

    width:170px;
    height:170px;

    border-radius:50%;

    background:rgba(255,184,0,.13);

    right:-70px;
    top:-80px;

    pointer-events:none;
}

.hero-side-card .icon{
    font-size:29px;
    margin-bottom:15px;
}

.hero-side-card h3{
    margin:0 0 8px;

    font-size:19px;

    font-weight:900;

    line-height:1.3;
}

.hero-side-card p{
    margin:0;

    color:var(--ws-muted);

    line-height:1.55;

    font-size:13px;
}

.featured p{
    color:#a7a7a7;
}

.featured-link{
    display:inline-flex;

    align-items:center;
    justify-content:center;

    margin-top:17px;

    background:var(--ws-yellow);
    color:#111;

    padding:11px 15px;

    border-radius:10px;

    font-weight:900;

    font-size:13px;

    position:relative;
    z-index:2;
}


/* ---------------------------------------------------------
   STATS
   --------------------------------------------------------- */

.stats{
    width:100%;

    display:grid;

    grid-template-columns:
        repeat(4,minmax(0,1fr));

    gap:12px;

    margin:15px 0 38px;
}

.stat{
    min-width:0;

    background:var(--ws-surface);

    border:1px solid var(--ws-border);

    border-radius:16px;

    padding:18px;
}

.stat strong{
    display:block;

    font-size:25px;

    font-weight:900;

    letter-spacing:-1px;
}

.stat span{
    display:block;

    color:var(--ws-muted);

    font-size:12px;

    margin-top:4px;
}


/* ---------------------------------------------------------
   CATEGORY BAR
   --------------------------------------------------------- */

.category-bar{
    position:sticky;

    top:72px;

    z-index:500;

    width:100%;

    background:color-mix(
        in srgb,
        var(--ws-bg) 92%,
        transparent
    );

    backdrop-filter:blur(15px);

    padding:10px 0;

    border-bottom:1px solid var(--ws-border);
}

.category-scroll{
    display:flex;

    gap:8px;

    overflow-x:auto;

    scrollbar-width:none;

    min-width:0;
}

.category-scroll::-webkit-scrollbar{
    display:none;
}

.category-btn{
    flex:0 0 auto;

    white-space:nowrap;

    border:1px solid var(--ws-border);

    background:var(--ws-surface);

    color:var(--ws-muted);

    border-radius:999px;

    padding:10px 15px;

    cursor:pointer;

    font-size:12px;

    font-weight:800;

    transition:var(--ws-transition);
}

.category-btn:hover,
.category-btn.active{
    background:var(--ws-yellow);

    color:#111;

    border-color:var(--ws-yellow);
}


/* ---------------------------------------------------------
   TOOLS SECTION
   --------------------------------------------------------- */

.tools-section{
    width:100%;

    padding:42px 0 70px;
}

.section-head{
    width:100%;

    display:flex;

    align-items:end;

    justify-content:space-between;

    gap:20px;

    margin-bottom:22px;
}

.section-title{
    margin:0;

    font-size:28px;

    letter-spacing:-1.2px;

    font-weight:900;
}

.section-subtitle{
    color:var(--ws-muted);

    margin-top:7px;

    font-size:14px;
}

.result-count{
    color:var(--ws-muted);

    font-size:13px;

    font-weight:700;

    white-space:nowrap;
}


/* =========================================================
   CRITICAL GRID FIX
   ========================================================= */

.tools-grid{

    display:grid !important;

    width:100% !important;

    max-width:100% !important;

    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)) !important;

    gap:14px !important;

    align-items:stretch;

    min-width:0;
}


/* ---------------------------------------------------------
   TOOL CARD
   --------------------------------------------------------- */

.tool-card{

    display:flex !important;

    flex-direction:column !important;

    width:100% !important;

    min-width:0 !important;

    max-width:none !important;

    min-height:245px;

    background:var(--ws-surface);

    border:1px solid var(--ws-border);

    border-radius:18px;

    padding:21px;

    transition:
        transform var(--ws-transition),
        box-shadow var(--ws-transition),
        border-color var(--ws-transition);

    position:relative;

    overflow:hidden;
}

.tool-card:hover{
    transform:translateY(-4px);

    box-shadow:var(--ws-shadow-hover);

    border-color:rgba(255,184,0,.45);
}

.tool-card.featured-tool{
    border-color:rgba(255,184,0,.35);
}

.tool-top{
    width:100%;

    display:flex;

    align-items:flex-start;

    justify-content:space-between;

    gap:10px;

    min-width:0;
}

.tool-icon{
    width:45px;
    height:45px;

    min-width:45px;

    flex:0 0 45px;

    border-radius:13px;

    display:grid;
    place-items:center;

    background:var(--ws-surface-2);

    font-size:22px;
}

.tool-badge{
    max-width:100%;

    background:rgba(255,184,0,.12);

    color:var(--ws-yellow-dark);

    border:1px solid rgba(255,184,0,.22);

    border-radius:999px;

    padding:5px 8px;

    font-size:9px;

    font-weight:900;

    text-transform:uppercase;

    white-space:nowrap;
}

[data-theme="dark"] .tool-badge{
    color:var(--ws-yellow);
}

.tool-card h3{
    margin:18px 0 8px;

    font-size:15px;

    line-height:1.35;

    letter-spacing:-.25px;

    font-weight:900;

    overflow-wrap:anywhere;
}

.tool-card p{
    margin:0;

    color:var(--ws-muted);

    font-size:12px;

    line-height:1.55;

    overflow-wrap:anywhere;
}

.tool-footer{
    margin-top:auto;

    padding-top:17px;

    display:flex;

    justify-content:space-between;

    align-items:center;

    gap:10px;

    min-width:0;
}

.tool-category{
    color:var(--ws-muted-2);

    font-size:10px;

    font-weight:800;

    min-width:0;
}

.tool-go{
    color:var(--ws-yellow-dark);

    font-size:12px;

    font-weight:900;

    white-space:nowrap;

    flex:0 0 auto;
}

[data-theme="dark"] .tool-go{
    color:var(--ws-yellow);
}

.tool-card.hidden{
    display:none !important;
}


/* ---------------------------------------------------------
   LEAD CTA
   --------------------------------------------------------- */

.lead-section{
    width:100%;

    margin:0 0 70px;
}

.lead-box{
    width:100%;

    min-width:0;

    border-radius:26px;

    background:#0d0e10;

    color:#fff;

    border:1px solid #27282b;

    padding:42px;

    display:grid;

    grid-template-columns:
        minmax(0,1fr)
        auto;

    align-items:center;

    gap:30px;

    overflow:hidden;

    position:relative;
}

.lead-box::before{
    content:"";

    position:absolute;

    width:280px;
    height:280px;

    border-radius:50%;

    background:rgba(255,184,0,.12);

    right:-80px;
    top:-150px;

    pointer-events:none;
}

.lead-box h2{
    margin:0 0 10px;

    font-size:31px;

    letter-spacing:-1.3px;

    line-height:1.15;
}

.lead-box p{
    margin:0;

    color:#a3a3a3;

    line-height:1.6;

    max-width:700px;

    font-size:14px;
}

.lead-button{
    position:relative;

    z-index:2;

    display:inline-flex;

    align-items:center;
    justify-content:center;

    white-space:nowrap;

    background:var(--ws-yellow);

    color:#111;

    padding:17px 22px;

    border-radius:13px;

    font-size:14px;

    font-weight:900;

    transition:var(--ws-transition);
}

.lead-button:hover{
    background:#fff;

    transform:translateY(-2px);
}


/* ---------------------------------------------------------
   EMPTY
   --------------------------------------------------------- */

.empty{
    display:none;

    width:100%;

    background:var(--ws-surface);

    border:1px dashed var(--ws-border);

    border-radius:18px;

    padding:45px;

    text-align:center;

    color:var(--ws-muted);
}

.empty strong{
    display:block;

    color:var(--ws-text);

    font-size:18px;

    margin-bottom:6px;
}


/* ---------------------------------------------------------
   FOOTER
   --------------------------------------------------------- */

footer{
    width:100%;

    border-top:1px solid var(--ws-border);

    background:var(--ws-surface);

    padding:38px 0;
}

.footer-inner{
    display:flex;

    justify-content:space-between;

    gap:25px;

    align-items:center;
}

.footer-brand{
    font-weight:900;
}

.footer-text{
    color:var(--ws-muted);

    font-size:12px;

    line-height:1.6;
}

.footer-links{
    display:flex;

    gap:15px;

    flex-wrap:wrap;
}

.footer-links a{
    color:var(--ws-muted);

    font-size:12px;

    font-weight:700;
}

.footer-links a:hover{
    color:var(--ws-yellow-dark);
}


/* =========================================================
   RESPONSIVE
   ========================================================= */


/* 3 COLUMN */
@media(max-width:1100px){

    .tools-grid{
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)) !important;
    }

}


/* TABLET */
@media(max-width:850px){

    .hero-grid{
        grid-template-columns:1fr;
    }

    .hero-side{
        display:grid;

        grid-template-columns:
            repeat(2,minmax(0,1fr));
    }

    .tools-grid{
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)) !important;
    }

    .stats{
        grid-template-columns:
            repeat(2,minmax(0,1fr));
    }

    .lead-box{
        grid-template-columns:1fr;
    }

}


/* MOBILE */
@media(max-width:600px){

    .container{
        width:100%;

        padding-left:10px;
        padding-right:10px;
    }

    .header-inner{
        min-height:62px;
    }

    .header-btn:not(.theme-btn){
        display:none;
    }

    .logo{
        font-size:17px;
    }

    .logo-mark{
        width:34px;
        height:34px;

        flex-basis:34px;
    }

    .hero{
        padding:22px 0 15px;
    }

    .hero-main{
        padding:25px 18px;

        border-radius:22px;
    }

    .hero h1{
        font-size:36px;

        letter-spacing:-1.8px;
    }

    .hero-description{
        font-size:14px;

        line-height:1.6;

        margin:15px 0 21px;
    }

    .hero-search input{
        min-height:58px;

        font-size:14px;

        padding-left:48px;
    }

    .hero-side{
        display:flex;

        flex-direction:column;
    }

    .hero-side-card{
        padding:20px;
    }

    
    .hero-side-card:not(.featured){
        display:none;
    }

    .stats{
        grid-template-columns:
            repeat(2,minmax(0,1fr));

        gap:8px;

        margin-bottom:25px;
    }

    .stat{
        padding:14px;
    }

    .stat strong{
        font-size:21px;
    }

    .category-bar{
        top:62px;
    }

    .tools-section{
        padding:30px 0 50px;
    }

    .section-head{
        align-items:flex-start;

        flex-direction:column;

        gap:5px;
    }

    .section-title{
        font-size:24px;
    }

    .tools-grid{
        grid-template-columns:
            minmax(0,1fr) !important;

        gap:10px !important;
    }

    .tool-card{
        min-height:auto;

        padding:18px;
    }

    .tool-card h3{
        font-size:16px;
    }

    .tool-card p{
        font-size:13px;
    }

    .tool-footer{
        padding-top:16px;
    }

    .lead-box{
        padding:27px 21px;

        border-radius:21px;
    }

    .lead-box h2{
        font-size:25px;
    }

    .lead-button{
        width:100%;

        text-align:center;
    }

    .footer-inner{
        flex-direction:column;

        align-items:flex-start;
    }

}


/* VERY SMALL DEVICES */
@media(max-width:380px){

    .hero h1{
        font-size:31px;
    }

    .stats{
        grid-template-columns:1fr;
    }

}


/* REDUCED MOTION */
@media(prefers-reduced-motion:reduce){

    *,
    *::before,
    *::after{
        scroll-behavior:auto!important;
        transition:none!important;
    }

}

</style>

<!-- STRUCTURED DATA -->
<script type="application/ld+json">
{
 "@context":"https://schema.org",
 "@type":"CollectionPage",
 "name":"İnşaat Hesaplama Araçları",
 "url":"https://worksitem.com/araclar",
 "description":"İnşaat, tadilat, metraj, maliyet, mühendislik ve yatırım hesaplama araçları.",
 "publisher":{
   "@type":"Organization",
   "name":"WORKSİTEM",
   "url":"https://worksitem.com"
 }
}
</script>

</head>

<body>

<header class="site-header">
    <div class="container header-inner">

        <a href="/" class="logo" aria-label="WORKSİTEM Anasayfa">
            <span class="logo-mark">W</span>
            <span>WORKSİTEM</span>
        </a>

        <div class="header-actions">
            <button class="header-btn theme-btn" id="themeToggle" type="button" aria-label="Tema değiştir">
                ☾ Tema
            </button>

            <a href="/maliyet-hesapla" class="header-btn primary">
                Maliyet Hesapla
            </a>
        </div>

    </div>
</header>


<main>

<section class="hero">
    <div class="container">

        <div class="hero-grid">

            <div class="hero-main">

                <div class="eyebrow">
                    <span></span>
                    2026 ConTech Araç Merkezi
                </div>

                <h1>
                    İnşaat hesabını<br>
                    <strong>saniyeler içinde yap.</strong>
                </h1>

                <p class="hero-description">
                    Maliyet, metraj, tadilat, betonarme, mühendislik,
                    yatırım ve şantiye hesaplamalarını tek merkezden yapın.
                    İhtiyacınız olan aracı bulun ve hemen hesaplamaya başlayın.
                </p>

                <div class="hero-search">
                    <span class="search-icon">⌕</span>
                    <input
                        type="search"
                        id="toolSearch"
                        autocomplete="off"
                        placeholder="Ne hesaplamak istiyorsunuz? Örn: boya, demir, beton, mutfak..."
                        aria-label="Hesaplama aracı ara"
                    >
                </div>

                <div class="search-hint">
                    Örn: “tadilat maliyeti”, “demir metrajı”, “çatı”, “beton”, “GES”
                </div>

            </div>


            <div class="hero-side">

                <div class="hero-side-card featured">

                    <div class="icon">🏗️</div>

                    <h3>Komple İnşaat Maliyeti</h3>

                    <p>
                        Arsanızdan anahtar teslim yapıya kadar
                        yaklaşık yatırım bütçenizi hesaplayın.
                    </p>

                    <a href="/maliyet-hesapla" class="featured-link">
                        Maliyeti Hesapla →
                    </a>

                </div>

                <div class="hero-side-card">

                    <div class="icon">✨</div>

                    <h3>AI Destekli Araçlar</h3>

                    <p>
                        Fotoğraf, proje ve verileriniz üzerinden
                        daha akıllı keşif ve tasarım süreçleri.
                    </p>

                </div>

            </div>

        </div>


        <div class="stats">

            <div class="stat">
                <strong>60+</strong>
                <span>Hesaplama Aracı</span>
            </div>

            <div class="stat">
                <strong>7</strong>
                <span>Ana Kategori</span>
            </div>

            <div class="stat">
                <strong>2026</strong>
                <span>Güncel Veri Odaklı</span>
            </div>

            <div class="stat">
                <strong>Ücretsiz</strong>
                <span>Hemen Kullan</span>
            </div>

        </div>

    </div>
</section>


<nav class="category-bar" aria-label="Araç kategorileri">
    <div class="container">
        <div class="category-scroll">

            <button class="category-btn active" data-category="all">
                Tümü
            </button>

            <button class="category-btn" data-category="maliyet">
                💰 Maliyet
            </button>

            <button class="category-btn" data-category="tadilat">
                🏠 Tadilat
            </button>

            <button class="category-btn" data-category="metraj">
                📐 Metraj
            </button>

            <button class="category-btn" data-category="muhendislik">
                🧱 Mühendislik
            </button>

            <button class="category-btn" data-category="mekanik">
                🔧 Mekanik
            </button>

            <button class="category-btn" data-category="yatirim">
                📊 Yatırım
            </button>

            <button class="category-btn" data-category="ai">
                ✨ AI
            </button>

        </div>
    </div>
</nav>


<section class="tools-section">
    <div class="container">

        <div class="section-head">

            <div>
                <h2 class="section-title">İnşaat Hesaplama Araçları</h2>
                <div class="section-subtitle">
                    İhtiyacınıza uygun aracı seçin ve hesabınızı hemen yapın.
                </div>
            </div>

            <div class="result-count" id="resultCount">
                0 araç
            </div>

        </div>


        <div class="tools-grid" id="toolsGrid">

            <a class="tool-card featured-tool"
               data-category="maliyet"
               data-search="maliyet bina inşaat komple yapı çşb anahtar teslim"
               href="/maliyet-hesapla">

                <div class="tool-top">
                    <div class="tool-icon">🏗️</div>
                    <span class="tool-badge">Popüler</span>
                </div>

                <h3>Komple Bina İnşaat Maliyeti</h3>

                <p>
                    Yapı sınıfı ve alan bilgisine göre
                    sıfırdan bina yatırım bütçesini hesaplayın.
                </p>

                <div class="tool-footer">
                    <span class="tool-category">Maliyet</span>
                    <span class="tool-go">Hesapla →</span>
                </div>

            </a>


            <a class="tool-card featured-tool"
               data-category="tadilat"
               data-search="banyo tadilat maliyet anahtar teslim banyo"
               href="/araclar/banyo-maliyet-hesaplayici">

                <div class="tool-top">
                    <div class="tool-icon">🚿</div>
                    <span class="tool-badge">Popüler</span>
                </div>

                <h3>Anahtar Teslim Banyo Tadilat Maliyeti</h3>

                <p>
                    Kırım, seramik, yalıtım, vitrifiye ve montaj
                    dahil banyo yenileme bütçesini hesaplayın.
                </p>

                <div class="tool-footer">
                    <span class="tool-category">Tadilat</span>
                    <span class="tool-go">Hesapla →</span>
                </div>

            </a>


            <a class="tool-card featured-tool"
               data-category="tadilat"
               data-search="mutfak mutfak yenileme tadilat dolap tezgah"
               href="/araclar/mutfak-maliyet-hesaplayici">

                <div class="tool-top">
                    <div class="tool-icon">🍳</div>
                    <span class="tool-badge">Popüler</span>
                </div>

                <h3>Komple Mutfak Yenileme Maliyeti</h3>

                <p>
                    Mutfak dolabı, tezgah, zemin ve diğer
                    yenileme kalemlerini yaklaşık hesaplayın.
                </p>

                <div class="tool-footer">
                    <span class="tool-category">Tadilat</span>
                    <span class="tool-go">Hesapla →</span>
                </div>

            </a>


            <a class="tool-card"
               data-category="maliyet"
               data-search="boya boya maliyet boya sarfiyat litre"
               href="/araclar/boya-hesaplayici">

                <div class="tool-top">
                    <div class="tool-icon">🎨</div>
                    <span class="tool-badge">Maliyet</span>
                </div>

                <h3>Boya Sarfiyat & Maliyet Hesaplayıcı</h3>

                <p>
                    Alanınıza göre boya miktarı ve yaklaşık
                    malzeme + işçilik maliyetini hesaplayın.
                </p>

                <div class="tool-footer">
                    <span class="tool-category">Maliyet</span>
                    <span class="tool-go">Hesapla →</span>
                </div>

            </a>


            <a class="tool-card"
               data-category="metraj"
               data-search="duvar tuğla gazbeton bims duvar metraj maliyet"
               href="/araclar/duvar-maliyet-hesaplayici">

                <div class="tool-top">
                    <div class="tool-icon">🧱</div>
                    <span class="tool-badge">Metraj</span>
                </div>

                <h3>Duvar Metraj & Maliyet Hesaplayıcı</h3>

                <p>
                    Tuğla, bims veya gazbeton duvar metrajını
                    ve yaklaşık maliyetini çıkarın.
                </p>

                <div class="tool-footer">
                    <span class="tool-category">Metraj</span>
                    <span class="tool-go">Hesapla →</span>
                </div>

            </a>


            <a class="tool-card"
               data-category="metraj"
               data-search="demir donatı çelik rebar tonaj metraj"
               href="/araclar/demir-metraji-hesaplayici">

                <div class="tool-top">
                    <div class="tool-icon">🔗</div>
                    <span class="tool-badge">Metraj</span>
                </div>

                <h3>İnşaat Demiri Metraj Hesaplayıcı</h3>

                <p>
                    Betonarme yapı için yaklaşık donatı tonajını
                    ve demir maliyetini hesaplayın.
                </p>

                <div class="tool-footer">
                    <span class="tool-category">Metraj</span>
                    <span class="tool-go">Hesapla →</span>
                </div>

            </a>


            <a class="tool-card"
               data-category="metraj"
               data-search="kalıp plywood betonarme kalıp metraj"
               href="/araclar/kalip-plywood-maliyeti">

                <div class="tool-top">
                    <div class="tool-icon">🪵</div>
                    <span class="tool-badge">Metraj</span>
                </div>

                <h3>Plywood Kalıp Metraj & Maliyet</h3>

                <p>
                    Betonarme kalıp yüzey alanı ve plywood
                    ihtiyacını hesaplayın.
                </p>

                <div class="tool-footer">
                    <span class="tool-category">Metraj</span>
                    <span class="tool-go">Hesapla →</span>
                </div>

            </a>


            <a class="tool-card"
               data-category="maliyet"
               data-search="çatı çatı alanı ahşap iskelet yalıtım"
               href="/araclar/cati-alani-maliyeti">

                <div class="tool-top">
                    <div class="tool-icon">🏠</div>
                    <span class="tool-badge">Maliyet</span>
                </div>

                <h3>Çatı Alanı & Maliyet Hesaplayıcı</h3>

                <p>
                    Eğim bilgisine göre gerçek çatı alanını
                    ve çatı yapım maliyetini hesaplayın.
                </p>

                <div class="tool-footer">
                    <span class="tool-category">Maliyet</span>
                    <span class="tool-go">Hesapla →</span>
                </div>

            </a>


            <a class="tool-card"
               data-category="maliyet"
               data-search="mantolama izolasyon eps xps taş yünü dış cephe"
               href="/araclar/mantolama-maliyeti">

                <div class="tool-top">
                    <div class="tool-icon">🌡️</div>
                    <span class="tool-badge">Maliyet</span>
                </div>

                <h3>Dış Cephe Mantolama Maliyeti</h3>

                <p>
                    EPS, XPS veya taşyünü seçenekleriyle
                    dış cephe ısı yalıtım maliyetini hesaplayın.
                </p>

                <div class="tool-footer">
                    <span class="tool-category">Maliyet</span>
                    <span class="tool-go">Hesapla →</span>
                </div>

            </a>


            <a class="tool-card"
               data-category="tadilat"
               data-search="parke laminat zemin döşeme maliyet"
               href="/araclar/parke-maliyet-hesaplayici">

                <div class="tool-top">
                    <div class="tool-icon">🪵</div>
                    <span class="tool-badge">Tadilat</span>
                </div>

                <h3>Laminat Parke & Zemin Maliyeti</h3>

                <p>
                    Fire payı, parke, şilte ve süpürgelik
                    ihtiyacını hesaplayın.
                </p>

                <div class="tool-footer">
                    <span class="tool-category">Tadilat</span>
                    <span class="tool-go">Hesapla →</span>
                </div>

            </a>


            <a class="tool-card"
               data-category="tadilat"
               data-search="seramik karo fayans yapıştırıcı derz metraj"
               href="/araclar/karo-hesaplayici">

                <div class="tool-top">
                    <div class="tool-icon">🧩</div>
                    <span class="tool-badge">Tadilat</span>
                </div>

                <h3>Karo & Seramik Maliyet Hesaplayıcı</h3>

                <p>
                    Seramik metrajı, fire, yapıştırıcı ve
                    derz ihtiyacını hesaplayın.
                </p>

                <div class="tool-footer">
                    <span class="tool-category">Tadilat</span>
                    <span class="tool-go">Hesapla →</span>
                </div>

            </a>


            <a class="tool-card"
               data-category="muhendislik"
               data-search="alçıpan asma tavan bölme duvar profil metraj"
               href="/araclar/alcipan-hesaplayici">

                <div class="tool-top">
                    <div class="tool-icon">🧱</div>
                    <span class="tool-badge">Metraj</span>
                </div>

                <h3>Alçıpan & Asma Tavan Hesaplayıcı</h3>

                <p>
                    Profil, plaka ve aksesuar ihtiyaçlarını
                    yaklaşık metrajla hesaplayın.
                </p>

                <div class="tool-footer">
                    <span class="tool-category">Metraj</span>
                    <span class="tool-go">Hesapla →</span>
                </div>

            </a>


            <a class="tool-card"
               data-category="maliyet"
               data-search="beton saha betonu c25 c30 hasır çelik"
               href="/araclar/saha-betonu-hesaplayici">

                <div class="tool-top">
                    <div class="tool-icon">🏗️</div>
                    <span class="tool-badge">Betonarme</span>
                </div>

                <h3>Saha Betonu Maliyet Hesaplayıcı</h3>

                <p>
                    Beton hacmi, hasır çelik ve yüzey
                    sertleştirici ihtiyacını hesaplayın.
                </p>

                <div class="tool-footer">
                    <span class="tool-category">Betonarme</span>
                    <span class="tool-go">Hesapla →</span>
                </div>

            </a>


            <a class="tool-card"
               data-category="metraj"
               data-search="şap zemin tesviye çimento self leveling"
               href="/araclar/sap-hesaplayici">

                <div class="tool-top">
                    <div class="tool-icon">📏</div>
                    <span class="tool-badge">Metraj</span>
                </div>

                <h3>Şap & Zemin Tesviye Hesaplayıcı</h3>

                <p>
                    Şap kalınlığı ve alanınıza göre
                    malzeme ihtiyacını hesaplayın.
                </p>

                <div class="tool-footer">
                    <span class="tool-category">Metraj</span>
                    <span class="tool-go">Hesapla →</span>
                </div>

            </a>


            <a class="tool-card"
               data-category="metraj"
               data-search="çimento kum harç şap sıva"
               href="/araclar/cimento-kum-hesaplama">

                <div class="tool-top">
                    <div class="tool-icon">🪣</div>
                    <span class="tool-badge">Sarfiyat</span>
                </div>

                <h3>Çimento & Kum Metraj Hesaplayıcı</h3>

                <p>
                    Harç, sıva veya şap için çimento ve
                    kum miktarını hesaplayın.
                </p>

                <div class="tool-footer">
                    <span class="tool-category">Sarfiyat</span>
                    <span class="tool-go">Hesapla →</span>
                </div>

            </a>


            <a class="tool-card"
               data-category="mekanik"
               data-search="su tesisat sıhhi tesisat boru pprc fitting"
               href="/araclar/su-tesisat-hesaplama">

                <div class="tool-top">
                    <div class="tool-icon">🚰</div>
                    <span class="tool-badge">Mekanik</span>
                </div>

                <h3>Sıhhi Tesisat Metraj Hesaplayıcı</h3>

                <p>
                    Banyo ve mutfak sayılarına göre
                    yaklaşık temiz/pis su tesisatı hesabı.
                </p>

                <div class="tool-footer">
                    <span class="tool-category">Mekanik</span>
                    <span class="tool-go">Hesapla →</span>
                </div>

            </a>


            <a class="tool-card"
               data-category="mekanik"
               data-search="klima btu klima kapasitesi iklimlendirme"
               href="/araclar/klima-btu-hesaplayici">

                <div class="tool-top">
                    <div class="tool-icon">❄️</div>
                    <span class="tool-badge">Mekanik</span>
                </div>

                <h3>Klima BTU Kapasite Hesaplayıcı</h3>

                <p>
                    Mekan alanı, kişi sayısı ve yalıtıma
                    göre gerekli klima kapasitesini hesaplayın.
                </p>

                <div class="tool-footer">
                    <span class="tool-category">Mekanik</span>
                    <span class="tool-go">Hesapla →</span>
                </div>

            </a>


            <a class="tool-card"
               data-category="mekanik"
               data-search="petek radyatör ısı kaybı kalorifer"
               href="/araclar/radyator-petek-hesaplayici">

                <div class="tool-top">
                    <div class="tool-icon">♨️</div>
                    <span class="tool-badge">Mekanik</span>
                </div>

                <h3>Petek & Radyatör Boyu Hesaplayıcı</h3>

                <p>
                    Oda hacmi ve yalıtım durumuna göre
                    yaklaşık radyatör metrajını hesaplayın.
                </p>

                <div class="tool-footer">
                    <span class="tool-category">Mekanik</span>
                    <span class="tool-go">Hesapla →</span>
                </div>

            </a>


            <a class="tool-card"
               data-category="muhendislik"
               data-search="elektrik kablo kesiti gerilim düşümü"
               href="/araclar/kablo-kesiti-hesabi">

                <div class="tool-top">
                    <div class="tool-icon">⚡</div>
                    <span class="tool-badge">Elektrik</span>
                </div>

                <h3>Elektrik Kablo Kesiti Hesaplayıcı</h3>

                <p>
                    Güç ve mesafe bilgilerine göre uygun
                    kablo kesitini değerlendirin.
                </p>

                <div class="tool-footer">
                    <span class="tool-category">Mühendislik</span>
                    <span class="tool-go">Hesapla →</span>
                </div>

            </a>


            <a class="tool-card"
               data-category="muhendislik"
               data-search="kolon taşıma kapasitesi beton donatı statik"
               href="/araclar/kolon-tasima-kapasitesi">

                <div class="tool-top">
                    <div class="tool-icon">🏛️</div>
                    <span class="tool-badge">Statik</span>
                </div>

                <h3>Kolon Taşıma Kapasitesi Hesabı</h3>

                <p>
                    Beton sınıfı ve donatı bilgileriyle
                    teorik eksenel taşıma kapasitesini hesaplayın.
                </p>

                <div class="tool-footer">
                    <span class="tool-category">Statik</span>
                    <span class="tool-go">Hesapla →</span>
                </div>

            </a>


            <a class="tool-card"
               data-category="muhendislik"
               data-search="basit kiriş statik moment kesme"
               href="/araclar/basit-kiris-statigi">

                <div class="tool-top">
                    <div class="tool-icon">📐</div>
                    <span class="tool-badge">Statik</span>
                </div>

                <h3>Basit Kiriş Statiği Hesaplayıcı</h3>

                <p>
                    Yayılı yük altında kirişin moment
                    ve kesme kuvvetini hesaplayın.
                </p>

                <div class="tool-footer">
                    <span class="tool-category">Statik</span>
                    <span class="tool-go">Hesapla →</span>
                </div>

            </a>


            <a class="tool-card"
               data-category="muhendislik"
               data-search="merdiven basamak rıht adım ergonomi"
               href="/araclar/merdiven-basamak-hesabi">

                <div class="tool-top">
                    <div class="tool-icon">🪜</div>
                    <span class="tool-badge">Mimari</span>
                </div>

                <h3>Mimari Merdiven Hesaplayıcı</h3>

                <p>
                    Rıht ve basamak ölçülerini ergonomik
                    formüle göre değerlendirin.
                </p>

                <div class="tool-footer">
                    <span class="tool-category">Mimari</span>
                    <span class="tool-go">Hesapla →</span>
                </div>

            </a>


            <a class="tool-card"
               data-category="muhendislik"
               data-search="asansör zorunluluğu kat bina yönetmelik"
               href="/araclar/asansor-zorunlulugu">

                <div class="tool-top">
                    <div class="tool-icon">🛗</div>
                    <span class="tool-badge">Mevzuat</span>
                </div>

                <h3>Asansör Zorunluluğu Kontrolü</h3>

                <p>
                    Bina kat ve yükseklik bilgilerine göre
                    asansör zorunluluğunu kontrol edin.
                </p>

                <div class="tool-footer">
                    <span class="tool-category">Mevzuat</span>
                    <span class="tool-go">Kontrol Et →</span>
                </div>

            </a>


            <a class="tool-card"
               data-category="yatirim"
               data-search="kat karşılığı arsa müteahhit paylaşım yatırım kar zarar roi"
               href="/araclar/kat-karsiligi-analiz">

                <div class="tool-top">
                    <div class="tool-icon">🤝</div>
                    <span class="tool-badge">Yatırım</span>
                </div>

                <h3>Kat Karşılığı Analiz</h3>

                <p>
                    Arsa sahibi ve müteahhit paylaşımını,
                    satış ve yatırım sonuçlarını analiz edin.
                </p>

                <div class="tool-footer">
                    <span class="tool-category">Yatırım</span>
                    <span class="tool-go">Analiz Et →</span>
                </div>

            </a>


            <a class="tool-card"
               data-category="yatirim"
               data-search="ges güneş enerji solar panel kw amortisman"
               href="/araclar/gunes-paneli-hesaplama">

                <div class="tool-top">
                    <div class="tool-icon">☀️</div>
                    <span class="tool-badge">Enerji</span>
                </div>

                <h3>GES Kurulum Fizibilitesi</h3>

                <p>
                    Elektrik tüketiminize göre güneş enerjisi
                    sistem kapasitesi ve yatırım fizibilitesi.
                </p>

                <div class="tool-footer">
                    <span class="tool-category">Yatırım</span>
                    <span class="tool-go">Analiz Et →</span>
                </div>

            </a>


            <a class="tool-card"
               data-category="yatirim"
               data-search="kira amortisman gayrimenkul yatırım kira değeri"
               href="/araclar/daire-kira-degeri">

                <div class="tool-top">
                    <div class="tool-icon">🏢</div>
                    <span class="tool-badge">Yatırım</span>
                </div>

                <h3>Kira & Amortisman Analizi</h3>

                <p>
                    Gayrimenkul değeri ve hedeflenen amortisman
                    süresine göre kira senaryosu oluşturun.
                </p>

                <div class="tool-footer">
                    <span class="tool-category">Yatırım</span>
                    <span class="tool-go">Analiz Et →</span>
                </div>

            </a>


            <a class="tool-card"
               data-category="ai"
               data-search="yapay zeka duvar rengi boya görselleştirme ai"
               href="/araclar/duvar-rengi-gorsellestirici">

                <div class="tool-top">
                    <div class="tool-icon">🎨</div>
                    <span class="tool-badge">AI</span>
                </div>

                <h3>AI Duvar Rengi Simülatörü</h3>

                <p>
                    Odanızın fotoğrafında farklı duvar
                    renklerini yapay zeka ile deneyin.
                </p>

                <div class="tool-footer">
                    <span class="tool-category">AI</span>
                    <span class="tool-go">Dene →</span>
                </div>

            </a>


            <a class="tool-card"
               data-category="ai"
               data-search="yapay zeka iç mimari tasarım oda dekorasyon fotoğraf"
               href="/ai-tasarim/ic-mekan">

                <div class="tool-top">
                    <div class="tool-icon">✨</div>
                    <span class="tool-badge">AI</span>
                </div>

                <h3>AI İç Mekan Tasarım</h3>

                <p>
                    Mekan fotoğrafınızı yapay zeka ile
                    farklı tasarım stillerinde yeniden yorumlayın.
                </p>

                <div class="tool-footer">
                    <span class="tool-category">AI</span>
                    <span class="tool-go">Tasarla →</span>
                </div>

            </a>


            <a class="tool-card"
               data-category="maliyet"
               data-search="işçilik maliyeti usta sgk işveren"
               href="/araclar/iscilik-maliyeti">

                <div class="tool-top">
                    <div class="tool-icon">👷</div>
                    <span class="tool-badge">Maliyet</span>
                </div>

                <h3>İşçilik Maliyeti Hesaplayıcı</h3>

                <p>
                    İşçilik ve personel maliyetlerini
                    yaklaşık olarak hesaplayın.
                </p>

                <div class="tool-footer">
                    <span class="tool-category">Maliyet</span>
                    <span class="tool-go">Hesapla →</span>
                </div>

            </a>


            <a class="tool-card"
               data-category="maliyet"
               data-search="nakliye kamyon hafriyat lojistik sefer tonaj"
               href="/araclar/lojistik-maliyeti">

                <div class="tool-top">
                    <div class="tool-icon">🚚</div>
                    <span class="tool-badge">Şantiye</span>
                </div>

                <h3>Nakliye & Kamyon Maliyeti</h3>

                <p>
                    Tonaj, sefer sayısı ve lojistik
                    operasyon maliyetini hesaplayın.
                </p>

                <div class="tool-footer">
                    <span class="tool-category">Şantiye</span>
                    <span class="tool-go">Hesapla →</span>
                </div>

            </a>


            <a class="tool-card"
               data-category="metraj"
               data-search="hafriyat kazı dolgu hacim kamyon sefer"
               href="/araclar/hafriyat-kazi-hesabi">

                <div class="tool-top">
                    <div class="tool-icon">🚜</div>
                    <span class="tool-badge">Hafriyat</span>
                </div>

                <h3>Kazı Hacmi & Hafriyat Hesabı</h3>

                <p>
                    Kazı hacmini, kabarma katsayısını ve
                    tahmini kamyon seferlerini hesaplayın.
                </p>

                <div class="tool-footer">
                    <span class="tool-category">Metraj</span>
                    <span class="tool-go">Hesapla →</span>
                </div>

            </a>


            <a class="tool-card"
               data-category="muhendislik"
               data-search="bina ağırlık betonarme ton deprem"
               href="/araclar/bina-agirlik-hesabi">

                <div class="tool-top">
                    <div class="tool-icon">🏢</div>
                    <span class="tool-badge">Statik</span>
                </div>

                <h3>Bina Ağırlık Hesabı</h3>

                <p>
                    Betonarme yapının yaklaşık toplam
                    kütlesini ve ağırlığını hesaplayın.
                </p>

                <div class="tool-footer">
                    <span class="tool-category">Statik</span>
                    <span class="tool-go">Hesapla →</span>
                </div>

            </a>


            <a class="tool-card"
               data-category="muhendislik"
               data-search="karot beton dayanım mpas çevirici"
               href="/araclar/karot-dayanim">

                <div class="tool-top">
                    <div class="tool-icon">🧪</div>
                    <span class="tool-badge">Statik</span>
                </div>

                <h3>Karot Dayanım Çevirici</h3>

                <p>
                    Karot kırılma yüküne göre eşdeğer
                    beton dayanımını değerlendirin.
                </p>

                <div class="tool-footer">
                    <span class="tool-category">Statik</span>
                    <span class="tool-go">Hesapla →</span>
                </div>

            </a>


            <a class="tool-card"
               data-category="muhendislik"
               data-search="kolon filiz bindirme boyu donatı ts500"
               href="/araclar/kolon-filiz-boyu">

                <div class="tool-top">
                    <div class="tool-icon">🔩</div>
                    <span class="tool-badge">Statik</span>
                </div>

                <h3>Kolon Filiz & Bindirme Boyu</h3>

                <p>
                    Donatı çapı ve beton sınıfına göre
                    kenetlenme uzunluğu hesabı.
                </p>

                <div class="tool-footer">
                    <span class="tool-category">Statik</span>
                    <span class="tool-go">Hesapla →</span>
                </div>

            </a>


            <a class="tool-card"
               data-category="muhendislik"
               data-search="statik proje bedeli mühendislik imo"
               href="/araclar/statik-proje-bedeli">

                <div class="tool-top">
                    <div class="tool-icon">📐</div>
                    <span class="tool-badge">Proje</span>
                </div>

                <h3>Statik Proje Bedeli</h3>

                <p>
                    Proje kapsamına göre yaklaşık mühendislik
                    hizmet bedelini değerlendirin.
                </p>

                <div class="tool-footer">
                    <span class="tool-category">Proje</span>
                    <span class="tool-go">Hesapla →</span>
                </div>

            </a>


            <a class="tool-card"
               data-category="maliyet"
               data-search="pvc pencere doğrama ısıcam pencere maliyet"
               href="/araclar/pvc-dograma-hesaplayici">

                <div class="tool-top">
                    <div class="tool-icon">🪟</div>
                    <span class="tool-badge">Cephe</span>
                </div>

                <h3>PVC Doğrama & Isıcam Hesaplayıcı</h3>

                <p>
                    Pencere alanı ve profil bilgilerine göre
                    yaklaşık doğrama maliyetini hesaplayın.
                </p>

                <div class="tool-footer">
                    <span class="tool-category">Cephe</span>
                    <span class="tool-go">Hesapla →</span>
                </div>

            </a>


            <a class="tool-card"
               data-category="mekanik"
               data-search="havalandırma kanal kesit debi menfez"
               href="/araclar/havalandirma-hesaplayici">

                <div class="tool-top">
                    <div class="tool-icon">💨</div>
                    <span class="tool-badge">Mekanik</span>
                </div>

                <h3>Havalandırma Kanalı Hesabı</h3>

                <p>
                    Hava debisi ve kanal hızına göre
                    gerekli kanal kesitini hesaplayın.
                </p>

                <div class="tool-footer">
                    <span class="tool-category">Mekanik</span>
                    <span class="tool-go">Hesapla →</span>
                </div>

            </a>


            <a class="tool-card"
               data-category="mekanik"
               data-search="pompa hidrofor basma yüksekliği debi kw"
               href="/araclar/pompa-gucu-hesabi">

                <div class="tool-top">
                    <div class="tool-icon">🚰</div>
                    <span class="tool-badge">Mekanik</span>
                </div>

                <h3>Pompa Gücü Hesaplayıcı</h3>

                <p>
                    Debi ve basma yüksekliğine göre
                    gerekli pompa gücünü hesaplayın.
                </p>

                <div class="tool-footer">
                    <span class="tool-category">Mekanik</span>
                    <span class="tool-go">Hesapla →</span>
                </div>

            </a>


            <a class="tool-card"
               data-category="metraj"
               data-search="hacim metreküp litre depo havuz kuyu"
               href="/araclar/metrekup-hacim-hesaplama">

                <div class="tool-top">
                    <div class="tool-icon">📦</div>
                    <span class="tool-badge">Matematik</span>
                </div>

                <h3>Hacim & Litre Hesaplayıcı</h3>

                <p>
                    Depo, havuz, kuyu ve temel alanlarının
                    hacmini m³ ve litre olarak hesaplayın.
                </p>

                <div class="tool-footer">
                    <span class="tool-category">Metraj</span>
                    <span class="tool-go">Hesapla →</span>
                </div>

            </a>


            <a class="tool-card"
               data-category="yatirim"
               data-search="tahmini bina maliyeti inşaat yatırım"
               href="/araclar/tahmini-bina-maliyeti">

                <div class="tool-top">
                    <div class="tool-icon">🏠</div>
                    <span class="tool-badge">Yatırım</span>
                </div>

                <h3>Tahmini Bina Maliyeti</h3>

                <p>
                    Yapı bilgilerine göre genel bina yatırım
                    maliyetini hızlıca tahmin edin.
                </p>

                <div class="tool-footer">
                    <span class="tool-category">Yatırım</span>
                    <span class="tool-go">Hesapla →</span>
                </div>

            </a>


            <a class="tool-card"
               data-category="yatirim"
               data-search="taks kaks imar emsal arsa inşaat alanı"
               href="/araclar/taks-kaks-hesaplayici">

                <div class="tool-top">
                    <div class="tool-icon">📍</div>
                    <span class="tool-badge">İmar</span>
                </div>

                <h3>TAKS / KAKS Hesaplayıcı</h3>

                <p>
                    Arsa alanı ve emsal bilgisine göre
                    yapılaşma kapasitesini hesaplayın.
                </p>

                <div class="tool-footer">
                    <span class="tool-category">İmar</span>
                    <span class="tool-go">Hesapla →</span>
                </div>

            </a>


            <a class="tool-card"
               data-category="maliyet"
               data-search="jeneratör kva demeraj güç şantiye"
               href="/araclar/jenerator-hesaplama">

                <div class="tool-top">
                    <div class="tool-icon">⚡</div>
                    <span class="tool-badge">Enerji</span>
                </div>

                <h3>Jeneratör KVA Hesaplayıcı</h3>

                <p>
                    Elektrikli cihazların yüküne göre
                    gerekli jeneratör kapasitesini hesaplayın.
                </p>

                <div class="tool-footer">
                    <span class="tool-category">Enerji</span>
                    <span class="tool-go">Hesapla →</span>
                </div>

            </a>


            <a class="tool-card"
               data-category="maliyet"
               data-search="iş makinesi ekskavatör loder vinç yakıt operatör"
               href="/araclar/is-makinesi-maliyeti">

                <div class="tool-top">
                    <div class="tool-icon">🚜</div>
                    <span class="tool-badge">Şantiye</span>
                </div>

                <h3>İş Makinesi Maliyeti</h3>

                <p>
                    Yakıt, operatör ve kiralama giderleriyle
                    makine operasyon maliyetini hesaplayın.
                </p>

                <div class="tool-footer">
                    <span class="tool-category">Şantiye</span>
                    <span class="tool-go">Hesapla →</span>
                </div>

            </a>


            <a class="tool-card"
               data-category="ai"
               data-search="otomatik metraj fiyatlandırma excel csv keşif yapay zeka"
               href="/araclar/otomatik-kesif-fiyatlandirma">

                <div class="tool-top">
                    <div class="tool-icon">🤖</div>
                    <span class="tool-badge">AI</span>
                </div>

                <h3>Otomatik Keşif & Fiyatlandırma</h3>

                <p>
                    Metraj listenizi kullanarak poz eşleştirme
                    ve yaklaşık maliyet çalışması oluşturun.
                </p>

                <div class="tool-footer">
                    <span class="tool-category">AI</span>
                    <span class="tool-go">Başlat →</span>
                </div>

            </a>


            <a class="tool-card"
               data-category="maliyet"
               data-search="birim fiyat analiz çşb poz rayiç"
               href="/araclar/birim-fiyat-analiz">

                <div class="tool-top">
                    <div class="tool-icon">📊</div>
                    <span class="tool-badge">Profesyonel</span>
                </div>

                <h3>Birim Fiyat Analizi</h3>

                <p>
                    Poz ve birim fiyat verilerini kullanarak
                    maliyet analizi yapın.
                </p>

                <div class="tool-footer">
                    <span class="tool-category">Maliyet</span>
                    <span class="tool-go">Analiz Et →</span>
                </div>

            </a>


            <a class="tool-card"
               data-category="maliyet"
               data-search="duvar boya maliyet alçı sıva"
               href="/araclar/alci-siva-hesaplama">

                <div class="tool-top">
                    <div class="tool-icon">🪣</div>
                    <span class="tool-badge">İnce İşler</span>
                </div>

                <h3>Alçı & Sıva Hesaplayıcı</h3>

                <p>
                    Alan ve uygulama tipine göre
                    yaklaşık sıva ve alçı sarfiyatını hesaplayın.
                </p>

                <div class="tool-footer">
                    <span class="tool-category">İnce İşler</span>
                    <span class="tool-go">Hesapla →</span>
                </div>

            </a>


            <a class="tool-card"
               data-category="metraj"
               data-search="kara sıva sıva kum çimento sarfiyat"
               href="/araclar/kara-siva-hesaplama">

                <div class="tool-top">
                    <div class="tool-icon">🧱</div>
                    <span class="tool-badge">Kaba İşler</span>
                </div>

                <h3>Kara Sıva Sarfiyat Hesabı</h3>

                <p>
                    Sıva kalınlığına göre kum ve çimento
                    ihtiyacını hesaplayın.
                </p>

                <div class="tool-footer">
                    <span class="tool-category">Kaba İşler</span>
                    <span class="tool-go">Hesapla →</span>
                </div>

            </a>


            <a class="tool-card"
               data-category="tadilat"
               data-search="wc misafir wc banyo tadilat vitrifiye fayans"
               href="/araclar/wc-maliyet-hesabi">

                <div class="tool-top">
                    <div class="tool-icon">🚽</div>
                    <span class="tool-badge">Tadilat</span>
                </div>

                <h3>WC & Islak Hacim Maliyeti</h3>

                <p>
                    Küçük ıslak hacim yenilemelerinde
                    yaklaşık anahtar teslim bütçeyi hesaplayın.
                </p>

                <div class="tool-footer">
                    <span class="tool-category">Tadilat</span>
                    <span class="tool-go">Hesapla →</span>
                </div>

            </a>


            <a class="tool-card"
               data-category="muhendislik"
               data-search="deprem izolatör sismik izolasyon bina"
               href="/araclar/deprem-izolator-maliyeti">

                <div class="tool-top">
                    <div class="tool-icon">🛡️</div>
                    <span class="tool-badge">Deprem</span>
                </div>

                <h3>Deprem İzolatörü Bütçe Analizi</h3>

                <p>
                    Bina özelliklerine göre sismik izolasyon
                    için yaklaşık bütçe senaryosu oluşturun.
                </p>

                <div class="tool-footer">
                    <span class="tool-category">Mühendislik</span>
                    <span class="tool-go">Analiz Et →</span>
                </div>

            </a>


            <a class="tool-card"
               data-category="muhendislik"
               data-search="sığınak yönetmeliği bina alan zorunluluk"
               href="/araclar/siginak-yonetmeligi">

                <div class="tool-top">
                    <div class="tool-icon">🛡️</div>
                    <span class="tool-badge">Mevzuat</span>
                </div>

                <h3>Sığınak Yönetmeliği Hesabı</h3>

                <p>
                    Yapı tipine göre sığınak alanı ve
                    ilgili gereklilikleri değerlendirin.
                </p>

                <div class="tool-footer">
                    <span class="tool-category">Mevzuat</span>
                    <span class="tool-go">Kontrol Et →</span>
                </div>

            </a>


            <a class="tool-card"
               data-category="metraj"
               data-search="malzeme ağırlığı yoğunluk ton lojistik"
               href="/araclar/malzeme-agirligi">

                <div class="tool-top">
                    <div class="tool-icon">⚖️</div>
                    <span class="tool-badge">Metraj</span>
                </div>

                <h3>İnşaat Malzeme Ağırlığı</h3>

                <p>
                    Malzemelerin yoğunluk ve miktarına göre
                    yaklaşık lojistik tonajını hesaplayın.
                </p>

                <div class="tool-footer">
                    <span class="tool-category">Metraj</span>
                    <span class="tool-go">Hesapla →</span>
                </div>

            </a>


            <a class="tool-card"
               data-category="mekanik"
               data-search="yağmur borusu çatı drenaj yağış pvc çap"
               href="/araclar/yagmur-borusu-hesabi">

                <div class="tool-top">
                    <div class="tool-icon">🌧️</div>
                    <span class="tool-badge">Hidrolik</span>
                </div>

                <h3>Yağmur Borusu Kesit Hesabı</h3>

                <p>
                    Çatı alanı ve yağış verilerine göre
                    tahliye borusu kesitini değerlendirin.
                </p>

                <div class="tool-footer">
                    <span class="tool-category">Hidrolik</span>
                    <span class="tool-go">Hesapla →</span>
                </div>

            </a>


            <a class="tool-card"
               data-category="muhendislik"
               data-search="yapı dinamiği doğal periyot deprem tbdy"
               href="/araclar/yapi-dinamigi">

                <div class="tool-top">
                    <div class="tool-icon">〰️</div>
                    <span class="tool-badge">Statik</span>
                </div>

                <h3>Yapı Dinamiği – Doğal Periyot</h3>

                <p>
                    Yapının yaklaşık doğal titreşim periyodunu
                    hesaplayın.
                </p>

                <div class="tool-footer">
                    <span class="tool-category">Statik</span>
                    <span class="tool-go">Hesapla →</span>
                </div>

            </a>


            <a class="tool-card"
               data-category="metraj"
               data-search="pafta ölçek mimari harita 1/50 1/100 1/500"
               href="/araclar/pafta-olcu-hesaplayici">

                <div class="tool-top">
                    <div class="tool-icon">🗺️</div>
                    <span class="tool-badge">Mimari</span>
                </div>

                <h3>Pafta & Ölçek Hesaplayıcı</h3>

                <p>
                    Pafta üzerindeki ölçüyü gerçek arazi
                    mesafesine dönüştürün.
                </p>

                <div class="tool-footer">
                    <span class="tool-category">Mimari</span>
                    <span class="tool-go">Hesapla →</span>
                </div>

            </a>


            <a class="tool-card"
               data-category="maliyet"
               data-search="iskele metraj güvenlikli iskele işçilik"
               href="/araclar/iskele-metraji-maliyeti">

                <div class="tool-top">
                    <div class="tool-icon">🏗️</div>
                    <span class="tool-badge">Şantiye</span>
                </div>

                <h3>İskele Metraj & Maliyet</h3>

                <p>
                    Cephe ölçülerine göre iskele metrajını
                    ve yaklaşık kurulum maliyetini hesaplayın.
                </p>

                <div class="tool-footer">
                    <span class="tool-category">Şantiye</span>
                    <span class="tool-go">Hesapla →</span>
                </div>

            </a>


            <a class="tool-card"
               data-category="maliyet"
               data-search="bahçe duvarı istinat duvarı betonarme taş maliyet"
               href="/araclar/bahce-duvari-maliyeti">

                <div class="tool-top">
                    <div class="tool-icon">🧱</div>
                    <span class="tool-badge">Peyzaj</span>
                </div>

                <h3>Bahçe & İstinat Duvarı Maliyeti</h3>

                <p>
                    Duvar tipine göre beton, kalıp ve
                    diğer kaba imalat maliyetlerini değerlendirin.
                </p>

                <div class="tool-footer">
                    <span class="tool-category">Peyzaj</span>
                    <span class="tool-go">Hesapla →</span>
                </div>

            </a>


            <a class="tool-card"
               data-category="maliyet"
               data-search="enerji tüketim elektrik fatura kw kwh"
               href="/araclar/enerji-tuketim-hesaplama">

                <div class="tool-top">
                    <div class="tool-icon">⚡</div>
                    <span class="tool-badge">Enerji</span>
                </div>

                <h3>Elektrik Enerji Tüketim Hesabı</h3>

                <p>
                    Cihazların güç ve kullanım sürelerinden
                    aylık enerji tüketimini hesaplayın.
                </p>

                <div class="tool-footer">
                    <span class="tool-category">Enerji</span>
                    <span class="tool-go">Hesapla →</span>
                </div>

            </a>


            <a class="tool-card"
               data-category="maliyet"
               data-search="sgk asgari işçilik ilişiksizlik inşaat"
               href="/araclar/sgk-asgari-iscilik">

                <div class="tool-top">
                    <div class="tool-icon">🏛️</div>
                    <span class="tool-badge">Mevzuat</span>
                </div>

                <h3>SGK Asgari İşçilik Hesabı</h3>

                <p>
                    İnşaat işlerinde asgari işçilik matrahı
                    ve prim senaryolarını inceleyin.
                </p>

                <div class="tool-footer">
                    <span class="tool-category">Mevzuat</span>
                    <span class="tool-go">Hesapla →</span>
                </div>

            </a>


            <a class="tool-card"
               data-category="maliyet"
               data-search="mühendis mimar maaş tmmob personel"
               href="/araclar/insaat-muhendisi-maas-hesabi">

                <div class="tool-top">
                    <div class="tool-icon">👷</div>
                    <span class="tool-badge">Personel</span>
                </div>

                <h3>İnşaat Mühendisi Maaş Analizi</h3>

                <p>
                    Deneyim ve pozisyona göre yaklaşık
                    sektörel maaş senaryolarını inceleyin.
                </p>

                <div class="tool-footer">
                    <span class="tool-category">Personel</span>
                    <span class="tool-go">Analiz Et →</span>
                </div>

            </a>


            <a class="tool-card"
               data-category="maliyet"
               data-search="asansör kuyu bina mimari"
               href="/araclar/asansor-zorunlulugu">

                <div class="tool-top">
                    <div class="tool-icon">🛗</div>
                    <span class="tool-badge">Mevzuat</span>
                </div>

                <h3>Asansör Zorunluluğu</h3>

                <p>
                    Bina özelliklerine göre asansör
                    gerekliliğini kontrol edin.
                </p>

                <div class="tool-footer">
                    <span class="tool-category">Mevzuat</span>
                    <span class="tool-go">Kontrol Et →</span>
                </div>

            </a>

        </div>


        <div class="empty" id="emptyState">
            <strong>Aradığınız araç bulunamadı.</strong>
            Farklı bir kelime deneyin: boya, beton, demir, mutfak, çatı...
        </div>

    </div>
</section>


<section class="lead-section">
    <div class="container">

        <div class="lead-box">

            <div>
                <h2>Hesaplamak yetmez. Projeyi hayata geçirelim.</h2>

                <p>
                    Yaklaşık maliyetinizi gördünüz.
                    Şimdi projeniz için profesyonel keşif ve teklif almak
                    istiyorsanız Kainta İnşaat ekibine talebinizi iletin.
                </p>
            </div>

            <a href="/maliyet-hesapla" class="lead-button">
                Ücretsiz Maliyet Hesapla →
            </a>

        </div>

    </div>
</section>

</main>


<footer>
    <div class="container footer-inner">

        <div>
            <div class="footer-brand">WORKSİTEM</div>
            <div class="footer-text">
                İnşaat ve yapı sektörünün dijital araç merkezi.
            </div>
        </div>

        <div class="footer-links">
            <a href="/">Anasayfa</a>
            <a href="/araclar">Araçlar</a>
            <a href="/maliyet-hesapla">Maliyet Hesapla</a>
            <a href="/firmalar">Firmalar</a>
        </div>

    </div>
</footer>


<script>
(function(){

    const searchInput = document.getElementById('toolSearch');
    const cards = Array.from(document.querySelectorAll('.tool-card'));
    const categoryButtons = Array.from(document.querySelectorAll('.category-btn'));
    const resultCount = document.getElementById('resultCount');
    const emptyState = document.getElementById('emptyState');
    const themeToggle = document.getElementById('themeToggle');

    let activeCategory = 'all';


    function normalize(text){
        return (text || '')
            .toLocaleLowerCase('tr-TR')
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g,'');
    }


    function filterTools(){

        const query = normalize(searchInput.value.trim());

        let visible = 0;

        cards.forEach(function(card){

            const category = card.dataset.category || '';
            const searchable = normalize(
                (card.innerText || '') + ' ' + (card.dataset.search || '')
            );

            const categoryMatch =
                activeCategory === 'all' ||
                category === activeCategory;

            const searchMatch =
                !query ||
                searchable.includes(query);

            const show = categoryMatch && searchMatch;

            card.classList.toggle('hidden',!show);

            if(show){
                visible++;
            }

        });

        resultCount.textContent = visible + ' araç';

        emptyState.style.display =
            visible === 0 ? 'block' : 'none';
    }


    searchInput.addEventListener('input',filterTools);


    categoryButtons.forEach(function(button){

        button.addEventListener('click',function(){

            categoryButtons.forEach(function(btn){
                btn.classList.remove('active');
            });

            button.classList.add('active');

            activeCategory = button.dataset.category || 'all';

            filterTools();

            if(window.innerWidth < 700){
                document.querySelector('.tools-section')
                    .scrollIntoView({
                        behavior:'smooth',
                        block:'start'
                    });
            }

        });

    });


    function updateThemeButton(){

        const theme = document.documentElement.dataset.theme;

        if(theme === 'dark'){
            themeToggle.innerHTML = '☀️ Açık';
        }else{
            themeToggle.innerHTML = '🌙 Koyu';
        }

    }


    themeToggle.addEventListener('click',function(){

        const current =
            document.documentElement.dataset.theme;

        const next =
            current === 'dark' ? 'light' : 'dark';

        document.documentElement.dataset.theme = next;

        localStorage.setItem(
            'worksitem-theme',
            next
        );

        updateThemeButton();

    });


    updateThemeButton();
    filterTools();

})();
</script>


</body>
</html>
<style>
/* =========================================================
   WORKSITEM - GRID / CARD FIX
   ========================================================= */

html,
body {
    width: 100%;
    max-width: 100%;
    overflow-x: hidden !important;
}

*,
*::before,
*::after {
    box-sizing: border-box;
}

{
    width: 100% !important;
    max-width: 100% !important;
    min-width: 0 !important;
    overflow-x: hidden !important;
}

.container {
    width: min(1240px, calc(100% - 32px)) !important;
    max-width: 1240px !important;
    min-width: 0 !important;
    margin-left: auto !important;
    margin-right: auto !important;
}

.tools-section {
    width: 100% !important;
    max-width: 100% !important;
    min-width: 0 !important;
}

.tools-grid {
    display: grid !important;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)) !important;
    width: 100% !important;
    max-width: 100% !important;
    min-width: 0 !important;
    gap: 14px !important;
}

.tool-card {
    display: flex !important;
    flex-direction: column !important;
    width: 100% !important;
    max-width: 100% !important;
    min-width: 0 !important;
    box-sizing: border-box !important;
    overflow: hidden !important;
}

.tool-card > * {
    min-width: 0 !important;
    max-width: 100% !important;
}

.tool-card h3,
.tool-card p {
    max-width: 100% !important;
    min-width: 0 !important;
    overflow-wrap: anywhere !important;
    word-break: normal !important;
}

img {
    max-width: 100%;
}

@media (max-width: 1100px) {
    .tools-grid {
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)) !important;
    }
}

@media (max-width: 850px) {
    .tools-grid {
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)) !important;
    }
}

@media (max-width: 600px) {
    .container {
        width: calc(100% - 20px) !important;
    }

    .tools-grid {
        grid-template-columns: minmax(0, 1fr) !important;
        gap: 10px !important;
    }
}
</style>

