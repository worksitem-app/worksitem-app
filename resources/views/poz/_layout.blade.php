<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">

  <title>@yield('title', 'WORKSİTEM')</title>
  <meta name="description" content="@yield('description', 'WORKSİTEM — inşaat ve tadilat maliyeti, poz ve birim fiyat platformu')">
  <meta name="robots" content="@yield('robots', 'index,follow,max-image-preview:large,max-snippet:-1,max-video-preview:-1')">
  <link rel="canonical" href="@yield('canonical', url('/'))">

  @hasSection('keywords')
  <meta name="keywords" content="@yield('keywords')">
  @endif

  <meta property="og:type" content="website">
  <meta property="og:site_name" content="WORKSİTEM">
  <meta property="og:locale" content="tr_TR">
  <meta property="og:title" content="@yield('title', 'WORKSİTEM')">
  <meta property="og:description" content="@yield('description', 'WORKSİTEM — inşaat ve tadilat maliyeti, poz ve birim fiyat platformu')">
  <meta property="og:url" content="@yield('canonical', url('/'))">

  <link rel="icon" href="/favicon.ico">
  <link rel="stylesheet" href="/css/worksitem-design.css">

  <script>
  (() => {
    let theme;
    try { theme = localStorage.getItem('worksitem-theme'); } catch (error) {}
    if (theme !== 'light' && theme !== 'dark') {
      theme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
    }
    document.documentElement.dataset.theme = theme;
  })();
  </script>

  <style>
    :root{
      --bg:#f6f7f9;
      --surface:#ffffff;
      --surface-2:#eef1f4;
      --text:#111318;
      --muted:#656b75;
      --line:rgba(17,19,24,.10);
      --yellow:#ffb800;
      --yellow-dark:#9b6800;
      --green:#137a47;
      --black:#08090a;
      --shadow:0 18px 50px rgba(0,0,0,.07);
      --max:1180px;
    }
    html[data-theme=dark]{
      --bg:#10151d;
      --surface:#18212c;
      --surface-2:#202c3a;
      --text:#f3f6fa;
      --muted:#b4c0ce;
      --line:rgba(230,238,250,.16);
      --yellow-dark:#ffc94b;
      --green:#72db9b;
      --shadow:0 22px 55px rgba(0,0,0,.30);
    }
    *{box-sizing:border-box}
    html{scroll-behavior:smooth}
    body{margin:0;font-family:Inter,ui-sans-serif,-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Arial,sans-serif;background:var(--bg);color:var(--text);line-height:1.6;-webkit-font-smoothing:antialiased;overflow-x:hidden}
    a{color:inherit;text-decoration:none}
    button,input,select{font:inherit}
    .poz-shell{width:min(calc(100% - 32px),var(--max));margin:auto}
    .poz-main{min-height:60vh;padding:18px 0 72px}
    .breadcrumb{display:flex;align-items:center;flex-wrap:wrap;gap:8px;margin:8px 0 20px;color:var(--muted);font-size:12px;font-weight:650}
    .breadcrumb a:hover{color:var(--yellow-dark)}
    .breadcrumb>span{opacity:.75}

    /* Liste sayfası mevcut class uyumluluğu */
    .hero{padding:20px 0 22px}
    .hero h1{margin:0;font-size:clamp(34px,4.8vw,56px);line-height:1.04;letter-spacing:-2px;font-weight:950}
    .hero h1 span{color:var(--ws-highlight,#9A6E00)}
    .hero p{margin:14px 0 0;color:var(--muted)}
    .stats{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:12px;margin:18px 0}
    .stat{background:var(--surface);border:1px solid var(--line);border-radius:16px;padding:20px;box-shadow:var(--shadow)}
    .stat-value{font-size:28px;font-weight:950;letter-spacing:-1px}
    .stat-label{margin-top:3px;color:var(--muted);font-size:11px;font-weight:850;text-transform:uppercase;letter-spacing:.7px}
    .search-box{display:grid;grid-template-columns:minmax(0,2fr) minmax(190px,1fr) auto;gap:10px;margin:18px 0;padding:14px;background:var(--surface);border:1px solid var(--line);border-radius:16px;box-shadow:var(--shadow)}
    .search-box input,.search-box select{min-height:48px;width:100%;border:1px solid var(--line);border-radius:11px;background:var(--surface-2);color:var(--text);padding:0 14px}
    .search-box button{min-height:48px;padding:0 24px;border:0;border-radius:11px;background:var(--yellow);color:#080808;font-weight:900;cursor:pointer}
    .poz-list{overflow:hidden;background:var(--surface);border:1px solid var(--line);border-radius:18px;box-shadow:var(--shadow)}
    .poz-item{display:flex;align-items:center;gap:16px;justify-content:space-between;padding:17px 20px;border-bottom:1px solid var(--line)}
    .poz-item:last-child{border-bottom:0}
    .poz-item:hover{background:var(--surface-2)}
    .poz-item>a{display:flex;align-items:center;gap:12px;min-width:0;flex:1}
    .poz-code{flex:0 0 auto;display:inline-flex;align-items:center;min-height:30px;padding:0 9px;border-radius:8px;background:var(--surface-2);font-family:ui-monospace,SFMono-Regular,Menlo,monospace;font-size:12px;font-weight:850}
    .poz-price{flex:0 0 auto;color:var(--green);font-weight:900;white-space:nowrap;font-size:13px}

    /* Detay */
    .poz-detail-hero{position:relative;overflow:hidden;padding:32px;background:var(--surface);border:1px solid var(--line);border-radius:24px;box-shadow:var(--shadow)}
    .poz-detail-hero:after{content:"";position:absolute;width:340px;height:340px;right:-170px;top:-180px;border-radius:50%;background:rgba(255,184,0,.14);filter:blur(10px);pointer-events:none}
    .poz-kicker{position:relative;z-index:1;display:inline-flex;align-items:center;gap:8px;color:var(--yellow-dark);font-size:11px;font-weight:950;letter-spacing:1px;text-transform:uppercase}
    .poz-kicker:before{content:"";width:7px;height:7px;border-radius:50%;background:var(--yellow)}
    .poz-title{position:relative;z-index:1;max-width:980px;margin:12px 0 0;font-size:clamp(28px,3.4vw,40px);line-height:1.08;letter-spacing:-1.8px;font-weight:950}
    .poz-title-code{color:var(--yellow-dark)}
    .poz-lead{position:relative;z-index:1;max-width:900px;margin:16px 0 0;color:var(--muted);font-size:15px}
    .poz-badges{position:relative;z-index:1;display:flex;flex-wrap:wrap;gap:8px;margin-top:20px}
    .poz-badge{display:inline-flex;align-items:center;min-height:34px;padding:0 11px;border:1px solid var(--line);border-radius:999px;background:var(--surface-2);font-size:11px;font-weight:800;color:var(--muted)}
    .poz-badge strong{color:var(--text);margin-left:5px}
    .poz-actions{position:relative;z-index:1;display:flex;flex-wrap:wrap;gap:10px;margin-top:22px}
    .poz-btn{min-height:48px;padding:0 18px;display:inline-flex;align-items:center;justify-content:center;border-radius:12px;font-size:13px;font-weight:900;border:1px solid var(--line);background:var(--surface);color:var(--text);transition:.18s ease}
    .poz-btn:hover{transform:translateY(-1px)}
    .poz-btn-primary{background:var(--yellow);color:#080808;border-color:var(--yellow)}
    .poz-grid{display:grid;grid-template-columns:minmax(0,1.65fr) minmax(300px,.72fr);gap:18px;align-items:start;margin-top:18px}
    .poz-card{background:var(--surface);border:1px solid var(--line);border-radius:20px;padding:24px;box-shadow:var(--shadow)}
    .poz-card + .poz-card{margin-top:18px}
    .poz-card-head{display:flex;align-items:flex-start;justify-content:space-between;gap:12px;margin-bottom:16px}
    .poz-card-kicker{color:var(--yellow-dark);font-size:10px;font-weight:950;letter-spacing:.9px;text-transform:uppercase}
    .poz-card h2{margin:3px 0 0;font-size:22px;line-height:1.2;letter-spacing:-.6px;font-weight:950}
    .poz-details{display:grid}
    .poz-details-row{display:grid;grid-template-columns:160px minmax(0,1fr);gap:18px;padding:14px 0;border-bottom:1px solid var(--line)}
    .poz-details-row:last-child{border-bottom:0}
    .poz-details-label{color:var(--muted);font-size:12px;font-weight:750}
    .poz-details-value{font-size:14px;font-weight:750;overflow-wrap:anywhere}
    .poz-details-value code{padding:4px 8px;border-radius:7px;background:var(--surface-2);font-weight:900}
    .poz-summary{position:sticky;top:92px}
    .poz-price-label{color:var(--muted);font-size:11px;font-weight:900;text-transform:uppercase;letter-spacing:.8px}
    .poz-price-value{margin-top:4px;font-size:clamp(32px,4vw,46px);line-height:1;letter-spacing:-1.8px;font-weight:950;color:var(--green)}
    .poz-price-unit{margin-top:6px;color:var(--muted);font-size:12px;font-weight:750}
    .poz-summary-list{margin-top:18px;padding-top:6px;border-top:1px solid var(--line)}
    .poz-summary-row{display:flex;align-items:flex-start;justify-content:space-between;gap:14px;padding:11px 0;border-bottom:1px solid var(--line);font-size:12px}
    .poz-summary-row:last-child{border-bottom:0}
    .poz-summary-row span{color:var(--muted)}
    .poz-summary-row strong{text-align:right}
    /* v113: tutar hesabı, ilgili pozlar, kurum varyantları, hub listeleri */
    .poz-calc{margin-top:16px;padding:14px;border:1px solid var(--line);border-radius:14px;background:var(--surface-2)}
    .poz-calc label{display:block;font-size:12px;font-weight:800;color:var(--muted);margin-bottom:6px}
    .poz-calc input{width:100%;min-height:44px;padding:0 12px;border:1px solid var(--line);border-radius:10px;font-size:16px;background:var(--surface);color:var(--text)}
    .poz-calc-out{display:grid;gap:6px;margin-top:10px}
    .poz-calc-out div{display:flex;justify-content:space-between;gap:10px;font-size:13px}
    .poz-calc-out span{color:var(--muted)}
    .poz-related{list-style:none;margin:0;padding:0}
    .poz-related li{display:flex;justify-content:space-between;gap:12px;padding:10px 0;border-top:1px solid var(--line);font-size:13.5px;line-height:1.45}
    .poz-related li:first-child{border-top:0}
    .poz-related a{color:var(--text);text-decoration:none}
    .poz-related a:hover{text-decoration:underline}
    .poz-related code{font-weight:800;margin-right:6px}
    .poz-related span{white-space:nowrap;color:var(--muted);font-weight:700}
    .poz-more{display:inline-block;margin-top:12px;font-size:13px;font-weight:800;color:var(--text)}
    .poz-variants{margin:14px 0 0;font-size:13px;color:var(--muted);line-height:1.6}
    .poz-variants a{color:var(--text);font-weight:700}
    .poz-hub-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:10px;margin-top:18px}
    .poz-hub-grid a{display:block;padding:14px;border:1px solid var(--line);border-radius:14px;background:var(--surface);color:var(--text);text-decoration:none;font-size:13px;line-height:1.45}
    .poz-hub-grid a:hover{border-color:var(--text)}
    .poz-hub-grid strong{display:block;font-size:15px;margin-bottom:4px}
    .poz-hub-grid em{display:block;font-style:normal;color:var(--muted);margin-top:4px}
    @media(max-width:640px){.poz-related li{flex-direction:column;gap:2px}}
    @media(max-width:900px){.poz-summary{order:-1}}   /* mobilde fiyat ve tutar hesabı önce */
    .poz-note{margin-top:16px;padding:13px 14px;border-left:3px solid var(--yellow);border-radius:0 10px 10px 0;background:var(--surface-2);color:var(--muted);font-size:11px;line-height:1.6}
    .poz-prose{color:var(--text);font-size:14px;line-height:1.85;overflow-wrap:anywhere}
    .poz-related-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:10px;margin-top:14px}
    .poz-related-card{display:block;padding:15px;border:1px solid var(--line);border-radius:14px;background:var(--surface-2);transition:.18s ease}
    .poz-related-card:hover{transform:translateY(-2px);border-color:rgba(255,184,0,.6)}
    .poz-related-code{display:block;font-size:11px;font-weight:950;color:var(--yellow-dark)}
    .poz-related-title{display:block;margin-top:6px;font-size:12px;line-height:1.5;font-weight:750}
    .poz-related-price{display:block;margin-top:8px;color:var(--green);font-size:11px;font-weight:900}
    .poz-cta{margin-top:18px;padding:28px;display:flex;align-items:center;justify-content:space-between;gap:22px;overflow:hidden;position:relative;border-radius:22px;background:var(--black);color:#fff}
    .poz-cta:after{content:"";position:absolute;width:280px;height:280px;right:-120px;top:-140px;border-radius:50%;background:rgba(255,184,0,.18)}
    .poz-cta>div,.poz-cta>a{position:relative;z-index:1}
    .poz-cta h2{margin:0;font-size:25px;line-height:1.15}
    .poz-cta p{margin:7px 0 0;color:#aeb4bd;font-size:13px}
    .poz-cta .poz-btn{border-color:var(--yellow)}
    .faq-item{margin-top:10px;padding:16px 18px;background:var(--surface);border:1px solid var(--line);border-radius:14px}
    .faq-item summary{cursor:pointer;font-size:13px;font-weight:900}
    .faq-item p{margin:10px 0 0;color:var(--muted);font-size:12px;line-height:1.7}
    .related-list{list-style:none;margin:12px 0 0;padding:0;overflow:hidden;border:1px solid var(--line);border-radius:16px;background:var(--surface)}
    .related-list li{padding:14px 16px;border-bottom:1px solid var(--line)}
    .related-list li:last-child{border-bottom:0}
    .related-list a{display:flex;align-items:center;gap:10px;flex-wrap:wrap}

    @media(max-width:900px){.poz-grid{grid-template-columns:1fr}.poz-summary{position:static}}
    @media(max-width:760px){
      .poz-shell{width:min(calc(100% - 24px),var(--max))}
      .poz-main{padding-top:10px}
      .poz-detail-hero{padding:22px 18px;border-radius:18px}
      .poz-title{font-size:clamp(28px,8vw,38px)}
      .poz-grid{gap:12px}
      .poz-card{padding:18px;border-radius:16px}
      .poz-details-row{grid-template-columns:1fr;gap:4px}
      .poz-related-grid{grid-template-columns:1fr}
      .poz-cta{display:block;padding:22px 18px}
      .poz-cta .poz-btn{width:100%;margin-top:16px}
      .search-box{grid-template-columns:1fr}
      .stats{grid-template-columns:1fr}
      .poz-item{align-items:flex-start;flex-direction:column}
      .poz-item>a{align-items:flex-start}
    }
  

    /* POZ PAGINATION */
.poz-pagination{
    display:flex;
    align-items:center;
    justify-content:center;
    flex-wrap:wrap;
    gap:10px;
    margin:28px 0 8px;
}
.poz-page,
.poz-page-info{
    min-height:44px;
    display:inline-flex;
    align-items:center;
    justify-content:center;
    padding:0 15px;
    border:1px solid var(--line);
    border-radius:11px;
    background:var(--surface);
    color:var(--text);
    font-size:12px;
    font-weight:850;
}
a.poz-page:hover{
    border-color:var(--yellow);
    background:rgba(255,184,0,.08);
}
.poz-page-info{
    color:var(--muted);
    background:var(--surface-2);
}
.poz-page.disabled{
    opacity:.45;
    cursor:not-allowed;
}
@media(max-width:480px){
    .poz-pagination{
        display:grid;
        grid-template-columns:1fr 1fr;
    }
    .poz-page-info{
        grid-column:1/-1;
        grid-row:1;
    }
}

  </style>

  @stack('head')
</head>
<body data-tool-slug="poz">
  <x-site.contech-header />

  <main class="poz-main">
    <div class="poz-shell">
      <nav class="breadcrumb" aria-label="Sayfa yolu">
        <a href="/">Ana Sayfa</a><span>›</span>
        @yield('breadcrumb')
      </nav>

      @yield('content')
    </div>
  </main>

  <x-site.contech-footer />
</body>
</html>

