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
        $apiKey = env('GEMINI_API_KEY');
        $model = env('GEMINI_BOQ_MODEL', 'gemini-2.5-flash');

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

        // Veritabanına Yazma (Update or Insert)
        $updatedCount = 0;
        foreach ($parsed['prices'] as $item) {
            if (empty($item['poz_no']) || empty($item['price'])) continue;

            DB::table('tender_pozs')->updateOrInsert(
                [
                    institution => ÇŞİDB/YFK,
                    year => date(Y),
                    poz_no => trim($item[poz_no]),
                    price_type => unit_price,
                ],
                [
                    period => date(Y-m),
                    description => trim($item[name]),
                    unit => trim($item[unit]),
                    unit_price => (float) $item[price],
                    profit_included => false,
                    vat_included => false,
                    is_active => true,
                    source_file => $file->getClientOriginalName(),
                    updated_at => now(),
                    created_at => now(),
                ]
            );

            $updatedCount++;
        }

        return [
            'success' => true,
            'message' => "Harika! AI dosyayı okudu ve $updatedCount adet pozun fiyatı sistemde güncellendi."
        ];
    }
}
