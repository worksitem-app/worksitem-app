<!DOCTYPE html>
<html lang="tr">
<head>
    <link rel="icon" href="/favicon.ico" type="image/x-icon">

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>2D Merdiven ve Basamak Hesaplayıcı | WORKSİTEM</title>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root { --bg: #0b0f17; --card-bg: #151c28; --border: #21262d; --yellow: #ffb700; --text: #f0f6fc; --muted: #8b949e; --green: #10b981; --cyan: #38bdf8; }
        body { font-family: 'Inter', sans-serif; background: var(--bg); color: var(--text); margin: 0; padding: 0; }
        .container { max-width: 1200px; margin: 40px auto; padding: 0 4%; display: grid; grid-template-columns: 350px 1fr; gap: 40px; }
        @media(max-width: 992px) { .container { grid-template-columns: 1fr; } }
        .form-card { background: var(--card-bg); border: 1px solid var(--border); border-radius: 16px; padding: 30px; }
        h1 { font-family: 'Bebas Neue'; font-size: 38px; color: #fff; margin-bottom: 10px; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; font-size: 13px; font-weight: 700; color: var(--muted); margin-bottom: 8px; }
        .form-control { width: 100%; padding: 14px; border-radius: 8px; background: #0d1117; border: 1px solid var(--border); color: white; font-family: inherit; }
        
        .result-box { background: rgba(56, 189, 248, 0.05); border: 1px solid var(--cyan); border-radius: 8px; padding: 15px; margin-top: 20px; }
        .res-item { display: flex; justify-content: space-between; padding: 8px 0; font-size: 14px; border-bottom: 1px dashed rgba(255,255,255,0.1); }
        .res-item:last-child { border-bottom: none; }
        
        .canvas-container { background: #0d1117; border: 2px dashed var(--border); border-radius: 12px; padding: 20px; display: flex; align-items: center; justify-content: center; overflow: hidden; position: relative; }
        canvas { background: transparent; max-width: 100%; }
        
        .cta-btn { display: block; width: 100%; text-align: center; background: var(--green); color: #fff; font-family: 'Bebas Neue'; font-size: 20px; padding: 15px; border-radius: 8px; text-decoration: none; margin-top: 20px; }
    </style>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body data-tool-slug="stair-calculator">
    <div style="padding:15px 4%; border-bottom:1px solid var(--border); background:#151c28;"><a href="/araclar" style="color:#fff; text-decoration:none; font-family:'Bebas Neue'; font-size:24px;">◀ WORKSİTEM ARAÇLAR</a></div>
    
    <div class="container">
        <!-- SOL PANEL -->
        <div class="form-card">
            <h1>📐 Merdiven Hesaplayıcı</h1>
            <p style="color:var(--muted); font-size:13px; margin-bottom:20px;">Ergonomik merdiven formülü (2h + b = 63 cm) kullanılarak basamak ölçüleri ve 2D kesit planı otomatik oluşturulur.</p>
            
            <div class="form-group">
                <label>Kat Yüksekliği (Zeminden Zemine - cm)</label>
                <input type="number" id="total_height" class="form-control" value="280" step="1" oninput="calculateStairs()">
            </div>
            <div class="form-group">
                <label>Basamak Sayısı (Adet)</label>
                <input type="number" id="steps" class="form-control" value="16" step="1" oninput="calculateStairs()">
            </div>

            <div class="result-box">
                <div style="font-family:'Bebas Neue'; color:var(--cyan); font-size:20px; margin-bottom:10px;">ANALİZ SONUCU</div>
                <div class="res-item"><span>Rıht Yüksekliği (h)</span> <strong id="res_h">0 cm</strong></div>
                <div class="res-item"><span>Basamak Genişliği (b)</span> <strong id="res_b">0 cm</strong></div>
                <div class="res-item"><span>Toplam Merdiven Uzunluğu (L)</span> <strong id="res_l" style="color:var(--yellow);">0 cm</strong></div>
                <div class="res-item"><span>Eğim Açısı</span> <strong id="res_angle">0°</strong></div>
                <div id="warning" style="color:#ef4444; font-size:12px; margin-top:10px; font-weight:bold; display:none;">Dikkat: Bu ölçüler ergonomik (60-64cm) standartlarının dışındadır! Basamak sayısını değiştirin.</div>
            </div>

            <a href="/maliyet-hesapla" class="cta-btn">Betonarme Maliyetini Hesapla →</a>
        </div>

        <!-- SAĞ PANEL: 2D ÇİZİM MOToru -->
        <div class="canvas-container">
            <canvas id="stairCanvas" width="800" height="500"></canvas>
            <div style="position:absolute; top:20px; left:20px; font-family:'Bebas Neue'; font-size:24px; color:rgba(255,255,255,0.2);">2D KESİT PLANI</div>
        </div>
    </div>

        <!-- STANDART KAİNTA LEAD HUNİSİ (CTA) -->
        <div class="no-print" style="margin-top: 40px; padding: 30px; background: #f0fdf4; border: 2px solid #bbf7d0; border-radius: 16px; text-align: center;">
            <h3 style="color: #166534; font-size: 20px; font-weight: 800; margin-bottom: 10px;">Hesaplamayı Yaptınız, Şimdi İşi Uzmanına Bırakın</h3>
            <p style="color: #15803d; font-size: 14px; margin-bottom: 20px;">Kainta İnşaat mühendislerinden projenize özel resmi keşif ve bütçe teklifi alın.</p>
            <a href="/maliyet-hesapla" style="display: inline-block; background: #166534; color: #fff; padding: 16px 32px; font-size: 16px; font-weight: 800; border-radius: 8px; text-decoration: none; box-shadow: 0 4px 10px rgba(22,101,52,0.3); transition: all 0.2s;">Ön maliyet hesabına geç →</a>
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
        function calculateStairs() {
            let H = parseFloat(document.getElementById('total_height').value);
            let n = parseInt(document.getElementById('steps').value);
            
            if(!H || !n || n < 2) return;

            // Rıht (Yükseklik)
            let h = H / n;
            // Ergonomik Formül: 2h + b = 63  => b = 63 - 2h
            let b = 63 - (2 * h);
            
            // Eğer basamak genişliği mantıksız çıkarsa sabitle
            let isErgonomic = true;
            if(b < 20 || b > 35) isErgonomic = false;

            // Toplam Uzunluk (Son basamak sahanlığa denk gelir, n-1 alınır)
            let L = (n - 1) * b;
            
            // Eğim Açısı
            let angle = Math.atan(h / b) * (180 / Math.PI);

            // DOM Güncelleme
            document.getElementById('res_h').innerText = h.toFixed(1) + ' cm';
            document.getElementById('res_b').innerText = b.toFixed(1) + ' cm';
            document.getElementById('res_l').innerText = L.toFixed(1) + ' cm';
            document.getElementById('res_angle').innerText = angle.toFixed(1) + '°';
            document.getElementById('warning').style.display = isErgonomic ? 'none' : 'block';

            drawStairs(n, h, b, H, L);
        }

        function drawStairs(n, h, b, totalH, totalL) {
            const canvas = document.getElementById('stairCanvas');
            const ctx = canvas.getContext('2d');
            ctx.clearRect(0, 0, canvas.width, canvas.height);

            // Çizim Ölçeklemesi (Canvas'a sığdırmak için)
            let scaleX = (canvas.width - 100) / totalL;
            let scaleY = (canvas.height - 100) / totalH;
            let scale = Math.min(scaleX, scaleY);

            let startX = 50;
            let startY = canvas.height - 50;

            // Arka Plan Grid
            ctx.strokeStyle = 'rgba(255,255,255,0.05)';
            ctx.lineWidth = 1;
            for(let i=0; i<canvas.width; i+=40) { ctx.beginPath(); ctx.moveTo(i,0); ctx.lineTo(i,canvas.height); ctx.stroke(); }
            for(let i=0; i<canvas.height; i+=40) { ctx.beginPath(); ctx.moveTo(0,i); ctx.lineTo(canvas.width,i); ctx.stroke(); }

            // Merdiven Çizimi
            ctx.beginPath();
            ctx.moveTo(startX, startY);
            
            let currentX = startX;
            let currentY = startY;

            ctx.strokeStyle = '#38bdf8'; // Cyan
            ctx.lineWidth = 3;

            for(let i = 0; i < n; i++) {
                // Rıht (Yukarı)
                currentY -= (h * scale);
                ctx.lineTo(currentX, currentY);
                
                // Basamak (Sağa - Son basamak hariç)
                if (i < n - 1) {
                    currentX += (b * scale);
                    ctx.lineTo(currentX, currentY);
                }
            }
            ctx.stroke();

            // Alt Betonarme Eğimi (Görsellik İçin)
            ctx.beginPath();
            ctx.moveTo(startX, startY);
            ctx.lineTo(currentX, currentY + (15*scale)); // Döşeme kalınlığı payı
            ctx.lineTo(currentX, startY);
            ctx.closePath();
            ctx.fillStyle = 'rgba(56, 189, 248, 0.1)';
            ctx.fill();

            // Ölçü Çizgileri
            ctx.fillStyle = '#fff';
            ctx.font = '12px Inter';
            ctx.fillText('L: ' + totalL.toFixed(1) + ' cm', startX + (currentX-startX)/2, startY + 20);
            ctx.fillText('H: ' + totalH.toFixed(1) + ' cm', currentX + 10, startY - (startY-currentY)/2);
        }

        document.addEventListener('DOMContentLoaded', calculateStairs);
    </script>
    <script src="/js/tool-events.js" defer></script>
</body>
</html>
