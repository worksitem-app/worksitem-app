<?php

namespace App\Estimators;

class PvcEstimator extends KitEstimator
{
    public function key(): string
    {
        return 'pvc';
    }

    public function slug(): string
    {
        return 'pvc-dograma-hesaplayici';
    }

    public function perUnitLabel(): ?string
    {
        return 'pencere başına';
    }

    private function p(): array
    {
        return (array) config('worksitem_estimators.pvc');
    }

    public function page(): array
    {
        $y = now()->year;

        return [
            'title' => 'PVC pencere maliyeti',
            'h1' => "PVC pencere ve doğrama fiyatı hesaplama ($y)",
            'lede' => 'Pencere sayısını ve ortalama ölçüyü girin; profil serisi, cam türü ve açılım tipine göre profil, cam, donanım, montaj ve söküm kalem kalem hesaplansın.',
            'seo_title' => "PVC Pencere Fiyatları $y: m² Doğrama ve Isıcam Maliyeti | Worksitem",
            'seo_desc' => "$y PVC pencere fiyatı: pencere ölçüsü, profil, ısıcam türü (çift, ısı kontrollü, üçlü) ve açılım tipine göre doğrama maliyetini montaj ve söküm dahil ücretsiz hesaplayın.",
            'service' => 'PVC Doğrama',
            'wait' => 'Pencere sayısını ve ölçüleri girin; maliyet burada anında hesaplanır.',
            'per_unit_label' => $this->perUnitLabel(),
            'sponsor_title' => 'Doğrama ve cam markaları',
            'facts' => ['Ücretsiz, kayıt gerekmez', 'Isıcam karşılaştırması', '$ € £ karşılığı'],
            'method' => [
                'Toplam doğrama alanı = pencere sayısı × genişlik × yükseklik.',
                'Profil ve donanım m² fiyatı profil serisine (kaliteye) göre; renkli/ahşap desende %15 eklenir.',
                'Isıcam m² fiyatı cam türüne göre; açılım donanımı ve montaj pencere başına eklenir.',
                'Söküm, panjur, sineklik ve denizlik seçilirse eklenir; %10 beklenmedik gider ve −%15 / +%20 aralık uygulanır.',
            ],
            'factors' => [
                ['Cam türü', 'Isı kontrol kaplamalı cam enerji tasarrufu sağlar; üçlü cam soğuk bölgelerde tercih edilir ve çift camın yaklaşık 1,7 katıdır.'],
                ['Profil serisi', 'Odacık sayısı ve et kalınlığı yüksek premium seriler hem yalıtımda hem ömürde fark yaratır.'],
                ['Açılım', 'Sabit pencere en ucuzu; çift açılım ve sürme sistemler donanım maliyetini artırır.'],
                ['Söküm ve tamir', 'Eski doğramanın sökülmesi sıva ve denizlik tamiri gerektirir.'],
            ],
            'faq' => [
                ['PVC pencere m² fiyatı ne kadar?', 'Orta seri beyaz profil, çift ısıcam ve tek açılım ile montaj dahil '.now()->year.' fiyatlarıyla bir 150×150 cm pencere yaklaşık '.$this->exampleRange(['count' => 1, 'width' => 150, 'height' => 150, 'quality' => 'orta', 'color' => 'beyaz', 'glass' => 'cift', 'opening' => 'tek', 'scope' => 'yeni']).' tutar.'],
                ['Isıcam mı, ısı kontrol cam mı?', 'Isı kontrol (Low-E) kaplamalı cam, standart ısıcama göre ısı kaybını yaklaşık %40–50 azaltır; fiyat farkı ise m² başına birkaç yüz liradır.'],
                ['Pencere değişimi kaç günde biter?', 'Ölçü alındıktan sonra üretim 1–2 hafta sürer; bir dairenin pencerelerinin sökülüp takılması genellikle 1–2 gündür.'],
                ['Hesaba neler dahil?', 'Profil, takviye, donanım, ısıcam ve montaj; seçerseniz söküm, panjur, sineklik ve denizlik. KDV dahil yaklaşık değerlerdir.'],
            ],
            'related' => [['/araclar/mantolama-maliyeti', 'Mantolama maliyeti'], ['/araclar/enerji-tuketim-hesaplama', 'Enerji tüketimi'], ['/araclar/boya-hesaplayici', 'Boya hesaplayıcı'], ['/maliyet-hesapla', 'Tüm ev tadilat bütçesi']],
        ];
    }

