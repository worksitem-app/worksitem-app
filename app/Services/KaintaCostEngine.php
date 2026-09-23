<?php

namespace App\Services;

use App\Models\UnitPrice;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;

class KaintaCostEngine
{
    public function calculate($projectType, $area, $quality = 'standart', $district = 'Genel')
    {
        // 1. Baz Fiyatın Belirlenmesi
        $basePrice = 7500; // Standart Tadilat
        
        if (str_contains(strtolower($projectType), 'hafif')) $basePrice = 3500;
        if (str_contains(strtolower($projectType), 'komple')) $basePrice = 12000;
        if (str_contains(strtolower($projectType), 'lüks')) $basePrice = 18000;
        
        if (str_contains(strtolower($projectType), 'bina') || str_contains(strtolower($projectType), 'inşaat')) {
            if (Schema::hasTable('unit_prices')) {
                $dbPrice = UnitPrice::where('key', 'bina_sinif_4a_m2')->first();
                $basePrice = $dbPrice ? $dbPrice->price : 21050;
            } else {
                $basePrice = 21050;
            }
        }

        // 2. Katsayılar (Çarpanlar)
        $qualityMultiplier = ($quality === 'premium' || $quality === 'lüks') ? 1.35 : 1.0;
        $districtMultiplier = ($district === 'Istanbul') ? 1.15 : 1.0;

        // 3. Nihai Birim Fiyat ve Toplam
        $unitPrice = $basePrice * $qualityMultiplier * $districtMultiplier;
        $totalCost = $area * $unitPrice;

        // 4. Kırılımlar
        $breakdown = [
            'Malzeme Giderleri' => $totalCost * 0.45,
            'İşçilik Maliyeti' => $totalCost * 0.35,
            'Genel Gider & Şantiye' => $totalCost * 0.10,
            'Kâr & Risk Payı' => $totalCost * 0.10,
        ];

        // 5. AI Danışman Yorumu (Gemini)
        $aiAdvice = $this->getAiExpertAdvice($projectType, $area, $totalCost);

        return [
            'hesaplanan_maliyet_tl' => $totalCost,
            'unit_price' => $unitPrice,
            'ana_kalemler' => $breakdown,
            'ai_tavsiye' => $aiAdvice
        ];
    }

    private function getAiExpertAdvice($projectType, $area, $totalCost)
    {
        $apiKey = config('services.gemini.api_key');
        if (empty($apiKey)) return "🤖 Sistem Uyarısı: AI modülü API anahtarı bekleniyor.";

        $url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent';
        $formattedCost = number_format($totalCost, 0, ',', '.');

        $prompt = "Sen Kainta İnşaat'ın Baş Mühendisisin. Müşteri '{$projectType}' projesi (Alan: {$area} m²) için maliyet hesapladı. Bütçe: {$formattedCost} TL. Amacın müşteriye güven vermek ve Kainta ile çalışmaya ikna etmek. Müşteriye hitaben, projeye özel 3 maddelik çok kısa teknik tavsiye ver. Fiyat hesabı YAZMA, kaliteyi vurgula.";

        try {
            $response = Http::withHeaders(['x-goog-api-key' => $apiKey])->timeout(60)->post($url, [
                'contents' => [['parts' => [['text' => $prompt]]]],
                'generationConfig' => ['temperature' => 0.7, 'maxOutputTokens' => 250]
            ]);

            if ($response->successful()) {
                $aiText = $response->json()['candidates'][0]['content']['parts'][0]['text'] ?? '';
                $aiText = preg_replace('/\*\*(.*?)\*\*/', '<strong>$1</strong>', $aiText);
                return str_replace('* ', '<br>✅ ', $aiText);
            }
            return "AI servisimiz şu an yoğun. Lütfen Kainta İnşaat yetkilileriyle görüşün.";
        } catch (\Exception $e) {
            return "AI Bağlantı Hatası.";
        }
    }
}
