<!DOCTYPE html>
<html lang="tr">
<head>
    <link rel="icon" href="/favicon.ico" type="image/x-icon">

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Kainta İnşaat AI | İnşaat Maliyet Hesaplama')</title>
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            background: #f8fafc;
            color: #0f172a;
            font-family: system-ui, -apple-system, sans-serif;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        /* HEADER TASARIMI */
        .navbar {
            background: #ffffff;
            border-bottom: 1px solid #e2e8f0;
            padding: 16px 5%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }
        .navbar-brand {
            font-size: 22px;
            font-weight: 800;
            color: #0f172a;
            text-decoration: none;
            letter-spacing: -0.5px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .navbar-brand span {
            background: #2563eb;
            color: white;
            padding: 4px 8px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: bold;
            letter-spacing: 1px;
        }
        .navbar-menu a {
            color: #64748b;
            text-decoration: none;
            font-size: 15px;
            font-weight: 500;
            margin-left: 24px;
            transition: color 0.2s;
        }
        .navbar-menu a:hover { color: #2563eb; }
        
        main { flex: 1; }

        /* FOOTER TASARIMI */
        .footer {
            background: #1e293b;
            color: #94a3b8;
            padding: 30px 20px;
            text-align: center;
            font-size: 14px;
            margin-top: auto;
        }
        .footer a { color: #cbd5e1; text-decoration: none; }
        .footer a:hover { color: #fff; }
        
        @yield('styles')
    </style>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body data-tool-slug="{{ str_replace("araclar/", "", request()->path()) }}">

    <nav class="navbar">
        <a href="{{ url('/maliyet-hesapla') }}" class="navbar-brand">
            Kainta İnşaat <span>AI</span>
        </a>
        <div class="navbar-menu">
            <a href="{{ url('/maliyet-hesapla') }}">Yeni Hesaplama</a>
            <a href="https://worksitem.com">Kurumsal Site</a>
        </div>
    </nav>

    <main>
        @yield('content')
    </main>

    <footer class="footer">
        <div>&copy; {{ date('Y') }} Kainta İnşaat Mimarlık & Mühendislik. Tüm hakları saklıdır.</div>
        <div style="font-size: 12px; margin-top: 8px; opacity: 0.7;">
            Yapay zeka destekli ön maliyet hesaplama motoru.
        </div>
    </footer>

    @yield('scripts')
<x-site.kainta-linker />
    <script src="/js/tool-events.js" defer></script>
</body>
</html>
