<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class GeminiRayicService
{
    private string $apiKey;
    private string $model;

    public function __construct()
    {
        $this->apiKey = (string) config('services.gemini.api_key');
        $this->model = (string) config(
            'services.gemini.rayic_model',
            'gemini-3.7-flash'
        );

        if (!$this->apiKey) {
            throw new RuntimeException(
                'GEMINI_API_KEY yapılandırılmamış.'
            );
        }
    }

    public function analyzePdf(string $path, string $period, string $source = 'YFK'): array
    {
        if (!is_file($path)) {
            throw new RuntimeException('PDF dosyası bulunamadı.');
        }

        $file = $this->uploadFile($path);

        $prompt = <<<PROMPT
Sen profesyonel bir inşaat rayiç fiyat veri çıkarma sistemisin.

Bu PDF resmi bir yapı/inşaat/tesisat rayiç fiyat listesidir.

PDF içerisindeki TABLOLARDA bulunan gerçek rayiç kayıtlarını çıkar.

Her kayıt için şu alanları üret:

- poz_no
- description
- unit
- price
- source_page
- category

KURALLAR:

1. PDF'de açıkça bulunmayan hiçbir poz numarası üretme.
2. PDF'de bulunmayan hiçbir fiyatı tahmin etme.
3. Fiyatı değiştirme veya yuvarlama yapma.
4. Türkçe ondalık virgülü sayısal ondalık noktaya çevir.
   Örneğin 1.234,56 -> 1234.56
5. Binlik ayırıcıları doğru yorumla.
6. Birimi PDF'de nasıl geçiyorsa ona göre çıkar.
7. Açıklamayı mümkün olduğunca PDF'deki haliyle koru.
8. Tablo başlıklarını kayıt olarak alma.
9. Sayfa numarasını PDF sayfası olarak belirt.
10. Aynı poz numarasını aynı PDF içinde tekrar etme.
11. Eğer bir kayıt okunamıyorsa onu tahmin etmek yerine atla.
12. Fiyat 0 veya negatif olamaz.
13. source alanı "$source" olacaktır.
14. period "$period" olacaktır.
15. category için makul teknik kategori kullan:
    beton, demir, kalip, duvar, siva, boya, seramik,
    parke, mantolama, tesisat, elektrik, mekanik,
    hafriyat, cimento, diger.
16. Sadece gerçek fiyat tablosu satırlarını çıkar.

ÇOK ÖNEMLİ:
Bu görev fiyat tahmini değildir.
PDF'de yazan veriyi çıkartma görevidir.

Sonucu yalnızca istenen JSON formatında döndür.
PROMPT;

        $response = Http::timeout(300)
            ->withHeaders([
                'x-goog-api-key' => $this->apiKey,
                'Content-Type' => 'application/json',
            ])
            ->post(
                "https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:generateContent",
                [
                    'contents' => [
                        [
                            'parts' => [
                                [
                                    'text' => $prompt,
                                ],
                                [
                                    'file_data' => [
                                        'mime_type' => 'application/pdf',
                                        'file_uri' => $file['uri'],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'generationConfig' => [
                        'temperature' => 0,
                        'responseFormat' => [
                            'text' => [
                                'mimeType' => 'application/json',
                                'schema' => [
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
                                                    'source_page' => [
                                                        'type' => 'integer',
                                                    ],
                                                    'category' => [
                                                        'type' => 'string',
                                                    ],
                                                ],
                                                'required' => [
                                                    'poz_no',
                                                    'description',
                                                    'unit',
                                                    'price',
                                                    'source_page',
                                                    'category',
                                                ],
                                            ],
                                        ],
                                    ],
                                    'required' => ['records'],
                                ],
                            ],
                        ],
                    ],
                ]
            );

        if (!$response->successful()) {
            throw new RuntimeException(
                'Gemini API hatası: ' . $response->body()
            );
        }

        $json = $response->json();

        $text = data_get(
            $json,
            'candidates.0.content.parts.0.text'
        );

        if (!$text) {
            throw new RuntimeException(
                'Gemini boş sonuç döndürdü.'
            );
        }

        $result = json_decode($text, true);

        if (!is_array($result) || !isset($result['records'])) {
            throw new RuntimeException(
                'Gemini geçerli JSON rayiç sonucu döndürmedi.'
            );
        }

        return $result;
    }

    private function uploadFile(string $path): array
    {
        $size = filesize($path);
        $mime = 'application/pdf';

        /*
         * Gemini Files API resumable upload.
         */
        $start = Http::timeout(120)
            ->withHeaders([
                'x-goog-api-key' => $this->apiKey,
                'X-Goog-Upload-Protocol' => 'resumable',
                'X-Goog-Upload-Command' => 'start',
                'X-Goog-Upload-Header-Content-Length' => (string) $size,
                'X-Goog-Upload-Header-Content-Type' => $mime,
                'Content-Type' => 'application/json',
            ])
            ->post(
                'https://generativelanguage.googleapis.com/upload/v1beta/files',
                [
                    'file' => [
                        'display_name' => basename($path),
                    ],
                ]
            );

        if (!$start->successful()) {
            throw new RuntimeException(
                'Gemini dosya yükleme başlatılamadı: ' .
                $start->body()
            );
        }

        $uploadUrl =
            $start->header('X-Goog-Upload-URL') ??
            $start->header('x-goog-upload-url');

        if (!$uploadUrl) {
            throw new RuntimeException(
                'Gemini upload URL döndürmedi.'
            );
        }

        $upload = Http::timeout(300)
            ->withHeaders([
                'Content-Length' => (string) $size,
                'X-Goog-Upload-Offset' => '0',
                'X-Goog-Upload-Command' => 'upload, finalize',
                'Content-Type' => $mime,
            ])
            ->withBody(
                file_get_contents($path),
                $mime
            )
            ->post($uploadUrl);

        if (!$upload->successful()) {
            throw new RuntimeException(
                'PDF Gemini Files API\'ye yüklenemedi: ' .
                $upload->body()
            );
        }

        $data = $upload->json();

        $uri = data_get($data, 'file.uri');

        if (!$uri) {
            throw new RuntimeException(
                'Gemini dosya URI döndürmedi.'
            );
        }

        return [
            'name' => data_get($data, 'file.name'),
            'uri' => $uri,
            'mime_type' => data_get(
                $data,
                'file.mimeType',
                $mime
            ),
        ];
    }
}
