# Worksitem — Yatırımcı Demo Senaryosu

**Güncelleme:** 15 Ekim 2026 (v69) · **Süre:** 6 dakika · **Cihaz:** Telefon (uygulama olarak eklenmiş) + dizüstü

Kural: Yalnız ölçülmüş rakam söylenir (bkz. INVESTOR_PITCH.md, 5. bölüm).

---

## 1. Açılış (30 sn) — anasayfa

> "Worksitem tadilat maliyetini güncel fiyatlarla anında hesaplatıyor; bütçesini bilen kullanıcıyı doğru firmayla, markayı da karar anındaki kullanıcıyla buluşturuyor."

Göster: üç kitle bandı (ev sahibi / firma / marka), "Nasıl çalışır?" bölümü.

## 2. Hesapla (1,5 dk) — /araclar/banyo-maliyet-hesaplayici

1. Ölçüyü gir, seçimleri yap: sonuç her seçimde anında değişir.
2. Kalem kalem döküm, fiyat tarihi, $ € £ karşılığı.
3. Pro rapor PDF: malzeme listesi, üç kalite karşılaştırması, iş programı.

Ek gösterim: **Boya → Dış cephe** (bina çevresi + kat sayısı → iskele dahil maliyet) ve **Seramik → Zemin + duvar** (iki yüzey ayrı ürünle).

> "Fiyatlar resmî poz fiyatları ve piyasa araştırmasından; robot her sabah araştırır, uzman onaylamadan yayına girmez."

## 3. Eşleş (1,5 dk) — /firma-uyeligi ve firma paneli

1. Firma kaydı: vergi no doğrulaması, iş kolu ve bölge, 1 dakikalık fiyat soruları.
2. Firma paneli (SMS ile şifresiz giriş): gelen iş, teklif verme, kabul sonrası iletişim.
3. Telefonda bildirim: "Yeni iş: …"

> "Kurucu firma programı 31.03.2027'ye kadar ücretsiz. Sonra yalnız müşteri firmayı seçtiğinde %5 eşleşme bedeli, üst sınır 7.500 TL."

Not: Kullanıcı tarafı (/teklif-al) avukat onayına kadar kapalı; demoda staging kullanılır.

## 4. Görün (45 sn) — /markalar-icin

Sponsor kartının hesap sonucundaki yeri, "Sponsorlu" etiketi, başvuru formu, aylık rapor.

## 5. Yapay zekâ (1 dk) — banyo planlayıcı görselleştirme

Planı çiz → fotogerçekçi görsel. Giriş + günde 1 ücretsiz + kredi; site geneli günlük tavan.

## 6. Kapanış (45 sn)

- Bugün: 74 araç yayında, 1.336 sayfa site haritasında, uygulama telefona eklenebiliyor.
- Sıradaki: İstanbul'da kurucu firma saha satışı, avukat onayı sonrası pazaryeri, Google Play.

---

## Hazırlık listesi

- [ ] Telefonda Worksitem ana ekrana eklenmiş, firma paneli test hesabı açık
- [ ] Staging'de örnek talep ve teklif hazır
- [ ] Yapay zekâ için krediler yüklü
- [ ] `launch-check.sh` çıktısı temiz (ekran görüntüsü)
