{{-- Hata sayfaları ortak kabuğu. Bilinçli olarak bağımsız: 500 anında veritabanı/oturum/Vite'a dayanmaz. --}}
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, follow">
    <title>@yield('title') | Worksitem</title>
    <link rel="icon" href="/brand/favicon.ico">
    <style>
        :root { --y:#FFB800; --ink:#101318; --muted:#5B6472; --page:#F5F6F8; --line:#E5E7EB; --amber:#8A6200; }
        * { box-sizing: border-box; }
        body { margin:0; min-height:100vh; display:flex; flex-direction:column; background:var(--page); color:var(--ink);
               font-family:'Inter',-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif; -webkit-font-smoothing:antialiased; }
        header { padding:16px; border-bottom:1px solid var(--line); background:#fff; }
        header a { display:inline-flex; align-items:center; gap:8px; text-decoration:none; color:var(--ink); font-weight:800; letter-spacing:.02em; }
        header img { height:28px; width:auto; }
        main { flex:1; display:flex; align-items:center; justify-content:center; padding:32px 16px; }
        .box { width:100%; max-width:560px; background:#fff; border:1px solid var(--line); border-radius:16px; padding:32px 24px; }
        .code { display:inline-block; font-size:13px; font-weight:700; color:var(--amber); background:#FFF8E1; border-radius:999px; padding:4px 12px; }
        h1 { font-size:26px; line-height:1.25; margin:14px 0 8px; }
        p { color:var(--muted); line-height:1.6; margin:0 0 20px; }
        .actions { display:flex; flex-wrap:wrap; gap:10px; }
        .btn { display:inline-flex; align-items:center; justify-content:center; min-height:44px; padding:0 18px; border-radius:10px;
               font-weight:700; text-decoration:none; border:1px solid var(--line); color:var(--ink); background:#fff; cursor:pointer; font-size:15px; }
        .btn.primary { background:var(--y); border-color:var(--y); }
        .btn:focus-visible { outline:3px solid var(--ink); outline-offset:2px; }
        ul { margin:24px 0 0; padding:0; list-style:none; border-top:1px solid var(--line); }
        li a { display:block; padding:12px 0; border-bottom:1px solid var(--line); color:var(--ink); text-decoration:none; }
        li a:hover { color:var(--amber); }
        footer { padding:16px; text-align:center; font-size:13px; color:var(--muted); }
        footer a { color:var(--muted); }
    </style>
</head>
<body>
    <header><a href="/"><img src="/brand/worksitem-logo.png" alt="WORKSİTEM" width="112" height="28"></a></header>
    <main>
        <div class="box">
            <span class="code">@yield('code')</span>
            <h1>@yield('heading')</h1>
            <p>@yield('message')</p>
            <div class="actions">@yield('actions')</div>
            @hasSection('links')
                <ul>@yield('links')</ul>
            @endif
        </div>
    </main>
    <footer>Sorun sürerse <a href="mailto:info@worksitem.com">info@worksitem.com</a> adresine yazın.</footer>
</body>
</html>
