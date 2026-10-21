# Worksitem — Yatırımcı Sunumu

**Güncelleme:** 15 Ekim 2026 (ürün sürümü v69) · **Web:** https://worksitem.com

> Bu belgede yalnız ölçülmüş veya kaynağı belirtilmiş rakamlar yer alır. Pazar büyüklüğü rakamları sunumdan önce kaynakla doğrulanacaktır (bkz. 7. bölüm).

---

## 1. Problem

- **Ev sahibi** tadilata başlamadan bütçesini bilmiyor; ustadan gelen fiyatı karşılaştıracak bir dayanağı yok.
- **Usta ve firma** bütçesi belirsiz, ciddiyeti ölçülmemiş taleplerle zaman kaybediyor; ilan siteleri ve reklam pahalı.
- **Marka ve üretici** ürününü, malzeme kararının verildiği anda gösteremiyor.

## 2. Çözüm

Worksitem üç kitleyi aynı akışta buluşturur:

1. **Hesapla:** Ücretsiz, üyeliksiz hesaplayıcılar; güncel ve tarihli fiyatla kalem kalem maliyet, PDF Pro rapor.
2. **Eşleş:** Bütçesini bilen, telefonunu SMS ile doğrulamış kullanıcının talebi; iş koluna, bölgesine ve en küçük iş tutarına uyan kayıtlı firmalara gider. Kullanıcı teklifleri görür, seçer; iletişim yalnız seçilen firmayla paylaşılır.
3. **Görün:** Markalar ilgili hesap kaleminin yanında, etiketli sponsor kartıyla görünür. Sonuçlar sponsorluktan etkilenmez.

Sahadan toplanan firma fiyat beyanları ve her sabah çalışan fiyat robotu (yönetici onaylı) hesapların isabetini sürekli artırır.

## 3. Ürün (canlıda, 15.10.2026)

| Bileşen | Durum |
|---|---|
| Araçlar | 77 araç; 74 yayında, 2 yapay zekâ aracı açılıyor (v69). Banyo, mutfak, boya (iç/dış cephe + iskele), seramik (zemin/duvar ayrı ürün), parke, çatı, mantolama, PVC, inşaat maliyeti, metraj, statik ön kontrol |
| Planlayıcılar | Banyo, WC ve mutfak için 2D/3D |
| Fiyat altyapısı | ÇŞB/YFK resmî poz fiyatları + piyasa araştırması; fiyat robotu her sabah, yönetici onayı; her raporda fiyat tarihi |
| Firma tarafı | Ücretsiz kayıt, vergi no doğrulaması, şifresiz (SMS) firma paneli, uygulama bildirimi |
| Teklif pazaryeri | Kurulu ve test edildi; kullanıcı tarafı avukat onayından sonra açılacak |
| Ödeme | iyzico ile Pro rapor ve yapay zekâ kredisi |
| Uygulama | PWA (ana ekrana ekle) canlıda; Google Play paketi hazırlanıyor |

## 4. İş modeli

| Gelir kalemi | Durum | Fiyat |
|---|---|---|
| Eşleşme bedeli (firmadan, yalnız müşteri seçtiğinde) | Kurucu firma programı: 31.03.2027'ye kadar ücretsiz | Sonra iş tutarının %5'i, en az 250, en çok 7.500 TL + KDV |
| Sponsor alanları (markalar) | Başvuru alınıyor; sözleşmesiz yayın yok | Teklifle |
| Pro rapor ve yapay zekâ kredisi | Altyapı canlıda | Admin'den ayarlanır |
| Öne çıkan firma | Altyapı hazır | 2.400 TL + KDV / yıl |

Teklif vermek firmalar için her zaman ücretsizdir.

## 5. Bugünkü sayılar (ölçülmüş)

| Ölçü | Değer | Kaynak |
|---|---|---|
| Yayındaki araç | 74 | Canlı kontrol, 15.10.2026 |
| Site haritasındaki adres | 1.336 | Canlı kontrol, 15.10.2026 |
| Kayıtlı firma / sponsor | 0 / 0 | Saha satışı 15.10.2026'da başlıyor |
| Veritabanındaki talep / araç olayı | 13 / 714 | 13.10.2026 envanteri |
| Otomatik test | 460 | Depo, 15.10.2026 |

**Pilot firma — Kainta İnşaat** (kurucunun firması; kurucu beyanı, Eylül 2026): 9 yıllık firma, yıllık ciro yaklaşık ₺5 milyon, kainta.com aylık 2.970 Google tıklaması / 189.000 gösterim (%1,57 TO). Kainta platformda hizmet veren firmalardan biri olarak konumlanır; sözleşmelerde taraf Worksitem'dir.

## 6. Yol haritası

| Dönem | Hedef |
|---|---|
| Ekim–Aralık 2026 | İstanbul'da banyo, mutfak, boya, zemin iş kollarında kurucu firma programı; avukat onayı sonrası teklif pazaryeri; Google Play |
| Ocak–Mart 2027 | Bir il + iş kolunda sürekli 3+ teklif veren firma; sponsor sözleşmeleri; yapay zekâ kredi satışı |
| Nisan 2027 | Eşleşme bedeli başlar; yeni iller |

Ölçütler ve sonuçlar her pazartesi `launch-check` ve yönetim paneli raporlarıyla takip edilir.

## 7. Sunumdan önce doğrulanacaklar

- Türkiye'de tadilat/yapı firması sayısı ve tadilat harcaması (TÜİK, sektör raporları).
- Rakip platformların komisyon ve üyelik modelleri.
- Pilot firma verilerinin ekran görüntüleri (Search Console).

## 8. Ekip

Kurucu (inşaat sektörü, Kainta İnşaat). Önerilen ilk işe alımlar: firma operasyonu / saha satış (tam zamanlı), yarı zamanlı yazılımcı (nöbet ve kod inceleme).
