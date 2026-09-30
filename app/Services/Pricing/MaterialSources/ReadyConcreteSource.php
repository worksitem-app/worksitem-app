<?php

namespace App\Services\Pricing\MaterialSources;

use Carbon\Carbon;
use DOMDocument;
use DOMXPath;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class ReadyConcreteSource implements MaterialPriceSource
{
    private const URL =
        'https://www.santiyeilan.com/2026/09/08/'.
        'guncel-hazir-beton-fiyatlari-ve-beton-hacim-maliyet-hesaplama-rehberi-eylul-2026/';

    public function key(): string
    {
        return 'ready_concrete_istanbul';
    }

    public function fetch(): array
    {
        $response = Http::timeout(30)
            ->withHeaders([
                'User-Agent' =>
                    'Mozilla/5.0 WORKSITEM-PriceResearch/1.0',
                'Accept-Language' =>
                    'tr-TR,tr;q=0.9',
            ])
            ->get(self::URL);

        if (! $response->successful()) {
            throw new RuntimeException(
                'Hazır beton kaynağı HTTP hatası: '.
                $response->status()
            );
        }

        libxml_use_internal_errors(true);

        $dom = new DOMDocument();

        $loaded = $dom->loadHTML(
            '<?xml encoding="utf-8" ?>'.
            $response->body()
        );

        libxml_clear_errors();

        if (! $loaded) {
            throw new RuntimeException(
                'Hazır beton HTML parse edilemedi.'
            );
        }

        $xpath = new DOMXPath($dom);

        /*
         * JSON-LD ve script metinleri gerçek içerik
         * ile karışmasın.
         */
        foreach (
            $xpath->query(
                '//script|//style|//noscript|//template'
            ) as $node
        ) {
            $node->parentNode?->removeChild($node);
        }

        $root = null;

        $articles = $xpath->query('//article');

        if ($articles && $articles->length > 0) {
            $root = $articles->item(0);
        }

        if (! $root) {
            $mains = $xpath->query('//main');

            if ($mains && $mains->length > 0) {
                $root = $mains->item(0);
            }
        }

        if (! $root) {
            $root = $dom->documentElement;
        }

        $text = html_entity_decode(
            $root->textContent,
            ENT_QUOTES | ENT_HTML5,
            'UTF-8'
        );

        $text = str_replace(
            ["\xC2\xA0", "\r", "\n", "\t"],
            ' ',
            $text
        );

        $text = preg_replace(
            '/\s+/u',
            ' ',
            $text
        );

        /*
         * Kaynak tarihi
         */
        $months = [
            'ocak' => 1,
            'şubat' => 2,
            'mart' => 3,
            'nisan' => 4,
            'mayıs' => 5,
            'haziran' => 6,
            'temmuz' => 7,
            'ağustos' => 8,
            'eylül' => 9,
            'ekim' => 10,
            'kasım' => 11,
            'aralık' => 12,
        ];

        $sourceDate = null;

        if (
            preg_match(
                '/Son\s*Güncelleme\s*:\s*'.
                '(\d{1,2})\s+'.
                '([[:alpha:]ÇĞİÖŞÜçğıöşü]+)\s+'.
                '(\d{4})/ui',
                $text,
                $dateMatch
            )
        ) {
            $monthName = mb_strtolower(
                $dateMatch[2],
                'UTF-8'
            );

            if (isset($months[$monthName])) {
                $sourceDate = Carbon::create(
                    (int) $dateMatch[3],
                    $months[$monthName],
                    (int) $dateMatch[1],
                    0,
                    0,
                    0,
                    'Europe/Istanbul'
                );
            }
        }

        if (! $sourceDate) {
            throw new RuntimeException(
                'Hazır beton kaynak tarihi bulunamadı.'
            );
        }

        if ($sourceDate->isFuture()) {
            throw new RuntimeException(
                'Hazır beton kaynak tarihi gelecekte.'
            );
        }

        /*
         * Beton aylık/periyodik veri olduğundan
         * demirdeki kadar kısa freshness zorunluluğu yok.
         */
        if (
            $sourceDate->lt(
                now('Europe/Istanbul')
                    ->subDays(45)
                    ->startOfDay()
            )
        ) {
            throw new RuntimeException(
                'Hazır beton kaynağı güncel değil: '.
                $sourceDate->format('Y-m-d')
            );
        }

        $vatIncluded =
            preg_match(
                '/%20\s*KDV\s*dahil/ui',
                $text
            ) === 1;

        $pumpCondition =
            preg_match(
                '/pompalı\s*teslimat/ui',
                $text
            ) === 1;

        if (! $vatIncluded) {
            throw new RuntimeException(
                'Hazır beton KDV koşulu doğrulanamadı.'
            );
        }

        if (! $pumpCondition) {
            throw new RuntimeException(
                'Hazır beton pompa koşulu doğrulanamadı.'
            );
        }

        /*
         * Sıra:
         * C25/30
         * C30/37
         * C35/45
         * C40/50
         */
        $pattern =
            '/İstanbul\s*'.
            '\(?\s*Avrupa\s*\/\s*Anadolu\s*\)?\s*'.
            '([\d\.,]+)\s*TL\s*'.
            '([\d\.,]+)\s*TL\s*'.
            '([\d\.,]+)\s*TL\s*'.
            '([\d\.,]+)\s*TL/ui';

        if (! preg_match($pattern, $text, $prices)) {
            throw new RuntimeException(
                'İstanbul hazır beton fiyatları parse edilemedi.'
            );
        }

        $normalize = static function (
            string $value
        ): float {
            $value = preg_replace(
                '/[^\d]/',
                '',
                $value
            );

            return (float) $value;
        };

        $c30 = $normalize($prices[2]);

        if ($c30 < 1500 || $c30 > 10000) {
            throw new RuntimeException(
                'C30/37 fiyatı sanity check dışı: '.$c30
            );
        }

        return [[
            'key' =>
                'ready_concrete_c30_37_m3',

            'name' =>
                'Hazır Beton C30/37 — İstanbul',

            'category' =>
                'hazir_beton',

            'spec' =>
                'C30/37; İstanbul Avrupa/Anadolu; '.
                '%20 KDV dahil; kaynakta belirtilen '.
                'pompalı teslimat koşulu',

            'price' =>
                $c30,

            'unit' =>
                'TL/m3',

            'region' =>
                'İstanbul',

            'currency' =>
                'TRY',

            'source_name' =>
                'santiyeilan.com',

            'source_url' =>
                self::URL,

            'source_date' =>
                $sourceDate->toDateString(),

            'vat_included' =>
                true,

            /*
             * Tek piyasa kaynağı ve aylık veri.
             * İlk aşamada otomatik onay yok.
             */
            'confidence_score' =>
                75,

            'verification_status' =>
                'needs_review',
        ]];
    }
}
