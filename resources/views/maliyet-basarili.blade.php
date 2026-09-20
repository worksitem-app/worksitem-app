<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Projeniz Değerlendirmede | WORKSİTEM & Kainta İnşaat</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800;900&display=swap" rel="stylesheet">
    <style>
        body { 
            font-family: 'Inter', sans-serif; 
            text-align: center; 
            background: #050505; 
            color: #fff; 
            min-height: 100vh; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            margin: 0;
        }
        .container { 
            background: #111; 
            padding: 50px 40px; 
            border-radius: 24px; 
            max-width: 580px; 
            width: 90%; 
            border: 1px solid rgba(255,255,255,0.08); 
            box-shadow: 0 20px 80px rgba(0,0,0,0.5); 
        }
        h1 { 
            font-size: 32px; 
            font-weight: 900; 
            letter-spacing: -1px; 
            margin-bottom: 15px; 
        }
        .icon { 
            font-size: 54px; 
            margin-bottom: 20px; 
        }
        p { 
            color: #a1a1aa; 
            font-size: 15px; 
            line-height: 1.7; 
            margin-bottom: 30px; 
        }
        .btn { 
            display: inline-block; 
            background: #ffb800; 
            color: #000; 
            padding: 16px 32px; 
            border-radius: 12px; 
            font-weight: 800; 
            text-decoration: none; 
            transition: 0.2s; 
        }
        .btn:hover { 
            background: #fff; 
            transform: translateY(-2px); 
        }
        .brand { 
            margin-top: 40px; 
            font-size: 12px; 
            color: #666; 
            font-weight: 600; 
        }
    </style>
</head>
<body>
    <div class="container">
        
        @if(isset($status) && $status === 'research')
            <!-- Sadece araştıran / Bütçe onayı vermeyenler için -->
            <div class="icon">🔎</div>
            <h1>Piyasa araştırmanız tamamlandı.</h1>
            <p>
                Projeniz için oluşturduğumuz yaklaşık maliyet tahmini sistemimize kaydedilmedi. 
                Siz ne zaman hazır hissederseniz ve bütçenizi netleştirirseniz, Kainta İnşaat olarak 
                size profesyonel destek sunmaktan memnuniyet duyarız.
            </p>
        @else
            <!-- Nitelikli (Qualified) Lead için Premium mesaj -->
            <div class="icon">✓</div>
            <h1>Projeniz değerlendirmeye alındı.</h1>
            <p>
                Girdiğiniz proje detayları ve yaklaşık bütçe onayı Kainta İnşaat teknik ekibine iletildi. 
                Projeniz operasyonel kriterlerimize uygun bulunduğu takdirde, detaylı keşif süreci 
                ve resmi teklif planlaması için yöneticilerimiz sizinle en kısa sürede iletişime geçecektir.
            </p>
        @endif

        <a href="/" class="btn">← Ana Sayfaya Dön</a>

        <div class="brand">WORKSİTEM by Kainta İnşaat</div>
    </div>
</body>
</html>
