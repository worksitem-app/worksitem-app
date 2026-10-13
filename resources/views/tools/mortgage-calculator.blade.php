<!DOCTYPE html>
<html lang="tr">
<head>
    <link rel="icon" href="/favicon.ico" type="image/x-icon">
    <meta property="og:image" content="https://worksitem.com/brand/og-image.png">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:image" content="https://worksitem.com/brand/og-image.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="manifest" href="/site.webmanifest">
    <meta name="theme-color" content="#ffffff">
<meta charset="UTF-8"><title>Azami Konut Kredisi Hesabı | WORKSİTEM</title>
    <meta name="description" content="Konut için kullanılabilecek azami kredi tutarını hesaplayın ve satın alma öncesinde ulaşabileceğiniz yaklaşık finansman limitini görün.">
    <link rel="canonical" href="https://worksitem.com/araclar/azami-konut-kredisi"><link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet"><style>:root { --bg: #f8fafc; --card-bg: #ffffff; --border: #e2e8f0; --primary: #101318; --text: #0f172a; --muted: #64748b; --cyan: #8a6200; --green: #059669; } body { font-family: 'Inter', sans-serif; background: var(--bg); color: var(--text); margin: 0; padding: 0; } .container { max-width: 1200px; margin: 40px auto; padding: 0 4%; display: grid; grid-template-columns: 1fr 1fr; gap: 40px; } @media(max-width: 992px) { .container { grid-template-columns: 1fr; } } .form-card { background: var(--card-bg); border: 1px solid var(--border); border-radius: 12px; padding: 30px; margin-bottom: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);} h1 { font-family:'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; font-size: 38px; color: var(--text); margin-bottom: 10px; } .form-group { margin-bottom: 20px; } .form-group label { display: block; font-size: 13px; font-weight: 700; color: var(--muted); margin-bottom: 8px; text-transform: uppercase; } .form-control { width: 100%; padding: 12px; border-radius: 6px; background: #f8fafc; border: 1px solid var(--border); color: var(--text); font-family: inherit; } .res-item { display: flex; justify-content: space-between; padding: 12px 0; border-bottom: 1px dashed var(--border); font-size: 14px; } .res-item.total { border-bottom: none; font-size: 22px; font-weight: 800; color: var(--primary); margin-top: 15px; } .ai-box { background: #fff8e1; border: 1px solid #fde68a; border-radius: 8px; padding: 20px; margin-top: 20px; }</style>    <meta name="csrf-token" content="{{ csrf_token() }}">
    <x-calc.head />
</head>
<body class="wc" data-tool-slug="mortgage-calculator">
    <x-site.contech-header />
    <div class="container">
        <div><x-calc.hero title="Konut kredisi limiti hesaplama" crumb="Konut kredisi"></x-calc.hero>
            <div class="form-card">
                <div class="form-group"><label>Evin Ekspertiz Değeri (TL)</label><input type="number" id="val" class="form-control" value="3000000" oninput="calc()"></div>
                <div class="form-group"><label>Aylık Belgelenebilir Hane Geliri (TL)</label><input type="number" id="inc" class="form-control" value="100000" oninput="calc()"></div>
                <div class="form-group"><label>Enerji Sınıfı (BDDK Kuralı)</label><select id="nrg" class="form-control" onchange="calc()"><option value="0.90" selected>A Sınıfı Enerji (Sıfır Bina - %90 Limit)</option><option value="0.80">B/C Sınıfı (İkinci El - %80 Limit)</option></select></div>
            </div>
        </div>
        <div>
            <div class="form-card" style="border-color:var(--primary); position:sticky; top:20px;" data-wc-result>
                <div style="font-family:'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; font-size:24px; color:var(--primary); margin-bottom:15px;">📊 KREDİ KAPASİTESİ</div>
                <div class="res-item"><span>BDDK Ekspertiz Sınırı</span> <strong id="r_bddk">0 TL</strong></div>
                <div class="res-item"><span>Gelire Göre Maks. Taksit (%50)</span> <strong id="r_ins">0 TL/Ay</strong></div>
                <div class="res-item total"><span>ÇEKİLEBİLECEK AZAMİ KREDİ</span> <span id="r_max" data-wc-price>0 TL</span></div>
                <div class="ai-box"><div style="color:#8a6200; font-family:'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; font-size:22px;">🤖 Finansal Uyarılar</div><ul id="ai_notes" style="font-size:13px; color:#0f172a; margin:0; padding-left:15px;"></ul></div>
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
            let val = parseFloat(document.getElementById('val').value)||0; let inc = parseFloat(document.getElementById('inc').value)||0; let nrg = parseFloat(document.getElementById('nrg').value);
            let bddk = val * nrg;
            let maxIns = inc * 0.50; // Bankalar genelde gelirin %50'si kadar taksite izin verir
            // Güncel faiz %3, 120 ay varsayımıyla çekilebilecek kredi:
            let r = 0.03; let n = 120;
            let pmtRate = (r * Math.pow(1+r, n)) / (Math.pow(1+r, n) - 1);
            let incomeBasedLoan = maxIns / pmtRate;
            let finalLoan = Math.min(bddk, incomeBasedLoan);
            
            document.getElementById('r_bddk').innerText = new Intl.NumberFormat('tr-TR').format(Math.round(bddk)) + ' TL'; document.getElementById('r_ins').innerText = new Intl.NumberFormat('tr-TR').format(Math.round(maxIns)) + ' TL/Ay'; document.getElementById('r_max').innerText = new Intl.NumberFormat('tr-TR').format(Math.round(finalLoan)) + ' TL';
            let notesBox = document.getElementById('ai_notes'); notesBox.innerHTML = '';
            if(incomeBasedLoan < bddk) notesBox.innerHTML += '<li style="color:#b91c1c;">Hane halkı geliriniz, evin ekspertiz değerinin izin verdiği limite ulaşmanızı engelliyor. Kefil bularak veya peşinatı artırarak limiti yükseltebilirsiniz.</li>';
            notesBox.innerHTML += '<li>Hesaplamalar %3.00 aylık faiz oranı ve 120 ay vade varsayımıyla yapılmıştır.</li>';
        } document.addEventListener('DOMContentLoaded', calc);
    </script>
<x-calc.kit />
    <x-site.contech-footer />
    <script src="/js/tool-events.js" defer></script>
</body>
</html>
