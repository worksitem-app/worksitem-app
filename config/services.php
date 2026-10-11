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

];