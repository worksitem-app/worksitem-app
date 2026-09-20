<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Anahtar Teslim İnşaat ve Tadilat | Kainta İnşaat</title>
    <meta name="description" content="Villa, ev, fabrika, ofis ve ticari yapılar için anahtar teslim inşaat ve tadilat hizmeti. Projenizin yaklaşık maliyetini Kainta İnşaat AI Proje Asistanı ile dakikalar içinde öğrenin.">
    
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    <link rel="shortcut icon" type="image/x-icon" href="/favicon.ico">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        :root { 
            --bg: #f8fafc; --card-bg: #ffffff; --input-bg: #f1f5f9; --border: #e2e8f0; 
            --yellow: #ffb700; --yellow-hover: #e0a200; --text-dark: #0f172a; 
            --text-muted: #64748b; --green: #10b981; --cyan: #0284c7; 
            --glass: rgba(255, 255, 255, 0.85);
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        html { scroll-behavior: smooth; }
        body { font-family: 'Inter', sans-serif; background: var(--bg); color: var(--text-dark); overflow-x: hidden; }

        .navbar { display: flex; justify-content: space-between; align-items: center; padding: 15px 5%; background: #ffffff; border-bottom: 2px solid var(--border); position: fixed; top: 0; left: 0; width: 100%; z-index: 1000; box-shadow: 0 4px 15px rgba(0,0,0,0.03); }
        .logo-container { display: flex; align-items: center; gap: 12px; text-decoration: none; }
        .logo-img { height: 42px; width: auto; object-fit: contain; }

        .hero-container { max-width: 1600px; margin: 0 auto; padding: 150px 4% 60px 4%; display: grid; grid-template-columns: 1fr 1fr; gap: 60px; align-items: center; min-height: 85vh; }
        @media(max-width: 1024px) { .hero-container { grid-template-columns: 1fr; text-align: center; padding-top: 120px; } }
        
        .hero-left { text-align: left; }
        @media(max-width: 1024px) { .hero-left { text-align: center; } }
        
        .badge-ai { display: inline-flex; align-items: center; gap: 8px; background: #fffbeb; border: 1px solid #fde68a; color: #d97706; font-size: 12px; font-weight: 800; padding: 8px 18px; border-radius: 30px; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 25px; box-shadow: 0 4px 15px rgba(217, 119, 6, 0.1); }
        .hero-title { font-family: 'Bebas Neue', cursive; font-size: clamp(40px, 6vw, 85px); line-height: 0.95; letter-spacing: 1px; color: var(--text-dark); text-transform: uppercase; margin-bottom: 20px; }
        .hero-title span { background: linear-gradient(90deg, #d97706, #ffb700); -webkit-background-clip: text; -webkit-text-fill-color: transparent; display: block; }
        .hero-sub { color: var(--text-muted); font-size: clamp(15px, 1.8vw, 17px); max-width: 650px; margin-bottom: 40px; font-weight: 500; line-height: 1.7; }
        @media(max-width: 1024px) { .hero-sub { margin-left: auto; margin-right: auto; } }

        .hero-ctas { display: flex; gap: 15px; flex-wrap: wrap; }
        @media(max-width: 1024px) { .hero-ctas { justify-content: center; } }
        .btn-main { display: inline-flex; align-items: center; justify-content: center; gap: 10px; background: var(--yellow); color: #000; font-family: 'Bebas Neue', cursive; font-size: 24px; letter-spacing: 1px; padding: 18px 40px; border-radius: 12px; text-decoration: none; transition: all 0.3s; box-shadow: 0 10px 30px rgba(255, 183, 0, 0.3); font-weight: bold; border: none; cursor: pointer; }
        .btn-second { display: inline-flex; align-items: center; justify-content: center; gap: 10px; background: #ffffff; border: 2px solid var(--border); color: var(--text-dark); font-family: 'Bebas Neue', cursive; font-size: 20px; letter-spacing: 1px; padding: 18px 35px; border-radius: 12px; text-decoration: none; transition: all 0.3s; font-weight: bold; cursor: pointer; }
        .btn-main:hover { transform: translateY(-4px); box-shadow: 0 15px 35px rgba(255, 183, 0, 0.4); }
        .btn-second:hover { border-color: var(--text-dark); transform: translateY(-4px); }

        .hero-right-cloud-box { position: relative; width: 100%; display: flex; flex-direction: column; align-items: center; }
        .clouds-grid-2x2 { display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; width: 100%; max-width: 650px; }
        @media(max-width: 600px) { .clouds-grid-2x2 { grid-template-columns: 1fr; } }
        .cloud-mini-card { background: #ffffff; border: 1px solid var(--border); border-radius: 20px; padding: 30px 20px; text-align: left; box-shadow: 0 10px 30px rgba(0,0,0,0.03); cursor: pointer; transition: all 0.3s ease; text-decoration: none; display: block; }
        .cloud-mini-card:hover { transform: translateY(-8px); border-color: var(--yellow); box-shadow: 0 15px 35px rgba(255, 183, 0, 0.2); }
        .cloud-mini-card .c-icon { font-size: 32px; display: block; margin-bottom: 15px; }
        .cloud-mini-card .c-title { font-size: 18px; font-weight: 800; color: var(--text-dark); margin-bottom: 8px; line-height: 1.2; }
        .cloud-mini-card .c-sub { font-size: 13px; color: var(--text-muted); font-weight: 500; line-height: 1.5; }

        /* YENİ DÜRÜST MALİYET TICKER'I VE MOBİL DÜZELTMESİ */
        .calculator-section { padding-top: 50px; }
        .ticker-wrap { width: 100%; background: #0f172a; border-bottom: 2px solid #1e293b; overflow: hidden; height: 50px; display: flex; align-items: center; box-shadow: 0 10px 20px rgba(0,0,0,0.1); position: relative; }
        .ticker-title { background: var(--yellow); color: #000; font-weight: 900; font-size: 12px; padding: 0 25px; height: 100%; display: flex; align-items: center; text-transform: uppercase; letter-spacing: 1px; z-index: 2; flex-shrink: 0; box-shadow: 5px 0 15px rgba(0,0,0,0.2); }
        .ticker-move { display: flex; gap: 40px; white-space: nowrap; animation: ticker 40s linear infinite; }
        .ticker-item { font-size: 14px; color: #cbd5e1; display: flex; align-items: center; gap: 8px; font-weight: 600; }
        .ticker-item strong { color: #ffffff; }
        @keyframes ticker { 0% { transform: translateX(0); } 100% { transform: translateX(-50%); } }

        @media(max-width: 768px) {
            .ticker-wrap { height: 45px; }
            .ticker-title { 
                font-size: 9px; 
                padding: 0 8px; 
                white-space: normal; 
                text-align: center; 
                line-height: 1.1; 
                max-width: 120px; 
            }
            .ticker-item { font-size: 12px; }
            .ticker-move { gap: 20px; }
        }

        .dashboard-container { display: grid; grid-template-columns: 580px 1fr; gap: 40px; padding: 50px 4%; max-width: 1650px; margin: 0 auto; }
        @media(max-width: 1100px) { .dashboard-container { grid-template-columns: 1fr; } }
        
        .left-panel { background: var(--card-bg); border: 1px solid var(--border); border-radius: 20px; padding: 40px; height: fit-content; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.08); position: relative; overflow: hidden; }
        .main-panel-title { font-family: 'Bebas Neue'; font-size: 32px; color: var(--text-dark); letter-spacing: 1px; margin-bottom: 20px; border-bottom: 2px solid var(--border); padding-bottom: 12px; display: flex; align-items: center; gap: 10px; }
        
        .form-tabs { display: flex; gap: 10px; margin-bottom: 25px; background: var(--input-bg); padding: 8px; border-radius: 12px; }
        .form-tab-btn { flex: 1; padding: 14px 5px; background: transparent; border: none; border-radius: 8px; font-weight: 800; font-size: 13px; color: var(--text-muted); cursor: pointer; transition: 0.3s; text-transform: uppercase; letter-spacing: 0.5px; text-align: center; }
        .form-tab-btn.active { background: var(--card-bg); color: var(--text-dark); box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        
        .form-section { display: none; }
        .form-section.active { display: block; animation: fadeIn 0.5s ease; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

        .section-sub-header { font-family: 'Bebas Neue'; font-size: 20px; color: #d97706; letter-spacing: 1px; margin-top: 30px; margin-bottom: 15px; display: flex; align-items: center; gap: 8px; }
        
        .form-group { margin-bottom: 20px; }
        .form-label { display: block; font-size: 12px; font-weight: 800; color: var(--text-dark); text-transform: uppercase; margin-bottom: 8px; letter-spacing: 0.5px; }
        .form-control { width: 100%; background: var(--input-bg); border: 1px solid var(--border); border-radius: 10px; padding: 14px 16px; color: var(--text-dark); font-size: 14px; font-family: inherit; outline: none; transition: all 0.3s; font-weight: 600; }
        .form-control:focus { border-color: var(--yellow); background: #ffffff; box-shadow: 0 0 0 4px rgba(255, 183, 0, 0.15); transform: translateY(-2px); }
        .grid-2-form { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }

        .textarea-notes {
            width: 100%; min-height: 110px; background: var(--input-bg); border: 1px solid var(--border);
            border-radius: 10px; padding: 14px 16px; color: var(--text-dark); font-size: 14px;
            font-family: inherit; resize: vertical; outline: none; transition: all 0.3s;
        }
        .textarea-notes:focus { border-color: var(--yellow); background: #ffffff; box-shadow: 0 0 0 4px rgba(255, 183, 0, 0.15); }

        .quality-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; margin-top: 8px; }
        .quality-card { background: var(--input-bg); border: 1px solid var(--border); border-radius: 12px; padding: 16px 8px; text-align: center; cursor: pointer; transition: all 0.3s; }
        .quality-card:hover, .quality-card.active { border-color: var(--yellow); background: #fffbe3; box-shadow: 0 8px 20px rgba(255,183,0,0.2); transform: translateY(-3px); }
        .quality-card .q-icon { font-size: 24px; display: block; margin-bottom: 6px; }
        .quality-card .q-title { font-size: 13px; font-weight: 800; color: var(--text-dark); }

        .upload-grid-5 { display: grid; grid-template-columns: repeat(2, 1fr); gap: 12px; margin-bottom: 15px; }
        .upload-card { background: var(--input-bg); border: 1.5px dashed #cbd5e1; border-radius: 12px; padding: 20px 10px; text-align: center; cursor: pointer; transition: all 0.3s; }
        .upload-card:hover { border-color: var(--yellow); background: #fffbe3; transform: translateY(-2px); }
        .upload-card.full-width { grid-column: span 2; }
        .upload-card.has-file { border-color: var(--green); background: #f0fdf4; border-style: solid; }
        .upload-card .icon { font-size: 24px; margin-bottom: 6px; display: block; }
        .upload-card .title { font-size: 12px; font-weight: 800; color: var(--text-dark); margin-bottom: 4px; }
        .upload-card .subtitle { font-size: 10px; color: var(--text-muted); font-weight: 600; }
        .hidden-file-input { display: none; }

        .extras-list { display: flex; flex-direction: column; gap: 10px; margin-top: 10px; }
        .extra-item-wrapper { background: var(--input-bg); border: 1px solid var(--border); border-radius: 12px; padding: 14px 16px; transition: all 0.3s; display: flex; flex-direction: column; gap: 10px; }
        .extra-item-wrapper:hover { border-color: var(--yellow); background: #fffbe3; transform: translateX(5px); box-shadow: 0 4px 15px rgba(255,183,0,0.1); }
        .extra-main-row { display: flex; align-items: center; justify-content: space-between; cursor: pointer; }
        .extra-main-row input[type="checkbox"] { width: 18px; height: 18px; accent-color: var(--yellow); cursor: pointer; margin-right: 12px; }
        .extra-label { font-size: 13px; font-weight: 800; color: var(--text-dark); cursor: pointer; flex: 1; }

        .csb-info-box { background: #f0f9ff; border: 1px solid #bae6fd; border-radius: 10px; padding: 14px; margin-top: 12px; font-size: 12px; }
        .csb-info-title { color: var(--cyan); font-weight: 800; display: flex; align-items: center; justify-content: space-between; cursor: pointer; user-select: none; }
        .csb-info-details { display: none; margin-top: 12px; color: var(--text-muted); border-top: 1px dashed #bae6fd; padding-top: 12px; line-height: 1.6; }
        .csb-info-details strong { color: var(--text-dark); }
        .csb-info-details ul { padding-left: 15px; margin-top: 5px; }
        .csb-info-details li { margin-bottom: 6px; }

        .metraj-table { width: 100%; border-collapse: collapse; margin-top: 20px; font-size: 13px; }
        .metraj-table th { background: #0f172a; color: #fff; padding: 12px; text-align: left; font-size: 12px; text-transform: uppercase; font-weight: 800; }
        .metraj-table td { padding: 12px 10px; border-bottom: 1px solid #e2e8f0; font-weight: 600; color: var(--text-dark); }
        .metraj-table td span { color: var(--cyan); font-weight: 800; }
        .metraj-table tr:hover { background: #f8fafc; }
        .metraj-result-box { display: none; margin-top: 20px; animation: fadeIn 0.4s ease; border: 2px solid var(--border); border-radius: 12px; overflow: hidden; }
        
        .btn-submit { width: 100%; background: var(--yellow); color: #000; font-family: 'Bebas Neue'; font-size: 26px; letter-spacing: 1.5px; padding: 20px; border: none; border-radius: 12px; cursor: pointer; margin-top: 25px; transition: all 0.3s; font-weight: bold; box-shadow: 0 10px 25px rgba(255, 183, 0, 0.3); }
        .btn-submit:hover { background: var(--yellow-hover); transform: translateY(-3px); box-shadow: 0 15px 35px rgba(255, 183, 0, 0.4); }

        .liveTadilatResultBox { display:none; margin-top:30px; background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%); border: 2px solid var(--green); border-radius: 16px; padding: 25px; text-align: center; animation: fadeIn 0.5s ease; box-shadow: 0 10px 30px rgba(16, 185, 129, 0.15); }

        /* SAĞ PANEL: İŞ MODELİ AÇIKLAMALARI VE TİCARİ HUNİ */
        .right-panel { background: var(--card-bg); border: 1px solid var(--border); border-radius: 20px; padding: 40px; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.08); display: flex; flex-direction: column; }
        .funnel-header { font-family: 'Bebas Neue'; font-size: 36px; color: var(--text-dark); margin-bottom: 10px; letter-spacing: 1px; }
        .funnel-sub { font-size: 14px; color: var(--text-muted); line-height: 1.6; margin-bottom: 30px; padding-bottom: 30px; border-bottom: 1px solid var(--border); }
        
        .process-list { display: flex; flex-direction: column; gap: 20px; }
        .process-item { display: flex; gap: 20px; align-items: flex-start; }
        .process-num { font-family: 'Bebas Neue'; font-size: 36px; color: var(--border); line-height: 1; }
        .process-content strong { display: block; font-size: 16px; color: var(--text-dark); font-weight: 800; margin-bottom: 5px; }
        .process-content p { font-size: 13px; color: var(--text-muted); line-height: 1.5; margin: 0; }
        
        .keşif-box { background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 16px; padding: 25px; margin-top: 40px; }
        .keşif-box-title { font-size: 15px; font-weight: 800; color: var(--text-dark); margin-bottom: 15px; text-transform: uppercase; letter-spacing: 0.5px; display: flex; align-items: center; gap: 8px; }
        .keşif-list { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 20px; }
        .keşif-list li { font-size: 12px; color: var(--text-muted); font-weight: 600; display: flex; align-items: center; gap: 6px; }
        .keşif-list li::before { content: '✓'; color: var(--green); font-weight: bold; }

        /* BRAND STATEMENT */
        .brand-statement-box { background: #0f172a; color: #ffffff; padding: 45px 5%; text-align: center; font-size: 20px; font-weight: 600; line-height: 1.7; margin-top: 60px; box-shadow: inset 0 10px 30px rgba(0,0,0,0.5); }
        .brand-statement-box span { color: var(--yellow); font-weight: 800; }

        /* FOOTER & MODALS */
        .site-footer { border-top: 1px solid var(--border); padding: 30px 5%; display: flex; justify-content: space-between; align-items: center; font-size: 13px; color: var(--text-muted); background: #ffffff; }
        .footer-links { display: flex; gap: 25px; }
        .footer-links a { color: var(--text-muted); text-decoration: none; cursor: pointer; font-weight: 700; transition: 0.2s; }
        .footer-links a:hover { color: var(--text-dark); }
        
        .modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(15, 23, 42, 0.8); z-index: 9999; display: none; align-items: center; justify-content: center; backdrop-filter: blur(8px); }
        .modal-overlay.active { display: flex; animation: fadeIn 0.3s ease; }
        .modal-box { background: #ffffff; border: 1px solid var(--border); border-radius: 16px; width: 90%; max-width: 600px; padding: 40px; max-height: 85vh; overflow-y: auto; box-shadow: 0 25px 50px rgba(0,0,0,0.3); }
        .modal-title { font-family: 'Bebas Neue'; font-size: 32px; color: var(--text-dark); margin-bottom: 20px; }
        .modal-close-btn { background: var(--yellow); color: #000; font-weight: 800; border: none; padding: 12px 30px; border-radius: 8px; cursor: pointer; margin-top: 30px; font-size: 14px; letter-spacing: 1px; }
    </style>
</head>
<body>

    <nav class="navbar">
        <a href="javascript:void(0)" onclick="openLeadModal('Anasayfa')" class="logo-container">
            <img src="/brand/worksitem-logo.png" alt="WORKSİTEM" class="logo-img">
        </a>
    </nav>

    <!-- HERO SECTION -->
    <div class="hero-container">
        <div class="hero-left">
            <div class="badge-ai">⚡ KAİNTA AI PROJE ASİSTANI</div>
            <h1 class="hero-title">İnşaat ve Tadilat Maliyeti Hesaplama | <br><span>Kainta İnşaat</span></h1>
            <p class="hero-sub">Villa, ev, fabrika, ofis ve ticari projeleriniz için yaklaşık inşaat ve tadilat maliyetini hesaplayın. Projenizi Kainta İnşaat'ın teknik ekibiyle değerlendirin.</p>
            
            <div class="hero-ctas">
                <button onclick="document.getElementById('tahminci').scrollIntoView({behavior: 'smooth'})" class="btn-main">İnşaat Maliyetimi Hesapla →</button>
                <button type="button" onclick="document.getElementById('leadModalIndex').style.display='flex'" class="btn-second" style="display:inline-flex;">Kainta İnşaat'tan Profesyonel Keşif İste →</button>
            </div>
        </div>

        <div class="hero-right-cloud-box">
            <div style="font-family:'Bebas Neue'; font-size:24px; margin-bottom:20px; color:var(--text-dark); text-align:center;">NELER YAPABİLİRSİNİZ?</div>
            <div class="clouds-grid-2x2">
                <div class="cloud-mini-card" onclick="switchTab('insaat')"><span class="c-icon">🏡</span><div class="c-title">Villa İnşaatı</div><div class="c-sub">Villa projenizin yaklaşık yapım maliyetini hesaplayın.</div></div>
                <div class="cloud-mini-card" onclick="switchTab('tadilat')"><span class="c-icon">🏠</span><div class="c-title">Ev Tadilatı</div><div class="c-sub">Tadilat kapsamınızı belirleyerek yaklaşık maliyet oluşturun.</div></div>
                <div class="cloud-mini-card" onclick="switchTab('insaat')"><span class="c-icon">🏭</span><div class="c-title">Fabrika & Depo</div><div class="c-sub">Sanayi yapılarınız için proje bazlı ön maliyet analizi yapın.</div></div>
                <div class="cloud-mini-card" onclick="switchTab('tadilat')"><span class="c-icon">🏢</span><div class="c-title">Ofis & Ticari Alan</div><div class="c-sub">Ofis, mağaza ve ticari alanların yapım/tadilat maliyetlerini değerlendirin.</div></div>
            </div>
        </div>
    </div>

    <!-- HESAPLAYICI BÖLÜMÜ -->
    <div id="tahminci" class="calculator-section">
        
        <!-- DÜRÜST MALİYET TICKER'I -->
        <div class="ticker-wrap">
            <div class="ticker-title">💡 2026 İnşaat Maliyetlerinde Hangi Kalemler Etkili?</div>
            <div class="ticker-move">
                <div class="ticker-item">▪ <strong>BETON</strong></div>
                <div class="ticker-item">▪ <strong>DEMİR (REBAR)</strong></div>
                <div class="ticker-item">▪ <strong>İŞÇİLİK GİDERLERİ</strong></div>
                <div class="ticker-item">▪ <strong>MEKANİK TESİSAT</strong></div>
                <div class="ticker-item">▪ <strong>ELEKTRİK TESİSATI</strong></div>
                <div class="ticker-item">▪ <strong>MİMARİ & İNCE İŞLER</strong></div>
                <div class="ticker-item">▪ <strong>ZEMİN VE KAPLAMA</strong></div>
                <div class="ticker-item">▪ <strong>DIŞ CEPHE YALITIM</strong></div>
                <div class="ticker-item">▪ <strong>ÇATI ÖRTÜSÜ</strong></div>
            </div>
        </div>

        <div class="dashboard-container">
            <div class="left-panel">
                <div class="form-tabs">
                    <button type="button" class="form-tab-btn active" id="btn-tab-insaat" onclick="switchTab('insaat')">🏢 İNŞAAT MALİYETİ</button>
                    <button type="button" class="form-tab-btn" id="btn-tab-tadilat" onclick="switchTab('tadilat')">🛠️ TADİLAT MALİYETİ</button>
                    <button type="button" class="form-tab-btn" id="btn-tab-metraj" onclick="switchTab('metraj')">📐 HIZLI METRAJ</button>
                </div>

                <!-- ================= TAB 1: YENİ İNŞAAT ALANI ================= -->
                <!-- name="" parametreleri db'ye uygun şekilde (question_22 vs) eşleşecek şekilde backend'de işlenecektir -->
                <form action="{{ route('projects.store') }}" method="POST" enctype="multipart/form-data" id="section-insaat" class="form-section active">
                    @csrf
                    <div class="main-panel-title">İnşaat Projenizin Yaklaşık Maliyetini Öğrenin</div>
                    <p style="font-size: 13px; color: var(--text-muted); margin-bottom: 25px; line-height: 1.6;">
                        Arsa alanı, inşaat m²'si, kat sayısı, yapı sistemi ve kalite seviyesine göre projenizin yaklaşık maliyetini hesaplayalım.<br>
                        <strong style="color: var(--text-dark); display: block; margin-top: 8px;">• Ön Fizibilite • Şeffaf Yaklaşım • Kainta İnşaat Uzmanlığı</strong>
                    </p>
                    
                    <div class="section-sub-header">📋 TEMEL BİLGİLER</div>
                    <div class="form-group">
                        <label class="form-label">Proje / Bina Adı</label>
                        <input type="text" name="name" class="form-control" placeholder="Örneğin Kadıköy Kentsel Dönüşüm Projesi" required>
                    </div>

                    <div class="grid-2-form">
                        <div class="form-group">
                            <label class="form-label">Şehir / Konum</label>
                            <select name="city" class="form-control" id="citySelect" onchange="updateDistricts()" required>
                                <option value="" disabled selected>Şehir seçin</option>
                                <option value="istanbul">İstanbul</option><option value="ankara">Ankara</option><option value="izmir">İzmir</option>
                                <option value="antalya">Antalya</option><option value="bursa">Bursa</option><option value="adana">Adana</option>
                                <option value="gaziantep">Gaziantep</option><option value="anadolu">Diğer Anadolu Şehirleri</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">İlçe / Bölge Segmenti</label>
                            <select name="district" class="form-control" id="districtSelect" required>
                                <option value="" disabled selected>Önce Şehir Seçin</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">ÇŞB Yapı Sınıfı & Tipi</label>
                        <select name="project_type_id" id="projectTypeSelect" class="form-control" required style="border: 2px solid var(--border); font-weight: 700; background: #fff;">
                            <option value="" disabled selected>Lütfen Yapı Sınıfı Seçin</option>
                            <!-- ID'ler Blade'de dinamik basılacak (Backend loop üzerinden) -->
                            @if(isset($projectTypes) && count($projectTypes) > 0)
                                @foreach($projectTypes as $type)
                                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                                @endforeach
                            @else
                                <option value="1">III-A: Apartman Konut (1-3 Katlı)</option>
                                <option value="2">III-B: Apartman Konut (4-7 Katlı)</option>
                                <option value="4">III-B: Müstakil Villa / İkiz Konut</option>
                                <option value="7">III-B: İş Merkezi / Ticari Yapı</option>
                            @endif
                        </select>
                        <div style="font-size:10px; color:var(--text-muted); margin-top:5px;">* 2026 resmî yapı yaklaşık birim maliyetleri referans alınarak hesaplanır; gerçek yapım maliyeti değildir.</div>
                    </div>

                    <div class="section-sub-header">📐 BOYUTLAR VE YAPI</div>
                    <div class="form-group">
                        <label class="form-label">Binanın Toplam İnşaat Alanı (m²)</label>
                        <input type="number" name="construction_area" class="form-control" placeholder="Örneğin 1500" required>
                    </div>

                    <div class="grid-2-form">
                        <div class="form-group"><label class="form-label">Arsa Alanı (m²)</label><input type="number" name="land_area" class="form-control" placeholder="Örneğin 800"></div>
                        <div class="form-group"><label class="form-label">Kat Başına Alan (m²)</label><input type="number" name="floor_area" class="form-control" placeholder="Örneğin 300"></div>
                    </div>

                    <div class="grid-2-form">
                        <div class="form-group">
                            <label class="form-label">Kat Sayısı</label>
                            <select name="floors" class="form-control">
                                <option value="1">Zemin Kat (Tek Kat)</option><option value="2">2 Katlı (Dublex)</option>
                                <option value="3">3 Katlı (Triplex)</option><option value="5">4-6 Katlı Bina</option><option value="10">7+ Katlı Apartman</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Bodrum</label>
                            <select name="basement" class="form-control">
                                <option value="0">Bodrum katı yok</option><option value="1">1 Bodrum</option><option value="2">2 Bodrum</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="section-sub-header">⭐ YAPI KALİTESİ</div>
                    <div class="quality-grid">
                        <div class="quality-card" onclick="selectQuality(this, 'ekonomi')"><span class="q-icon">📦</span><div class="q-title">Ekonomi</div></div>
                        <div class="quality-card active" onclick="selectQuality(this, 'standart')"><span class="q-icon">🏢</span><div class="q-title">Standart</div></div>
                        <div class="quality-card" onclick="selectQuality(this, 'premium')"><span class="q-icon">👑</span><div class="q-title">Premium</div></div>
                    </div>
                    <input type="hidden" name="quality" id="qualityInput" value="standart">

                    <div class="section-sub-header">📁 PLANLARI VE FOTOĞRAFLARI YÜKLE</div>
                    <div class="upload-grid-5">
                        <div class="upload-card" onclick="document.getElementById('input_bina').click()"><span class="icon">🏠</span><div class="title" id="lbl_bina">Bina Fotoğrafı</div><div class="subtitle">JPG / PNG / PDF</div><input type="file" name="file_bina" id="input_bina" class="hidden-file-input" onchange="handleFileSelect(this, 'lbl_bina')"></div>
                        <div class="upload-card" onclick="document.getElementById('input_kat').click()"><span class="icon">📄</span><div class="title" id="lbl_kat">Kat Planı</div><div class="subtitle">JPG / PNG / PDF</div><input type="file" name="file_kat" id="input_kat" class="hidden-file-input" onchange="handleFileSelect(this, 'lbl_kat')"></div>
                        <div class="upload-card" onclick="document.getElementById('input_kolon').click()"><span class="icon">📒</span><div class="title" id="lbl_kolon">Kolon & Temel Planı</div><div class="subtitle">Yüklemek için tıklayın</div><input type="file" name="file_kolon" id="input_kolon" class="hidden-file-input" onchange="handleFileSelect(this, 'lbl_kolon')"></div>
                        <div class="upload-card" onclick="document.getElementById('input_doseme').click()"><span class="icon">🔅</span><div class="title" id="lbl_doseme">Döşeme & Kiriş Planı</div><div class="subtitle">Yüklemek için tıklayın</div><input type="file" name="file_doseme" id="input_doseme" class="hidden-file-input" onchange="handleFileSelect(this, 'lbl_doseme')"></div>
                        <div class="upload-card full-width" onclick="document.getElementById('input_diger').click()"><span class="icon">📎</span><div class="title" id="lbl_diger">Diğer Belgeler</div><div class="subtitle">Yüklemek için tıklayın</div><input type="file" name="file_diger" id="input_diger" class="hidden-file-input" onchange="handleFileSelect(this, 'lbl_diger')"></div>
                    </div>

                    <div class="section-sub-header">📝 EK GEREKSİNİMLER</div>
                    <div class="form-group"><textarea name="notes" class="textarea-notes" placeholder="Örneğin yüzme havuzu, güneş panelleri, modüler mutfak..."></textarea></div>

                    <button type="submit" class="btn-submit">Maliyetimi Hesapla →</button>
                </form>

                <!-- ================= TAB 2: TADİLAT ================= -->
                <form action="{{ route('projects.store') }}" method="POST" id="section-tadilat" class="form-section">
                    @csrf
                    <input type="hidden" name="is_renovation" value="1">
                    <div class="main-panel-title">Tadilat Detaylarınızı Girin</div>
                    <p style="font-size: 13px; color: var(--text-muted); margin-bottom: 25px; line-height: 1.6;">
                        Tadilatınız için gerçekçi bir fiyat tahmini almak ve uzman ekibimizden teklif istemek için aşağıdaki soruları yanıtlayın.
                    </p>
                    
                    <div class="form-group">
                        <label class="form-label">Mülk Türü *</label>
                        <select name="property_type" id="live_prop" class="form-control" onchange="calculateLive()" required>
                            <option value="Apartman Binası (Daire)">Apartman Binası (Daire)</option>
                            <option value="Müstakil Ev / Villa">Müstakil Ev / Villa</option>
                            <option value="Ticari / Ofis">Ticari Alan / Ofis</option>
                        </select>
                    </div>

                    <div class="grid-2-form">
                        <div class="form-group">
                            <label class="form-label">Yenilenecek Alan (m²) *</label>
                            <input type="number" name="renovation_area" id="live_area" class="form-control" placeholder="Örn: 100" onkeyup="calculateLive()" onchange="calculateLive()" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Konum *</label>
                            <select name="renovation_city" id="live_city" class="form-control" onchange="calculateLive()" required>
                                <option value="merkez">İstanbul (Kadıköy, Beşiktaş, Şişli vb.)</option>
                                <option value="orta">İstanbul (Ümraniye, Maltepe, Pendik vb.)</option>
                                <option value="dis">İstanbul (Esenyurt, Beylikdüzü vb.)</option>
                                <option value="diger">Diğer Şehirler</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Yenileme Kapsamı *</label>
                        <select name="renovation_scope" id="live_scope" class="form-control" onchange="calculateLive()" required>
                            <option value="hafif">Hafif Tadilat (Boyama, zemin döşeme, ufak onarımlar)</option>
                            <option value="orta" selected>Orta Ölçekli (Örn: Mutfak + Yüzey yenileme)</option>
                            <option value="komple">Komple Tadilat (Birden fazla oda, banyo/mutfak, tesisat)</option>
                        </select>
                    </div>

                    <div class="section-sub-header">YENİLEME HANGİ ALANLARI KAPSIYOR?</div>
                    <div class="grid-2-form" style="margin-bottom: 20px;">
                        <label style="display:flex; align-items:center; gap:8px; font-size:13px; font-weight:600;"><input type="checkbox" name="rooms[]" value="Banyo" onchange="calculateLive()" style="width:18px; height:18px;"> Banyo</label>
                        <label style="display:flex; align-items:center; gap:8px; font-size:13px; font-weight:600;"><input type="checkbox" name="rooms[]" value="Mutfak" onchange="calculateLive()" style="width:18px; height:18px;"> Mutfak</label>
                        <label style="display:flex; align-items:center; gap:8px; font-size:13px; font-weight:600;"><input type="checkbox" name="rooms[]" value="Zeminler" onchange="calculateLive()" style="width:18px; height:18px;"> Zeminler (Parke/Seramik)</label>
                        <label style="display:flex; align-items:center; gap:8px; font-size:13px; font-weight:600;"><input type="checkbox" name="rooms[]" value="Duvarlar/Tavanlar" onchange="calculateLive()" style="width:18px; height:18px;"> Duvarlar ve Tavanlar</label>
                        <label style="display:flex; align-items:center; gap:8px; font-size:13px; font-weight:600;"><input type="checkbox" name="rooms[]" value="Tesisat" onchange="calculateLive()" style="width:18px; height:18px;"> Su Tesisatı</label>
                        <label style="display:flex; align-items:center; gap:8px; font-size:13px; font-weight:600;"><input type="checkbox" name="rooms[]" value="Elektrik" onchange="calculateLive()" style="width:18px; height:18px;"> Elektrik İşleri</label>
                    </div>

                    <div class="section-sub-header">KALİTE SEVİYESİ *</div>
                    <div class="quality-grid">
                        <div class="quality-card" onclick="selectRenovationQuality(this, 'temel')"><span class="q-icon">📦</span><div class="q-title">Temel Seviye</div><div style="font-size:10px; color:var(--text-muted); margin-top:4px;">İşlevsel ve düzenli</div></div>
                        <div class="quality-card active" onclick="selectRenovationQuality(this, 'orta')"><span class="q-icon">🏢</span><div class="q-title">İyi Orta Sınıf</div><div style="font-size:10px; color:var(--text-muted); margin-top:4px;">Dayanıklı, yüksek kalite</div></div>
                        <div class="quality-card" onclick="selectRenovationQuality(this, 'premium')"><span class="q-icon">👑</span><div class="q-title">Premium</div><div style="font-size:10px; color:var(--text-muted); margin-top:4px;">Tasarımcı seviyesi</div></div>
                    </div>
                    <input type="hidden" name="renovation_quality" id="live_quality" value="orta">

                    <button type="submit" class="btn-submit" style="background: #10b981; color:#fff; margin-top:25px;">Kesin Hesaplama ve Rapor Al →</button>
                </form>

                <!-- ================= TAB 3: HIZLI METRAJ HESAP MAKİNESİ ================= -->
                <div id="section-metraj" class="form-section">
                    <div class="main-panel-title">Yaklaşık Kaba İnşaat Metrajı</div>
                    <p style="font-size: 13px; color: var(--text-muted); margin-bottom: 20px;">* Sonuçlar ön fizibilite amacıyla yaklaşık katsayılarla hesaplanır; uygulama metrajı değildir. Türkiye mimari katsayılarına göre temel inşaat malzemelerini saniyeler içinde miktar olarak görün.</p>
                    
                    <div class="form-group">
                        <label class="form-label">Toplam İnşaat Alanı (m²)</label>
                        <input type="number" id="calcArea" class="form-control" placeholder="Örneğin: 1500" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">İmalat Cinsi (Bina Tipi)</label>
                        <select id="calcType" class="form-control" style="border: 2px solid var(--yellow); font-weight: 700;">
                            <option value="betonarme">Betonarme Karkas Bina (Standart)</option>
                            <option value="yigma">Yığma Bina (Tuğla/Kagir)</option>
                        </select>
                    </div>
                    <button type="button" onclick="calculateMetraj()" class="btn-submit" style="background: #10b981; color: #fff; margin-bottom: 10px;">📐 HIZLI METRAJ HESAPLA</button>
                    
                    <div id="metrajResultBox" class="metraj-result-box">
                        <table class="metraj-table">
                            <thead><tr><th colspan="2" style="background:#0f172a;">🏗️ KABA İNŞAAT METRAJI</th></tr></thead>
                            <tbody>
                                <tr><td>Betonarme Betonu</td><td style="text-align: right;"><span id="res_beton">0</span> m³</td></tr>
                                <tr><td>Betonarme Demiri (Rebar)</td><td style="text-align: right;"><span id="res_demir">0</span> Ton</td></tr>
                                <tr><td>İnşaat Kalıbı</td><td style="text-align: right;"><span id="res_kalip">0</span> m²</td></tr>
                                <tr><td>Duvar İmalatı</td><td style="text-align: right;"><span id="res_tugla">0</span> m³</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>

            <!-- SAĞ PANEL: NASIL ÇALIŞIR & SATIŞ HUNİSİ -->
            <div class="right-panel">
                <div class="funnel-header">KAİNTA AI PROJE ASİSTANI</div>
                <div class="funnel-sub">
                    Kainta İnşaat'ın geliştirdiği dijital proje analiz sistemiyle inşaat ve tadilat projenizin yaklaşık maliyetini hesaplayın.<br><br>
                    <strong>Not:</strong> <em>Worksitem altyapısı, Kainta İnşaat'ın keşif, maliyet analizi ve teklif süreçlerini dijitalleştirmek için kullanılmaktadır.</em>
                </div>

                <div class="process-list">
                    <div class="process-item">
                        <div class="process-num">01</div>
                        <div class="process-content">
                            <strong>Projenizi Tanımlayın</strong>
                            <p>Arsa, inşaat alanı, yapı sistemi, kalite ve proje özelliklerini girin.</p>
                        </div>
                    </div>
                    <div class="process-item">
                        <div class="process-num">02</div>
                        <div class="process-content">
                            <strong>Ön Maliyeti Hesaplayın</strong>
                            <p>Sistem verdiğiniz bilgilere göre yaklaşık maliyet analizi oluşturur.</p>
                        </div>
                    </div>
                    <div class="process-item">
                        <div class="process-num">03</div>
                        <div class="process-content">
                            <strong>Kainta İnşaat'tan Keşif Alın</strong>
                            <p>Projeyi gerçek saha ve teknik koşullarıyla değerlendirelim.</p>
                        </div>
                    </div>
                    <div class="process-item">
                        <div class="process-num">04</div>
                        <div class="process-content">
                            <strong>Anahtar Teslim Teklifinizi Alın</strong>
                            <p>Kapsam netleştirildikten sonra Kainta İnşaat tarafından bağlayıcı teklif hazırlanır.</p>
                        </div>
                    </div>
                </div>

                <div class="keşif-box" id="iletisim">
                    <div class="keşif-box-title">💼 KESİN TEKLİF İÇİN KAİNTA PROFESYONEL KEŞİF</div>
                    <ul class="keşif-list">
                        <li>Saha İncelemesi & Ölçüm</li>
                        <li>Detaylı Metraj Çıkarılması</li>
                        <li>Teknik Değerlendirme</li>
                        <li>Malzeme & Kalite Analizi</li>
                        <li>Mimari Proje İncelemesi</li>
                        <li>Kapsamlı Teklif Hazırlama</li>
                    </ul>
                    <button type="button" onclick="document.getElementById('leadModalIndex').style.display='flex'" class="btn-main" style="width:100%; font-size:18px; padding:12px; display:inline-block; text-align:center;">Kainta İnşaat'tan Keşif Talep Et →</button>
                </div>
            </div>

        </div>
    </div>

        <!-- BAŞARI MESAJI BİLDİRİMİ -->
    @if(session('success'))
        <div style="position: fixed; top: 20px; right: 20px; background: rgba(16, 185, 129, 0.95); border: 1px solid var(--green); color: #fff; padding: 20px; border-radius: 12px; z-index: 10001; font-weight: 600; box-shadow: 0 10px 25px rgba(16, 185, 129, 0.3); animation: slideInRight 0.5s ease-out;">
            ✅ {{ session('success') }}
            <button onclick="this.parentElement.style.display='none'" style="float: right; background: none; border: none; color: white; font-size: 20px; cursor: pointer; margin-left: 15px;">&times;</button>
        </div>
        <style>@keyframes slideInRight { from { transform: translateX(100%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }</style>
    @endif

    <!-- ANA SAYFA LEAD (KEŞİF TALEBİ) MODAL FORMU -->
    <div id="leadModalIndex" style="position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.8); z-index:9999; display:none; align-items:center; justify-content:center; backdrop-filter: blur(5px);">
        <div style="background:var(--card-bg); border:1px solid var(--border); border-radius:16px; width:90%; max-width:450px; padding:30px; position:relative; box-shadow:0 25px 50px rgba(0,0,0,0.5);">
            <button type="button" onclick="document.getElementById('leadModalIndex').style.display='none'" style="position:absolute; right:20px; top:20px; background:none; border:none; color:var(--muted); font-size:24px; cursor:pointer;">&times;</button>
            
            <div style="font-family:'Bebas Neue'; font-size:28px; color:var(--yellow); margin-bottom:10px; letter-spacing:1px;">PROFESYONEL KEŞİF TALEBİ</div>
            <p style="font-size:13px; color:var(--muted); margin-bottom:25px; line-height:1.5;">Uzman ekibimizin projenizi incelemesi ve size özel çözümler sunması için iletişim bilgilerinizi bırakın.</p>
            
            <form action="{{ route('leads.store') }}" method="POST">
                @csrf
                <div style="margin-bottom:15px;">
                    <label style="display:block; font-size:12px; font-weight:700; color:var(--muted); margin-bottom:6px; text-transform:uppercase;">Proje Tipi *</label>
                    <select name="project_type" required style="width:100%; padding:14px; border-radius:8px; background:var(--input-bg); border:1px solid var(--border); color:white; font-family:inherit;">
                        <option value="yeni_insaat">Yeni İnşaat (Villa, Bina, Fabrika)</option>
                        <option value="tadilat">Tadilat ve Renovasyon</option>
                        <option value="diger">Diğer / Emin Değilim</option>
                    </select>
                </div>
                
                <div style="margin-bottom:15px;">
                    <label style="display:block; font-size:12px; font-weight:700; color:var(--muted); margin-bottom:6px; text-transform:uppercase;">Ad Soyad *</label>
                    <input type="text" name="name" required style="width:100%; padding:14px; border-radius:8px; background:var(--input-bg); border:1px solid var(--border); color:white; font-family:inherit;">
                </div>
                
                <div style="margin-bottom:15px;">
                    <label style="display:block; font-size:12px; font-weight:700; color:var(--muted); margin-bottom:6px; text-transform:uppercase;">Telefon Numaranız *</label>
                    <input type="tel" name="phone" required placeholder="05XX XXX XX XX" style="width:100%; padding:14px; border-radius:8px; background:var(--input-bg); border:1px solid var(--border); color:white; font-family:inherit;">
                </div>
                
                <div style="margin-bottom:25px;">
                    <label style="display:block; font-size:12px; font-weight:700; color:var(--muted); margin-bottom:6px; text-transform:uppercase;">Ek Notlarınız (Opsiyonel)</label>
                    <textarea name="notes" rows="3" placeholder="Projeniz hakkında kısa bilgi..." style="width:100%; padding:14px; border-radius:8px; background:var(--input-bg); border:1px solid var(--border); color:white; font-family:inherit; resize:vertical;"></textarea>
                </div>
                
                <button type="submit" style="width:100%; background:var(--green); color:white; font-family:'Bebas Neue'; font-size:22px; letter-spacing:1px; padding:16px; border:none; border-radius:8px; cursor:pointer; transition:0.3s;">ÜCRETSİZ KEŞİF İSTE →</button>
            </form>
        </div>
    </div>

    <!-- BRAND STATEMENT -->
    <div class="brand-statement-box">
        "Sadece inşaat teklifi vermiyoruz; <span>projenizi önce analiz ediyor</span>, maliyetini çıkarıyor ve <span>anahtar teslim uygulamaya</span> hazırlıyoruz."
    </div>

    <!-- FOOTER -->
    <footer class="site-footer">
        <div>© 2026 Kainta İnşaat | Worksitem AI Asistanı Ortaklığıyla Sunulmaktadır.</div>
        <div class="footer-links">
            <a onclick="openModal('modal-privacy')">Gizlilik Politikası</a>
            <a onclick="openModal('modal-terms')">Şartlar</a>
            <a onclick="openModal('modal-faq')">SSS</a>
        </div>
    <!-- 40+ CONTECH HESAPLAMA ARAÇLARI MERKEZİ -->
<div style="max-width:1200px; margin:60px auto; padding:0 20px;">
    <div style="text-align:center; margin-bottom:40px;">
        <span style="background:#eff6ff; color:#2563eb; font-weight:800; font-size:0.8rem; padding:6px 14px; border-radius:9999px; text-transform:uppercase;">UZMAN MÜHENDİSLİK ARAÇLARI</span>
        <h2 style="font-size:2rem; font-weight:800; color:#0f172a; margin:10px 0 6px 0;">40+ Profesyonel İnşaat ve Metraj Hesaplayıcı</h2>
        <p style="color:#64748b; font-size:1rem;">Projenizin tüm teknik kalemlerini 2026 ÇŞB rayiçleriyle ücretsiz hesaplayın.</p>
    </div>

    <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(260px, 1fr)); gap:20px;">
        <a href="/araclar/boya-hesaplayici" style="background:#fff; border:1px solid #e2e8f0; border-radius:12px; padding:20px; text-decoration:none; color:#0f172a; transition:all 0.2s;" onmouseover="this.style.borderColor='#2563eb'">
            <div style="font-size:1.5rem; margin-bottom:8px;">🎨</div>
            <strong style="display:block; font-size:1rem; margin-bottom:4px;">Boya Maliyet Hesabı</strong>
            <span style="font-size:0.85rem; color:#64748b;">m² alan ve litre sarfiyatı</span>
        </a>
        <a href="/araclar/karo-hesaplayici" style="background:#fff; border:1px solid #e2e8f0; border-radius:12px; padding:20px; text-decoration:none; color:#0f172a; transition:all 0.2s;" onmouseover="this.style.borderColor='#2563eb'">
            <div style="font-size:1.5rem; margin-bottom:8px;">🧩</div>
            <strong style="display:block; font-size:1rem; margin-bottom:4px;">Seramik & Karo Hesabı</strong>
            <span style="font-size:0.85rem; color:#64748b;">Kutu sayısı ve derz dolgusu</span>
        </a>
        <a href="/araclar/banyo-maliyet-hesaplayici" style="background:#fff; border:1px solid #e2e8f0; border-radius:12px; padding:20px; text-decoration:none; color:#0f172a; transition:all 0.2s;" onmouseover="this.style.borderColor='#2563eb'">
            <div style="font-size:1.5rem; margin-bottom:8px;">🚿</div>
            <strong style="display:block; font-size:1rem; margin-bottom:4px;">Banyo Yenileme</strong>
            <span style="font-size:0.85rem; color:#64748b;">Yalıtım ve tesisat dahil</span>
        </a>
        <a href="/araclar/mutfak-maliyet-hesaplayici" style="background:#fff; border:1px solid #e2e8f0; border-radius:12px; padding:20px; text-decoration:none; color:#0f172a; transition:all 0.2s;" onmouseover="this.style.borderColor='#2563eb'">
            <div style="font-size:1.5rem; margin-bottom:8px;">🍳</div>
            <strong style="display:block; font-size:1rem; margin-bottom:4px;">Mutfak Tadilatı</strong>
            <span style="font-size:0.85rem; color:#64748b;">Dolap ve tezgah bütçesi</span>
        </a>
        <a href="/araclar/demir-metraji-hesaplayici" style="background:#fff; border:1px solid #e2e8f0; border-radius:12px; padding:20px; text-decoration:none; color:#0f172a; transition:all 0.2s;" onmouseover="this.style.borderColor='#2563eb'">
            <div style="font-size:1.5rem; margin-bottom:8px;">🔗</div>
            <strong style="display:block; font-size:1rem; margin-bottom:4px;">İnşaat Demiri Metrajı</strong>
            <span style="font-size:0.85rem; color:#64748b;">Donatı tonajı ve borsa fiyatı</span>
        </a>
        <a href="/araclar/duvar-rengi-gorsellestirici" style="background:#fff; border:1px solid #e2e8f0; border-radius:12px; padding:20px; text-decoration:none; color:#0f172a; transition:all 0.2s;" onmouseover="this.style.borderColor='#2563eb'">
            <div style="font-size:1.5rem; margin-bottom:8px;">📸</div>
            <strong style="display:block; font-size:1rem; margin-bottom:4px;">AI Duvar Renklendirici</strong>
            <span style="font-size:0.85rem; color:#64748b;">Oda fotoğrafı üzerinde boyama</span>
        </a>
        <a href="/araclar/belge-olusturucu" style="background:#fff; border:1px solid #e2e8f0; border-radius:12px; padding:20px; text-decoration:none; color:#0f172a; transition:all 0.2s;" onmouseover="this.style.borderColor='#2563eb'">
            <div style="font-size:1.5rem; margin-bottom:8px;">📑</div>
            <strong style="display:block; font-size:1rem; margin-bottom:4px;">Sözleşme Sihirbazı (PDF)</strong>
            <span style="font-size:0.85rem; color:#64748b;">Resmi A4 taşeron sözleşmesi</span>
        </a>
        <a href="/araclar" style="background:#eff6ff; border:1px solid #bfdbfe; border-radius:12px; padding:20px; text-decoration:none; color:#2563eb; display:flex; flex-direction:column; justify-content:center; align-items:center; text-align:center;">
            <strong style="font-size:1.1rem; margin-bottom:4px;">Tüm Araçları İncele →</strong>
            <span style="font-size:0.85rem;">40+ Mühendislik Hesaplayıcı</span>
        </a>
    </div>
</div>
</footer>

    <!-- MODALLAR -->
    <div class="modal-overlay" id="modal-privacy"><div class="modal-box"><div class="modal-title">🔒 GİZLİLİK POLİTİKASI</div><div class="modal-content"><p>Girdiğiniz veriler yalnızca maliyet tahmini oluşturmak için kullanılır. Verileriniz 3. taraflarla asla paylaşılmaz.</p></div><button class="modal-close-btn" onclick="closeModal('modal-privacy')">KAPAT</button></div></div>
    <div class="modal-overlay" id="modal-terms"><div class="modal-box"><div class="modal-title">📋 HİZMET ŞARTLARI</div><div class="modal-content"><p>Burada sunulan maliyet tahminleri bilgilendirme amaçlıdır. Kesin maliyetler için Kainta İnşaat profesyonel mimar ve mühendis kadrosuyla yerinde keşif yapılması gerekmektedir.</p></div><button class="modal-close-btn" onclick="closeModal('modal-terms')">KAPAT</button></div></div>
    <div class="modal-overlay" id="modal-faq"><div class="modal-box"><div class="modal-title">❓ SIKÇA SORULAN SORULAR</div><div class="modal-content"><p>Bu sistem ön fizibilite oluşturmak amacıyla çalışır. Tüm rakamlar yaklaşiktır.</p></div><button class="modal-close-btn" onclick="closeModal('modal-faq')">KAPAT</button></div></div>

    <!-- JAVASCRIPT MOTORU -->
    <script>
        function switchTab(tabId) {
            document.querySelectorAll('.form-tab-btn').forEach(btn => btn.classList.remove('active'));
            document.querySelectorAll('.form-section').forEach(sec => sec.classList.remove('active'));
            document.getElementById('btn-tab-' + tabId).classList.add('active');
            document.getElementById('section-' + tabId).classList.add('active');
            document.getElementById('tahminci').scrollIntoView({ behavior: 'smooth' });
        }

        function selectQuality(elem, val) {
            document.querySelectorAll('#section-insaat .quality-card').forEach(c => c.classList.remove('active'));
            elem.classList.add('active');
            document.getElementById('qualityInput').value = val;
        }

        function selectRenovationQuality(elem, val) {
            document.querySelectorAll('#section-tadilat .quality-card').forEach(c => c.classList.remove('active'));
            elem.classList.add('active');
            document.getElementById('renovationQualityInput').value = val;
        }

        function handleFileSelect(input, labelId) {
            if (input.files && input.files[0]) {
                const fileName = input.files[0].name;
                const label = document.getElementById(labelId);
                const card = input.closest('.upload-card');
                label.innerHTML = "✅ " + (fileName.length > 18 ? fileName.substring(0, 15) + "..." : fileName);
                label.style.color = "var(--green)";
                card.classList.add('has-file');
            }
        }

        function updateDistricts() {
            const city = document.getElementById('citySelect').value;
            const districtSelect = document.getElementById('districtSelect');
            districtSelect.innerHTML = '';

            if (city === 'istanbul') {
                districtSelect.innerHTML = `<option value="kadikoy">Kadıköy, Beşiktaş (Lüks)</option><option value="umraniye">Ümraniye (A Sınıfı)</option><option value="silivri">Silivri (Kırsal)</option>`;
            } else if (city === 'ankara') {
                districtSelect.innerHTML = `<option value="cankaya">Çankaya, Gölbaşı (Lüks)</option><option value="etimesgut">Etimesgut (A)</option>`;
            } else if (city === 'izmir') {
                districtSelect.innerHTML = `<option value="cesme">Çeşme, Alaçatı (Lüks)</option><option value="karsiyaka">Karşıyaka (A)</option>`;
            } else {
                districtSelect.innerHTML = `<option value="merkez">İl Merkezi</option><option value="ilce">İlçe / Kırsal</option>`;
            }
        }
        setTimeout(updateDistricts, 500);

        function toggleCsbInfo() {
            const details = document.getElementById('csbDetails');
            const arrow = document.getElementById('csbArrow');
            if (details.style.display === 'block') { details.style.display = 'none'; arrow.innerHTML = '▼'; }
            else { details.style.display = 'block'; arrow.innerHTML = '▲'; }
        }

                function calculateLive() {
            let area = parseFloat(document.getElementById('live_area').value) || 0;
            let scope = document.getElementById('live_scope').value;
            let quality = document.getElementById('live_quality').value;
            let city = document.getElementById('live_city').value;
            let prop = document.getElementById('live_prop').options[document.getElementById('live_prop').selectedIndex].text;
            
            // Seçilen odaları topla
            let selectedRooms = Array.from(document.querySelectorAll('input[name="rooms[]"]:checked')).map(cb => cb.value);
            
            // Basit Fiyat Motoru (TL)
            let basePrice = 0;
            if(scope === 'hafif') basePrice = 3500;
            if(scope === 'orta') basePrice = 7500;
            if(scope === 'komple') basePrice = 12500;

            let qualityMult = (quality === 'temel') ? 1.0 : ((quality === 'orta') ? 1.3 : 1.8);
            let cityMult = (city === 'merkez') ? 1.35 : ((city === 'orta') ? 1.10 : ((city === 'dis') ? 0.95 : 0.85));

            let totalAvg = area * basePrice * qualityMult * cityMult;
            let minPrice = totalAvg * 0.80;
            let maxPrice = totalAvg * 1.20;

            // DOM Güncelle
            if(area > 0) {
                document.getElementById('live_price_range').innerText = 
                    new Intl.NumberFormat('tr-TR').format(Math.round(minPrice)) + " - " + 
                    new Intl.NumberFormat('tr-TR').format(Math.round(maxPrice)) + " TL";
                document.getElementById('live_price_avg').innerText = new Intl.NumberFormat('tr-TR').format(Math.round(totalAvg));
            } else {
                document.getElementById('live_price_range').innerText = "0 - 0 TL";
                document.getElementById('live_price_avg').innerText = "0";
            }

            document.getElementById('lbl_prop').innerText = prop;
            document.getElementById('lbl_area').innerText = area + " m²";
            document.getElementById('lbl_scope').innerText = document.getElementById('live_scope').options[document.getElementById('live_scope').selectedIndex].text;
            document.getElementById('lbl_rooms').innerText = selectedRooms.length > 0 ? selectedRooms.join(', ') : 'Seçilmedi';
        }

        // Tab değiştirirken Canlı Kutuyu göster/gizle ve kalite değişimini yakala
        const originalSwitchTab = switchTab;
        switchTab = function(tabId) {
            originalSwitchTab(tabId);
            document.getElementById('liveResultBox').style.display = (tabId === 'tadilat') ? 'block' : 'none';
            document.getElementById('funnelBox').style.display = (tabId === 'tadilat') ? 'none' : 'block';
        };

        const originalSelectRenovationQuality = selectRenovationQuality;
        selectRenovationQuality = function(elem, val) {
            originalSelectRenovationQuality(elem, val);
            document.getElementById('live_quality').value = val;
            calculateLive();
        };

        function calculateMetraj() {
            const area = parseFloat(document.getElementById('calcArea').value);
            const type = document.getElementById('calcType').value;
            if (!area || area <= 0) { alert("Lütfen geçerli bir metrekare (m²) alanı giriniz."); return; }
            
            const coeffs = {
                'betonarme': { beton: 0.380, demir: 34, kalip: 2.60, tugla: 0.150 },
                'yigma': { beton: 0.250, demir: 22, kalip: 1.75, tugla: 0.200 }
            };
            const sel = coeffs[type];
            
            document.getElementById('res_beton').innerText = (area * sel.beton).toLocaleString('tr-TR', {maximumFractionDigits:1});
            document.getElementById('res_demir').innerText = ((area * sel.demir) / 1000).toLocaleString('tr-TR', {maximumFractionDigits:2});
            document.getElementById('res_kalip').innerText = (area * sel.kalip).toLocaleString('tr-TR', {maximumFractionDigits:1});
            document.getElementById('res_tugla').innerText = (area * sel.tugla).toLocaleString('tr-TR', {maximumFractionDigits:1});

            document.getElementById('metrajResultBox').style.display = 'block';
        }

        function openModal(id) { document.getElementById(id).classList.add('active'); }
        function closeModal(id) { document.getElementById(id).classList.remove('active'); }
    </script>
<x-lead-modal />
</body>
</html>
