# Worksitem — Canlıya Çıkış Runbook'u

Güncel: 15.10.2026 (v69). Sunucu: Hostinger paylaşımlı hosting. Kod: GitHub (özel depo).

## 0. Günlük akış (özet)

1. Değişiklik **staging**'e uygulanır (`~/apps/worksitem-staging`), testleri geçer, GitHub'a gönderilir.
2. Canlıya alma: `bash ~/apps/worksitem-staging/scripts/golive-from-staging.sh deploy` → **CANLI** yazılır.
   Betik: yedek (`~/backups/golive-TARIH`) → yeni `.env` anahtarlarını onayla ekleme → bakım modu → kod eşitleme →
   `composer install --no-dev` → yayın klasörü kısayolları → migration (önce SQL önizlemesi) → önbellekler → site açılır → preflight + duman testi.
3. Kontrol: `bash ~/apps/worksitem/scripts/launch-check.sh` (yalnız okur; ✘ satırı olmamalı).
4. Sorun olursa: `bash ~/apps/worksitem-staging/scripts/golive-from-staging.sh rollback TARIH`.

**Yayın klasörü:** `~/domains/worksitem.com/public_html` uygulamanın `public/` klasörüne tek tek kısayollarla bağlıdır.
Yeni dosyaların kısayolu 6/10 adımında otomatik kurulur; elle kopyalanmış bir dosya varsa uyarılır.
`robots.txt` ve `.htaccess` yayın klasörünün kendi dosyalarıdır (uygulamadakiyle eşitlenmez); değişiklik gerekiyorsa yedek alınıp elle eklenir.
`llms.txt` ve `sitemap.xml` uygulama tarafından üretilir; aynı adla statik dosya bırakılmamalıdır.

**Gizli bilgiler:** `.env` değerleri ekrana yazdırılmaz; yeni anahtar `read -s` ile girilir.

## 0b. Sürüm notları

| Sürüm | Not |
|---|---|
| v66–v68 | PWA: `public/sw.js`, `/cevrimdisi`, `/uygulama`, yükleme bandı. Yayın klasörü `.htaccess`'ine `sw.js` için `Cache-Control: no-cache` eklendi (WORKSITEM-PWA bloğu). VAPID anahtarı: `php artisan push:keys --write` |
| v69 | Boya dış cephe/iskele modu, seramik zemin + duvar ayrı ürün, `php artisan ai:check`, yapay zekâ araçlarının açılması (`AI_SPEND_ENABLED=true` + `tools:publish yapay-zeka`). Dış cephe fiyatları Fiyat Merkezi › pricing_settings'ten güncellenebilir: `paint_facade_15l`, `paint_facade_primer_15l`, `paint_facade_labor_m2` |

## 0c. Google Play (TWA)

PWABuilder ile paket (`com.worksitem.app`) → Play Console kapalı test → Uygulama imzalama SHA-256 →
canlı `.env` `TWA_SHA256` (virgülle birden çok) → `php artisan config:cache` → `/.well-known/assetlinks.json` parmak izini göstermeli.
Yayından sonra `TWA_PLAY_URL` yazılır; sitedeki yükleme bandı Android'de Play'e yönlenir.

## 1. İlk kurulum (bir kez)

1. **Yedek:** hPanel → Veritabanları → yedek alın ve indirin.
2. **Migration provası:** Prod DB'nin kopyasını staging'e yükleyip `php artisan migrate --force` çalıştırın, hata vermediğini görün.
3. **.env:** `.env.production.example` dosyasını `.env` olarak kopyalayıp doldurun (mevcut `.env`'deki şifreleri taşıyın).
   - Cloudflare kullanılıyorsa `TRUSTED_PROXIES=*`
4. **Cron** (hPanel → Gelişmiş → Cron Jobs). Eski satırları silin, yalnızca bunu bırakın:
   ```
   */5 * * * * cd /home/KULLANICI/apps/worksitem && nice -n 19 php artisan schedule:run >> /dev/null 2>&1
   ```
5. **Admin kullanıcısı:**
   ```
   php artisan app:make-admin siz@worksitem.com --name="Ad Soyad"
   ```
6. **Sağlayıcı tarafı harcama limitleri:** Replicate ve Google AI Studio'da aylık bütçe uyarısı ve limit tanımlayın. Uygulamadaki `AI_GLOBAL_DAILY_LIMIT` ikinci bir emniyet katmanıdır.
7. **İzleme:** UptimeRobot (ücretsiz) ile `https://worksitem.com/up` adresini 5 dakikada bir kontrol ettirin, e-posta/SMS uyarısı açın.
8. **Otomatik yedek:** hPanel'de günlük otomatik yedeği açın. `scripts/deploy.sh` her deploy'da ayrıca `~/worksitem-backups` altına yedek alır (son 14 yedek tutulur).
9. **Tam yedek (v78, spatie/laravel-backup):** her gece 03:00 (İstanbul) veritabanı + dosyalar (.env, storage/app; vendor/.git hariç) tek zip olarak `storage/app/spatie-backups/worksitem-production/` altına yazılır, son 7 gün tutulur, 09:05'te sağlık kontrolü yapılır; sorun olursa `BACKUP_NOTIFY_EMAIL` adresine e-posta gider. Ayrı cron gerekmez: yukarıdaki 5 dakikalık `schedule:run` satırı yeter (v84: her dakika çalıştırmak paylaşımlı sunucuyu yavaşlatıyordu; tüm görev saatleri 5'in katı). Elle: `php artisan backup:run`, liste: `php artisan backup:list`. Geri yükleme: zip'i açın, `db-dumps/*.sql` dosyasını `mysql -u USER -p DB_ADI < dosya.sql` ile yükleyin, `storage/app` ve `.env`'i yerine koyun.

