<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;

class PdfToRayicCsv extends Command
{
    protected $signature = 'rayic:pdf-to-csv
                            {file : PDF dosya adı veya tam yolu}
                            {--period= : Dönem, örn. 2026-08}
                            {--source=YFK : Kaynak}
                            {--model=gemini-3.7-flash : Gemini modeli}
                            {--output= : Çıktı CSV dosyası}
                            {--from=1 : Başlangıç sayfası}
                            {--to= : Bitiş sayfası}
                            {--batch=1 : Her istekte işlenecek sayfa sayısı}
                            {--resume : Checkpoint üzerinden devam et}';

    protected $description = 'PDF rayiç listesini Gemini ile sayfa bazlı analiz ederek CSV oluşturur';

    public function handle(): int
    {
        $apiKey = env('GEMINI_API_KEY');

        if (!$apiKey) {
            $this->error('GEMINI_API_KEY .env içinde bulunamadı.');
            return self::FAILURE;
        }

        $input = $this->argument('file');

        if (!str_starts_with($input, '/')) {
            $input = storage_path('app/rayic/' . $input);
        }

        if (!File::exists($input)) {
            $this->error("PDF bulunamadı: {$input}");
            return self::FAILURE;
        }

        if (strtolower(File::extension($input)) !== 'pdf') {
            $this->error('Dosya PDF olmalıdır.');
            return self::FAILURE;
        }

        $period = trim((string) $this->option('period'));

        if (!preg_match('/^\d{4}-\d{2}$/', $period)) {
            $this->error('--period=YYYY-MM şeklinde verilmelidir.');
            return self::FAILURE;
        }

        $source = trim((string) $this->option('source'));
        $model = trim((string) $this->option('model'));

        $fromPage = max(1, (int) $this->option('from'));
        $toOption = $this->option('to');

        if ($toOption === null || $toOption === '') {
            $this->error('--to verilmelidir. Örn: --from=1 --to=3');
            return self::FAILURE;
        }

        $toPage = (int) $toOption;

        if ($toPage < $fromPage) {
            $this->error('--to, --from değerinden küçük olamaz.');
            return self::FAILURE;
        }

        $batchSize = max(1, (int) $this->option('batch'));

        $output = $this->option('output');

        if (!$output) {
            $base = pathinfo($input, PATHINFO_FILENAME);
            $output = storage_path('app/rayic/' . $base . '.csv');
        } elseif (!str_starts_with($output, '/')) {
            $output = storage_path('app/rayic/' . $output);
        }

        File::ensureDirectoryExists(dirname($output));

        $checkpoint = $output . '.checkpoint.json';

        $this->info('==============================================');
        $this->info('WORKSITEM AI RAYİÇ PDF → CSV');
        $this->info('==============================================');
        $this->line('PDF       : ' . basename($input));
        $this->line('Kaynak    : ' . $source);
        $this->line('Dönem     : ' . $period);
        $this->line('Model     : ' . $model);
        $this->line('Sayfalar  : ' . $fromPage . '-' . $toPage);
        $this->line('Batch     : ' . $batchSize);
        $this->line('CSV       : ' . $output);
        $this->line('');

        /*
         * ------------------------------------------------------------
         * 1. PDF Gemini Files API'ye yükleniyor
         * ------------------------------------------------------------
         */

        $this->info('1/4 PDF Gemini Files API\'ye yükleniyor...');

        $fileUri = $this->uploadPdf($input, $apiKey);

        if (!$fileUri) {
            return self::FAILURE;
        }

        $this->info('PDF Gemini\'ye yüklendi.');
        $this->line('');

        /*
         * ------------------------------------------------------------
         * 2. Checkpoint
         * ------------------------------------------------------------
         */

        $records = [];
        $completedBatches = [];

        if ($this->option('resume') && File::exists($checkpoint)) {

            $checkpointData = json_decode(
                File::get($checkpoint),
                true
            );

            if (is_array($checkpointData)) {

                if (
                    isset($checkpointData['records']) &&
                    is_array($checkpointData['records'])
                ) {
                    $records = $checkpointData['records'];
                }

                if (
                    isset($checkpointData['completed_batches']) &&
                    is_array($checkpointData['completed_batches'])
                ) {
                    $completedBatches =
                        $checkpointData['completed_batches'];
                }

                $this->info(
                    'Checkpoint bulundu. '
                    . count($records)
                    . ' kayıt geri yüklendi.'
                );
            }
        }

        /*
         * ------------------------------------------------------------
         * 3. Sayfa sayfa Gemini analizi
         * ------------------------------------------------------------
         */

        $this->info('2/4 Gemini PDF tablolarını analiz ediyor...');
        $this->line('');

        for (
            $start = $fromPage;
            $start <= $toPage;
            $start += $batchSize
        ) {

            $end = min(
                $toPage,
                $start + $batchSize - 1
            );

            $batchKey = $start . '-' . $end;

            if (
                in_array(
                    $batchKey,
                    $completedBatches,
                    true
                )
            ) {
                $this->line(
                    "SKIP: Sayfa {$start}-{$end} daha önce işlendi."
                );
                continue;
            }

            $this->info(
                "Sayfa {$start}-{$end} Gemini'ye gönderiliyor..."
            );

            $batchRecords = $this->analyzePages(
                $fileUri,
                $model,
                $apiKey,
                $start,
                $end
            );

            if ($batchRecords === null) {

                $this->error(
                    "Sayfa {$start}-{$end} analiz edilemedi."
                );

                $this->saveCheckpoint(
                    $checkpoint,
                    $records,
                    $completedBatches,
                    $input,
                    $period,
                    $source
                );

                return self::FAILURE;
            }

            $accepted = 0;
            $rejected = 0;

            foreach ($batchRecords as $record) {

                $validated = $this->validateRecord(
                    $record,
                    $start,
                    $end
                );

                if ($validated === null) {
                    $rejected++;
                    continue;
                }

                $records[] = $validated;
                $accepted++;
            }

            $completedBatches[] = $batchKey;

            $this->line(
                "  Gemini kayıtları : " . count($batchRecords)
            );

            $this->line(
                "  Kabul edilen     : " . $accepted
            );

            $this->line(
                "  Reddedilen       : " . $rejected
            );

            $this->line(
                "  Toplam kayıt     : " . count($records)
            );

            $this->saveCheckpoint(
                $checkpoint,
                $records,
                $completedBatches,
                $input,
                $period,
                $source
            );

            $this->line('');
        }

        /*
         * ------------------------------------------------------------
         * 4. Tekilleştirme + CSV
         * ------------------------------------------------------------
         */

        $this->info(
            '3/4 Kayıtlar tekilleştiriliyor...'
        );

        $records = $this->deduplicateRecords(
            $records,
            $source,
            $period
        );

        $this->info(
            'Benzersiz kayıt: ' . count($records)
        );

        $this->info('4/4 CSV oluşturuluyor...');

        $handle = fopen($output, 'w');

        if (!$handle) {
            $this->error(
                'CSV dosyası oluşturulamadı: ' . $output
            );

            return self::FAILURE;
        }

        /*
         * Excel UTF-8 BOM
         */
        fwrite(
            $handle,
            "\xEF\xBB\xBF"
        );

        fputcsv(
            $handle,
            [
                'source',
                'period',
                'poz_no',
                'description',
                'unit',
                'price',
                'source_file',
                'source_page',
                'category',
                'confidence',
            ]
        );

        foreach ($records as $record) {

            fputcsv(
                $handle,
                [
                    $source,
                    $period,
                    $record['poz_no'],
                    $record['description'],
                    $record['unit'],
                    number_format(
                        $record['price'],
                        2,
                        '.',
                        ''
                    ),
                    basename($input),
                    $record['page'],
                    '',
                    number_format(
                        $record['confidence'],
                        2,
                        '.',
                        ''
                    ),
                ]
            );
        }

        fclose($handle);

        /*
         * Başarılı tamamlandıysa checkpoint sil.
         */
        if (File::exists($checkpoint)) {
            File::delete($checkpoint);
        }

        $this->line('');
        $this->info('==============================================');
        $this->info('TAMAMLANDI');
        $this->info('==============================================');
        $this->line(
            'CSV kayıt sayısı : ' . count($records)
        );
        $this->line(
            'CSV              : ' . $output
        );
        $this->line('');

        $this->warn(
            'CSV henüz rayic_prices tablosuna aktarılmadı.'
        );

        return self::SUCCESS;
    }

