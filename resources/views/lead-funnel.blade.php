<!DOCTYPE html>
<html lang="tr">
<head>
    <link rel="icon" href="/favicon.ico" type="image/x-icon">

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kainta İnşaat | Profesyonel Keşif ve Net Teklif Talebi</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root { --bg:#f3f4f6; --surface:#ffffff; --border:#e5e7eb; --text-main:#111827; --text-muted:#6b7280; --primary:#2563eb; --primary-hover:#1d4ed8; --green:#059669; }
        * { box-sizing:border-box; margin:0; padding:0; }
        body { font-family:'Inter', sans-serif; background:var(--bg); color:var(--text-main); line-height:1.5; }
        .navbar { padding:20px 5%; background:var(--surface); border-bottom:1px solid var(--border); text-align:center; }
        .logo { font-weight:900; font-size:24px; color:#1e3a8a; text-decoration:none; letter-spacing:-1px;}
        
        .container { max-width:600px; margin:50px auto; padding:0 20px; }
        .card { background:var(--surface); border:1px solid var(--border); border-radius:16px; padding:40px; box-shadow:0 10px 25px -5px rgba(0,0,0,0.05); }
        
        h1 { font-size:28px; font-weight:800; margin-bottom:10px; letter-spacing:-0.5px; text-align:center; }
        p.subtitle { color:var(--text-muted); text-align:center; margin-bottom:30px; font-size:15px; }

        .ai-banner { background:#eff6ff; border:1px solid #bfdbfe; border-radius:12px; padding:16px; margin-bottom:30px; display:flex; gap:15px; align-items:flex-start; }
        .ai-banner span { font-size:24px; }
        .ai-banner div { font-size:14px; color:#1e3a8a; font-weight:500; }
        .ai-banner strong { font-weight:800; }

        .form-group { margin-bottom:20px; }
        .form-group label { display:block; font-size:13px; font-weight:700; color:var(--text-muted); margin-bottom:8px; text-transform:uppercase; letter-spacing:0.5px;}
        .form-control { width:100%; padding:14px; border:1px solid var(--border); border-radius:8px; font-family:inherit; font-size:15px; background:var(--bg); transition:all 0.2s; }
        .form-control:focus { outline:none; border-color:var(--primary); box-shadow:0 0 0 3px rgba(37,99,235,0.1); background:#fff; }
        .grid-2 { display:grid; grid-template-columns:1fr 1fr; gap:15px; }

        .btn-submit { width:100%; padding:16px; background:var(--green); color:#fff; font-weight:800; font-size:16px; border:none; border-radius:8px; cursor:pointer; transition:all 0.2s; margin-top:10px; }
        .btn-submit:hover { background:#047857; transform:translateY(-2px); box-shadow:0 10px 15px -3px rgba(5,150,105,0.3); }
        
        .trust-badges { display:flex; justify-content:center; gap:20px; margin-top:30px; border-top:1px solid var(--border); padding-top:20px; color:var(--text-muted); font-size:12px; font-weight:600;}
        .trust-badges span { display:flex; align-items:center; gap:5px; }
    </style>
</head>
<body>
    <nav class="navbar"><a href="/" class="logo"><img src="/brand/kainta-logo.jpg" alt="Kainta İnşaat" style="height:45px; border-radius:4px;"></a></nav>

    <div class="container">
        <div class="card">
            <h1>Profesyonel Keşif & Net Teklif Talebi</h1>
            <p class="subtitle">Projeniz yerinde incelenir, metraj ve teknik kapsam netleştirilir. Profesyonel saha keşfi ücretlidir; keşif bedeli randevu onayından önce bildirilir.</p>
            
            @if($service || $area)
            <div class="ai-banner">
                <span>🤖</span>
                <div>
                    <strong>Yapay Zeka Asistanı:</strong> Hesaplanmış bir araçtan geldiğinizi tespit ettim. Verilerinizi teklif dosyasına otomatik ekliyorum. Yeniden girmenize gerek yok!
                </div>
            </div>
            @endif

            <form action="{{ route('leads.store') }}" method="POST">
                        @include('components.lead-tracking-fields')
                @csrf
                <input type="hidden" name="source" value="worksitem_lead_funnel">
                <!-- Hesaplayıcılardan gelen gizli veriler (Pre-filled) -->
                <input type="hidden" name="calculated_service" value="{{ $service }}">
                <input type="hidden" name="calculated_area" value="{{ $area }}">

                <div class="grid-2">
                    <div class="form-group">
                        <label>Adınız Soyadınız</label>
                        <input type="text" name="name" class="form-control" required placeholder="Örn: Ahmet Yılmaz">
                    </div>
                    <div class="form-group">
                        <label>Telefon Numaranız</label>
                        <input type="tel" name="phone" class="form-control" required placeholder="05XX XXX XX XX">
                    </div>
                </div>

                <div class="form-group">
                    <label>Proje İli ve İlçesi</label>
                    <input type="text" name="location" class="form-control" required placeholder="Örn: Kadıköy, İstanbul">
                </div>

                <div class="form-group">
                    <label>Projeniz Hakkında Kısa Bir Not</label>
                    <textarea name="notes" class="form-control" rows="3" placeholder="Örn: {{ $service ? strtoupper($service) . ' işi için ' . $area . ' m2 alanım var. ' : '' }}Mutfak dolapları ve zeminler değişecek...">{{ $service ? strtoupper($service) . ' işi için ' . $area . ' m2 alan hesaplaması yaptım. Profesyonel keşif ve net teklif talep ediyorum.' : '' }}</textarea>
                </div>

                                @include('components.lead-consent-fields')

<button type="submit" class="btn-submit">Profesyonel Keşif Talebimi Gönder →</button>
            </form>

            <div class="trust-badges">
                <span>✅ Ücretli Profesyonel Keşif</span>
                <span>✅ Uzman Mühendisler</span>
                <span>✅ 2026 ÇŞB Rayiçleri</span>
            </div>
        </div>
    </div>
</body>
</html>
