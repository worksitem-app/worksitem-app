<?php

namespace App\Http\Controllers;

use App\Models\TenderPoz;
use App\Support\PozCatalog;
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

    public function show(Request $request, string $pozNo)
    {
        // Aynı poz no birden çok kurumda olabilir (v113): ?kurum= ile seçilir, yoksa öncelikli kurum.
        // Kanonik adres her zaman /poz/{no}; kurum varyantları robots.txt'de (?kurum=) taranmaz.
        $variants = PozCatalog::variants($pozNo);
        abort_if($variants->isEmpty(), 404);
        $wanted = (string) $request->query('kurum', '');
        $poz = ($wanted !== '' ? $variants->first(fn ($v) => PozCatalog::institutionSlug((string) $v->institution) === $wanted || $v->institution === $wanted) : null)
            ?? $variants->first();
        $otherInstitutions = $variants->reject(fn ($v) => $v->is($poz))->values();

        // İlgili pozlar: aynı kurum ve aynı grup (ör. 15.150.*), bu pozun hemen öncesi ve sonrası
        $group = PozCatalog::groupOf((string) $poz->poz_no);
        $base = TenderPoz::query()->where('is_active', true)->where('institution', $poz->institution)
            ->where('poz_no', 'like', str_replace(['%', '_'], ['\\%', '\\_'], $group).'.%')
            ->where('poz_no', '!=', $poz->poz_no)->select('poz_no', 'description', 'unit', 'unit_price');
        $before = (clone $base)->where('poz_no', '<', $poz->poz_no)->orderByDesc('poz_no')->limit(5)->get()->reverse();
        $after = (clone $base)->where('poz_no', '>', $poz->poz_no)->orderBy('poz_no')->limit(10 - $before->count())->get();
        $related = $before->concat($after)->unique('poz_no')->values();

        $sponsorCategories = PozCatalog::sponsorCategories((string) $poz->description);
        $groupUrl = PozCatalog::groupUrl((string) $poz->institution, $group);
        $institutionUrl = PozCatalog::institutionUrl((string) $poz->institution);

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
                ['@type' => 'ListItem', 'position' => 3, 'name' => (string) $poz->institution, 'item' => url($institutionUrl)],
                ['@type' => 'ListItem', 'position' => 4, 'name' => $group.' pozları', 'item' => url($groupUrl)],
                ['@type' => 'ListItem', 'position' => 5, 'name' => $poz->poz_no, 'item' => \App\Support\PozUrl::absolute($poz->poz_no)],
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
            'breadcrumbSchema',
            'otherInstitutions',
            'sponsorCategories',
            'group',
            'groupUrl',
            'institutionUrl'
        ));
    }

    /** /pozlar/{kurum}: kurumun poz grupları (taranabilir HTML bağlantılar) */
    public function institution(string $kurum)
    {
        $inst = PozCatalog::institutions()[$kurum] ?? null;
        abort_if($inst === null, 404);
        $groups = PozCatalog::groups($inst);
        $total = array_sum(array_column($groups, 'count'));

        return view('poz.institution', [
            'inst' => $inst, 'slug' => $kurum, 'groups' => $groups, 'total' => $total,
            'institutions' => PozCatalog::institutions(),
        ]);
    }

    /** /pozlar/{kurum}/{grup}: gruptaki pozlar, birim fiyatlarıyla (sayfa başına 100) */
    public function group(string $kurum, string $grup)
    {
        $inst = PozCatalog::institutions()[$kurum] ?? null;
        abort_if($inst === null, 404);
        $g = PozCatalog::groups($inst)[$grup] ?? null;
        abort_if($g === null, 404);

        $pozs = TenderPoz::query()->where('is_active', true)->where('institution', $inst)
            ->where(fn ($q) => $q->where('poz_no', 'like', $g['group'].'.%')->orWhere('poz_no', $g['group']))
            ->select('poz_no', 'description', 'unit', 'unit_price', 'year')
            ->orderBy('poz_no')->get()
            ->filter(fn ($p) => PozCatalog::groupOf((string) $p->poz_no) === $g['group'])
            ->unique('poz_no')->values();
        $page = max(1, (int) request()->query('sayfa', 1));
        $per = 100;
        $pages = max(1, (int) ceil($pozs->count() / $per));
        abort_if($page > $pages, 404);

        return view('poz.group', [
            'inst' => $inst, 'kurum' => $kurum, 'g' => $g,
            'pozs' => $pozs->slice(($page - 1) * $per, $per)->values(),
            'page' => $page, 'pages' => $pages, 'count' => $pozs->count(),
            'year' => $pozs->max('year'),
        ]);
    }

    /**
     * Canlı poz arama API'si (eski: routes/web.php içinde closure).
     * GET /api/pozlar/search?q=...&institution=...
     */
    public function search(Request $request, \App\Services\TenderPricingService $pricing)
    {
        $validated = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'institution' => ['nullable', 'string', 'max:100'],
        ]);

        $q = trim((string) ($validated['q'] ?? ''));
        $inst = (string) ($validated['institution'] ?? 'all');

        $query = TenderPoz::query()->where('is_active', true);

        if ($inst !== 'all' && $inst !== '') {
            $query->where('institution', $inst);
        }

        if ($q !== '') {
            // LIKE joker karakterlerini kaçır: "%" araması tüm tabloyu taramasın
            $like = '%'.addcslashes($q, '%_\\').'%';
            $query->where(function ($sub) use ($like) {
                $sub->where('poz_no', 'LIKE', $like)
                    ->orWhere('description', 'LIKE', $like);
            });
        }

        $rows = $query->orderBy('poz_no')->limit(40)->get()->map(function ($poz) use ($pricing) {
            $calc = $pricing->calculate($poz, 1);

            return [
                'id' => $poz->id,
                'year' => $poz->year,
                'period' => $poz->period,
                'institution' => $poz->institution,
                'poz_no' => $poz->poz_no,
                'description' => $poz->description,
                'unit' => $poz->unit,
                'official_unit_price' => $calc['official_unit_price'],
                'offer_unit_price' => $calc['offer_unit_price'],
                'quote_status' => $calc['status'],
                'quote_message' => $calc['reason'],
            ];
        });

        return response()->json($rows);
    }
}