    private function uploadPdf(
        string $input,
        string $apiKey
    ): ?string {

        $fileSize = filesize($input);

        if ($fileSize === false) {
            $this->error('PDF boyutu okunamadı.');
            return null;
        }

        $uploadUrl =
            'https://generativelanguage.googleapis.com/upload/v1beta/files?key='
            . urlencode($apiKey);

        $response = Http::timeout(300)
            ->withHeaders([
                'X-Goog-Upload-Protocol' => 'resumable',
                'X-Goog-Upload-Command' => 'start',
                'X-Goog-Upload-Header-Content-Length' => $fileSize,
                'X-Goog-Upload-Header-Content-Type' => 'application/pdf',
                'Content-Type' => 'application/json',
            ])
            ->post(
                $uploadUrl,
                [
                    'file' => [
                        'display_name' =>
                            basename($input),
                    ],
                ]
            );

        if (!$response->successful()) {

            $this->error(
                'Gemini upload başlatılamadı.'
            );

            $this->line(
                $response->body()
            );

            return null;
        }

        $uploadTarget = null;

        foreach (
            $response->headers()
            as $key => $values
        ) {

            if (
                strtolower($key) ===
                'x-goog-upload-url'
            ) {
                $uploadTarget =
                    $values[0] ?? null;

                break;
            }
        }

        if (!$uploadTarget) {

            $this->error(
                'Gemini upload URL alınamadı.'
            );

            return null;
        }

        $pdfBytes = file_get_contents($input);

        if ($pdfBytes === false) {

            $this->error(
                'PDF okunamadı.'
            );

            return null;
        }

        $upload = Http::timeout(600)
            ->withHeaders([
                'Content-Length' => $fileSize,
                'X-Goog-Upload-Offset' => '0',
                'X-Goog-Upload-Command' =>
                    'upload, finalize',
            ])
            ->withBody(
                $pdfBytes,
                'application/pdf'
            )
            ->post($uploadTarget);

        if (!$upload->successful()) {

            $this->error(
                'PDF Gemini\'ye yüklenemedi.'
            );

            $this->line(
                $upload->body()
            );

            return null;
        }

        $fileUri = data_get(
            $upload->json(),
            'file.uri'
        );

        if (!$fileUri) {

            $this->error(
                'Gemini file URI alınamadı.'
            );

            $this->line(
                $upload->body()
            );

            return null;
        }

        return $fileUri;
    }

