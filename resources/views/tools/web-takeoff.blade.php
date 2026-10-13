<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dijital Metraj (Web Takeoff) | WORKSİTEM</title>
    <meta name="description" content="PDF, JPG veya PNG kat planınızı yükleyin; WORKSİTEM Dijital Metraj aracıyla proje üzerinden metraj çıkarmaya başlayın.">
    <link rel="canonical" href="https://worksitem.com/araclar/dijital-metraj">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <!-- PDF.js Kütüphanesi -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.min.js"></script>
    <style>
        :root {
            --bg: #1e1e24; --surface: #2b2b36; --border: #3f3f4e; --text: #fff; --muted: #a1a1aa;
            --accent: #ffb800; --accent-hover: #e5a500;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Inter', sans-serif; background: var(--bg); color: var(--text); display:flex; flex-direction:column; height:100vh; overflow:hidden; }
        .takeoff-shell { display:flex; flex:1; min-height:0; overflow:hidden; }
        
        /* APP LAYOUT */
        .sidebar-left { width: 70px; background: var(--surface); border-right: 1px solid var(--border); display: flex; flex-direction: column; align-items: center; padding: 20px 0; z-index: 10;}
        .main-workspace { flex: 1; display: flex; flex-direction: column; position: relative; background: #121215; overflow: hidden;}
        .sidebar-right { width: 320px; background: var(--surface); border-left: 1px solid var(--border); display: flex; flex-direction: column; z-index: 10;}

        /* LEFT TOOLBAR */
        .tool-btn { width: 44px; height: 44px; margin-bottom: 15px; border-radius: 12px; border: none; background: transparent; color: var(--muted); font-size: 20px; cursor: pointer; transition: .2s; display: flex; align-items: center; justify-content: center;}
        .tool-btn:hover { background: rgba(255,255,255,0.1); color: #fff; }
        .tool-btn.active { background: rgba(255,184,0,0.2); color: var(--accent); border: 1px solid var(--accent); }
        .tool-tooltip { font-size: 10px; text-align: center; color: var(--muted); margin-top: -10px; margin-bottom: 15px; }

        /* MAIN WORKSPACE */
        .topbar { height: 60px; background: var(--surface); border-bottom: 1px solid var(--border); display: flex; align-items: center; padding: 0 20px; justify-content: space-between; }
        .brand { font-weight: 900; color: #fff; text-decoration: none; display: flex; align-items: center; gap: 10px;}
        .brand span { background: var(--accent); color: #000; padding: 3px 6px; border-radius: 4px; font-size: 10px; font-weight: 900;}
        
        .canvas-container { flex: 1; overflow: auto; position: relative; cursor: crosshair; display: flex; align-items: center; justify-content: center; }
        canvas { background: #fff; box-shadow: 0 0 30px rgba(0,0,0,0.5); transform-origin: top left; }
        
        .upload-overlay { position: absolute; inset: 20px; border: 2px dashed var(--border); border-radius: 20px; display: flex; flex-direction: column; align-items: center; justify-content: center; background: rgba(30,30,36,0.9); z-index: 5;}
        .upload-overlay h2 { margin-bottom: 10px; font-size: 24px; font-weight: 800;}
        .upload-overlay p { color: var(--muted); margin-bottom: 20px;}
        .file-input-btn { background: var(--accent); color: #000; padding: 12px 24px; border-radius: 8px; font-weight: 800; cursor: pointer; }
        input[type="file"] { display: none; }

        /* RIGHT SIDEBAR (BOQ) */
        .right-header { padding: 20px; border-bottom: 1px solid var(--border); }
        .right-header h3 { font-size: 16px; font-weight: 800; }
        
        .measurements-list { flex: 1; overflow-y: auto; padding: 20px; }
        .m-item { background: rgba(0,0,0,0.2); border: 1px solid var(--border); border-radius: 8px; padding: 15px; margin-bottom: 10px; display: flex; justify-content: space-between; align-items: center;}
        .m-name { font-size: 13px; font-weight: 600; }
        .m-value { font-size: 16px; font-weight: 900; color: var(--accent); }
        
        .right-footer { padding: 20px; border-top: 1px solid var(--border); background: #1a1a20;}
        .total-box { display: flex; justify-content: space-between; margin-bottom: 20px;}
        .total-box span { font-size: 14px; color: var(--muted); }
        .total-box strong { font-size: 20px; font-weight: 900; color: #fff;}
        .action-btn { display: block; width: 100%; text-align: center; background: var(--accent); color: #000; text-decoration: none; padding: 15px; border-radius: 8px; font-weight: 900; font-size: 14px; transition: .2s; border: none; cursor: pointer;}
        .action-btn:hover { background: var(--accent-hover); }

        /* SCALE PROMPT */
        #scalePrompt { display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); background: var(--surface); padding: 30px; border-radius: 16px; border: 1px solid var(--border); box-shadow: 0 20px 50px rgba(0,0,0,0.8); z-index: 100; text-align: center;}
        #scalePrompt input { width: 100px; padding: 10px; margin: 15px 0; border-radius: 6px; border: 1px solid var(--border); background: #111; color: #fff; text-align: center; font-size: 16px;}
        
        /* B2B LEAD MODAL */
        #leadModal { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.8); backdrop-filter: blur(5px); z-index: 200; align-items: center; justify-content: center;}
        .lead-content { background: var(--surface); padding: 40px; border-radius: 20px; border: 1px solid var(--border); width: 100%; max-width: 500px; }
        .lead-content h2 { font-size: 22px; font-weight: 900; margin-bottom: 10px;}
        .lead-content p { color: var(--muted); font-size: 14px; margin-bottom: 25px;}
        .form-group { margin-bottom: 15px; text-align: left;}
        .form-group label { display: block; font-size: 12px; font-weight: 700; margin-bottom: 5px; color: var(--muted);}
        .form-group input { width: 100%; padding: 12px 15px; border-radius: 8px; border: 1px solid var(--border); background: #111; color: #fff; font-family: inherit;}
        .form-group input:focus { border-color: var(--accent); outline: none;}
    </style>
    <link rel="icon" href="/favicon.ico" type="image/x-icon">
    <meta property="og:image" content="https://worksitem.com/brand/og-image.png">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:image" content="https://worksitem.com/brand/og-image.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="manifest" href="/site.webmanifest">
    <meta name="theme-color" content="#ffffff">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <x-calc.head />
    <style id="ws-app-shell">
        body.wc{height:auto!important;overflow:auto!important;display:block!important}
        /* Uygulamanın koyu çalışma alanı paleti (sayfa kiti açık renk değişkenlerini ezmesin) */
        .takeoff-shell{--bg:#1e1e24;--surface:#2b2b36;--border:#3f3f4e;--text:#fff;--muted:#a1a1aa;color:#fff;background:#1e1e24}
        .takeoff-shell{height:calc(100vh - 72px);min-height:620px}
        .ws-mobile-note{display:none}
        @media (max-width: 900px){
            .takeoff-shell{flex-wrap:wrap;height:auto;min-height:0}
            .sidebar-left{width:100%!important;flex-direction:row!important;justify-content:center;gap:12px;padding:10px 0!important;border-right:0!important;border-bottom:1px solid var(--border)}
            .sidebar-left .tool-tooltip{display:none}
            .main-workspace{flex:1 1 100%!important;height:62vh;min-height:380px}
            .sidebar-right{width:100%!important;border-left:0!important}
            .ws-mobile-note{display:block;margin:0 16px 12px;padding:10px 12px;border-radius:10px;background:#FFF8E1;border:1px solid #FFE08A;font-size:13px;color:#5c4a10}
        }
    </style>
</head>
<body class="wc" data-tool-slug="web-takeoff">
<x-site.contech-header />
<div class="wc-wrap" style="padding-bottom:14px">
    <x-calc.hero title="Dijital metraj: PDF plan üzerinden alan ölçümü" crumb="Dijital metraj" :facts="['PDF, JPG, PNG plan', 'Ölçek kalibrasyonu', 'Metraj listesi (BOQ)']">Kat planınızı yükleyin, bilinen bir ölçüyle ölçeği ayarlayın ve alanları tıklayarak ölçün; ölçümler metraj listesine eklenir ve tek tuşla fiyatlandırılır.</x-calc.hero>
</div>
<p class="ws-mobile-note">Ölçüm için en iyi deneyim bilgisayar ekranındadır; telefonda planı yakınlaştırarak kullanabilirsiniz.</p>
<div class="takeoff-shell">

    <!-- LEFT TOOLBAR -->
    <aside class="sidebar-left">
        <button class="tool-btn" id="toolMove" title="Kaydır (Pan)">✋</button>
        <div class="tool-tooltip">Kaydır</div>
        
        <button class="tool-btn" id="toolScale" title="Ölçek Belirle (Kalibrasyon)">📏</button>
        <div class="tool-tooltip">Ölçek</div>

        <button class="tool-btn active" id="toolArea" title="Alan Ölç (Metrekare)">⬠</button>
        <div class="tool-tooltip">Alan (m²)</div>
    </aside>

    <!-- MAIN WORKSPACE -->
    <main class="main-workspace">
        <header class="topbar">
            <a href="/araclar" class="brand">WORKSİTEM <span>CONTECH TAKEOFF</span></a>
            <div style="font-size:12px; color:#a1a1aa;">Proje: <span id="docName" style="color:#fff;">Yüklenmedi</span></div>
        </header>

        <div class="canvas-container" id="canvasContainer">
            <div class="upload-overlay" id="uploadOverlay">
                <h2>Projenizi Yükleyin</h2>
                <p>Kat planınızı (PDF, JPG, PNG) yükleyerek metraj çıkarmaya başlayın.</p>
                <label class="file-input-btn" for="fileInput">📁 Bilgisayardan Seç</label>
                <input type="file" id="fileInput" accept="image/jpeg, image/png, application/pdf">
            </div>
            <canvas id="takeoffCanvas"></canvas>
        </div>
    </main>

    <!-- RIGHT SIDEBAR -->
    <aside class="sidebar-right">
        <div class="right-header">
            <h3>Metraj Listesi (BOQ)</h3>
        </div>
        <div class="measurements-list" id="measurementsList">
            <div style="color:var(--muted); font-size:12px; text-align:center; margin-top:50px;">Henüz bir ölçüm yapılmadı.<br>Önce 'Ölçek' aracıyla referans belirleyin.</div>
        </div>
        <div class="right-footer">
            <div class="total-box">
                <span>Toplam Alan</span>
                <strong id="totalArea">0.00 m²</strong>
            </div>
            <button class="action-btn" id="btnGetQuote">Ölçümleri Fiyatlandır →</button>
        </div>
    </aside>
</div>

    <!-- KÜÇÜK MODALLAR -->
    <div id="scalePrompt">
        <h3>Ölçek Kalibrasyonu</h3>
        <p style="font-size:13px; color:#a1a1aa;">Çizdiğiniz bu çizgi gerçekte kaç metre?</p>
        <input type="number" id="scaleInput" step="0.1" placeholder="Örn: 5.2">
        <br>
        <button class="file-input-btn" id="btnSaveScale">Uygula</button>
    </div>

    <!-- B2B LEAD MODAL -->
    <div id="leadModal">
        <div class="lead-content">
            <h2>Kainta İnşaat'tan Uygulama Teklifi</h2>
            <p>Çıkardığınız <strong><span id="leadTotalArea">0</span> m²</strong> alan metrajı için anahtar teslim inşaat/tadilat fiyatlandırması isteyin.</p>
            
            <form action="{{ route('maliyet_hesapla.lead') }}" method="POST">
                @csrf
                <input type="hidden" name="source" value="worksitem_web_takeoff">
                <input type="hidden" name="calculated_service" value="Dijital Metraj (Web Takeoff)">
                <input type="hidden" name="calculated_area" id="leadHiddenData" value="">
                <input type="hidden" name="budget_confirmed" value="1">
                <input type="hidden" name="lead_quality" value="hot">

                <div class="form-group">
                    <label>Adınız Soyadınız / Firma</label>
                    <input type="text" name="name" required placeholder="Firma veya Ad Soyad">
                </div>
                <div class="form-group">
                    <label>Telefon Numaranız</label>
                    <input type="tel" name="phone" required placeholder="05XX XXX XX XX">
                </div>
                <div class="form-group">
                    <label>Proje Konumu</label>
                    <input type="text" name="location" required placeholder="İl / İlçe">
                </div>
                <div class="form-group">
                    <label>Notunuz (Opsiyonel)</label>
                    <input type="text" name="notes" placeholder="Ek detaylar...">
                </div>
                <button type="submit" class="action-btn" style="margin-top: 15px;">Metrajı Gönder ve Teklif Al →</button>
                <button type="button" onclick="document.getElementById('leadModal').style.display='none'" style="width:100%; background:transparent; border:none; color:var(--muted); margin-top:15px; cursor:pointer;">İptal Et</button>
            </form>
        </div>
    </div>

<script>
    // CANVAS ENGINE BİLEŞENLERİ
    const canvas = document.getElementById('takeoffCanvas');
    const ctx = canvas.getContext('2d');
    const container = document.getElementById('canvasContainer');
    
    let imageObj = new Image();
    let isImageLoaded = false;
    let scalePxToM = 0; // 1 metre kaç pixel?

    let currentTool = 'area'; // move, scale, area
    let scalePoints = [];
    let currentPolygon = [];
    let savedPolygons = [];
    let totalM2 = 0;

    // PDF.js Tanımlamaları
    if (window.pdfjsLib) pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.worker.min.js';

    // TOOLBAR İŞLEMLERİ
    document.querySelectorAll('.tool-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.tool-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            currentTool = this.id.replace('tool', '').toLowerCase();
            
            if(currentTool === 'move') canvas.style.cursor = 'grab';
            else canvas.style.cursor = 'crosshair';

            scalePoints = [];
            currentPolygon = [];
            redraw();
        });
    });

    // DOSYA YÜKLEME (PDF & IMG)
    document.getElementById('fileInput').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if(!file) return;

        document.getElementById('uploadOverlay').style.display = 'none';
        document.getElementById('docName').innerText = file.name;

        if (file.type === 'application/pdf') {
            const fileReader = new FileReader();
            fileReader.onload = function() {
                const typedarray = new Uint8Array(this.result);
                pdfjsLib.getDocument(typedarray).promise.then(pdf => {
                    pdf.getPage(1).then(page => {
                        const viewport = page.getViewport({ scale: 2.0 }); // Netlik için 2x
                        canvas.width = viewport.width;
                        canvas.height = viewport.height;
                        
                        const renderContext = { canvasContext: ctx, viewport: viewport };
                        page.render(renderContext).promise.then(() => {
                            imageObj.src = canvas.toDataURL(); // Çizimi image'a al
                            isImageLoaded = true;
                            alert("PDF yüklendi! Önce 'Ölçek' aracıyla bir duvarı referans alın.");
                        });
                    });
                });
            };
            fileReader.readAsArrayBuffer(file);
        } else {
            // Normal Resim
            const reader = new FileReader();
            reader.onload = function(event) {
                imageObj.onload = function() {
                    canvas.width = imageObj.width;
                    canvas.height = imageObj.height;
                    isImageLoaded = true;
                    redraw();
                    alert("Plan yüklendi! Lütfen sol menüden 'Ölçek' (Cetvel) aracını seçip bilinen bir duvarın başını ve sonunu tıklayarak sistemi kalibre edin.");
                }
                imageObj.src = event.target.result;
            }
            reader.readAsDataURL(file);
        }
    });

    // CANVAS TIKLAMA OLAYLARI (DİJİTAL METRAJ)
    canvas.addEventListener('mousedown', function(e) {
        if(!isImageLoaded) return;
        const rect = canvas.getBoundingClientRect();
        const x = (e.clientX - rect.left) * (canvas.width / rect.width);
        const y = (e.clientY - rect.top) * (canvas.height / rect.height);

        if (currentTool === 'scale') {
            if (scalePoints.length < 2) {
                scalePoints.push({x, y});
                if (scalePoints.length === 2) {
                    document.getElementById('scalePrompt').style.display = 'block';
                }
            }
        } 
        else if (currentTool === 'area') {
            if (scalePxToM === 0) {
                alert("Önce sistemi kalibre etmelisiniz! (Sol menüden Cetvel ikonuna tıklayın)");
                return;
            }
            // Çift tıkla bitirme simülasyonu
            if (currentPolygon.length > 2 && Math.abs(x - currentPolygon[0].x) < 20 && Math.abs(y - currentPolygon[0].y) < 20) {
                finishPolygon();
            } else {
                currentPolygon.push({x, y});
            }
        }
        redraw();
    });

    // ÇİZİM MOTORU
    function redraw() {
        if (!isImageLoaded) return;
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        ctx.drawImage(imageObj, 0, 0);

        // Ölçek Çizgisi
        if (scalePoints.length > 0) {
            ctx.beginPath();
            ctx.moveTo(scalePoints[0].x, scalePoints[0].y);
            if (scalePoints.length === 2) {
                ctx.lineTo(scalePoints[1].x, scalePoints[1].y);
            }
            ctx.strokeStyle = '#ffb800'; ctx.lineWidth = 3; ctx.stroke();
            scalePoints.forEach(p => {
                ctx.beginPath(); ctx.arc(p.x, p.y, 6, 0, Math.PI*2); ctx.fillStyle = 'red'; ctx.fill();
            });
        }

        // Kaydedilmiş Poligonlar (Odalar)
        savedPolygons.forEach(poly => drawPolygon(poly.points, 'rgba(22, 163, 74, 0.4)', '#16a34a'));

        // Aktif Çizilen Poligon
        if (currentPolygon.length > 0) drawPolygon(currentPolygon, 'rgba(255, 184, 0, 0.3)', '#ffb800', true);
    }

    function drawPolygon(points, fillColor, strokeColor, isOpen = false) {
        if(points.length === 0) return;
        ctx.beginPath();
        ctx.moveTo(points[0].x, points[0].y);
        for(let i=1; i<points.length; i++) ctx.lineTo(points[i].x, points[i].y);
        if(!isOpen) ctx.closePath();
        
        ctx.fillStyle = fillColor; ctx.fill();
        ctx.strokeStyle = strokeColor; ctx.lineWidth = 3; ctx.stroke();
        
        points.forEach(p => { ctx.beginPath(); ctx.arc(p.x, p.y, 4, 0, Math.PI*2); ctx.fillStyle = strokeColor; ctx.fill(); });
    }

    // ÖLÇEK KAYDET
    document.getElementById('btnSaveScale').addEventListener('click', function() {
        const meters = parseFloat(document.getElementById('scaleInput').value);
        if(meters > 0 && scalePoints.length === 2) {
            const dx = scalePoints[1].x - scalePoints[0].x;
            const dy = scalePoints[1].y - scalePoints[0].y;
            const pxDistance = Math.sqrt(dx*dx + dy*dy);
            scalePxToM = pxDistance / meters; // 1 metre = X pixel
            
            document.getElementById('scalePrompt').style.display = 'none';
            scalePoints = [];
            currentTool = 'area';
            document.querySelectorAll('.tool-btn').forEach(b => b.classList.remove('active'));
            document.getElementById('toolArea').classList.add('active');
            canvas.style.cursor = 'crosshair';
            
            document.getElementById('measurementsList').innerHTML = '<div style="color:#16a34a; font-size:12px; text-align:center; margin-top:20px;">Sistem Kalibre Edildi. ✓<br>Şimdi Alan Ölç (⬠) aracıyla odaların etrafını çizin ve kapatmak için başlangıç noktasına tıklayın.</div>';
            redraw();
        }
    });

    // ALAN HESAPLAMA (Shoelace Formula)
    function finishPolygon() {
        if (currentPolygon.length < 3) return;
        
        let areaPx = 0;
        let j = currentPolygon.length - 1;
        for (let i = 0; i < currentPolygon.length; i++) {
            areaPx += (currentPolygon[j].x + currentPolygon[i].x) * (currentPolygon[j].y - currentPolygon[i].y);
            j = i;
        }
        areaPx = Math.abs(areaPx / 2.0);
        
        // Pixel Kare'yi Metre Kare'ye çevir
        const m2 = areaPx / (scalePxToM * scalePxToM);
        
        savedPolygons.push({ points: [...currentPolygon], m2: m2 });
        totalM2 += m2;
        currentPolygon = [];
        redraw();
        updateBOQ();
    }

    function updateBOQ() {
        const list = document.getElementById('measurementsList');
        if (savedPolygons.length === 1) list.innerHTML = ''; // İlkini temizle
        
        const last = savedPolygons[savedPolygons.length - 1];
        const html = `
            <div class="m-item">
                <div class="m-name">Ölçüm Alanı ${savedPolygons.length}</div>
                <div class="m-value">${last.m2.toFixed(2)} m²</div>
            </div>
        `;
        list.insertAdjacentHTML('beforeend', html);
        document.getElementById('totalArea').innerText = totalM2.toFixed(2) + ' m²';
    }

    // B2B TEKLİF BUTONU
    document.getElementById('btnGetQuote').addEventListener('click', function() {
        if (totalM2 === 0) {
            alert("Lütfen en az bir alanı ölçünüz.");
            return;
        }
        document.getElementById('leadTotalArea').innerText = totalM2.toFixed(2);
        document.getElementById('leadHiddenData').value = `Web Takeoff Metraj: ${totalM2.toFixed(2)} m2`;
        document.getElementById('leadModal').style.display = 'flex';
    });

</script>
    <script src="/js/tool-events.js" defer></script>
<x-site.contech-footer />
</body>
</html>
