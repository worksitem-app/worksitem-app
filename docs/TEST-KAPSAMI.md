# Test kapsamı — araçlar ve para hesapları (v87, 21.10.2026)

Amaç: brick/money (kuruş hassasiyeti) gelmeden önce bugünkü TL sonuçlarını kilitlemek.
"Altın değer" = bilinen girdi → beklenen tutar. Fiyat bilerek değişirse beklenen değer de güncellenir;
fiyat değişmediği halde sayı değişiyorsa hesap bozulmuştur.

## Özet (78 araç)

| Grup | Araç | Hesap nerede | Altın değer testi |
|---|---|---|---|
| Hesap motoru (KitRegistry) | 31 | PHP `app/Estimators` | ✅ `KitGoldenValuesTest` — 31 aracın hepsi + 7 dal varyantı (kalem kalem, alt/orta/üst, birim fiyat; canlı `/hesapla` ucu da aynı tutarı vermeli). Yeni araç eklenince test kırılır |
| Banyo maliyet | 1 | PHP `BathroomEstimator` | ✅ `BathroomCalculatorTest` (önceden vardı) |
| GES planlayıcı | 1 | PHP `SolarPlanService::economics` | ✅ `MoneyPathsGoldenTest` — konut, düz çatı fabrika + batarya, arazi |
| Planlayıcılar (banyo/WC/mutfak/oda) | 4 | Tarayıcı + PHP katalog | Katalog/ürün testleri var; tutar hesabı yok |
| Tarayıcıda hesaplayan araçlar | 41 | Sayfa içi JavaScript | ⏳ Yalnız sayfa açılıyor/yapı testleri. PHP testi JS'i çalıştıramaz |

Araç dışı para yolları:

| Yol | Hesap | Test |
|---|---|---|
| `/maliyet-hesapla` proje sonucu | `KaintaCostEngine` | ✅ 5 durum (İstanbul çarpanı dahil). **Bugün float kayması var:** 60 m² hafif İstanbul = 241.499,99999999997 TL; testler 0,01 TL toleranslı, brick/money sonrası tam eşitliğe çekilecek |
| Ana sayfa yapı maliyeti `/hesaplama/yapi-maliyeti` | `BuildingEstimateService` | ✅ m² × sınıf birim maliyeti × 1,60; ±%10; dağılım toplamı |
| Ana sayfa tadilat `/hesaplama/tadilat-maliyeti` | `RenovationEstimateService` | ✅ kısmi / orta / komple, ±%20 |
| Poz / ihale teklif fiyatı | `TenderPricingService` | ✅ kâr hariç, kâr dahil %25, kuruş sınırı (833,325 → 833,33), kâr durumu bilinmiyorsa fiyat yok |
| Seramik ucu `/hesaplama/seramik-maliyeti` | `TileEstimateService` | ⏳ Kalite kontrolünden geçmiş piyasa fiyatı ister; test verisi hazırlanmalı |
| AI ön keşif (`AutoBoq`, `AiCostEstimationService`) | Gemini çıktısı | — Model çıktısı; altın değer anlamsız |

## Korunacak yuvarlama kuralları (brick/money'de `RoundingMode::HALF_UP`)

- Hesap motoru: her kalem 100 TL'ye, alt/orta/üst 1.000 TL'ye yuvarlanır; yarım yukarı (2.750 → 2.800, 2.500 → 3.000).
- Toplam, yuvarlanmış kalemlerin toplamından alınır; sıfır/negatif kalem listeden düşer.
- GES: kalemler 100 TL'ye, toplam ise **yuvarlanmamış** kalemlerden (×1,05) 1.000 TL'ye — kalemlerin toplamı toplamı tutmayabilir (bilinçli).
- İhale: birim ve toplam kuruşa; toplam yuvarlanmamış birimden hesaplanır.

## Not edilen tutarsızlık

`config/worksitem_estimators.php` seramik yapıştırıcı ve derz yedek fiyatlarına %20 KDV ekliyor, ama veritabanındaki kayıtlar
(`unit_prices`, 06.10 migration) zaten KDV dahil. Veritabanı doluyken sonuç doğru; yedek fiyat yalnız tablo boşsa devreye girer ve o zaman ~%20 yüksek çıkar.

## Sıradaki

1. **v88 brick/money**: önce `KaintaCostEngine`, `TenderPricingService`, `KitEstimator::estimate()` yuvarlaması; bu testler değişmeden yeşil kalmalı.
2. **Tarayıcı araçları**: 41 aracın hesabı sayfa içi JS'te. Paket eklemeden yapılabilir: hesap fonksiyonlarını `public/js/calc/*.js` dosyasına ayırıp Node'un kendi test aracıyla (`node --test`, GitHub'da hazır) altın değer. Öncelik: para hesaplayanlar (iş makinesi, lojistik, işçilik, SGK asgari işçilik, birim fiyat analizi, kat karşılığı, kira değeri, konut kredisi), sonra mühendislik (kablo, kiriş, kolon, karot, merdiven).