    public function steps(): array
    {
        return [
            ['type' => 'numbers', 'title' => 'Pencereler', 'cols' => 3, 'fields' => [
                ['key' => 'count', 'label' => 'Pencere sayısı', 'unit' => 'adet', 'min' => 1, 'max' => 300, 'step' => 1, 'placeholder' => 'Örn. 6', 'required' => true],
                ['key' => 'width', 'label' => 'Ortalama genişlik', 'unit' => 'cm', 'min' => 40, 'max' => 400, 'step' => 5, 'placeholder' => 'Örn. 150', 'required' => true],
                ['key' => 'height', 'label' => 'Ortalama yükseklik', 'unit' => 'cm', 'min' => 40, 'max' => 300, 'step' => 5, 'placeholder' => 'Örn. 150', 'required' => true],
            ], 'help' => 'Balkon kapısı varsa onu da pencere olarak sayın ve ortalamaya katın.'],
            ['type' => 'cards', 'key' => 'quality', 'title' => 'Profil serisi', 'short' => 'Profil serisi', 'required' => true, 'cols' => 3, 'options' => [
                'ekonomik' => ['label' => 'Ekonomik', 'hint' => '3–4 odacık'],
                'orta' => ['label' => 'Orta', 'hint' => '5 odacık, bilinen marka'],
                'premium' => ['label' => 'Premium', 'hint' => '6+ odacık, A sınıfı'],
            ]],
            ['type' => 'cards', 'key' => 'color', 'title' => 'Profil rengi', 'short' => 'Profil rengi', 'required' => true, 'cols' => 2, 'options' => [
                'beyaz' => ['label' => 'Beyaz', 'hint' => 'Standart'],
                'renkli' => ['label' => 'Renkli / ahşap desen', 'hint' => '+%15'],
            ]],
            ['type' => 'cards', 'key' => 'glass', 'title' => 'Cam', 'short' => 'Cam türü', 'required' => true, 'cols' => 3, 'options' => [
                'cift' => ['label' => 'Çift ısıcam', 'hint' => '4+12+4 standart'],
                'isi' => ['label' => 'Isı kontrol (Low-E)', 'hint' => 'Konfor / sinerji'],
                'uclu' => ['label' => 'Üçlü cam', 'hint' => 'Soğuk bölge'],
            ]],
            ['type' => 'cards', 'key' => 'opening', 'title' => 'Açılım', 'short' => 'Açılım tipi', 'required' => true, 'cols' => 4, 'options' => [
                'sabit' => ['label' => 'Sabit', 'hint' => 'Açılmaz'],
                'tek' => ['label' => 'Tek açılım', 'hint' => 'Çift yönlü (vasistas)'],
                'cift' => ['label' => 'Çift kanat', 'hint' => 'İki açılır kanat'],
                'surme' => ['label' => 'Sürme', 'hint' => 'Balkon / geniş açıklık'],
            ]],
            ['type' => 'cards', 'key' => 'scope', 'title' => 'Mevcut durum', 'short' => 'Mevcut durum', 'required' => true, 'cols' => 2, 'options' => [
                'yeni' => ['label' => 'Yeni yapı', 'hint' => 'Boş kasa / açıklık'],
                'degisim' => ['label' => 'Eski pencere değişecek', 'hint' => 'Söküm + tamir'],
            ]],
            ['type' => 'checks', 'key' => 'extras', 'title' => 'Ekstralar', 'options' => ['panjur' => 'Panjur', 'sineklik' => 'Sineklik', 'denizlik' => 'Mermer denizlik']],
        ];
    }

