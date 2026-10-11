<!DOCTYPE html>
<html lang="tr">
<head>
    <link rel="icon" href="/favicon.ico" type="image/x-icon">

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>İletişim Bilgileri | Kainta İnşaat</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; background: #f5f7fa; color: #18202a; font-family:'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; }
        .container { width: min(600px, calc(100% - 28px)); margin: 60px auto; }
        .card { background: #fff; padding: 40px; border-radius: 18px; box-shadow: 0 7px 22px rgba(15,23,42,.04); border: 1px solid #e5e7eb; }
        h1 { font-size: 28px; margin-top: 0; text-align: center; }
        p { color: #64748b; text-align: center; line-height: 1.5; margin-bottom: 30px; }
        .form-group { margin-bottom: 20px; }
        label { display: block; font-weight: bold; margin-bottom: 8px; color: #334155; }
        input { width: 100%; padding: 14px; border: 1px solid #cbd5e1; border-radius: 10px; font-size: 16px; transition: border-color 0.2s; }
        input:focus { outline: none; border-color: #0f172a; }
        .btn { display: block; width: 100%; padding: 16px; background: #111827; color: #fff; text-align: center; font-size: 16px; font-weight: bold; border: none; border-radius: 12px; cursor: pointer; transition: background 0.2s; }
        .btn:hover { background: #1f2937; }
        .error { color: #ef4444; font-size: 13px; margin-top: 5px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <h1>Sonuçlarınız Hazır! 🎉</h1>
            <p>Maliyet hesabınızı tamamladık. Sonuçları görebilmek ve size özel fizibilite raporunu iletebilmemiz için lütfen bilgilerinizi girin.</p>
            
            <form action="{{ route('projects.lead.save', $project) }}" method="POST">
                @include('components.lead-tracking-fields')
                @csrf
                <div class="form-group">
                    <label>Adınız Soyadınız *</label>
                    <input type="text" name="name" required placeholder="Örn: Ahmet Yılmaz" value="{{ old('name') }}">
                    @error('name') <div class="error">{{ $message }}</div> @enderror
                </div>
                
                <div class="form-group">
                    <label>Telefon Numaranız *</label>
                    <input type="tel" name="phone" required placeholder="Örn: 0532 123 45 67" value="{{ old('phone') }}">
                    @error('phone') <div class="error">{{ $message }}</div> @enderror
                </div>
                
                <div class="form-group">
                    <label>E-posta Adresiniz (İsteğe Bağlı)</label>
                    <input type="email" name="email" placeholder="Örn: ahmet@sirket.com" value="{{ old('email') }}">
                </div>
                
                                @include('components.lead-consent-fields')

<button type="submit" class="btn">Sonuçları Göster →</button>
            </form>
        </div>
    </div>
</body>
</html>
