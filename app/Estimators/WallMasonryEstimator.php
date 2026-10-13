<?php

namespace App\Estimators;

/** Duvar örgü: aynı hesap, metin ve vurgu "kaç adet blok / tuğla" üzerine */
class WallMasonryEstimator extends WallEstimator
{
    public function key(): string { return 'duvar_orgu'; }

    public function slug(): string { return 'duvar-orgu'; }

    public function page(): array
    {
        $y = now()->year;
        $page = parent::page();

        return array_merge($page, [
            'title' => 'Duvar örgü hesabı',
            'h1' => "Duvar örgü hesabı: kaç tuğla, kaç gazbeton? ($y)",
            'lede' => 'Duvar ölçülerini girin, malzemeyi seçin; kaç adet tuğla veya gazbeton, ne kadar harç gerektiği ve örgü maliyeti kalem kalem hesaplansın.',
            'seo_title' => "Duvar Örgü Hesabı $y: m²'ye Kaç Tuğla, Kaç Gazbeton? | Worksitem",
            'seo_desc' => "$y duvar örgü hesabı: duvar m²'si için gereken tuğla, gazbeton veya bims adedini, harç miktarını ve örgü maliyetini ücretsiz hesaplayın.",
            'faq' => array_merge([$page['faq'][1]], [$page['faq'][0]], array_slice($page['faq'], 2)),
            'related' => [['/araclar/duvar-maliyet-hesaplayici', 'Duvar maliyeti'], ['/araclar/kara-siva-hesaplama', 'Kara sıva'], ['/araclar/cimento-kum-hesaplama', 'Çimento ve kum'], ['/araclar/bahce-duvari-maliyeti', 'Bahçe duvarı']],
        ]);
    }

    protected function compute(array $in): array
    {
        $r = parent::compute($in);
        [, $per] = $this->p()['materials'][$in['material']];
        $r['meta'][] = ['Blok / tuğla adedi', number_format(ceil($this->net($in) * $per * 1.05), 0, ',', '.').' adet'];

        return $r;
    }
}
