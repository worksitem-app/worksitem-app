# Worksitem — Yatırımcı Teknik Değerlendirmesi

**Hazırlanma tarihi:** 21 Eylül 2026  
**Canlı URL:** https://worksitem.com  
**Repo:** https://github.com/worksitem-app/worksitem-app

---

## 1. Ürün Özeti

**Worksitem**, Türkiye'deki müteahhitler, mimarlar ve yapı profesyonelleri için geliştirilmiş bulut tabanlı **maliyet hesaplama ve teklif yönetim platformu**dur.

### Değer Önerisi

İnşaat projelerinin **ön maliyet hesabı** ve **teklif hazırlama** sürecini tek platformda toplar:

- **60+ hesaplayıcı** — beton, demir, sıva, boya, mantolama, çatı, MEP, statik, finans
- **Keşif (BoQ)** — poz import, birim fiyat analizi, kâr marjı yönetimi
- **Teklif üretimi** — PDF çıktı, müşteriye özel fiyatlandırma
- **AI destekli özellikler** — iç mekan tasarımı, otomatik fiyat güncelleme
- **Lead yönetimi** — CRM, satış hunisi, dönüşüm takibi

### Hedef Kitle

- Müteahhitler (küçük-orta ölçek)
- Mimarlık ofisleri
- Yapı denetim firmaları
- Tadilat ve dekorasyon firmaları

### Niş ve Rekabet Avantajı

Türkiye'de inşaat sektörüne özel **AI destekli** ve **uçtan uca** bir platform bulunmuyor. Mevcut çözümler:
- Ya çok pahalı (SAP, Oracle)
- Ya çok basit (Excel)
- Ya da sektöre özel değil (genel muhasebe)

Worksitem, **SEO ile ücretsiz araç** üzerinden lead toplayıp, **SaaS abonelik** ile gelir elde ediyor.

---

## 2. Teknik Güçlü Yönler

### Tech Stack

| Katman | Teknoloji |
|---|---|
| Framework | Laravel 13.x (en güncel) |
| Dil | PHP 8.3 |
| Admin Panel | Filament 3.3 |
| Frontend | Blade + Livewire + Tailwind 4 |
| Build | Vite 8 |
| Veritabanı | MySQL 8 |
| PDF | barryvdh/laravel-dompdf |
| Excel | phpoffice/phpspreadsheet |

### Proje İstatistikleri

- **233 route** — gerçek SaaS kapsamı
- **18 controller**
- **14 Eloquent model**
- **24 database migration**
- **60+ hesaplayıcı aracı**
- **~11.300 satır PHP** (app + routes + database)

### Kalite ve Süreç

- ✅ **Git versiyon kontrolü** — 7 commit, temiz history
- ✅ **GitHub Actions CI** — her push'ta 17 test otomatik
- ✅ **Test kapsamı** — %15-20 (hedef %80)
- ✅ **README** — profesyonel dokümantasyon
- ✅ **Migration tutarlılığı** — sıfırdan kurulum yapılabiliyor
- ✅ **Environment yönetimi** — `.env`, `.env.example`, gitignore

### Güvenlik

- `.env` dosyası git'te yok
- API anahtarları environment variable olarak saklanıyor
- SQL injection koruması (Eloquent ORM)
- CSRF token tüm POST isteklerinde
- KVKK uyumlu consent tracking (contact_consent, marketing_consent)
- IP hash saklama (KVKK uyumlu)

---

## 3. Riskler ve Mitigasyonlar

