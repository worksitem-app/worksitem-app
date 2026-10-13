# Sunucu toparlama ve VPS'e taşıma planı

**Karar (13.10.2026):** Şimdilik paylaşımlı hostingte kalınır. Aşama 0–2 (envanter, temizlik,
GitHub) hemen yapılır; Aşama 3–7 aşağıdaki tetikleyicilerden biri gerçekleşince başlar.

| Tetikleyici | Ölçü |
| --- | --- |
| Trafik | Ayda 30.000+ ziyaret ya da hPanel'de CPU / giriş süreci (entry process) sınırı uyarısı |
| Yapay zekâ | Görsel üretiminde zaman aşımı; arka plan kuyruğu (Redis) gerekmesi |
| Gelir | İlk ödeme yapan sponsor ya da aylık online kredi satışı başladığında (kesinti maliyeti yükselir) |
| Ekip | İlk teknik çalışan / ortak katıldığında (sunucu bakımını üstlenecek kişi) |
| Yatırım | Yatırımcı incelemesi (due diligence) öncesi |

Canlı veritabanı 29 MB, 13 talep, 714 araç olayı (13.10.2026); mevcut yük paylaşımlı hostingin çok altında.

Paylaşımlı hostingten (Hostinger, `~/apps/*` + `~/domains/*/public_html` bağlantıları)
kendi sunucumuza (VPS) kesintisiz geçiş için adım adım plan. Her aşama bir öncekinin
çıktısıyla başlar; hiçbir adım dosya **silmez**, önce arşive taşır.

| Aşama | Ne yapılır | Araç | Geri dönüş |
| --- | --- | --- | --- |
| 0. Envanter | Sunucudaki her şeyin listesi: uygulamalar, eski kopyalar, artık dosyalar, yedekler, cron, veritabanı boyutları | `scripts/server-audit.sh` (yalnızca okur) | Gerekmez |
| 1. Temizlik | Yapıştırma parçaları, yedek kopyalar, eski loglar arşive taşınır | `scripts/server-tidy.sh` (önce deneme, sonra `--apply`) | `--restore ~/backups/arsiv-TARIH` |
| 2. Tek kaynak | Kod GitHub'da (özel depo); staging ve canlı aynı depodan güncellenir, yapıştırma dosyası dönemi biter | git | Depo geçmişi |
| 3. Taşınabilirlik | Hostinger'a özgü ayarlar ayrılır: `public_html` bağlantıları, cron yolu, e-posta (MX/SPF/DKIM), yüklenen dosyalar | `.env.production.example`, bu belge | — |
| 4. VPS kurulumu | Ubuntu 24.04, nginx, PHP 8.3-FPM, MySQL 8, SSL, güvenlik duvarı, otomatik yedek; staging + canlı iki site | kurulum betiği (sonraki adım) | Sunucu sıfırdan kurulabilir |
| 5. Prova | Veritabanı dökümü + `storage/app` + `.env` VPS'e kopyalanır; geçici alt alan adında tam test | rsync, mysqldump | Eski sunucu dokunulmadan çalışır |
| 6. Geçiş | DNS TTL 24 saat önce 300 sn'ye indirilir; bakım modu → son döküm → DNS A kaydı değişir → kontrol | DNS paneli | DNS eski IP'ye geri alınır |
| 7. Kapanış | Eski hosting 2 hafta salt okunur bekler, sonra kapatılır | — | — |

## Taşınırken dikkat

- **E-posta:** Alan adının e-postası Hostinger'daysa DNS taşınırken MX, SPF, DKIM ve DMARC
  kayıtları aynen kopyalanmalı; aksi halde e-postalar kaybolur.
- **Yüklenen dosyalar:** `storage/app/public` (planlayıcı görselleri, 3D modeller, seramik
  dokuları) kodla taşınmaz; ayrıca kopyalanır.
- **Zamanlayıcı:** VPS'te tek cron satırı: `* * * * * cd /var/www/worksitem/current && php artisan schedule:run`.
- **Kuyruk:** Şu an `QUEUE_CONNECTION=sync`; VPS'te Redis + supervisor ile arka plana alınabilir (yapay zekâ işleri için).
- **Belge kökü:** VPS'te nginx doğrudan `public/` klasörünü gösterir; `public_html` bağlantılarına gerek kalmaz.
- **Eski betikler:** `scripts/deploy.sh` (git pull akışı) ve `scripts/daily-market-mail.sh` (kullanım dışı) VPS geçişinde yenisiyle değiştirilecek.
