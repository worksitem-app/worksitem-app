<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>İhale ve Keşif Motoru | WORKSİTEM</title>
    <meta name="description" content="İhale ve keşif çalışmalarınızı WORKSİTEM İhale ve Keşif Motoru ile yönetin ve proje bazlı keşif sürecinizi tek çalışma alanında düzenleyin.">
    <link rel="canonical" href="https://worksitem.com/araclar/ihale-modulu">
    <link rel="icon" href="/favicon.ico" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        :root { --bg:#050505; --surface:#0A0A0A; --border:rgba(255,255,255,0.1); --text-main:#FFFFFF; --text-muted:#A1A1AA; --primary:#FFB800; --green:#FFB800; }
        * { box-sizing:border-box; margin:0; padding:0; }
        body { font-family:'Inter', sans-serif; background:var(--bg); color:var(--text-main); height: 100vh; display: flex; flex-direction: column; overflow: hidden;}
        ::-webkit-scrollbar { width: 8px; height: 8px; } ::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.2); border-radius: 4px; }
        ::selection { background: var(--primary); color: #000; }
        
        .navbar { display:flex; justify-content:space-between; align-items:center; padding:16px 24px; background:rgba(10,10,10,0.9); border-bottom:1px solid var(--border); z-index:10;}
        .logo { font-weight:800; font-size:16px; color:#fff; text-decoration:none; display:flex; align-items:center; gap:10px;}
        .logo img { height: 24px; filter: brightness(0) invert(1); }
        .nav-actions { display:flex; gap:12px; }
        .btn { padding:10px 20px; border-radius:8px; font-size:13px; font-weight:800; cursor:pointer; border:none; transition:all 0.2s; color:#000; text-transform:uppercase;}
        .btn-excel { background:rgba(255,255,255,0.1); color:#fff; border:1px solid var(--border);} .btn-excel:hover { border-color:var(--primary); color:var(--primary); }
        .btn-save { background:var(--primary); } .btn-save:hover { background:#fff; }

        .app-container { display: flex; flex: 1; overflow: hidden; position: relative;}
        .ambient-glow { position:absolute; top:0; left:50%; transform:translateX(-50%); width:100%; height:100%; background:radial-gradient(circle at 30% 50%, rgba(255,184,0,0.05), transparent 50%); pointer-events:none; z-index:0;}

        /* SOL PANEL */
        .sidebar { width: 380px; background: var(--surface); border-right: 1px solid var(--border); display: flex; flex-direction: column; flex-shrink: 0; z-index:1;}
        .sidebar-header { padding: 20px; border-bottom: 1px solid var(--border); }
        .sidebar-header h2 { font-size: 13px; font-weight: 900; text-transform: uppercase; color: var(--primary); margin-bottom: 15px; letter-spacing:1px;}
        
        .search-box { display: flex; flex-direction: column; gap: 10px; }
        .search-input { padding: 12px 16px; background: rgba(255,255,255,0.03); border: 1px solid var(--border); border-radius: 8px; font-size: 13px; color:#fff; width: 100%; outline:none;}
        .search-input:focus { border-color: var(--primary); box-shadow:0 0 0 3px rgba(255,184,0,0.1); }
        .filter-select { padding: 12px; background: rgba(255,255,255,0.03); border: 1px solid var(--border); color:#fff; border-radius: 8px; font-size: 13px; width: 100%; outline:none;}
        .filter-select option { background: #111; color:#fff;}

        .poz-list { flex: 1; overflow-y: auto; padding: 15px; }
        .poz-item { padding: 16px; border: 1px solid var(--border); border-radius: 12px; margin-bottom: 10px; cursor: pointer; transition: all 0.2s; background: rgba(255,255,255,0.02);}
        .poz-item:hover { border-color: var(--primary); background: rgba(255,184,0,0.05); transform:translateY(-2px); }
        .poz-no { font-family: monospace; font-size: 14px; font-weight: 900; color: var(--primary); }
        .poz-name { font-size: 13px; color: #fff; margin: 8px 0; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; line-height:1.5;}
        .poz-meta { display: flex; justify-content: space-between; align-items: center; font-size: 11px; color: var(--text-muted); font-weight:600;}
        .poz-price { font-weight: 900; color: #fff; font-size: 14px;}

        /* SAĞ PANEL */
        .main-content { flex: 1; display: flex; flex-direction: column; overflow: hidden; z-index:1; background:transparent;}
        .toolbar { padding: 15px 30px; border-bottom: 1px solid var(--border); display: flex; gap: 20px; background: rgba(0,0,0,0.5);}
        .tab-btn { padding: 8px 0; font-size: 13px; font-weight: 700; color: var(--text-muted); background: transparent; border: none; cursor: pointer; border-bottom: 2px solid transparent; text-transform:uppercase;}
        .tab-btn.active { color: var(--primary); border-bottom-color: var(--primary); }

        .table-container { flex: 1; overflow: auto; padding: 30px; }
        table { width: 100%; border-collapse: collapse; background: var(--surface); border: 1px solid var(--border); border-radius: 12px; overflow: hidden;}
        th { background: rgba(255,255,255,0.03); padding: 16px; text-align: left; font-size: 11px; font-weight: 900; color: var(--text-muted); border-bottom: 1px solid var(--border); text-transform: uppercase; letter-spacing:1px;}
        td { padding: 16px; border-bottom: 1px solid rgba(255,255,255,0.05); font-size: 13px; vertical-align: middle; color:#d4d4d8;}
        tr:hover td { background: rgba(255,184,0,0.03); }
        
        .input-qty { width: 100px; padding: 8px 12px; background:rgba(0,0,0,0.5); border: 1px solid var(--border); border-radius: 6px; text-align: right; font-size: 14px; font-weight:800; color:var(--primary); outline:none;}
        .input-qty:focus { border-color:var(--primary); }
        .row-total { font-weight: 900; color: #fff; text-align: right;}
        .col-right { text-align: right; }

        /* FOOTER */
        .summary-footer { padding: 25px 30px; background: var(--surface); border-top: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center; flex-shrink: 0;}
        .summary-item { display: flex; flex-direction: column; gap: 6px; }
        .summary-label { font-size: 11px; font-weight: 800; color: var(--text-muted); text-transform: uppercase; letter-spacing:1px;}
        .summary-value { font-size: 24px; font-weight: 900; color: #fff; font-variant-numeric: tabular-nums;}
        .summary-value.grand { color: var(--primary); font-size: 32px; }
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

    <div class="app-container">
        <div class="ambient-glow"></div>
        
        <!-- SOL PANEL -->
        <div class="sidebar">
            <div class="sidebar-header">
                <h2>Poz Kütüphanesi</h2>
                <div class="search-box">
                    <select class="filter-select" id="instFilter" onchange="fetchPozs()">
                        <option value="all">Tüm Kurumlar</option>
                        <option value="ÇŞB">Çevre ve Şehircilik Bakanlığı</option>
                        <option value="Karayolları">Karayolları Genel Müd.</option>
                        <option value="İller Bankası">İller Bankası</option>
                        <option value="Özel">Özel / Özel Pozlar</option>
                    </select>
                    <input type="text" class="search-input" id="searchInput" placeholder="Poz No veya Tanım Ara..." oninput="fetchPozs()">
                </div>
            </div>
            
            <div class="poz-list" id="pozList">
                <div style="text-align:center; padding:30px; color:var(--text-muted); font-size:13px;">Bağlantı kuruluyor...</div>
            </div>
        </div>

        <!-- SAĞ PANEL -->
        <div class="main-content">
            <div class="toolbar">
                <button class="tab-btn active">Keşif Özeti</button>
                <button class="tab-btn" onclick="alert('Metraj Cetveli Yükleniyor...')">Metraj Cetveli</button>
                <button class="tab-btn" onclick="alert('Analizler...')">Birim Fiyat Analizi</button>
            </div>

            <div class="table-container">
                <table id="tenderTable">
                    <thead>
                        <tr>
                            <th width="15%">POZ NO</th>
                            <th width="35%">İŞ KALEMİ TANIMI</th>
                            <th width="10%">BİRİM</th>
                            <th width="12%" class="col-right">MİKTAR</th>
                            <th width="13%" class="col-right">BİRİM FİYAT</th>
                            <th width="15%" class="col-right">TUTAR</th>
                        </tr>
                    </thead>
                    <tbody id="tenderBody">
                        <tr id="emptyRow">
                            <td colspan="6" style="text-align:center; padding:80px; color:var(--text-muted);">
                                <div style="font-size:32px; margin-bottom:15px; color:var(--primary);">⌁</div>
                                Sol panelden poz arayın ve tıklayarak ekleyin.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="summary-footer">
                <div class="summary-item">
                    <span class="summary-label">Proje Unvanı</span>
                    <span style="font-size:16px; font-weight:800; color:#fff;">WORKSİTEM Örnek İhale Dosyası</span>
                </div>
                <div style="display:flex; gap:50px;">
                    <div class="summary-item col-right">
                        <span class="summary-label">Toplam Tutar (KDV Hariç)</span>
                        <span class="summary-value" id="subTotal">0,00 ₺</span>
                    </div>
                    <div class="summary-item col-right">
                        <span class="summary-label">Genel Toplam (+%20 KDV)</span>
                        <span class="summary-value grand" id="grandTotal">0,00 ₺</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let items = [];

        async function fetchPozs() {
            const q = document.getElementById('searchInput').value;
            const inst = document.getElementById('instFilter').value;
            const pozList = document.getElementById('pozList');

            try {
                const res = await fetch(`/api/pozlar/search?q=${encodeURIComponent(q)}&institution=${encodeURIComponent(inst)}`);
                const data = await res.json();

                if(data.length === 0) {
                    pozList.innerHTML = '<div style="text-align:center; padding:30px; color:var(--text-muted); font-size:13px;">Eşleşen poz bulunamadı.</div>';
                    return;
                }

                pozList.innerHTML = '';
                data.forEach(p => {
                    const priceFormatted = new Intl.NumberFormat('tr-TR', { minimumFractionDigits: 2 }).format(p.offer_unit_price ?? p.unit_price);
                    const div = document.createElement('div');
                    div.className = 'poz-item';
                    div.onclick = () => addPoz(p.poz_no, p.description, p.unit, p.offer_unit_price ?? p.unit_price);
                    div.innerHTML = `
                        <div class="poz-no">${p.poz_no}</div>
                        <div class="poz-name">${p.description}</div>
                        <div class="poz-meta"><span>${p.institution}</span><span class="poz-price">${priceFormatted} ₺</span></div>
                    `;
                    pozList.appendChild(div);
                });
            } catch(e) {
                pozList.innerHTML = '<div style="color:#ef4444; text-align:center; font-size:13px;">API bağlantı hatası.</div>';
            }
        }

        function addPoz(no, name, unit, price) {
            document.getElementById('emptyRow').style.display = 'none';
            let existing = items.find(i => i.no === no);
            if(existing) {
                existing.qty += 1;
                document.getElementById('qty_' + String(no).replace(/\./g, '_')).value = existing.qty;
                updateRow(no);
                return;
            }
            items.push({ no: no, name: name, unit: unit, price: parseFloat(price), qty: 1 });
            renderRow(no, name, unit, price);
            calculateTotals();
        }

        function renderRow(no, name, unit, price) {
            let tbody = document.getElementById('tenderBody');
            let tr = document.createElement('tr');
            let cleanNo = String(no).replace(/\./g, '_');
            tr.id = 'row_' + cleanNo;
            let formattedPrice = new Intl.NumberFormat('tr-TR', { minimumFractionDigits: 2 }).format(price);
            
            tr.innerHTML = `
                <td style="font-family:monospace; font-weight:800; color:var(--primary); cursor:pointer;" onclick="removeRow('${no}')" title="Silmek için tıklayın">${no} <span style="color:#ef4444; font-size:10px; vertical-align:top;">✕</span></td>
                <td style="font-weight:600;">${name}</td>
                <td>${unit}</td>
                <td class="col-right"><input type="number" id="qty_${cleanNo}" class="input-qty" value="1" step="0.01" oninput="updateRow('${no}')"></td>
                <td class="col-right">${formattedPrice} ₺</td>
                <td class="col-right row-total" id="total_${cleanNo}">${formattedPrice} ₺</td>
            `;
            tbody.appendChild(tr);
        }

        function updateRow(no) {
            let cleanNo = String(no).replace(/\./g, '_');
            let item = items.find(i => i.no === no);
            let qty = parseFloat(document.getElementById('qty_' + cleanNo).value) || 0;
            item.qty = qty;
            let total = qty * item.price;
            document.getElementById('total_' + cleanNo).innerText = new Intl.NumberFormat('tr-TR', { minimumFractionDigits: 2 }).format(total) + ' ₺';
            calculateTotals();
        }

        function removeRow(no) {
            let cleanNo = String(no).replace(/\./g, '_');
            items = items.filter(i => i.no !== no);
            document.getElementById('row_' + cleanNo).remove();
            if(items.length === 0) document.getElementById('emptyRow').style.display = 'table-row';
            calculateTotals();
        }

        function calculateTotals() {
            let subTotal = 0;
            items.forEach(i => { subTotal += (i.qty * i.price); });
            let grandTotal = subTotal * 1.20;
            document.getElementById('subTotal').innerText = new Intl.NumberFormat('tr-TR', { minimumFractionDigits: 2 }).format(subTotal) + ' ₺';
            document.getElementById('grandTotal').innerText = new Intl.NumberFormat('tr-TR', { minimumFractionDigits: 2 }).format(grandTotal) + ' ₺';
        }

        document.addEventListener('DOMContentLoaded', fetchPozs);
    </script>
</body>
</html>
