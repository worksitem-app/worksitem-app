<!DOCTYPE html>
<html lang="tr">
<head>
    <link rel="icon" href="/favicon.ico" type="image/x-icon">

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Klima BTU Hesaplayıcı | WORKSİTEM</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        :root { --bg:#f8fafc; --surface:#ffffff; --border:#e2e8f0; --primary:#101318; --primary-light:#fff8e1; --text-main:#0f172a; --text-muted:#64748b; --green:#059669; --transition:all 0.25s ease;}
        body { font-family:'Inter',sans-serif; background:var(--bg); color:var(--text-main); margin:0; padding-bottom:80px; }
        .navbar { display:flex; justify-content:space-between; align-items:center; padding:1rem 5%; background:var(--surface); border-bottom:1px solid var(--border); position:sticky; top:0; z-index:50;}
        .logo-wrapper { display:flex; align-items:center; gap:8px; text-decoration:none; }
        .logo-mark { background:var(--primary); color:#fff; font-weight:800; font-size:14px; padding:4px 10px; border-radius:6px; }
        .logo-text { font-weight:800; font-size:20px; color:var(--text-main); }
        .container { max-width:1200px; margin:40px auto; padding:0 4%; display:grid; grid-template-columns:1fr 1fr; gap:40px; }
        @media(max-width:992px) { .container { grid-template-columns:1fr; } }
        .form-card { background:var(--surface); border:1px solid var(--border); border-radius:16px; padding:30px; margin-bottom:20px; box-shadow:0 4px 6px -1px rgba(0,0,0,0.05); }
        h1 { font-size:2rem; font-weight:800; margin-bottom:10px; letter-spacing:-0.5px; }
        .subtitle { color:var(--text-muted); font-size:0.95rem; margin-bottom:30px; }
        .form-group { margin-bottom:20px; }
        .form-group label { display:block; font-size:0.85rem; font-weight:700; margin-bottom:8px; text-transform:uppercase; letter-spacing:0.5px; }
        .form-control { width:100%; padding:14px; border-radius:8px; background:var(--bg); border:1px solid var(--border); font-family:inherit; font-size:1rem; }
        .grid-2 { display:grid; grid-template-columns:1fr 1fr; gap:15px; }
        .result-header { font-size:1.25rem; font-weight:800; color:var(--primary); margin-bottom:20px; display:flex; align-items:center; gap:8px;}
        .res-item { display:flex; justify-content:space-between; padding:14px 0; border-bottom:1px dashed var(--border); font-size:0.95rem; font-weight:500; }
        .res-item strong { font-weight:800; font-size:1.1rem; }
        .res-item.total { border-bottom:none; margin-top:10px; padding-top:20px; border-top:2px solid var(--border); }
        .res-item.total strong { font-size:1.75rem; color:var(--primary); }
        .ai-box { background:var(--primary-light); border:1px solid #fde68a; border-radius:12px; padding:24px; margin-top:25px; }
        .cta-box { background:var(--surface); border:2px solid var(--green); border-radius:12px; padding:24px; text-align:center; margin-top:25px; }
        .cta-btn { display:block; background:#101318; color:#fff; font-weight:800; font-size:1.1rem; padding:16px; border-radius:8px; text-decoration:none; transition:var(--transition); }
        .cta-btn:hover { background:#047857; transform:translateY(-2px); }
    </style>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body data-tool-slug="ac-calculator">
    <nav class="navbar"><a href="/" class="logo-wrapper"><img src="/brand/worksitem-logo.png" alt="Worksitem Logo" style="height: 35px; width: auto;"></a><a href="/araclar" style="font-weight: 800; color: var(--text-muted); text-decoration: none; font-size: 0.95rem; transition: color 0.2s;" onmouseover="this.style.color='var(--primary)'" onmouseout="this.style.color='var(--text-muted)'">◀ Tüm Araçlar</a></nav>
    <div class="container">
        <div>
            <h1>Akıllı Klima (BTU) Hesabı</h1>
            <p class="subtitle">Kişi sayısı, yalıtım ve cephe durumuna göre ihtiyacınız olan doğru klima kapasitesini bulun.</p>
            <div class="form-card">
                <div class="grid-2">
                    <div class="form-group"><label>Oda Alanı (m²)</label><input type="number" id="area" class="form-control" value="25" oninput="calc()"></div>
                    <div class="form-group"><label>Kişi Sayısı</label><input type="number" id="ppl" class="form-control" value="3" oninput="calc()"></div>
                </div>
                <div class="form-group">
                    <label>Cephe Durumu</label>
                    <select id="sun" class="form-control" onchange="calc()">
                        <option value="0">Kuzey Cephe (Az Güneş)</option>
                        <option value="10" selected>Doğu/Batı Cephe</option>
                        <option value="20">Güney Cephe (Çok Güneş)</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Yalıtım Durumu</label>
                    <select id="ins" class="form-control" onchange="calc()">
                        <option value="-10">Yeni / İyi Yalıtımlı</option>
                        <option value="0" selected>Standart Yalıtım</option>
                        <option value="15">Yalıtımsız / En Üst Kat</option>
                    </select>
                </div>
            </div>
        </div>
        <div>
            <div class="form-card" style="border-color:var(--primary); position:sticky; top:100px;">
                <div class="result-header">İKLİMLENDİRME ANALİZİ</div>
                <div class="res-item"><span>Temel Isı Yükü</span> <strong id="r_base">0 BTU</strong></div>
                <div class="res-item"><span>Kişi Yükü</span> <strong id="r_ppl">0 BTU</strong></div>
                <div class="res-item total"><span>ÖNERİLEN CİHAZ</span> <strong id="r_total">0 BTU</strong></div>
                
                <div class="ai-box">
                    <div style="color:var(--primary); font-weight:800; margin-bottom:10px;">🤖 Mekanik Uzmanı Notu</div>
                    <ul id="ai_notes" style="font-size:0.875rem; margin:0; padding-left:18px;"></ul>
                </div>
                </div>

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
        function calc() {
            let area = parseFloat(document.getElementById('area').value)||0; let ppl = parseFloat(document.getElementById('ppl').value)||0; 
            let sun = parseFloat(document.getElementById('sun').value); let ins = parseFloat(document.getElementById('ins').value);

            let base = (area * 2.8) * 150; // Hacim * 150
            let pplBtu = ppl * 600;
            let factorBtu = base * ((sun + ins) / 100);
            
            let total = base + pplBtu + factorBtu;
            
            let rec = 9000;
            if(total>9000 && total<=12000) rec = 12000;
            else if(total>12000 && total<=18000) rec = 18000;
            else if(total>18000 && total<=24000) rec = 24000;
            else if(total>24000) rec = Math.ceil(total/1000)*1000;

            document.getElementById('r_base').innerText = Math.round(base) + ' BTU';
            document.getElementById('r_ppl').innerText = '+' + Math.round(pplBtu) + ' BTU';
            document.getElementById('r_total').innerText = rec + ' BTU';

            let notes = [];
            if(ins === 15) notes.push("Binanızın yalıtımsız veya en üst kat olması ısı kaybını çok artırmaktadır. Cihaz kapasitesini zorlamamak için yalıtım önerilir.");
            if(sun === 20) notes.push("Güney cephe doğrudan ısı kazancına yol açar. Inverter teknolojili A++ cihazlar faturanızı dengeler.");
            if(total > 24000) notes.push("24.000 BTU üstü ihtiyaçlar için hava sirkülasyonu adına 2 ayrı cihaz (Örn: 2x 12000) kullanmanız daha sağlıklı olabilir.");
            
            let notesBox = document.getElementById('ai_notes'); notesBox.innerHTML = '';
            notes.forEach(n => { let li = document.createElement('li'); li.innerText = n; li.style.marginBottom="5px"; notesBox.appendChild(li); });
        } document.addEventListener('DOMContentLoaded', calc);
    </script>
    <script src="/js/tool-events.js" defer></script>
</body>
</html>
