<!DOCTYPE html>
<html lang="tr">
<head>
    <link rel="icon" href="/favicon.ico" type="image/x-icon">
<meta charset="UTF-8"><title>Asansör Mevzuat Kontrolü | WORKSİTEM</title><link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet"><style>:root { --bg: #f8fafc; --card-bg: #ffffff; --border: #e2e8f0; --primary: #101318; --text: #0f172a; --muted: #64748b; --cyan: #8a6200; --green: #059669; --red:#ef4444;} body { font-family: 'Inter', sans-serif; background: var(--bg); color: var(--text); margin: 0; padding: 0; } .container { max-width: 1200px; margin: 40px auto; padding: 0 4%; display: grid; grid-template-columns: 1fr 1fr; gap: 40px; } @media(max-width: 992px) { .container { grid-template-columns: 1fr; } } .form-card { background: var(--card-bg); border: 1px solid var(--border); border-radius: 12px; padding: 30px; margin-bottom: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);} h1 { font-family:'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; font-size: 38px; color: var(--text); margin-bottom: 10px; } .form-group { margin-bottom: 20px; } .form-group label { display: block; font-size: 13px; font-weight: 700; color: var(--muted); margin-bottom: 8px; text-transform: uppercase; } .form-control { width: 100%; padding: 12px; border-radius: 6px; background: #f8fafc; border: 1px solid var(--border); color: var(--text); font-family: inherit; } .res-item { display: flex; justify-content: space-between; padding: 12px 0; border-bottom: 1px dashed var(--border); font-size: 14px; } .res-item.total { border-bottom: none; font-size: 24px; font-weight: 800; color: var(--primary); margin-top: 15px; } .ai-box { background: #fff8e1; border: 1px solid #fde68a; border-radius: 8px; padding: 20px; margin-top: 20px; }</style>    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body data-tool-slug="elevator-calculator">
    <div style="padding:15px 4%; border-bottom:1px solid var(--border); background:#fff;"><a href="/araclar" style="color:var(--text); text-decoration:none; font-family:'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; font-size:24px;">◀ WORKSİTEM ARAÇLAR</a></div>
    <div class="container">
        <div><h1>Asansör Zorunluluğu Kontrolü</h1>
            <div class="form-card">
                <div class="form-group"><label>Bina Toplam Kat Sayısı (Zemin + Katlar)</label><input type="number" id="floors" class="form-control" value="3" oninput="calc()"></div>
                <div class="form-group"><label>Bina Kat Yüksekliği (m) (Zeminden en üst döşemeye)</label><input type="number" id="h" class="form-control" value="10.5" step="0.1" oninput="calc()"></div>
                <div class="form-group">
                    <label>Bina Tipi</label>
                    <select id="type" class="form-control" onchange="calc()">
                        <option value="konut" selected>Konut / Mesken</option>
                        <option value="ticari">Ticari / Ofis / Kamu</option>
                    </select>
                </div>
            </div>
        </div>
        <div>
            <div class="form-card" style="border-color:var(--primary); position:sticky; top:20px;">
                <div style="font-family:'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; font-size:24px; color:var(--primary); margin-bottom:15px;">📊 PLANLI ALANLAR İMAR YÖNETMELİĞİ</div>
                <div class="res-item total" style="justify-content:center; text-align:center;">
                    <span id="r_result" style="color:var(--green); font-size:32px;">ZORUNLU DEĞİL</span>
                </div>
                <div class="ai-box"><div style="color:#8a6200; font-family:'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; font-size:22px;">🤖 Yönetmelik Yorumu</div><ul id="ai_notes" style="font-size:13px; color:#0f172a; margin:0; padding-left:15px; line-height:1.6;"></ul></div>
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
            let floors = parseInt(document.getElementById('floors').value)||0; 
            let h = parseFloat(document.getElementById('h').value)||0; 
            let type = document.getElementById('type').value;

            // Kural: 4 kat ve üzeri (Zemin+3) VEYA iskân edilebilir bina yüksekliği 13.50 m'yi aşan binalarda zorunludur.
            // 10 kat (Zemin+9) üzeri ise EN AZ 2 asansör zorunludur.
            let required = false;
            let doubleReq = false;

            if(floors >= 4 || h >= 13.50) required = true;
            if(floors >= 10) doubleReq = true;
            if(type === 'ticari' && (floors >= 3)) required = true; // Ticari yapılarda daha sıkı kurallar uygulanabiliyor.

            let resEl = document.getElementById('r_result');
            if(doubleReq) {
                resEl.innerText = "EN AZ 2 ASANSÖR ZORUNLU";
                resEl.style.color = "var(--red)";
            } else if (required) {
                resEl.innerText = "ASANSÖR ZORUNLU";
                resEl.style.color = "var(--red)";
            } else {
                resEl.innerText = "ZORUNLU DEĞİL";
                resEl.style.color = "var(--green)";
            }

            let notesBox = document.getElementById('ai_notes'); notesBox.innerHTML = '';
            notesBox.innerHTML += '<li>Planlı Alanlar İmar Yönetmeliği Madde 34\'e göre; tek bağımsız bölümlü konutlar hariç, iskan edilebilir kat sayısı 4 ve daha fazla olan veya bina yüksekliği 13.50 metreyi aşan yapılarda asansör tesisi zorunludur.</li>';
            if(required && !doubleReq) notesBox.innerHTML += '<li>Zorunlu olan bu tek asansörün kabin ebadı engelli erişimine uygun olarak en az 1.20 m x 1.20 m olmak zorundadır.</li>';
            if(doubleReq) notesBox.innerHTML += '<li style="color:#b91c1c; font-weight:bold;">10 kat ve üzeri binalarda asansörlerden en az bir tanesinin yük/sedye taşıma (min. 1.20 m x 2.10 m) standartlarında olması şarttır.</li>';
        } document.addEventListener('DOMContentLoaded', calc);
    </script>
    <script src="/js/tool-events.js" defer></script>
</body>
</html>