| Risk | Etki | Mitigasyon |
|---|---|---|
| **Bus factor: 1 geliştirici** | Yüksek | Kod dokümante edilmiş, README + yorumlar. İlk yatırımla junior developer alımı planlı. |
| **AI API bağımlılığı** | Orta | Gemini + Replicate bağımsız. Birinin kesintisi hizmeti durdurmuyor. |
| **Test kapsamı düşük (%15-20)** | Orta | Roadmap'te %80 hedef. CI altyapısı hazır. |
| **Shared hosting** | Orta | Trafik 3x artarsa dedicated sunucuya geçiş. Maliyet ~$50/ay. |
| **Regülasyon (KVKK)** | Düşük | Consent tracking mevcut, IP hash. |
| **Kur dalgalanması (TL)** | Orta | Fiyatlar DB'den çekiliyor, admin panelinden güncellenebiliyor. |

---

## 4. Roadmap (6-12 ay)

### Çeyrek 1 (0-3 ay)
- [ ] Stripe abonelik entegrasyonu (3 tier: Free / Pro / Enterprise)
- [ ] Test kapsamı %50'ye çıkarma
- [ ] Landing page yenileme
- [ ] Onboarding flow (kayıt → ilk proje → ilk teklif)

### Çeyrek 2 (3-6 ay)
- [ ] Çok dilli destek (EN)
- [ ] Mobil uygulama (React Native)
- [ ] ERP entegrasyonu (Logo, Netsis)
- [ ] Real-time işbirliği (Laravel Reverb)

### Çeyrek 3-4 (6-12 ay)
- [ ] API marketplace (3. parti entegrasyonlar)
- [ ] AI içerik üretimi (sözleşme, şartname)
- [ ] Rapor ve analitik dashboard
- [ ] Kurumsal satış ekibi

---

## 5. Son 30 Günün Teknik Aktivitesi

Bu dönemde yapılan teknik iyileştirmeler:

- ✅ DB şifresi güçlendirildi
- ✅ Prod config güvenli hale getirildi (`APP_DEBUG=false`, `APP_ENV=production`)
- ✅ Blade syntax hataları çözüldü
- ✅ 84 çöp dosya temizlendi
- ✅ Git repo kurulumu ve GitHub push
- ✅ CI/CD altyapısı (GitHub Actions)
- ✅ Test kapsamı %1.7 → %15-20
- ✅ Migration tutarlılığı düzeltildi
- ✅ README ve dokümantasyon yazımı

---

## 6. Yatırımcı İçin Temel Sorular ve Cevaplar

**S: Bu ürün ne yapıyor?**  
C: İnşaat projelerinin maliyet hesabı ve teklif hazırlama sürecini tek platformda topluyor.

**S: Kime satıyor?**  
C: Müteahhitler, mimarlar, yapı profesyonelleri.

**S: Rakipleri kim?**  
C: Excel (ücretsiz ama dağınık), SAP/Oracle (pahalı, hantal), yerli ERP'ler (inşaat odaklı değil).

**S: Nasıl para kazanıyor?**  
C: Freemium SaaS abonelik (Free / Pro / Enterprise). Free plan SEO ile lead topluyor.

**S: Ne kadar olgun?**  
C: MVP'nin ötesinde — 233 route, 60+ araç, gerçek müşteri akışı. Ürün-pazar uyumu araştırma aşamasında.

**S: Teknik ekip?**  
C: Şu an solo founder. Yatırımla 1-2 junior developer alımı planlı.

**S: En büyük risk?**  
C: Bus factor (tek geliştirici). Ama kod dokümante, test altyapısı kuruldu, teknik borç yönetilebilir.

---

## 7. Sonuç

Worksitem, **Türkiye inşaat sektörüne özel**, **AI destekli**, **uçtan uca** bir SaaS platformu. Teknik temel sağlam, kod dokümante, test altyapısı kurulu.

Yatırım sonrası öncelikler:
1. **Go-to-market** — gerçek müşteri kazanımı
2. **Ürün-pazar uyumu** — hangi segment en çok değer görüyor?
3. **Ölçeklenebilirlik** — altyapı ve ekip büyütme
4. **Test kapsamı** — %80 hedefine çıkarma

---

*Bu belge, teknik due diligence sürecini hızlandırmak için hazırlanmıştır. Sorularınız için: info@worksitem.com*
