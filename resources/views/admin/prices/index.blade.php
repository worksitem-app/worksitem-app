<!DOCTYPE html>
<html lang="tr">
<head>
    <link rel="icon" href="/favicon.ico" type="image/x-icon">

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kainta İnşaat Admin | Merkezi Fiyat Motoru</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root { --bg:#f3f4f6; --surface:#ffffff; --border:#e5e7eb; --text-main:#111827; --text-muted:#6b7280; --primary:#2563eb; --green:#059669; }
        * { box-sizing:border-box; margin:0; padding:0; }
        body { font-family:'Inter', sans-serif; background:var(--bg); color:var(--text-main); }
        
        .sidebar { position:fixed; left:0; top:0; bottom:0; width:260px; background:#111827; color:#fff; padding:24px; }
        .sidebar-logo { font-size:20px; font-weight:800; color:#fff; text-decoration:none; margin-bottom:40px; display:block; letter-spacing:-0.5px;}
        .sidebar-logo span { color:#3b82f6; }
        .nav-item { display:flex; align-items:center; gap:10px; padding:12px 16px; color:#d1d5db; text-decoration:none; border-radius:8px; margin-bottom:8px; font-weight:500; transition:all 0.2s;}
        .nav-item:hover, .nav-item.active { background:#1f2937; color:#fff; }
        
        .main-content { margin-left:260px; padding:40px; max-width:1200px; }
        .page-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:30px; }
        .page-header h1 { font-size:28px; font-weight:800; letter-spacing:-0.5px; }
        
        .alert { background:#d1fae5; border:1px solid #34d399; color:#065f46; padding:16px; border-radius:8px; margin-bottom:24px; font-weight:500; display:flex; justify-content:space-between; align-items:center;}
        
        .category-block { background:var(--surface); border:1px solid var(--border); border-radius:12px; margin-bottom:30px; overflow:hidden; box-shadow:0 4px 6px -1px rgba(0,0,0,0.05); }
        .category-header { background:#f9fafb; padding:16px 24px; font-size:16px; font-weight:700; color:#374151; border-bottom:1px solid var(--border); text-transform:uppercase; letter-spacing:0.5px;}
        
        table { width:100%; border-collapse:collapse; }
        th { text-align:left; padding:16px 24px; font-size:12px; color:var(--text-muted); font-weight:600; border-bottom:1px solid var(--border); }
        td { padding:16px 24px; border-bottom:1px solid var(--border); font-size:14px; vertical-align:middle;}
        tr:last-child td { border-bottom:none; }
        
        .item-name { font-weight:600; color:var(--text-main); }
        .item-key { font-size:12px; color:var(--text-muted); font-family:monospace; background:#f3f4f6; padding:2px 6px; border-radius:4px; margin-top:4px; display:inline-block;}
        
        .price-form { display:flex; gap:10px; align-items:center; }
        .price-input { width:140px; padding:10px; border:1px solid var(--border); border-radius:6px; font-family:inherit; font-size:14px; font-weight:600; }
        .price-input:focus { outline:none; border-color:var(--primary); box-shadow:0 0 0 3px rgba(37,99,235,0.1); }
        .btn-save { background:var(--primary); color:#fff; border:none; padding:10px 16px; border-radius:6px; font-weight:600; cursor:pointer; transition:all 0.2s; }
        .btn-save:hover { background:#1d4ed8; }
        
        .unit-badge { color:var(--text-muted); font-size:13px; font-weight:500; }
    </style>
</head>
<body>

    <div class="sidebar">
        <a href="/" class="sidebar-logo"><img src="/brand/kainta-logo.jpg" alt="Kainta İnşaat Admin" style="height:40px; margin-bottom:-10px; border-radius:4px;"> YÖNETİM</a>
        <a href="#" class="nav-item">📊 Gösterge Paneli</a>
        <a href="/admin/fiyatlar" class="nav-item active">💰 Fiyat Motoru (DB)</a>
        <a href="#" class="nav-item">👥 Müşteri Talepleri (CRM)</a>
        <a href="/araclar" class="nav-item" target="_blank">🌐 Siteye Dön</a>
    </div>

    <div class="main-content">
        <div class="page-header">
            <div>
                <h1>Merkezi Fiyat Motoru</h1>
                <p style="color:var(--text-muted); margin-top:5px;">Sistemdeki 40+ aracı ve teklifleri besleyen ana rayiç bedeller.</p>
            </div>
            <div>
                <span style="background:#dbeafe; color:#1e3a8a; padding:8px 16px; border-radius:99px; font-size:13px; font-weight:700;">✅ Motor Aktif</span>
            </div>
        </div>

        @if(session('success'))
        <div class="alert" id="alertBox">
            <span>✅ {{ session('success') }}</span>
            <button style="background:none; border:none; cursor:pointer; font-size:16px;" onclick="document.getElementById('alertBox').style.display='none'">✕</button>
        </div>
        @endif

        @foreach($pricesByCategory as $category => $prices)
        <div class="category-block">
            <div class="category-header">📦 {{ str_replace('_', ' ', $category) }} KATEGORİSİ</div>
            <table>
                <thead>
                    <tr>
                        <th width="35%">POZ / MALZEME ADI</th>
                        <th width="20%">BİRİM</th>
                        <th width="45%">GÜNCEL FİYAT (TL) VE GÜNCELLEME</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($prices as $price)
                    <tr>
                        <td>
                            <div class="item-name">{{ $price->name }}</div>
                            <div class="item-key">{{ $price->key }}</div>
                        </td>
                        <td><span class="unit-badge">{{ $price->unit }}</span></td>
                        <td>
                            <form action="{{ route('admin.prices.update', $price->id) }}" method="POST" class="price-form">
                                @csrf
                                <input type="number" step="0.01" name="price" class="price-input" value="{{ $price->price }}">
                                <button type="submit" class="btn-save">Kaydet</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endforeach

    </div>
</body>
</html>
