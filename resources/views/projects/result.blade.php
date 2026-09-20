<!DOCTYPE html>
<html lang="tr">
<head>
    <link rel="icon" href="/favicon.ico" type="image/x-icon">

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ön Maliyet Sonucu | Kainta İnşaat</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; background: #f5f7fa; color: #18202a; font-family: Arial, sans-serif; }
        .container { width: min(900px, calc(100% - 28px)); margin: 0 auto; padding: 50px 0 70px; }
        .header { text-align: center; margin-bottom: 32px; }
        h1 { margin: 10px 0; font-size: clamp(28px, 5vw, 42px); }
        .subtitle { color: #64748b; line-height: 1.6; }
        .card { background: #fff; border: 1px solid #e5e7eb; border-radius: 18px; padding: 30px; box-shadow: 0 7px 22px rgba(15,23,42,.04); }
        .total-box { background: #111827; color: #fff; border-radius: 12px; padding: 40px 20px; text-align: center; margin-bottom: 30px; }
        .total-title { font-size: 16px; color: #94a3b8; margin-bottom: 15px; text-transform: uppercase; letter-spacing: 1px; }
        .total-amount { font-size: clamp(36px, 6vw, 54px); font-weight: bold; margin: 0; }
        .m2-cost { color: #cbd5e1; margin-top: 15px; font-size: 15px; }
        .table-title { font-size: 20px; font-weight: bold; margin-bottom: 20px; border-bottom: 2px solid #eef1f5; padding-bottom: 10px; }
        .row { display: flex; justify-content: space-between; padding: 16px 0; border-bottom: 1px solid #eef1f5; }
        .row:last-child { border-bottom: none; }
        .item-name { font-weight: bold; color: #334155; }
        .item-value { font-weight: bold; color: #0f172a; }
        .disclaimer { margin-top: 30px; padding: 20px; background: #e0f2fe; color: #0369a1; border-radius: 12px; font-size: 14px; line-height: 1.6; }
        .btn-home { display: block; width: 100%; text-align: center; background: #fff; color: #0f172a; border: 1px solid #cbd5e1; padding: 16px; border-radius: 12px; font-size: 16px; font-weight: bold; text-decoration: none; margin-top: 20px; cursor: pointer; transition: background 0.2s; }
        .btn-home:hover { background: #f8fafc; }
    </style>
</head>
<body>
    <nav class="navbar" style="background:#fff; border-bottom:1px solid #e2e8f0; padding:16px 5%; display:flex; justify-content:space-between; align-items:center;">
        <a href="https://kainta.com" target="_blank" rel="noopener" style="font-size:22px; font-weight:800; color:#0f172a; text-decoration:none;">KAINTA <span style="background:#2563eb; color:white; padding:4px 8px; border-radius:6px; font-size:12px;">AI</span></a>
        <div><a href="https://worksitem.com" style="color:#64748b; text-decoration:none; font-weight:500;">Ana Site</a></div>
    </nav>


    <div class="container">
        <div class="header">
            <h1>Ön Maliyet Hesabı</h1>
            <div class="subtitle">Girdiğiniz verilere göre hesaplanan tahmini projelendirme ve inşaat maliyetidir.</div>
        </div>

        <div class="card">
            <div class="total-box">
                <div class="total-title">Tahmini Toplam Maliyet</div>
                <div class="total-amount">
                    {{ number_format($costData['total'], 0, ',', '.') }} ₺
                </div>
                <div class="m2-cost">
                    Yaklaşık m² birim maliyeti: {{ number_format($costData['m2_cost'], 0, ',', '.') }} ₺
                </div>
            </div>

            <div class="table-title">Maliyet Kalemleri Özeti</div>
            
            @foreach($costData['items'] as $name => $amount)
            <div class="row">
                <div class="item-name">{{ $name }}</div>
                <div class="item-value">{{ number_format($amount, 0, ',', '.') }} ₺</div>
            </div>
            @endforeach

            <div class="disclaimer">
                <strong>Önemli Not:</strong> Bu sonuçlar ön fizibilite ve fikir verme amaçlıdır. Gerçek piyasa koşulları, arsa eğimi, zemin durumu, seçilecek malzemenin tam markası ve bölgeye göre fiyatlar değişiklik gösterebilir. Kesin bir teklif için detaylı mimari proje ve zemin etüdü gereklidir.
            </div>

            <a href="{{ route('maliyet_hesapla') }}" class="btn-home">Yeni Proje Hesapla</a>
        </div>
    </div>

    <footer style="background:#1e293b; color:#94a3b8; padding:30px 20px; text-align:center; font-size:14px; margin-top:50px;">
        </body>copy; 2026 Kainta İnşaat Mimarlık. Tüm hakları saklıdır.<br><span style="font-size:11px; opacity:0.6;">Yapay zeka destekli motor</span>
    </footer>
</body>
</html>
