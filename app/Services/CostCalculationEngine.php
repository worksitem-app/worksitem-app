<?php
namespace App\Services;

class CostCalculationEngine
{
    /**
     * Tüm hesaplamalar artık bu ana motordan geçecek.
     * Hardcoded (25.000 TL vb.) değerler yerine DB_PRICES kullanılacak.
     */
    public static function getBasePrice($serviceType)
    {
        // Merkezi veritabanı fiyatlarını çekiyoruz
        $prices = CostEngine::getGlobalPrices();

        // Eğer veritabanında özel bir tanım yoksa, dinamik varsayılanlar
        $defaults = [
            'komple_bina' => 25000,
            'kaba_insaat' => 12000,
            'ince_isler'  => 7500,
            'anahtar_teslim' => 18000,
        ];

        // 1. Önce DB'de var mı bak (Örn: 'base_price_komple_bina')
        $dbKey = 'base_price_' . $serviceType;
        if (isset($prices[$dbKey])) {
            return floatval($prices[$dbKey]);
        }

        // 2. Yoksa varsayılanı dön
        return $defaults[$serviceType] ?? 0;
    }

    public static function calculateTotal($serviceType, $area, $customMultiplier = 1.0)
    {
        $basePrice = self::getBasePrice($serviceType);
        $kaintaMultiplier = 1.40;
        return $basePrice * $area * $customMultiplier * $kaintaMultiplier; // ÇŞB Fiyatı x 1.40 Kainta Çarpanı
    }
}
