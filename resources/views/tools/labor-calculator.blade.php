<!DOCTYPE html>
<html lang="tr">
<head>
    <link rel="icon" href="/favicon.ico" type="image/x-icon">
<meta charset="UTF-8"><title>Şantiye İşçilik Maliyeti | WORKSİTEM</title><link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet"><style>:root { --bg: #f8fafc; --card-bg: #ffffff; --border: #e2e8f0; --primary: #101318; --text: #0f172a; --muted: #64748b; --cyan: #8a6200; --green: #059669; --yellow: #d97706;} body { font-family: 'Inter', sans-serif; background: var(--bg); color: var(--text); margin: 0; padding: 0; } .container { max-width: 1200px; margin: 40px auto; padding: 0 4%; display: grid; grid-template-columns: 1fr 1fr; gap: 40px; } @media(max-width: 992px) { .container { grid-template-columns: 1fr; } } .form-card { background: var(--card-bg); border: 1px solid var(--border); border-radius: 12px; padding: 30px; margin-bottom: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);} h1 { font-family:'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; font-size: 38px; color: var(--text); margin-bottom: 10px; } .form-group { margin-bottom: 20px; } .form-group label { display: block; font-size: 13px; font-weight: 700; color: var(--muted); margin-bottom: 8px; text-transform: uppercase; } .form-control { width: 100%; padding: 12px; border-radius: 6px; background: #f8fafc; border: 1px solid var(--border); color: var(--text); font-family: inherit; } .res-item { display: flex; justify-content: space-between; padding: 12px 0; border-bottom: 1px dashed var(--border); font-size: 14px; } .res-item.total { border-bottom: none; font-size: 22px; font-weight: 800; color: var(--yellow); margin-top: 15px; } .ai-box { background: #fef9c3; border: 1px solid #fde047; border-radius: 8px; padding: 20px; margin-top: 20px; }</style>    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body data-tool-slug="labor-calculator">
    <div style="padding:15px 4%; border-bottom:1px solid var(--border); background:#fff;"><a href="/araclar" style="color:var(--text); text-decoration:none; font-family:'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; font-size:24px;">◀ WORKSİTEM ARAÇLAR</a></div>
    <div class="container">
        <div><h1>İşçilik & Taşeron Maliyeti</h1>
            <div class="form-card">
                <div class="form-group"><label>Net Günlük Yevmiye (TL)</label><input type="number" id="wage" class="form-control" value="2500" oninput="calc()"></div>
                <div class="form-group"><label>Çalışılacak Gün Sayısı</label><input type="number" id="days" class="form-control" value="30" oninput="calc()"></div>
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:15px;">
                    <div class="form-group"><label>Günlük Yemek (TL)</label><input type="number" id="food" class="form-control" value="300" oninput="calc()"></div>
                    <div class="form-group"><label>Günlük Yol (TL)</label><input type="number" id="travel" class="form-control" value="150" oninput="calc()"></div>
                </div>
            </div>
        </div>
        <div>
            <div class="form-card" style="border-color:var(--yellow); position:sticky; top:20px;">
                <div style="font-family:'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; font-size:24px; color:var(--yellow); margin-bottom:15px;">📊 GİDER ANALİZİ</div>
                <div class="res-item"><span>Ödenecek Net Yevmiye Toplamı</span> <strong id="r_net">0 TL</strong></div>
                <div class="res-item"><span>SGK ve Vergi (%37.5 Brüt Üzerinden)</span> <strong id="r_sgk">0 TL</strong></div>
                <div class="res-item"><span>Yemek & Yol Gideri</span> <strong id="r_extra">0 TL</strong></div>
                <div class="res-item total"><span>İŞVERENE TOPLAM MALİYET</span> <span id="r_total">0 TL</span></div>
                <div class="ai-box"><div style="color:#b45309; font-family:'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; font-size:22px;">💡 İş Güvenliği Notu</div><ul id="ai_notes" style="font-size:13px; color:#0f172a; margin:0; padding-left:15px;"></ul></div>
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
            let wage = parseFloat(document.getElementById('wage').value)||0; let days = parseFloat(document.getElementById('days').value)||0; let food = parseFloat(document.getElementById('food').value)||0; let travel = parseFloat(document.getElementById('travel').value)||0;
            let net = wage * days;
            // Kaba bir brütten nete / SGK tahmini (Yaklaşık Net'in %50'si kadar SGK ve vergi yükü biner)
            let sgk = net * 0.50; 
            let extra = (food + travel) * days;
            let total = net + sgk + extra;
            
            document.getElementById('r_net').innerText = new Intl.NumberFormat('tr-TR').format(Math.round(net)) + ' TL'; document.getElementById('r_sgk').innerText = new Intl.NumberFormat('tr-TR').format(Math.round(sgk)) + ' TL'; document.getElementById('r_extra').innerText = new Intl.NumberFormat('tr-TR').format(Math.round(extra)) + ' TL'; document.getElementById('r_total').innerText = new Intl.NumberFormat('tr-TR').format(Math.round(total)) + ' TL';
            let notesBox = document.getElementById('ai_notes'); notesBox.innerHTML = '';
            notesBox.innerHTML += '<li>Bir işçinin net yevmiyesinin üzerinde, işverene SGK, stopaj, işsizlik fonu gibi ciddi bir gizli maliyet biner.</li>';
            notesBox.innerHTML += '<li>Giderlere İSG (İş Sağlığı ve Güvenliği) ekipmanı (Baret, Yelek, Emniyet Kemeri) maliyetleri dahil edilmemiştir.</li>';
        } document.addEventListener('DOMContentLoaded', calc);
    </script>
    <script src="/js/tool-events.js" defer></script>
</body>
</html>
