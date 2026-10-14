<?php

/*
|--------------------------------------------------------------------------
| Arama motoru ayarları
|--------------------------------------------------------------------------
| poz_index_only_enriched: Yalnızca açıklama ve SSS ile zenginleştirilmiş poz
| sayfaları Google dizinine ve sitemap'e girer. Zenginleştirilmemiş ~40 bin poz
| sayfası (yalnızca poz no + fiyat) "noindex, follow" olur; sayfalar açık kalır,
| kullanıcı ve iç bağlantılar için çalışır. Zenginleştirme: php artisan poz:enrich-seo
| (zamanlayıcıda günde 100). Kapatmak için .env: SEO_POZ_INDEX_ONLY_ENRICHED=false
*/

return [
    'poz_index_only_enriched' => (bool) env('SEO_POZ_INDEX_ONLY_ENRICHED', true),
];
