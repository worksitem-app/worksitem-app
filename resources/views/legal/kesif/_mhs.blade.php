@php extract(\App\Support\InspectionOffer::docVars($lead ?? null)); $provider = $co['legal_name'] ?? 'Kainta İnşaat'; @endphp
<h2>Madde 1 — Taraflar</h2>
<p>
    <strong>1.1. Hizmet Sağlayıcı:</strong> {{ $provider }}@if (! empty($co['mersis'])), MERSİS: {{ $co['mersis'] }}@endif,
    Adres: {{ $co['address'] ?? '' }}, E-posta: {{ $co['email'] ?? '' }}@if (! empty($co['kep'])), KEP: {{ $co['kep'] }}@endif
    ("Hizmet Sağlayıcı").
</p>
<p>
    <strong>1.2. Alıcı:</strong> {!! $f($o['name'], 'Ad Soyad') !!}, Telefon: {!! $f($o['phone'], 'Telefon') !!},
    Keşif yeri: {!! $f($o['place'], 'Şehir / ilçe') !!} ("Alıcı").
</p>
<p>Hizmet Sağlayıcı ve Alıcı birlikte "Taraflar" olarak anılır.</p>

<h2>Madde 2 — Sözleşmenin Konusu</h2>
<p>
    İşbu Sözleşme'nin konusu, Alıcı'nın Hizmet Sağlayıcı'nın işlettiği worksitem.com ("Platform") üzerinden elektronik ortamda
    siparişini verdiği yerinde keşif hizmetinin sunulmasına ilişkin olarak, 6502 sayılı Kanun ve Mesafeli Sözleşmeler Yönetmeliği
    uyarınca Taraflar'ın hak ve yükümlülüklerinin belirlenmesidir.
</p>

<h2>Madde 3 — Tanımlar</h2>
<ul>
    <li><strong>Hizmet:</strong> Ön Bilgilendirme Formu'nda kapsamı belirtilen yerinde keşif hizmeti.</li>
    <li><strong>Keşif Raporu:</strong> Keşif sonucunda hazırlanan ölçü, metraj, mevcut durum tespiti ve yazılı fiyat teklifini içeren belge.</li>
    <li><strong>Randevu:</strong> Keşfin yapılacağı, Taraflarca birlikte belirlenen tarih ve saat.</li>
    <li><strong>Kalıcı Veri Saklayıcısı:</strong> E-posta, PDF ve benzeri, bilginin değiştirilmeden saklanmasına ve erişilmesine imkân veren araçlar.</li>
</ul>

<h2>Madde 4 — Bedel, Ödeme ve Mahsup</h2>
<p>
    4.1. Hizmet bedeli {{ $tl($a['net']) }} + KDV (%{{ $vatRate }}) olmak üzere toplam <strong>{{ $tl($a['total']) }}</strong>'dir.
    Hizmetin kapsamı, ödeme şekli ve diğer koşullar Alıcı'nın onayladığı Ön Bilgilendirme Formu'nda yer alır; form işbu Sözleşme'nin ayrılmaz parçasıdır.
</p>
<p>
    4.2. Bedel, randevu kesinleşmeden önce kart (iyzico 3D Secure) veya havale / EFT ile peşin ödenir. Ödeme yapılmadan randevu kesinleşmez.
    Seçilen yöntem: {!! $f($o['method'], 'Sipariş sırasında seçilir') !!}.
</p>
<p>
    4.3. Keşif tarihinden itibaren {{ $days }} gün içinde aynı iş için Taraflar arasında yazılı uygulama sözleşmesi imzalanırsa, ödenen keşif
    bedeli toplam iş bedelinden düşülür. Sözleşme imzalanmazsa keşif bedeli iade edilmez; Keşif Raporu Alıcı'da kalır.
</p>

