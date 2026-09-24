<?php

namespace App\Services\ConTech;

use App\Services\PriceService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AutoBoqService
{
    public function __construct(
        private readonly PriceService $prices
    ) {}

    /**
     * AI dosyayı analiz eder.
     *
     * AI:
     * - iş kalemi çıkarır
     * - metraj çıkarır
     * - güven seviyesi verir
     *
     * AI fiyat VERMEZ.
     *
     * Fiyat:
     * PriceService -> rayic_prices / unit_prices
     */
    public function analyse(
        UploadedFile $file,
        string $projectType = 'genel',
        string $quality = 'standard'
    ): array {

        // Excel dosyaları için özel handling — Gemini binary Excel'i desteklemiyor
        $mimeType = $file->getMimeType();
        $extension = strtolower($file->getClientOriginalExtension());

        if (in_array($extension, ['xlsx', 'xls']) ||
            in_array($mimeType, [
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'application/vnd.ms-excel',
                'application/octet-stream'
            ])) {
            $text = $this->excelToText($file);
            if (!empty($text)) {
                return $this->analyseText($text, $projectType, $quality);
            }
        }

        $apiKey = config('services.gemini.api_key');

        if (empty($apiKey)) {
            throw new \Exception(
                'Sistem Hatası: GEMINI_API_KEY tanımlı değil.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | 1. DOSYA
        |--------------------------------------------------------------------------
        */

        $mimeType = $file->getMimeType();

        $fileData = base64_encode(
            file_get_contents(
                $file->getRealPath()
            )
        );


        /*
        |--------------------------------------------------------------------------
        | 2. AI PROMPT
        |--------------------------------------------------------------------------
        */

        $prompt = <<<PROMPT
Sen uzman bir inşaat keşif ve metraj mühendisisin.

Ekli dosyayı analiz et.

Proje Tipi:
{$projectType}

AMAÇ:
Dosyada gerçekten görülebilen veya ölçülebilen iş kalemlerini ve miktarlarını çıkarmak.

KRİTİK KURALLAR:

1. ASLA fiyat üretme.
2. ASLA birim fiyat tahmin etme.
3. Dosyada olmayan ölçüyü uydurma.
4. Fotoğrafta ölçek veya ölçü yoksa kesin metraj uydurma.
5. Emin olmadığın iş kaleminde confidence değerini low veya medium yap.
6. Bir iş kaleminin tipi fiyatlandırma için önemliyse adı mümkün olduğunca teknik ve açık yaz.

Örnek:

"Alçıpan bölme duvar C75 suya dayanıklı"

yerine sadece

"Alçıpan"

yazma.

Betonda sınıf görünüyorsa:

"C25/30 beton"

olarak belirt.

Seramikte kullanım yeri görünüyorsa:

"Duvar seramiği"

veya

"Porselen zemin kaplaması"

olarak belirt.

Miktar güvenilir biçimde belirlenemiyorsa quantity alanını null yap.

Birimleri mümkün olduğunca şu formatta kullan:

m2
m3
m
adet
kg
ton

JSON dışında hiçbir açıklama yazma.

JSON FORMAT:

{
  "items": [
    {
      "name": "Alçıpan bölme duvar C75 suya dayanıklı",
      "quantity": 150,
      "unit": "m2",
      "confidence": "high",
      "source_page": 4,
      "measurement_basis": "measured",
      "notes": null
    }
  ]
}

measurement_basis:

measured = dosyadaki ölçüden hesaplandı
stated = dosyada miktar açıkça yazıyor
inferred = kısmen yorumlandı
unknown = belirlenemedi
PROMPT;


        /*
        |--------------------------------------------------------------------------
        | 3. GEMINI
        |--------------------------------------------------------------------------
        */

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'x-goog-api-key' => $apiKey
        ])
        ->timeout(30)
        ->retry(3, 1500, throw: false)
        ->post(
            'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent',
            [
                'contents' => [
                    [
                        'parts' => [
                            [
                                'text' => $prompt
                            ],
                            [
                                'inlineData' => [
                                    'mimeType' => $mimeType,
                                    'data' => $fileData
                                ]
                            ]
                        ]
                    ]
                ],

                'generationConfig' => [
                    'responseMimeType' => 'application/json'
                ]
            ]
        );


        if (!$response->successful()) {

            Log::error(
                'Gemini API Hatası: ' .
                $response->body()
            );

            throw new \Exception(
                'Yapay Zeka dosyayı okurken bir hata oluştu.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | 4. AI JSON
        |--------------------------------------------------------------------------
        */

        $result = $response->json(
            'candidates.0.content.parts.0.text'
        );


        $parsedData = json_decode(
            $result,
            true
        );


        if (
            json_last_error() !== JSON_ERROR_NONE ||
            !isset($parsedData['items']) ||
            !is_array($parsedData['items'])
        ) {

            throw new \Exception(
                'AI dosyadan anlamlı bir metraj verisi çıkaramadı.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | 5. WORKSITEM PRICE ENGINE
        |--------------------------------------------------------------------------
        */

        $boqItems = [];

        $verifiedTotal = 0.0;

        $pricedItemCount = 0;

        $missingPriceCount = 0;


        $labels = [
            'high' => 'Yüksek',
            'medium' => 'Orta',
            'low' => 'Düşük (Kontrol Gerekir)'
        ];


        foreach ($parsedData['items'] as $item) {

            $name = trim(
                (string) (
                    $item['name']
                    ?? 'Bilinmeyen Kalem'
                )
            );


            $qty =
                isset($item['quantity']) &&
                is_numeric($item['quantity'])
                ? (float) $item['quantity']
                : 0.0;


            $unit = trim(
                (string) (
                    $item['unit']
                    ?? ''
                )
            );


            $confidence = strtolower(
                trim(
                    (string) (
                        $item['confidence']
                        ?? 'low'
                    )
                )
            );


            if (
                !array_key_exists(
                    $confidence,
                    $labels
                )
            ) {
                $confidence = 'low';
            }


            /*
             * Burada:
             *
             * AI -> yalnız iş kalemi
             * PriceService -> fiyat
             */

            $price = $this->resolvePriceForItem(
                $name,
                $unit,
                $quality
            );


            $lineTotal = null;

            $pricedQuantity = null;


            if (
                $qty > 0 &&
                ($price['found'] ?? false) === true &&
                isset($price['unit_price']) &&
                is_numeric($price['unit_price'])
            ) {

                $factor = (float) (
                    $price['quantity_factor']
                    ?? 1
                );


                $pricedQuantity =
                    $qty *
                    $factor;


                $lineTotal =
                    $pricedQuantity *
                    (float) $price['unit_price'];


                $verifiedTotal +=
                    $lineTotal;


                $pricedItemCount++;

            } else {

                $missingPriceCount++;
            }


            /*
             * Miktar yoksa fiyat bulunsa bile
             * satır toplamı üretme.
             */

            if ($qty <= 0) {

                $priceMessage =
                    'Dosyadan güvenilir miktar çıkarılamadı.';

            } else {

                $priceMessage =
                    $price['message']
                    ?? null;
            }


            $boqItems[] = [

                'name' => $name,

                'quantity' => $qty > 0
                    ? $qty
                    : null,

                'unit' => $unit,

                'unit_normalized' =>
                    $this->normalizeUnit(
                        $unit
                    ),

                'unit_price' =>
                    $lineTotal !== null
                    ? number_format(
                        (float) $price['unit_price'],
                        2,
                        ',',
                        '.'
                    ) .
                    ' TL/' .
                    ($price['price_unit'] ?? '')
                    : 'Doğrulanmış fiyat yok',

                'total' =>
                    $lineTotal !== null
                    ? number_format(
                        $lineTotal,
                        2,
                        ',',
                        '.'
                    ) . ' TL'
                    : 'Hesaplanmadı',

                'raw_unit_price' =>
                    $lineTotal !== null
                    ? (float) $price['unit_price']
                    : null,

                'raw_total' =>
                    $lineTotal,

                'priced_quantity' =>
                    $pricedQuantity,

                'price_found' =>
                    $lineTotal !== null,

                'price_source' =>
                    $price['source']
                    ?? null,

                'price_reference' =>
                    $price['reference']
                    ?? null,

                'price_message' =>
                    $priceMessage,

                'confidence' =>
                    $confidence,

                'confidence_label' =>
                    $labels[$confidence],

                'source_page' =>
                    $item['source_page']
                    ?? null,

                'measurement_basis' =>
                    $item['measurement_basis']
                    ?? 'unknown',

                'notes' =>
                    $item['notes']
                    ?? null,
            ];
        }


        /*
        |--------------------------------------------------------------------------
        | 6. GENEL SONUÇ
        |--------------------------------------------------------------------------
        |
        | Eski sistemdeki:
        |
        | %90 - %115
        |
        | yapay fiyat aralığı kaldırıldı.
        |
        | Sadece doğrulanmış kalemlerin gerçek subtotalı verilir.
        |
        */

        $totalFormatted =
            number_format(
                $verifiedTotal,
                2,
                ',',
                '.'
            ) . ' TL';


        if ($missingPriceCount > 0) {

            $totalFormatted .=
                ' + ' .
                $missingPriceCount .
                ' eksik fiyatlı kalem';
        }


        return [

            'success' => true,

            'total_formatted' =>
                $totalFormatted,

            'item_count' =>
                count($boqItems) .
                ' İş Kalemi',

            'priced_item_count' =>
                $pricedItemCount,

            'missing_price_count' =>
                $missingPriceCount,

            'complete_pricing' =>
                $missingPriceCount === 0,

            'confidence_label' =>
                $missingPriceCount === 0
                ? 'AI Ön Keşif · Doğrulanmış Fiyatlar'
                : 'AI Ön Keşif · Eksik Fiyat Kalemleri Var',

            'pricing_note' =>
                'AI fiyat üretmez. Fiyatlar yalnız PriceService üzerinden doğrulanmış rayiç/birim fiyat kayıtlarından alınır.',

            'quality' =>
                $quality,

            'items' =>
                $boqItems,

            /*
             * Eski frontend uyumluluğu için
             * raw_min/raw_max korunuyor.
             *
             * Ancak artık yapay yüzde uygulanmıyor.
             */

            'raw_total' =>
                $verifiedTotal,

            'raw_min' =>
                $verifiedTotal,

            'raw_max' =>
                $verifiedTotal,
        ];
    }


    /**
     * Bir AI iş kalemini doğrulanmış
     * Worksitem fiyat kaynağına bağlar.
     *
     * Fallback fiyat YOK.
     */
    /**
     * Excel dosyasını CSV benzeri text'e çevirir.
     */
    private function excelToText(UploadedFile $file): string
    {
        try {
            if (!class_exists(\PhpOffice\PhpSpreadsheet\IOFactory::class)) {
                Log::warning('PhpSpreadsheet yüklü değil, Excel okunamıyor');
                return '';
            }

            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getRealPath());
            $lines = [];

            $maxRows = 500; // MAX 500 satır (büyük Excel'lerde timeout önlenir)
            $rowCount = 0;

            foreach ($spreadsheet->getAllSheets() as $sheet) {
                $lines[] = '# Sheet: ' . $sheet->getTitle();

                foreach ($sheet->getRowIterator() as $row) {
                    $rowCount++;
                    if ($rowCount > $maxRows) {
                        $lines[] = '# ... (ilk ' . $maxRows . ' satır gösteriliyor, toplam daha fazla)';
                        break 2;
                    }
                    $cellIterator = $row->getCellIterator();
                    $cellIterator->setIterateOnlyExistingCells(false);

                    $cells = [];
                    foreach ($cellIterator as $cell) {
                        $value = $cell->getValue();
                        if ($value !== null && $value !== '') {
                            $cells[] = trim((string) $value);
                        }
                    }

                    if (!empty($cells)) {
                        $lines[] = implode(' | ', $cells);
                    }
                }
            }

            return implode("\n", $lines);
        } catch (\Throwable $e) {
            Log::error('Excel okuma hatası: ' . $e->getMessage());
            return '';
        }
    }
    /**
     * Metin girdisini analiz eder (dosya yerine).
     * Aynı AI + PriceService zincirini kullanır.
     */
    public function analyseText(
        string $text,
        string $projectType = 'genel',
        string $quality = 'standard'
    ): array {
        $apiKey = config('services.gemini.api_key');

        if (empty($apiKey)) {
            throw new \Exception('Sistem Hatası: GEMINI_API_KEY tanımlı değil.');
        }

        $text = trim($text);
        if ($text === '') {
            throw new \Exception('Metin boş olamaz.');
        }

        $prompt = <<<PROMPT
Sen uzman bir inşaat keşif ve metraj mühendisisin.

Aşağıdaki metraj listesini analiz et.

Proje Tipi:
{$projectType}

METRAJ LİSTESİ:
{$text}

AMAÇ:
Metraj listesindeki iş kalemlerini ve miktarlarını çıkar.

KRİTİK KURALLAR:

1. ASLA fiyat üretme.
2. ASLA birim fiyat tahmin etme.
3. Listede olmayan ölçüyü uydurma.
4. Emin olmadığın iş kaleminde confidence değerini low veya medium yap.
5. Bir iş kaleminin tipi fiyatlandırma için önemliyse adı mümkün olduğunca teknik ve açık yaz.
6. Her satır için iş kalemi adı ve miktar (varsa) çıkar.

Birimleri şu formatta kullan: m2, m3, m, adet, kg, ton

JSON FORMAT:

{
  "items": [
    {
      "name": "Ahşap Kalıp Yapılması",
      "quantity": 150,
      "unit": "m2",
      "confidence": "high",
      "source_page": null,
      "measurement_basis": "stated",
      "notes": null
    }
  ]
}

JSON dışında hiçbir açıklama yazma.
PROMPT;

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'x-goog-api-key' => $apiKey
        ])
        ->timeout(30)
        ->retry(3, 1500, throw: false)
        ->post(
            'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent',
            [
                'contents' => [
                    ['parts' => [['text' => $prompt]]]
                ],
                'generationConfig' => [
                    'responseMimeType' => 'application/json'
                ]
            ]
        );

        if (!$response->successful()) {
            Log::error('Gemini API Hatası (Text): ' . $response->body());
            throw new \Exception('Yapay Zeka metni okurken bir hata oluştu.');
        }

        $result = $response->json('candidates.0.content.parts.0.text');
        $parsedData = json_decode($result, true);

        if (json_last_error() !== JSON_ERROR_NONE || !isset($parsedData['items']) || !is_array($parsedData['items'])) {
            throw new \Exception('AI metinden anlamlı bir metraj verisi çıkaramadı.');
        }

        // MAX 100 kalem (response boyutu kontrolü)
        $items = array_slice($parsedData['items'], 0, 100);

        return $this->buildBoqResponse($items, $quality);
    }

    /**
     * Dosya ve text için ortak fiyatlandırma + response builder.
     */
    private function buildBoqResponse(array $rawItems, string $quality): array
    {
        $boqItems = [];
        $verifiedTotal = 0.0;
        $pricedItemCount = 0;
        $missingPriceCount = 0;

        $labels = [
            'high' => 'Yüksek',
            'medium' => 'Orta',
            'low' => 'Düşük (Kontrol Gerekir)'
        ];

        foreach ($rawItems as $item) {
            $name = trim((string) ($item['name'] ?? 'Bilinmeyen Kalem'));
            $qty = isset($item['quantity']) && is_numeric($item['quantity']) ? (float) $item['quantity'] : 0.0;
            $unit = trim((string) ($item['unit'] ?? ''));
            $confidence = strtolower(trim((string) ($item['confidence'] ?? 'low')));
            if (!array_key_exists($confidence, $labels)) $confidence = 'low';

            $price = $this->resolvePriceForItem($name, $unit, $quality);

            $lineTotal = null;
            $pricedQuantity = null;

            if ($qty > 0 && ($price['found'] ?? false) === true && isset($price['unit_price']) && is_numeric($price['unit_price'])) {
                $factor = (float) ($price['quantity_factor'] ?? 1);
                $pricedQuantity = $qty * $factor;
                $lineTotal = $pricedQuantity * (float) $price['unit_price'];
                $verifiedTotal += $lineTotal;
                $pricedItemCount++;
            } else {
                $missingPriceCount++;
            }

            $priceMessage = $qty <= 0 ? 'Dosyadan güvenilir miktar çıkarılamadı.' : ($price['message'] ?? null);
            $priceFound = ($price['found'] ?? false) === true && isset($price['unit_price']) && is_numeric($price['unit_price']);

            $boqItems[] = [
                'name' => $name,
                'quantity' => $qty > 0 ? $qty : null,
                'unit' => $unit,
                'unit_normalized' => $this->normalizeUnit($unit),
                'unit_price' => $priceFound
                    ? number_format((float) $price['unit_price'], 2, ',', '.') . ' TL/' . ($price['price_unit'] ?? '')
                    : 'Doğrulanmış fiyat yok',
                'total' => $lineTotal !== null
                    ? number_format($lineTotal, 2, ',', '.') . ' TL'
                    : ($priceFound ? 'Miktar girin' : 'Hesaplanmadı'),
                'raw_unit_price' => $lineTotal !== null ? (float) $price['unit_price'] : null,
                'raw_total' => $lineTotal,
                'priced_quantity' => $pricedQuantity,
                'price_found' => $lineTotal !== null,
                'price_source' => $price['source'] ?? null,
                'price_reference' => $price['reference'] ?? null,
                'price_message' => $priceMessage,
                'confidence' => $confidence,
                'confidence_label' => $labels[$confidence],
                'source_page' => $item['source_page'] ?? null,
                'measurement_basis' => $item['measurement_basis'] ?? 'unknown',
                'notes' => $item['notes'] ?? null,
            ];
        }

        return [
            'items' => $boqItems,
            'verified_total' => round($verifiedTotal, 2),
            'raw_total' => round($verifiedTotal, 2),
            'priced_item_count' => $pricedItemCount,
            'missing_price_count' => $missingPriceCount,
            'total_item_count' => count($boqItems),
            'success' => true,
        ];
    }
    /**
     * tender_pozs (43.290 resmi poz) tablosunda fuzzy arama yapar.
     *
     * Algoritma:
     * - Kelime kesişimi + Türkçe kök eşleştirme
     * - Skor = (eşleşen kelime ağırlığı) / (DB kelime sayısı)
     * - Birim uyumu bonus
     * - En yüksek skoru döner (>= 0.35)
     */
    private function searchTenderPoz(string $name, string $unit = ''): ?array
    {
        $query = mb_strtolower(trim($name));
        if (mb_strlen($query) < 4) {
            return null;
        }

        $cacheKey = 'tender_v4_' . md5($name . '|' . $unit);
        $cached = \Cache::get($cacheKey);
        if ($cached !== null) {
            return $cached === '__NOT_FOUND__' ? null : $cached;
        }

        $stopwords = [
            've', 'ile', 'veya', 'her', 'turlu', 'herturlu', 'icin', 'yapilmasi', 'yapılması',
            'edilmesi', 'temini', 'montaji', 'montajı', 'sokulmesi', 'sökülmesi',
            'olan', 'tip', 'imalat', 'imalati', 'imalatı', 'ise', 'gore', 'göre', 'uzere', 'üzere',
            'cesit', 'çeşit', 'uygulama', 'uygulamasi', 'uygulaması', 'dahil', 'hariç', 'haric',
        ];

        $userWords = array_values(array_filter(
            preg_split('/\s+/', $this->normalizeText($query)),
            fn ($w) => mb_strlen($w) >= 3 && !in_array($w, $stopwords, true)
        ));

        if (empty($userWords)) {
            \Cache::put($cacheKey, '__NOT_FOUND__', now()->addMinutes(10));
            return null;
        }

        $userStems = array_values(array_unique(array_map([$this, 'turkishStem'], $userWords)));

        $candidates = \DB::table('tender_pozs')
            ->where('is_active', true)
            ->where(function ($q) use ($userWords) {
                foreach (array_slice($userWords, 0, 3) as $w) {
                    $q->orWhere('description', 'like', '%' . $w . '%');
                }
            })
            ->limit(200)
            ->get(['id', 'poz_no', 'description', 'unit', 'unit_price', 'institution']);

        if ($candidates->isEmpty()) {
            \Cache::put($cacheKey, '__NOT_FOUND__', now()->addMinutes(10));
            return null;
        }

        $negativeRaw = ['sök', 'yik', 'yık', 'atil', 'atıl', 'kesil', 'kaldir', 'kaldır', 'tahliye'];
        $negative = array_map(fn ($w) => $this->normalizeText($w), $negativeRaw);
        $userDesc = $this->normalizeText($name);
        $userHasNegative = false;
        foreach ($negative as $neg) {
            if (str_contains($userDesc, $neg)) {
                $userHasNegative = true;
                break;
            }
        }

        $best = null;
        $bestScore = 0.0;

        foreach ($candidates as $poz) {
            $pozUnit = $this->normalizeUnit($poz->unit ?? '');

            // Birim uyumu
            if ($unit !== '' && !$this->unitsCompatible($unit, $pozUnit)) {
                continue;
            }

            $dbDesc = $this->normalizeText($poz->description ?? '');

            // Negatif
            if (!$userHasNegative) {
                $isNegative = false;
                foreach ($negative as $neg) {
                    if (str_contains($dbDesc, $neg)) {
                        $isNegative = true;
                        break;
                    }
                }
                if ($isNegative) {
                    continue;
                }
            }

            $dbWords = array_values(array_filter(
                preg_split('/\s+/', $dbDesc),
                fn ($w) => mb_strlen($w) >= 3 && !in_array($w, $stopwords, true)
            ));

            if (empty($dbWords)) {
                continue;
            }

            $dbStems = array_values(array_unique(array_map([$this, 'turkishStem'], $dbWords)));

            // JACCARD
            $intersection = array_intersect($userStems, $dbStems);
            $union = array_unique(array_merge($userStems, $dbStems));
            $score = count($union) > 0 ? count($intersection) / count($union) : 0;

            if ($unit !== '' && $pozUnit === $unit) {
                $score += 0.05;
            }

            if (mb_strlen($poz->description ?? '') < 60) {
                $score += 0.05;
            }

            $score = min(1.0, $score);

            if ($score > $bestScore) {
                $bestScore = $score;
                $best = $poz;
            }
        }

        if ($best === null || $bestScore < 0.25) {
            \Cache::put($cacheKey, '__NOT_FOUND__', now()->addMinutes(10));
            return null;
        }

        $pozUnit = $this->normalizeUnit($best->unit ?? '');
        $unitPrice = (float) $best->unit_price;
        $priceUnit = $best->unit ?? '';

        if ($unit === 'kg' && $pozUnit === 'ton') {
            $unitPrice = $unitPrice / 1000;
            $priceUnit = 'kg';
        } elseif ($unit === 'ton' && $pozUnit === 'kg') {
            $unitPrice = $unitPrice * 1000;
            $priceUnit = 'ton';
        }

        $result = [
            'found' => true,
            'unit_price' => $unitPrice,
            'price_unit' => $priceUnit,
            'quantity_factor' => 1.0,
            'source' => 'tender_pozs',
            'reference' => $best->poz_no . ' | ' . mb_substr($best->description, 0, 80),
            'message' => null,
            'score' => round($bestScore, 3),
            'poz_no' => $best->poz_no,
            'description' => $best->description,
            'institution' => $best->institution,
        ];

        \Cache::put($cacheKey, $result, now()->addMinutes(10));

        return $result;
    }

    private function unitsCompatible(string $a, string $b): bool
    {
        if ($a === $b) return true;
        $compat = ['kg' => ['ton'], 'ton' => ['kg']];
        return isset($compat[$a]) && in_array($b, $compat[$a], true);
    }

    /**
     * Basit Türkçe kök temizleme.
     */
    private function turkishStem(string $word): string
    {
        $word = $this->normalizeText($word);
        if (mb_strlen($word) < 4) {
            return $word;
        }

        $suffixes = [
            'leri', 'lari', 'ligi', 'lığı', 'lugu', 'lüğü', 'liği',
            'ler', 'lar', 'lik', 'lık', 'luk', 'lük',
            'nin', 'nın', 'nun', 'nün',
            'den', 'dan', 'ten', 'tan',
            'ye', 'ya', 'de', 'da', 'te', 'ta',
            'si', 'sı', 'su', 'sü',
            'in', 'ın', 'un', 'ün',
        ];

        foreach ($suffixes as $suf) {
            if (mb_substr($word, -mb_strlen($suf)) === $suf && mb_strlen($word) - mb_strlen($suf) >= 3) {
                return mb_substr($word, 0, -mb_strlen($suf));
            }
        }

        return $word;
    }

    /**
     * Türkçe karakter normalizasyonu + lowercase.
     */
    private function normalizeText(string $text): string
    {
        $text = mb_strtolower($text, 'UTF-8');
        $text = str_replace(
            ['ğ', 'ü', 'ş', 'ı', 'ö', 'ç', 'â', 'î', 'û'],
            ['g', 'u', 's', 'i', 'o', 'c', 'a', 'i', 'u'],
            $text
        );
        $text = preg_replace('/[^a-z0-9\s]/', ' ', $text);
        $text = preg_replace('/\s+/', ' ', $text);
        return trim($text);
    }
    public function resolvePriceForItem(
        string $name,
        string $unit = '',
        string $quality = 'standard'
    ): array {

        $n = mb_strtolower(
            trim($name)
        );


        $u = $this->normalizeUnit(
            $unit
        );


        $quality = strtolower(
            trim($quality)
        );


        /*
        |--------------------------------------------------------------------------
        | TENDER_POZS (43.290 resmi poz) - ÖNCELİKLİ ARAMA
        |--------------------------------------------------------------------------
        */

        $tenderMatch = $this->searchTenderPoz($name, $u);

        if ($tenderMatch !== null) {
            return $tenderMatch;
        }


        /*
        |--------------------------------------------------------------------------
        | SERAMİK
        |--------------------------------------------------------------------------
        */

        if (
            $this->containsAny(
                $n,
                [
                    'seramik',
                    'fayans',
                    'porselen'
                ]
            )
        ) {

            if ($u !== 'm2') {
                return $this->missing(
                    'Seramik kaleminin birimi m² olmalıdır.'
                );
            }


            if (
                $this->containsAny(
                    $n,
                    [
                        'porselen',
                        'sırlı porselen',
                        'sirli porselen'
                    ]
                )
            ) {

                return $this->fromKey(
                    'seramik_silli_porselen_m2',
                    'm²'
                );
            }


            if (
                $this->containsAny(
                    $n,
                    [
                        'duvar serami',
                        'duvar fayans',
                        'duvar kaplama'
                    ]
                )
            ) {

                return $this->fromKey(
                    'seramik_standart_duvar_m2',
                    'm²'
                );
            }


            return $this->missing(
                'Seramik kullanım yeri veya tipi yeterince açık değil.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | BOYA
        |--------------------------------------------------------------------------
        */

        if (
            str_contains(
                $n,
                'boya'
            )
        ) {

            if ($u !== 'm2') {

                return $this->missing(
                    'Boya kaleminin birimi m² olmalıdır.'
                );
            }


            if (
                str_contains(
                    $n,
                    'ekonomik'
                )
            ) {

                return $this->fromKey(
                    'boya_ekonomik_m2',
                    'm²'
                );
            }


            if (
                str_contains(
                    $n,
                    'premium'
                ) ||
                $quality === 'premium'
            ) {

                return $this->fromKey(
                    'boya_premium_m2',
                    'm²'
                );
            }


            if ($quality === 'luxury') {

                return $this->missing(
                    'Lüks boya için ayrı doğrulanmış fiyat anahtarı bulunamadı.'
                );
            }


            return $this->fromKey(
                'boya_standart_m2',
                'm²'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | ALÇIPAN BÖLME DUVAR
        |--------------------------------------------------------------------------
        */

        if (
            $this->containsAny(
                $n,
                [
                    'alçıpan',
                    'alcipan'
                ]
            )
        ) {

            if ($u !== 'm2') {

                return $this->missing(
                    'Alçıpan kaleminin birimi m² olmalıdır.'
                );
            }


            /*
             * Asma tavanı duvar fiyatıyla
             * KESİNLİKLE fiyatlandırma.
             */

            if (
                $this->containsAny(
                    $n,
                    [
                        'asma tavan',
                        'tavan'
                    ]
                )
            ) {

                return $this->missing(
                    'Alçıpan tavan sistemi, bölme duvar fiyatıyla eşleştirilmedi.'
                );
            }


            if (
                !$this->containsAny(
                    $n,
                    [
                        'bölme',
                        'bolme',
                        'duvar'
                    ]
                )
            ) {

                return $this->missing(
                    'Alçıpan sisteminin bölme duvar mı, giydirme mi olduğu belirtilmedi.'
                );
            }


            $profile = null;


            if (
                $this->containsAny(
                    $n,
                    [
                        'c100',
                        'c 100'
                    ]
                )
            ) {
                $profile = 'c100';
            }
            elseif (
                $this->containsAny(
                    $n,
                    [
                        'c75',
                        'c 75'
                    ]
                )
            ) {
                $profile = 'c75';
            }
            elseif (
                $this->containsAny(
                    $n,
                    [
                        'c50',
                        'c 50'
                    ]
                )
            ) {
                $profile = 'c50';
            }


            if ($profile === null) {

                return $this->missing(
                    'Alçıpan bölme duvar profil genişliği C50/C75/C100 belirtilmedi.'
                );
            }


            $water =
                $this->containsAny(
                    $n,
                    [
                        'suya dayan',
                        'su dayan',
                        'neme dayan',
                        'nem dayan'
                    ]
                );


            $fire =
                $this->containsAny(
                    $n,
                    [
                        'yangına dayan',
                        'yangina dayan',
                        'yangın dayan',
                        'yangin dayan'
                    ]
                );


            $variant =
                $water && $fire
                ? 'water_fire'
                : (
                    $water
                    ? 'water'
                    : (
                        $fire
                        ? 'fire'
                        : 'standard'
                    )
                );


            $pozMap = [

                'c50' => [
                    'standard' => '15.530.1251',
                    'water' => '15.530.1252',
                    'fire' => '15.530.1253',
                    'water_fire' => '15.530.1254',
                ],

                'c75' => [
                    'standard' => '15.530.1259',
                    'water' => '15.530.1260',
                    'fire' => '15.530.1261',
                    'water_fire' => '15.530.1262',
                ],

                'c100' => [
                    'standard' => '15.530.1267',
                    'water' => '15.530.1268',
                    'fire' => '15.530.1269',
                    'water_fire' => '15.530.1270',
                ],
            ];


            return $this->fromPoz(
                $pozMap[$profile][$variant],
                'm²'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | BETON
        |--------------------------------------------------------------------------
        */

        if (
            str_contains(
                $n,
                'beton'
            )
        ) {

            if ($u !== 'm3') {

                return $this->missing(
                    'Beton kaleminin birimi m³ olmalıdır.'
                );
            }


            if (
                $this->containsAny(
                    $n,
                    [
                        'c20/25',
                        'c20-25',
                        'c20 25'
                    ]
                )
            ) {

                return $this->fromKey(
                    'beton_c20_25_m3',
                    'm³'
                );
            }


            if (
                $this->containsAny(
                    $n,
                    [
                        'c25/30',
                        'c25-30',
                        'c25 30'
                    ]
                )
            ) {

                return $this->fromKey(
                    'beton_c25_30_m3',
                    'm³'
                );
            }


            return $this->missing(
                'Beton sınıfı belirtilmedi. C20/25 veya C25/30 gerekli.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | DONATI DEMIRI
        |--------------------------------------------------------------------------
        */

        if (
            $this->containsAny(
                $n,
                [
                    'demir',
                    'donatı',
                    'donati',
                    'nervür',
                    'nervur'
                ]
            )
        ) {

            if ($u === 'ton') {

                return $this->fromKey(
                    'demir_nervurlu_ton',
                    'Ton',
                    1
                );
            }


            if ($u === 'kg') {

                return $this->fromKey(
                    'demir_nervurlu_ton',
                    'Ton',
                    0.001
                );
            }


            return $this->missing(
                'Donatı miktarı kg veya ton olarak verilmelidir.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | LAMINAT PARKE
        |--------------------------------------------------------------------------
        */

        if (
            $this->containsAny(
                $n,
                [
                    'laminat parke',
                    'lamine parke'
                ]
            ) &&
            $u === 'm2'
        ) {

            return $this->fromKey(
                'parke_laminat_ac3_m2',
                'm²'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | KARA SIVA
        |--------------------------------------------------------------------------
        */

        if (
            $this->containsAny(
                $n,
                [
                    'kara sıva',
                    'kara siva'
                ]
            ) &&
            $u === 'm2'
        ) {

            return $this->fromKey(
                'siva_kara_siva_m2',
                'm²'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | MANTOLAMA 5 CM
        |--------------------------------------------------------------------------
        */

        if (
            str_contains(
                $n,
                'mantolama'
            ) &&
            $u === 'm2'
        ) {

            if (
                str_contains(
                    $n,
                    'eps'
                ) &&
                $this->containsAny(
                    $n,
                    [
                        '5 cm',
                        '5cm'
                    ]
                )
            ) {

                return $this->fromKey(
                    'mantolama_eps_5cm_m2',
                    'm²'
                );
            }


            if (
                $this->containsAny(
                    $n,
                    [
                        'taşyünü',
                        'taş yünü',
                        'tasyunu',
                        'tas yunu'
                    ]
                ) &&
                $this->containsAny(
                    $n,
                    [
                        '5 cm',
                        '5cm'
                    ]
                )
            ) {

                return $this->fromKey(
                    'mantolama_tasyunu_5cm_m2',
                    'm²'
                );
            }


            return $this->missing(
                'Mantolama malzemesi ve kalınlığı doğrulanamadı.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | PPRC BORU
        |--------------------------------------------------------------------------
        */

        if (
            $this->containsAny(
                $n,
                [
                    'pprc',
                    'ppr-c'
                ]
            ) &&
            $u === 'm'
        ) {

            return $this->fromKey(
                'tesisat_pprc_boru_m',
                'm'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | KALIP
        |--------------------------------------------------------------------------
        */

        if (
            str_contains(
                $n,
                'kalıp'
            ) ||
            str_contains(
                $n,
                'kalip'
            )
        ) {

            if ($u !== 'm2') {

                return $this->missing(
                    'Kalıp kaleminin birimi m² olmalıdır.'
                );
            }


            if (
                $this->containsAny(
                    $n,
                    [
                        'plywood',
                        'kontrplak'
                    ]
                )
            ) {

                return $this->fromKey(
                    'kalip_plywood_m2',
                    'm²'
                );
            }


            if (
                $this->containsAny(
                    $n,
                    [
                        'ahşap',
                        'ahsap'
                    ]
                )
            ) {

                return $this->fromKey(
                    'kalip_ahsap_m2',
                    'm²'
                );
            }


            return $this->missing(
                'Kalıp tipi belirtilmedi.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | KAPI
        |--------------------------------------------------------------------------
        |
        | unit_prices içinde doğrulanmış iç kapı fiyatı
        | şu an yok.
        |
        */

        if (
            $this->containsAny(
                $n,
                [
                    'kapı',
                    'kapi'
                ]
            )
        ) {

            return $this->missing(
                'Kapı için doğrulanmış birim fiyat bulunamadı.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | BILINMEYEN
        |--------------------------------------------------------------------------
        */

        return $this->missing(
            'Bu iş kalemi için doğrulanmış fiyat eşlemesi bulunamadı.'
        );
    }


    private function fromKey(
        string $key,
        string $priceUnit,
        float $quantityFactor = 1
    ): array {

        $r = $this->prices->byKey(
            $key
        );


        if (
            !$r ||
            !isset($r['price']) ||
            !is_numeric($r['price'])
        ) {

            return $this->missing(
                'Doğrulanmış unit_prices kaydı bulunamadı.',
                $key
            );
        }


        return [

            'found' => true,

            'unit_price' =>
                (float) $r['price'],

            'price_unit' =>
                $priceUnit,

            'quantity_factor' =>
                $quantityFactor,

            'source' =>
                'unit_prices',

            'reference' =>
                $key,

            'message' =>
                null,
        ];
    }


    private function fromPoz(
        string $poz,
        string $priceUnit,
        float $quantityFactor = 1
    ): array {

        $r = $this->prices->byPoz(
            $poz,
            '2026-08',
            'YFK'
        );


        if (
            !$r ||
            !isset($r['price']) ||
            !is_numeric($r['price'])
        ) {

            return $this->missing(
                'Doğrulanmış YFK pozu bulunamadı.',
                $poz
            );
        }


        return [

            'found' => true,

            'unit_price' =>
                (float) $r['price'],

            'price_unit' =>
                $priceUnit,

            'quantity_factor' =>
                $quantityFactor,

            'source' =>
                'rayic_prices',

            'reference' =>
                $poz,

            'message' =>
                null,
        ];
    }


    private function missing(
        string $message,
        ?string $reference = null
    ): array {

        return [

            'found' => false,

            'unit_price' => null,

            'price_unit' => null,

            'quantity_factor' => 1,

            'source' => null,

            'reference' => $reference,

            'message' => $message,
        ];
    }


    private function normalizeUnit(
        string $unit
    ): string {

        $u = mb_strtolower(
            trim($unit)
        );


        $u = str_replace(
            [
                '²',
                '³',
                '.',
                ' '
            ],
            [
                '2',
                '3',
                '',
                ''
            ],
            $u
        );


        return match ($u) {

            'm2',
            'metrekare' =>
                'm2',

            'm3',
            'metrekup',
            'metreküp' =>
                'm3',

            'm',
            'metre' =>
                'm',

            'adet',
            'ad',
            'pcs',
            'piece' =>
                'adet',

            'ton',
            'tonne',
            't' =>
                'ton',

            'kg',
            'kilogram' =>
                'kg',

            default =>
                $u,
        };
    }


    private function containsAny(
        string $haystack,
        array $needles
    ): bool {

        foreach ($needles as $needle) {

            if (
                str_contains(
                    $haystack,
                    $needle
                )
            ) {

                return true;
            }
        }


        return false;
    }
}
