<!DOCTYPE html>
<html lang="tr">
<head>
    <link rel="icon" href="/favicon.ico" type="image/x-icon">

    <meta charset="UTF-8">
    <title>SGK Asgari İşçilik & Birim Maliyet Hesaplayıcı | WORKSİTEM</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
    <style>
        body { font-family:'Inter', sans-serif; background:#fdfdfd; color:#111827; margin:0; padding-bottom:60px;}
        .navbar { display: flex; justify-content: space-between; align-items: center; padding: 16px 5%; background: #ffffff; border-bottom: 1px solid #e5e7eb; position: sticky; top: 0; z-index: 50;}
        .logo { font-weight:800; font-size:20px; text-decoration:none; color:#111827; display:flex; align-items:center; gap:8px;}
        .logo-box { background:#2563eb; color:#fff; padding:4px 8px; border-radius:6px; font-size:14px; }
        
        .container { max-width:900px; margin:40px auto; padding:30px; background:#fff; border:1px solid #e5e7eb; border-radius:16px; box-shadow:0 4px 6px rgba(0,0,0,0.05); }
        h1 { font-size:24px; font-weight:800; margin-bottom:10px; color:#1e3a8a;}
        .subtitle { color:#6b7280; margin-bottom:30px; line-height:1.5; }
        
        .grid-2 { display:grid; grid-template-columns:1fr 1fr; gap:15px; margin-bottom:15px; }
        .grid-3 { display:grid; grid-template-columns:1fr 1fr 1fr; gap:15px; margin-bottom:15px; }
        .form-group label { display:block; font-size:12px; font-weight:700; color:#6b7280; margin-bottom:6px; text-transform:uppercase;}
        input, select { width:100%; padding:12px; border:1px solid #e5e7eb; border-radius:8px; font-family:inherit; font-size:14px; background:#f9fafb;}
        input:focus, select:focus { outline:none; border-color:#2563eb; background:#fff; }
        
        .section-title { font-size:16px; font-weight:800; color:#111827; margin:30px 0 15px 0; padding-bottom:8px; border-bottom:2px solid #e5e7eb;}
        
        .result-box { background:#f0fdf4; border:1px solid #bbf7d0; padding:20px; border-radius:12px; margin-top:20px; display:grid; grid-template-columns:1fr 1fr; gap:20px;}
        .res-col { display:flex; flex-direction:column; gap:8px;}
        .res-col span { font-size:13px; font-weight:700; color:#166534; text-transform:uppercase; }
        .res-val { font-size:20px; font-weight:800; color:#14532d; }
        
        .danger-box { background:#fef2f2; border:1px solid #fecdd3; padding:20px; border-radius:12px; margin-top:20px; text-align:center; }
        .danger-box span { font-size:13px; font-weight:700; color:#be123c; text-transform:uppercase; }
        .danger-val { font-size:32px; font-weight:800; color:#9f1239; margin-top:5px; }

        .cta { display:block; width:100%; padding:16px; background:#1e3a8a; color:#fff; text-align:center; font-weight:800; text-decoration:none; border-radius:8px; margin-top:30px; transition:all 0.2s;}
        .cta:hover { background:#1e40af; transform:translateY(-2px); }
        
        table { width:100%; border-collapse:collapse; margin-top:15px; font-size:13px; }
        th, td { border:1px solid #e5e7eb; padding:10px; text-align:left; }
        th { background:#f3f4f6; color:#4b5563; font-weight:700;}
    </style>
</head>
<body>
    <nav class="navbar"><a href="/araclar" class="logo"><span class="logo-box">◀</span> WORKSİTEM Hukuk & Mevzuat</a></nav>

    <div class="container">
        <h1>🏛️ SGK İlişiksizlik Belgesi ve Asgari İşçilik</h1>
        <p class="subtitle">Resmi Gazete yapı yaklaşık birim maliyetleri üzerinden, iskan ruhsatı alabilmeniz için ödemeniz gereken eksik prim (Fark SPEK) tutarını hesaplayın.</p>
        
        <div class="section-title">1. YAPI BİLGİLERİ VE RAYİÇ BEDEL</div>
        <div class="grid-3">
            <div class="form-group">
                <label>Hesaplama Yılı</label>
                <select id="year" onchange="calc()">
                    <option value="2026">2026</option>
                    <option value="2025">2025</option>
                    <option value="2024">2024</option>
                </select>
            </div>
            <div class="form-group">
                <label>Sınıf</label>
                <select id="classType" onchange="calc()">
                    <option value="I">I. Sınıf</option>
                    <option value="II">II. Sınıf</option>
                    <option value="III" selected>III. Sınıf</option>
                    <option value="IV">IV. Sınıf</option>
                    <option value="V">V. Sınıf</option>
                </select>
            </div>
            <div class="form-group">
                <label>Grup</label>
                <select id="groupType" onchange="calc()">
                    <option value="A">A Grubu</option>
                    <option value="B" selected>B Grubu</option>
                    <option value="C">C Grubu</option>
                </select>
            </div>
        </div>

        <div class="grid-2">
            <div class="form-group">
                <label>İnşaat Türü</label>
                <select id="constType" onchange="calc()">
                    <option value="9.00">Karkas / Betonarme Bina (%9.00)</option>
                    <option value="12.00">Yığma İnşaat (%12.00)</option>
                    <option value="7.00">Prefabrik (%7.00)</option>
                </select>
            </div>
            <div class="form-group">
                <label>Toplam İnşaat Alanı (m²)</label>
                <input type="number" id="area" value="1000" oninput="calc()">
            </div>
        </div>

        <div class="section-title">2. SGK BİLDİRİM VE PRİM MATRAHI</div>
        <div class="grid-2">
            <div class="form-group">
                <label>Kuruma Önceden Bildirilen Matrah (₺)</label>
                <input type="number" id="declared" value="0" placeholder="Örn: 150000" oninput="calc()">
            </div>
            <div class="form-group">
                <label>Uygulanacak İndirimli Asgari İşçilik Oranı</label>
                <input type="text" id="discountedRate" value="% 6.75" disabled>
                <small style="color:#9ca3af; font-size:11px;">(Sistematik %25 Eksiltme Uygulanır)</small>
            </div>
        </div>

        <div class="result-box">
            <div class="res-col">
                <span>Yapı Birim Maliyeti (m²)</span>
                <div class="res-val" id="r_unit">0,00 ₺</div>
            </div>
            <div class="res-col">
                <span>Resmi İnşaat Maliyeti</span>
                <div class="res-val" id="r_total">0,00 ₺</div>
            </div>
            <div class="res-col">
                <span>Bildirilmesi Gereken Asgari İşçilik</span>
                <div class="res-val" id="r_req">0,00 ₺</div>
            </div>
            <div class="res-col">
                <span>Fark SPEK (Eksik Bildirim)</span>
                <div class="res-val" id="r_diff">0,00 ₺</div>
            </div>
        </div>

        <div class="danger-box" id="dangerBox">
            <span>ÖDENMESİ GEREKEN CEZALI PRİM TUTARI (FARK SGK PRİMİ)</span>
            <div class="danger-val" id="r_penalty">0,00 ₺</div>
            <p style="font-size:12px; color:#be123c; margin-top:5px;">*%37.5 prim oranı (Kısa vadeli sigorta kolları, MYÖ, GSS, İşsizlik) üzerinden hesaplanmıştır.</p>
        </div>

        <a href="/maliyet-hesapla?service=iskan-ve-sgk" class="cta">Kainta İnşaat SGK Danışmanlığı ve İskan Takibi İçin Başvur →</a>
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
        // ÇŞB Temsili Birim Fiyat Tablosu (Simülasyon - Veritabanına Bağlanabilir)
        const unitCosts = {
            '2026': { 'III_B': 16000, 'IV_A': 21050, 'V_A': 32000, 'I_A': 3500 },
            '2025': { 'III_B': 12500, 'IV_A': 17500, 'V_A': 25000, 'I_A': 2500 },
            '2024': { 'III_B': 8500,  'IV_A': 12000, 'V_A': 18000, 'I_A': 1500 }
        };

        function calc() {
            let year = document.getElementById('year').value;
            let cType = document.getElementById('classType').value;
            let gType = document.getElementById('groupType').value;
            let key = cType + '_' + gType;
            
            // Eğer matriste tam eşleşme yoksa ortalama birim fiyat atıyoruz (SaaS Simülasyonu)
            let unitPrice = unitCosts[year][key] || (unitCosts[year]['III_B'] * 0.9);

            let area = parseFloat(document.getElementById('area').value) || 0;
            let baseRate = parseFloat(document.getElementById('constType').value);
            let declared = parseFloat(document.getElementById('declared').value) || 0;

            // SGK %25 indirim uygular
            let appliedRate = baseRate * 0.75; 
            document.getElementById('discountedRate').value = '% ' + appliedRate.toFixed(2);

            let totalCost = area * unitPrice;
            let reqLabor = totalCost * (appliedRate / 100);
            
            let diff = reqLabor - declared;
            if (diff < 0) diff = 0;

            // SGK Fark primi ortalama %37.5 alınır (Asgari işçilik farkı tahsilatı)
            let penalty = diff * 0.375;

            document.getElementById('r_unit').innerText = new Intl.NumberFormat('tr-TR').format(unitPrice) + ' ₺';
            document.getElementById('r_total').innerText = new Intl.NumberFormat('tr-TR').format(totalCost) + ' ₺';
            document.getElementById('r_req').innerText = new Intl.NumberFormat('tr-TR').format(Math.round(reqLabor)) + ' ₺';
            document.getElementById('r_diff').innerText = new Intl.NumberFormat('tr-TR').format(Math.round(diff)) + ' ₺';
            
            document.getElementById('r_penalty').innerText = new Intl.NumberFormat('tr-TR').format(Math.round(penalty)) + ' ₺';
            
            if(penalty === 0) {
                document.getElementById('dangerBox').style.background = '#f0fdf4';
                document.getElementById('dangerBox').style.borderColor = '#bbf7d0';
                document.querySelector('.danger-box span').style.color = '#166534';
                document.getElementById('r_penalty').style.color = '#14532d';
                document.querySelector('.danger-box span').innerText = 'EKSİK BİLDİRİM YOK (İLİŞİKSİZLİK BELGESİ ALINABİLİR)';
            } else {
                document.getElementById('dangerBox').style.background = '#fef2f2';
                document.getElementById('dangerBox').style.borderColor = '#fecdd3';
                document.querySelector('.danger-box span').style.color = '#be123c';
                document.getElementById('r_penalty').style.color = '#9f1239';
                document.querySelector('.danger-box span').innerText = 'ÖDENMESİ GEREKEN CEZALI PRİM TUTARI (FARK SGK PRİMİ)';
            }
        }
        document.addEventListener('DOMContentLoaded', calc);
    </script>
</body>
</html>
