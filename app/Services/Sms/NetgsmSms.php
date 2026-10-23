<?php

namespace App\Services\Sms;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Netgsm SMS (REST v2). Bilgilendirme amaçlı (işlemsel) mesajlar için — pazarlama SMS'i GÖNDERMEZ.
 */
class NetgsmSms
{
    public const URL = 'https://api.netgsm.com.tr/sms/rest/v2/send';

    // Eski (form) API — Netgsm panelindeki PHP örnek kodlarının kullandığı uç nokta
    public const LEGACY_URL = 'https://api.netgsm.com.tr/sms/send/get';

    private const ERRORS = [
        '20' => 'Mesaj metni hatalı veya çok uzun',
        '30' => 'Kullanıcı adı / şifre hatalı ya da API erişimi yok (Netgsm panelinde API yetkisi ve IP kısıtını kontrol edin)',
        '40' => 'Mesaj başlığı (gönderici adı) sistemde tanımlı değil',
        '50' => 'Abone hesabıyla İYS kontrollü gönderim yapılamıyor',
        '51' => 'İYS marka bilgisi bulunamadı',
        '60' => 'Hesapta tanımlı JobID bulunamadı',
        '70' => 'Hatalı sorgu: parametreleri kontrol edin',
        '80' => 'Gönderim sınırı aşıldı',
        '85' => 'Mükerrer gönderim sınırı aşıldı (aynı numaraya 1 dakikada 20\'den fazla)',
    ];

    public function configured(): bool
    {
        return (bool) config('services.netgsm.enabled')
            && filled(config('services.netgsm.usercode'))
            && filled(config('services.netgsm.password'))
            && filled(config('services.netgsm.header'));
    }

    /** 05xx, 5xx, +905xx, 905xx → 5xxxxxxxxx; geçersizse null */
    public static function normalize(?string $phone): ?string
    {
        $d = preg_replace('/\D+/', '', (string) $phone);
        if (str_starts_with($d, '90') && strlen($d) === 12) {
            $d = substr($d, 2);
        }
        if (str_starts_with($d, '0') && strlen($d) === 11) {
            $d = substr($d, 1);
        }

        return preg_match('/^5\d{9}$/', $d) ? $d : null;
    }

    /**
     * @return array{ok: bool, code: ?string, jobid: ?string, message: string}
     */
    public function send(string $phone, string $message): array
    {
        $no = self::normalize($phone);
        if (! $no) {
            return ['ok' => false, 'code' => null, 'jobid' => null, 'message' => 'Geçersiz cep telefonu numarası'];
        }
        if (! $this->configured()) {
            return ['ok' => false, 'code' => null, 'jobid' => null, 'message' => 'Netgsm ayarları eksik (.env)'];
        }

        // Son güvenlik tavanı: bir açık ya da hata günde binlerce SMS'e dönüşmesin
        $capKey = 'sms-hard-cap:'.now('Europe/Istanbul')->format('Ymd');
        $cap = (int) config('worksitem_sms.hard_cap_day', 1500);
        if ($cap > 0 && (int) Cache::get($capKey, 0) >= $cap) {
            Log::error('Günlük SMS tavanı doldu, gönderim durduruldu', ['tavan' => $cap]);

            return ['ok' => false, 'code' => null, 'jobid' => null, 'message' => 'Günlük SMS tavanı doldu'];
        }
        Cache::add($capKey, 0, now()->addDay());
        Cache::increment($capKey);

        $mode = (string) config('services.netgsm.api', 'auto');
        $rest = $mode === 'legacy' ? null : $this->sendRest($no, $message);
        if ($rest && ($rest['ok'] || $mode === 'rest')) {
            return $rest;
        }

        // REST v2 başarısızsa (ör. hesapta yalnızca eski API yetkisi varsa) eski uç noktayı dene
        $legacy = $this->sendLegacy($no, $message);
        if (! $legacy['ok'] && $rest) {
            $legacy['message'] .= ' [REST v2: '.($rest['code'] ?? '-').']';
        }

        return $legacy;
    }

    /** @return array{ok: bool, code: ?string, jobid: ?string, message: string} */
    private function sendRest(string $no, string $message): array
    {
        try {
            $response = Http::timeout(15)
                ->withBasicAuth((string) config('services.netgsm.usercode'), (string) config('services.netgsm.password'))
                ->acceptJson()
                ->post(self::URL, [
                    'msgheader' => (string) config('services.netgsm.header'),
                    'encoding' => 'TR',
                    'iysfilter' => '0', // bilgilendirme (işlemsel) mesajı
                    'messages' => [['msg' => $message, 'no' => $no]],
                ]);

            $json = (array) ($response->json() ?? []);
            $code = isset($json['code']) ? (string) $json['code'] : null;
            $jobid = isset($json['jobid']) ? (string) $json['jobid'] : null;
            $ok = $response->successful() && ($code === '00' || ($code === null && $jobid));

            return $this->result($ok, $code, $jobid, $response->status(), $response->body());
        } catch (\Throwable $e) {
            return ['ok' => false, 'code' => null, 'jobid' => null, 'message' => 'Bağlantı hatası: '.$e->getMessage()];
        }
    }

    /** @return array{ok: bool, code: ?string, jobid: ?string, message: string} */
    private function sendLegacy(string $no, string $message): array
    {
        try {
            $response = Http::timeout(15)->asForm()->post(self::LEGACY_URL, [
                'usercode' => (string) config('services.netgsm.usercode'),
                'password' => (string) config('services.netgsm.password'),
                'gsmno' => '90'.$no,
                'message' => $message,
                'msgheader' => (string) config('services.netgsm.header'),
                'filter' => '0',
                'dil' => 'TR',
            ]);

            $body = trim($response->body());
            $json = json_decode($body, true);
            $parts = preg_split('/\s+/', $body) ?: [];
            $code = is_array($json) && isset($json['code']) ? (string) $json['code'] : ($parts[0] ?? null);
            $jobid = is_array($json) ? ($json['jobid'] ?? null) : ($parts[1] ?? null);
            $ok = $response->successful() && in_array($code, ['00', '01', '02'], true);

            return $this->result($ok, $code, $jobid ? (string) $jobid : null, $response->status(), $body);
        } catch (\Throwable $e) {
            return ['ok' => false, 'code' => null, 'jobid' => null, 'message' => 'Bağlantı hatası: '.$e->getMessage()];
        }
    }

    /** @return array{ok: bool, code: ?string, jobid: ?string, message: string} */
    private function result(bool $ok, ?string $code, ?string $jobid, int $http, string $body): array
    {
        if (! $ok) {
            Log::warning('Netgsm SMS gönderilemedi', ['code' => $code, 'http' => $http, 'body' => mb_substr($body, 0, 300)]);
        }

        return [
            'ok' => $ok,
            'code' => $code,
            'jobid' => $jobid,
            'message' => $ok ? 'Gönderildi' : (self::ERRORS[$code] ?? ('HTTP '.$http.': '.mb_substr($body, 0, 200))),
        ];
    }
}
