<?php

namespace App\Http\Controllers;

use App\Models\TenderPoz;
use Illuminate\Http\Request;

class PozPublicController extends Controller
{
    public function index(Request $request)
    {
        $institution = $request->get('kurum', 'all');
        $search = trim($request->get('q', ''));

        $query = TenderPoz::query()->where('is_active', true);

        if ($institution !== 'all') {
            $query->where('institution', $institution);
        }

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('poz_no', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%");
            });
        }

        $pozs = $query->orderBy('poz_no')->paginate(50);

        $institutions = TenderPoz::query()
            ->where('is_active', true)
            ->distinct()
            ->pluck('institution')
            ->filter()
            ->sort()
            ->values();

        return view('poz.index', compact('pozs', 'institutions', 'institution', 'search'));
    }

    public function show(string $pozNo)
    {
        $poz = TenderPoz::where('poz_no', $pozNo)
            ->where('is_active', true)
            ->firstOrFail();

        $related = TenderPoz::where('institution', $poz->institution)
            ->where('is_active', true)
            ->where('poz_no', '!=', $poz->poz_no)
            ->orderBy('poz_no')
            ->limit(10)
            ->get();

        // Tüm hesaplamalar controller'da
        $priceFormatted = number_format((float)$poz->unit_price, 2, ',', '.');
        $yearText = $poz->year ? ' — ' . $poz->year . ' yılı' : '';

        $profitText = 'Kaynak veride bu bilgi belirtilmemiştir.';
        if ($poz->profit_status === 'included') {
            $profitText = 'Evet, yayımlanan bu resmi fiyata yüklenici kârı dahildir.';
        } elseif ($poz->profit_status === 'excluded') {
            $profitText = 'Hayır, yayımlanan bu fiyata yüklenici kârı hariçtir.';
        }

        // FAQ items
        $faqItems = is_array($poz->faq_data) ? $poz->faq_data : [];
        $hasFaq = count($faqItems) > 0;

        // Schema JSON üretimi (controller'da)
        $faqSchema = null;
        if ($hasFaq) {
            $faqSchema = [
                '@context' => 'https://schema.org',
                '@type' => 'FAQPage',
                'mainEntity' => array_map(function ($faq) {
                    return [
                        '@type' => 'Question',
                        'name' => $faq['q'] ?? '',
                        'acceptedAnswer' => [
                            '@type' => 'Answer',
                            'text' => $faq['a'] ?? '',
                        ],
                    ];
                }, $faqItems),
            ];
        }

        $productSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'Product',
            'name' => $poz->poz_no . ' — ' . $poz->description,
            'sku' => $poz->poz_no,
            'description' => $poz->meta_description ?? $poz->description,
            'category' => $poz->institution,
            'offers' => [
                '@type' => 'Offer',
                'price' => number_format((float)$poz->unit_price, 2, '.', ''),
                'priceCurrency' => 'TRY',
                'unitText' => $poz->unit,
                'availability' => 'https://schema.org/InStock',
            ],
        ];

        $breadcrumbSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => [
                ['@type' => 'ListItem', 'position' => 1, 'name' => 'Ana Sayfa', 'item' => url('/')],
                ['@type' => 'ListItem', 'position' => 2, 'name' => 'Pozlar', 'item' => url('/pozlar')],
                ['@type' => 'ListItem', 'position' => 3, 'name' => $poz->poz_no, 'item' => url('/poz/' . $poz->poz_no)],
            ],
        ];

        return view('poz.show', compact(
            'poz',
            'related',
            'priceFormatted',
            'yearText',
            'profitText',
            'faqItems',
            'hasFaq',
            'faqSchema',
            'productSchema',
            'breadcrumbSchema'
        ));
    }
}
