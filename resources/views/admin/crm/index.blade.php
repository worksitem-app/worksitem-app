<!DOCTYPE html>
<html lang="tr">
<head>
    <link rel="icon" href="/favicon.ico" type="image/x-icon">

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Müşteri Talepleri (CRM) | Kainta İnşaat Admin</title>
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
        .page-header { margin-bottom:30px; }
        .page-header h1 { font-size:28px; font-weight:800; letter-spacing:-0.5px; }
        
        .card { background:var(--surface); border:1px solid var(--border); border-radius:12px; overflow:hidden; box-shadow:0 4px 6px -1px rgba(0,0,0,0.05); }
        table { width:100%; border-collapse:collapse; }
        th { text-align:left; padding:16px 24px; font-size:12px; color:var(--text-muted); font-weight:600; border-bottom:1px solid var(--border); text-transform:uppercase;}
        td { padding:16px 24px; border-bottom:1px solid var(--border); font-size:14px; vertical-align:middle;}
        tr:hover { background:#f9fafb; }
        
        .status-badge { padding:4px 8px; border-radius:6px; font-size:12px; font-weight:700; text-transform:uppercase; }
        .status-yeni { background:#dbeafe; color:#1e3a8a; }
        .status-teklif_iletildi { background:#d1fae5; color:#065f46; }
        
        .btn-ai { background:linear-gradient(45deg, #a855f7, #ec4899); color:#fff; border:none; padding:8px 16px; border-radius:6px; font-weight:700; font-size:13px; text-decoration:none; display:inline-flex; align-items:center; gap:6px; transition:all 0.2s;}
        .btn-ai:hover { opacity:0.9; transform:translateY(-1px); box-shadow:0 4px 10px rgba(168, 85, 247, 0.4);}
    </style>
</head>
<body>

    <div class="sidebar">
        <a href="/" class="sidebar-logo"><img src="/brand/kainta-logo.jpg" alt="Kainta İnşaat Admin" style="height:40px; margin-bottom:-10px; border-radius:4px;"> YÖNETİM</a>
        <a href="#" class="nav-item">📊 Gösterge Paneli</a>
        <a href="/admin/fiyatlar" class="nav-item">💰 Fiyat Motoru (DB)</a>
        <a href="/admin/talepler" class="nav-item active">👥 Müşteri Talepleri (CRM)</a>
        <a href="/araclar" class="nav-item" target="_blank">🌐 Siteye Dön</a>
    </div>

    <div class="main-content">
        <div class="page-header">
            <h1>Müşteri Talepleri (CRM)</h1>
            <p style="color:var(--text-muted); margin-top:5px;">Web sitesinden gelen formlar ve yapay zeka entegreli teklif yönetimi.</p>
        </div>

        <div class="card">
            <table>
                <thead>
                    <tr>
                        <th>Tarih</th>
                        <th>Müşteri / İletişim</th>
                        <th>Proje Verileri</th>
                        <th>Durum</th>
                        <th>İşlem (Yapay Zeka)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($leads as $lead)
                    <tr>
                        <td>{{ $lead->created_at->format('d.m.Y H:i') }}</td>
                        <td>
                            <strong>{{ $lead->name }}</strong><br>
                            <span style="color:var(--text-muted); font-size:12px;">{{ $lead->phone }} | {{ $lead->location }}</span>
                        </td>
                        <td>
                            <div style="font-size:12px; font-weight:600;">
                                Hizmet: <span style="color:var(--primary);">{{ $lead->calculated_service ?: 'Belirtilmedi' }}</span><br>
                                Alan: <span style="color:var(--primary);">{{ $lead->calculated_area ?: 'Belirtilmedi' }} m²</span>
                            </div>
                            <div style="font-size:11px; color:var(--text-muted); margin-top:4px; max-width:250px;">{{ Str::limit($lead->notes, 60) }}</div>
                        </td>
                        <td>
                            <span class="status-badge status-{{ $lead->status }}">
                                {{ $lead->status == 'yeni' ? 'YENİ TALEP' : 'TEKLİF ÜRETİLDİ' }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('admin.leads.proposal', $lead->id) }}" class="btn-ai" target="_blank">
                                Teklif üret (PDF)
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
