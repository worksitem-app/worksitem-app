# Worksitem

[![Tests](https://github.com/worksitem-app/worksitem-app/actions/workflows/tests.yml/badge.svg)](https://github.com/worksitem-app/worksitem-app/actions/workflows/tests.yml)

> İnşaat maliyet hesaplama, keşif (BoQ) ve teklif yönetimi SaaS platformu.

Türkiye'deki müteahhitler, mimarlar ve yapı profesyonelleri için geliştirilmiş bulut tabanlı maliyet ve teklif yönetim platformu. 60+ hesaplayıcı, AI destekli fiyat motoru ve müteahhit iş akışı ile inşaat öncesi hazırlık sürecini hızlandırır.

Canlı: https://worksitem.com

---

## Ne Yapıyor?

- **60+ hesaplayıcı** — beton, demir, sıva, boya, mantolama, çatı, MEP, statik, finans
- **Keşif (BoQ)** — poz import, birim fiyat analizi, kâr marjı yönetimi
- **Teklif üretimi** — PDF çıktı, müşteriye özel fiyatlandırma
- **AI destekli özellikler** — iç mekan tasarımı, otomatik fiyat güncelleme
- **Lead yönetimi** — CRM, satış hunisi, dönüşüm takibi

---

## Tech Stack

| Katman | Teknoloji |
|---|---|
| Framework | Laravel 13.x |
| Dil | PHP 8.3 |
| Admin Panel | Filament 3.3 |
| Frontend | Blade + Livewire + Tailwind 4 |
| Veritabanı | MySQL 8 |

---

## Kurulum

### Gereksinimler

- PHP 8.3+
- Composer 2.9+
- Node.js 20+ / npm
- MySQL 8+

### Adımlar

1. `git clone https://github.com/worksitem-app/worksitem-app.git`
2. `cd worksitem-app`
3. `composer install && npm install`
4. `cp .env.example .env && php artisan key:generate`
5. `php artisan migrate && php artisan db:seed`
6. `npm run build`
7. `php artisan serve`

Tarayıcıda aç: `http://localhost:8000`

> **Canlıya çıkış:** [DEPLOY.md](DEPLOY.md) — `scripts/deploy.sh`, `php artisan app:preflight`, `php artisan app:make-admin`

### Yapılandırma

.env dosyasında tanımlanması gereken API anahtarları:

- `GEMINI_API_KEY` — Google Gemini (AI fiyat güncelleme)
- `GEMINI_BOQ_MODEL` — varsayılan: `gemini-2.5-flash`
- `REPLICATE_API_TOKEN` — Replicate (AI iç mekan tasarımı)

---

## Test

```bash
php artisan test
```

---

## Proje İstatistikleri

- 233 route
- 18 controller
- 14 Eloquent model
- 24 database migration
- 60+ hesaplayıcı aracı
- ~11.300 satır PHP

---

## Lisans

Ticari mülkiyet. Tüm hakları saklıdır. © 2026 Worksitem

---

## İletişim

- Web: https://worksitem.com
- GitHub: https://github.com/worksitem-app
- E-posta: info@worksitem.com
