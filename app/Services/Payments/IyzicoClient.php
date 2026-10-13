<?php

namespace App\Services\Payments;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

/**
 * iyzico Checkout Form (ortak ödeme sayfası) — SDK'sız, IYZWSv2 (HMAC-SHA256) imzalı.
 * Kart bilgisi hiçbir zaman bizim sunucumuza gelmez; ödeme iyzico'nun sayfasında yapılır.
 */
class IyzicoClient
{
    public const INIT_PATH = '/payment/iyzipos/checkoutform/initialize/auth/ecom';

    public const RETRIEVE_PATH = '/payment/iyzipos/checkoutform/auth/ecom/detail';

    public function configured(): bool
    {
        return filled(config('services.iyzico.api_key')) && filled(config('services.iyzico.secret_key'));
    }

    public function isSandbox(): bool
    {
        return str_contains((string) config('services.iyzico.base_url'), 'sandbox');
    }

    /** @return array<string, mixed> iyzico yanıtı (status, token, paymentPageUrl, errorMessage ...) */
    public function initialize(array $payload): array
    {
        return $this->post(self::INIT_PATH, $payload);
    }

    /** @return array<string, mixed> (status, paymentStatus, paidPrice, basketId, paymentId ...) */
    public function retrieve(string $token, ?string $conversationId = null): array
    {
        return $this->post(self::RETRIEVE_PATH, array_filter([
            'locale' => 'tr',
            'conversationId' => $conversationId,
            'token' => $token,
        ]));
    }

    /** Kimlik doğrulama denemesi (ödeme oluşturmaz): BIN sorgusu */
    public function binCheck(string $bin = '554960'): array
    {
        return $this->post('/payment/bin/check', ['locale' => 'tr', 'conversationId' => 'bin-'.now()->format('His'), 'binNumber' => $bin]);
    }

    /** iyzico tutar biçimi: "3000.0" gibi noktalı ondalık */
    public static function price(float|int $amount): string
    {
        $s = rtrim(rtrim(number_format((float) $amount, 2, '.', ''), '0'), '.');

        return str_contains($s, '.') ? $s : $s.'.0';
    }

    /** @return array<string, mixed> */
    private function post(string $path, array $payload): array
    {
        $body = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $rnd = (string) now()->getTimestampMs().Str::random(8);
        $signature = hash_hmac('sha256', $rnd.$path.$body, (string) config('services.iyzico.secret_key'));
        $auth = base64_encode('apiKey:'.config('services.iyzico.api_key').'&randomKey:'.$rnd.'&signature:'.$signature);

        try {
            $response = Http::timeout(20)
                ->withHeaders([
                    'Authorization' => 'IYZWSv2 '.$auth,
                    'x-iyzi-rnd' => $rnd,
                    'Accept' => 'application/json',
                ])
                ->withBody($body, 'application/json')
                ->post(config('services.iyzico.base_url').$path);

            $json = $response->json();

            return is_array($json) ? $json : ['status' => 'failure', 'errorMessage' => 'Geçersiz yanıt (HTTP '.$response->status().')'];
        } catch (\Throwable $e) {
            return ['status' => 'failure', 'errorMessage' => 'Bağlantı hatası: '.$e->getMessage()];
        }
    }
}