<h2>Madde 5 — Randevu ve İfa</h2>
<p>
    5.1. Hizmet Sağlayıcı, ödemenin onaylanmasından itibaren en geç {{ $ic['appointment_days'] }} iş günü içindeki bir tarihte keşfi yapmak
    üzere Alıcı ile randevu belirler. Keşif Raporu ve yazılı teklif, keşiften sonraki {{ $ic['report_days'] }} iş günü içinde Alıcı'ya iletilir.
</p>
<p>
    5.2. Alıcı, randevudan en az {{ $ic['reschedule_hours'] }} saat önce bildirmek kaydıyla randevuyu ücretsiz olarak yeniden planlayabilir.
    Hizmet Sağlayıcı randevuya gelemeyecekse bunu en kısa sürede bildirir ve yeni randevu önerir.
</p>
<p>
    5.3. Hizmet, sipariş tarihinden itibaren en geç 30 gün içinde ifa edilir. Hizmet Sağlayıcı'dan kaynaklanan nedenle bu süre içinde ifa
    edilemezse Alıcı sözleşmeyi feshedebilir ve ödediği bedelin tamamı 14 gün içinde iade edilir.
</p>

<h2>Madde 6 — Hizmet Sağlayıcı'nın Yükümlülükleri</h2>
<ul>
    <li>Keşfi, konusunda yetkin mühendis veya teknik personel ile, mesleki özen ve dürüstlük kuralları çerçevesinde yapmak.</li>
    <li>Keşif Raporu'nu ve yazılı teklifi süresinde, açık ve anlaşılır biçimde iletmek; teklifin geçerlilik süresini teklifte yazmak.</li>
    <li>Alıcı'nın talep ve şikâyetlerini en geç 5 iş günü içinde yanıtlamak.</li>
    <li>Ayıplı ifa halinde Alıcı'nın 6502 sayılı Kanun'un 15. maddesindeki seçimlik haklarına (yeniden ifa, bedelden indirim, sözleşmeden dönme, tazminat) saygı göstermek.</li>
</ul>

<h2>Madde 7 — Alıcı'nın Yükümlülükleri</h2>
<ul>
    <li>Keşif yerinin açık adresini ve iletişim bilgilerini doğru vermek; randevu saatinde mekâna erişimi sağlamak.</li>
    <li>Mekânın maliki değilse, malikin veya yöneticinin keşif için iznini almak; ortak alanlarda gerekli bildirimleri yapmak.</li>
    <li>Bilinen sorunları (su kaçağı, nem, eski tesisat, yapısal hasar vb.) keşif sırasında Hizmet Sağlayıcı'ya bildirmek.</li>
</ul>

<h2>Madde 8 — Keşif Raporunun Niteliği</h2>
<p>
    8.1. Keşif Raporu, keşif günündeki gözlem ve ölçümlere dayanır. Duvar, zemin veya tavan içinde kalan ve kırım, söküm ya da
    özel test yapılmadan görülemeyen kusurlar (gizli tesisat arızaları, nem, yapısal hasar vb.) raporda yer almayabilir; bu durum
    uygulama sırasında ortaya çıkarsa Taraflarca ayrıca değerlendirilir.
</p>
<p>
    8.2. Keşif Raporu bir mühendislik statik hesabı, zemin etüdü veya resmî belge değildir. Taşıyıcı sisteme müdahale içeren işler için
    yetkili mühendis tarafından ayrıca proje ve hesap hazırlanması gerekir.
</p>
<p>
    8.3. Keşif Raporu, Alıcı'nın kendi kullanımı içindir; Alıcı raporu başka bir yükleniciden teklif almak için de kullanabilir.
    Raporun Hizmet Sağlayıcı'nın marka ve logosuyla ticari amaçla çoğaltılması ve yayımlanması yasaktır.
</p>

