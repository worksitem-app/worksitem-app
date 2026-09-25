<!DOCTYPE html>
<html lang="tr" data-theme="light">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
  <title>@yield('title', 'Worksitem')</title>
  <meta name="description" content="@yield('description', 'Worksitem - İnşaat ve tadilat maliyeti platformu')">
  <link rel="canonical" href="@yield('canonical', url('/'))">
  <link rel="icon" href="/favicon.ico">

  <style>
    :root{
      --bg-color:#f6f7f9;
      --surface:#fff;
      --s2:#eef1f4;
      --s3:#e2e8f0;
      --text:#0f172a;
      --muted:#64748b;
      --line:rgba(15,23,42,0.08);
      --yellow:#ffb800;
      --yellow2:#d97706;
      --yellow-light:rgba(255,184,0,0.1);
      --green:#10b981;
      --black:#020617;
      --shadow-sm:0 4px 6px -1px rgba(0,0,0,.05), 0 2px 4px -1px rgba(0,0,0,.03);
      --shadow:0 20px 40px -10px rgba(0,0,0,.08);
      --max:1180px;
      --radius-md:12px;
      --radius-lg:24px;
    }
    html[data-theme=dark]{
      --bg-color:#020617;
      --surface:#0f172a;
      --s2:#1e293b;
      --s3:#334155;
      --text:#f8fafc;
      --muted:#94a3b8;
      --line:rgba(255,255,255,.1);
      --yellow-light:rgba(255,184,0,0.05);
    }
    *{box-sizing:border-box;margin:0;padding:0}
    html{scroll-behavior:smooth}
    body{font-family:Inter,ui-sans-serif,-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Arial,sans-serif;background:var(--bg-color);color:var(--text);line-height:1.6;-webkit-font-smoothing:antialiased;overflow-x:hidden}
    a{color:inherit;text-decoration:none}
    button{font:inherit}
    img{max-width:100%;display:block}
    .container{width:min(calc(100% - 32px),var(--max));margin:auto}

    /* NAVBAR */
    .navbar{position:sticky;top:0;z-index:1000;background:rgba(246,247,249,.92);backdrop-filter:blur(16px);border-bottom:1px solid var(--line)}
    html[data-theme=dark] .navbar{background:rgba(2,6,23,.9)}
    .nav-inner{min-height:72px;display:flex;align-items:center;justify-content:space-between;gap:18px}
    .nav-brand{display:flex;align-items:center;gap:10px;font-weight:900;font-size:20px;letter-spacing:-1px}
    .nav-brand span{color:var(--yellow)}
    .nav-links{display:flex;gap:4px;align-items:center}
    .nav-link{padding:10px 14px;border-radius:10px;color:var(--muted);font-size:14px;font-weight:600;transition:all .2s}
    .nav-link:hover{background:var(--s2);color:var(--text)}
    .nav-cta{display:inline-flex;align-items:center;gap:6px;padding:10px 20px;background:var(--yellow);color:#000;border-radius:10px;font-weight:800;font-size:14px}
    .nav-cta:hover{transform:translateY(-1px);box-shadow:0 8px 20px -6px rgba(255,184,0,0.5)}

    /* BREADCRUMB */
    .breadcrumb{font-size:13px;color:var(--muted);margin:24px 0 16px}
    .breadcrumb a:hover{color:var(--yellow2)}
    .breadcrumb>span{margin:0 8px;opacity:.5}

    /* HERO */
    .hero{position:relative;padding:20px 0 40px}
    .hero:before{content:"";position:absolute;width:600px;height:600px;right:-200px;top:-200px;border-radius:50%;background:radial-gradient(circle,rgba(255,184,0,0.15) 0%,rgba(255,184,0,0) 70%);pointer-events:none}

    /* TYPOGRAPHY */
    h1{font-size:clamp(32px,5vw,56px);line-height:1.05;letter-spacing:-2px;font-weight:900;margin-bottom:16px}
    h1 span{color:var(--yellow)}
    h2{font-size:clamp(20px,3vw,32px);font-weight:800;letter-spacing:-1px;margin:40px 0 20px}
    h3{font-size:18px;font-weight:700;margin:24px 0 12px}

    /* CARDS */
    .card{background:var(--surface);border:1px solid var(--line);border-radius:var(--radius-lg);padding:32px;box-shadow:var(--shadow);margin:24px 0}

    /* DIRECT ANSWER */
    .direct-answer{background:linear-gradient(135deg,#fef3c7 0%,#fde68a 100%);border-left:4px solid var(--yellow);padding:24px 28px;border-radius:var(--radius-md);margin:24px 0}
    html[data-theme=dark] .direct-answer{background:linear-gradient(135deg,rgba(255,184,0,0.15) 0%,rgba(255,184,0,0.05) 100%)}
    .direct-answer p:first-child{font-size:18px;line-height:1.6;margin-bottom:8px}
    .direct-answer strong{font-size:20px;font-weight:900;color:#78350f}
    html[data-theme=dark] .direct-answer strong{color:var(--yellow)}

    /* PRICE BADGE */
    .price-badge{display:inline-block;background:#dcfce7;color:#166534;padding:8px 16px;border-radius:8px;font-weight:900;font-size:20px;letter-spacing:-0.5px}
    html[data-theme=dark] .price-badge{background:rgba(16,185,129,0.2);color:#10b981}

    /* TABLE */
    .data-table{width:100%;border-collapse:collapse}
    .data-table th,.data-table td{padding:16px 0;text-align:left;border-bottom:1px solid var(--s2);font-size:15px}
    .data-table th{font-weight:600;color:var(--muted);width:40%;font-size:14px}
    .data-table tr:hover{background:var(--s2)}
    .data-table tr:last-child th,.data-table tr:last-child td{border-bottom:none}

    /* CTA */
    .cta-box{background:linear-gradient(135deg,var(--black) 0%,#1e293b 100%);color:#fff;padding:48px 32px;border-radius:var(--radius-lg);text-align:center;margin:40px 0;position:relative;overflow:hidden}
    .cta-box:before{content:"";position:absolute;top:-100px;right:-100px;width:300px;height:300px;border-radius:50%;background:radial-gradient(circle,rgba(255,184,0,0.2) 0%,rgba(255,184,0,0) 70%)}
    .cta-box h2{color:#fff;margin:0 0 12px}
    .cta-box p{color:#cbd5e1;margin-bottom:24px}

    /* BUTTONS */
    .btn{min-height:56px;padding:0 32px;border-radius:var(--radius-md);display:inline-flex;align-items:center;justify-content:center;border:1px solid transparent;font-size:16px;font-weight:800;transition:all .2s ease;cursor:pointer}
    .btn-primary{background:linear-gradient(135deg,var(--yellow) 0%,#f59e0b 100%);color:#000;box-shadow:0 8px 20px -6px rgba(255,184,0,0.5)}
    .btn-primary:hover{transform:translateY(-2px);box-shadow:0 12px 25px -8px rgba(255,184,0,0.6)}

    /* TRUST BAR */
    .trust-bar{display:flex;flex-wrap:wrap;gap:20px;margin:40px 0;padding:24px 0;border-top:1px solid var(--line);border-bottom:1px solid var(--line)}
    .trust-item{display:flex;align-items:center;gap:10px;font-size:14px;font-weight:600;color:var(--muted)}
    .trust-icon{display:flex;align-items:center;justify-content:center;width:28px;height:28px;background:var(--green);color:#fff;border-radius:50%;font-size:12px;flex-shrink:0}

    /* SEARCH */
    .search-box{background:var(--surface);border:1px solid var(--line);border-radius:var(--radius-lg);padding:24px;margin:24px 0;display:flex;gap:12px;flex-wrap:wrap;box-shadow:var(--shadow-sm)}
    .search-box input, .search-box select{padding:14px 20px;border:1px solid var(--s3);border-radius:var(--radius-md);font-size:15px;flex:1;min-width:200px;background:var(--surface);color:var(--text);font-family:inherit}
    .search-box input:focus, .search-box select:focus{outline:none;border-color:var(--yellow);box-shadow:0 0 0 4px var(--yellow-light)}
    .search-box button{padding:14px 32px;background:var(--yellow);color:#000;border:none;border-radius:var(--radius-md);font-weight:800;font-size:15px;cursor:pointer;transition:all .2s}
    .search-box button:hover{transform:translateY(-1px);box-shadow:0 8px 20px -6px rgba(255,184,0,0.5)}

    /* FOOTER */
    .footer{border-top:1px solid var(--line);padding:40px 0;margin-top:60px;color:var(--muted);font-size:14px}
    .footer-inner{display:flex;justify-content:space-between;gap:20px;flex-wrap:wrap;align-items:center}

    /* POZ LIST */
    .poz-list{background:var(--surface);border:1px solid var(--line);border-radius:var(--radius-lg);overflow:hidden;box-shadow:var(--shadow-sm)}
    .poz-item{padding:20px 24px;border-bottom:1px solid var(--s2);display:flex;justify-content:space-between;align-items:center;gap:16px;transition:all .2s}
    .poz-item:last-child{border-bottom:none}
    .poz-item:hover{background:var(--s2)}
    .poz-item a{color:var(--text);font-weight:500;flex:1;display:flex;align-items:center;gap:12px}
    .poz-item:hover a{color:var(--yellow2)}
    .poz-code{font-family:monospace;background:var(--s2);padding:4px 10px;border-radius:6px;font-size:13px;font-weight:700}
    .poz-price{font-weight:800;color:var(--green);white-space:nowrap;font-size:15px}

    /* RELATED */
    .related-list{list-style:none;background:var(--surface);border:1px solid var(--line);border-radius:var(--radius-lg);padding:8px 24px}
    .related-list li{padding:16px 0;border-bottom:1px solid var(--s2)}
    .related-list li:last-child{border-bottom:none}
    .related-list a{color:var(--text);font-weight:500;display:flex;align-items:center;gap:12px;flex-wrap:wrap}
    .related-list a:hover{color:var(--yellow2)}

    /* FAQ */
    .faq-item{background:var(--surface);border:1px solid var(--line);border-radius:var(--radius-md);padding:20px 24px;margin-bottom:12px;transition:all .2s}
    .faq-item:hover{border-color:var(--yellow)}
    .faq-item summary{font-weight:700;cursor:pointer;color:var(--text);font-size:16px}
    .faq-item[open] summary{color:var(--yellow2)}
    .faq-item p{margin-top:12px;color:var(--muted);line-height:1.7}

    /* STATS */
    .stats{display:grid;grid-template-columns:repeat(auto-fit,minmax(150px,1fr));gap:20px;margin:24px 0}
    .stat{background:var(--surface);border:1px solid var(--line);border-radius:var(--radius-md);padding:24px;text-align:center}
    .stat-value{font-size:32px;font-weight:900;color:var(--yellow2);letter-spacing:-1px}
    .stat-label{font-size:13px;color:var(--muted);text-transform:uppercase;font-weight:600;margin-top:4px}

    /* MOBILE */
    @media(max-width:768px){
      .nav-links{display:none}
      .hero{padding:16px 0 24px}
      .card{padding:20px}
      .cta-box{padding:32px 20px}
      .direct-answer{padding:20px}
      .footer-inner{flex-direction:column;text-align:center}
    }
  </style>
</head>
<body>
  <!-- NAVBAR -->
  <nav class="navbar">
    <div class="container nav-inner">
      <a href="/" class="nav-brand">
        WORKSİTEM<span>.</span>
      </a>
      <div class="nav-links">
        <a href="/pozlar" class="nav-link">Pozlar</a>
        <a href="/araclar" class="nav-link">Araçlar</a>
        <a href="/maliyet-hesapla" class="nav-link">Maliyet Hesapla</a>
      </div>
      <a href="/maliyet-hesapla" class="nav-cta">Ücretsiz Hesapla</a>
    </div>
  </nav>

  <main class="container">
    <div class="breadcrumb">
      <a href="/">Ana Sayfa</a><span>›</span>
      @yield('breadcrumb')
    </div>

    @yield('content')
  </main>

  <!-- FOOTER -->
  <footer class="footer">
    <div class="container footer-inner">
      <div>Worksitem — İnşaat ve tadilat maliyeti platformu</div>
      <div>Kainta İnşaat'ın saha deneyimi ve güncel fiyat verileriyle desteklenmektedir.</div>
    </div>
  </footer>
</body>
</html>