## 2. Eski git akışı (kullanım dışı)

`scripts/deploy.sh` git pull ile tek klasörde çalışan eski akıştır; bugünkü staging → canlı düzeninde kullanılmaz (VPS geçişinde yenilenecek, bkz. `docs/SUNUCU-TASIMA.md`).

**Duman testi (2 dk):** ana sayfa, `/araclar`, bir hesaplayıcı, `/maliyet-hesapla` akışı sonuna kadar (lead e-postası geldi mi?), `/admin` girişi, çerez bandında Reddet ve Kabul et, `/up`.

## 2b. Staging'den canlıya geçiş (Ekim 2026 araç dalgası)

Staging (`~/apps/worksitem-staging`) test edildikten sonra kod oradan canlıya (`~/apps/worksitem`) taşınır. Git veya FTP gerekmez.

```bash
cd ~/apps/worksitem-staging
bash scripts/golive-from-staging.sh check                       # 1) sadece kontrol, hiçbir şeyi değiştirmez
bash scripts/golive-from-staging.sh deploy --preset ilk-dalga   # 2) onaydan sonra: CANLI yazınca başlar
bash scripts/golive-from-staging.sh rollback                    # gerekirse: son yedeğe dön (GERI yazınca)
```

`deploy` sırası: yedek (`~/backups/golive-TARIH/`: kod arşivi, veritabanı dökümü, .env) → staging'de olup canlıda olmayan .env anahtarlarını onayla ekleme (değerler ekrana yazılmaz; APP_, DB_, MAIL_, GA4_, iyzico adresi, AI_SPEND_ENABLED gibi ortama özel anahtarlar asla kopyalanmaz) → bakım modu → kod eşitleme (app, config, routes, resources, database, bootstrap, lang içinde canlıda kalmış eski dosyalar silinir; storage, .env, vendor, public/storage dokunulmaz) → `composer install --no-dev` (composer yoksa vendor staging'den kopyalanır) → migration (önce SQL önizlemesi yedek klasörüne yazılır) → önbellekler → araç ön ayarı → site açılır → `app:preflight`, `app:integrations`, duman testi.

**Araç ön ayarları** (`config/worksitem_golive.php`):

| Ön ayar | İçerik |
|---|---|
| `ilk-dalga` | Tadilat maliyeti ve planlayıcılar, ince ve kaba işler (25) |
| `ikinci-dalga` | Yapay zekâ dışındaki kalan araçlar (48) |
| `yeni-araclar` | Mutfak planlayıcı |
| `yapay-zeka` | Oda planlayıcı, otomatik keşif. Yalnızca `AI_SPEND_ENABLED=true`, sağlayıcı bütçe limiti ve `php artisan ai:check` başarılıysa |

Ön ayar listede olmayan araçlara dokunmaz. Sonradan tek tek açıp kapatmak için: admin › Sistem Yönetimi › Araç Yayın Durumu, ya da `php artisan tools:publish ikinci-dalga`. Deneme için `--dry-run`, listede olmayanları kapatmak için `--only`.

## 3. Geri dönüş

```bash
bash ~/apps/worksitem-staging/scripts/golive-from-staging.sh rollback               # son yedek
bash ~/apps/worksitem-staging/scripts/golive-from-staging.sh rollback 20261015-182716  # belirli yedek
```

Yedek klasörü (`~/backups/golive-TARIH`) kod arşivi, veritabanı dökümü ve `.env` kopyasını içerir. Rollback GERI onayı ister.

## 4. `php artisan app:preflight`

Canlı ortam yapılandırmasını denetler. FAIL varsa çıkış kodu 1 döner.

| Kontrol | Seviye |
|---|---|
| APP_ENV=production, APP_DEBUG=false, APP_KEY, APP_URL https, SESSION_SECURE_COOKIE | FAIL |
| DB bağlantısı, bekleyen migration, en az bir admin, `test@example.com` olmaması | FAIL |
| storage yazılabilir, `public/storage` linki, mail sürücüsü, lead bildirim adresi | FAIL |
| Gemini/Replicate anahtarları, GA4 ID biçimi, LOG_LEVEL | WARN |
| Zamanlayıcı kalp atışı (cron çalışıyor mu), config/route cache | WARN |

## 5. Açık kalan konular (canlı sonrası yol haritası)

- **Avukat:** Teklif Pazaryeri Hukuk İnceleme Notu, firma üyelik ve teklif alma koşulları; ardından `MARKET_ENABLED` ve `FIRMS_DIRECTORY_ENABLED`. ETBİS ve İYS kayıtları.
- **İzleme:** UptimeRobot ile `/up` (5 dk).
- **Google:** Search Console site haritası; Play Console kapalı test (kişisel hesapta 12 test kullanıcısı, 14 gün).

- **Hukuk:** KVKK metinleri ve VERBİS kaydı bir hukukçuya gözden geçirtilmeli. Çerez politikası, bant ile artık uyumlu.
- **Lead e-postası:** Senkron gönderiliyor. SMTP hatasında lead DB'ye kaydedilir ama e-posta gitmez (log'a düşer). Admin bildirimi panelde ayrıca var. Kalıcı çözüm: kuyruk worker'ı ya da `lead_outbox` tablosuyla yeniden deneme.
- **Hata takibi:** Sentry/Flare entegrasyonu (şimdilik `storage/logs/laravel-*.log`).
- **Admin 2FA:** Filament paneline iki adımlı doğrulama.
- **MySQL'de CI:** Testler SQLite'ta koşuyor; GitHub Actions'a MySQL servisi eklenmeli.