    protected function compute(array $in): array
    {
        $p = $this->p();
        $n = (int) round((float) $in['count']);
        $w = (float) $in['width'] / 100;
        $h = (float) $in['height'] / 100;
        $area = $n * $w * $h;
        $frame = $area * $this->q($in, $p['frame_m2']) * ($in['color'] === 'renkli' ? $p['color_factor'] : 1);
        $items = [
            'sokum' => ['label' => 'Eski doğrama sökümü ve tamir', 'mid' => $in['scope'] === 'degisim' ? $n * $p['removal_each'] : 0, 'color' => '#5B6472'],
            'profil' => ['label' => 'PVC profil, takviye ve donanım', 'mid' => $frame + $n * $p['opening'][$in['opening']], 'color' => '#101318'],
            'cam' => ['label' => 'Isıcam ('.$this->steps()[3]['options'][$in['glass']]['label'].')', 'mid' => $area * 0.82 * $p['glass_m2'][$in['glass']], 'color' => '#7FB3C8'],
            'montaj' => ['label' => 'Montaj, köpük ve silikon', 'mid' => $n * $p['install_each'], 'color' => '#FFB800'],
            'panjur' => ['label' => 'Panjur', 'mid' => $this->has($in, 'extras', 'panjur') ? $area * 1.1 * $p['shutter_m2'] : 0, 'color' => '#8A6200'],
            'sineklik' => ['label' => 'Sineklik', 'mid' => $this->has($in, 'extras', 'sineklik') ? $n * $p['screen_each'] : 0, 'color' => '#C9A55A'],
            'denizlik' => ['label' => 'Mermer denizlik', 'mid' => $this->has($in, 'extras', 'denizlik') ? $n * ($w + 0.1) * $p['sill_m'] : 0, 'color' => '#2F6F8F'],
        ];
        $days = [max(1, (int) ceil($n / 8)), max(2, (int) ceil($n / 4))];

        return [
            'items' => $items,
            'per_unit' => $n,
            'days' => $days,
            'meta' => [['Montaj süresi', $days[0].'–'.$days[1].' iş günü'], ['Toplam doğrama', self::n1($area).' m²']],
            'notes' => ['Üretim süresi (genellikle 1–2 hafta) montaj süresine dahil değildir.'],
        ];
    }

    public function materials(array $in): array
    {
        $n = (int) round((float) $in['count']);
        $w = (float) $in['width'] / 100;
        $h = (float) $in['height'] / 100;
        $rows = [
            ['PVC pencere ('.((int) $in['width']).'×'.((int) $in['height']).' cm)', $n, 'adet', $this->steps()[4]['options'][$in['opening']]['label']],
            ['Isıcam', self::n1($n * $w * $h * 0.82), 'm²', 'Kasa payı düşülmüş yaklaşık'],
            ['Montaj köpüğü', (int) max(1, ceil($n / 3)), 'tüp', 'Pencere başına yaklaşık 1/3 tüp'],
            ['Silikon', (int) max(1, ceil($n / 2)), 'tüp', 'İç ve dış derz'],
            ['Montaj dübeli / vida', $n * 8, 'adet', 'Pencere başına 8'],
        ];
        if ($this->has($in, 'extras', 'denizlik')) {
            $rows[] = ['Mermer denizlik', self::n1($n * ($w + 0.1)), 'mtül', 'Pencere genişliği + 10 cm'];
        }

        return $rows;
    }

    public function phases(array $in): array
    {
        return [['Ölçü ve üretim siparişi', .3], [($in['scope'] ?? '') === 'degisim' ? 'Eski doğrama sökümü' : 'Kasa hazırlığı', .2], ['Montaj ve ayar', .35], ['Silikon, tamir, temizlik', .15]];
    }
}