    private function analyzePages(
        string $fileUri,
        string $model,
        string $apiKey,
        int $fromPage,
        int $toPage
    ): ?array {

        $prompt = <<<PROMPT
Sen resmi inşaat rayiç ve birim fiyat PDF'lerinden
veri çıkaran profesyonel bir veri çıkarma motorusun.

PDF içinden SADECE şu sayfaları incele:

{$fromPage} - {$toPage}

Bu sayfa aralığının dışındaki hiçbir bilgiyi kullanma.

AMAÇ:

PDF üzerinde gerçekten bulunan tablo satırlarını
yapılandırılmış JSON olarak çıkarmaktır.

HER KAYITTA:

poz_no
description
unit
price
page
confidence

alanları bulunmalıdır.

KURALLAR:

1. PDF'de bulunmayan poz numarası üretme.

2. PDF'de bulunmayan fiyat üretme.

3. Tahmin yapma.

4. Matematiksel hesaplama yapma.

5. Fiyatı değiştirme.

6. Fiyatı yuvarlama.

7. Poz numarasını PDF'de görüldüğü şekilde çıkar.

8. Açıklamayı mümkün olduğunca eksiksiz çıkar.

9. Açıklama PDF'de birden fazla satıra yayılıyorsa,
   aynı poz numarasına ait satırları birleştir.

10. Farklı poz numaralarının açıklamalarını birbirine
    kesinlikle karıştırma.

11. Tablo başlıklarını kayıt olarak alma.

12. Bölüm başlıklarını kayıt olarak alma.

13. Toplam satırlarını kayıt olarak alma.

14. Boş satırları kayıt olarak alma.

15. Sayfa numarası gerçek PDF sayfa numarası olmalıdır.

16. Poz numarası ile fiyatın aynı tablo satırına ait
    olduğundan emin ol.

17. Bir fiyatın hangi poz numarasına ait olduğundan emin
    değilsen kaydı çıkarma.

18. Birim açıkça görünmüyorsa kaydı çıkarma.

19. Fiyat PDF'de Türkçe formatta örneğin
    1.234,56 ise JSON price alanına 1234.56 yaz.

20. price alanı SADECE sayısal değer olmalıdır.

21. OCR belirsizliği varsa confidence düşür.

22. Confidence 0.60'ın altındaysa kaydı döndürme.

23. PDF'de ilgili sayfalarda tablo satırı yoksa:
    records = []

24. Kesinlikle veri uydurma.

CONFIDENCE:

1.00 = tüm alanlar açık ve net
0.90 = çok küçük biçimsel belirsizlik
0.80 = küçük OCR belirsizliği
0.70 = okunabilir ancak kontrol faydalı
0.60 = ciddi belirsizlik

ÖNEMLİ:

Bir poz numarasının açıklaması sonraki satırda devam ediyorsa
açıklamayı birleştir.

Fakat sonraki poz numarası başladığında önceki açıklamayı
orada durdur.

SADECE JSON döndür.
PROMPT;

        $schema = [
            'type' => 'object',
            'properties' => [
                'records' => [
                    'type' => 'array',
                    'items' => [
                        'type' => 'object',
                        'properties' => [
                            'poz_no' => [
                                'type' => 'string',
                            ],
                            'description' => [
                                'type' => 'string',
                            ],
                            'unit' => [
                                'type' => 'string',
                            ],
                            'price' => [
                                'type' => 'number',
                            ],
                            'page' => [
                                'type' => 'integer',
                            ],
                            'confidence' => [
                                'type' => 'number',
                            ],
                        ],
                        'required' => [
                            'poz_no',
                            'description',
                            'unit',
                            'price',
                            'page',
                            'confidence',
                        ],
                    ],
                ],
            ],
            'required' => [
                'records',
            ],
        ];

        $url =
            'https://generativelanguage.googleapis.com/v1beta/models/'
            . $model
            . ':generateContent?key='
            . urlencode($apiKey);

        $body = [
            'contents' => [
                [
                    'parts' => [
                        [
                            'text' => $prompt,
                        ],
                        [
                            'file_data' => [
                                'mime_type' =>
                                    'application/pdf',
                                'file_uri' =>
                                    $fileUri,
                            ],
                        ],
                    ],
                ],
            ],
            'generationConfig' => [
                'responseMimeType' =>
                    'application/json',
                'responseSchema' => $schema,
                'temperature' => 0,
            ],
        ];

        $ai = Http::timeout(600)
            ->withHeaders([
                'Content-Type' =>
                    'application/json',
            ])
            ->post($url, $body);

        if (!$ai->successful()) {

            $this->error(
                'Gemini analiz hatası.'
            );

            $this->line(
                $ai->body()
            );

            return null;
        }

        $aiJson = $ai->json();

        $text = data_get(
            $aiJson,
            'candidates.0.content.parts.0.text'
        );

        if (!$text) {

            $this->error(
                'Gemini JSON çıktısı alınamadı.'
            );

            $this->line(
                json_encode(
                    $aiJson,
                    JSON_PRETTY_PRINT
                    | JSON_UNESCAPED_UNICODE
                )
            );

            return null;
        }

        $parsed = json_decode(
            $text,
            true
        );

        if (
            !is_array($parsed) ||
            !isset($parsed['records']) ||
            !is_array($parsed['records'])
        ) {

            $this->error(
                'Gemini geçerli records JSON üretmedi.'
            );

            $this->line($text);

            return null;
        }

        return $parsed['records'];
    }

