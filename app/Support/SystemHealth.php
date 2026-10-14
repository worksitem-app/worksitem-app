<?php

namespace App\Support;

use App\Console\Commands\Preflight;
use App\Services\BathroomEstimator;
use App\Services\FxRates;
use App\Services\Payments\IyzicoClient;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

/**
 * Kontrol merkezi için entegrasyon ve sistem durumu (sırları göstermez, yalnızca var/yok ve tarih).
 * Her satır: [grup, etiket, durum ok|warn|fail|off, açıklama]
 */
class SystemHealth
{
    /** @return list<array{group:string,label:string,state:string,detail:string}> */
    public static function checks(): array
    {
        $rows = [];
        $add = function (string $group, string $label, string $state, string $detail) use (&$rows) {
            $rows[] = compact('group', 'label', 'state', 'detail');
        };

        // ── Satış ve ödeme
        $iyzicoKeys = app(IyzicoClient::class)->configured();
        $iyzicoOn = (bool) config('worksitem_inspection.iyzico_enabled');
        $add('Ödeme', 'iyzico kartla ödeme', $iyzicoOn && $iyzicoKeys ? 'ok' : ($iyzicoKeys ? 'warn' : 'fail'),
            $iyzicoOn ? ($iyzicoKeys ? 'Açık' : 'Açık ama anahtar eksik') : ($iyzicoKeys ? 'Anahtar var, kapalı (Checkout Form iyzico\'dan açtırılmalı)' : 'Anahtar yok'));
        $add('Ödeme', 'Havale / EFT (IBAN)', filled(config('worksitem_company.iban')) ? 'ok' : 'warn',
            filled(config('worksitem_company.iban')) ? 'IBAN tanımlı' : 'IBAN tanımlı değil');

        // ── İletişim
        $sms = filled(config('services.netgsm.usercode')) && filled(config('services.netgsm.password')) && filled(config('services.netgsm.header'));
        $add('İletişim', 'Netgsm SMS', ! config('services.netgsm.enabled') ? 'off' : ($sms ? 'ok' : 'fail'),
            ! config('services.netgsm.enabled') ? 'Kapalı (NETGSM_ENABLED=false)' : ($sms ? 'Bilgiler tanımlı · başlık: '.config('services.netgsm.header') : 'Kullanıcı kodu / şifre / başlık eksik'));
        $mailer = (string) config('mail.default');
        $add('İletişim', 'E-posta bildirimi', in_array($mailer, ['log', 'array'], true) ? 'fail' : 'ok', 'Gönderici: '.$mailer.' → '.config('worksitem_leads.notification_email'));
        $day = now('Europe/Istanbul')->format('Ymd');
        $otpUsed = (int) Cache::get('otp-global:'.$day, 0);
        $otpCap = (int) config('worksitem_sms.otp.global_day', 400);
        $add('İletişim', 'SMS doğrulama kodu (bugün)', $otpUsed >= $otpCap ? 'fail' : ($otpUsed >= $otpCap * 0.8 ? 'warn' : 'ok'), $otpUsed.' / '.$otpCap.' günlük tavan');

        // ── Yapay zekâ
        $add('Yapay zekâ', 'AI harcama kilidi', config('services.ai_spend_enabled') ? 'ok' : 'off',
            config('services.ai_spend_enabled') ? 'Açık — AI özellikleri çalışıyor' : 'Kapalı — AI özellikleri 503 döner (AI_SPEND_ENABLED)');
        $add('Yapay zekâ', 'Günde ücretsiz + kredi sistemi', config('worksitem_ai_credits.enabled') ? 'ok' : 'warn',
            config('worksitem_ai_credits.enabled') ? 'Açık · günde '.config('worksitem_ai_credits.free_per_day').' ücretsiz' : 'Kapalı — AI sınırsız (yalnızca IP sınırı)');
        $add('Yapay zekâ', 'Gemini anahtarı', filled(config('services.gemini.api_key')) ? 'ok' : 'fail', filled(config('services.gemini.api_key')) ? 'Tanımlı' : 'Eksik');
        $add('Yapay zekâ', 'Replicate anahtarı', filled(config('services.replicate.token')) ? 'ok' : 'warn', filled(config('services.replicate.token')) ? 'Tanımlı' : 'Eksik (iç mekân tasarımı yedek görsel döner)');

        // ── Veri tazeliği
        $fx = app(FxRates::class)->current();
        $add('Veri', 'Döviz kuru (TCMB)', $fx ? 'ok' : 'warn', $fx ? 'Son kur: '.($fx['date'] ?? '?') : 'Önbellekte kur yok (php artisan fx:update)');
        $prices = app(BathroomEstimator::class)->config()['prices_date'] ?? null;
        $age = $prices ? (int) Carbon::parse($prices)->diffInDays(now()) : null;
        $add('Veri', 'Banyo fiyatları (Fiyat Merkezi)', $age === null ? 'warn' : ($age > 45 ? 'warn' : 'ok'),
            $prices ? 'Son güncelleme: '.Carbon::parse($prices)->locale('tr')->translatedFormat('j F Y').' ('.$age.' gün önce)' : 'Admin fiyatı yok — dosyadaki varsayılanlar kullanılıyor');

        // ── Altyapı
        $beat = Preflight::lastBeat();
        $add('Altyapı', 'Zamanlayıcı (cron)', $beat > 0 && time() - $beat <= 300 ? 'ok' : 'fail',
            $beat > 0 ? 'Son çalışma: '.Carbon::createFromTimestamp($beat)->locale('tr')->diffForHumans() : 'Hiç çalışmamış — SMS hatırlatma ve kur güncellemesi durur');
        $add('Altyapı', 'Ortam', app()->environment('production') ? 'ok' : 'warn',
            app()->environment().(config('app.debug') ? ' · DEBUG AÇIK' : '').' · '.parse_url((string) config('app.url'), PHP_URL_HOST));
        $mersis = preg_replace('/\D+/', '', (string) config('worksitem_company.mersis'));
        $add('Altyapı', 'Şirket künyesi (sözleşmeler)', strlen($mersis) === 16 && filled(config('worksitem_company.tax_no')) ? 'ok' : 'warn',
            strlen($mersis) === 16 ? 'MERSİS ve vergi bilgisi tamam' : 'MERSİS '.strlen($mersis).' hane (16 olmalı)');

        return $rows;
    }

    /** @return array{ok:int,warn:int,fail:int,off:int} */
    public static function summary(array $rows): array
    {
        $s = ['ok' => 0, 'warn' => 0, 'fail' => 0, 'off' => 0];
        foreach ($rows as $r) {
            $s[$r['state']]++;
        }

        return $s;
    }
}
