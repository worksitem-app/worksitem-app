<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kurumsal Sözleşme & Teklif Oluşturucu | WORKSİTEM</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <style>
        :root { --bg: #f5f6f8; --text: #101318; --line: #e4e7eb; --accent: #10b981; }
        body { font-family: 'Inter', sans-serif; background: var(--bg); margin: 0; }
        .header { background: #fff; padding: 15px 20px; border-bottom: 1px solid var(--line); display: flex; justify-content: space-between; }
        .container { max-width: 1200px; margin: 30px auto; padding: 0 20px; display: grid; grid-template-columns: 350px 1fr; gap: 30px; }
        @media(max-width: 900px) { .container { grid-template-columns: 1fr; } }
        
        .sidebar { background: #fff; padding: 25px; border-radius: 16px; border: 1px solid var(--line); height: fit-content; }
        .form-group { margin-bottom: 15px; }
        label { display: block; font-size: 12px; font-weight: 800; margin-bottom: 5px; }
        input { width: 100%; height: 40px; border: 1px solid var(--line); border-radius: 8px; padding: 0 10px; font-family: inherit; }
        .btn { background: var(--accent); color: #fff; border: none; padding: 15px; width: 100%; border-radius: 12px; font-weight: 800; cursor: pointer; margin-top: 10px; }
        .btn-word { background: #2563eb; margin-top: 10px; }
        
        .document-wrapper { background: #fff; padding: 40px; box-shadow: 0 10px 40px rgba(0,0,0,0.1); width: 100%; max-width: 21cm; min-height: 29.7cm; margin: 0 auto; box-sizing: border-box; }
        .doc-header { text-align: center; border-bottom: 2px solid #111; padding-bottom: 20px; margin-bottom: 30px; }
        .doc-title { font-size: 20px; font-weight: 900; letter-spacing: 1px; }
        .doc-content { font-size: 13px; line-height: 1.8; color: #333; text-align: justify; }
        .doc-signatures { display: flex; justify-content: space-between; margin-top: 80px; }
        .sign-box { text-align: center; width: 40%; }
        .sign-line { border-top: 1px solid #111; margin-top: 50px; padding-top: 10px; font-weight: 700; }
    </style>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body data-tool-slug="pdf-contract">
<header class="header">
    <a href="/araclar" style="text-decoration:none; color:#6b7280; font-weight:700;">← Araçlara Dön</a>
    <img src="/200.png" height="30" alt="WORKSİTEM">
</header>
<div class="container">
    <div class="sidebar">
        <h3 style="margin-bottom:20px; font-weight:900;">Sözleşme Bilgileri</h3>
        <div class="form-group"><label>Müşteri / İşveren Adı</label><input type="text" id="clientName" value="Örnek Yatırım A.Ş."></div>
        <div class="form-group"><label>Proje Adı ve Konumu</label><input type="text" id="projectName" value="Kadıköy 24 Daireli Konut Projesi"></div>
        <div class="form-group"><label>Toplam Sözleşme Bedeli (TL)</label><input type="text" id="price" value="35.500.000 TL"></div>
        <div class="form-group"><label>İşin Teslim Süresi</label><input type="text" id="duration" value="12 Ay"></div>
        
        <button class="btn" onclick="generatePDF()">📄 PDF Olarak İndir</button>
        <button class="btn btn-word" onclick="exportWord()">📝 Word (.doc) Olarak İndir</button>
    </div>

    <div class="document-wrapper" id="contractDoc">
        <div class="doc-header">
            <h1 class="doc-title">İNŞAAT TAAHHÜT VE YAPIM SÖZLEŞMESİ</h1>
            <p style="font-size:12px; color:#555;">Tarih: <span id="outDate"></span></p>
        </div>
        <div class="doc-content">
            <strong>MADDE 1 - TARAFLAR</strong><br>
            Bir tarafta WORKSİTEM (Kainta İnşaat) (Bundan böyle "YÜKLENİCİ" olarak anılacaktır) ile diğer tarafta <strong id="outClient" style="color:blue;">Örnek Yatırım A.Ş.</strong> (Bundan böyle "İŞVEREN" olarak anılacaktır) arasında aşağıdaki şartlarda anlaşmaya varılmıştır.<br><br>

            <strong>MADDE 2 - İŞİN KONUSU</strong><br>
            İşbu sözleşmenin konusu; İŞVEREN'e ait <strong id="outProject" style="color:blue;">Kadıköy 24 Daireli Konut Projesi</strong> işlerinin, ÇŞB standartlarına, fen ve sanat kurallarına uygun olarak anahtar teslim yapılmasıdır.<br><br>

            <strong>MADDE 3 - SÖZLEŞME BEDELİ VE SÜRESİ</strong><br>
            Taraflar, sözleşme konusu işin toplam bedelinin KDV hariç <strong id="outPrice" style="color:blue;">35.500.000 TL</strong> olması hususunda mutabık kalmıştır. İşin teslim süresi, yer teslimi yapıldığı tarihten itibaren <strong id="outDuration" style="color:blue;">12 Ay</strong> olarak belirlenmiştir.<br><br>

            <strong>MADDE 4 - ÖDEME PLANI VE HAKEDİŞ</strong><br>
            İşveren, sözleşme bedelini aylık imalat hakediş raporlarına istinaden nakden ve defaten ödeyecektir. İmalat onaylanmadan hakediş düzenlenemez.<br><br>
            
            <em>İşbu 4 (dört) maddelik sözleşme taraflarca okunup, mutabakata varılarak imza altına alınmıştır.</em>
        </div>
        <div class="doc-signatures">
            <div class="sign-box"><div class="sign-line">İŞVEREN<br><span id="outClientSign">Örnek Yatırım A.Ş.</span></div></div>
            <div class="sign-box"><div class="sign-line">YÜKLENİCİ<br>WORKSİTEM / Kainta İnşaat</div></div>
        </div>
    </div>
</div>

<script>
    // Canlı Önizleme Güncellemesi
    const inputs = ['clientName', 'projectName', 'price', 'duration'];
    inputs.forEach(id => {
        document.getElementById(id).addEventListener('input', updateDoc);
    });

    function updateDoc() {
        document.getElementById('outClient').innerText = document.getElementById('clientName').value;
        document.getElementById('outClientSign').innerText = document.getElementById('clientName').value;
        document.getElementById('outProject').innerText = document.getElementById('projectName').value;
        document.getElementById('outPrice').innerText = document.getElementById('price').value;
        document.getElementById('outDuration').innerText = document.getElementById('duration').value;
        
        let today = new Date();
        document.getElementById('outDate').innerText = today.toLocaleDateString('tr-TR');
    }
    updateDoc(); // İlk yüklemede çalıştır

    // PDF İndirme (html2pdf)
    function generatePDF() {
        const element = document.getElementById('contractDoc');
        const opt = {
            margin: 0,
            filename: 'Worksitem_Sozlesme.pdf',
            image: { type: 'jpeg', quality: 0.98 },
            html2canvas: { scale: 2 },
            jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
        };
        html2pdf().set(opt).from(element).save();
    }

    // Word İndirme (Basit HTML to Doc)
    function exportWord() {
        var header = "<html xmlns:o='urn:schemas-microsoft-com:office:office' xmlns:w='urn:schemas-microsoft-com:office:word' xmlns='http://www.w3.org/TR/REC-html40'><head><meta charset='utf-8'><title>Export HTML to Word</title>    <meta name="csrf-token" content="{{ csrf_token() }}">
</head><body data-tool-slug="pdf-contract">";
        var footer = "    <script src="/js/tool-events.js" defer></script>
</body></html>";
        var sourceHTML = header + document.getElementById("contractDoc").innerHTML + footer;
        
        var source = 'data:application/vnd.ms-word;charset=utf-8,' + encodeURIComponent(sourceHTML);
        var fileDownload = document.createElement("a");
        document.body.appendChild(fileDownload);
        fileDownload.href = source;
        fileDownload.download = 'Worksitem_Sozlesme.doc';
        fileDownload.click();
        document.body.removeChild(fileDownload);
    }
</script>
    <script src="/js/tool-events.js" defer></script>
</body>
</html>
