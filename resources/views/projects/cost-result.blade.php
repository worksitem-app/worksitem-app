<!DOCTYPE html>
<html lang="tr">
<head>
    <link rel="icon" href="/favicon.ico" type="image/x-icon">

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tadilat Tahmini Fiyat Aralığı | Kainta İnşaat</title>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root { --bg: #f8fafc; --card-bg: #ffffff; --border: #e2e8f0; --text-dark: #0f172a; --text-muted: #64748b; --yellow: #ffb700; --green: #10b981; }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Inter', sans-serif; background: var(--bg); color: var(--text-dark); }
        .navbar { display: flex; justify-content: space-between; align-items: center; padding: 15px 4%; background: #fff; border-bottom: 1px solid var(--border); }
        .logo-title { font-family: 'Bebas Neue'; font-size: 26px; color: var(--text-dark); text-decoration: none; }
        
        .result-container { max-width: 1000px; margin: 40px auto; padding: 0 4%; }
        
        .range-card { background: var(--card-bg); border-radius: 16px; border: 1px solid var(--border); box-shadow: 0 10px 30px rgba(0,0,0,0.05); padding: 40px; margin-bottom: 30px; text-align: center; position: relative; overflow: hidden; }
        .range-card::before { content: ''; position: absolute; top: 0; left: 0; width: 100%; height: 6px; background: var(--yellow); }
        
        .range-title { font-size: 16px; font-weight: 600; color: var(--text-muted); margin-bottom: 15px; }
        .range-price { font-size: clamp(32px, 5vw, 56px); font-weight: 800; color: var(--text-dark); letter-spacing: -1px; margin-bottom: 10px; }
        .range-typical { font-size: 15px; color: var(--text-muted); font-weight: 500; margin-bottom: 30px; }
        
        .basis-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; text-align: left; background: #f8fafc; padding: 25px; border-radius: 12px; border: 1px solid var(--border); margin-bottom: 30px; }
        .basis-item { display: flex; flex-direction: column; gap: 4px; }
        .basis-label { font-size: 12px; color: var(--text-muted); font-weight: 700; text-transform: uppercase; }
        .basis-value { font-size: 15px; font-weight: 600; color: var(--text-dark); }
        
        .details-box { text-align: left; margin-bottom: 30px; }
        .details-title { font-size: 18px; font-weight: 700; color: var(--text-dark); margin-bottom: 15px; }
        .details-text { font-size: 14px; color: var(--text-muted); line-height: 1.6; margin-bottom: 20px; }
        
        .lists-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 30px; }
        .list-col h4 { font-size: 14px; font-weight: 700; margin-bottom: 15px; color: var(--text-dark); }
        .list-col ul { list-style: none; padding: 0; }
        .list-col ul li { font-size: 13px; color: var(--text-muted); margin-bottom: 10px; position: relative; padding-left: 20px; line-height: 1.4; }
        .list-col ul li::before { content: '•'; position: absolute; left: 0; color: var(--text-dark); font-weight: bold; }
        
        .btn-cta { display: inline-block; width: 100%; background: var(--green); color: #fff; text-decoration: none; padding: 18px; border-radius: 8px; font-family: 'Bebas Neue'; font-size: 24px; letter-spacing: 1px; transition: 0.3s; border: none; cursor: pointer; }
        .btn-cta:hover { transform: translateY(-3px); box-shadow: 0 10px 20px rgba(16, 185, 129, 0.2); }
    </style>
</head>
<body>

    <nav class="navbar">
        <a href="/maliyet-hesapla" class="logo-title"><img src="/brand/kainta-logo.jpg" alt="Kainta İnşaat" style="height:45px; border-radius:4px;"></a>
        <a href="/maliyet-hesapla" style="color: var(--text-muted); text-decoration: none; font-weight: 600; font-size: 14px;">◀ Yeniden Hesapla</a>
    </nav>

    <div class="result-container">
        
        @if(session('success'))
            <div style="background: rgba(16, 185, 129, 0.1); border: 1px solid var(--green); color: var(--green); padding: 18px; border-radius: 8px; margin-bottom: 20px; font-weight: 600; text-align: center;">
                ✅ {{ session('success') }}
            </div>
        @endif

        <div class="range-card">
            <div class="range-title">📈 Tadilatınız için tahmini fiyat aralığı</div>
            
            <div class="range-price">
                {{ number_format($min_cost ?? ($estimatedCost*0.8), 0, ',', '.') }} – {{ number_format($max_cost ?? ($estimatedCost*1.2), 0, ',', '.') }} ₺
            </div>
            <div class="range-typical">Tipik sonuç: yaklaşık {{ number_format($typical_cost ?? $estimatedCost, 0, ',', '.') }} ₺</div>
            
            <div class="basis-grid">
                <div class="basis-item">
                    <span class="basis-label">Mülk Türü</span>
                    <span class="basis-value">{{ $answers['tadilat-mulk-turu'] ?? 'Apartman' }}</span>
                </div>
                <div class="basis-item">
                    <span class="basis-label">Alan</span>
                    <span class="basis-value">{{ $constructionArea ?? 0 }} m²</span>
                </div>
                <div class="basis-item">
                    <span class="basis-label">Yenileme Kapsamı</span>
                    <span class="basis-value">{{ $answers['tadilat-kapsami'] ?? 'Kapsam Belirtilmedi' }}</span>
                </div>
                <div class="basis-item">
                    <span class="basis-label">Kalite Seviyesi</span>
                    <span class="basis-value">{{ $answers['tadilat-kalite'] ?? 'Standart' }}</span>
                </div>
                <div class="basis-item" style="grid-column: span 2;">
                    <span class="basis-label">Seçilen Odalar / İşler</span>
                    <span class="basis-value" style="color: var(--green);">{{ $answers['tadilat-odalar'] ?? 'Tüm Ev' }}</span>
                </div>
            </div>
            
            <div class="details-box">
                <h3 class="details-title">Büyük tadilat projesi</h3>
                <p class="details-text">Tadilat birden fazla odayı ve kalemi kapsadığından, gerçekçi ve kesin bir bütçe belirleyebilmek için yerinde inceleme ve detaylı bir planlama yapmanızı öneririz. Size özel fiyat teklifi için uzmanlarımızla iletişime geçin.</p>
                
                <div class="lists-grid">
                    <div class="list-col">
                        <h4>✅ Tahmine genellikle şunlar dahildir:</h4>
                        <ul>
                            <li>Seçilen işler için işçilik maliyetleri</li>
                            <li>Standart yapı malzemeleri ve sarf malzemeleri</li>
                            <li>Normal yüzey yenileme ve kırım/döküm çalışmaları</li>
                            <li>Nakliye ve genel şantiye giderleri</li>
                        </ul>
                    </div>
                    <div class="list-col">
                        <h4>❌ İçermediği şeyler:</h4>
                        <ul>
                            <li>Tasarım, 3D Render ve iç mimar ücretleri</li>
                            <li>Özel üretim / spesifik tasarım malzemeleri</li>
                            <li>Gizli yapısal onarımlar (çürük tesisat vb.)</li>
                            <li>Hareketli mobilyalar, beyaz eşyalar ve ev aletleri</li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <div style="border-top: 1px solid var(--border); padding-top: 25px;">
                <p style="font-size: 14px; font-weight: 600; color: var(--text-dark); margin-bottom: 15px;">Daha detaylı bir fiyat teklifi mi istiyorsunuz?</p>
                <p style="font-size: 13px; color: var(--text-muted); margin-bottom: 20px;">Kesin fiyat, ancak Kainta İnşaat uzmanlarının yerinde yapacağı ücretsiz inceleme ve rölöve sonrasında netleşir.</p>
                
                <button onclick="document.getElementById('leadModal').style.display='flex'" class="btn-cta">ÜCRETSİZ FİYAT TEKLİFİ İSTEYİN →</button>
            </div>
        </div>
    </div>

    <!-- LEAD MODAL -->
    <div id="leadModal" style="position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.8); z-index:9999; display:none; align-items:center; justify-content:center; backdrop-filter: blur(5px);">
        <div style="background:#fff; border-radius:16px; width:90%; max-width:450px; padding:30px; position:relative;">
            <button type="button" onclick="document.getElementById('leadModal').style.display='none'" style="position:absolute; right:20px; top:20px; background:none; border:none; color:var(--text-muted); font-size:24px; cursor:pointer;">&times;</button>
            <div style="font-family:'Bebas Neue'; font-size:28px; color:var(--yellow); margin-bottom:10px;">ÜCRETSİZ KEŞİF TALEBİ</div>
            <form action="{{ route('leads.store') }}" method="POST">
                @include('components.lead-tracking-fields')
                @csrf
                <input type="hidden" name="project_type" value="tadilat">
                <input type="text" name="name" required placeholder="Adınız Soyadınız" style="width:100%; padding:14px; border:1px solid var(--border); border-radius:8px; margin-bottom:15px;">
                <input type="tel" name="phone" required placeholder="Telefon Numaranız" style="width:100%; padding:14px; border:1px solid var(--border); border-radius:8px; margin-bottom:15px;">
                <textarea name="notes" rows="3" placeholder="Ek Notlarınız (Opsiyonel)" style="width:100%; padding:14px; border:1px solid var(--border); border-radius:8px; margin-bottom:20px;"></textarea>
                                @include('components.lead-consent-fields')

<button type="submit" style="width:100%; background:var(--green); color:#fff; padding:16px; font-family:'Bebas Neue'; font-size:22px; border:none; border-radius:8px; cursor:pointer;">TALEBİ GÖNDER →</button>
            </form>
        </div>
    </div>
</body>
</html>
