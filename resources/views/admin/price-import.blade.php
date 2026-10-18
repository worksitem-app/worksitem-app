<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>AI Fiyat Güncelleme | WORKSİTEM Admin</title>
    <style>
        body { font-family: sans-serif; background: #0f1115; color: #fff; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .box { background: #16191f; padding: 40px; border-radius: 16px; border: 1px solid #2a2e37; max-width: 500px; text-align: center; width: 100%; }
        h1 { margin-top: 0; font-size: 24px; color: #ffb800; }
        p { color: #9ca3af; font-size: 14px; margin-bottom: 30px; line-height: 1.5; }
        input[type="file"] { margin-bottom: 20px; color: #fff; width: 100%; }
        button { background: #ffb800; color: #000; border: none; padding: 14px 24px; border-radius: 8px; font-weight: bold; cursor: pointer; width: 100%; font-size: 15px; transition: 0.2s; }
        button:hover { background: #e5a400; }
        button:disabled { background: #4b5563; color: #9ca3af; cursor: not-allowed; }
        
        .alert { padding: 15px; margin-bottom: 20px; border-radius: 8px; font-size: 14px; }
        .alert-success { background: rgba(21,148,71,0.2); color: #4ade80; border: 1px solid #166534; }
        .alert-error { background: rgba(220,38,38,0.2); color: #f87171; border: 1px solid #991b1b; }

        /* Yükleme Animasyonu */
        #loading { display: none; margin-top: 20px; }
        .spinner { width: 40px; height: 40px; border: 4px solid #2a2e37; border-top: 4px solid #ffb800; border-radius: 50%; animation: spin 1s linear infinite; margin: 0 auto 15px; }
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
        .loading-text { color: #ffb800; font-weight: bold; font-size: 14px; margin-bottom: 5px; }
        .loading-sub { color: #9ca3af; font-size: 12px; }
    </style>
</head>
<body>
    <div class="box">
        <h1>Poz ve fiyat okuyucu</h1>
        <p>Bakanlık veya tedarikçi fiyat listesini (PDF) yükleyin. Yapay zeka tüm pozları okuyup veritabanınızı otomatik güncellesin.</p>
        
        @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
        @if(session('error')) <div class="alert alert-error">{{ session('error') }}</div> @endif

        <form id="uploadForm" action="{{ route('admin.prices.upload') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="file" name="price_pdf" id="fileInput" accept=".pdf" required>
            <button type="submit" id="submitBtn">PDF'i Analiz Et ve Kaydet</button>
        </form>

        <div id="loading">
            <div class="spinner"></div>
            <div class="loading-text">Yapay Zeka PDF'i Analiz Ediyor...</div>
            <div class="loading-sub">Lütfen bekleyin, bu işlem dosya boyutuna göre 30-60 saniye sürebilir. Sayfayı kapatmayın.</div>
        </div>
    </div>

    <script>
        document.getElementById('uploadForm').addEventListener('submit', function() {
            if(document.getElementById('fileInput').files.length > 0) {
                document.getElementById('submitBtn').style.display = 'none';
                document.getElementById('loading').style.display = 'block';
            }
        });
    </script>
</body>
</html>
