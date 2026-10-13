# Worksitem — Canlıya Çıkış Runbook'u

## 0. Bu sürüme özel notlar (Ekim 2026 güvenlik sürümü)

| Değişiklik | Etkisi | Yapılacak |
|---|---|---|
| Proje / AI tasarım linkleri sıralı ID → ULID token (IDOR kapatıldı) | Eski `/maliyet-hesapla/proje/123` linkleri **404** döner | Kabul edilen bedel. Müşteriye link gönderildiyse yenisini iletin |
| Yeni migration: `projects.public_token`, `ai_generations.public_id` mevcut kayıtlar için doldurulur | Büyük tabloda birkaç saniye sürebilir | **Önce prod DB kopyasında deneyin** (aşağıda) |
| `SESSION_ENCRYPT=true` | Açık oturumlar (admin) bir kez düşer | Tekrar giriş yapın |
| Çerez onay bandı | GA4 ve Google Ads artık yalnızca onayla yüklenir. GA4'te ziyaretçi sayısı **düşmüş görünecek** | Beklenen davranış; raporlara not düşün |
| Zamanlayıcı birleştirildi | Eski `daily-market-mail.sh` cron'u devre dışı | Cron'u aşağıdaki tek satırla değiştirin |

## 1. İlk kurulum (bir kez)

1. **Yedek:** hPanel → Veritabanları → yedek alın ve indirin.
2. **Migration provası:** Prod DB'nin kopyasını staging'e yükleyip `php artisan migrate --force` çalıştırın, hata vermediğini görün.
3. **.env:** `.env.production.example` dosyasını `.env` olarak kopyalayıp doldurun (mevcut `.env`'deki şifreleri taşıyın).
   - Cloudflare kullanılıyorsa `TRUSTED_PROXIES=*`
4. **Cron** (hPanel → Gelişmiş → Cron Jobs). Eski satırları silin, yalnızca bunu bırakın:
   ```
   * * * * * cd /home/KULLANICI/worksitem && php artisan schedule:run >> /dev/null 2>&1
   ```
5. **Admin kullanıcısı:**
   ```
   php artisan app:make-admin siz@worksitem.com --name="Ad Soyad"
   ```
6. **Sağlayıcı tarafı harcama limitleri:** Replicate ve Google AI Studio'da aylık bütçe uyarısı ve limit tanımlayın. Uygulamadaki `AI_GLOBAL_DAILY_LIMIT` ikinci bir emniyet katmanıdır.
7. **İzleme:** UptimeRobot (ücretsiz) ile `https://worksitem.com/up` adresini 5 dakikada bir kontrol ettirin, e-posta/SMS uyarısı açın.
8. **Otomatik yedek:** hPanel'de günlük otomatik yedeği açın. `scripts/deploy.sh` her deploy'da ayrıca `~/worksitem-backups` altına yedek alır (son 14 yedek tutulur).

## 2. Her deploy

```bash
ssh ...
cd ~/worksitem
bash scripts/deploy.sh          # git ile
bash scripts/deploy.sh --no-pull  # dosyalar FTP ile yüklendiyse
```

Betik sırasıyla şunları yapar: bakım modu, DB yedeği, `git pull`, `composer install --no-dev`, `migrate --force`, `storage:link`, cache'ler, siteyi açma, `app:preflight`.

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
| `ilk-dalga` | Banyo, mutfak, WC, seramik, boya, parke, zemin, mantolama, PVC, su tesisatı, beton, duvar rengi (12) |
| `ikinci-dalga` | Kaba inşaat ve teknik maliyet araçları (21) |
| `yapay-zeka` | Oda planlayıcı, otomatik keşif. Yalnızca `AI_SPEND_ENABLED=true` ve sağlayıcı bütçe limitlerinden sonra |

Ön ayar listede olmayan araçlara dokunmaz. Sonradan tek tek açıp kapatmak için: admin › Sistem Yönetimi › Araç Yayın Durumu, ya da `php artisan tools:publish ikinci-dalga`. Deneme için `--dry-run`, listede olmayanları kapatmak için `--only`.

## 3. Geri dönüş

```bash
php artisan down
git checkout <önceki-commit>
composer install --no-dev --optimize-autoloader
gunzip < ~/worksitem-backups/worksitem-YYYYMMDD-HHMMSS.sql.gz | mysql -u USER -p DB_ADI
php artisan optimize:clear && php artisan optimize
php artisan up
```

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

- **Hukuk:** KVKK metinleri ve VERBİS kaydı bir hukukçuya gözden geçirtilmeli. Çerez politikası, bant ile artık uyumlu.
- **Lead e-postası:** Senkron gönderiliyor. SMTP hatasında lead DB'ye kaydedilir ama e-posta gitmez (log'a düşer). Admin bildirimi panelde ayrıca var. Kalıcı çözüm: kuyruk worker'ı ya da `lead_outbox` tablosuyla yeniden deneme.
- **Hata takibi:** Sentry/Flare entegrasyonu (şimdilik `storage/logs/laravel-*.log`).
- **Admin 2FA:** Filament paneline iki adımlı doğrulama.
- **MySQL'de CI:** Testler SQLite'ta koşuyor; GitHub Actions'a MySQL servisi eklenmeli.
