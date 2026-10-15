<?php

namespace App\Services\Push;

use App\Models\Firm;
use App\Models\PushSubscription;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Web bildirimi (VAPID, RFC 8292) — harici paket olmadan.
 *
 * İçeriksiz ("dürtme") bildirim gönderilir: şifreleme gerekmez. Servis çalışanı bildirimi alınca
 * /firma-paneli/bildirim adresinden son mesajı çeker ve gösterir. Mesaj, abonelik satırında bekler.
 */
class WebPush
{
    public static function enabled(): bool
    {
        return (bool) config('worksitem_app.push.enabled') && self::publicKey() && self::privatePem();
    }

    public static function publicKey(): ?string
    {
        return config('worksitem_app.push.public_key') ?: null;
    }

    private static function privatePem(): ?string
    {
        $b = (string) config('worksitem_app.push.private_pem_b64');

        return $b !== '' ? (base64_decode($b, true) ?: null) : null;
    }

    /** Yeni VAPID anahtar çifti: [public (base64url, 65 bayt), private PEM (base64)] */
    public static function generateKeys(): array
    {
        $key = openssl_pkey_new(['curve_name' => 'prime256v1', 'private_key_type' => OPENSSL_KEYTYPE_EC]);
        openssl_pkey_export($key, $pem);
        $d = openssl_pkey_get_details($key)['ec'];
        $pub = "\x04".str_pad($d['x'], 32, "\0", STR_PAD_LEFT).str_pad($d['y'], 32, "\0", STR_PAD_LEFT);

        return [self::b64url($pub), base64_encode($pem)];
    }

    public static function allowedEndpoint(string $endpoint): bool
    {
        $p = parse_url($endpoint);
        if (($p['scheme'] ?? '') !== 'https' || empty($p['host']) || strlen($endpoint) > 1000) {
            return false;
        }
        $host = strtolower($p['host']);
        foreach ((array) config('worksitem_app.push.hosts') as $h) {
            if ($host === $h || str_ends_with($host, '.'.$h)) {
                return true;
            }
        }

        return false;
    }

    /** Firmanın tüm cihazlarına bildirim; gönderilen cihaz sayısı */
    public function toFirm(Firm $firm, string $title, string $body, string $url): int
    {
        if (! self::enabled()) {
            return 0;
        }
        $n = 0;
        foreach (PushSubscription::query()->where('firm_id', $firm->id)->get() as $s) {
            $n += $this->send($s, $title, $body, $url) ? 1 : 0;
        }

        return $n;
    }

    public function send(PushSubscription $s, string $title, string $body, string $url): bool
    {
        $s->forceFill(['pending' => ['title' => mb_substr($title, 0, 80), 'body' => mb_substr($body, 0, 240), 'url' => $url, 'at' => now()->timestamp]])->save();
        if (! self::allowedEndpoint($s->endpoint)) {
            $s->delete();

            return false;
        }
        try {
            $r = Http::timeout((int) config('worksitem_app.push.timeout', 5))
                ->withHeaders($this->headers($s->endpoint) + ['TTL' => (string) config('worksitem_app.push.ttl', 86400), 'Urgency' => 'high'])
                ->withBody('', 'application/octet-stream')
                ->post($s->endpoint);
        } catch (\Throwable $e) {
            Log::warning('Web bildirimi gönderilemedi', ['error' => $e->getMessage()]);
            $s->increment('failed_count');

            return false;
        }
        if (in_array($r->status(), [404, 410], true)) {
            $s->delete(); // abonelik iptal edilmiş

            return false;
        }
        if (! $r->successful()) {
            $s->increment('failed_count');
            if ($s->failed_count >= 10) {
                $s->delete();
            }

            return false;
        }
        $s->forceFill(['last_sent_at' => now(), 'failed_count' => 0])->save();

        return true;
    }

    /** VAPID Authorization başlığı */
    public function headers(string $endpoint): array
    {
        $p = parse_url($endpoint);
        $aud = $p['scheme'].'://'.$p['host'].(isset($p['port']) ? ':'.$p['port'] : '');
        $jwt = self::jwt(['aud' => $aud, 'exp' => time() + 12 * 3600, 'sub' => (string) config('worksitem_app.push.subject')]);

        return ['Authorization' => 'vapid t='.$jwt.', k='.self::publicKey()];
    }

    public static function jwt(array $claims): string
    {
        $input = self::b64url(json_encode(['typ' => 'JWT', 'alg' => 'ES256'])).'.'.self::b64url(json_encode($claims, JSON_UNESCAPED_SLASHES));
        openssl_sign($input, $der, self::privatePem(), OPENSSL_ALGO_SHA256);

        return $input.'.'.self::b64url(self::derToRaw($der));
    }

    /** ECDSA imzası: DER (SEQUENCE r,s) → 64 bayt r||s */
    public static function derToRaw(string $der): string
    {
        $o = 2;
        if (ord($der[1]) & 0x80) {
            $o += ord($der[1]) & 0x7F;
        }
        $out = '';
        for ($i = 0; $i < 2; $i++) {
            $len = ord($der[$o + 1]);
            $v = ltrim(substr($der, $o + 2, $len), "\0");
            $out .= str_pad($v, 32, "\0", STR_PAD_LEFT);
            $o += 2 + $len;
        }

        return $out;
    }

    public static function b64url(string $s): string
    {
        return rtrim(strtr(base64_encode($s), '+/', '-_'), '=');
    }
}
