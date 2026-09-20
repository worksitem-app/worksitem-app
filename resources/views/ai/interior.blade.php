<!DOCTYPE html>
<html lang="tr">
<head>
    <link rel="icon" href="/favicon.ico" type="image/x-icon">

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI İç Mekan Tasarımı | WORKSİTEM</title>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root { --bg: #0b0f17; --card-bg: #151c28; --border: #21262d; --yellow: #ffb700; --text: #f0f6fc; --muted: #8b949e; --cyan: #38bdf8; --green:#10b981; }
        body { font-family: 'Inter', sans-serif; background: var(--bg); color: var(--text); margin: 0; padding: 0; }
        .container { max-width: 1000px; margin: 40px auto; padding: 0 4%; }
        h1 { font-family: 'Bebas Neue'; font-size: 42px; color: #fff; margin-bottom: 10px; text-align: center; }
        .subtitle { text-align: center; color: var(--muted); margin-bottom: 40px; }
        
        .upload-area { background: #0d1117; border: 2px dashed var(--border); border-radius: 12px; padding: 40px; text-align: center; cursor: pointer; transition: 0.3s; margin-bottom: 20px; }
        .upload-area:hover { border-color: var(--cyan); background: rgba(56,189,248,0.05); }
        .upload-area input { display: none; }
        
        .style-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px; margin-bottom: 30px; }
        @media(max-width: 768px) { .style-grid { grid-template-columns: 1fr 1fr; } }
        .style-card { border: 1px solid var(--border); background: var(--card-bg); padding: 15px; border-radius: 8px; text-align: center; cursor: pointer; transition: 0.2s; }
        .style-card:hover { border-color: var(--yellow); }
        .style-card.active { border-color: var(--yellow); box-shadow: 0 0 15px rgba(255,183,0,0.2); background: rgba(255,183,0,0.05); }
        .style-card input { display: none; }
        
        .submit-btn { width: 100%; background: var(--cyan); color: #000; font-family: 'Bebas Neue'; font-size: 24px; padding: 16px; border: none; border-radius: 8px; cursor: pointer; transition: 0.3s; }
        .submit-btn:hover { background: #22a9e3; }
        
        .result-box { background: var(--card-bg); border: 1px solid var(--border); border-radius: 12px; padding: 30px; margin-top: 40px; text-align: center; }
        .result-img { max-width: 100%; border-radius: 8px; border: 2px solid var(--border); margin-bottom: 20px; }
        .cta-btn { display: inline-block; background: var(--green); color: #fff; font-family: 'Bebas Neue'; font-size: 22px; padding: 15px 30px; border-radius: 8px; text-decoration: none; }
    </style>
</head>
<body>
    <div style="padding:15px 4%; border-bottom:1px solid var(--border); background:#151c28;"><a href="/araclar" style="color:#fff; text-decoration:none; font-family:'Bebas Neue'; font-size:24px;">◀ WORKSİTEM ARAÇLAR</a></div>

    <div class="container">
        <h1>🛋️ AI İç Mekan Tasarımcısı</h1>
        <p class="subtitle">Odanızın boş veya eski halinin fotoğrafını yükleyin, yapay zeka saniyeler içinde yeni stilinizi yaratsın.</p>

        @if(session('success_image'))
            <!-- SONUÇ EKRANI (AI ÇIKTISI) -->
            <div class="result-box" id="resultArea">
                <h2 style="font-family:'Bebas Neue'; color:var(--yellow); font-size:32px; margin-bottom:15px;">Tasarımınız Hazır! (Stil: {{ session('style') }})</h2>
                <img src="{{ session('success_image') }}" class="result-img" alt="AI Interior Design">
                <p style="color:var(--muted); font-size:14px; margin-bottom:20px;">Bu tasarım yapay zeka tarafından referans amaçlı üretilmiştir. Gerçek hayatta birebir uygulaması mümkündür.</p>
                <a href="/maliyet-hesapla" class="cta-btn">Kainta İnşaat AI ile Bu Tasarımın Maliyetini Hesapla →</a>
            </div>
        @else
            <!-- YÜKLEME FORMU -->
            <form action="{{ route('ai.interior.generate') }}" method="POST" enctype="multipart/form-data" id="aiForm">
                @csrf
                <label class="upload-area" id="dropzone">
                    <input type="file" name="room_image" id="fileInput" accept="image/*" required>
                    <div style="font-size:40px; margin-bottom:10px;">📸</div>
                    <div style="color:#fff; font-weight:600; font-size:16px;" id="fileName">Oda fotoğrafınızı buraya sürükleyin veya tıklayın</div>
                    <div style="color:var(--muted); font-size:12px; margin-top:5px;">Max: 5MB (JPG, PNG)</div>
                </label>

                <h3 style="color:#fff; font-size:16px; margin-bottom:15px;">Dekorasyon Stilini Seçin:</h3>
                <div class="style-grid">
                    <label class="style-card active" onclick="selectStyle(this)">
                        <input type="radio" name="style" value="Modern" checked>
                        <div style="font-weight:700; color:#fff;">Modern</div>
                        <div style="font-size:11px; color:var(--muted);">Temiz hatlar, ferah görünüm</div>
                    </label>
                    <label class="style-card" onclick="selectStyle(this)">
                        <input type="radio" name="style" value="Minimalist">
                        <div style="font-weight:700; color:#fff;">Minimalist</div>
                        <div style="font-size:11px; color:var(--muted);">Az eşya, maksimum sadelik</div>
                    </label>
                    <label class="style-card" onclick="selectStyle(this)">
                        <input type="radio" name="style" value="Endüstriyel">
                        <div style="font-weight:700; color:#fff;">Endüstriyel</div>
                        <div style="font-size:11px; color:var(--muted);">Tuğla, metal ve beton dokuları</div>
                    </label>
                    <label class="style-card" onclick="selectStyle(this)">
                        <input type="radio" name="style" value="Lüks">
                        <div style="font-weight:700; color:#fff;">Premium Lüks</div>
                        <div style="font-size:11px; color:var(--muted);">Parlak yüzeyler, kaliteli detaylar</div>
                    </label>
                    <label class="style-card" onclick="selectStyle(this)">
                        <input type="radio" name="style" value="Japandi">
                        <div style="font-weight:700; color:#fff;">Japandi</div>
                        <div style="font-size:11px; color:var(--muted);">Ahşap tonları ve Zen atmosferi</div>
                    </label>
                    <label class="style-card" onclick="selectStyle(this)">
                        <input type="radio" name="style" value="Klasik">
                        <div style="font-weight:700; color:#fff;">Klasik (Vintage)</div>
                        <div style="font-size:11px; color:var(--muted);">Zarif ahşap işçiliği ve oymalar</div>
                    </label>
                </div>

                <button type="submit" class="submit-btn" id="btnSubmit">✨ YAPAY ZEKA İLE TASARLA</button>
            </form>
                @if($errors->any())
                    <div style="background:rgba(239, 68, 68, 0.1); border:1px solid #ef4444; color:#fca5a5; padding:15px; border-radius:8px; margin-top:20px; font-size:14px;">
                        <strong>Sistem Mesajı:</strong> {{ $errors->first() }}
                    </div>
                @endif
        @endif
    </div>

    <script>
        // Stil seçimi görsel efekti
        function selectStyle(element) {
            document.querySelectorAll('.style-card').forEach(el => el.classList.remove('active'));
            element.classList.add('active');
        }

        // Dosya adı gösterme
        const fileInput = document.getElementById('fileInput');
        if(fileInput) {
            fileInput.addEventListener('change', function() {
                if(this.files && this.files[0]) {
                    document.getElementById('fileName').innerText = "Seçildi: " + this.files[0].name;
                    document.getElementById('dropzone').style.borderColor = 'var(--cyan)';
                }
            });
        }
        
        // Form gönderilirken butonu kilitle ve loading göster
        const aiForm = document.getElementById('aiForm');
        if(aiForm) {
            aiForm.addEventListener('submit', function() {
                const btn = document.getElementById('btnSubmit');
                btn.innerHTML = '⏳ Yapay Zeka Tasarlıyor, Lütfen Bekleyin...';
                btn.style.opacity = '0.7';
                btn.style.pointerEvents = 'none';
            });
        }
    </script>
</body>
</html>
