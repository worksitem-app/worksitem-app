<?php

/*
|--------------------------------------------------------------------------
| Arama motoru ayarları
|--------------------------------------------------------------------------
| poz_index_only_enriched: true ise yalnızca AI ile zenginleştirilmiş poz sayfaları
| dizine ve sitemap'e girer, kalanlar "noindex, follow" olur.
| VARSAYILAN KAPALI (v113): v39 bu kuralı açınca (14.10.2026) zenginleştirme AI kilidi
| yüzünden hiç çalışmadı ve poz trafiği (sitenin %97'si) bir günde sıfırlandı.
| Açmadan önce zenginleştirmenin gerçekten çalıştığını ve talep gören pozları
| kapsadığını doğrulayın. Açmak için .env: SEO_POZ_INDEX_ONLY_ENRICHED=true
*/

return [
    'poz_index_only_enriched' => (bool) env('SEO_POZ_INDEX_ONLY_ENRICHED', false),
];
