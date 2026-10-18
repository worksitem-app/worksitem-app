<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kurumsal Sözleşme & Belge Oluşturucu | WORKSİTEM</title>
    <meta name="description" content="Anahtar teslim, kat karşılığı, taşeron sözleşmesi, teklif mektubu, kabul tutanağı gibi inşaat belgelerini şablondan hazırlayın ve indirin. Ücretsiz.">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <style>
        :root { --bg: #f5f6f8; --text: #101318; --line: #e4e7eb; --accent: #10b981; }
        body { font-family: 'Inter', sans-serif; background: var(--bg); margin: 0; padding-bottom:60px;}
        .header { background: #fff; padding: 15px 20px; border-bottom: 1px solid var(--line); display: flex; justify-content: space-between; position: sticky; top:0; z-index:10;}
        .container { max-width: 1300px; margin: 30px auto; padding: 0 20px; display: grid; grid-template-columns: 350px 1fr; gap: 30px; }
        @media(max-width: 900px) { .container { grid-template-columns: 1fr; } }
        
        .sidebar { background: #fff; padding: 25px; border-radius: 16px; border: 1px solid var(--line); height: fit-content; position: sticky; top: 80px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; font-size: 12px; font-weight: 800; margin-bottom: 5px; color:#333; }
        input, select { width: 100%; height: 45px; border: 1px solid var(--line); border-radius: 8px; padding: 0 10px; font-family: inherit; font-size:13px; outline:none;}
        input:focus, select:focus { border-color: #101318; }
        .btn { background: var(--accent); color: #fff; border: none; padding: 15px; width: 100%; border-radius: 12px; font-weight: 800; font-size:14px; cursor: pointer; margin-top: 10px; transition:.2s; }
        .btn:hover { opacity:0.9; }
        .btn-word { background: #101318; margin-top: 10px; }

        .legal-warning { background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.3); border-radius: 12px; padding: 15px; margin-bottom: 20px; }
        .legal-warning h4 { color: #ef4444; font-size: 13px; font-weight: 900; margin: 0 0 5px 0; }
        .legal-warning p { color: #7f1d1d; font-size: 11px; margin: 0; line-height: 1.5; }
        
        .document-wrapper { background: #fff; padding: 50px; box-shadow: 0 10px 40px rgba(0,0,0,0.1); width: 100%; max-width: 21cm; min-height: 29.7cm; margin: 0 auto; box-sizing: border-box; font-family: 'Times New Roman', serif; }
        .doc-header { text-align: center; border-bottom: 2px solid #111; padding-bottom: 20px; margin-bottom: 30px; }
        .doc-title { font-size: 22px; font-weight: bold; letter-spacing: 1px; text-transform: uppercase; }
        .doc-content { font-size: 14px; line-height: 1.8; color: #000; text-align: justify; }
        .dynamic-text { color: #101318; font-weight: bold; }
        .doc-signatures { display: flex; justify-content: space-between; margin-top: 100px; }
        .sign-box { text-align: center; width: 40%; font-size:14px; }
        .sign-line { border-top: 1px solid #111; margin-top: 50px; padding-top: 10px; font-weight: bold; }
    </style>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <x-calc.head />
</head>
<body class="wc" data-tool-slug="document-builder">

<x-site.contech-header />
<div class="wc-wrap" style="padding-bottom:0">
    <x-calc.hero title="İnşaat sözleşmesi taslağı oluşturucu" crumb="Belge oluşturucu" :facts="['Anahtar teslim, kat karşılığı, taşeron, malzeme', 'PDF ve Word olarak indir']">Sözleşme türünü seçin, tarafları ve bedeli girin; örnek sözleşme taslağını anında görün ve PDF ya da Word olarak indirin. Taslaklar hukuki tavsiye değildir, kullanmadan önce avukatınıza danışın.</x-calc.hero>
</div>

<div class="container">
    <div class="sidebar">
        {{-- YASAL UYARI KUTUSU --}}
        <div class="legal-warning">
            <h4>ÖNEMLİ YASAL UYARI</h4>
            <p>Burada sunulan tüm belgeler, yalnızca genel bilgi ve örnek amaçlı hazırlanmış taslaklardır. Her inşaat projesi ve hukuki durum kendine özgüdür. Bu taslak metinler, hukuki tavsiye niteliği taşımaz ve tüm yasal gereklilikleri eksiksiz olarak kapsamayabilir.<br><br>Bu belgeleri kullanmadan önce, güncel yasal mevzuata hakim bir <strong>avukata danışmanız</strong> ve belgeleri kendi özel durumunuza göre uyarlatmanız kesinlikle tavsiye edilir.<br><br>Taslakların kendi durumunuza uygunluğunu değerlendirmek sizin sorumluluğunuzdadır; Worksitem, kanunen sınırlandırılamayan haller dışında bu taslakların kullanımından doğan zararlardan sorumlu tutulamaz.</p>
        </div>

        <h3 style="margin-bottom:15px; font-weight:900; font-size:16px;">Sözleşme / Belge Türü</h3>
        
        <div class="form-group">
            <select id="contractType" onchange="changeTemplate()">
                <option value="anahtar_teslim">1. Anahtar Teslim İnşaat Sözleşmesi (Konut/Ticari)</option>
                <option value="kat_karsiligi">2. Kat Karşılığı (Arsa Payı) İnşaat Sözleşmesi</option>
                <option value="arsa_satis_vaadi">3. Arsa Satış Vaadi ve İnşaat Sözleşmesi</option>
                <option value="taseron">4. Taşeron (Alt Yüklenici) Sözleşmesi</option>
                <option value="malzeme">5. Malzeme Satın Alma Sözleşmesi</option>
                <option value="danismanlik">6. Proje Yönetimi / Danışmanlık Sözleşmesi</option>
                <option value="kiralama">7. Kiralama Sözleşmesi (İnşaat Ekipmanı)</option>
                <option value="isg">8. İş Sağlığı ve Güvenliği Taahhütnamesi (Taşeron İçin)</option>
                <option value="teklif">9. Teklif Mektubu (İnşaat İşi)</option>
                <option value="kabul">10. Geçici / Kesin Kabul Tutanağı</option>
            </select>
        </div>

        <h3 style="margin-bottom:15px; font-weight:900; font-size:16px;">Sözleşme Detayları</h3>
        <div class="form-group"><label>1. Taraf (İşveren / Arsa Sahibi / Müşteri)</label><input type="text" id="clientName" value="Örnek Yatırım A.Ş."></div>
        <div class="form-group"><label>2. Taraf (Yüklenici / Müteahhit / Taşeron)</label><input type="text" id="contractorName" value="Örnek Yüklenici A.Ş."></div>
        <div class="form-group"><label>Proje / İşin Adı</label><input type="text" id="projectName" value="Örnek 24 Daireli Konut Projesi"></div>
        <div class="form-group"><label>Bedel / Oran (TL veya %)</label><input type="text" id="price" value="35.500.000 TL"></div>
        <div class="form-group"><label>Süre / Tarih</label><input type="text" id="duration" value="12 Ay"></div>
        
        <button class="btn" onclick="generatePDF()">📄 PDF Olarak İndir</button>
        <button class="btn btn-word" onclick="exportWord()">📝 Word (.doc) Olarak İndir</button>
    </div>

    <div class="document-wrapper" id="contractDoc">
        <div class="doc-header">
            <div class="doc-title" id="outTitle" role="heading" aria-level="2">ANAHTAR TESLİM İNŞAAT SÖZLEŞMESİ</div>
            <p style="font-size:12px; color:#555;">Düzenleme Tarihi: <span id="outDate"></span></p>
        </div>
        <div class="doc-content" id="outContent">
            {{-- Dinamik Şablon İçeriği --}}
        </div>
        <div class="doc-signatures">
            <div class="sign-box"><div class="sign-line"><span id="outSign1">İŞVEREN</span><br><br><span class="dynamic-text" id="outClientSign">Örnek Yatırım A.Ş.</span></div></div>
            <div class="sign-box"><div class="sign-line"><span id="outSign2">YÜKLENİCİ</span><br><br><span class="dynamic-text" id="outContractorSign">Örnek Yüklenici A.Ş.</span></div></div>
        </div>
    </div>
</div>

<script>
    const templates = {
        anahtar_teslim: {
            title: "ANAHTAR TESLİM İNŞAAT SÖZLEŞMESİ",
            sign1: "İŞVEREN", sign2: "MÜTEAHHİT",
            body: `<strong>MADDE 1 - TARAFLAR</strong><br>
            Bir tarafta <span class="dynamic-text outContractor"></span> (Bundan böyle "MÜTEAHHİT" olarak anılacaktır) ile diğer tarafta <span class="dynamic-text outClient"></span> (Bundan böyle "İŞVEREN" olarak anılacaktır) arasında aşağıdaki şartlarda anlaşmaya varılmıştır.<br><br>
            <strong>MADDE 2 - İŞİN KONUSU</strong><br>
            İşbu sözleşmenin konusu; İŞVEREN'e ait <span class="dynamic-text outProject"></span> işlerinin, projelerine, teknik şartnamelere ve fen kurallarına uygun olarak anahtar teslim yapılmasıdır.<br><br>
            <strong>MADDE 3 - BEDEL VE SÜRE</strong><br>
            İşin toplam bedeli KDV hariç <span class="dynamic-text outPrice"></span> olarak belirlenmiştir. İşin teslim süresi, yer teslimi yapıldığı tarihten itibaren <span class="dynamic-text outDuration"></span>'dır.<br><br>
            <strong>MADDE 4 - ÖDEME ŞEKLİ VE HAKEDİŞ</strong><br>
            İşveren, sözleşme bedelini aylık imalat hakediş raporlarına istinaden ödeyecektir. İmalat onaylanmadan hakediş düzenlenemez ve ödeme yapılamaz.`
        },
        kat_karsiligi: {
            title: "KAT KARŞILIĞI (ARSA PAYI) İNŞAAT SÖZLEŞMESİ",
            sign1: "ARSA SAHİBİ", sign2: "MÜTEAHHİT",
            body: `<strong>MADDE 1 - TARAFLAR</strong><br>
            Bir tarafta arsa maliki <span class="dynamic-text outClient"></span> (Bundan böyle "ARSA SAHİBİ" olarak anılacaktır) ile diğer tarafta <span class="dynamic-text outContractor"></span> (Bundan böyle "MÜTEAHHİT" olarak anılacaktır) arasında gayrimenkul satış vaadi ve arsa payı karşılığı inşaat yapım sözleşmesi imzalanmıştır.<br><br>
            <strong>MADDE 2 - İŞİN KONUSU</strong><br>
            İşbu sözleşmenin konusu; mülkiyeti ARSA SAHİBİ'ne ait olan taşınmaz üzerinde <span class="dynamic-text outProject"></span> projesinin yapımı ve bağımsız bölümlerin paylaşımıdır.<br><br>
            <strong>MADDE 3 - PAYLAŞIM ORANI VE TESLİM SÜRESİ</strong><br>
            Taraflar, yapılacak bağımsız bölümlerin <span class="dynamic-text outPrice"></span> oranında paylaşılması hususunda mutabık kalmıştır. İnşaatın tamamlanıp iskanın alınması için öngörülen süre ruhsat tarihinden itibaren <span class="dynamic-text outDuration"></span>'dır.<br><br>
            <strong>MADDE 4 - GİDERLER VE HARÇLAR</strong><br>
            Proje çizimi, yapı denetim, ruhsat harçları, SGK primleri ve tüm inşaat yapım maliyetleri tamamen MÜTEAHHİT'e aittir.`
        },
        arsa_satis_vaadi: {
            title: "ARSA SATIŞ VAADİ VE İNŞAAT SÖZLEŞMESİ",
            sign1: "ARSA SAHİBİ", sign2: "ALICI / YÜKLENİCİ",
            body: `<strong>MADDE 1 - TARAFLAR</strong><br>
            Arsa maliki <span class="dynamic-text outClient"></span> ile Alıcı/Yüklenici <span class="dynamic-text outContractor"></span> arasında akdedilmiştir.<br><br>
            <strong>MADDE 2 - KONU</strong><br>
            <span class="dynamic-text outProject"></span> üzerine yapılacak inşaat faaliyeti karşılığında arsa paylarının devri ve satışının vaat edilmesidir.<br><br>
            <strong>MADDE 3 - BEDEL VE SÜRE</strong><br>
            Sözleşmeye konu arsa paylarının satış bedeli / devir şartı <span class="dynamic-text outPrice"></span> olarak belirlenmiştir. Tarafların yükümlülüklerini yerine getirme süresi <span class="dynamic-text outDuration"></span>'dır.`
        },
        taseron: {
            title: "TAŞERON (ALT YÜKLENİCİ) SÖZLEŞMESİ",
            sign1: "ANA YÜKLENİCİ", sign2: "TAŞERON",
            body: `<strong>MADDE 1 - TARAFLAR</strong><br>
            Ana yüklenici <span class="dynamic-text outClient"></span> ile Alt yüklenici (Taşeron) <span class="dynamic-text outContractor"></span> arasında imzalanmıştır.<br><br>
            <strong>MADDE 2 - İŞİN KONUSU</strong><br>
            Taşeron, <span class="dynamic-text outProject"></span> projesindeki taşere edilen belirli imalatları fen ve sanat kurallarına uygun olarak yapmayı taahhüt eder.<br><br>
            <strong>MADDE 3 - BEDEL VE SÜRE</strong><br>
            Taşeronun yapacağı işlerin toplam götürü bedeli / tahmini tutarı <span class="dynamic-text outPrice"></span>'dir. İşlerin tamamlanması için tanınan süre <span class="dynamic-text outDuration"></span>'dır.<br><br>
            <strong>MADDE 4 - İŞ GÜVENLİĞİ VE SİGORTA</strong><br>
            Taşeron, kendi çalışanlarının özlük haklarından, SGK primlerinden ve iş sağlığı güvenliği tedbirlerinden tek başına hukuken sorumludur.`
        },
        isg: {
            title: "İŞ SAĞLIĞI VE GÜVENLİĞİ TAAHHÜTNAMESİ",
            sign1: "PROJE YÖNETİMİ / İŞVEREN", sign2: "TAŞERON / ÇALIŞAN",
            body: `<strong>KAPSAM VE TAAHHÜT</strong><br>
            Bu taahhütname, <span class="dynamic-text outProject"></span> şantiyesinde çalışacak olan <span class="dynamic-text outContractor"></span> ile şantiye yönetimi <span class="dynamic-text outClient"></span> arasında İSG kurallarının kabul beyanıdır.<br><br>
            <strong>KURALLAR</strong><br>
            1. Şantiye alanında baret, fosforlu yelek ve çelik burunlu iş ayakkabısı olmadan kesinlikle çalışmayacağımı,<br>
            2. Yüksekte çalışmalarda standartlara uygun emniyet kemeri kullanacağımı,<br>
            3. Mesleki Yeterlilik Belgem (MYK) ve yasal İSG Eğitimim olmadan işbaşı yapmayacağımı,<br>
            4. İhlal durumunda hak edişimden <span class="dynamic-text outPrice"></span> tutarında cezai kesinti yapılmasını kabul ettiğimi,<br>
            <span class="dynamic-text outDuration"></span> tarihi itibariyle gayrikabili rücu beyan ve taahhüt ederim.`
        },
        teklif: {
            title: "İNŞAAT İŞİ FİYAT TEKLİF MEKTUBU",
            sign1: "MÜŞTERİ / İŞVEREN", sign2: "TEKLİFİ SUNAN FİRMA",
            body: `<strong>SAYIN:</strong> <span class="dynamic-text outClient"></span><br><br>
            Firmanızın/Şahsınızın tarafımıza ilettiği taleplere ve teknik dokümanlara istinaden, <span class="dynamic-text outProject"></span> işinin yapımına talibiz.<br><br>
            <strong>TEKLİF BEDELİ VE SÜRE</strong><br>
            Söz konusu işi projesine ve standartlara uygun olarak KDV hariç <span class="dynamic-text outPrice"></span> bedel ile yapmayı taahhüt ediyoruz.<br>
            Yer teslimi yapıldığı günden itibaren işi <span class="dynamic-text outDuration"></span> içerisinde eksiksiz teslim edeceğiz.<br><br>
            <strong>GEÇERLİLİK</strong><br>
            İşbu teklifimiz sunulduğu tarihten itibaren 15 gün süreyle geçerlidir.`
        },
        danismanlik: {
            title: "PROJE YÖNETİMİ VE DANIŞMANLIK SÖZLEŞMESİ",
            sign1: "İŞVEREN", sign2: "DANIŞMAN FİRMA",
            body: `<strong>MADDE 1 - TARAFLAR</strong><br>
            İşveren <span class="dynamic-text outClient"></span> ile Müşavir/Danışman firma <span class="dynamic-text outContractor"></span> arasında imzalanmıştır.<br><br>
            <strong>MADDE 2 - KONU</strong><br>
            <span class="dynamic-text outProject"></span> projesinin mimari, statik, resmi onay ve ihale süreçlerinin İşveren adına yönetilmesi ve denetlenmesidir.<br><br>
            <strong>MADDE 3 - HİZMET BEDELİ</strong><br>
            Sözleşme kapsamındaki danışmanlık hizmet bedeli <span class="dynamic-text outPrice"></span> olarak belirlenmiştir. Hizmet süresi <span class="dynamic-text outDuration"></span>'dır.`
        },
        kabul: {
            title: "GEÇİCİ / KESİN KABUL TUTANAĞI",
            sign1: "KABUL KOMİSYONU / İŞVEREN", sign2: "YÜKLENİCİ FİRMA",
            body: `<strong>PROJE BİLGİSİ</strong><br>
            Yüklenici <span class="dynamic-text outContractor"></span> tarafından yapımı üstlenilen <span class="dynamic-text outProject"></span> işi, İşveren <span class="dynamic-text outClient"></span> teknik yetkililerince sahada incelenmiştir.<br><br>
            <strong>KABUL DURUMU</strong><br>
            Sözleşme bedeli <span class="dynamic-text outPrice"></span> olan işin, teknik şartnamelere ve projesine uygun olarak <span class="dynamic-text outDuration"></span> tarihinde fiilen bitirildiği tespit edilmiştir.<br><br>
            <strong>SONUÇ</strong><br>
            Komisyonumuzca yapılan inceleme neticesinde, işin kullanımına engel teşkil edecek bir kusur bulunmadığı görülmüş ve kabul işleminin yapılmasına karar verilmiştir.`
        },
        malzeme: {
            title: "MALZEME TEDARİK VE SATIN ALMA SÖZLEŞMESİ",
            sign1: "ALICI FİRMA", sign2: "SATICI / TEDARİKÇİ",
            body: `<strong>MADDE 1 - TARAFLAR</strong><br>
            Alıcı <span class="dynamic-text outClient"></span> ile Satıcı/Tedarikçi <span class="dynamic-text outContractor"></span> arasında imzalanmıştır.<br><br>
            <strong>MADDE 2 - KONU</strong><br>
            Alıcı'nın yapımını üstlendiği <span class="dynamic-text outProject"></span> şantiyesi için gerekli yapı malzemelerinin tedariği ve teslimidir.<br><br>
            <strong>MADDE 3 - BEDEL VE TESLİMAT</strong><br>
            Sözleşme kapsamındaki malzemelerin toplam bedeli <span class="dynamic-text outPrice"></span>'dir. Teslimat süresi sipariş onayından itibaren <span class="dynamic-text outDuration"></span>'dır.`
        },
        kiralama: {
            title: "İNŞAAT EKİPMANI VE İŞ MAKİNESİ KİRALAMA SÖZLEŞMESİ",
            sign1: "KİRALAYAN (MÜŞTERİ)", sign2: "KİRAYA VEREN FİRMA",
            body: `<strong>MADDE 1 - TARAFLAR</strong><br>
            Kiralayan şantiye yetkilisi <span class="dynamic-text outClient"></span> ile Kiraya veren ekipman sahibi <span class="dynamic-text outContractor"></span> arasında akdedilmiştir.<br><br>
            <strong>MADDE 2 - KONU VE SÜRE</strong><br>
            <span class="dynamic-text outProject"></span> işinde kullanılmak üzere belirlenen iş makinesi ve ekipmanlarının kiralanmasıdır. Kiralama süresi <span class="dynamic-text outDuration"></span>'dır.<br><br>
            <strong>MADDE 3 - KİRA BEDELİ VE YAKIT</strong><br>
            Kira bedeli KDV hariç <span class="dynamic-text outPrice"></span>'dir. Makinenin rutin bakımları Kiraya Verene; günlük yakıt (mazot) sarfiyatı ve güvenliği Kiralayana aittir.`
        }
    };

    function changeTemplate() {
        const type = document.getElementById('contractType').value;
        const temp = templates[type];
        
        document.getElementById('outTitle').innerText = temp.title;
        document.getElementById('outContent').innerHTML = temp.body;
        document.getElementById('outSign1').innerText = temp.sign1;
        document.getElementById('outSign2').innerText = temp.sign2;
        
        updateDoc();
    }

    function updateDoc() {
        const c1 = document.getElementById('clientName').value;
        const c2 = document.getElementById('contractorName').value;
        const p = document.getElementById('projectName').value;
        const pr = document.getElementById('price').value;
        const d = document.getElementById('duration').value;

        document.getElementById('outClientSign').innerText = c1;
        document.getElementById('outContractorSign').innerText = c2;
        document.getElementById('outDate').innerText = new Date().toLocaleDateString('tr-TR');

        document.querySelectorAll('.outClient').forEach(el => el.innerText = c1);
        document.querySelectorAll('.outContractor').forEach(el => el.innerText = c2);
        document.querySelectorAll('.outProject').forEach(el => el.innerText = p);
        document.querySelectorAll('.outPrice').forEach(el => el.innerText = pr);
        document.querySelectorAll('.outDuration').forEach(el => el.innerText = d);
    }

    const inputsToListen = ['clientName', 'contractorName', 'projectName', 'price', 'duration'];
    inputsToListen.forEach(id => { document.getElementById(id).addEventListener('input', updateDoc); });

    changeTemplate();

    function generatePDF() {
        const element = document.getElementById('contractDoc');
        const opt = {
            margin: [10, 0, 10, 0],
            filename: 'Sozlesme_Taslagi.pdf',
            image: { type: 'jpeg', quality: 0.98 },
            html2canvas: { scale: 2 },
            jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
        };
        // Tüm araç çıktılarında aynı Worksitem logolu başlık ve alt bilgi
        const wrap = document.createElement('div');
        const d = new Date().toLocaleDateString('tr-TR');
        wrap.innerHTML = '<div style="display:flex;justify-content:space-between;align-items:center;padding:0 18mm 8px;margin-bottom:10px;border-bottom:3px solid #FFB800;font-family:Inter,Arial,sans-serif">'
            + '<img src="' + location.origin + '/brand/worksitem-logo.png" style="height:28px" alt="Worksitem">'
            + '<span style="font-size:11px;color:#5B6472">Taslak · ' + d + '</span></div>';
        wrap.appendChild(element.cloneNode(true));
        wrap.insertAdjacentHTML('beforeend', '<div style="margin-top:12px;padding:6px 18mm 0;border-top:1px solid #E3E6EA;font:10px Inter,Arial,sans-serif;color:#8b93a0;text-align:center">worksitem.com ile hazırlanan taslaktır; imzadan önce hukuki kontrol önerilir.</div>');
        html2pdf().set(opt).from(wrap).save();
    }

    function exportWord() {
        var header = "<html xmlns:o='urn:schemas-microsoft-com:office:office' xmlns:w='urn:schemas-microsoft-com:office:word' xmlns='http://www.w3.org/TR/REC-html40'><head><meta charset='utf-8'><title>Sozlesme Taslagi</title></he" + "ad><bo" + "dy>";
        var footer = "</bo" + "dy></ht" + "ml>"; // etiketler bölünür (sunucu katmanları sayfanın kapanış etiketini arar)
        var sourceHTML = header + document.getElementById("contractDoc").innerHTML + footer;
        var source = 'data:application/vnd.ms-word;charset=utf-8,' + encodeURIComponent(sourceHTML);
        var fileDownload = document.createElement("a");
        document.body.appendChild(fileDownload);
        fileDownload.href = source;
        fileDownload.download = 'Sozlesme_Taslagi.doc';
        fileDownload.click();
        document.body.removeChild(fileDownload);
    }
</script>
    <script src="/js/tool-events.js" defer></script>
    <x-site.contech-footer />
</body>
</html>
