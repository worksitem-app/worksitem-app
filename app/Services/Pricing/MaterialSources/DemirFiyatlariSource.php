<?php

namespace App\Services\Pricing\MaterialSources;

use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class DemirFiyatlariSource implements MaterialPriceSource
{
    private const URL = 'https://www.demirfiyatlari.com/';

    public function key(): string
    {
        return 'demirfiyatlari';
    }

    public function fetch(): array
    {
        $response = Http::timeout(30)
            ->withHeaders([
                'User-Agent' =>
                    'Mozilla/5.0 WORKSITEM-PriceResearch/1.0',
                'Accept-Language' => 'tr-TR,tr;q=0.9',
            ])
            ->get(self::URL);

        if (! $response->successful()) {
            throw new RuntimeException(
                'Demir kaynağı HTTP hatası: '.
                $response->status()
            );
        }

        $text = html_entity_decode(
            strip_tags($response->body()),
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

        $sourceDate = null;

        if (
            preg_match(
                '/(\d{2}\.\d{2}\.\d{4})\s*Demir\s*Fiyatlar/ui',
                $text,
                $dateMatch
            )
        ) {
            $sourceDate = Carbon::createFromFormat(
                'd.m.Y',
                $dateMatch[1]
            )->startOfDay();
        }

        if (! $sourceDate) {
            throw new RuntimeException(
                'Demir kaynağında tarih bulunamadı.'
            );
        }

        if ($sourceDate->isFuture()) {
            throw new RuntimeException(
                'Demir kaynak tarihi gelecekte.'
            );
        }

        /*
         * Hafta sonu / tatil toleransı.
         */
        if ($sourceDate->lt(now()->subDays(4)->startOfDay())) {
            throw new RuntimeException(
                'Demir kaynak tarihi güncel değil: '.
                $sourceDate->format('Y-m-d')
            );
        }

        $vatIncluded =
            preg_match(
                '/%20\s*KDV\s*Dahil/ui',
                $text
            ) === 1;

        $freightExcluded =
            preg_match(
                '/Nakliye\s*Hariç/ui',
                $text
            ) === 1;

        if (! $vatIncluded || ! $freightExcluded) {
            throw new RuntimeException(
                'Demir fiyat koşulları doğrulanamadı.'
            );
        }

        $pattern =
            '/İstanbul\s*'.
            '([\d\.,]+)\s*TL\s*'.
            '([\d\.,]+)\s*TL\s*'.
            '([\d\.,]+)\s*TL/ui';

        if (! preg_match($pattern, $text, $prices)) {
            throw new RuntimeException(
                'İstanbul demir fiyatları parse edilemedi.'
            );
        }

        $normalize = static function (string $value): float {
            $number = preg_replace(
                '/[^\d]/',
                '',
                $value
            );

            return (float) $number;
        };

        $q8 = $normalize($prices[1]);
        $q10 = $normalize($prices[2]);
        $q12q32 = $normalize($prices[3]);

        foreach ([$q8, $q10, $q12q32] as $price) {
            if ($price < 10000 || $price > 100000) {
                throw new RuntimeException(
                    'Demir fiyatı sanity check dışı: '.$price
                );
            }
        }

        $common = [
            'category' => 'demir',
            'unit' => 'TL/ton',
            'region' => 'İstanbul',
            'currency' => 'TRY',
            'source_name' => 'demirfiyatlari.com',
            'source_url' => self::URL,
            'source_date' => $sourceDate->toDateString(),
            'vat_included' => true,
            'confidence_score' => 85,
            'verification_status' => 'needs_review',
        ];

        return [
            array_merge($common, [
                'key' => 'rebar_q8_istanbul',
                'name' =>
                    'Nervürlü İnşaat Demiri Q8 — İstanbul',
                'spec' =>
                    'Q8 mm; kalite sınıfı kaynakta açıkça belirtilmemiş',
                'price' => $q8,
            ]),

            array_merge($common, [
                'key' => 'rebar_q10_istanbul',
                'name' =>
                    'Nervürlü İnşaat Demiri Q10 — İstanbul',
                'spec' =>
                    'Q10 mm; kalite sınıfı kaynakta açıkça belirtilmemiş',
                'price' => $q10,
            ]),

            array_merge($common, [
                'key' => 'rebar_q12_q32_istanbul',
                'name' =>
                    'Nervürlü İnşaat Demiri Q12–Q32 — İstanbul',
                'spec' =>
                    'Q12–Q32 mm; kalite sınıfı kaynakta açıkça belirtilmemiş',
                'price' => $q12q32,
            ]),
        ];
    }
}
