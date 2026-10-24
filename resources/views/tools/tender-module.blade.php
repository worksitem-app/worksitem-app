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
        :root { --bg:#f5f6f8; --surface:#ffffff; --border:#e5e7eb; --text-main:#101318; --text-muted:#5b6472; --primary:#FFB800; --green:#FFB800; }
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
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <x-calc.head />
    <style id="ws-app-shell">
        /* Uygulama kabuğu: sayfa kaydırılabilir, uygulama alanı ekran yüksekliğinde; açık temada okunur metin */
        body.wc{height:auto!important;overflow:auto!important;display:block!important}
        .app-container{height:calc(100vh - 72px);min-height:640px;border-top:1px solid var(--border)}
        .search-input,.filter-select{color:#101318!important;background:#fff!important}
        .filter-select option{background:#fff!important;color:#101318!important}
        .poz-name,.poz-price,.row-total,.summary-value{color:#101318!important}
        .summary-value.grand{color:#8A6200!important}
        .btn-excel{background:#fff!important;color:#101318!important}
        .input-qty{background:#fff!important;color:#101318!important}
        .toolbar{background:#fff!important}
        .tab-btn[disabled]{opacity:.55;cursor:not-allowed}
        .summary-totals{display:flex;gap:40px;flex-wrap:wrap}
        .summary-footer{flex-wrap:wrap;gap:16px}
        .price-legend{font-size:12px;color:var(--text-muted);margin-top:12px;line-height:1.5}
        .price-warn{font-size:13px;font-weight:700;color:#8A4B00;background:#FFF4E0;border:1px solid #F5C77A;border-radius:8px;padding:10px 12px;margin-top:10px}
        .poz-price.none,.price-none{color:#9a3412!important;font-weight:700;font-size:12px}
        .price-tag{display:inline-block;font-size:10px;font-weight:800;border-radius:999px;padding:1px 6px;margin-left:4px;background:#eef2ff;color:#3730a3;vertical-align:middle}
        .price-sub{display:block;font-size:11px;color:var(--text-muted);font-weight:600}
        tr.no-price td{background:#FFF7ED}
        .row-del{background:none;border:0;color:#ef4444;font-size:12px;cursor:pointer;margin-left:6px;padding:2px 4px}
        .row-no{font-family:monospace;font-weight:800;color:var(--primary)}
        .tab-btn small{font-size:10px;font-weight:800;background:#FFF8E1;color:#8A6200;border-radius:999px;padding:2px 6px;margin-left:6px;text-transform:none}
        @media (max-width: 900px){
            .app-container{flex-direction:column;height:auto;min-height:0}
            .sidebar{width:100%!important;max-height:440px;border-right:0!important;border-bottom:1px solid var(--border)}
            .main-content{overflow:visible!important}
            .table-container{padding:14px!important;overflow-x:auto}
        }
    </style>
</head>
<body class="wc" data-tool-slug="tender-module">

    <x-site.contech-header />
    <div class="wc-wrap" style="padding-bottom:14px">
        <x-calc.hero title="İhale ve keşif özeti hazırlama (poz bazlı)" crumb="İhale modülü" :facts="['ÇŞB, Karayolları, İller Bankası pozları', 'Metraj cetveli ve birim fiyat analizi']">Poz kütüphanesinden iş kalemlerini seçin, miktarları girin; KDV hariç ve KDV dahil keşif özetini, metraj cetvelini ve birim fiyat analizini tek ekranda hazırlayın.</x-calc.hero>
    </div>

    <div class="app-container">
        <div class="ambient-glow"></div>
        
        {{-- SOL PANEL --}}
        <div class="sidebar">
            <div class="sidebar-header">
                <h2>Poz Kütüphanesi</h2>
                <div class="search-box">
                    <select class="filter-select" id="instFilter" aria-label="Kurum">
                        <option value="all">Tüm Kurumlar</option>
                        @foreach (\App\Http\Controllers\PozPublicController::searchInstitutionOptions() as $instVal => $instLabel)
                            <option value="{{ $instVal }}">{{ $instLabel }}</option>
                        @endforeach
                    </select>
                    <input type="text" class="search-input" id="searchInput" placeholder="Poz No veya Tanım Ara..." aria-label="Poz ara">
                </div>
            </div>
            
            <div class="poz-list" id="pozList">
                <div style="text-align:center; padding:30px; color:var(--text-muted); font-size:13px;">Bağlantı kuruluyor...</div>
            </div>
        </div>

        {{-- SAĞ PANEL --}}
        <div class="main-content">
            <div class="toolbar">
                <button class="tab-btn active">Keşif Özeti</button>
                <a class="tab-btn" href="/araclar/dijital-metraj" style="text-decoration:none">Metraj (plandan ölçüm)<small>ayrı araç →</small></a>
                <a class="tab-btn" href="/araclar/birim-fiyat-analiz" style="text-decoration:none">Birim Fiyat Analizi<small>ayrı araç →</small></a>
            </div>

            <div class="table-container">
                <table id="tenderTable">
                    <thead>
                        <tr>
                            <th width="12%">POZ NO</th>
                            <th width="30%">İŞ KALEMİ TANIMI</th>
                            <th width="7%">BİRİM</th>
                            <th width="11%" class="col-right">MİKTAR</th>
                            <th width="12%" class="col-right" title="Kurumun yayımladığı birim fiyat">RESMÎ BİRİM FİYAT</th>
                            <th width="13%" class="col-right" id="offerHead" title="Hedef kâr marjıyla hesaplanan teklif birim fiyatı">TEKLİF BİRİM FİYATI</th>
                            <th width="15%" class="col-right">TUTAR</th>
                        </tr>
                    </thead>
                    <tbody id="tenderBody">
                        <tr id="emptyRow">
                            <td colspan="7" style="text-align:center; padding:80px; color:var(--text-muted);">
                                <div style="font-size:32px; margin-bottom:15px; color:var(--primary);">⌁</div>
                                Sol panelden poz arayın ve tıklayarak ekleyin.
                            </td>
                        </tr>
                    </tbody>
                </table>
                <p class="price-legend" id="priceLegend">Resmî birim fiyat: kurumun yayımladığı fiyat (KDV hariç). Teklif birim fiyatı: resmî fiyattan hedef kâr marjıyla hesaplanan önerilen teklif fiyatıdır, resmî fiyat değildir. Tutar teklif fiyatıyla; teklif fiyatı hesaplanamayan pozda resmî fiyatla hesaplanır. Fiyatı olmayan pozlar toplama girmez.</p>
                <p class="price-warn" id="priceWarn" role="status" hidden></p>
            </div>

            <div class="summary-footer">
                <div class="summary-item">
                    <span class="summary-label">Proje Unvanı</span>
                    <span style="font-size:16px; font-weight:800; color:var(--text-main);">WORKSİTEM Örnek İhale Dosyası</span>
                </div>
                <div class="summary-totals">
                    <div class="summary-item col-right">
                        <span class="summary-label">Resmî fiyatlarla toplam (KDV hariç)</span>
                        <span class="summary-value" id="officialTotal">0,00 ₺</span>
                    </div>
                    <div class="summary-item col-right">
                        <span class="summary-label">Tutar toplamı (KDV hariç)</span>
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
        // v119: fiyatsız poz NaN üretmez; DOM textContent ile kurulur (depolanmış XSS yok);
        // satır silme addEventListener ile (poz no'da ' olsa da çalışır).
        let items = [];
        let fetchSeq = 0;
        const fmt = new Intl.NumberFormat('tr-TR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        const money = v => fmt.format(v) + ' ₺';
        const num = v => { const n = (v === null || v === undefined || v === '') ? NaN : Number(v); return Number.isFinite(n) && n > 0 ? n : null; };
        function el(tag, cls, text) { const e = document.createElement(tag); if (cls) e.className = cls; if (text !== undefined && text !== null) e.textContent = String(text); return e; }

        function msg(text, color) {
            const pozList = document.getElementById('pozList');
            pozList.replaceChildren();
            const d = el('div', null, text);
            d.style.cssText = 'text-align:center; padding:30px; font-size:13px; color:' + (color || 'var(--text-muted)');
            pozList.appendChild(d);
        }

        async function fetchPozs() {
            const q = document.getElementById('searchInput').value;
            const inst = document.getElementById('instFilter').value;
            const pozList = document.getElementById('pozList');
            const seq = ++fetchSeq;

            try {
                const res = await fetch(`/api/pozlar/search?q=${encodeURIComponent(q)}&institution=${encodeURIComponent(inst)}`, { headers: { 'Accept': 'application/json' } });
                if (!res.ok) throw new Error('HTTP ' + res.status);
                const data = await res.json();
                if (seq !== fetchSeq) return; // eski yanıt yenisinin üstüne yazmasın

                if (!Array.isArray(data) || data.length === 0) { msg('Eşleşen poz bulunamadı.'); return; }

                pozList.replaceChildren();
                data.forEach(p => {
                    const official = num(p.official_unit_price);
                    const offer = official ? num(p.offer_unit_price) : null;
                    const div = el('div', 'poz-item');
                    div.tabIndex = 0;
                    div.setAttribute('role', 'button');
                    const add = () => addPoz(p);
                    div.addEventListener('click', add);
                    div.addEventListener('keydown', e => { if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); add(); } });
                    div.appendChild(el('div', 'poz-no', p.poz_no));
                    div.appendChild(el('div', 'poz-name', p.description));
                    const meta = el('div', 'poz-meta');
                    meta.appendChild(el('span', null, p.institution));
                    if (offer) {
                        const pr = el('span', 'poz-price', money(offer));
                        pr.appendChild(el('span', 'price-sub', 'teklif · resmî ' + money(official)));
                        meta.appendChild(pr);
                    } else if (official) {
                        const pr = el('span', 'poz-price', money(official));
                        pr.appendChild(el('span', 'price-sub', 'resmî fiyat'));
                        meta.appendChild(pr);
                    } else {
                        meta.appendChild(el('span', 'poz-price none', 'fiyat yok'));
                    }
                    div.appendChild(meta);
                    pozList.appendChild(div);
                });
            } catch (e) {
                if (seq === fetchSeq) msg('Poz listesi alınamadı. Lütfen tekrar deneyin.', '#b91c1c');
            }
        }

        let searchTimer = null;
        document.getElementById('searchInput').addEventListener('input', () => { clearTimeout(searchTimer); searchTimer = setTimeout(fetchPozs, 250); });
        document.getElementById('instFilter').addEventListener('change', fetchPozs);

        function addPoz(p) {
            document.getElementById('emptyRow').style.display = 'none';
            const existing = items.find(i => i.no === p.poz_no && i.inst === p.institution);
            if (existing) {
                existing.qty += 1;
                existing.qtyInput.value = existing.qty;
                updateRow(existing);
                return;
            }
            const official = num(p.official_unit_price);
            const offer = official ? num(p.offer_unit_price) : null;
            const item = { no: p.poz_no, inst: p.institution, name: p.description, unit: p.unit, official, offer, price: offer ?? official, qty: 1 };
            items.push(item);
            renderRow(item);
            calculateTotals();
        }

        function renderRow(item) {
            const tbody = document.getElementById('tenderBody');
            const tr = el('tr');
            if (item.price === null) tr.className = 'no-price';

            const tdNo = el('td');
            tdNo.appendChild(el('span', 'row-no', item.no));
            const del = el('button', 'row-del', '✕');
            del.type = 'button';
            del.title = 'Satırı sil';
            del.setAttribute('aria-label', 'Satırı sil: ' + item.no);
            del.addEventListener('click', () => removeRow(item));
            tdNo.appendChild(del);
            tr.appendChild(tdNo);

            const tdName = el('td', null, item.name);
            tdName.style.fontWeight = '600';
            tr.appendChild(tdName);
            tr.appendChild(el('td', null, item.unit));

            const tdQty = el('td', 'col-right');
            const inp = el('input', 'input-qty');
            inp.type = 'number'; inp.min = '0'; inp.step = '0.01'; inp.value = '1';
            inp.setAttribute('aria-label', 'Miktar: ' + item.no);
            inp.addEventListener('input', () => updateRow(item));
            tdQty.appendChild(inp);
            tr.appendChild(tdQty);
            item.qtyInput = inp;

            tr.appendChild(el('td', item.official ? 'col-right' : 'col-right price-none', item.official ? money(item.official) : 'fiyat yok'));
            const tdOffer = el('td', 'col-right');
            if (item.offer) {
                tdOffer.textContent = money(item.offer);
            } else {
                tdOffer.className = 'col-right price-none';
                tdOffer.textContent = item.official ? 'hesaplanamadı' : 'fiyat yok';
                if (item.official) tdOffer.appendChild(el('span', 'price-sub', 'tutarda resmî fiyat kullanılır'));
            }
            tr.appendChild(tdOffer);

            const tdTot = el('td', 'col-right row-total');
            tr.appendChild(tdTot);
            item.totalCell = tdTot;
            item.row = tr;
            tbody.appendChild(tr);
            paintTotal(item);
        }

        function paintTotal(item) {
            const c = item.totalCell;
            c.replaceChildren();
            if (item.price === null) {
                c.textContent = 'fiyatsız';
                c.classList.add('price-none');
                c.appendChild(el('span', 'price-sub', 'toplama girmez'));
                return;
            }
            c.textContent = money(item.qty * item.price);
            if (!item.offer) c.appendChild(el('span', 'price-tag', 'resmî'));
        }

        function updateRow(item) {
            let qty = parseFloat(item.qtyInput.value);
            if (!Number.isFinite(qty) || qty < 0) qty = 0; // negatif miktar toplamı eksiye çekmesin
            item.qty = qty;
            paintTotal(item);
            calculateTotals();
        }

        function removeRow(item) {
            items = items.filter(i => i !== item);
            item.row.remove();
            if (items.length === 0) document.getElementById('emptyRow').style.display = 'table-row';
            calculateTotals();
        }

        function calculateTotals() {
            let subTotal = 0, officialTotal = 0, noPrice = 0, officialFallback = 0;
            items.forEach(i => {
                if (i.price === null) { noPrice++; return; }
                subTotal += i.qty * i.price;
                officialTotal += i.qty * i.official;
                if (!i.offer) officialFallback++;
            });
            document.getElementById('officialTotal').textContent = money(officialTotal);
            document.getElementById('subTotal').textContent = money(subTotal);
            document.getElementById('grandTotal').textContent = money(subTotal * 1.20);
            const parts = [];
            if (noPrice) parts.push(noPrice + ' poz fiyatsız; toplama dahil edilmedi.');
            if (officialFallback) parts.push(officialFallback + ' pozda teklif fiyatı hesaplanamadı; tutarda resmî fiyat kullanıldı.');
            const w = document.getElementById('priceWarn');
            w.textContent = parts.join(' ');
            w.hidden = parts.length === 0;
        }

        document.addEventListener('DOMContentLoaded', fetchPozs);
    </script>
    <script src="/js/tool-events.js" defer></script>
    <x-site.contech-footer />
</body>
</html>
