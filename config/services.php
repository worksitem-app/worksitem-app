<?php

return [

    // AI harcama kilidi: false iken tüm public AI uçları 503 döner (EnsureAiSpendEnabled)
    'ai_spend_enabled' => (bool) env('AI_SPEND_ENABLED', false),

    'tile_visualizer' => [
        'test_mode' => (bool) env('TILE_VISUALIZER_TEST_MODE', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Resend, Postmark, AWS, and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'gemini' => [
        'api_key' => env('GEMINI_API_KEY'),
        'rayic_model' => env('GEMINI_RAYIC_MODEL', 'gemini-3.6-flash'),
        'boq_model' => env('GEMINI_BOQ_MODEL', 'gemini-3.6-flash'),
        'price_research_model' => env('GEMINI_PRICE_RESEARCH_MODEL', 'gemini-3.6-flash'),
        'image_model' => env('GEMINI_IMAGE_MODEL', 'gemini-3.1-flash-image'),
        // HD görsel (2 kredi): ayrı model ve çözünürlük seçilebilir (sağlayıcının desteklediği: 1K, 2K, 4K)
        'image_model_hd' => env('GEMINI_IMAGE_MODEL_HD', env('GEMINI_IMAGE_MODEL', 'gemini-3.1-flash-image')),
        'image_hd_size' => env('GEMINI_IMAGE_HD_SIZE', '2K'),
    ],

    'openai' => [
        'api_key' => env('OPENAI_API_KEY'),
    ],

    'replicate' => [
        'token' => env('REPLICATE_API_TOKEN'),
    ],

    'openai' => [
        'api_key' => env('OPENAI_API_KEY'),
    ],

    // Google Analytics 4
    'ai' => [
        // Tüm kullanıcılar için günlük toplam AI isteği tavanı (throttle:ai)
        'global_daily_limit' => (int) env('AI_GLOBAL_DAILY_LIMIT', 300),
    ],

    'ga4' => [
        'measurement_id' => env('GA4_MEASUREMENT_ID'),
    ],

    // Google Search Console HTML etiketi doğrulaması (DNS ile doğrulandıysa boş kalabilir)
    'google' => [
        'site_verification' => env('GOOGLE_SITE_VERIFICATION'),
    ],

    // iyzico (kart ile keşif ödemesi). Anahtar adları farklı projelerden kopyalanabildiği için eş adlar da okunur.
    'iyzico' => [
        'api_key' => env('IYZICO_API_KEY', env('IYZIPAY_API_KEY', env('IYZICO_KEY'))),
        'secret_key' => env('IYZICO_SECRET_KEY', env('IYZIPAY_SECRET_KEY', env('IYZICO_SECRET'))),
        // Test: https://sandbox-api.iyzipay.com · Canlı: https://api.iyzipay.com
        'base_url' => rtrim((string) env('IYZICO_BASE_URL', env('IYZIPAY_BASE_URL', env('IYZICO_URL', 'https://sandbox-api.iyzipay.com'))), '/'),
    ],

    // Netgsm (SMS bildirimleri)
    'netgsm' => [
        'usercode' => env('NETGSM_USERCODE', env('NETGSM_USERNAME', env('NETGSM_USER'))),
        'password' => env('NETGSM_PASSWORD', env('NETGSM_PASS')),
        'header' => env('NETGSM_HEADER', env('NETGSM_MSGHEADER', env('NETGSM_SENDER'))),
        'enabled' => (bool) env('NETGSM_ENABLED', true),
        // auto: önce REST v2, olmazsa eski API · rest · legacy
        'api' => env('NETGSM_API', 'auto'),
        // Keşif siparişi geldiğinde ekibe SMS (boşsa gönderilmez)
        'team_phone' => env('NETGSM_TEAM_PHONE'),
    ],

];