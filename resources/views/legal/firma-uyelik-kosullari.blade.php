<x-legal.document
    title="Firma Üyelik ve Aracılık Hizmet Sözleşmesi"
    description="Worksitem firma üyeliği, teklif davetleri, eşleşme bedeli, fatura ve sorumluluk koşulları."
    path="/legal/firma-uyelik-kosullari"
    type="Sözleşme"
    version="2026-10-v2 (taslak · avukat onayı bekliyor)"
    effective="15.10.2026"
    robots="noindex, follow"
>
@if (\App\Support\Pilot::enabled())
<p style="padding:12px 14px;border:1px solid #E7D7A6;background:#FFF8E1;border-radius:10px"><strong>Pilot dönem notu:</strong> Bu metin, firma üyeliği ve kayıtlı firmalardan teklif alma hizmetleri için hazırlanmış taslaktır. Pilot dönemde bu hizmetler kapalıdır ve bu metin kimseyle akdedilmez. Hizmetler açıldığında güncel ve onaylı metin yayımlanır.</p>
@endif
<p>Bu sözleşme, worksitem.com platformunu işleten ve unvan, adres ve iletişim bilgileri bu sayfanın sonundaki künyede yer alan hizmet sağlayıcı ("<strong>Worksitem</strong>") ile firma üyeliği için başvuran gerçek veya tüzel kişi ("<strong>Firma</strong>") arasında, Firma'nın başvuru formunu elektronik ortamda onaylamasıyla kurulur. Bu sözleşmede hak ve yükümlülük sahibi Worksitem'dir; platformda hizmet veren diğer firmalar (Worksitem'le aynı gruptan olanlar dahil) bu sözleşmenin tarafı değildir.</p>

<h2>1. Worksitem'in rolü</h2>
<p>Worksitem, 6563 sayılı Elektronik Ticaretin Düzenlenmesi Hakkında Kanun kapsamında <strong>aracı hizmet sağlayıcıdır</strong>. Hesaplama araçlarını kullanan ve teklif isteyen kullanıcılar ("<strong>Kullanıcı</strong>") ile Firma'yı buluşturur. Worksitem; Kullanıcı ile Firma arasında kurulacak eser, hizmet, satış veya başka herhangi bir sözleşmenin tarafı, temsilcisi, yüklenicisi, kefili veya garantörü değildir; işi yapmaz, denetlemez, işin bedelini tahsil etmez.</p>

<h2>2. Üyelik, doğrulama ve doğru bilgi</h2>
<p>2.1. Firma; unvan, vergi kimlik / TC kimlik numarası, vergi dairesi, fatura adresi, e-Fatura mükellefiyeti ve vergi levhası dahil verdiği tüm bilgilerin doğru, güncel ve kendisine ait olduğunu; başvuruyu yapmaya yetkili olduğunu beyan ve taahhüt eder. Bilgiler değiştiğinde 7 gün içinde bildirir.</p>
<p>2.2. Worksitem bilgileri vergi levhası, kontrol hanesi ve Gelir İdaresi Başkanlığı'nın e-Fatura kayıtlı kullanıcılar listesi üzerinden kontrol eder; gerekçe göstermeksizin başvuruyu reddedebilir, üyeliği askıya alabilir veya sona erdirebilir. Kontrol, Firma'nın yeterliliğine, ehliyetine veya işin kalitesine ilişkin bir taahhüt değildir.</p>
<p>2.3. Telefon numarası SMS koduyla doğrulanır. Firma paneline giriş bu numaraya gönderilen kodla yapılır; numaranın ve kodların güvenliğinden Firma sorumludur.</p>

<h2>3. Teklif davetleri ve teklif verme</h2>
<p>3.1. Kullanıcı teklif istediğinde talep; iş kolu ve hizmet bölgesi (il/ilçe) uyan, iş almaya açık en fazla beş Firma'ya SMS ve e-posta ile iletilir. Davet sırası öne çıkan üyelik ve en uzun süre davet almamış olma esasına göre belirlenir; Worksitem davet sayısı veya iş miktarı garanti etmez.</p>
<p>3.2. Teklif vermek ücretsizdir. Teklif, KDV dahil toplam tutar olarak verilir ve yerinde keşifle netleşecek ön teklif niteliğindedir. Firma, teklif notlarında telefon, e-posta, internet adresi veya başka iletişim bilgisi paylaşmamayı kabul eder; bu bilgiler otomatik gizlenir.</p>
<p>3.3. Eşleşme gerçekleşmeden Kullanıcı'nın adı ve telefonu Firma ile paylaşılmaz. Firma, Worksitem üzerinden öğrendiği bir talep için Kullanıcı'ya eşleşme dışında ulaşmaya veya eşleşme bedelini dolanmaya yönelik hiçbir girişimde bulunmaz; aksi hâlde üyelik derhal sona erdirilir ve doğan zarar talep edilir.</p>
<p>3.4. Firma'nın isteğe bağlı olarak verdiği hizmet bilgileri (en küçük iş tutarı, ekip, birim fiyat beyanları) Kullanıcı'lara ve diğer firmalara gösterilmez; yalnızca uygun işlerin seçilmesi ve hesaplama araçlarındaki bölgesel fiyatların doğrulanması için toplu ve anonim olarak kullanılır.</p>

<h2>4. Eşleşme bedeli</h2>
<p>4.0. <strong>Kurucu firma programı:</strong> {{ \App\Services\Market\Marketplace::freeUntil()?->format('d.m.Y') ?? '—' }} tarihine kadar (bu tarih dahil) kabul edilen tekliflerde eşleşme bedeli alınmaz. Bu dönemde Firma, kabulden sonra 48 saat içinde firma panelinden eşleşmeyi onaylar; onayla iletişim bilgileri karşılıklı açılır. Bu tarihten sonra kabul edilen teklifler için aşağıdaki bedel uygulanır; Worksitem bu tarihi Firma lehine uzatabilir, bedeli artırmadan önce en az 30 gün önceden bildirir.</p>
<p>4.1. Kullanıcı Firma'nın teklifini kabul ettiğinde Firma, eşleşme bedelini 48 saat içinde iyzico güvenli ödeme sayfası üzerinden kartla öder. Ödeme doğrulandığında Kullanıcı'nın adı, telefonu ve ilçesi Firma'ya; Firma'nın adı ve telefonu Kullanıcı'ya iletilir. Süre içinde ödenmezse kabul düşer ve Kullanıcı başka teklif seçebilir.</p>
<p>4.2. Eşleşme bedeli kabul edilen teklif tutarının <strong>%{{ rtrim(rtrim(number_format(config('worksitem_market.fee_rate') * 100, 1, ',', ''), '0'), ',') }}</strong>'idir; en az <strong>{{ number_format(config('worksitem_market.fee_min'), 0, ',', '.') }} TL</strong>, en çok <strong>{{ number_format(config('worksitem_market.fee_max'), 0, ',', '.') }} TL</strong> olup bu tutarlara KDV eklenir. Bedel, tanıtım ve eşleştirme hizmetinin karşılığıdır; işin sonradan yapılıp yapılmamasına veya teklif tutarının değişmesine bağlı değildir.</p>
<p>4.3. <strong>İade:</strong> Firma'nın 72 saat içinde Kullanıcı'ya hiçbir şekilde ulaşamadığı (telefonun yanlış/kapalı olduğu) veya Kullanıcı'nın talebi gerçek dışı olduğu Worksitem tarafından doğrulanırsa eşleşme bedeli iade edilir. Bunun dışında iade yapılmaz.</p>
<p>4.4. <strong>Fatura:</strong> Eşleşme bedeli ve öne çıkan üyelik için Worksitem, Firma'nın bildirdiği unvan ve vergi bilgileriyle, e-Fatura mükellefine e-Fatura, diğerlerine e-Arşiv fatura düzenler. Yanlış bilgi nedeniyle doğan vergi ve ceza yükü Firma'ya aittir.</p>

<h2>5. Firma'nın yükümlülükleri ve sorumluluğu</h2>
<p>5.1. Kullanıcı ile yapılacak işin keşfi, fiyatı, sözleşmesi, malzemesi, uygulaması, süresi, garantisi, iş sağlığı ve güvenliği, sigorta, ruhsat ve izinler, vergi ve SGK yükümlülükleri, atık ve komşuluk ilişkileri dahil tüm sonuçlarından <strong>yalnızca Firma sorumludur</strong>. Firma, Kullanıcı'ya kendi adına fatura düzenler; işe başlamadan önce yazılı sözleşme yapar; tüketici mevzuatına (6502 sayılı Kanun) uyar.</p>
<p>5.2. Firma, kendi fiil, ihmal, ayıplı ifa veya mevzuata aykırılığı nedeniyle Kullanıcı veya üçüncü kişilerce Worksitem'e yöneltilen her türlü talep, dava, idari para cezası, tazminat, avukatlık ücreti ve masrafı ilk talepte <strong>Worksitem'e ödemeyi ve Worksitem'i bu taleplerden ari tutmayı</strong> kabul eder.</p>
<p>5.3. Worksitem; Kullanıcı ile Firma arasındaki uyuşmazlıklardan, işin hiç veya ayıplı yapılmasından, ödemelerden ve bunlardan doğan doğrudan veya dolaylı zararlardan, kanunen sınırlandırılamayan hâller (kast, ağır ihmal ve tüketiciye karşı emredici sorumluluk hâlleri) dışında sorumlu değildir.</p>

<h2>6. Şikâyet, askıya alma ve fesih</h2>
<p>Kullanıcı şikâyetleri Firma'ya iletilir; Firma 3 iş günü içinde yanıt verir. Tekrarlanan veya ağır şikâyet, sahte bilgi, iletişim bilgisi paylaşma girişimi veya ödeme yapmama hâlinde Worksitem üyeliği askıya alabilir veya sona erdirebilir. Firma üyeliğini her zaman sonlandırabilir; ödenmiş bedeller bu nedenle iade edilmez.</p>

<h2>7. Firma rehberi ve öne çıkan üyelik</h2>
<p>Onaylanan Firma'nın adı, türü, iş kolları, hizmet bölgesi, kuruluş yılı ve tanıtım yazısı firma rehberinde yayınlanabilir; vergi numarası, vergi levhası, fatura adresi ve e-posta yayınlanmaz. Öne çıkan üyelik bir yıl sürer ve peşin ödenir; hesaplama sonuçları ve fiyatlar üyelikten etkilenmez.</p>

<h2>8. Kişisel veriler</h2>
<p>Yetkili kişinin verileri <a href="/legal/kvkk-aydinlatma">KVKK Aydınlatma Metni</a> kapsamında işlenir. Firma, eşleşme ile kendisine iletilen Kullanıcı verilerini yalnız o iş için kullanır, üçüncü kişilerle paylaşmaz, işin bitiminden sonra gereksiz hâle gelen verileri siler ve 6698 sayılı Kanun'a göre ayrı bir veri sorumlusu olarak sorumludur.</p>

<h2>9. Diğer hükümler</h2>
<p>Worksitem, bu sözleşmeden doğan hak ve yükümlülüklerini platformu devralan veya işletecek olan şirkete devredebilir; devir Firma'ya bildirilir. Worksitem bu sözleşmeyi değiştirebilir; değişiklikler bu sayfada yayımlanır ve kayıtlı e-posta/SMS ile bildirilir, Firma'nın kullanmaya devam etmesi kabul sayılır. Worksitem'in kayıtları (sistem kayıtları, SMS ve ödeme kayıtları) HMK m.193 uyarınca delil sözleşmesi niteliğindedir. Uyuşmazlıklarda Worksitem'in künyede yazılı merkezinin bulunduğu yer mahkemeleri ve icra daireleri yetkilidir.</p>
<p style="margin-top:16px;font-size:13px;color:#6b7280">Bu metin taslaktır ve avukat incelemesindedir. Not: Anayasa Mahkemesi'nin 12.02.2026 tarihli, E.2024/187, K.2026/42 sayılı kararıyla aracı hizmet sağlayıcıların tüketici sözleşmelerindeki sorumsuzluğuna ilişkin hükümler iptal edilmiştir (RG 02.06.2026; yürürlük yayımdan 9 ay sonra).</p>
</x-legal.document>
