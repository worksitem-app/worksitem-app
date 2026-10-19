# Yapay zekâ sağlayıcısı planı (v81 — yalnız plan, kod yok)

Durum: 20.10.2026. Bu sürümde hiçbir sağlayıcı eklenmedi, hiçbir paket kurulmadı.
Amaç: Gemini'nin yanına Claude (ve gerekirse OpenAI) eklenebilsin; seçim tek `.env` ayarıyla yapılsın.

## 1. Bugün ne var

Gemini'ye doğrudan HTTP ile giden **13 dosya** var; model adları çoğunda koda gömülü:

| Grup | Dosyalar | İş |
|---|---|---|
| Metin / JSON | `KaintaCostEngine`, `AISalesEngineer`, `ConTech/AutoBoqService` (2 çağrı), `ConTech/PriceImportService`, `GeminiRayicService`, `Console/EnrichPozSeo`, `Console/PdfToRayicCsv` | maliyet tahmini, otomatik keşif, rayiç ayıklama, SEO metni |
| Google aramalı (grounding / Interactions API) | `Pricing/PriceRobot`, `Console/ResearchTileWebPrices`, `Console/ResearchPaintWebPrices` | fiyat robotu, web fiyat araştırması |
| Görsel üretim | `AI/GeminiImage`, `TileVisualizerController`, `ToolController` (render) | oda/seramik görseli |
| Dosya yükleme | `GeminiRayicService`, `PdfToRayicCsv` (Files API) | PDF rayiç okuma |

Emniyet katmanları zaten var ve korunacak: `AI_SPEND_ENABLED`, `AI_GLOBAL_DAILY_LIMIT` (300), kullanıcı başı ücretsiz hak, `AiUsageRecorder` (harcama defteri), `AiCredits`.

## 2. Ne taşınır, ne taşınmaz

- **Taşınabilir (metin/JSON):** yalnız ilk gruptaki işler. Claude bu işleri yapabilir.
- **Gemini'de kalır:** görsel üretim (Claude görsel üretmez) ve Google aramalı fiyat robotu (Google arama sonuçlarına bağlı). Bunlar plan dışı.
- **Dikkat:** PDF rayiç okuma Gemini Files API'ye bağlı; Claude'a geçerse PDF doğrudan mesajla gönderilir, akış ayrıca test edilmeli.

## 3. Mimari (önerilen)

1. `app/Services/AI/TextAi.php` arayüzü: `json(string $task, string $system, array $input, array $schema): array` ve `text(...)`.
2. `GeminiTextDriver` — bugünkü HTTP çağrılarının tek yerde toplanmış hali. **İlk adım yalnız bu**: davranış aynı kalır, sadece kod düzenlenir.
3. `ClaudeTextDriver` — sonra eklenir. Seçenekler:
   - `anthropics/anthropic-sdk-php` (resmi, MIT): sadece Claude; en az bağımlılık. **Önerilen.**
   - `prism-php/prism` (MIT): Gemini + Claude + OpenAI tek API; daha fazla bağımlılık, sürüm takibi gerekir.
4. Görev bazlı seçim, `config/services.php`:
   ```
   AI_TEXT_PROVIDER=gemini            # varsayılan; claude yazılınca tüm metin işleri Claude'a
   AI_PROVIDER_KAINTA_COST=           # boşsa varsayılan; tek işi ayrı sağlayıcıya yönlendirmek için
   AI_FALLBACK_PROVIDER=              # birincisi hata verirse (zaman aşımı/5xx) bir kez ikincisi
   ```
5. Her çağrı `AiUsageRecorder`'a sağlayıcı ve model adıyla yazılır; günlük tavan sağlayıcıdan bağımsız toplamdan sayılır.
6. Model adları koddan çıkar, yalnız config/.env'de durur.

## 4. Sıra (her biri ayrı sürüm, önce staging)

| Adım | İş | Risk |
|---|---|---|
| A | `TextAi` + `GeminiTextDriver`; 7 metin servisini buna geçir. Sağlayıcı yine yalnız Gemini | Düşük: davranış aynı, testlerle karşılaştırılır |
| B | Model adlarını config'e taşı, `AiUsageRecorder`'a sağlayıcı alanı (migration) | Düşük |
| C | `ClaudeTextDriver` (resmi SDK), `AI_TEXT_PROVIDER=gemini` kalır; staging'de tek görevle dene (ör. `AISalesEngineer`) | Orta: JSON biçimi ve Türkçe çıktı karşılaştırması |
| D | Yedek sağlayıcı (fallback) ve yönetici panelinde sağlayıcı/maliyet raporu | Orta |
| E | promptfoo ile talimat enjeksiyonu testleri (VPS'e geçince, öneri listesi #10) | — |

## 5. Senden gerekecekler (C adımından önce, şimdi değil)

- Anthropic Console'da hesap, **aylık harcama limiti** ve ayrı bir API anahtarı (GitHub güvenlik incelemesindekinden farklı anahtar).
- Kişisel veri: kullanıcı girdileri (adres, telefon) modele gitmemeli; bugünkü kırpma kuralları yeni sürücüde de uygulanır. Yurt dışına veri aktarımı için KVKK metnine sağlayıcı adı eklenmeli (avukat notuna madde).
- Model seçimi ve fiyat: C adımında güncel model listesi ve fiyatlarla birlikte karar verilir; bu belgeye fiyat yazılmadı.

## 6. Yapılmayacaklar

- OmniRoute gibi ayrı ağ geçidi servisi (paylaşımlı hostingte sürekli çalışan Node süreci yok; 15.10 kararı).
- Görsel üretimi ve aramalı fiyat robotunu Claude'a taşımak.
- Sağlayıcıyı kullanıcının seçmesi (yalnız yönetici/.env).
