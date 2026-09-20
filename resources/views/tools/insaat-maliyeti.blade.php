<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- YÜKSEK SEO & CTR ODAKLI ETİKETLER -->
    <title>Müteahhitler İçin İnşaat Keşif ve Maliyet Hesaplayıcı (2026) | WORKSİTEM</title>
    <meta name="description" content="Müteahhitler ve mühendisler için geliştirilmiş B2B inşaat maliyet hesaplama aracı. Güncel piyasa rayiçleriyle malzeme, işçilik, ekipman ve resmî harç bütçenizi çıkarın, profesyonel fizibilite raporunuzu anında indirin.">
    <meta name="keywords" content="inşaat maliyeti hesaplama, müteahhit keşif aracı, şantiye bütçesi, B2B inşaat fizibilite, 2026 inşaat maliyetleri, worksitem pro">
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        :root { --bg: #f3f4f6; --surface: #ffffff; --text: #0f172a; --muted: #64748b; --line: #e2e8f0; --accent: #2563eb; --radius: 12px; }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Inter', sans-serif; background: var(--bg); color: var(--text); padding-bottom: 60px; }
        .header { background: #fff; padding: 15px 20px; border-bottom: 1px solid var(--line); display: flex; justify-content: space-between; position: sticky; top:0; z-index:10;}
        
        /* SEO BAŞLIK ALANI */
        .page-header { text-align: center; max-width: 900px; margin: 40px auto 20px; padding: 0 20px; }
        .page-header h1 { font-size: 32px; font-weight: 900; letter-spacing: -1px; margin: 15px 0 10px; color: #0f172a; }
        .page-header p { font-size: 15px; color: var(--muted); line-height: 1.6; }
        .ai-badge { display: inline-block; background: rgba(37, 99, 235, 0.1); color: var(--accent); padding: 6px 16px; border-radius: 100px; font-size: 13px; font-weight: 900; text-transform: uppercase; letter-spacing: 0.5px; }

        .container { max-width: 1200px; margin: 20px auto 30px; padding: 0 20px; display: flex; gap: 30px; align-items: flex-start; }
        @media(max-width: 900px) { .container { flex-direction: column; } }
        
        .sidebar-tabs { width: 250px; background: #fff; border-radius: var(--radius); border: 1px solid var(--line); padding: 15px 0; flex-shrink: 0; position: sticky; top: 80px; }
        .tab-btn { display: block; width: 100%; text-align: left; padding: 15px 25px; background: none; border: none; font-family: inherit; font-size: 14px; font-weight: 700; color: var(--muted); cursor: pointer; border-left: 3px solid transparent; transition: .2s; }
        .tab-btn:hover { background: #f8fafc; color: var(--text); }
        .tab-btn.active { background: #eff6ff; color: var(--accent); border-left-color: var(--accent); }
        
        .content-area { flex-grow: 1; background: #fff; border-radius: var(--radius); border: 1px solid var(--line); padding: 30px; min-height: 500px; }
        .tab-content { display: none; animation: fadeIn 0.3s ease; }
        .tab-content.active { display: block; }
        
        h2 { font-size: 20px; font-weight: 900; margin-bottom: 5px; }
        p.desc { font-size: 13px; color: var(--muted); margin-bottom: 25px; line-height: 1.5; }
        
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; font-size: 12px; font-weight: 800; margin-bottom: 8px; color: #334155; }
        .form-control { width: 100%; height: 45px; border: 1px solid var(--line); border-radius: 8px; padding: 0 15px; font-size: 14px; font-family: inherit; background: #f8fafc; outline: none; transition: .2s; }
        .form-control:focus { border-color: var(--accent); background: #fff; }
        
        .item-row { display: grid; grid-template-columns: 2fr 1fr 1fr 1fr; gap: 15px; align-items: end; padding: 15px; border: 1px solid var(--line); border-radius: 8px; margin-bottom: 15px; background: #fafafa; }
        .item-title { font-size: 14px; font-weight: 800; color: var(--text); margin-bottom: 5px; display: block; }
        .item-sub { font-size: 11px; color: var(--muted); }
        .calc-box { background: #e2e8f0; padding: 12px; border-radius: 6px; font-size: 13px; font-weight: 700; text-align: right; height: 45px; display: flex; align-items: center; justify-content: flex-end; }
        
        .summary-card { background: #0f172a; color: #fff; border-radius: 12px; padding: 30px; margin-top: 20px; }
        .summary-row { display: flex; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid rgba(255,255,255,0.1); font-size: 14px; }
        .summary-row:last-child { border-bottom: none; }
        .summary-total { font-size: 24px; font-weight: 900; color: #38bdf8; padding-top: 20px; margin-top: 10px; border-top: 2px dashed rgba(255,255,255,0.2); }
        .btn-action { background: var(--accent); color: #fff; border: none; padding: 12px 20px; border-radius: 8px; font-weight: 800; cursor: pointer; width: 100%; margin-top: 20px; transition: .2s; }
        .btn-action:hover { opacity: 0.9; }
        
        @keyframes fadeIn { from { opacity: 0; transform: translateY(5px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</head>
<body>

<header class="header">
    <a href="/araclar" style="text-decoration:none; color:var(--muted); font-weight:700;">← Araçlara Dön</a>
    <img src="/200.png" height="30" alt="WORKSİTEM">
</header>

<!-- YENİ: SEO ODAKLI H1 BAŞLIK ALANI -->
<div class="page-header">
    <span class="ai-badge">🏢 AI Destekli B2B İnşaat Simülatörü</span>
    <h1>Profesyonel Şantiye Maliyeti ve Fizibilite Motoru</h1>
    <p>Müteahhitler, mimarlar ve proje yöneticileri için tasarlanmış kapsamlı keşif aracı. Malzeme sarfiyatlarını, işçilik yevmiyelerini ve resmî harçları hesaplayın; ticari kârınızı netleştirin.</p>
</div>

<div class="container">
    <div class="sidebar-tabs">
        <button class="tab-btn active" onclick="openTab('tab-proje')">📋 1. Proje Bilgileri</button>
        <button class="tab-btn" onclick="openTab('tab-hukuki')">⚖️ 2. Hukuki & Resmî Belgeler</button>
        <button class="tab-btn" onclick="openTab('tab-malzeme')">🧱 3. Malzemeler (Otomatik)</button>
        <button class="tab-btn" onclick="openTab('tab-isgucu')">👷 4. İş Gücü (Taşeron)</button>
        <button class="tab-btn" onclick="openTab('tab-ekipman')">🚜 5. Ekipman & Makine</button>
        <button class="tab-btn" onclick="openTab('tab-ozet')">📊 6. Kesin Özet Raporu</button>
    </div>

    <div class="content-area">
        
        <!-- SEKM 1 -->
        <div id="tab-proje" class="tab-content active">
            <h2>İnşaat Projesi Bilgileri</h2>
            <p class="desc">Toplam inşaat alanını ve ticari çarpanları girin. Diğer tüm sekmeler bu metrekareye göre standart tüketim miktarlarını otomatik hesaplayacaktır.</p>
            
            <div class="form-grid">
                <div class="form-group"><label>Proje Adı</label><input type="text" id="pName" class="form-control" value="Örnek Konut Projesi"></div>
                <div class="form-group">
                    <label>Proje Türü</label>
                    <select id="pType" class="form-control">
                        <option value="konut">Konut / Apartman</option>
                        <option value="ticari">Ticari / Ofis</option>
                        <option value="endustri">Endüstriyel Tesis</option>
                    </select>
                </div>
                <div class="form-group"><label>Toplam İnşaat Alanı (m²)</label><input type="number" id="pArea" class="form-control" value="1000" oninput="calculateAll()"></div>
                <div class="form-group"><label>Tahmini Proje Süresi (Ay)</label><input type="number" id="pDuration" class="form-control" value="12"></div>
                <div class="form-group"><label>Şantiye Genel Giderleri (%)</label><input type="number" id="pOverhead" class="form-control" value="8" oninput="calculateAll()"></div>
                <div class="form-group"><label>Müteahhit Kâr Marjı (%)</label><input type="number" id="pProfit" class="form-control" value="15" oninput="calculateAll()"></div>
            </div>
            <button class="btn-action" onclick="openTab('tab-hukuki')" style="width:auto; float:right;">İleri: Hukuki Belgeler →</button>
        </div>

        <!-- SEKM 2 -->
        <div id="tab-hukuki" class="tab-content">
            <h2>Resmî Harçlar ve Proje Giderleri</h2>
            <p class="desc">Ruhsat, yapı denetim ve mimari/statik tasarım bedelleri gibi yasal maliyetleri girin.</p>
            
            <div class="item-row">
                <div><span class="item-title">Mimari & Statik Proje Çizimleri</span><span class="item-sub">Tüm disiplinlerin tasarım bedeli</span></div>
                <div class="form-group"><label>Birim</label><input type="text" class="form-control" value="Paket" disabled></div>
                <div class="form-group"><label>Tutar (TL)</label><input type="number" id="legProj" class="form-control val-leg" value="350000" oninput="calculateAll()"></div>
                <div><label>Satır Toplamı</label><div class="calc-box" id="tot-legProj">0 TL</div></div>
            </div>
            
            <div class="item-row">
                <div><span class="item-title">Yapı Denetim Hizmet Bedeli</span><span class="item-sub">Bakanlık tarifesi (Yaklaşık)</span></div>
                <div class="form-group"><label>Birim</label><input type="text" class="form-control" value="Paket" disabled></div>
                <div class="form-group"><label>Tutar (TL)</label><input type="number" id="legYapi" class="form-control val-leg" value="250000" oninput="calculateAll()"></div>
                <div><label>Satır Toplamı</label><div class="calc-box" id="tot-legYapi">0 TL</div></div>
            </div>

            <div class="item-row">
                <div><span class="item-title">Belediye Ruhsat & İskan Harçları</span><span class="item-sub">Otopark, yol katılım vb.</span></div>
                <div class="form-group"><label>Birim</label><input type="text" class="form-control" value="Paket" disabled></div>
                <div class="form-group"><label>Tutar (TL)</label><input type="number" id="legRuhsat" class="form-control val-leg" value="450000" oninput="calculateAll()"></div>
                <div><label>Satır Toplamı</label><div class="calc-box" id="tot-legRuhsat">0 TL</div></div>
            </div>
            
            <div style="text-align:right; margin-top:20px; font-size:18px; font-weight:800;">Hukuki Toplam: <span id="catTot-leg" style="color:var(--accent);">0 TL</span></div>
        </div>

        <!-- SEKM 3 -->
        <div id="tab-malzeme" class="tab-content">
            <h2>Malzeme Miktarı ve Maliyet Tahmincisi</h2>
            <p class="desc">Türkiye standartlarında betonarme bir yapı için m² bazlı sarfiyatlar otomatik hesaplanmıştır. Piyasadan aldığınız güncel birim fiyatları girin.</p>
            
            <div class="item-row">
                <div><span class="item-title">Hazır Beton (C30/37)</span><span class="item-sub">Standart: 0.38 m³ / m²</span></div>
                <div class="form-group"><label>Gerekli (m³)</label><div class="calc-box" id="qty-beton" style="background:#f1f5f9;">0</div></div>
                <div class="form-group"><label>Birim Fiyat (TL/m³)</label><input type="number" id="matBeton" class="form-control val-mat" value="2800" oninput="calculateAll()"></div>
                <div><label>Satır Toplamı</label><div class="calc-box" id="tot-matBeton">0 TL</div></div>
            </div>

            <div class="item-row">
                <div><span class="item-title">Nervürlü İnşaat Demiri</span><span class="item-sub">Standart: 35 kg / m² (Ton hesabı)</span></div>
                <div class="form-group"><label>Gerekli (Ton)</label><div class="calc-box" id="qty-demir" style="background:#f1f5f9;">0</div></div>
                <div class="form-group"><label>Birim Fiyat (TL/Ton)</label><input type="number" id="matDemir" class="form-control val-mat" value="26500" oninput="calculateAll()"></div>
                <div><label>Satır Toplamı</label><div class="calc-box" id="tot-matDemir">0 TL</div></div>
            </div>

            <div class="item-row">
                <div><span class="item-title">Tuğla / Bims Blok</span><span class="item-sub">Standart: 25 Adet / m²</span></div>
                <div class="form-group"><label>Gerekli (Adet)</label><div class="calc-box" id="qty-tugla" style="background:#f1f5f9;">0</div></div>
                <div class="form-group"><label>Birim Fiyat (TL/Adet)</label><input type="number" id="matTugla" class="form-control val-mat" value="15" oninput="calculateAll()"></div>
                <div><label>Satır Toplamı</label><div class="calc-box" id="tot-matTugla">0 TL</div></div>
            </div>
            
            <div style="text-align:right; margin-top:20px; font-size:18px; font-weight:800;">Malzeme Toplamı: <span id="catTot-mat" style="color:var(--accent);">0 TL</span></div>
        </div>

        <!-- SEKM 4 -->
        <div id="tab-isgucu" class="tab-content">
            <h2>İş Gücü (Taşeron) Yönetimi</h2>
            <p class="desc">Kaba ve ince işler için gerekli personel sayısını ve günlük yevmiyeleri/taşeron bedellerini girin.</p>
            
            <div class="item-row">
                <div><span class="item-title">Kaba İnşaat Ekibi (Kalıp/Demir)</span><span class="item-sub">Süre x Kişi x Yevmiye</span></div>
                <div class="form-group"><label>Kişi Sayısı x Gün</label><input type="number" id="labKabaQty" class="form-control" value="600" oninput="calculateAll()"></div>
                <div class="form-group"><label>Günlük Yevmiye (TL)</label><input type="number" id="labKabaVal" class="form-control val-lab" value="2500" oninput="calculateAll()"></div>
                <div><label>Satır Toplamı</label><div class="calc-box" id="tot-labKaba">0 TL</div></div>
            </div>

            <div class="item-row">
                <div><span class="item-title">İnce İşler Ustaları (Seramik/Boya)</span><span class="item-sub">Süre x Kişi x Yevmiye</span></div>
                <div class="form-group"><label>Kişi Sayısı x Gün</label><input type="number" id="labInceQty" class="form-control" value="800" oninput="calculateAll()"></div>
                <div class="form-group"><label>Günlük Yevmiye (TL)</label><input type="number" id="labInceVal" class="form-control val-lab" value="2800" oninput="calculateAll()"></div>
                <div><label>Satır Toplamı</label><div class="calc-box" id="tot-labInce">0 TL</div></div>
            </div>

            <div style="text-align:right; margin-top:20px; font-size:18px; font-weight:800;">İşçilik Toplamı: <span id="catTot-lab" style="color:var(--accent);">0 TL</span></div>
        </div>

        <!-- SEKM 5 -->
        <div id="tab-ekipman" class="tab-content">
            <h2>İnşaat Ekipmanları ve Makine Kiraları</h2>
            <p class="desc">Ağır makine ve ekipman kiralama giderlerini (Operatör ve yakıt dâhil veya hariç) hesaplayın.</p>
            
            <div class="item-row">
                <div><span class="item-title">Ekskavatör / Hafriyat Makinesi</span><span class="item-sub">Süre x Günlük Kira</span></div>
                <div class="form-group"><label>Kiralama (Gün)</label><input type="number" id="eqEksQty" class="form-control" value="15" oninput="calculateAll()"></div>
                <div class="form-group"><label>Günlük Kira (TL)</label><input type="number" id="eqEksVal" class="form-control val-eq" value="12000" oninput="calculateAll()"></div>
                <div><label>Satır Toplamı</label><div class="calc-box" id="tot-eqEks">0 TL</div></div>
            </div>

            <div class="item-row">
                <div><span class="item-title">Kule Vinç Kurulum & Kira</span><span class="item-sub">Süre x Aylık Kira</span></div>
                <div class="form-group"><label>Kiralama (Ay)</label><input type="number" id="eqVincQty" class="form-control" value="6" oninput="calculateAll()"></div>
                <div class="form-group"><label>Aylık Kira (TL)</label><input type="number" id="eqVincVal" class="form-control val-eq" value="250000" oninput="calculateAll()"></div>
                <div><label>Satır Toplamı</label><div class="calc-box" id="tot-eqVinc">0 TL</div></div>
            </div>

            <div style="text-align:right; margin-top:20px; font-size:18px; font-weight:800;">Ekipman Toplamı: <span id="catTot-eq" style="color:var(--accent);">0 TL</span></div>
        </div>

        <!-- SEKM 6 -->
        <div id="tab-ozet" class="tab-content">
            <h2>İnşaat Maliyet Özeti ve Bütçe Dağılımı</h2>
            <p class="desc">Tüm sekmelerdeki verileriniz toplanmış, üzerine genel giderler ve kâr marjınız eklenmiştir.</p>
            
            <div class="summary-card">
                <div class="summary-row"><span>Malzemeler</span><strong id="sum-mat">0 TL</strong></div>
                <div class="summary-row"><span>İş Gücü (Taşeron)</span><strong id="sum-lab">0 TL</strong></div>
                <div class="summary-row"><span>Teçhizat & Ekipman</span><strong id="sum-eq">0 TL</strong></div>
                <div class="summary-row"><span>Hukuki & Resmî Belgeler</span><strong id="sum-leg">0 TL</strong></div>
                
                <div class="summary-row" style="margin-top:15px; border-top:1px solid #475569; padding-top:15px; color:#94a3b8;">
                    <span>Saha İmalat Ara Toplamı</span><strong id="sum-subtotal">0 TL</strong>
                </div>
                
                <div class="summary-row"><span>Şantiye Genel Giderleri (<span id="perc-overhead">%0</span>)</span><strong id="sum-overhead">0 TL</strong></div>
                <div class="summary-row"><span>Müteahhit Kâr Marjı (<span id="perc-profit">%0</span>)</span><strong id="sum-profit">0 TL</strong></div>
                
                <div class="summary-row summary-total">
                    <span>Tahmini Toplam Proje Maliyeti</span><span id="sum-grand">0 TL</span>
                </div>
                <div style="text-align:right; color:#94a3b8; font-size:13px; margin-top:10px;">Birim Maliyet: <strong id="sum-sqm" style="color:#fff;">0 TL / m²</strong></div>
            </div>
            
            <button class="btn-action" style="background:#10b981;">📄 Profesyonel PDF Raporu İndir (Beta)</button>
        </div>

    </div>
</div>

<script>
function openTab(tabId) {
    document.querySelectorAll('.tab-content').forEach(el => el.classList.remove('active'));
    document.querySelectorAll('.tab-btn').forEach(el => el.classList.remove('active'));
    document.getElementById(tabId).classList.add('active');
    event.currentTarget.classList.add('active');
}

const fmt = new Intl.NumberFormat('tr-TR', { maximumFractionDigits: 0 });

function calculateAll() {
    const area = parseFloat(document.getElementById('pArea').value) || 0;
    const overPerc = parseFloat(document.getElementById('pOverhead').value) || 0;
    const profPerc = parseFloat(document.getElementById('pProfit').value) || 0;

    const qtyBeton = area * 0.38;
    const qtyDemir = (area * 35) / 1000;
    const qtyTugla = area * 25;
    
    document.getElementById('qty-beton').innerText = fmt.format(qtyBeton);
    document.getElementById('qty-demir').innerText = fmt.format(qtyDemir);
    document.getElementById('qty-tugla').innerText = fmt.format(qtyTugla);

    let totLeg = 0, totMat = 0, totLab = 0, totEq = 0;

    let legProj = parseFloat(document.getElementById('legProj').value) || 0;
    let legYapi = parseFloat(document.getElementById('legYapi').value) || 0;
    let legRuhsat = parseFloat(document.getElementById('legRuhsat').value) || 0;
    document.getElementById('tot-legProj').innerText = fmt.format(legProj) + " TL";
    document.getElementById('tot-legYapi').innerText = fmt.format(legYapi) + " TL";
    document.getElementById('tot-legRuhsat').innerText = fmt.format(legRuhsat) + " TL";
    totLeg = legProj + legYapi + legRuhsat;
    document.getElementById('catTot-leg').innerText = fmt.format(totLeg) + " TL";

    let matBeton = qtyBeton * (parseFloat(document.getElementById('matBeton').value) || 0);
    let matDemir = qtyDemir * (parseFloat(document.getElementById('matDemir').value) || 0);
    let matTugla = qtyTugla * (parseFloat(document.getElementById('matTugla').value) || 0);
    document.getElementById('tot-matBeton').innerText = fmt.format(matBeton) + " TL";
    document.getElementById('tot-matDemir').innerText = fmt.format(matDemir) + " TL";
    document.getElementById('tot-matTugla').innerText = fmt.format(matTugla) + " TL";
    totMat = matBeton + matDemir + matTugla;
    document.getElementById('catTot-mat').innerText = fmt.format(totMat) + " TL";

    let labKaba = (parseFloat(document.getElementById('labKabaQty').value) || 0) * (parseFloat(document.getElementById('labKabaVal').value) || 0);
    let labInce = (parseFloat(document.getElementById('labInceQty').value) || 0) * (parseFloat(document.getElementById('labInceVal').value) || 0);
    document.getElementById('tot-labKaba').innerText = fmt.format(labKaba) + " TL";
    document.getElementById('tot-labInce').innerText = fmt.format(labInce) + " TL";
    totLab = labKaba + labInce;
    document.getElementById('catTot-lab').innerText = fmt.format(totLab) + " TL";

    let eqEks = (parseFloat(document.getElementById('eqEksQty').value) || 0) * (parseFloat(document.getElementById('eqEksVal').value) || 0);
    let eqVinc = (parseFloat(document.getElementById('eqVincQty').value) || 0) * (parseFloat(document.getElementById('eqVincVal').value) || 0);
    document.getElementById('tot-eqEks').innerText = fmt.format(eqEks) + " TL";
    document.getElementById('tot-eqVinc').innerText = fmt.format(eqVinc) + " TL";
    totEq = eqEks + eqVinc;
    document.getElementById('catTot-eq').innerText = fmt.format(totEq) + " TL";

    let subtotal = totMat + totLab + totEq + totLeg;
    let overheadCost = subtotal * (overPerc / 100);
    let profitCost = (subtotal + overheadCost) * (profPerc / 100);
    let grandTotal = subtotal + overheadCost + profitCost;
    let costPerSqm = area > 0 ? grandTotal / area : 0;

    document.getElementById('sum-mat').innerText = fmt.format(totMat) + " TL";
    document.getElementById('sum-lab').innerText = fmt.format(totLab) + " TL";
    document.getElementById('sum-eq').innerText = fmt.format(totEq) + " TL";
    document.getElementById('sum-leg').innerText = fmt.format(totLeg) + " TL";
    
    document.getElementById('sum-subtotal').innerText = fmt.format(subtotal) + " TL";
    document.getElementById('perc-overhead').innerText = "%" + overPerc;
    document.getElementById('sum-overhead').innerText = "+ " + fmt.format(overheadCost) + " TL";
    document.getElementById('perc-profit').innerText = "%" + profPerc;
    document.getElementById('sum-profit').innerText = "+ " + fmt.format(profitCost) + " TL";
    
    document.getElementById('sum-grand').innerText = fmt.format(grandTotal) + " TL";
    document.getElementById('sum-sqm').innerText = fmt.format(costPerSqm) + " TL / m²";
}

document.addEventListener('DOMContentLoaded', calculateAll);
</script>
</body>
</html>
