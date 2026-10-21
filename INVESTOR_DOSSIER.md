# Worksitem — Yatırımcı Teknik Değerlendirmesi

**Güncelleme:** 15 Ekim 2026 (v69) · **Canlı:** https://worksitem.com · **Depo:** GitHub (özel)

İş modeli ve yol haritası: [INVESTOR_PITCH.md](INVESTOR_PITCH.md). Mimari ve işletim: [README.md](README.md), [DEPLOY.md](DEPLOY.md).

---

## 1. Ürün özeti

Worksitem, tadilat ve inşaat maliyetini güncel fiyatlarla hesaplayan ücretsiz araçlar üzerinden ev sahiplerini, usta/firmaları ve markaları buluşturan bir platformdur. Gelir: firmalardan eşleşme bedeli (31.03.2027'ye kadar kurucu firma programıyla ücretsiz), sponsor alanları, Pro rapor ve yapay zekâ kredisi.

## 2. Teknik yapı

| Katman | Teknoloji |
|---|---|
| Uygulama | Laravel 13, PHP 8.3 |
| Yönetim paneli | Filament 3 (16 kaynak + özel sayfalar: saha fiyatları, A/B testleri, fiyat merkezi) |
| Arayüz | Blade, Livewire, Tailwind 4, Vite 8; PWA (servis çalışanı, web bildirimi) |
| Veritabanı | MariaDB (canlı), SQLite (test) |
| Dış servisler | Gemini, Replicate, iyzico, Netgsm, TCMB, GA4 |

**Ölçüler (15.10.2026):** 361 route · 40 controller · 33 model · 65 migration · 25 artisan komutu · 460 otomatik test · ~49.000 satır PHP · 210 Blade şablonu · 77 araç (74 yayında).

### Mimari kararlar

- **Tek hesap kaynağı:** Hesap motorundaki 31 araçta sonuç sunucuda hesaplanır; canlı sonuç ve PDF Pro rapor aynı kodu kullanır.
- **Fiyat güveni:** Resmî poz fiyatları + yönetici onaylı piyasa fiyatları; robotun önerisi onaysız yayına girmez; fiyat bulunamazsa sonuç uydurulmaz.
- **Harcama kilitleri:** Yapay zekâ çağrıları `AI_SPEND_ENABLED` ile tek noktadan kapanır; kullanıcı başına günlük hak + kredi + site geneli günlük tavan. SMS'te numara/IP/site günlük tavanları.
- **Kademeli açılış:** Pazaryeri, firma rehberi, ödemeler, yapay zekâ ayrı özellik anahtarlarıyla açılır; araçlar yayın ön ayarlarıyla.
- **Güvenli yayın:** Staging → canlı betiği yedek alır, migration önizlemesi çıkarır, duman testi yapar, tek komutla geri döner.

## 3. Kalite ve güvenlik

- 460 otomatik test (GitHub Actions), her sürümden önce tamamı çalıştırılır; arayüz değişiklikleri tarayıcıda (Playwright) kontrol edilir.
- Canlı yapılandırma denetimi (`app:preflight`) ve günlük "motor kontrolü" (`launch-check.sh`): son kontrol 0 FAIL / 0 WARN.
- Gizli bilgiler yalnız sunucu `.env` dosyasında; çıktılarda maskelenir.
- CSRF, oturum şifreleme, güvenli çerez, güvenlik başlıkları; proje bağlantılarında tahmin edilemeyen kimlik (ULID); kimlik numaraları herkese açık sayfalarda maskeli.
- KVKK: çerez onayı (GA4 yalnız onayla), açık rıza kaydı, aydınlatma ve başvuru sayfaları; telefon numarası kullanıcı izni olmadan paylaşılmaz.
- Vergi numarası/TCKN kontrol haneli doğrulama; e-fatura/e-arşiv için fatura bilgileri; faturayı mali müşavir keser.

## 4. Riskler

| Risk | Etki | Önlem |
|---|---|---|
| Tek kişilik teknik ekip | Yüksek | Ayrıntılı belgeler, testler, geri dönüşlü yayın betiği; yarı zamanlı yazılımcı alımı planlı |
| Paylaşımlı hosting | Orta | VPS geçiş planı ve tetikleyicileri hazır (`docs/SUNUCU-TASIMA.md`) |
| Pazaryeri hukuki sorumluluk (AYM K.2026/42, yürürlük ~Mart 2027) | Orta | Firma tazmin taahhüdü, avukat incelemesi tamamlanmadan kullanıcı tarafı kapalı |
| Yapay zekâ maliyeti | Orta | Harcama kilidi, günlük tavanlar, kredi sistemi, sağlayıcı bütçe limiti |
| Fiyat isabeti | Orta | Robot + onay akışı, saha fiyat beyanları, tarihli rapor |

## 5. Açık işler

- Avukat onayı → teklif pazaryeri ve firma rehberi.
- Google Play yayını (kapalı test).
- Hata takibi servisi (Sentry/Flare), yönetim paneline iki adımlı doğrulama, CI'da MariaDB.

---

*Teknik inceleme soruları için: info@worksitem.com*
