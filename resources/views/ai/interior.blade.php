<!DOCTYPE html>
<html lang="tr">
<head>
    <link rel="icon" href="/favicon.ico" type="image/x-icon">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>AI İç Mekan Tasarımı: Odanın Fotoğrafını Yükle, Yeni Halini Gör | WORKSİTEM</title>
    <meta name="description" content="Odanızın fotoğrafını yükleyin, yapay zekâ ile modern, minimalist veya klasik stilde yeni tasarımını saniyeler içinde görün ve tahmini tadilat bütçesini öğrenin. Ücretsiz.">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #f5f6f8; --card-bg: #ffffff; --border: #e5e7eb;
            --yellow: #ffb800; --text: #101318; --muted: #5b6472;
            --cyan: #8a6200; --green: #10b981; --input-bg: #ffffff;
        }
        :root[data-theme="dark"] {
            --bg: #0b0f17; --card-bg: #151c28; --border: #1e293b;
            --yellow: #ffb800; --text: #f1f5f9; --muted: #94a3b8;
            --cyan: #ffb800; --green: #10b981; --input-bg: #0d1117;
        }
        * { transition: background-color 0.3s, color 0.3s, border-color 0.3s; }
        body { font-family: 'Inter', sans-serif; background: var(--bg); color: var(--text); margin: 0; padding: 0; }
        .container { max-width: 1000px; margin: 40px auto; padding: 0 4%; }
        h1 { font-family:'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; font-size: 42px; color: var(--text); margin-bottom: 10px; text-align: center; }
        .subtitle { text-align: center; color: var(--muted); margin-bottom: 40px; }

        .topbar { padding: 15px 4%; border-bottom: 1px solid var(--border); background: var(--card-bg); display: flex; justify-content: space-between; align-items: center; }
        .topbar a { color: var(--text); text-decoration: none; font-family:'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; font-size: 24px; }

        .theme-toggle {
            background: var(--input-bg); border: 1px solid var(--border); color: var(--text);
            padding: 8px 16px; border-radius: 100px; cursor: pointer; font-size: 14px;
            display: flex; align-items: center; gap: 8px; font-weight: 500;
        }
        .theme-toggle:hover { border-color: var(--cyan); }

        .upload-area { display: block; background: var(--input-bg); border: 2px dashed var(--border); border-radius: 12px; padding: 40px; text-align: center; cursor: pointer; transition: 0.3s; margin-bottom: 20px; }
        .upload-area:hover { border-color: #ffb800; background: rgba(255,184,0,0.06); }
        .upload-area input { display: none; }

        .style-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px; margin-bottom: 25px; }
        @media(max-width: 768px) { .style-grid { grid-template-columns: 1fr 1fr; } }
        .style-card { border: 1px solid var(--border); background: var(--card-bg); padding: 15px; border-radius: 8px; text-align: center; cursor: pointer; transition: 0.2s; }
        .style-card:hover { border-color: var(--yellow); }
        .style-card.active { border-color: var(--yellow); box-shadow: 0 0 15px rgba(255,183,0,0.2); background: rgba(255,183,0,0.05); }
        .style-card input { display: none; }

        .prompt-label { color: var(--text); font-size: 16px; margin-bottom: 10px; font-weight: 600; display: block; }
        .prompt-textarea {
            width: 100%; padding: 14px; border-radius: 8px; background: var(--input-bg); color: var(--text);
            border: 1px solid var(--border); font-size: 14px; box-sizing: border-box;
            resize: vertical; font-family: 'Inter', sans-serif; line-height: 1.5; min-height: 80px;
        }
        .prompt-textarea:focus { outline: none; border-color: var(--cyan); }
        .char-counter { color: var(--muted); font-size: 12px; margin-top: 5px; text-align: right; }

        .submit-btn { width: 100%; background: #ffb800; color: #101318; font-weight: 800; font-family:inherit; font-size: 17px; padding: 16px; border: none; border-radius: 8px; cursor: pointer; transition: 0.3s; margin-top: 20px; }
        .submit-btn:hover { background: #e5a500; }
        .submit-btn:disabled { opacity: 0.5; cursor: not-allowed; }

        .result-box { background: var(--card-bg); border: 1px solid var(--border); border-radius: 12px; padding: 30px; margin-top: 40px; text-align: center; }
        .result-img { max-width: 100%; border-radius: 8px; border: 2px solid var(--border); margin-bottom: 20px; }
        .cta-btn { display: inline-block; background: var(--green); color: #fff; font-family:'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; font-size: 22px; padding: 15px 30px; border-radius: 8px; text-decoration: none; }
        .error-box { background: rgba(239, 68, 68, 0.1); border: 1px solid #ef4444; color: #fca5a5; padding: 15px; border-radius: 8px; margin-top: 20px; font-size: 14px; }

        /* === YÜKLEME EKRANI === */
        .loading-box {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 50px 30px;
            margin-top: 40px;
            text-align: center;
            position: relative;
            overflow: hidden;
            min-height: 500px;
        }
        .loading-box::before {
            content: '';
            position: absolute;
            top: 0; left: -100%;
            width: 100%; height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,184,0,0.08), transparent);
            animation: shine 4s infinite;
        }
        @keyframes shine { 0% { left: -100%; } 100% { left: 100%; } }

        /* Sahne Alanı */
        .scene {
            position: relative;
            height: 200px;
            margin: 20px auto 30px;
            max-width: 500px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* SAHNE 1: MİMARİ ANALİZ */
        .scene-blueprint {
            position: absolute;
            inset: 0;
            background-image:
                linear-gradient(rgba(255,184,0,0.15) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,184,0,0.15) 1px, transparent 1px);
            background-size: 30px 30px;
            border-radius: 12px;
            animation: blueprint-pulse 2s infinite;
        }
        @keyframes blueprint-pulse {
            0%, 100% { opacity: 0.3; }
            50% { opacity: 0.6; }
        }
        .ruler {
            font-size: 90px;
            animation: ruler-spin 3s ease-in-out infinite;
            filter: drop-shadow(0 0 20px rgba(255,184,0,0.6));
            z-index: 2;
            position: relative;
        }
        @keyframes ruler-spin {
            0%, 100% { transform: rotate(-15deg) scale(1); }
            50% { transform: rotate(15deg) scale(1.1); }
        }
        .measure-line {
            position: absolute;
            height: 2px;
            background: linear-gradient(90deg, transparent, var(--cyan), transparent);
            animation: measure-scan 2s infinite;
        }
        .measure-line-1 { top: 20%; width: 60%; left: 20%; animation-delay: 0s; }
        .measure-line-2 { top: 50%; width: 80%; left: 10%; animation-delay: 0.5s; }
        .measure-line-3 { top: 80%; width: 50%; left: 25%; animation-delay: 1s; }
        @keyframes measure-scan {
            0% { transform: scaleX(0); opacity: 0; }
            50% { transform: scaleX(1); opacity: 1; }
            100% { transform: scaleX(0); opacity: 0; }
        }

        /* SAHNE 2: İNŞAAT */
        .scene-construction {
            position: absolute;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .crane-big {
            font-size: 100px;
            animation: crane-lift 3s ease-in-out infinite;
            filter: drop-shadow(0 0 25px rgba(255,183,0,0.5));
            z-index: 2;
        }
        @keyframes crane-lift {
            0%, 100% { transform: translateY(0) rotate(-3deg); }
            50% { transform: translateY(-20px) rotate(3deg); }
        }
        .brick-stack {
            position: absolute;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 4px;
            flex-wrap: wrap;
            width: 80px;
            justify-content: center;
            z-index: 1;
        }
        .brick {
            width: 20px;
            height: 10px;
            background: linear-gradient(135deg, #b45309, #78350f);
            border-radius: 2px;
            animation: brick-appear 2s infinite;
        }
        .brick:nth-child(1) { animation-delay: 0s; }
        .brick:nth-child(2) { animation-delay: 0.3s; }
        .brick:nth-child(3) { animation-delay: 0.6s; }
        .brick:nth-child(4) { animation-delay: 0.9s; }
        .brick:nth-child(5) { animation-delay: 1.2s; }
        .brick:nth-child(6) { animation-delay: 1.5s; }
        @keyframes brick-appear {
            0% { opacity: 0; transform: translateY(-20px); }
            50% { opacity: 1; transform: translateY(0); }
            100% { opacity: 0; transform: translateY(-20px); }
        }
        .worker-center {
            position: absolute;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            font-size: 60px;
            animation: worker-hammer 0.8s infinite;
            filter: drop-shadow(0 0 15px rgba(255,184,0,0.5));
            z-index: 3;
        }
        @keyframes worker-hammer {
            0%, 100% { transform: translateX(-50%) rotate(-5deg); }
            50% { transform: translateX(-50%) rotate(5deg); }
        }

        /* SAHNE 3: SON RÖTUŞ */
        .scene-polish {
            position: absolute;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .sparkle-main {
            font-size: 100px;
            animation: sparkle-pulse 1.5s infinite;
            filter: drop-shadow(0 0 30px rgba(255,183,0,0.7));
            z-index: 2;
        }
        @keyframes sparkle-pulse {
            0%, 100% { transform: scale(1) rotate(0deg); opacity: 1; }
            50% { transform: scale(1.3) rotate(15deg); opacity: 0.8; }
        }
        .sparkle-mini {
            position: absolute;
            font-size: 30px;
            animation: sparkle-float 2s infinite;
        }
        .sparkle-mini:nth-child(2) { top: 15%; left: 15%; animation-delay: 0s; }
        .sparkle-mini:nth-child(3) { top: 20%; right: 15%; animation-delay: 0.4s; }
        .sparkle-mini:nth-child(4) { bottom: 20%; left: 20%; animation-delay: 0.8s; }
        .sparkle-mini:nth-child(5) { bottom: 15%; right: 20%; animation-delay: 1.2s; }
        @keyframes sparkle-float {
            0%, 100% { transform: scale(0.5) translateY(0); opacity: 0; }
            50% { transform: scale(1.2) translateY(-10px); opacity: 1; }
        }

        .stage-title {
            font-family:'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            font-size: 28px;
            color: var(--cyan);
            margin: 15px 0 8px;
            letter-spacing: 1px;
        }
        .stage-subtitle {
            color: var(--muted);
            font-size: 14px;
            margin-bottom: 25px;
            min-height: 20px;
        }
        .time-estimate {
            display: inline-block;
            background: rgba(255, 183, 0, 0.1);
            border: 1px solid rgba(255, 183, 0, 0.3);
            border-radius: 100px;
            padding: 8px 20px;
            color: var(--yellow);
            font-size: 14px;
            font-weight: 600;
            margin-top: 15px;
        }
        .time-estimate strong { font-size: 16px; color: var(--yellow); }

        .progress-container {
            background: var(--input-bg);
            border-radius: 100px;
            height: 10px;
            overflow: hidden;
            margin: 25px auto;
            max-width: 500px;
            border: 1px solid var(--border);
        }
        .progress-bar {
            height: 100%;
            background: linear-gradient(90deg, var(--cyan), var(--yellow));
            border-radius: 100px;
            width: 0%;
            transition: width 0.5s ease-out;
            position: relative;
            overflow: hidden;
        }
        .progress-bar::after {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            animation: progress-shine 1.5s infinite;
        }
        @keyframes progress-shine {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }
        .progress-percent {
            color: var(--cyan);
            font-family:'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            font-size: 26px;
            margin-top: 8px;
            letter-spacing: 2px;
        }
        .stage-dots {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-top: 25px;
        }
        .stage-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: var(--border);
            transition: all 0.3s;
        }
        .stage-dot.active {
            background: var(--cyan);
            box-shadow: 0 0 15px var(--cyan);
            transform: scale(1.3);
        }
        .stage-dot.completed {
            background: var(--green);
        }
        .notify-btn {
            display: inline-block;
            background: transparent;
            border: 1px solid var(--border);
            color: var(--muted);
            padding: 10px 20px;
            border-radius: 100px;
            font-size: 13px;
            cursor: pointer;
            transition: 0.3s;
            margin-top: 20px;
        }
        .notify-btn:hover { border-color: var(--cyan); color: var(--cyan); }
        .notify-btn.enabled { border-color: var(--green); color: var(--green); }

        .floating-emoji {
            position: absolute;
            font-size: 24px;
            opacity: 0.15;
            animation: float 6s infinite ease-in-out;
        }
        @keyframes float {
            0%, 100% { transform: translateY(0) rotate(0deg); }
            50% { transform: translateY(-15px) rotate(10deg); }
        }
    </style>
</head>
<body>
    <x-site.contech-header />

    <div class="container">
        <h1>AI İç Mekan Tasarımcısı</h1>
        <p class="subtitle">Odanızın fotoğrafını yükleyin, hayalinizdeki tasarımı yazın, yapay zeka gerçeğe dönüştürsün.</p>

        <div id="errorContainer"></div>
        <div id="loadingContainer"></div>
        <div id="resultContainer"></div>

        <form id="aiForm" enctype="multipart/form-data">
            @csrf

            <label class="upload-area" id="dropzone">
                <input type="file" name="room_image" id="fileInput" accept="image/*" required>
                <div style="font-size:40px; margin-bottom:10px;">📸</div>
                <div style="color:var(--text); font-weight:600; font-size:16px;" id="fileName">Oda fotoğrafınızı buraya sürükleyin veya tıklayın</div>
                <div style="color:var(--muted); font-size:12px; margin-top:5px;">Max: 5MB (JPG, PNG)</div>
            </label>

            <h3 style="color:var(--text); font-size:16px; margin-bottom:15px; margin-top:25px;">Dekorasyon Stilini Seçin:</h3>
            <div class="style-grid">
                <label class="style-card active" onclick="selectStyle(this)">
                    <input type="radio" name="style" value="Modern" checked>
                    <div style="font-weight:700; color:var(--text);">Modern</div>
                    <div style="font-size:11px; color:var(--muted);">Temiz hatlar, ferah görünüm</div>
                </label>
                <label class="style-card" onclick="selectStyle(this)">
                    <input type="radio" name="style" value="Minimalist">
                    <div style="font-weight:700; color:var(--text);">Minimalist</div>
                    <div style="font-size:11px; color:var(--muted);">Az eşya, maksimum sadelik</div>
                </label>
                <label class="style-card" onclick="selectStyle(this)">
                    <input type="radio" name="style" value="Endüstriyel">
                    <div style="font-weight:700; color:var(--text);">Endüstriyel</div>
                    <div style="font-size:11px; color:var(--muted);">Tuğla, metal ve beton dokuları</div>
                </label>
                <label class="style-card" onclick="selectStyle(this)">
                    <input type="radio" name="style" value="Lüks">
                    <div style="font-weight:700; color:var(--text);">Premium Lüks</div>
                    <div style="font-size:11px; color:var(--muted);">Parlak yüzeyler, kaliteli detaylar</div>
                </label>
                <label class="style-card" onclick="selectStyle(this)">
                    <input type="radio" name="style" value="Japandi">
                    <div style="font-weight:700; color:var(--text);">Japandi</div>
                    <div style="font-size:11px; color:var(--muted);">Ahşap tonları ve Zen atmosferi</div>
                </label>
                <label class="style-card" onclick="selectStyle(this)">
                    <input type="radio" name="style" value="Klasik">
                    <div style="font-weight:700; color:var(--text);">Klasik (Vintage)</div>
                    <div style="font-size:11px; color:var(--muted);">Zarif ahşap işçiliği ve oymalar</div>
                </label>
            </div>

            <label class="prompt-label" for="userPrompt">📝 Odanız için ne istiyorsunuz? (opsiyonel)</label>
            <textarea name="user_prompt" id="userPrompt" class="prompt-textarea" maxlength="300" rows="4"
                placeholder="Örn: Kitap rafı ekle, yeşil bitkiler koy, sıcak ve samimi bir atmosfer olsun, duvarlar krem rengi olsun..."></textarea>
            <div class="char-counter"><span id="charCount">0</span>/300 karakter</div>

            @if(config('services.ai_spend_enabled'))
            <button type="submit" class="submit-btn" id="btnSubmit">Tasarımı oluştur</button>
            @else
            <button type="button" class="submit-btn" id="btnSubmit" disabled
                    style="opacity:.55;cursor:not-allowed;">
                AI tasarım geçici olarak kapalı
            </button>
            <div style="margin-top:12px;text-align:center;color:var(--muted);font-size:13px;line-height:1.5;">
                Görsel üretim özelliği şu anda kullanıma kapalıdır.
            </div>
            @endif
        </form>
    </div>

    <script>
        // Tema: site geneli header bileşeni yönetir (localStorage: worksitem-theme)

        function selectStyle(element) {
            document.querySelectorAll('.style-grid .style-card').forEach(el => el.classList.remove('active'));
            element.classList.add('active');
        }

        const fileInput = document.getElementById('fileInput');
        if (fileInput) {
            fileInput.addEventListener('change', function() {
                if (this.files && this.files[0]) {
                    document.getElementById('fileName').innerText = "Seçildi: " + this.files[0].name;
                    document.getElementById('dropzone').style.borderColor = 'var(--cyan)';
                }
            });
        }

        const userPromptInput = document.getElementById('userPrompt');
        const charCount = document.getElementById('charCount');
        if (userPromptInput && charCount) {
            userPromptInput.addEventListener('input', function() {
                charCount.textContent = this.value.length;
            });
        }

        const aiForm = document.getElementById('aiForm');
        const btnSubmit = document.getElementById('btnSubmit');
        const errorContainer = document.getElementById('errorContainer');
        const loadingContainer = document.getElementById('loadingContainer');
        const resultContainer = document.getElementById('resultContainer');

        const STAGES = [
            { scene: 'blueprint', title: 'ODANIZ ANALİZ EDİLİYOR', subtitle: 'Mimari çizgiler ve ölçüler tespit ediliyor...', duration: 8 },
            { scene: 'construction', title: 'TASARIM OLUŞTURULUYOR', subtitle: 'Yapay zeka mobilya, renk ve ışık seçiyor...', duration: 12 },
            { scene: 'polish', title: 'SON RÖTUŞLAR YAPILIYOR', subtitle: 'Tasarımınız neredeyse hazır...', duration: 10 }
        ];

        let stageTimer = null;
        let progressTimer = null;
        let timeEstimateTimer = null;
        let notificationEnabled = false;
        window.currentGenerationId = null;
        let remainingQuota = 3;

        function setScene(sceneName) {
            const container = document.getElementById('sceneContainer');
            if (!container) return;

            if (sceneName === 'blueprint') {
                container.innerHTML = `
                    <div class="scene-blueprint"></div>
                    <div class="measure-line measure-line-1"></div>
                    <div class="measure-line measure-line-2"></div>
                    <div class="measure-line measure-line-3"></div>
                    <div class="ruler">📐</div>
                `;
            } else if (sceneName === 'construction') {
                container.innerHTML = `
                    <div class="scene-construction">
                        <div class="crane-big">🏗️</div>
                        <div class="brick-stack">
                            <div class="brick"></div><div class="brick"></div>
                            <div class="brick"></div><div class="brick"></div>
                            <div class="brick"></div><div class="brick"></div>
                        </div>
                        <div class="worker-center">👷</div>
                    </div>
                `;
            } else if (sceneName === 'polish') {
                container.innerHTML = `
                    <div class="scene-polish">
                        <div class="sparkle-main">✨</div>
                        <div class="sparkle-mini">✨</div>
                        <div class="sparkle-mini">⭐</div>
                        <div class="sparkle-mini">💫</div>
                        <div class="sparkle-mini">🌟</div>
                    </div>
                `;
            }
        }

        function showLoadingAnimation() {
            loadingContainer.innerHTML = `
                <div class="loading-box">
                    <span class="floating-emoji" style="top: 8%; left: 8%;">📐</span>
                    <span class="floating-emoji" style="top: 12%; right: 10%; animation-delay: 1s;">🎨</span>
                    <span class="floating-emoji" style="bottom: 20%; left: 12%; animation-delay: 2s;">🔨</span>
                    <span class="floating-emoji" style="bottom: 15%; right: 8%; animation-delay: 1.5s;">📏</span>

                    <div class="scene" id="sceneContainer"></div>

                    <h2 class="stage-title" id="stageTitle">ODANIZ ANALİZ EDİLİYOR</h2>
                    <p class="stage-subtitle" id="stageSubtitle">Mimari çizgiler ve ölçüler tespit ediliyor...</p>

                    <div class="time-estimate" id="timeEstimate">
                        Tahmini süre: <strong id="timeCountdown">~30 saniye</strong>
                    </div>

                    <div class="progress-container">
                        <div class="progress-bar" id="progressBar"></div>
                    </div>
                    <div class="progress-percent" id="progressPercent">0%</div>

                    <div class="stage-dots">
                        <div class="stage-dot active" id="dot0"></div>
                        <div class="stage-dot" id="dot1"></div>
                        <div class="stage-dot" id="dot2"></div>
                    </div>

                    <button class="notify-btn" id="notifyBtn" onclick="toggleNotifications()">
                        🔔 Hazır olduğunda bildir
                    </button>
                </div>
            `;
            setScene('blueprint');
            startStageAnimations();
            startTimeEstimate();
        }

        function startStageAnimations() {
            let currentStage = 0;
            let progress = 0;
            const totalDuration = 30;
            const tickInterval = 300;
            const totalTicks = (totalDuration * 1000) / tickInterval;
            const progressPerTick = 95 / totalTicks;

            progressTimer = setInterval(() => {
                progress = Math.min(95, progress + progressPerTick);
                updateProgress(progress);
            }, tickInterval);

            function nextStage() {
                if (currentStage >= STAGES.length) return;
                const stage = STAGES[currentStage];
                setScene(stage.scene);

                const titleEl = document.getElementById('stageTitle');
                const subtitleEl = document.getElementById('stageSubtitle');

                if (titleEl && subtitleEl) {
                    titleEl.innerText = stage.title;
                    subtitleEl.innerText = stage.subtitle;
                    document.querySelectorAll('.stage-dot').forEach((dot, i) => {
                        dot.classList.remove('active');
                        if (i < currentStage) dot.classList.add('completed');
                        if (i === currentStage) dot.classList.add('active');
                    });
                }
                currentStage++;
                if (currentStage < STAGES.length) {
                    stageTimer = setTimeout(nextStage, stage.duration * 1000);
                }
            }
            nextStage();
        }

        function startTimeEstimate() {
            let seconds = 30;
            const el = document.getElementById('timeCountdown');
            if (!el) return;
            timeEstimateTimer = setInterval(() => {
                seconds--;
                if (seconds <= 0) {
                    clearInterval(timeEstimateTimer);
                    if (el) el.innerText = 'Son rötuşlar...';
                    return;
                }
                if (el) el.innerText = `~${seconds} saniye`;
            }, 1000);
        }

        function updateProgress(percent) {
            const bar = document.getElementById('progressBar');
            const pct = document.getElementById('progressPercent');
            if (bar) bar.style.width = percent + '%';
            if (pct) pct.innerText = Math.round(percent) + '%';
        }

        function stopLoadingAnimation(success = true) {
            if (stageTimer) clearTimeout(stageTimer);
            if (progressTimer) clearInterval(progressTimer);
            if (timeEstimateTimer) clearInterval(timeEstimateTimer);
            if (success) updateProgress(100);
        }

        function toggleNotifications() {
            if (!('Notification' in window)) { alert('Tarayıcınız bildirimleri desteklemiyor.'); return; }
            if (Notification.permission === 'granted') {
                notificationEnabled = true;
                const btn = document.getElementById('notifyBtn');
                if (btn) { btn.classList.add('enabled'); btn.innerHTML = '🔔 Bildirim Açık ✓'; }
                return;
            }
            Notification.requestPermission().then(permission => {
                if (permission === 'granted') {
                    notificationEnabled = true;
                    const btn = document.getElementById('notifyBtn');
                    if (btn) { btn.classList.add('enabled'); btn.innerHTML = '🔔 Bildirim Açık ✓'; }
                }
            });
        }

        function sendNotification(title, body) {
            if (!notificationEnabled) return;
            if (Notification.permission !== 'granted') return;
            new Notification(title, { body: body, icon: '/favicon.ico', badge: '/favicon.ico' });
        }

        aiForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            errorContainer.innerHTML = '';
            resultContainer.innerHTML = '';
            loadingContainer.innerHTML = '';
            btnSubmit.disabled = true;
            btnSubmit.innerText = 'Tasarlanıyor…';
            showLoadingAnimation();

            try {
                const formData = new FormData(aiForm);
                const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

                const startRes = await fetch('/ai-tasarim/ic-mekan/start', {
                    method: 'POST',
                    body: formData,
                    headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                });
                const startData = await startRes.json();

                if (!startRes.ok || !startData.success) {
                    throw new Error(startData.message || 'Tasarım başlatılamadı.');
                }

                const generationId = startData.generation_id;
                window.window.currentGenerationId = generationId;
                let attempts = 0;
                const maxAttempts = 60;

                const pollInterval = setInterval(async () => {
                    attempts++;
                    try {
                        const statusRes = await fetch(`/ai-tasarim/ic-mekan/status/${generationId}`, {
                            headers: { 'Accept': 'application/json' },
                        });
                        const statusData = await statusRes.json();

                        if (statusData.status === 'succeeded' && statusData.output_url) {
                            clearInterval(pollInterval);
                            stopLoadingAnimation(true);
                            sendNotification('🎨 Tasarımınız Hazır!', 'Worksitem AI iç mekan tasarımınız hazır.');
                            setTimeout(() => showResult(statusData.output_url), 500);
                            btnSubmit.disabled = false;
                            remainingQuota = Math.max(0, remainingQuota - 1);
                            btnSubmit.innerText = 'Yeni tasarım oluştur';
                        } else if (statusData.status === 'failed') {
                            clearInterval(pollInterval);
                            stopLoadingAnimation(false);
                            showError(statusData.message || 'Tasarım başarısız oldu.');
                            btnSubmit.disabled = false;
                            remainingQuota = Math.max(0, remainingQuota - 1);
                            btnSubmit.innerText = 'Tekrar dene';
                        } else if (attempts >= maxAttempts) {
                            clearInterval(pollInterval);
                            stopLoadingAnimation(false);
                            showError('Zaman aşımı. Lütfen tekrar deneyin.');
                            btnSubmit.disabled = false;
                            remainingQuota = Math.max(0, remainingQuota - 1);
                            btnSubmit.innerText = 'Tekrar dene';
                        }
                    } catch (err) { console.error('Polling hatası:', err); }
                }, 3000);
            } catch (err) {
                stopLoadingAnimation(false);
                showError(err.message);
                btnSubmit.disabled = false;
                            remainingQuota = Math.max(0, remainingQuota - 1);
                btnSubmit.innerText = 'Tasarımı oluştur';
            }
        });

        function showResult(imageUrl) {
            loadingContainer.innerHTML = '';
            resultContainer.innerHTML = `
                <div class="result-box">
                    <h2 style="font-family:'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; color:var(--yellow); font-size:32px; margin-bottom:15px;">🎉 Tasarımınız Hazır!</h2>
                    <img src="${imageUrl}" class="result-img" alt="AI Interior Design">
                    <p style="color:var(--muted); font-size:14px; margin-bottom:20px;">Bu tasarım yapay zeka tarafından referans amaçlı üretilmiştir.</p>
                    <button class="cta-btn" onclick="openCostModal()">💰 Bu Tasarımın Maliyetini Hesapla →</button>
                </div>
            `;
            resultContainer.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }

        function showError(message) {
            loadingContainer.innerHTML = '';
            errorContainer.innerHTML = `<div class="error-box"><strong>Hata:</strong> ${message}</div>`;
        }
    </script>
    <script src="/js/tool-events.js" defer></script>
    <script src="/js/ai-cost-modal.js"></script>
    <x-site.contech-footer />
</body>
</html>