<h2>Madde 9 — Kişisel Verilerin Korunması</h2>
<p>
    Hizmet Sağlayıcı, Alıcı'nın kişisel verilerini 6698 sayılı KVKK'ya uygun olarak ve
    <a href="/legal/kvkk-aydinlatma">KVKK Aydınlatma Metni</a>'nde belirtilen amaçlarla işler. Ticari elektronik ileti ancak Alıcı'nın
    ayrıca vereceği onayla gönderilir ve bu onay her zaman ücretsiz olarak geri alınabilir.
</p>

<h2>Madde 10 — Cayma Hakkı</h2>
<p>
    10.1. Alıcı, Sözleşme'nin kurulduğu günden itibaren 14 gün içinde, herhangi bir gerekçe göstermeksizin ve cezai şart ödemeksizin
    Sözleşme'den cayabilir. Bildirim <a href="mailto:{{ $ic['cancel_email'] }}">{{ $ic['cancel_email'] }}</a> adresine e-posta ile veya yazılı olarak yapılır.
</p>
<p>
    10.2. Hizmet Sağlayıcı, cayma bildiriminin kendisine ulaştığı tarihten itibaren 14 gün içinde tahsil ettiği bedeli, ödemenin yapıldığı
    araçla ve masrafsız olarak iade eder.
</p>
<p>
    10.3. Mesafeli Sözleşmeler Yönetmeliği'nin 15. maddesi uyarınca, cayma süresi dolmadan Alıcı'nın onayıyla ifasına başlanan hizmetlerde
    cayma hakkı kullanılamaz. Alıcı'nın sipariş sırasında keşfin 14 günlük süre dolmadan yapılmasını açıkça talep etmesi halinde, keşif
    yerinde yapıldıktan sonra cayma hakkı sona erer. Bu istisna, yalnızca Alıcı'nın ayrı ve önceden işaretlenmemiş bir kutuyla onay verdiği
    hallerde uygulanır (Ek-2).
</p>
<p>
    10.4. Ön Bilgilendirme Formu'nda cayma hakkına ilişkin bilgilendirme yapılmamışsa Alıcı, cayma hakkını 14 günlük süreye bağlı olmaksızın,
    bu sürenin bitiminden itibaren bir yıl içinde kullanabilir.
</p>

<h2>Madde 11 — Sorumluluğun Sınırlandırılması</h2>
<p>
    11.1. Hizmet Sağlayıcı; Alıcı'nın yanlış veya eksik bilgi vermesinden, mekâna erişim sağlanamamasından ve Madde 8.1'de belirtilen,
    keşif sırasında makul özenle tespit edilemeyecek gizli kusurlardan doğan sonuçlardan sorumlu değildir.
</p>
<p>
    11.2. Bu madde, Hizmet Sağlayıcı'nın kast veya ağır ihmalinden doğan sorumluluğunu ve 6502 sayılı Kanun'dan doğan, tüketici aleyhine
    sınırlandırılamayan hakları ortadan kaldırmaz. Tüketici aleyhine olan hükümler, Kanun'un 5. maddesi uyarınca haksız şart niteliğinde
    olup Alıcı'yı bağlamaz.
</p>

<h2>Madde 12 — Mücbir Sebep</h2>
<p>
    Doğal afet, salgın, savaş, terör, yaygın altyapı veya enerji kesintisi, resmî makam kararları gibi Taraflar'ın kontrolü dışındaki olaylar
    nedeniyle yükümlülüklerin ifa edilememesinden Taraflar sorumlu tutulamaz. Mücbir sebep 30 günü aşarsa Alıcı Sözleşme'yi feshederek
    ödediği bedelin iadesini talep edebilir.
</p>

<h2>Madde 13 — Delil Sözleşmesi</h2>
<p>
    Uyuşmazlıklarda Hizmet Sağlayıcı'nın sistem kayıtları, e-posta ve WhatsApp yazışmaları ile elektronik onay kayıtları, 6100 sayılı HMK'nın
    193. maddesi kapsamında delil teşkil eder. Bu hüküm, Alıcı'nın karşı delil sunma hakkını ortadan kaldırmaz.
</p>

