@php extract(\App\Support\InspectionOffer::docVars($lead ?? null)); @endphp
<p>
    İşbu Ön Bilgilendirme Formu, 6502 sayılı Tüketicinin Korunması Hakkında Kanun ve Mesafeli Sözleşmeler
    Yönetmeliği uyarınca, yerinde keşif hizmetine ilişkin Mesafeli Hizmet Sözleşmesi kurulmadan önce Alıcı'yı
    bilgilendirmek amacıyla düzenlenmiştir. Form, ödeme yükümlülüğü doğuran sipariş onayından önce gösterilir
    ve ayrı bir onay kutusuyla teyit alınır.
</p>

<h2>1. Hizmet Sağlayıcı (Satıcı) Bilgileri</h2>
<table class="lg-kv">
    @foreach ($seller as $k => $v)<tr><td>{{ $k }}</td><td>{{ $v }}</td></tr>@endforeach
</table>

<h2>2. Alıcı Bilgileri</h2>
<table class="lg-kv">
    <tr><td>Ad soyad</td><td>{!! $f($o['name'], 'Sipariş sırasında girilen ad soyad') !!}</td></tr>
    <tr><td>Telefon</td><td>{!! $f($o['phone'], 'Sipariş sırasında girilen telefon') !!}</td></tr>
    <tr><td>E-posta</td><td>{!! $f($o['email'], 'Varsa e-posta adresi') !!}</td></tr>
    <tr><td>Keşif yeri</td><td>{!! $f($o['place'], 'Şehir / ilçe') !!} — açık adres randevu sırasında yazılı olarak teyit edilir</td></tr>
    @if ($inv && ($inv['type'] ?? '') === 'kurumsal')
        <tr><td>Fatura</td><td><span class="lg-filled">{{ $inv['title'] ?? '' }} · {{ $inv['tax_office'] ?? '' }} / {{ $inv['tax_no'] ?? '' }}</span></td></tr>
    @endif
</table>

<h2>3. Hizmetin Temel Nitelikleri</h2>
<table class="lg-kv">
    <tr><td>Hizmet</td><td>Yerinde keşif ve keşif raporu</td></tr>
    <tr><td>Kapsam</td><td>Mekânda ölçüm ve metraj, mevcut durumun tespiti ve fotoğraflanması, yapılacak işin kapsamının Alıcı ile netleştirilmesi, keşif raporu ve yazılı fiyat teklifi hazırlanması</td></tr>
    <tr><td>Proje</td><td>{!! $f($o['project'], 'Talepte belirtilen iş (ör. banyo tadilatı)') !!}</td></tr>
    <tr><td>Kapsam dışı</td><td>Statik / zemin etüdü, laboratuvar testleri, ruhsat ve proje işleri, kırım veya sondaj gerektiren tespitler ile işin kendisi (tadilat uygulaması ayrı bir sözleşmeye tabidir)</td></tr>
    <tr><td>İfa süresi</td><td>Keşif tarihi, ödemenin onaylanmasından itibaren en geç {{ $ic['appointment_days'] }} iş günü içindeki bir gün olarak taraflarca belirlenir. Keşif raporu ve yazılı teklif, keşiften sonraki {{ $ic['report_days'] }} iş günü içinde e-posta veya WhatsApp ile iletilir.</td></tr>
</table>

<h2>4. Fiyat ve Ödeme</h2>
<table class="lg-kv">
    <tr><td>Keşif bedeli (KDV hariç)</td><td>{{ $tl($a['net']) }}</td></tr>
    <tr><td>KDV (%{{ $vatRate }})</td><td>{{ $tl($a['vat']) }}</td></tr>
    <tr><td><strong>Toplam (vergiler dahil)</strong></td><td><strong>{{ $tl($a['total']) }}</strong></td></tr>
    <tr><td>Ek ücretler</td><td>Bulunmamaktadır. Hizmet bölgesi dışındaki bir adres için ulaşım ücreti gerekirse sipariş kesinleşmeden önce yazılı bildirilir; Alıcı kabul etmezse sipariş iptal edilir ve ödeme alınmaz ya da tamamı iade edilir.</td></tr>
    <tr><td>Ödeme yöntemi</td><td>{!! $f($o['method'], 'Kredi / banka kartı (iyzico) veya havale / EFT') !!}</td></tr>
</table>
<div class="lg-note">
    <strong>Mahsup:</strong> Keşif tarihinden itibaren {{ $days }} gün içinde aynı iş için {{ $co['legal_name'] ?? 'Kainta İnşaat' }} ile yazılı
    sözleşme imzalanırsa, ödenen keşif bedeli toplam iş bedelinden düşülür. Sözleşme imzalanmazsa keşif bedeli iade edilmez; keşif raporu Alıcı'da kalır.
</div>
<p>
    Kart ile ödemede tutar, iyzico altyapısı üzerinden 3D Secure ile tahsil edilir; kart bilgileri Hizmet Sağlayıcı tarafından
    görülmez veya saklanmaz. Havale / EFT ile ödemede açıklama alanına sipariş (talep) numarası yazılır. Fatura, ödemenin
    ardından {{ $co['legal_name'] ?? 'Kainta İnşaat' }} tarafından düzenlenir. Bu formdaki fiyat ve bilgiler sipariş onaylanana kadar geçerlidir.