    private function validateRecord(
        array $record,
        int $fromPage,
        int $toPage
    ): ?array {

        $poz = trim(
            (string)($record['poz_no'] ?? '')
        );

        $description = trim(
            (string)($record['description'] ?? '')
        );

        $unit = trim(
            (string)($record['unit'] ?? '')
        );

        $price = $record['price'] ?? null;

        $page = (int)(
            $record['page'] ?? 0
        );

        $confidence = (float)(
            $record['confidence'] ?? 0
        );

        if ($poz === '') {
            return null;
        }

        /*
         * YFK poz formatının temel doğrulaması.
         */
        if (!preg_match(
            '/^\d+(?:\.\d+)+$/',
            $poz
        )) {
            return null;
        }

        if ($description === '') {
            return null;
        }

        if ($unit === '') {
            return null;
        }

        if (!is_numeric($price)) {
            return null;
        }

        $price = (float)$price;

        if ($price < 0) {
            return null;
        }

        if (
            $page < $fromPage ||
            $page > $toPage
        ) {
            return null;
        }

        if ($confidence < 0.60) {
            return null;
        }

        if (mb_strlen($poz) > 50) {
            return null;
        }

        if (mb_strlen($description) > 5000) {
            return null;
        }

        if (mb_strlen($unit) > 30) {
            return null;
        }

        return [
            'poz_no' => $poz,
            'description' => $description,
            'unit' => $unit,
            'price' => round($price, 2),
            'page' => $page,
            'confidence' => round(
                $confidence,
                2
            ),
        ];
    }

