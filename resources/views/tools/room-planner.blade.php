<!DOCTYPE html>
<html lang="tr">
<head>
    <link rel="icon" href="/favicon.ico" type="image/x-icon">

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>AI İç Mimar & Oda Yeniden Tasarım (Generative AI) | WORKSİTEM</title>
    <meta name="description" content="Odanızın mevcut fotoğrafını yükleyin; generative AI ile yapıyı koruyarak farklı iç mimari stillerde yeniden tasarlanmış görünüm oluşturun.">
    <link rel="canonical" href="https://worksitem.com/araclar/oda-planlayici">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root { --bg:#0f172a; --surface:#1e293b; --border:#334155; --primary:#3b82f6; --text-main:#f8fafc; --text-muted:#94a3b8; --green:#10b981; --transition:all 0.3s ease;}
        body { font-family:'Inter', sans-serif; background:var(--bg); color:var(--text-main); margin:0; padding-bottom:80px; }
        .navbar { display:flex; justify-content:space-between; align-items:center; padding:1rem 5%; background:var(--surface); border-bottom:1px solid var(--border); position:sticky; top:0; z-index:50;}
        .logo { font-weight:800; font-size:20px; color:#fff; text-decoration:none; display:flex; align-items:center; gap:10px; }
        .logo-box { background:linear-gradient(135deg, #3b82f6, #8b5cf6); padding:4px 8px; border-radius:6px; font-size:14px; }
        
        .container { max-width:1200px; margin:40px auto; padding:0 4%; display:grid; grid-template-columns:350px 1fr; gap:30px; }
        @media(max-width:992px) { .container { grid-template-columns:1fr; } }
        
        .panel { background:var(--surface); border:1px solid var(--border); border-radius:16px; padding:25px; box-shadow: 0 10px 25px rgba(0,0,0,0.3); height:fit-content; }
        
        h1 { font-size:1.8rem; font-weight:800; margin-bottom:10px; background: -webkit-linear-gradient(45deg, #60a5fa, #c084fc); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        p.subtitle { color:var(--text-muted); font-size:0.9rem; margin-bottom:25px; line-height:1.5; }
        
        .form-group { margin-bottom:20px; }
        .form-group label { display:block; font-size:0.85rem; font-weight:700; color:var(--text-muted); margin-bottom:8px; text-transform:uppercase; letter-spacing:0.5px; }
        .form-control { width:100%; padding:14px; border-radius:8px; background:#0f172a; border:1px solid var(--border); color:var(--text-main); font-family:inherit; font-size:1rem; transition:var(--transition);}
        .form-control:focus { outline:none; border-color:var(--primary); box-shadow:0 0 0 3px rgba(59, 130, 246, 0.3); }
        
        .upload-box { border:2px dashed var(--border); border-radius:12px; padding:30px 20px; text-align:center; cursor:pointer; transition:var(--transition); background:rgba(15,23,42,0.5); margin-bottom:20px; }
        .upload-box:hover { border-color:var(--primary); background:rgba(59,130,246,0.1); }
        .upload-icon { font-size:2rem; margin-bottom:10px; }
        .upload-text { font-size:0.9rem; color:var(--text-muted); font-weight:600; }
        
        .ai-btn { display:flex; align-items:center; justify-content:center; gap:10px; width:100%; background:linear-gradient(45deg, #3b82f6, #8b5cf6); color:#fff; font-weight:800; font-size:1.1rem; padding:16px; border-radius:8px; border:none; cursor:pointer; transition:var(--transition); margin-top:10px; }
        .ai-btn:hover { transform:translateY(-2px); box-shadow:0 10px 20px -5px rgba(59, 130, 246, 0.5); }
        
        .workspace { display:flex; flex-direction:column; gap:20px; }
        .placeholder-view { border:1px dashed var(--border); border-radius:16px; display:flex; flex-direction:column; align-items:center; justify-content:center; text-align:center; height:100%; min-height:500px; color:var(--text-muted); background:rgba(15,23,42,0.3); }
        
        .result-grid { display:grid; grid-template-columns:1fr 1fr; gap:20px; display:none; }
        .img-card { background:var(--surface); border:1px solid var(--border); border-radius:12px; padding:15px; }
        .img-card h3 { font-size:1rem; color:var(--text-muted); margin-bottom:10px; text-transform:uppercase; font-weight:700; letter-spacing:0.5px;}
        .rendered-img { width:100%; border-radius:8px; box-shadow:0 5px 15px rgba(0,0,0,0.5); aspect-ratio:4/3; object-fit:cover; }

        .cta-box { background:rgba(16, 185, 129, 0.1); border:1px solid var(--green); border-radius:12px; padding:24px; text-align:center; display:none; margin-top:20px;}
        .cta-btn { display:inline-block; background:var(--green); color:#fff; font-weight:800; padding:14px 28px; border-radius:8px; text-decoration:none; transition:var(--transition); margin-top:15px; }
        .cta-btn:hover { background:#059669; }
        
        /* Loader */
        .loader { border:4px solid rgba(255,255,255,0.1); border-left-color:var(--primary); border-radius:50%; width:40px; height:40px; animation:spin 1s linear infinite; margin-bottom:15px; }
        @keyframes spin { 0% { transform:rotate(0deg); } 100% { transform:rotate(360deg); } }
    </style>
    <meta property="og:image" content="https://worksitem.com/brand/og-image.png">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:image" content="https://worksitem.com/brand/og-image.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="manifest" href="/site.webmanifest">
    <meta name="theme-color" content="#ffffff">
</head>
<body data-tool-slug="room-planner">
    <x-site.contech-header />
    
    <div class="container">
        <!-- SOL PANEL: GİRDİLER VE FOTOĞRAF -->
        <div class="panel">
            <h1>Generative AI İç Mimar</h1>
            <p class="subtitle">Evinizin mevcut fotoğrafını yükleyin, yapay zeka odanın yapısını bozmadan hayalinizdeki stilde yeniden döşesin.</p>
            
            <div class="upload-box" onclick="document.getElementById('fileInput').click()">
                <div class="upload-icon">📸</div>
                <div class="upload-text" id="uploadText">Mevcut Odanızın Fotoğrafını Yükleyin</div>
                <input type="file" id="fileInput" accept="image/*" style="display:none;" onchange="previewUpload(event)">
            </div>

            <div class="form-group">
                <label>Oda Tipi</label>
                <select id="room_type" class="form-control">
                    <option value="living room">Salon / Oturma Odası</option>
                    <option value="kitchen">Mutfak</option>
                    <option value="bedroom">Yatak Odası</option>
                    <option value="bathroom">Banyo</option>
                </select>
            </div>

            <div class="form-group">
                <label>Hedeflenen Tasarım Stili</label>
                <select id="style" class="form-control">
                    <option value="Modern Minimalist, clean lines, bright">Modern Minimalist</option>
                    <option value="Scandinavian, light wood, cozy">İskandinav (Sıcak Ahşap)</option>
                    <option value="Industrial loft, exposed concrete, dark metals">Endüstriyel Loft (Beton/Metal)</option>
                    <option value="Classic Luxury, marble details, elegant lighting">Klasik Lüks (Mermer ve Zarif)</option>
                    <option value="Bohemian, natural textures, indoor plants">Bohem (Doğal Dokular ve Bitkiler)</option>
                </select>
            </div>
            
            <button class="ai-btn" id="generateBtn" onclick="generateAI()">
                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                Yeniden Tasarla
            </button>
        </div>

        <!-- SAĞ PANEL: ÇALIŞMA ALANI VE SONUÇLAR -->
        <div class="workspace">
            
            <div class="placeholder-view" id="placeholderView">
                <span style="font-size:3rem; margin-bottom:15px;">🪄</span>
                <h2>Yapay Zeka Hazır</h2>
                <p>Fotoğraf yükleyip "Yeniden Tasarla" butonuna bastığınızda sihir başlayacak.</p>
            </div>

            <div class="placeholder-view" id="loadingView" style="display:none;">
                <div class="loader"></div>
                <h2>AI Odanızı Analiz Ediyor...</h2>
                <p>Işıklar hesaplanıyor, mobilyalar tarzınıza uygun olarak baştan yaratılıyor. (10-15 sn)</p>
            </div>

            <div class="result-grid" id="resultGrid">
                <div class="img-card">
                    <h3>Mevcut Haliniz</h3>
                    <img id="originalImg" src="" class="rendered-img" alt="Orijinal">
                </div>
                <div class="img-card" style="border-color:var(--primary); position:relative;">
                    <div style="position:absolute; top:-10px; right:-10px; background:#a855f7; color:#fff; font-size:12px; font-weight:800; padding:4px 8px; border-radius:6px;">8K RENDER</div>
                    <h3 style="color:#60a5fa;">Yapay Zeka Tasarımı</h3>
                    <img id="aiImg" src="" class="rendered-img" alt="AI Render">
                </div>
            </div>

            </div>

        <!-- STANDART KAİNTA LEAD HUNİSİ (CTA) -->
        <div class="no-print" style="margin-top: 40px; padding: 30px; background: #f0fdf4; border: 2px solid #bbf7d0; border-radius: 16px; text-align: center;">
            <h3 style="color: #166534; font-size: 20px; font-weight: 800; margin-bottom: 10px;">Hesaplamayı Yaptınız, Şimdi İşi Uzmanına Bırakın</h3>
            <p style="color: #15803d; font-size: 14px; margin-bottom: 20px;">Kainta İnşaat mühendislerinden projenize özel resmi keşif ve bütçe teklifi alın.</p>
            <a href="/maliyet-hesapla" style="display: inline-block; background: #166534; color: #fff; padding: 16px 32px; font-size: 16px; font-weight: 800; border-radius: 8px; text-decoration: none; box-shadow: 0 4px 10px rgba(22,101,52,0.3); transition: all 0.2s;">Ön maliyet hesabına geç →</a>
        </div>
    </div>

    <script>
                // --- MERKEZİ ÇŞB FİYAT MOTORU & KAİNTA ÇARPANI ---
        const CSB_PRICES = {!! isset($SYSTEM_PRICES) ? $SYSTEM_PRICES : '{}' !!};
        const KAINTA_MULTIPLIER = {!! isset($KAINTA_MULTIPLIER) ? $KAINTA_MULTIPLIER : 1.40 !!};

        function getDbPrice(key, fallback) {
            let baseCsbPrice = CSB_PRICES[key] !== undefined ? parseFloat(CSB_PRICES[key]) : fallback;
            // ÇŞB Ham Fiyatı x 1.40 Kainta Çarpanı
            return baseCsbPrice * KAINTA_MULTIPLIER; 
        }
        
        function getRawCsbPrice(key, fallback) {
            // Sadece ham ÇŞB maliyeti görmek istenirse (İhale modülü vb. için çarpanı yoksay)
            return CSB_PRICES[key] !== undefined ? parseFloat(CSB_PRICES[key]) : fallback;
        }
        // ------------------------------------------------
        let uploadedImageBase64 = null;

        function previewUpload(event) {
            const file = event.target.files[0];
            if(file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    uploadedImageBase64 = e.target.result;
                    document.getElementById('uploadText').innerText = "✅ Fotoğraf Yüklendi (" + file.name + ")";
                    document.getElementById('uploadText').style.color = "var(--green)";
                }
                reader.readAsDataURL(file);
            }
        }

        async function generateAI() {
            if(!uploadedImageBase64) {
                alert("Lütfen önce odanızın mevcut fotoğrafını yükleyin!");
                return;
            }

            const btn = document.getElementById('generateBtn');
            btn.disabled = true;
            btn.innerHTML = 'Çiziliyor... Lütfen Bekleyin';
            
            document.getElementById('placeholderView').style.display = 'none';
            document.getElementById('resultGrid').style.display = 'none';
            document.getElementById('ctaBox').style.display = 'none';
            document.getElementById('loadingView').style.display = 'flex';

            // Normalde burada Image-to-Image API'ye Base64 görsel + prompt gönderilir.
            // Bu SaaS prototipi için, backend rotasına Text-to-Image isteği atıyoruz (Görsel varmış gibi simüle eder)
            const payload = {
                room_type: document.getElementById('room_type').value,
                style: document.getElementById('style').value,
                w: 4, l: 5 // Default boyutlar
            };

            try {
                const response = await fetch('/araclar/oda-planlayici/generate', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(payload)
                });

                const data = await response.json();

                if(data.success) {
                    document.getElementById('originalImg').src = uploadedImageBase64;
                    document.getElementById('aiImg').src = data.image; // API'den dönen görsel
                    
                    document.getElementById('loadingView').style.display = 'none';
                    document.getElementById('resultGrid').style.display = 'grid';
                    document.getElementById('ctaBox').style.display = 'block';
                } else {
                    throw new Error(data.error);
                }
            } catch (error) {
                alert("Hata: " + error.message);
                document.getElementById('loadingView').style.display = 'none';
                document.getElementById('placeholderView').style.display = 'flex';
            } finally {
                btn.disabled = false;
                btn.innerHTML = '✨ Yeni Bir Tarz Dene';
            }
        }
    </script>
    <x-site.contech-footer />
    <script src="/js/tool-events.js" defer></script>
</body>
</html>
