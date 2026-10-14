<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Auto-BOQ | Keşif Fiyatlandırıcı | Worksitem</title>
    <meta name="description" content="Proje dosyanızı (PDF, görsel, Excel) yükleyin; yapay zekâ ön keşif ve metraj çıkarsın, yaklaşık maliyeti hesaplasın.">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        :root {
            --bg: #f8fafc;
            --card: #ffffff;
            --text: #0f172a;
            --muted: #6b7280;
            --accent: #101318;
            --success: #10b981;
            --danger: #ef4444;
            --warning: #f59e0b;
            --border: #e5e7eb;
        }
        body { font-family: 'Inter', sans-serif; background: var(--bg); color: var(--text); min-height: 100vh; }
        .header { background: #fff; padding: 16px 24px; border-bottom: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center; }
        .header a { color: var(--muted); text-decoration: none; font-weight: 700; }
        .header a:hover { color: var(--text); }
        .header img { height: 30px; }
        .container { max-width: 1100px; margin: 40px auto; padding: 0 20px; }
        .ai-badge { background: rgba(16,19,24,0.1); color: var(--accent); padding: 6px 14px; border-radius: 100px; font-size: 12px; font-weight: 900; display: inline-block; }
        h1 { font-weight: 900; margin-top: 10px; font-size: 32px; }
        .subtitle { color: var(--muted); margin-top: 8px; font-size: 15px; }
        .card { background: var(--card); border-radius: 16px; padding: 32px; margin-top: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); }
        .tabs { display: flex; gap: 8px; margin-bottom: 24px; border-bottom: 1px solid var(--border); }
        .tab { padding: 12px 24px; cursor: pointer; font-weight: 700; color: var(--muted); border-bottom: 3px solid transparent; background: none; border-top: none; border-left: none; border-right: none; font-size: 14px; font-family: inherit; }
        .tab.active { color: var(--accent); border-bottom-color: var(--accent); }
        .tab-content { display: none; }
        .tab-content.active { display: block; }
        label { font-weight: 800; font-size: 14px; display: block; margin-bottom: 8px; }
        textarea, input[type="file"] { width: 100%; padding: 16px; border: 2px dashed var(--border); border-radius: 12px; font-family: inherit; font-size: 14px; transition: border 0.2s; }
        textarea { min-height: 200px; resize: vertical; border-style: solid; }
        textarea:focus, input[type="file"]:focus { outline: none; border-color: var(--accent); }
        input[type="file"] { cursor: pointer; background: #fafbfc; }
        input[type="file"]::file-selector-button { background: var(--accent); color: #fff; border: none; padding: 10px 20px; border-radius: 8px; cursor: pointer; font-weight: 700; margin-right: 12px; font-family: inherit; }
        .file-info { margin-top: 10px; padding: 12px 16px; background: #fff8e1; border-radius: 8px; font-size: 13px; color: var(--accent); font-weight: 600; }
        .options { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-top: 20px; }
        .options label { font-weight: 700; font-size: 13px; color: var(--muted); }
        .options select { width: 100%; padding: 12px; border: 1px solid var(--border); border-radius: 8px; font-family: inherit; font-size: 14px; font-weight: 600; }
        .btn { width: 100%; padding: 18px; margin-top: 24px; background: var(--text); color: #fff; border: none; border-radius: 12px; font-weight: 900; font-size: 15px; cursor: pointer; transition: transform 0.15s; font-family: inherit; }
        .btn:hover:not(:disabled) { transform: translateY(-1px); }
        .btn:disabled { opacity: 0.6; cursor: not-allowed; }
        .loader-box { display: none; padding: 60px 20px; text-align: center; }
        .loader-box.active { display: block; }
        .loader-icon { font-size: 48px; margin-bottom: 16px; animation: bounce 1.2s infinite; }
        @keyframes bounce { 0%,100% { transform: translateY(0); } 50% { transform: translateY(-10px); } }
        .loader-title { font-weight: 900; font-size: 18px; margin-bottom: 8px; }
        .loader-sub { color: var(--muted); font-size: 14px; margin-bottom: 24px; }
        .progress-bar { width: 100%; max-width: 400px; height: 6px; background: var(--border); border-radius: 100px; margin: 0 auto; overflow: hidden; }
        .progress-fill { height: 100%; background: var(--accent); width: 0%; transition: width 0.3s; }
        .result-card { display: none; }
        .result-card.active { display: block; }
        .result-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; padding-bottom: 16px; border-bottom: 2px solid var(--border); }
        .result-header h2 { font-weight: 900; font-size: 22px; }
        .summary { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 12px; margin: 20px 0; }
        .summary-item { padding: 16px; background: #f8fafc; border-radius: 10px; }
        .summary-item .label { font-size: 12px; color: var(--muted); font-weight: 700; margin-bottom: 4px; }
        .summary-item .value { font-size: 20px; font-weight: 900; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; font-size: 13px; }
        th { background: #f8fafc; padding: 12px; text-align: left; font-weight: 800; font-size: 12px; color: var(--muted); text-transform: uppercase; border-bottom: 2px solid var(--border); }
        td { padding: 14px 12px; border-bottom: 1px solid var(--border); }
        .badge { display: inline-block; padding: 4px 10px; border-radius: 6px; font-size: 11px; font-weight: 800; }
        .badge-success { background: rgba(16, 185, 129, 0.1); color: var(--success); }
        .badge-warning { background: rgba(245, 158, 11, 0.1); color: var(--warning); }
        .badge-danger { background: rgba(239, 68, 68, 0.1); color: var(--danger); }
        .actions { display: flex; gap: 12px; margin-top: 24px; }
        .actions button { flex: 1; padding: 14px; border-radius: 10px; font-weight: 800; cursor: pointer; font-family: inherit; font-size: 14px; }
        .btn-secondary { background: #fff; border: 2px solid var(--border); color: var(--text); }
        .btn-primary { background: var(--success); color: #fff; border: none; }
        .error-box { display: none; padding: 20px; background: rgba(239, 68, 68, 0.1); border-radius: 12px; margin-top: 20px; color: var(--danger); font-weight: 700; }
        .error-box.active { display: block; }
        @media (max-width: 768px) { .options { grid-template-columns: 1fr; } h1 { font-size: 24px; } .container { margin: 20px auto; } }
    </style>
</head>
<body>
<x-site.contech-header />

<div class="container">
    <div style="text-align:center; margin-bottom:30px;">
        <span class="ai-badge">✦ AI Destekli Keşif Analizi v3.0</span>
        <h1>Auto-BOQ (Keşif Fiyatlandırıcı)</h1>
        <p class="subtitle">PDF, Excel, JPG dosyanızı veya yazılı metraj listenizi yükleyin. Yapay zeka iş kalemlerini çıkarır, doğrulanmış rayiçlerle fiyatlandırır.</p>
    </div>

    <!-- YÜKLEME FORMU -->
    <div class="card" id="uploadCard">
        <div class="tabs">
            <button type="button" class="tab active" data-tab="file">📄 Dosya Yükle</button>
            <button type="button" class="tab" data-tab="text">⌨️ Metin Yapıştır</button>
        </div>

        <form id="autoBoqForm" enctype="multipart/form-data" onsubmit="return false;">
            @csrf

            <div class="tab-content active" id="tab-file">
                <label>Keşif Dosyası (PDF / Excel / JPG / PNG)</label>
                <input type="file" name="project_file" id="projectFile"
                       accept=".pdf,.csv,.xlsx,.xls,.jpg,.jpeg,.png">
                <div class="file-info" id="fileInfo" style="display:none;"></div>
            </div>

            <div class="tab-content" id="tab-text">
                <label>Metraj Listesi (Her satıra bir kalem)</label>
                <textarea name="text_input" id="textInput" placeholder="Örnek:&#10;Ahşap Kalıp 150 m2&#10;C30 Hazır Beton 100 m3&#10;13.5 cm Tuğla Duvar 200 m2"></textarea>
            </div>

            <div class="options">
                <div>
                    <label>Proje Tipi</label>
                    <select name="project_type">
                        <option value="genel">Genel İnşaat</option>
                        <option value="konut">Konut Projesi</option>
                        <option value="ticari">Ticari Yapı</option>
                        <option value="altyapi">Altyapı</option>
                        <option value="renovasyon">Renovasyon</option>
                    </select>
                </div>
                <div>
                    <label>Kalite Sınıfı</label>
                    <select name="quality">
                        <option value="standart">Standart</option>
                        <option value="ekonomik">Ekonomik</option>
                        <option value="lüks">Lüks</option>
                    </select>
                </div>
            </div>

            @if(config('services.ai_spend_enabled'))
            <button type="button" class="btn" id="submitBtn">✦ Yapay Zeka ile Fiyatlandır</button>
            @else
            <button type="button" class="btn" id="submitBtn" disabled>
                AI Ön Keşif Geçici Olarak Kapalı
            </button>
            <div style="margin-top:12px;text-align:center;color:var(--muted);font-size:13px;line-height:1.5;">
                Poz, rayiç ve diğer ücretsiz hesaplama araçları kullanılmaya devam edebilir.
            </div>
            @endif
        </form>
    </div>

    <!-- YÜKLEME EKRANI -->
    <div class="card loader-box" id="loaderBox">
        <div class="loader-icon">🚜</div>
        <div class="loader-title" id="loaderTitle">Yapay Zeka Dosyayı Okuyor...</div>
        <div class="loader-sub" id="loaderSub">Gemini Vision ile iş kalemleri çıkarılıyor.</div>
        <div class="progress-bar"><div class="progress-fill" id="progressFill"></div></div>
    </div>

    <!-- HATA KUTUSU -->
    <div class="card error-box" id="errorBox"></div>

    <!-- SONUÇ KARTI -->
    <div class="card result-card" id="resultCard">
        <div class="result-header">
            <h2>📊 Fiyatlandırılmış Keşif Özeti</h2>
            <span class="badge badge-success" id="resultBadge">Tamamlandı</span>
        </div>

        <div class="summary">
            <div class="summary-item">
                <div class="label">Toplam Kalem</div>
                <div class="value" id="summaryItems">0</div>
            </div>
            <div class="summary-item">
                <div class="label">Fiyatlı</div>
                <div class="value" style="color: var(--success);" id="summaryPriced">0</div>
            </div>
            <div class="summary-item">
                <div class="label">Fiyatsız</div>
                <div class="value" style="color: var(--danger);" id="summaryMissing">0</div>
            </div>
            <div class="summary-item">
                <div class="label">Toplam</div>
                <div class="value" id="summaryTotal">0 TL</div>
            </div>
        </div>

        <div style="overflow-x:auto;">
            <table>
                <thead>
                    <tr>
                        <th>İş Kalemi</th>
                        <th>Miktar</th>
                        <th>Birim</th>
                        <th>Güven</th>
                        <th>Birim Fiyat</th>
                        <th style="text-align:right;">Toplam</th>
                    </tr>
                </thead>
                <tbody id="resultTbody"></tbody>
            </table>
        </div>

        <div class="actions">
            <button type="button" class="btn-secondary" onclick="window.location.reload()">Yeni Analiz</button>
            <button type="button" class="btn-primary" id="exportBtn">📥 Excel'e Aktar</button>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    // Tab switching
    document.querySelectorAll('.tab').forEach(tab => {
        tab.addEventListener('click', () => {
            document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
            tab.classList.add('active');
            document.getElementById('tab-' + tab.dataset.tab).classList.add('active');
        });
    });

    // File info
    const fileInput = document.getElementById('projectFile');
    fileInput.addEventListener('change', (e) => {
        const file = e.target.files[0];
        const info = document.getElementById('fileInfo');
        if (file) {
            info.textContent = `✓ ${file.name} (${(file.size / 1024).toFixed(1)} KB)`;
            info.style.display = 'block';
        } else {
            info.style.display = 'none';
        }
    });

    // Form submit
    const form = document.getElementById('autoBoqForm');
    const submitBtn = document.getElementById('submitBtn');
    submitBtn.addEventListener('click', async (e) => {
        e.preventDefault();

        const file = fileInput.files[0];
        const text = document.getElementById('textInput').value.trim();

        if (!file && !text) {
            showError('Lütfen bir dosya yükleyin veya metin girin.');
            return;
        }

        if (file && text) {
            showError('Lütfen sadece bir yöntem seçin: dosya veya metin.');
            return;
        }

        // Hazırlık
        document.getElementById('uploadCard').style.display = 'none';
        document.getElementById('errorBox').classList.remove('active');
        document.getElementById('loaderBox').classList.add('active');
        document.getElementById('submitBtn').disabled = true;

        // Progress animasyonu
        let progress = 0;
        const progressFill = document.getElementById('progressFill');
        const title = document.getElementById('loaderTitle');
        const sub = document.getElementById('loaderSub');

        const interval = setInterval(() => {
            progress = Math.min(progress + 2, 95);
            progressFill.style.width = progress + '%';

            if (progress === 30) { title.textContent = "İş Kalemleri Ayrıştırılıyor..."; sub.textContent = "Yapay zeka metraj verilerini çıkarıyor."; }
            if (progress === 60) { title.textContent = "Rayiçler Sorgulanıyor..."; sub.textContent = "Doğrulanmış fiyat motorundan birim fiyatlar çekiliyor."; }
            if (progress === 85) { title.textContent = "Hesaplamalar Tamamlanıyor..."; sub.textContent = "Keşif raporu hazırlanıyor."; }
        }, 60);

        // API Çağrısı
        try {
            const formData = new FormData(form);

            // Metin varsa dosya alanını çıkar
            if (text && !file) {
                formData.delete('project_file');
                formData.append('text_input', text);
            } else if (file && !text) {
                formData.delete('text_input');
            }

            const response = await fetch('{{ route("tools.auto_boq.calc") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                }
            });

            clearInterval(interval);
            progressFill.style.width = '100%';

            const result = await response.json();

            if (!response.ok || !result.success) {
                throw new Error(result.message || 'Analiz başarısız oldu.');
            }

            setTimeout(() => renderResult(result), 400);

        } catch (error) {
            clearInterval(interval);
            document.getElementById('loaderBox').classList.remove('active');
            document.getElementById('uploadCard').style.display = 'block';
            document.getElementById('submitBtn').disabled = false;
            showError(error.message || 'Beklenmeyen bir hata oluştu.');
        }
    });

    function showError(msg) {
        const box = document.getElementById('errorBox');
        box.textContent = '✗ ' + msg;
        box.classList.add('active');
        box.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }

    let lastResult = null;

    function renderResult(result) {
        lastResult = result;

        document.getElementById('loaderBox').classList.remove('active');
        document.getElementById('resultCard').classList.add('active');
        document.getElementById('uploadCard').style.display = 'none';

        const items = result.items || [];
        const total = result.raw_total || 0;
        const pricedCount = items.filter(i => i.price_found).length;
        const missingCount = items.length - pricedCount;

        document.getElementById('summaryItems').textContent = items.length;
        document.getElementById('summaryPriced').textContent = pricedCount;
        document.getElementById('summaryMissing').textContent = missingCount;
        document.getElementById('summaryTotal').textContent = new Intl.NumberFormat('tr-TR', { maximumFractionDigits: 0 }).format(total) + ' TL';

        const tbody = document.getElementById('resultTbody');
        tbody.innerHTML = '';

        items.forEach(item => {
            const tr = document.createElement('tr');
            const confBadge = item.confidence === 'high' ? 'success' : (item.confidence === 'medium' ? 'warning' : 'danger');
            tr.innerHTML = `
                <td><strong>${escapeHtml(item.name || '-')}</strong></td>
                <td>${item.quantity ?? '-'}</td>
                <td>${escapeHtml(item.unit || '-')}</td>
                <td><span class="badge badge-${confBadge}">${escapeHtml(item.confidence_label || '-')}</span></td>
                <td>${escapeHtml(item.unit_price || '-')}</td>
                <td style="text-align:right; font-weight:800;">${escapeHtml(item.total || '-')}</td>
            `;
            tbody.appendChild(tr);
        });

        document.getElementById('resultCard').scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    function escapeHtml(str) {
        return String(str ?? '').replace(/[&<>"']/g, m => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m]));
    }

    // Excel export
    document.getElementById('exportBtn').addEventListener('click', () => {
        if (!lastResult || !lastResult.items) return;
        const rows = lastResult.items.map(i => ({
            'İş Kalemi': i.name,
            'Miktar': i.quantity,
            'Birim': i.unit,
            'Güven': i.confidence_label,
            'Birim Fiyat': i.unit_price,
            'Toplam': i.total,
            'Fiyat Kaynağı': i.price_source || '-',
        }));
        const ws = XLSX.utils.json_to_sheet(rows);
        ws['!cols'] = [{wch:40},{wch:10},{wch:10},{wch:14},{wch:20},{wch:20},{wch:14}];
        const wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, 'Kesif');
        XLSX.writeFile(wb, 'Worksitem_Kesif_' + new Date().toISOString().slice(0,10) + '.xlsx');
    });
});
</script>
    <x-site.contech-footer />
</body>
</html>
