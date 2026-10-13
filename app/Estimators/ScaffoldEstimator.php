<?php

namespace App\Estimators;

class ScaffoldEstimator extends KitEstimator
{
    public function key(): string { return 'iskele'; }

    public function slug(): string { return 'iskele-metraji-maliyeti'; }

    public function perUnitLabel(): ?string { return 'cephe m² başına'; }

    private function p(): array { return (array) config('worksitem_estimators.iskele'); }

    public function contingencyRate(): float { return 0.05; }

    public function page(): array
    {
        $y = now()->year;

        return [
            'title' => 'İskele maliyeti',
            'h1' => "İskele metrajı ve kiralama maliyeti hesaplama ($y)",
            'lede' => 'Cephe uzunluğunu ve bina yüksekliğini girin, iskele tipini ve kira süresini seçin; kurulum, söküm, güvenlik filesi ve nakliye kalem kalem hesaplansın.',
            'seo_title' => "İskele Fiyatı $y: m² İskele Kiralama ve Kurulum Maliyeti | Worksitem",
            'seo_desc' => "$y iskele fiyatları: cephe iskelesi ve boru iskelenin m² kiralama, kurulum, söküm, güvenlik filesi ve nakliye dahil maliyetini ücretsiz hesaplayın.",
            'service' => 'İskele Kurulumu',
            'wait' => 'Cephe ölçülerini girin ve seçimleri yapın; maliyet burada anında hesaplanır.',
            'per_unit_label' => $this->perUnitLabel(),
            'sponsor_title' => 'İskele ve iş güvenliği firmaları',
            'facts' => ['Ücretsiz, kayıt gerekmez', 'Kira süresine göre', '$ € £ karşılığı'],
            'method' => [
                'İskele alanı = toplam cephe uzunluğu × bina yüksekliği (+1 m korkuluk payı).',
                'm² fiyatı ilk ay kira + kurulum + söküm içindir; her ek ay için %35 eklenir.',
                'Güvenlik filesi m² başına, nakliye sabit eklenir; %5 beklenmedik gider ve −%15 / +%20 aralık uygulanır.',
            ],
            'factors' => [
                ['İskele tipi', 'Ön yapımlı (flanşlı/H) cephe iskelesi daha güvenli ve hızlı kurulur; boru iskeleden pahalıdır.'],
                ['Süre', 'Mantolama ve boya gibi işler uzadıkça aylık kira toplamı belirgin artar.'],
                ['Güvenlik', 'İş Sağlığı ve Güvenliği mevzuatı gereği korkuluk, topuk tahtası ve file zorunludur.'],
                ['Erişim', 'Dar sokak ve yüksek bina nakliye ve kurulum süresini uzatır.'],
            ],
            'faq' => [
                ['İskele m² fiyatı ne kadar?', 'Ön yapımlı cephe iskelesi 1 ay kira, kurulum ve söküm dahil '.now()->year.' fiyatlarıyla m² başına yaklaşık '.$this->exampleRange(['type' => 'cephe', 'length' => 60, 'height' => 15, 'months' => 1, 'net' => 'evet'], true).' arasıdır.'],
                ['İskele ne kadar sürede kurulur?', '5 katlı bir bina cephesi için 2–4 gün kurulum, 1–2 gün söküm yaygındır.'],
                ['Güvenlik filesi zorunlu mu?', 'Yaya ve araç trafiği olan cephelerde düşen malzemeye karşı file ve koruma zorunludur.'],
                ['Hesaba neler dahil?', 'Kira, kurulum, söküm, seçilirse file ve nakliye. KDV dahil yaklaşık değerlerdir.'],
            ],
            'related' => [['/araclar/mantolama-maliyeti', 'Mantolama maliyeti'], ['/araclar/boya-hesaplayici', 'Boya hesaplayıcı'], ['/araclar/kara-siva-hesaplama', 'Kara sıva'], ['/araclar/cati-maliyet-hesaplayici', 'Çatı maliyeti']],
        ];
    }

