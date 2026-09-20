<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiDesignService
{
    public function generateInterior($imagePath, $style)
    {
        $token = env('REPLICATE_API_TOKEN');

        if (!$token) {
            return ['status' => 'error', 'message' => 'Lütfen .env dosyasına REPLICATE_API_TOKEN ekleyin.'];
        }

        try {
            $imageData = base64_encode(file_get_contents($imagePath));
            $mimeType = mime_content_type($imagePath);
            $base64Image = "data:{$mimeType};base64,{$imageData}";

            $prompts = [
                'Modern' => 'modern interior design, clean lines, bright, contemporary furniture, photorealistic, 8k resolution',
                'Minimalist' => 'minimalist interior design, uncluttered, simple furniture, neutral colors, natural light, wabi-sabi',
                'Endüstriyel' => 'industrial interior design, exposed brick walls, metal accents, vintage leather furniture',
                'Lüks' => 'luxury premium interior design, marble floors, gold accents, elegant velvet furniture, 8k',
                'Japandi' => 'japandi interior design, warm wood tones, wabi-sabi elements, minimalistic, indoor plants',
                'Klasik' => 'classic vintage interior design, ornate wood carvings, elegant curtains, antique furniture'
            ];

            $selectedPrompt = $prompts[$style] ?? $prompts['Modern'];

            // Odanın mimari çizgilerini koruyan MLSD ControlNet modeli
            $response = Http::withToken($token)
                ->post('https://api.replicate.com/v1/predictions', [
                    'version' => '854e8727697a057c525cdb45ab037f64ecca770a1ce69e428df4c62f2702b800',
                    'input' => [
                        'image' => $base64Image,
                        'prompt' => 'interior design of a room, ' . $selectedPrompt,
                        'a_prompt' => 'best quality, extremely detailed, interior architecture magazine',
                        'n_prompt' => 'longbody, lowres, worst quality, low quality, deformed structure',
                        'image_resolution' => '512'
                    ]
                ]);

            if (!$response->successful()) {
                throw new \Exception('API Başlatma Hatası: ' . $response->body());
            }

            $prediction = $response->json();
            $getUrl = $prediction['urls']['get'];

            $attempts = 0;
            while ($prediction['status'] !== 'succeeded' && $prediction['status'] !== 'failed' && $attempts < 15) {
                sleep(2);
                $prediction = Http::withToken($token)->get($getUrl)->json();
                $attempts++;
            }

            if ($prediction['status'] === 'succeeded' && !empty($prediction['output'])) {
                $outputUrl = is_array($prediction['output']) ? end($prediction['output']) : $prediction['output'];
                return ['status' => 'success', 'provider' => 'replicate', 'output_url' => $outputUrl];
            }

            throw new \Exception('Görsel üretilemedi veya zaman aşımına uğradı.');
        } catch (\Exception $e) {
            Log::error('AI Tasarım Hatası: ' . $e->getMessage());
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }
}
