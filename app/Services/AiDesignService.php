<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiDesignService
{
    private array $styles = [
        'Modern' => 'modern interior design, clean lines, contemporary furniture, bright, photorealistic',
        'Minimalist' => 'minimalist interior design, uncluttered, simple furniture, neutral colors',
        'Endüstriyel' => 'industrial interior design, exposed brick walls, metal accents, vintage furniture',
        'Lüks' => 'luxury interior design, marble, gold accents, elegant velvet furniture',
        'Japandi' => 'japandi interior design, warm wood tones, minimal, indoor plants',
        'Klasik' => 'classic interior design, ornate details, elegant curtains, antique furniture',
    ];

    // Nano Banana 2 (Google Gemini 3.1 Flash Image)
    private string $modelVersion = 'd1be8b5fc0931a253d417e12a484ac01ee9ccbc6daffd4792151377d5e5ff55f';

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
     * Kullanıcı metnini temizle
     */
    private function sanitizeUserPrompt(?string $prompt): string
    {
        if (empty($prompt)) {
            return '';
        }

        $prompt = strip_tags($prompt);
        $prompt = preg_replace('/https?:\/\/\S+/', '', $prompt);
        $prompt = preg_replace('/\s+/', ' ', $prompt);

        return trim($prompt);
    }

    /**
     * Tasarımı başlat (Nano Banana 2)
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

            // Nano Banana 2 için özel prompt formatı
            $promptParts = [
                'Transform this room into a beautiful new design.',
                'Keep the architectural structure (walls, windows, doors, ceiling) EXACTLY the same.',
                'Only change furniture, decor, colors, materials, and lighting.',
            ];

            if (!empty($userText)) {
                $promptParts[] = "User request: {$userText}.";
            }

            $promptParts[] = "Style: {$styleText}.";
            $promptParts[] = 'Photorealistic, professional interior photography, 4K quality.';

            $fullPrompt = implode(' ', $promptParts);

            $response = Http::withToken($token)
                ->timeout(60)
                ->post('https://api.replicate.com/v1/predictions', [
                    'version' => $this->modelVersion,
                    'input' => [
                        'prompt' => $fullPrompt,
                        'image_input' => [$base64Image],
                        'resolution' => '1K',
                        'aspect_ratio' => 'match_input_image',
                        'output_format' => 'jpg',
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
                'prompt_used' => $fullPrompt,
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
