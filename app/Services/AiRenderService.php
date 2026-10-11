<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiRenderService
{
    /**
     * WORKSITEM CONTECH: DALL-E 3 RENDER MOTORU (Samimi & Ulaşılabilir Mimari)
     */
    public function generateGuestConceptRender(array $data)
    {
        if (!config('services.ai_spend_enabled')) {
            return null;
        }

        // Stabilite için OPENAI_API_KEY kullanıyoruz
        // env() config:cache sonrası null döner → prod'da hep fallback görsel gidiyordu.
        $apiKey = config('services.openai.api_key');

        $type = $data['type'] ?? 'renovation';
        $quality = $data['quality'] ?? 'standard';
        $scope = $data['scope'] ?? 'complete';

        // Sıcak, samimi ve "ulaşılabilir" (inviting) kalite etiketleri
        $qualityMap = [
            'economic' => 'clean, modest, cozy, and highly functional',
            'standard' => 'warm, inviting, modern, and comfortable',
            'premium' => 'elegant, beautifully designed, welcoming, with premium natural materials',
            'luxury' => 'luxurious but warm, sophisticated, with a sincere and inviting atmosphere'
        ];
        $qLabel = $qualityMap[$quality] ?? 'warm and modern';

        // Samimi Prompt (Komut) Mühendisliği
        if ($type === 'new_build') {
            $prompt = "A warm, inviting, and approachable architectural photography of a {$qLabel} newly built residential home. Golden hour sunlight, lush natural landscaping, welcoming entrance, cozy atmosphere, photorealistic.";
        } elseif ($type === 'commercial') {
            // Soğuk gökdelenler yerine, butik, sıcak ve şık bir ticari yapı
            $prompt = "A warm, inviting, and sincere architectural photography of a {$qLabel} boutique commercial building and modern workspace. Human-scale design, lots of natural light, green landscaping, welcoming facade, vibrant atmosphere, photorealistic.";
        } elseif ($type === 'partial_renovation') {
            $prompt = "A warm and inviting interior photography of a {$qLabel} modern kitchen. Sunlight streaming through windows, lived-in feel, cozy and approachable design, photorealistic.";
        } else {
            // Tadilat
            $scopeLabel = ($scope === 'light') ? 'living room' : 'entire living space';
            $prompt = "A highly welcoming, warm and sincere interior photography of a {$qLabel} renovated {$scopeLabel}. Soft natural lighting, comfortable and cozy furniture, inviting atmosphere, photorealistic.";
        }

        // =====================================================================
        // SAMİMİ YEDEK (FALLBACK) GÖRSELLERİ (API Key Yoksa Çökmeyi Engeller)
        // =====================================================================
        if (empty($apiKey)) {
            Log::warning('AI Render: OPENAI_API_KEY eksik. Samimi Fallback görseli gönderiliyor.');
            
            if ($type === 'commercial') {
                return 'https://images.unsplash.com/photo-1556804367-18231e784570?auto=format&fit=crop&w=1024&q=80';
            } elseif ($type === 'new_build') {
                return 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=1024&q=80';
            } elseif ($type === 'partial_renovation') {
                return 'https://images.unsplash.com/photo-1556909114-f6e7ad7d3136?auto=format&fit=crop&w=1024&q=80';
            } else {
                return 'https://images.unsplash.com/photo-1600210491369-e753d80a41f3?auto=format&fit=crop&w=1024&q=80';
            }
        }

        // =====================================================================
        // STABİL DALL-E 3 API BAĞLANTISI
        // =====================================================================
        try {
            $response = Http::withToken($apiKey)
                ->timeout(20)
                ->post('https://api.openai.com/v1/images/generations', [
                    'model' => 'dall-e-3',
                    'prompt' => $prompt,
                    'n' => 1,
                    'size' => '1024x1024',
                    'quality' => 'standard'
                ]);

            if ($response->successful()) {
                return $response->json('data.0.url');
            }

            Log::error('DALL-E Render API Hatası: ' . $response->body());
            return null;

        } catch (\Exception $e) {
            Log::error('OpenAI HTTP/Servis Hatası: ' . $e->getMessage());
            return null;
        }
    }

    public function generateConceptRender($answers, $isRenovation)
    {
        return null;
    }
}