</p>

<h2>5. Hizmetin İfası ve Randevu</h2>
<ul>
    <li>Ödeme onaylandıktan sonra Kainta İnşaat Uzmanı Alıcı'yı arayarak keşif randevusunu birlikte belirler.</li>
    <li>Alıcı, randevudan en az {{ $ic['reschedule_hours'] }} saat önce bildirmek kaydıyla randevuyu ücretsiz olarak yeniden planlayabilir.</li>
    <li>Alıcı veya yetkilendirdiği kişi randevuda adreste bulunmazsa yeni bir randevu belirlenir.</li>
    <li>Hizmet, her durumda sipariş tarihinden itibaren yasal üst sınır olan 30 gün içinde ifa edilir; ifa edilemezse ödenen bedelin tamamı 14 gün içinde iade edilir.</li>
</ul>

<h2>6. Cayma Hakkı</h2>
<p>
    Alıcı, sözleşmenin kurulduğu günden itibaren 14 (on dört) gün içinde herhangi bir gerekçe göstermeksizin ve cezai şart
    ödemeksizin sözleşmeden cayabilir. Cayma bildirimi bu süre içinde yazılı olarak veya
    <a href="mailto:{{ $ic['cancel_email'] }}">{{ $ic['cancel_email'] }}</a> adresine e-posta ile yapılır; Ek-1'deki
    Cayma Formu kullanılabilir. Hizmet Sağlayıcı, bildirimin kendisine ulaşmasından itibaren 14 gün içinde ödemenin tamamını,
    ödemenin yapıldığı araçla ve masrafsız olarak iade eder.
</p>
<div class="lg-warn">
    <strong>Cayma hakkının kullanılamayacağı hal:</strong> Mesafeli Sözleşmeler Yönetmeliği'nin 15. maddesi uyarınca, cayma süresi
    dolmadan Alıcı'nın onayıyla ifasına başlanan hizmetlerde cayma hakkı kullanılamaz. Alıcı, sipariş sırasında keşfin 14 günlük
    süre dolmadan yapılmasını açıkça talep ederse, <strong>keşif yerinde yapıldıktan sonra</strong> cayma hakkı sona erer.
    Keşif yapılmadan önce cayma hakkı aynen devam eder. Bu onay verilmemişse keşif, 14 günlük süre dolduktan sonra yapılır.
</div>

<h2>7. Şikâyet ve Uyuşmazlık Çözümü</h2>
<p>
    Alıcı talep ve şikâyetlerini <a href="mailto:{{ $co['email'] ?? 'info@worksitem.com' }}">{{ $co['email'] ?? 'info@worksitem.com' }}</a>
    veya {{ $co['phone'] ?? '' }} üzerinden iletebilir; şikâyetler en geç 5 iş günü içinde yanıtlanır. Uyuşmazlıklarda, Ticaret
    Bakanlığınca her yıl ilan edilen parasal sınırlar dahilinde Alıcı'nın veya Hizmet Sağlayıcı'nın yerleşim yerindeki Tüketici
    Hakem Heyetleri, bu sınırları aşan durumlarda Tüketici Mahkemeleri yetkilidir. Başvuru e-Devlet üzerinden TÜBİS ile de yapılabilir.
</p>

<h2>8. Diğer Hususlar</h2>
<ul>
    <li>Kişisel veriler <a href="/legal/kvkk-aydinlatma">KVKK Aydınlatma Metni</a>'ne uygun olarak işlenir.</li>
    <li>Sözleşme metni ve elektronik onay kayıtları Hizmet Sağlayıcı tarafından yasal saklama süreleri boyunca saklanır; Alıcı sipariş sonrasında PDF kopyasını indirebilir veya e-posta ile talep edebilir.</li>
    <li>Alıcı'nın tacir veya tüzel kişi olarak ticari ya da mesleki amaçla sipariş vermesi hâlinde tüketici mevzuatına özgü hükümler (cayma hakkı dahil) uygulanmaz.</li>
    <li>Sözleşme dili Türkçedir.</li>
</ul>

<h2>9. Alıcı Beyanı</h2>
<p>
    Alıcı; hizmetin temel nitelikleri, vergiler dahil toplam fiyatı, ödeme ve ifa koşulları, cayma hakkı ve kullanılamayacağı
    hal, şikâyet ve itiraz yolları konusunda bu form ile elektronik ortamda bilgilendirildiğini, formu okuyup anladığını ve teyit
    ettiğini kabul eder.
</p>
<table class="lg-kv">
    <tr><td>Sipariş (talep) no</td><td>{!! $f($o['order_no'], 'Sipariş sırasında oluşur') !!}</td></tr>
    <tr><td>Tarih</td><td>{!! $f($o['date'], 'Sipariş tarihi') !!}</td></tr>
</table>
