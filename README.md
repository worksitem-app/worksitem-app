# Worksitem

[![Tests](https://github.com/worksitem-app/worksitem-app/actions/workflows/tests.yml/badge.svg)](https://github.com/worksitem-app/worksitem-app/actions/workflows/tests.yml)

> Tadilat ve inşaat maliyetini güncel fiyatlarla anında hesaplayan, hesaplayanı doğru firmayla buluşturan Türkiye odaklı ConTech platformu.

Canlı: https://worksitem.com · Uygulama: https://worksitem.com/uygulama · Güncel durum: v69 (15.10.2026)

---

## Kimler için, ne yapıyor?

| Kitle | Worksitem'de ne bulur |
|---|---|
| **Ev sahipleri, proje sahipleri** | Banyo, mutfak, boya (iç ve dış cephe, iskele dahil), seramik (zemin ve duvar ayrı ürün), parke, çatı, inşaat maliyeti gibi ücretsiz, üyeliksiz hesaplayıcılar; kalem kalem döküm, tarihli fiyat, PDF Pro rapor; 2D/3D banyo, WC ve mutfak planlayıcıları; uzmandan keşif ve (avukat onayından sonra) kayıtlı firmalardan teklif |
| **Usta, firma, tedarikçi** | Ücretsiz firma kaydı (vergi numarası kontrol haneli doğrulama, vergi levhası), iş koluna ve bölgeye göre iş davetleri, SMS ile şifresiz firma paneli, uygulama bildirimi. Kurucu firma programı: 31.03.2027'ye kadar eşleşme bedeli yok |
| **Markalar, üreticiler** | Hesap sonucunun altında, ilgili kalemin yanında etiketli sponsor kartı; sponsor firma başvurusu; aylık gösterim/tıklama raporu |
| **Mühendis, müteahhit** | Statik ön kontrol, metraj, birim fiyat analizi, poz arama, ihale ve belge araçları |

Hesap sonuçları sponsorluktan etkilenmez; sponsorlu içerik her zaman etiketlidir. Telefon numarası kullanıcının izni olmadan paylaşılmaz.

---

## Ürün bileşenleri

| Bileşen | Açıklama | Ana kod |
|---|---|---|
| **Araçlar (77)** | 74 yayında, 2 yapay zekâ aracı (`AI_SPEND_ENABLED` ile açılır), 1 eski adres yönlendirmesi. Yayın durumu veritabanında (admin › Araç Yayın Durumu, `php artisan tools:publish <ön-ayar>`) | `config/worksitem_tools.php`, `config/worksitem_golive.php` |
| **Hesap motoru (31 araç)** | Adım şeması + sunucuda hesap; aynı hesap canlı sonuçta ve PDF Pro raporda. Koşullu adımlar (`show` / `hide`): ör. boya dış cephe modu, seramik zemin + duvar ayrı ürün | `app/Estimators/KitEstimator.php`, `app/Estimators/*Estimator.php`, `resources/views/tools/kit.blade.php` |
| **Fiyatlar** | ÇŞB/YFK resmî poz fiyatları, piyasa araştırması, Kainta saha referansları; her sabah 06:30 fiyat robotu (Gemini + Google arama) önerir, yönetici onaylar. Her raporda fiyat tarihi | `app/Services/Pricing/PriceRobot.php`, `app/Services/ToolPriceService.php`, `config/worksitem_estimators.php` |
| **Teklif pazaryeri** | Talep → iş kolu/il/ilçe/en küçük iş tutarına göre en çok 5 firmaya davet → teklif → müşteri seçer → eşleşme (ücretsiz dönemde onay, sonra %5, en az 250, en çok 7.500 TL + KDV) → iletişim açılır. Kullanıcı tarafı `MARKET_ENABLED` ile açılır | `app/Services/Market/Marketplace.php`, `app/Http/Controllers/Market/*` |
| **Firmalar** | Kayıt, hizmet bilgileri ve saha fiyat soruları (fiyatlar müşteriye gösterilmez, admin › Saha fiyat beyanları), firma rehberi (`FIRMS_DIRECTORY_ENABLED`) | `app/Http/Controllers/FirmController.php`, `app/Services/Market/FirmQuestions.php` |
| **Sponsorlar** | Başvuru formu, admin onayı, taslak (pasif) kart; imzalı sözleşme olmadan yayına alınmaz | `app/Http/Controllers/SponsorController.php`, `config/worksitem_sponsors.php` |
| **Ödeme** | iyzico ile Pro rapor ve yapay zekâ kredisi; faturayı mali müşavir keser (admin › Kesilecek faturalar, CSV) | `app/Services/Payments/OnlinePayment.php` |
| **Yapay zekâ** | Banyo/oda görselleştirme, seramik görselleştirici, otomatik keşif; telefonla giriş + günde 1 ücretsiz + kredi; günlük toplam tavan `AI_GLOBAL_DAILY_LIMIT` | `app/Services/AI/*`, `app/Http/Middleware/EnsureAiQuota.php` |
| **Uygulama** | PWA (servis çalışanı, çevrimdışı sayfa, ana ekrana ekle bandı, `/uygulama`), firma paneline web bildirimi (VAPID, harici paket yok), Google Play TWA (`/.well-known/assetlinks.json`) | `public/sw.js`, `app/Services/Push/WebPush.php`, `app/Http/Controllers/AppController.php` |
| **Büyüme** | Her araçta "Nasıl çalışır?" ve "Neden Worksitem?", fiyatlı arama açıklamaları, A/B testleri (admin › A/B testleri), site haritası, `llms.txt`, BreadcrumbList/FAQ işaretlemesi | `app/Http/Middleware/ToolPageEnhancer.php`, `app/Support/Ab.php`, `app/Support/ToolSeo.php` |
| **Yasal** | Kullanım koşulları, KVKK, çerez bandı, firma üyelik ve teklif alma koşulları (taraf: Worksitem; künye `.env` `COMPANY_*`) | `resources/views/legal/*` |

---

## Teknoloji

| Katman | Teknoloji |
|---|---|
| Uygulama | Laravel 13, PHP 8.3 |
| Yönetim paneli | Filament 3 |
| Arayüz | Blade, Livewire, Tailwind 4, Vite 8; çoğu araç bağımsız Blade sayfası (ortak katman `BrandShell` middleware'i) |
| Veritabanı | MariaDB (Hostinger), testlerde SQLite |
| PDF | barryvdh/laravel-dompdf |
| Dış servisler | Google Gemini, Replicate, iyzico, Netgsm (SMS), TCMB döviz kuru, GA4 |

Ölçüler (15.10.2026): 361 route, 40 controller, 33 model, 65 migration, 25 artisan komutu, 460 test (3.700+ doğrulama), yaklaşık 49.000 satır PHP, 210 Blade şablonu.

---

## Yerel kurulum

```bash
git clone https://github.com/worksitem-app/worksitem-app.git && cd worksitem-app
composer install && npm install
cp .env.example .env && php artisan key:generate
php artisan migrate && php artisan db:seed
npm run build
php artisan serve        # http://localhost:8000
php artisan test         # 460 test, SQLite bellek içi
```

Canlı ortam anahtarlarının tam listesi ve açıklamaları: `.env.production.example`. Gizli anahtarlar yalnız sunucudaki `.env` dosyasında durur; depoya ve sohbete yazılmaz.

### Özellik anahtarları

| Anahtar | Varsayılan | Ne açar |
|---|---|---|
| `AI_SPEND_ENABLED` | false | Dış yapay zekâ çağrıları (kapalıyken 503) ve kredi satışı. Önce sağlayıcıda bütçe limiti, sonra `php artisan ai:check` |
| `ONLINE_PAYMENTS_ENABLED` | false | iyzico ile kartla ödeme |
| `MARKET_ENABLED` | false | Kullanıcının firmalardan teklif istemesi (`/teklif-al`) — avukat onayından sonra |
| `FIRMS_DIRECTORY_ENABLED` | false | Herkese açık firma rehberi — avukat onayından sonra |
| `PRICE_ROBOT_ENABLED` | true | Her sabah fiyat araştırması (onaysız fiyat yayına girmez) |
| `PUSH_ENABLED` | true | Firma paneli web bildirimleri (VAPID anahtarları gerekir) |

---

## Sunucu ve yayın

| Ortam | Klasör | Yayın klasörü |
|---|---|---|
| Staging | `~/apps/worksitem-staging` | `~/domains/worksitem.com/public_html/app` (kısayollar) |
| Canlı | `~/apps/worksitem` | `~/domains/worksitem.com/public_html` (kısayollar) |

Yayın klasörü uygulamanın `public/` klasörüne tek tek kısayollarla bağlıdır; `robots.txt` ve `.htaccess` her ortamın kendi dosyasıdır. Yeni dosyaların kısayolu canlıya alma sırasında otomatik kurulur.

Akış: değişiklik staging'e uygulanır ve GitHub'a gönderilir → `bash scripts/golive-from-staging.sh deploy` (CANLI onayı, otomatik yedek, migration, önbellek, kontroller) → `bash scripts/launch-check.sh`. Ayrıntı: [DEPLOY.md](DEPLOY.md).

| Komut | Ne yapar |
|---|---|
| `bash scripts/launch-check.sh` | Canlı "motor" kontrolü (yalnız okur): sayfalar, SEO, uygulama, yayın klasörü, preflight, cron, günün sayıları |
| `bash scripts/golive-from-staging.sh check \| deploy \| rollback` | Staging → canlı |
| `bash scripts/link-public.sh DOCROOT UYGULAMA --apply` | Yayın klasörü kısayolları (ana sayfa değişirse kendini geri alır) |
| `php artisan app:preflight` | Canlı yapılandırma denetimi |
| `php artisan ai:check` | Yapay zekâ hazırlığı ve modellerin sağlayıcıda varlığı (ücretsiz) |
| `php artisan push:keys --write` | VAPID anahtarları (yalnız yoksa üretir) |
| `php artisan tools:publish <ön-ayar>` | Araç yayın ön ayarı (`ilk-dalga`, `ikinci-dalga`, `yeni-araclar`, `yapay-zeka`) |

### Zamanlanmış görevler (cron: her dakika `schedule:run`)

| Saat | Görev |
|---|---|
| her dakika | Zamanlayıcı kalp atışı |
| her saat | `market:tick` (teklif süreleri, eşleşme bedeli süreleri) |
| 02:00 | Poz sayfalarına SEO içeriği (yapay zekâ açıkken) |
| 03:30 / 03:45 | Yapay zekâ girdilerini temizleme / veritabanı yedeği (14 gün) |
| 06:30 | Fiyat robotu |
| 08:15 / 08:30 | Günlük yönetici raporu / malzeme fiyat raporu |
| 18:00 | Keşif randevusu SMS hatırlatması |
| Pazartesi 07:00 | Haftalık piyasa karşılaştırması |
| 10:00 ve 16:00 | TCMB döviz kuru |

---

## Klasör haritası

```
app/Estimators/          hesap motoru ve araç hesapları
app/Services/Market/     teklif pazaryeri, firma soruları
app/Services/Pricing/    fiyat robotu
app/Services/Push/       web bildirimi (VAPID)
app/Http/Middleware/     BrandShell (marka + PWA katmanı), ToolPageEnhancer, AI kilidi ve kotası
app/Filament/            yönetim paneli
config/worksitem_*.php   ürün ayarları (araçlar, fiyatlar, pazaryeri, sponsorlar, uygulama)
resources/views/tools/   araç sayfaları (kit.blade.php: hesap motoru arayüzü)
resources/llms/base.txt  yapay zekâ asistanları için site özeti (/llms.txt)
scripts/                 sunucu betikleri
docs/                    sunucu taşıma planı, sunum taslağı
```

---

## Lisans

Ticari mülkiyet. Tüm hakları saklıdır. © 2026 Worksitem

İletişim: info@worksitem.com · https://worksitem.com
