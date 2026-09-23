<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class AISalesEngineer
{
    public static function generateProposal($customerName, $service, $area, $notes)
    {
        $apiKey = config('services.gemini.api_key');
        if (empty($apiKey)) return "API Anahtarı eksik.";

        $url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-pro:generateContent';

        $prompt = "Sen Kainta İnşaat'ın Kıdemli Satış ve Teklif Mühendisisin. Müşterimiz {$customerName}, {$area} m² alan için '{$service}' talebinde bulundu. Müşteri notu: '{$notes}'. 
        Amacın: Müşteriye çok şık, güven veren, teknik ama anlaşılır bir ön teklif / niyet mektubu yazmak.
        Format:
        1. Sıcak ve kurumsal bir selamlama.
        2. Projenin ( {$service} ) teknik olarak Kainta İnşaat standartlarında nasıl kusursuz yapılacağının 3 maddelik özeti (malzeme kalitesi, usta garantisi vurgusu).
        3. Fiyat belirtme (Çünkü saha keşfi yapılmadı), sadece 'Uzman mimarlarımız yerinde keşif sonrasında nihai bütçeyi sunacaktır' de.
        4. Harekete geçirici (Call to Action) ve profesyonel bir kapanış (Kainta İnşaat adına).
        Lütfen HTML etiketleri (<strong>, <br>, <ul>, <li>) kullanarak doğrudan basıma/gönderime hazır bir metin üret.";

        try {
            $response = Http::withHeaders(['x-goog-api-key' => $apiKey])->timeout(60)->post($url, [
                'contents' => [['parts' => [['text' => $prompt]]]],
                'generationConfig' => ['temperature' => 0.6, 'maxOutputTokens' => 800]
            ]);

            if ($response->successful()) {
                return $response->json()['candidates'][0]['content']['parts'][0]['text'] ?? 'Teklif oluşturulamadı.';
            }
            return "AI Servis Hatası.";
        } catch (\Exception $e) {
            return "Bağlantı Hatası.";
        }
    }
}
