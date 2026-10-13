<?php

namespace App\Console\Commands;

use App\Services\Payments\IyzicoClient;
use App\Services\Sms\NetgsmSms;
use Illuminate\Console\Command;

/**
 * Entegrasyon kontrolü (şifreleri EKRANA YAZMAZ):
 *   php artisan app:integrations                 → hangi ayarlar dolu
 *   php artisan app:integrations --iyzico        → iyzico'da 1 TL'lik deneme ödeme sayfası açar (ödeme yapılmazsa para çekilmez)
 *   php artisan app:integrations --sms=05XXXXXXXXX → Netgsm ile deneme SMS'i gönderir
 */
class IntegrationsCheck extends Command
{
    protected $signature = 'app:integrations {--iyzico : iyzico deneme ödeme sayfası oluştur} {--sms= : Deneme SMS gönderilecek cep numarası}';

    protected $description = 'GA4, Search Console, iyzico, Netgsm, e-posta ve şirket bilgisi ayarlarını kontrol eder';

    public function handle(IyzicoClient $iyzico, NetgsmSms $sms): int
    {
        $mask = fn (?string $v) => blank($v) ? '<fg=red>BOŞ</>' : '<fg=green>dolu</> ('.mb_strlen((string) $v).' karakter)';
        $yes = fn (bool $b) => $b ? '<fg=green>evet</>' : '<fg=red>hayır</>';

        $this->line('<options=bold>Ortam</>: '.app()->environment().' · '.config('app.url'));
        $this->newLine();
        $this->line('<options=bold>Google</>');
        $ga = (string) config('services.ga4.measurement_id');
        $this->line('  GA4 ölçüm kimliği ......... '.($ga === '' ? '<fg=red>BOŞ</>' : (preg_match('/^G-[A-Z0-9]{4,20}$/', $ga) ? "<fg=green>$ga</>" : "<fg=red>geçersiz: $ga</>")));
        $this->line('  Search Console HTML etiketi '.$mask(config('services.google.site_verification')).' (DNS ile doğrulandıysa boş kalabilir)');

        $this->newLine();
        $this->line('<options=bold>iyzico</>');
        $this->line('  API key ................... '.$mask(config('services.iyzico.api_key')));
        $this->line('  Secret key ................ '.$mask(config('services.iyzico.secret_key')));
        $this->line('  Adres ..................... '.config('services.iyzico.base_url').($iyzico->isSandbox() ? ' <fg=yellow>(TEST / sandbox)</>' : ' <fg=green>(CANLI)</>'));
        $this->line('  Keşifte kartla ödeme açık . '.$yes((bool) config('worksitem_inspection.iyzico_enabled')).' (IYZICO_KESIF_ENABLED)');

        $this->newLine();
        $this->line('<options=bold>Netgsm</>');
        $this->line('  Kullanıcı kodu ............ '.$mask(config('services.netgsm.usercode')));
        $this->line('  Şifre ..................... '.$mask(config('services.netgsm.password')));
        $this->line('  Mesaj başlığı ............. '.(blank(config('services.netgsm.header')) ? '<fg=red>BOŞ</>' : config('services.netgsm.header')));
        $this->line('  Ekip SMS numarası ......... '.$mask(config('services.netgsm.team_phone')));
        try {
            $ip = trim((string) \Illuminate\Support\Facades\Http::timeout(5)->get('https://api.ipify.org')->body());
        } catch (\Throwable) {
            $ip = 'bulunamadı';
        }
        $this->line('  Sunucu çıkış IP adresi .... '.$ip.' (Netgsm/iyzico IP kısıtı varsa bu adres izinli olmalı)');

        $this->newLine();
        $this->line('<options=bold>E-posta ve şirket</>');
        $this->line('  MAIL_MAILER ............... '.config('mail.default').' · bildirim: '.config('worksitem_leads.notification_email'));
        foreach (['mersis' => 'MERSİS', 'tax_office' => 'Vergi dairesi', 'tax_no' => 'Vergi no', 'kep' => 'KEP', 'iban' => 'IBAN'] as $k => $label) {
            $this->line('  '.str_pad($label.' ', 26, '.').' '.$mask(config("worksitem_company.$k")));
        }

        $code = self::SUCCESS;

        if ($this->option('iyzico')) {
            $this->newLine();
            if (! $iyzico->configured()) {
                $this->error('iyzico anahtarları eksik.');

                return self::FAILURE;
            }
            $bin = $iyzico->binCheck();
            ($bin['status'] ?? '') === 'success'
                ? $this->info('iyzico kimlik doğrulama: TAMAM (anahtarlar ve imza doğru)')
                : $this->error('iyzico kimlik doğrulama HATA: '.($bin['errorCode'] ?? '').' '.($bin['errorMessage'] ?? ''));
            $id = 'TEST-'.now()->format('YmdHis');
            $addr = ['contactName' => 'Deneme Kullanici', 'city' => 'Istanbul', 'country' => 'Turkey', 'address' => 'Umraniye Istanbul'];
            $res = $iyzico->initialize([
                'locale' => 'tr', 'conversationId' => $id, 'price' => '1.0', 'paidPrice' => '1.0', 'currency' => 'TRY',
                'basketId' => $id, 'paymentGroup' => 'PRODUCT', 'callbackUrl' => route('kesif.iyzico.callback'), 'enabledInstallments' => [1],
                'buyer' => ['id' => $id, 'name' => 'Deneme', 'surname' => 'Kullanici', 'gsmNumber' => '+905350000000', 'email' => 'test@worksitem.com',
                    'identityNumber' => '11111111111', 'registrationAddress' => 'Umraniye Istanbul', 'ip' => '85.34.78.112', 'city' => 'Istanbul', 'country' => 'Turkey'],
                'shippingAddress' => $addr, 'billingAddress' => $addr,
                'basketItems' => [['id' => 'KESIF-TEST', 'name' => 'Baglanti testi', 'category1' => 'Hizmet', 'itemType' => 'VIRTUAL', 'price' => '1.0']],
            ]);
            if (($res['status'] ?? '') === 'success') {
                $this->info('iyzico bağlantısı ÇALIŞIYOR. Deneme ödeme sayfası: '.($res['paymentPageUrl'] ?? '-'));
                $this->line($iyzico->isSandbox() ? '  (Sandbox: test kartıyla ödeme yapabilirsiniz, gerçek para çekilmez.)' : '  <fg=yellow>CANLI anahtar: bu sayfada ödeme yaparsanız 1 TL gerçekten çekilir. Ödeme yapmanız gerekmez.</>');
            } else {
                $this->error('iyzico ödeme formu HATA: '.($res['errorCode'] ?? '').' '.($res['errorMessage'] ?? 'bilinmiyor').(isset($res['errorGroup']) ? ' ['.$res['errorGroup'].']' : ''));
                $code = self::FAILURE;
            }
        }

        if ($phone = $this->option('sms')) {
            $this->newLine();
            $r = $sms->send($phone, 'Worksitem deneme SMS: Netgsm entegrasyonu çalışıyor. '.now()->format('d.m.Y H:i'));
            $r['ok']
                ? $this->info('SMS GÖNDERİLDİ (jobid '.$r['jobid'].')')
                : $this->error('SMS HATA '.($r['code'] ?? '').': '.$r['message']);
            $code = $r['ok'] ? $code : self::FAILURE;
        }

        return $code;
    }
}