    public function steps(): array
    {
        return [
            ['type' => 'cards', 'key' => 'type', 'title' => 'İskele tipi', 'short' => 'İskele tipi', 'required' => true, 'cols' => 2, 'options' => [
                'cephe' => ['label' => 'Ön yapımlı cephe iskelesi', 'hint' => 'Flanşlı / H tipi, tam güvenlikli'],
                'boru' => ['label' => 'Çelik boru iskele', 'hint' => 'Kelepçeli, standart'],
            ]],
            ['type' => 'numbers', 'title' => 'Ölçüler ve süre', 'cols' => 3, 'fields' => [
                ['key' => 'length', 'label' => 'Toplam cephe uzunluğu', 'unit' => 'm', 'min' => 2, 'max' => 5000, 'step' => 1, 'placeholder' => 'Örn. 60', 'required' => true],
                ['key' => 'height', 'label' => 'Bina yüksekliği', 'unit' => 'm', 'min' => 2, 'max' => 120, 'step' => 0.5, 'placeholder' => 'Örn. 15', 'required' => true],
                ['key' => 'months', 'label' => 'Kira süresi', 'unit' => 'ay', 'min' => 1, 'max' => 24, 'step' => 1, 'placeholder' => '1', 'default' => 1],
            ]],
            ['type' => 'cards', 'key' => 'net', 'title' => 'Güvenlik filesi', 'short' => 'Güvenlik filesi', 'required' => true, 'cols' => 2, 'options' => [
                'evet' => ['label' => 'File olsun', 'hint' => 'Yasal zorunluluk (yaya / trafik)'],
                'hayir' => ['label' => 'File yok', 'hint' => 'Önermiyoruz'],
            ]],
        ];
    }

    private function area(array $in): float
    {
        return (float) $in['length'] * ((float) $in['height'] + 1);
    }

    protected function compute(array $in): array
    {
        $p = $this->p();
        $a = $this->area($in);
        $m = max(1, (int) round((float) ($in['months'] ?? 1)));
        $items = [
            'kira' => ['label' => 'Kurulum, söküm ve ilk ay kira', 'mid' => $a * $p['m2'][$in['type']], 'color' => '#FFB800'],
            'ek_ay' => ['label' => 'Ek kira ('.($m - 1).' ay)', 'mid' => $m > 1 ? $a * $p['m2'][$in['type']] * $p['extra_month'] * ($m - 1) : 0, 'color' => '#C9A55A'],
            'file' => ['label' => 'Güvenlik filesi', 'mid' => $in['net'] === 'evet' ? $a * $p['net_m2'] : 0, 'color' => '#7FB3C8'],
            'nakliye' => ['label' => 'Nakliye', 'mid' => $p['transport'] * ($a > 1500 ? 2 : 1), 'color' => '#5B6472'],
        ];
        $days = [max(1, (int) ceil($a / 400)), max(2, (int) ceil($a / 200))];

        return ['items' => $items, 'per_unit' => $a, 'days' => $days, 'meta' => [['Kurulum süresi', $days[0].'–'.$days[1].' iş günü'], ['İskele alanı', number_format($a, 0, ',', '.').' m²']]];
    }

    public function materials(array $in): array
    {
        $a = $this->area($in);
        $L = (float) $in['length'];
        $H = (float) $in['height'];

        return [
            ['İskele alanı', number_format($a, 0, ',', '.'), 'm²', 'Uzunluk × (yükseklik + 1 m)'],
            ['Çalışma katı', (int) ceil($H / 2), 'kat', 'Her 2 m\'de bir platform'],
            ['Platform / döşeme tahtası', (int) ceil($L / 2.5) * (int) ceil($H / 2), 'adet', '2,5 m modül'],
            ['Duvar ankrajı', (int) ceil($a / 12), 'adet', 'Yaklaşık her 12 m²\'de bir'],
            ['Güvenlik filesi', $in['net'] === 'evet' ? (int) ceil($a) : 0, 'm²', ''],
        ];
    }

    public function phases(array $in): array
    {
        return [['Nakliye ve zemin hazırlığı', .2], ['Kurulum', .6], ['Kontrol ve teslim', .2]];
    }
}
