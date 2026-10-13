<?php

namespace App\Services\Sms;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;

/**
 * Telefonla tek seferlik doğrulama kodu (firma üyeliği ve AI kredi sistemi için).
 * Kod düz metin saklanmaz (HMAC); 5 dk geçerli; 5 yanlış denemede iptal; 10 dk'da en fazla 3 gönderim.
 */
class PhoneOtp
{
    public function __construct(private NetgsmSms $sms) {}

    /** @return array{ok: bool, message: string} */
    public function send(string $phone, string $purpose = 'giris'): array
    {
        $no = NetgsmSms::normalize($phone);
        if (! $no) {
            return ['ok' => false, 'message' => 'Lütfen geçerli bir cep telefonu numarası yazın.'];
        }

        $limitKey = "otp-send:$no";
        if (RateLimiter::tooManyAttempts($limitKey, (int) config('worksitem_sms.otp.max_sends', 3))) {
            return ['ok' => false, 'message' => 'Çok fazla kod istendi. Lütfen '.ceil(RateLimiter::availableIn($limitKey) / 60).' dakika sonra tekrar deneyin.'];
        }
        // SMS maliyet koruması: IP, numara ve site geneli günlük tavan
        $day = now('Europe/Istanbul')->format('Ymd');
        $ipKey = 'otp-ip:'.$day.':'.sha1((string) request()->ip());
        $phoneDayKey = 'otp-phone:'.$day.':'.$no;
        $globalKey = 'otp-global:'.$day;
        $o = (array) config('worksitem_sms.otp');
        if ((int) Cache::get($ipKey, 0) >= (int) ($o['max_per_ip_day'] ?? 6)
            || (int) Cache::get($phoneDayKey, 0) >= (int) ($o['max_per_phone_day'] ?? 5)) {
            return ['ok' => false, 'message' => 'Bugün için kod gönderme sınırına ulaşıldı. Lütfen yarın tekrar deneyin.'];
        }
        if ((int) Cache::get($globalKey, 0) >= (int) ($o['global_day'] ?? 400)) {
            \Illuminate\Support\Facades\Log::warning('OTP günlük site tavanı doldu', ['tavan' => $o['global_day'] ?? 400]);

            return ['ok' => false, 'message' => 'Doğrulama servisi şu an yoğun. Lütfen daha sonra tekrar deneyin.'];
        }
        RateLimiter::hit($limitKey, 600);
        foreach ([$ipKey, $phoneDayKey, $globalKey] as $k) {
            Cache::add($k, 0, now()->addDay());
            Cache::increment($k);
        }

        $code = (string) random_int(100000, 999999);
        Cache::put($this->key($no, $purpose), ['hash' => $this->hash($no, $code), 'tries' => 0], now()->addMinutes((int) config('worksitem_sms.otp.ttl_minutes', 5)));

        $r = $this->sms->send($no, strtr((string) config('worksitem_sms.otp.text'), [':kod' => $code]));

        return ['ok' => $r['ok'], 'message' => $r['ok'] ? 'Doğrulama kodu gönderildi.' : 'Kod gönderilemedi, lütfen biraz sonra tekrar deneyin.'];
    }

    public function verify(string $phone, string $code, string $purpose = 'giris'): bool
    {
        $no = NetgsmSms::normalize($phone);
        if (! $no) {
            return false;
        }
        $key = $this->key($no, $purpose);
        $entry = Cache::get($key);
        if (! is_array($entry)) {
            return false;
        }

        if (hash_equals($entry['hash'], $this->hash($no, preg_replace('/\D+/', '', $code)))) {
            Cache::forget($key);

            return true;
        }

        $entry['tries']++;
        $entry['tries'] >= (int) config('worksitem_sms.otp.max_attempts', 5)
            ? Cache::forget($key)
            : Cache::put($key, $entry, now()->addMinutes((int) config('worksitem_sms.otp.ttl_minutes', 5)));

        return false;
    }

    private function key(string $no, string $purpose): string
    {
        return "otp:$purpose:$no";
    }

    private function hash(string $no, string $code): string
    {
        return hash_hmac('sha256', $no.'|'.$code, (string) config('app.key'));
    }
}
