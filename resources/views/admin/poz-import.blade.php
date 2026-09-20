<!DOCTYPE html>
<html lang="tr">
<head>
    <link rel="icon" href="/favicon.ico" type="image/x-icon">

    <meta charset="UTF-8">
    <title>Resmi Poz Excel/CSV Import | Kainta İnşaat Admin</title>
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
        
        .main-content { margin-left:260px; padding:40px; max-width:1100px; }
        .page-header { margin-bottom:30px; }
        .page-header h1 { font-size:28px; font-weight:800; letter-spacing:-0.5px; }
        
        .card { background:var(--surface); border:1px solid var(--border); border-radius:12px; padding:30px; margin-bottom:30px; box-shadow:0 4px 6px -1px rgba(0,0,0,0.05); }
        .alert { background:#d1fae5; border:1px solid #34d399; color:#065f46; padding:16px; border-radius:8px; margin-bottom:24px; font-weight:600;}
        .alert.error { background:#fee2e2; border-color:#f87171; color:#991b1b; }

        .grid-2 { display:grid; grid-template-columns:1fr 1fr; gap:20px; }
        .form-group { margin-bottom:20px; }
        .form-group label { display:block; font-size:13px; font-weight:700; color:var(--text-muted); margin-bottom:8px; text-transform:uppercase;}
        .form-control { width:100%; padding:12px; border:1px solid var(--border); border-radius:8px; font-family:inherit; font-size:14px; background:#f9fafb; }
        
        .upload-area { border:2px dashed var(--primary); background:#eff6ff; border-radius:12px; padding:40px; text-align:center; cursor:pointer; margin-bottom:20px; transition:all 0.2s;}
        .upload-area:hover { background:#dbeafe; }
        
        .btn-submit { background:var(--green); color:#fff; font-weight:800; font-size:16px; border:none; padding:16px; border-radius:8px; width:100%; cursor:pointer; transition:all 0.2s;}
        .btn-submit:hover { background:#047857; }

        .stat-grid { display:grid; grid-template-columns:repeat(3, 1fr); gap:15px; margin-bottom:30px; }
        .stat-card { background:#fff; border:1px solid var(--border); padding:20px; border-radius:10px; text-align:center;}
        .stat-val { font-size:28px; font-weight:800; color:var(--primary); }
        .stat-lbl { font-size:12px; color:var(--text-muted); font-weight:700; text-transform:uppercase; margin-top:4px;}
    </style>
</head>
<body>

    <div class="sidebar">
        <a href="/" class="sidebar-logo"><img src="/brand/kainta-logo.jpg" alt="Kainta İnşaat Admin" style="height:40px; margin-bottom:-10px; border-radius:4px;"> YÖNETİM</a>
        <a href="/admin/fiyatlar" class="nav-item">💰 Birim Fiyatlar</a>
        <a href="/admin/poz-import" class="nav-item active">📥 Poz Excel Import</a>
        <a href="/admin/talepler" class="nav-item">👥 Müşteri Talepleri</a>
        <a href="/araclar/ihale-modulu" class="nav-item" target="_blank">📑 İhale Modülüne Git</a>
    </div>

    <div class="main-content">
        <div class="page-header">
            <h1>Resmi Kurum Pozları Excel / CSV İçe Aktarma</h1>
            <p style="color:var(--text-muted); margin-top:5px;">ÇŞB, Karayolları, İller Bankası 15.000+ satırlık poz listelerini saniyeler içinde veritabanına yükleyin.</p>
        </div>

        @if(session('success'))
        <div class="alert">✅ {{ session('success') }}</div>
        @endif
        @if(session('error'))
        <div class="alert error">❌ {{ session('error') }}</div>
        @endif

        <div class="stat-grid">
            <div class="stat-card">
                <div class="stat-val">{{ number_format($totalPozCount) }}</div>
                <div class="stat-lbl">Sistemdeki Toplam Poz</div>
            </div>
            @foreach($institutionCounts as $ic)
            <div class="stat-card">
                <div class="stat-val" style="color:#059669;">{{ number_format($ic->total) }}</div>
                <div class="stat-lbl">{{ $ic->institution }} Poz Sayısı</div>
            </div>
            @endforeach
        </div>

        <div class="card">
            <form action="{{ route('admin.poz.import.upload') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="grid-2">
                    <div class="form-group">
                        <label>Kurum / Kitap Adı</label>
                        <select name="institution" class="form-control" required>
                            <option value="ÇŞB">Çevre ve Şehircilik Bakanlığı (ÇŞB)</option>
                            <option value="Karayolları">Karayolları Genel Müdürlüğü</option>
                            <option value="İller Bankası">İller Bankası</option>
                            <option value="DSİ">Devlet Su İşleri (DSİ)</option>
                            <option value="Özel">Kainta İnşaat Özel Pozları</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Yıl</label>
                        <select name="year" class="form-control" required>
                            <option value="2026">2026 Yılı</option>
                            <option value="2025">2025 Yılı</option>
                        </select>
                    </div>
                </div>

                <div class="upload-area" onclick="document.getElementById('excelFile').click()">
                    <div style="font-size:2.5rem; margin-bottom:10px;">📊</div>
                    <div style="font-weight:800; font-size:16px; color:var(--primary);" id="fileName">Excel veya CSV Dosyanızı Sürükleyin / Seçin</div>
                    <div style="font-size:12px; color:var(--text-muted); margin-top:5px;">Sütun Sıralaması: <b>Poz No ; Poz Tanımı ; Birim ; Birim Fiyat</b> şeklinde olmalıdır.</div>
                    <input type="file" id="excelFile" name="file" accept=".csv, .xlsx, .xls, .txt" style="display:none;" onchange="document.getElementById('fileName').innerText = this.files[0].name">
                </div>

                <button type="submit" class="btn-submit">🚀 Pozları Veritabanına Yükle (Import)</button>
            </form>
        </div>
    </div>
</body>
</html>