<h2>Madde 14 — Uyuşmazlıkların Çözümü</h2>
<p>
    İşbu Sözleşme'den doğan uyuşmazlıklarda, Ticaret Bakanlığınca ilan edilen parasal sınırlar dahilinde Alıcı'nın veya Hizmet Sağlayıcı'nın
    yerleşim yerindeki Tüketici Hakem Heyetleri, bu sınırların üzerindeki uyuşmazlıklarda Tüketici Mahkemeleri yetkilidir. Sözleşme'ye Türk hukuku uygulanır.
</p>

<h2>Madde 15 — Yürürlük</h2>
<p>
    İşbu Sözleşme 15 maddeden ve 2 ekten ibaret olup, Alıcı'nın Platform üzerinden siparişi elektronik ortamda onaylaması ile kurulmuş ve
    yürürlüğe girmiştir. Sözleşme'nin bir kopyası sipariş onayının ardından Alıcı'ya PDF olarak sunulur ve talep halinde e-posta ile gönderilir.
</p>

<table class="lg-sign-t" style="width:100%;margin-top:18px;border-collapse:separate;border-spacing:8px 0">
    <tr>
        <td style="border:1px dashed #C9CED6;border-radius:12px;padding:12px;vertical-align:top;width:33%"><b style="display:block;font-size:11px;color:#5B6472;text-transform:uppercase">Hizmet Sağlayıcı</b>{{ $provider }}</td>
        <td style="border:1px dashed #C9CED6;border-radius:12px;padding:12px;vertical-align:top;width:33%"><b style="display:block;font-size:11px;color:#5B6472;text-transform:uppercase">Alıcı</b>{!! $f($o['name'], 'Ad Soyad') !!} — elektronik onay</td>
        <td style="border:1px dashed #C9CED6;border-radius:12px;padding:12px;vertical-align:top;width:33%"><b style="display:block;font-size:11px;color:#5B6472;text-transform:uppercase">Sipariş no / tarih</b>{!! $f($o['order_no'], 'Sipariş no') !!} / {!! $f($o['date'], 'tarih') !!}</td>
    </tr>
</table>

<h2>Ek-1 — Cayma Formu</h2>
<p>(Bu formu yalnızca sözleşmeden cayma hakkınızı kullanmak istediğinizde doldurup gönderiniz.)</p>
<p>Kime: {{ $provider }}, {{ $co['address'] ?? '' }}, {{ $ic['cancel_email'] }}</p>
<p>Bu formla aşağıdaki hizmetin satımına ilişkin sözleşmeden cayma hakkımı kullandığımı bildiririm.</p>
<table class="lg-kv">
    <tr><td>Hizmet</td><td>Yerinde keşif ve keşif raporu</td></tr>
    <tr><td>Sipariş (talep) no</td><td>{!! $f($o['order_no'], ' ') !!}</td></tr>
    <tr><td>Sipariş tarihi</td><td>{!! $f($o['date'], ' ') !!}</td></tr>
    <tr><td>Alıcı ad soyad</td><td>{!! $f($o['name'], ' ') !!}</td></tr>
    <tr><td>Alıcı adresi</td><td><span class="lg-field">&nbsp;</span></td></tr>
    <tr><td>Tarih</td><td><span class="lg-field">&nbsp;</span></td></tr>
    <tr><td>İmza (yalnızca kâğıt üzerinde gönderiliyorsa)</td><td><span class="lg-field">&nbsp;</span></td></tr>
</table>

<h2>Ek-2 — Sipariş Onay Kutusu Metinleri</h2>
<p>Aşağıdaki kutular ödeme düğmesinden önce yer alır, önceden işaretlenmiş değildir ve her biri ayrı alınır:</p>
<ol>
    @foreach (\App\Support\InspectionOffer::consentTexts() as $text)
        <li>{{ $text }}</li>
    @endforeach
</ol>
<p>Ödeme düğmesi metni: <strong>"Siparişi Onayla ve Öde"</strong>.</p>
