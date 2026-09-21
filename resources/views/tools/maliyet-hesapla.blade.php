<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resmi Keşif & Maliyet Hesapla | WORKSİTEM</title>
    <link rel="icon" href="/favicon.ico" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #050505; --surface: #111111; --border: rgba(255,255,255,0.1);
            --yellow: #ffb800; --yellow-soft: rgba(255,184,0,0.1);
            --white: #fff; --gray: #a1a1aa; --radius: 16px;
            --transition: all 0.3s ease;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Inter', sans-serif; }
        body { background: var(--bg); color: var(--white); -webkit-font-smoothing: antialiased; }
        
        .navbar { padding: 20px; text-align: center; border-bottom: 1px solid var(--border); background: #000; }
        .navbar img { height: 35px; }

        .container { max-width: 800px; margin: 60px auto; padding: 0 20px; }
        .header { text-align: center; margin-bottom: 40px; }
        .header h1 { font-size: clamp(28px, 4vw, 42px); font-weight: 900; letter-spacing: -1px; margin-bottom: 10px;}
        .header h1 span { color: var(--yellow); }
        .header p { color: var(--gray); font-size: 16px; line-height: 1.6; }

        .calc-card { background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius); padding: 40px; box-shadow: 0 20px 40px rgba(0,0,0,0.5); }
        
        .form-group { margin-bottom: 25px; }
        .form-group label { display: block; font-size: 13px; font-weight: 700; color: var(--gray); text-transform: uppercase; margin-bottom: 10px; letter-spacing: 1px;}
        .form-control { width: 100%; background: #000; border: 1px solid var(--border); color: #fff; padding: 16px; border-radius: 10px; font-size: 16px; font-weight: 600; outline: none; transition: var(--transition);}
        .form-control:focus { border-color: var(--yellow); box-shadow: 0 0 0 4px var(--yellow-soft); }
        select.form-control { appearance: none; cursor: pointer; background-image: url("data:image/svg+xml,%3Csvg width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%23ffb800' stroke-width='3' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 16px center;}

        .btn-calc { width: 100%; background: var(--yellow); color: #000; border: none; padding: 20px; border-radius: 10px; font-size: 18px; font-weight: 900; cursor: pointer; text-transform: uppercase; letter-spacing: 1px; transition: var(--transition); margin-top: 10px;}
        .btn-calc:hover { background: #fff; transform: translateY(-2px); }

        /* Sonuç Ekranı */
        #resultArea { display: none; margin-top: 40px; padding-top: 40px; border-top: 1px dashed var(--border); animation: fadeIn 0.5s ease; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(20px);} to { opacity: 1; transform: translateY(0); } }
        
        .result-box { background: #000; border: 1px solid var(--yellow); border-radius: 12px; padding: 30px; text-align: center; position: relative;}
        .result-badge { position: absolute; top: -12px; left: 50%; transform: translateX(-50%); background: var(--yellow); color: #000; font-size: 11px; font-weight: 900; padding: 4px 12px; border-radius: 100px; text-transform: uppercase; letter-spacing: 1px;}
        .price-val { font-size: 48px; font-weight: 900; color: #fff; letter-spacing: -1px; margin: 15px 0 5px;}
        .price-lbl { color: var(--gray); font-size: 14px; font-weight: 500; }
        
        .breakdown { display: flex; justify-content: space-between; margin-top: 20px; padding-top: 20px; border-top: 1px solid var(--border); font-size: 13px; color: var(--gray); text-align: left;}
        .breakdown strong { color: #fff; }

        .lead-form { margin-top: 40px; }
        .lead-form h3 { font-size: 20px; margin-bottom: 20px; color: #fff;}
    </style>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body data-tool-slug="maliyet-hesapla">

    <header class="navbar">
        <a href="/"><img src="/brand/kainta-logo.jpg" alt="Kainta İnşaat" style="border-radius: 4px;"></a>
    </header>

    <div class="container">
        <div class="header">
            <h1>Kainta İnşaat <span>Uygulama Keşfi</span></h1>
            <p>2026 Çevre ve Şehircilik Bakanlığı (ÇŞB) yapı sınıfları referans alınarak, Kainta İnşaat'ın anahtar teslim güncel inşaat yapım bedelini hesaplayın.</p>
        </div>

        <div class="calc-card">
            <div class="form-group">
                <label>ÇŞB Yapı Sınıfı ve Grubu</label>
                <select id="csbClass" class="form-control">
                    <option value="" disabled selected>Lütfen binanızın sınıfını seçin...</option>
                    <option value="3A">3. Sınıf A Grubu (Konutlar, max 4 kat)</option>
                    <option value="3B">3. Sınıf B Grubu (Asansörlü/Kaloriferli Konutlar)</option>
                    <option value="4A">4. Sınıf A Grubu (Nitelikli Yüksek Konutlar)</option>
                    <option value="4B">4. Sınıf B Grubu (İş Merkezleri, Alışveriş Merkezleri)</option>
                    <option value="4C">4. Sınıf C Grubu (Oteller, Hastaneler, Bankalar)</option>
                    <option value="5A">5. Sınıf A Grubu (Lüks Çok Katlı Kompleksler)</option>
                    <option value="5B">5. Sınıf B Grubu (Kongre Merkezleri, Stadyumlar)</option>
                </select>
            </div>

            <div class="form-group">
                <label>Toplam Brüt İnşaat Alanı (m²)</label>
                <input type="number" id="areaInput" class="form-control" placeholder="Örn: 1200" min="1">
            </div>

            <button class="btn-calc" onclick="calculateKainta()">Maliyeti Hesapla</button>

            <!-- HESAPLAMA SONUCU -->
            <div id="resultArea">
                <div class="result-box">
                    <div class="result-badge">Kainta İnşaat Anahtar Teslim Bütçesi</div>
                    <div class="price-val" id="totalCost">0 ₺</div>
                    <div class="price-lbl">KDV Hariç Tahmini Toplam Maliyet</div>
                    
                    <div class="breakdown">
                        <div>ÇŞB Taban Birim Fiyatı:<br><strong id="basePriceInfo">0 ₺ / m²</strong></div>
                        <div style="text-align:right;">Kainta İnşaat Özel Sektör Çarpanı:<br><strong style="color:var(--yellow);">x 1.40</strong></div>
                    </div>
                    <div class="breakdown" style="border-top:none; padding-top:10px; margin-top:0;">
                        <div>Uygulama Birim Fiyatı:<br><strong id="kaintaPriceInfo">0 ₺ / m²</strong></div>
                        <div style="text-align:right;">KDV (%20):<br><strong id="taxInfo">0 ₺</strong></div>
                    </div>
                </div>

                <!-- CRM LEAD FORMU -->
                <div class="lead-form">
                    <h3>Bu Fiyatı Teklife Dönüştürün</h3>
                    <form action="/leads/store" method="POST">
                        @include('components.lead-tracking-fields')
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" name="source" value="Kainta_Maliyet_Hesapla">
                        <input type="hidden" id="hiddenData" name="project_data" value="">
                        
                        <div class="form-group">
                            <input type="text" name="name" class="form-control" placeholder="Adınız Soyadınız" required>
                        </div>
                        <div class="form-group">
                            <input type="tel" name="phone" class="form-control" placeholder="Telefon Numaranız" required>
                        </div>
                                        @include('components.lead-consent-fields')

<button type="submit" class="btn-calc" style="background:#fff; color:#000;">Yetkili Teklifi İste →</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // 2026 ÇŞB TAHMİNİ BİRİM FİYATLARI (KDV HARİÇ - TL/m2)
        const CSB_PRICES = {
            "3A": 12500, // Konut 4 kata kadar
            "3B": 17800, // Asansörlü kaloriferli konut
            "4A": 23500, // Nitelikli yüksek konut
            "4B": 28400, // İş merkezi, AVM
            "4C": 34200, // Otel, Hastane
            "5A": 42500, // Lüks kompleks
            "5B": 51000  // Mega yapılar
        };

        // KAINTA İNŞAAT UYGULAMA ÇARPANI
        const KAINTA_MULTIPLIER = 1.40;

        function calculateKainta() {
            const cls = document.getElementById('csbClass').value;
            const area = parseFloat(document.getElementById('areaInput').value);

            if(!cls || isNaN(area) || area <= 0) {
                alert('Lütfen yapı sınıfını seçin ve geçerli bir alan (m²) girin.');
                return;
            }

            // 1. Devletin Açıkladığı Ham Fiyat
            const basePrice = CSB_PRICES[cls];
            
            // 2. Müteahhit Kârı, Şantiye Gideri ve Lüks İşçilik Çarpanı (x1.40)
            const kaintaUnitPrice = basePrice * KAINTA_MULTIPLIER;
            
            // 3. Toplamlar
            const totalCost = kaintaUnitPrice * area;
            const tax = totalCost * 0.20;

            // Para Formatlayıcı
            const fmt = new Intl.NumberFormat('tr-TR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

            // Ekrana Yazdırma
            document.getElementById('totalCost').innerText = fmt.format(totalCost) + ' ₺';
            document.getElementById('basePriceInfo').innerText = fmt.format(basePrice) + ' ₺ / m²';
            document.getElementById('kaintaPriceInfo').innerText = fmt.format(kaintaUnitPrice) + ' ₺ / m²';
            document.getElementById('taxInfo').innerText = fmt.format(tax) + ' ₺';

            // Hidden Form Data for CRM
            const summary = `Sınıf: ${cls}, Alan: ${area}m2, ÇŞB Birim: ${basePrice}, Çarpan: 1.40, Kainta Maliyet: ${totalCost} TL`;
            document.getElementById('hiddenData').value = summary;

            // Sonuç Ekranını Göster
            document.getElementById('resultArea').style.display = 'block';
            
            // Yumuşak kaydırma
            setTimeout(() => {
                document.getElementById('resultArea').scrollIntoView({ behavior: 'smooth' });
            }, 100);
        }
    </script>
    <script src="/js/tool-events.js" defer></script>
</body>
</html>
