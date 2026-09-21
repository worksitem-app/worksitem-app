<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiDesignService
{
    // Stil tanımları (İngilizce — model İngilizce anlıyor)
    private array $styles = [
        'Modern' => 'modern interior design, clean lines, contemporary furniture, bright',
        'Minimalist' => 'minimalist interior design, uncluttered, simple furniture, neutral colors',
        'Endüstriyel' => 'industrial interior design, exposed brick walls, metal accents, vintage furniture',
        'Lüks' => 'luxury interior design, marble, gold accents, elegant velvet furniture',
        'Japandi' => 'japandi interior design, warm wood tones, minimal, indoor plants',
        'Klasik' => 'classic interior design, ornate details, elegant curtains, antique furniture',
    ];

    // Hızlı model (LCM — 6-8 adım)
    private string $modelVersion = 'fbbd475b1084de80c47c35bfe4ae64b964294aa7e237e6537eed938cfd24903d';

    private int $maxImageSize = 1024;

    /**
     * Resmi küçült (OOM önlemi)
     */
    private function resizeImage(string $imagePath): string
    {
        if (!function_exists('imagecreatefromstring')) {
            return $imagePath;
        }

        $imageInfo = getimagesize($imagePath);
        if ($imageInfo === false) {
            return $imagePath;
        }

        [$width, $height] = $imageInfo;

        if ($width <= $this->maxImageSize && $height <= $this->maxImageSize) {
            return $imagePath;
        }

        $ratio = min($this->maxImageSize / $width, $this->maxImageSize / $height);
        $newWidth = (int) ($width * $ratio);
        $newHeight = (int) ($height * $ratio);

        $source = imagecreatefromstring(file_get_contents($imagePath));
        if ($source === false) {
            return $imagePath;
        }

        $resized = imagecreatetruecolor($newWidth, $newHeight);
        imagealphablending($resized, false);
        imagesavealpha($resized, true);
        imagecopyresampled($resized, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

        $tempPath = sys_get_temp_dir() . '/ai_input_' . uniqid() . '.jpg';
        imagejpeg($resized, $tempPath, 85);

        imagedestroy($source);
        imagedestroy($resized);

        return $tempPath;
    }

    /**
     * Kullanıcı metnini temizle (güvenlik)
     */
    private function sanitizeUserPrompt(?string $prompt): string
    {
        if (empty($prompt)) {
            return '';
        }

        // HTML etiketlerini temizle
        $prompt = strip_tags($prompt);

        // URL'leri temizle
        $prompt = preg_replace('/https?:\/\/\S+/', '', $prompt);

        // Fazla boşlukları temizle
        $prompt = preg_replace('/\s+/', ' ', $prompt);

        // İngilizce'ye çevirmeden olduğu gibi bırak
        // (LCM modeli Türkçe de anlıyor — düşük ihtimalle)
        return trim($prompt);
    }

    /**
     * Tasarımı başlat
     */
    public function startAsync(
        string $imagePath,
        string $style,
        ?string $userPrompt = null
    ): array {
        $token = config('services.replicate.token');

        if (!$token) {
            return ['status' => 'error', 'message' => 'REPLICATE_API_TOKEN bulunamadı.'];
        }

        $tempPath = null;

        try {
            $tempPath = $this->resizeImage($imagePath);

            $imageData = base64_encode(file_get_contents($tempPath));
            $mimeType = mime_content_type($tempPath);
            $base64Image = "data:{$mimeType};base64,{$imageData}";

            // Prompt oluştur
            $styleText = $this->styles[$style] ?? $this->styles['Modern'];
            $userText = $this->sanitizeUserPrompt($userPrompt);

            // Format: [kullanıcı metni] + [stil] + [kalite]
            $promptParts = ['interior design of a room'];

            if (!empty($userText)) {
                $promptParts[] = $userText;
            }

            $promptParts[] = $styleText;
            $promptParts[] = 'photorealistic, 4K, ultra detailed, architectural photography';

            $fullPrompt = implode(', ', $promptParts);

            $response = Http::withToken($token)
                ->timeout(30)
                ->post('https://api.replicate.com/v1/predictions', [
                    'version' => $this->modelVersion,
                    'input' => [
                        'image' => $base64Image,
                        'prompt' => $fullPrompt,
                        'negative_prompt' => 'blurry, low quality, deformed, ugly, distorted, watermark',
                        'prompt_strength' => 0.65,
                        'num_inference_steps' => 6,
                        'guidance_scale' => 2,
                        'scheduler' => 'LCM',
                        'width' => 1024,
                        'height' => 1024,
                        'apply_watermark' => false,
                    ],
                ]);

            if ($tempPath && $tempPath !== $imagePath && file_exists($tempPath)) {
                @unlink($tempPath);
            }

            if (!$response->successful()) {
                throw new \Exception('API Başlatma Hatası: ' . $response->body());
            }

            $data = $response->json();

            return [
                'status' => 'success',
                'prediction_id' => $data['id'],
                'prompt_used' => $fullPrompt, // Debug için
            ];
        } catch (\Exception $e) {
            if ($tempPath && $tempPath !== $imagePath && file_exists($tempPath)) {
                @unlink($tempPath);
            }
            Log::error('AI start failed: ' . $e->getMessage());
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    /**
     * Prediction durumunu sorgula
     */
    public function checkAsync(string $predictionId): array
    {
        $token = config('services.replicate.token');

        if (!$token) {
            return ['status' => 'failed', 'message' => 'Token bulunamadı.'];
        }

        try {
            $response = Http::withToken($token)
                ->timeout(15)
                ->get("https://api.replicate.com/v1/predictions/{$predictionId}");

            if (!$response->successful()) {
                return ['status' => 'failed', 'message' => 'Durum sorgulanamadı.'];
            }

            $data = $response->json();

            if ($data['status'] === 'succeeded' && !empty($data['output'])) {
                $outputUrl = is_array($data['output']) ? end($data['output']) : $data['output'];
                return ['status' => 'succeeded', 'output_url' => $outputUrl];
            }

            if ($data['status'] === 'failed' || $data['status'] === 'canceled') {
                return ['status' => 'failed', 'message' => $data['error'] ?? 'Model hatası.'];
            }

            return ['status' => $data['status'], 'message' => 'İşleniyor...'];
        } catch (\Exception $e) {
            Log::error('AI check failed: ' . $e->getMessage());
            return ['status' => 'failed', 'message' => $e->getMessage()];
        }
    }
}
