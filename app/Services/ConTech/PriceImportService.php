<?php
namespace App\Services\ConTech;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PriceImportService
{
    public function importFromPdf(UploadedFile $file)
    {
        if (!config('services.ai_spend_enabled')) {
            throw new \RuntimeException('AI fiyat içe aktarma geçici olarak devre dışı.');
        }

        $apiKey = config('services.gemini.api_key');
        $model = config('services.gemini.boq_model', 'gemini-2.5-flash');

        if (empty($apiKey)) {
            throw new \Exception("GEMINI_API_KEY bulunamadı.");
        }

        $mime = $file->getMimeType();
        $base64 = base64_encode(file_get_contents($file->getRealPath()));

        $prompt = "Sen uzman bir veri giriş ve maliyet mühendisisin. 
        Ekli PDF dosyasındaki inşaat birim fiyatları (poz) listesini analiz et. 
        Dosyadaki her bir kalemin poz numarasını, adını (açıklamasını), birimini (m2, m3, kg vb.) ve güncel fiyatını (sadece sayı olarak) çıkar.
        Sadece istenen JSON formatında yanıt ver, başka hiçbir metin ekleme.";

        $schema = [
            'type' => 'object',
            'properties' => [
                'prices' => [
                    'type' => 'array',
                    'items' => [
                        'type' => 'object',
                        'properties' => [
                            'poz_no' => ['type' => 'string'],
                            'name' => ['type' => 'string'],
                            'unit' => ['type' => 'string'],
                            'price' => ['type' => 'number']
                        ],
                        'required' => ['poz_no', 'name', 'unit', 'price']
                    ]
                ]
            ]
        ];

        $payload = [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt],
                        ['inlineData' => ['mimeType' => $mime, 'data' => $base64]]
                    ]
                ]
            ],
            'generationConfig' => [
                'temperature' => 0.1,
                'responseMimeType' => 'application/json',
                'responseSchema' => $schema
            ]
        ];

        $response = Http::timeout(120)->withHeaders([
            'x-goog-api-key' => $apiKey,
            'Content-Type' => 'application/json',
        ])->post("https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent", $payload);

        if (!$response->successful()) {
            throw new \Exception('Gemini API hatası: ' . $response->body());
        }

        $resultText = $response->json('candidates.0.content.parts.0.text');
        $parsed = json_decode($resultText, true);

        if (!isset($parsed['prices']) || !is_array($parsed['prices'])) {
            throw new \Exception("PDF'den fiyat listesi çıkarılamadı.");
        }

        // Veritabanına Yazma (Update or Insert) — tek transaction, kısmi yazma yok
        $updatedCount = 0;
        $year = (int) date('Y');
        $period = date('Y-m');
        $sourceFile = $file->getClientOriginalName();

        DB::transaction(function () use ($parsed, $year, $period, $sourceFile, &$updatedCount) {
            foreach ($parsed['prices'] as $item) {
                $pozNo = trim((string) ($item['poz_no'] ?? ''));
                $price = (float) ($item['price'] ?? 0);

                if ($pozNo === '' || $price <= 0) {
                    continue;
                }

                DB::table('tender_pozs')->updateOrInsert(
                    [
                        'institution' => 'ÇŞİDB/YFK',
                        'year' => $year,
                        'poz_no' => $pozNo,
                        'price_type' => 'unit_price',
                    ],
                    [
                        'period' => $period,
                        'description' => trim((string) ($item['name'] ?? '')),
                        'unit' => trim((string) ($item['unit'] ?? '')),
                        'unit_price' => $price,
                        'profit_included' => false,
                        'vat_included' => false,
                        'is_active' => true,
                        'source_file' => $sourceFile,
                        'updated_at' => now(),
                        'created_at' => now(),
                    ]
                );

                $updatedCount++;
            }
        });

        return [
            'success' => true,
            'message' => "Harika! AI dosyayı okudu ve $updatedCount adet pozun fiyatı sistemde güncellendi."
        ];
    }
}