    private function deduplicateRecords(
        array $records,
        string $source,
        string $period
    ): array {

        $unique = [];

        foreach ($records as $record) {

            $key =
                $source
                . '|'
                . $period
                . '|'
                . $record['poz_no'];

            if (!isset($unique[$key])) {

                $unique[$key] = $record;

                continue;
            }

            /*
             * Aynı poz iki batch'e denk geldiyse
             * confidence yüksek olanı koru.
             */
            if (
                $record['confidence'] >
                $unique[$key]['confidence']
            ) {
                $unique[$key] = $record;
            }
        }

        usort(
            $unique,
            function ($a, $b) {
                return strnatcmp(
                    $a['poz_no'],
                    $b['poz_no']
                );
            }
        );

        return array_values($unique);
    }

    private function saveCheckpoint(
        string $checkpoint,
        array $records,
        array $completedBatches,
        string $input,
        string $period,
        string $source
    ): void {

        File::put(
            $checkpoint,
            json_encode(
                [
                    'file' =>
                        basename($input),
                    'period' =>
                        $period,
                    'source' =>
                        $source,
                    'records' =>
                        array_values($records),
                    'completed_batches' =>
                        array_values(
                            array_unique(
                                $completedBatches
                            )
                        ),
                    'updated_at' =>
                        now()->toIso8601String(),
                ],
                JSON_PRETTY_PRINT
                | JSON_UNESCAPED_UNICODE
                | JSON_UNESCAPED_SLASHES
            )
        );
    }
}
