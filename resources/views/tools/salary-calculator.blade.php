<!DOCTYPE html>
<html lang="tr">
<head>
    <link rel="icon" href="/favicon.ico" type="image/x-icon">

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>İnşaat Mühendisi Maaş Hesaplama 2026 | WORKSİTEM</title>
    <meta name="description" content="TMMOB asgari ücretleri, deneyim yılı ve pozisyona göre inşaat mühendisleri için tahmini net maaş aralığını hesaplayın.">
    <link rel="canonical" href="https://worksitem.com/araclar/insaat-muhendisi-maas-hesabi">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root { --bg: #0b0f17; --card-bg: #151c28; --border: #21262d; --yellow: #ffb700; --text: #f0f6fc; --muted: #8b949e; --green: #10b981; --cyan: #38bdf8; }
        body { font-family: 'Inter', sans-serif; background: var(--bg); color: var(--text); margin: 0; padding: 0; overflow-x: hidden; }
        .container { max-width: 1200px; margin: 40px auto; padding: 0 4%; display: grid; grid-template-columns: 1fr 1fr; gap: 40px; }
        @media(max-width: 992px) { .container { grid-template-columns: 1fr; } }
        .form-card { background: var(--card-bg); border: 1px solid var(--border); border-radius: 12px; padding: 30px; margin-bottom: 20px; }
        h1 { font-family: 'Bebas Neue'; font-size: 38px; color: #fff; margin-bottom: 10px; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; font-size: 13px; font-weight: 700; color: var(--muted); margin-bottom: 8px; text-transform: uppercase; }
        .form-control { width: 100%; padding: 12px; border-radius: 6px; background: #0d1117; border: 1px solid var(--border); color: white; font-family: inherit; }
        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
        .res-item { display: flex; justify-content: space-between; padding: 12px 0; border-bottom: 1px dashed var(--border); font-size: 14px; }
        .res-item.total { border-bottom: none; font-size: 24px; font-weight: 800; color: var(--green); margin-top: 15px; }
        .ai-box { background: rgba(56, 189, 248, 0.05); border: 1px solid var(--cyan); border-radius: 8px; padding: 20px; margin-top: 20px; }
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
<body>
    <x-site.contech-header />
    <div class="container">
        <div>
            <h1>💼 İnşaat Mühendisi Maaş Hesabı</h1>
            <p style="color:var(--muted); font-size:14px; margin-bottom:30px;">TMMOB asgari ücretleri, deneyim yılı ve pozisyonunuza göre piyasadaki tahmini net maaşınızı öğrenin.</p>
            <div class="form-card">
                <div class="grid-2">
                    <div class="form-group"><label>Sektörel Deneyim (Yıl)</label><input type="number" id="exp" class="form-control" value="3" oninput="calc()"></div>
                    <div class="form-group">
                        <label>Yabancı Dil Seviyesi</label>
                        <select id="lang" class="form-control" onchange="calc()">
                            <option value="1.0" selected>Başlangıç / Yok</option>
                            <option value="1.15">İyi (İngilizce)</option>
                            <option value="1.30">Çok İyi (İngilizce + İkinci Dil)</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label>Çalışılan Pozisyon</label>
                    <select id="pos" class="form-control" onchange="calc()">
                        <option value="1.0">Saha Mühendisi / Ofis Mühendisi</option>
                        <option value="1.2" selected>Şantiye Şefi / Teknik Ofis Şefi</option>
                        <option value="1.6">Proje Müdürü</option>
                    </select>
                </div>
            </div>
        </div>
        <div>
            <div class="form-card" style="border-color:var(--green); position:sticky; top:20px;">
                <div style="font-family:'Bebas Neue'; font-size:24px; color:var(--green); margin-bottom:15px;">📊 2026 KARİYER ANALİZİ</div>
                <div class="res-item"><span>TMMOB Brüt Asgari Ücret (Taban)</span> <strong>45.000 TL</strong></div>
                <div class="res-item"><span>Deneyim Etkisi</span> <strong id="r_exp_impact">+0 TL</strong></div>
                <div class="res-item total"><span>TAHMİNİ NET PİYASA MAAŞI</span> <span id="r_total">0 TL</span></div>
                
                <div class="ai-box">
                    <div style="color:var(--cyan); font-family:'Bebas Neue'; font-size:22px; margin-bottom:10px;">🤖 Kariyer Tavsiyesi</div>
                    <ul id="ai_notes" style="font-size:13px; margin:0; padding-left:15px; line-height:1.6;"></ul>
                </div>
            </div>
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
        function calc() {
            let exp = parseInt(document.getElementById('exp').value) || 0;
            let lang = parseFloat(document.getElementById('lang').value);
            let pos = parseFloat(document.getElementById('pos').value);

            // TMMOB referans taban net maaş varsayımı
            let base = 38000; 
            
            // Yıla göre değer artışı (Her yıl için ort. 2500 TL çarpanı)
            let expImpact = exp * 2500;
            
            let subTotal = base + expImpact;
            let finalSalary = subTotal * pos * lang;

            document.getElementById('r_exp_impact').innerText = '+' + new Intl.NumberFormat('tr-TR').format(expImpact) + ' TL';
            document.getElementById('r_total').innerText = new Intl.NumberFormat('tr-TR').format(Math.round(finalSalary)) + ' TL';

            let notes = [];
            notes.push("Bu hesaplama Türkiye'deki kurumsal firmaların piyasa ortalamalarını yansıtır, taşeron veya küçük çaplı firmalarda rakamlar %20-30 daha düşük olabilir.");
            if(exp < 5 && pos === 1.6) notes.push("⚠️ 5 yıldan az tecrübe ile Proje Müdürü pozisyonu kurumsal firmalarda nadiren karşılaşılan bir durumdur.");
            if(lang === 1.0) notes.push("İngilizce seviyenizi geliştirmek, uluslararası projelerde (Rusya, Orta Doğu) veya yabancı ortaklı firmalarda maaşınızı %30'a kadar artıracaktır.");
            
            let notesBox = document.getElementById('ai_notes');
            notesBox.innerHTML = '';
            notes.forEach(n => { let li = document.createElement('li'); li.innerText = n; li.style.marginBottom = "5px"; notesBox.appendChild(li); });
        }
        document.addEventListener('DOMContentLoaded', calc);
    </script>
    <x-site.contech-footer />
</body>
</html>
