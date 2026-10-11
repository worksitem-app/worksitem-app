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

        $pozs = $query->orderBy('poz_no')->paginate(50)->withQueryString();

        $institutions = TenderPoz::query()
            ->where('is_active', true)
            ->distinct()
            ->pluck('institution')
            ->filter()
            ->sort()
            ->values();

        $popular = TenderPoz::where('is_active', true)->where('seo_enriched', true)->orderByDesc('unit_price')->limit(5)->get();

        return view('poz.index', compact('pozs', 'institutions', 'institution', 'search', 'popular'));
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

        $popular = TenderPoz::where('is_active', true)->where('seo_enriched', true)->orderByDesc('unit_price')->limit(5)->get();
        $priceFormatted = number_format((float)$poz->unit_price, 2, ',', '.');
        $yearText = $poz->year ? ' — ' . $poz->year . ' yılı' : '';

        $profitText = 'Kaynak veride bu bilgi belirtilmemiştir.';
        if ($poz->profit_status === 'included') {
            $profitText = 'Evet, yayımlanan bu resmi fiyata yüklenici kârı dahildir.';
        } elseif ($poz->profit_status === 'excluded') {
            $profitText = 'Hayır, yayımlanan bu fiyata yüklenici kârı hariçtir.';
        }

        $faqItems = is_array($poz->faq_data) ? $poz->faq_data : [];
        $hasFaq = count($faqItems) > 0;

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

        /*
         * Poz kaydı bir e-ticaret ürünü değildir.
         * View uyumluluğu için değişken adı korunurken semantik yapı
         * WebPage + DefinedTerm olarak modellenir.
         */
        $productSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'WebPage',
            '@id' => \App\Support\PozUrl::absolute($poz->poz_no) . '#webpage',
            'url' => \App\Support\PozUrl::absolute($poz->poz_no),
            'name' => $poz->poz_no . ' — ' . $poz->description,
            'description' => $poz->meta_description ?? $poz->description,
            'inLanguage' => 'tr-TR',
            'mainEntity' => [
                '@type' => 'DefinedTerm',
                '@id' => \App\Support\PozUrl::absolute($poz->poz_no) . '#term',
                'name' => $poz->poz_no . ' — ' . $poz->description,
                'termCode' => $poz->poz_no,
                'description' => $poz->description,
                'inDefinedTermSet' => [
                    '@type' => 'DefinedTermSet',
                    'name' => trim(
                        ($poz->institution ?: 'Poz listesi')
                        . ($poz->year ? ' ' . $poz->year : '')
                    ),
                    'url' => url('/pozlar'),
                ],
            ],
        ];
        $breadcrumbSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => [
                ['@type' => 'ListItem', 'position' => 1, 'name' => 'Ana Sayfa', 'item' => url('/')],
                ['@type' => 'ListItem', 'position' => 2, 'name' => 'Pozlar', 'item' => url('/pozlar')],
                ['@type' => 'ListItem', 'position' => 3, 'name' => $poz->poz_no, 'item' => \App\Support\PozUrl::absolute($poz->poz_no)],
            ],
        ];

        return view('poz.show', compact(
            'poz',
            'related',
            'popular',
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