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

        $sponsorCategories = PozCatalog::withParents(PozCatalog::categoriesFor($poz));
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
     * İhale modülü kurum filtresi (v119): seçenek değeri => veride geçen kurum etiketleri.
     * Aynı kurum veride farklı adlarla geçiyor (ör. ÇŞB ve "ÇŞİDB/YFK"); "Ulaştırma" altında
     * "Ulaştırma/…" alt kurumları toplanır. Veride bu listede olmayan kurumlar kendi adıyla eklenir.
     */
    private const INSTITUTION_GROUPS = [
        'ÇŞB' => ['label' => 'Çevre, Şehircilik ve İklim Değişikliği Bak. (ÇŞB / YFK)', 'names' => ['ÇŞB', 'ÇŞİDB/YFK', 'ÇŞİDB', 'YFK']],
        'Karayolları' => ['label' => 'Karayolları Genel Müd.', 'names' => ['Karayolları', 'KGM']],
        'İller Bankası' => ['label' => 'İller Bankası (İLBANK)', 'names' => ['İller Bankası', 'İLBANK', 'İLBANK İller Bankası']],
        'DSİ' => ['label' => 'DSİ', 'names' => ['DSİ']],
        'MSB' => ['label' => 'Millî Savunma Bak. (MSB)', 'names' => ['MSB']],
        'Kültür/Vakıflar' => ['label' => 'Kültür / Vakıflar', 'names' => ['Kültür/Vakıflar', 'Vakıflar', 'Kültür']],
        'PTT' => ['label' => 'PTT', 'names' => ['PTT']],
        'Ulaştırma' => ['label' => 'Ulaştırma (DHMİ, Liman vb.)', 'names' => ['Ulaştırma'], 'prefix' => 'Ulaştırma/'],
        'Özel' => ['label' => 'Özel / Özel Pozlar', 'names' => ['Özel']],
    ];

    /** Filtre seçenekleri: sabit gruplar + veride geçen diğer kurumlar. @return array<string,string> değer => etiket */
    public static function searchInstitutionOptions(): array
    {
        $opts = [];
        foreach (self::INSTITUTION_GROUPS as $key => $g) {
            $opts[$key] = $g['label'];
        }
        try {
            $present = TenderPoz::query()->where('is_active', true)->distinct()->pluck('institution')->filter()->all();
        } catch (\Throwable $e) {
            $present = [];
        }
        foreach ($present as $inst) {
            if (self::groupKeyFor((string) $inst) === null) {
                $opts[(string) $inst] = (string) $inst;
            }
        }

        return $opts;
    }

    /** Veride geçen kurum adının ait olduğu filtre grubu (yoksa null) */
    private static function groupKeyFor(string $inst): ?string
    {
        foreach (self::INSTITUTION_GROUPS as $key => $g) {
            if (in_array($inst, $g['names'], true) || (isset($g['prefix']) && str_starts_with($inst, $g['prefix']))) {
                return $key;
            }
        }

        return null;
    }

    /**
     * Canlı poz arama API'si (eski: routes/web.php içinde closure).
     * GET /api/pozlar/search?q=...&institution=...
     *
     * v119: Kâr durumu "unknown" olan pozlarda teklif fiyatı (offer_unit_price) bilinçli olarak null;
     * resmî fiyat (official_unit_price = unit_price ya da malzeme+işçilik) yine de döner. Ön yüz eskiden
     * yanıtta olmayan "unit_price" alanına düşüyordu → NaN. Yanıtta has_official_price / has_offer_price
     * bayrakları var; fiyatlı pozlar öne sıralanır.
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
            $group = self::INSTITUTION_GROUPS[$inst] ?? null;
            if ($group === null) {
                $query->where('institution', $inst);
            } else {
                $query->where(function ($sub) use ($group) {
                    $sub->whereIn('institution', $group['names']);
                    if (isset($group['prefix'])) {
                        $sub->orWhere('institution', 'LIKE', addcslashes($group['prefix'], '%_\\').'%');
                    }
                });
            }
        }

        if ($q !== '') {
            // LIKE joker karakterlerini kaçır: "%" araması tüm tabloyu taramasın
            $like = '%'.addcslashes($q, '%_\\').'%';
            $query->where(function ($sub) use ($like) {
                $sub->where('poz_no', 'LIKE', $like)
                    ->orWhere('description', 'LIKE', $like);
            });
        }

        // Sıralama: önce teklif fiyatı hesaplanabilenler (kâr durumu bilinen + resmî fiyatı olan),
        // sonra yalnız resmî fiyatı olanlar, en sonda fiyatsızlar.
        $hasOfficial = '(COALESCE(unit_price,0) > 0 OR COALESCE(material_price,0) + COALESCE(installation_price,0) > 0)';
        $hasOffer = "(profit_status = 'excluded' OR (profit_status = 'included' AND COALESCE(included_profit_rate,0) > 0))";
        $query->orderByRaw("CASE WHEN {$hasOfficial} AND {$hasOffer} THEN 0 WHEN {$hasOfficial} THEN 1 ELSE 2 END");

        $rows = $query->orderBy('poz_no')->limit(40)->get()->map(function ($poz) use ($pricing) {
            $calc = $pricing->calculate($poz, 1);
            $official = $calc['official_unit_price'];
            $hasOfficialPrice = is_numeric($official) && (float) $official > 0;
            $offer = $hasOfficialPrice ? $calc['offer_unit_price'] : null;

            return [
                'id' => $poz->id,
                'year' => $poz->year,
                'period' => $poz->period,
                'institution' => $poz->institution,
                'poz_no' => $poz->poz_no,
                'description' => $poz->description,
                'unit' => $poz->unit,
                'official_unit_price' => $hasOfficialPrice ? (float) $official : null,
                'offer_unit_price' => $offer,
                'target_margin' => $calc['target_margin'],
                'has_official_price' => $hasOfficialPrice,
                'has_offer_price' => $offer !== null,
                'vat_included' => (bool) $calc['vat_included'],
                'quote_status' => $hasOfficialPrice ? $calc['status'] : 'no_price',
                'quote_message' => $hasOfficialPrice ? $calc['reason'] : 'Bu poz için kayıtlı fiyat yok.',
            ];
        });

        return response()->json($rows);
    }
}
