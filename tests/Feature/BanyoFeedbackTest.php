<?php

namespace Tests\Feature;

use App\Services\BathroomEstimator;
use App\Services\Sms\PhoneOtp;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class BanyoFeedbackTest extends TestCase
{
    use RefreshDatabase;

    public function test_secimler_varsayilan_isaretli_gelmez_ve_etiketler_dogru(): void
    {
        $html = $this->get('/araclar/banyo-maliyet-hesaplayici')->assertOk()
            ->assertSee('>Yükseklik</label>', false)
            ->assertDontSee('>Tavan</label>', false)
            ->assertSee('>İlerle</button>', false)
            ->assertDontSee('Sonucu ve dökümü gör')
            ->assertSee('<input type="hidden" id="quality" value="">', false)
            ->assertSee('<input type="hidden" id="shower" value="">', false)
            ->getContent();

        // Kalite, kapsam ve duş radyoları ile ekstra kutuları işaretsiz
        $this->assertDoesNotMatchRegularExpression('/name="r_(scope|quality|shower)"[^>]*checked/', $html);
        $this->assertDoesNotMatchRegularExpression('/id="ex_[a-z]+" checked/', $html);
    }

    public function test_dus_secilmezse_hesaba_katilmaz(): void
    {
        $e = app(BathroomEstimator::class);
        $base = ['type' => 'standart', 'w' => 2, 'l' => 2.5, 'h' => 2.6, 'scope' => 'yuzey', 'quality' => 'orta', 'extras' => []];
        $none = $e->estimate($base + ['shower' => '']);
        $kabin = $e->estimate($base + ['shower' => 'kabin']);
        $this->assertSame(0, (int) $none['items']['dus']['mid']);
        $this->assertGreaterThan(0, (int) $kabin['items']['dus']['mid']);
        $this->assertSame(0, (int) $none['items']['tesisat']['mid'], 'yüzey yenilemede tesisat yok');
    }

    public function test_fiyat_tarihi_gun_ile_yazilir(): void
    {
        $label = app(BathroomEstimator::class)->pricesLabel();
        $this->assertMatchesRegularExpression('/^(\d{1,2} )?\p{L}+ \d{4}$/u', $label);
    }

    public function test_sms_kod_maliyet_korumasi_gunluk_numara_siniri(): void
    {
        config(['services.netgsm.usercode' => '8501234567', 'services.netgsm.password' => 'x', 'services.netgsm.header' => 'worksitem',
            'worksitem_sms.otp.max_sends' => 100, 'worksitem_sms.otp.max_per_phone_day' => 2, 'worksitem_sms.otp.max_per_ip_day' => 100]);
        Http::fake(['api.netgsm.com.tr/*' => Http::response(['code' => '00', 'jobid' => 'J1'])]);
        $otp = app(PhoneOtp::class);
        $this->assertTrue($otp->send('05327654321')['ok']);
        $this->assertTrue($otp->send('05327654321')['ok']);
        $this->assertFalse($otp->send('05327654321')['ok']);
        Http::assertSentCount(2);
    }

    public function test_sms_kod_site_geneli_gunluk_tavan(): void
    {
        config(['services.netgsm.usercode' => '8501234567', 'services.netgsm.password' => 'x', 'services.netgsm.header' => 'worksitem',
            'worksitem_sms.otp.global_day' => 1, 'worksitem_sms.otp.max_per_ip_day' => 100]);
        Http::fake(['api.netgsm.com.tr/*' => Http::response(['code' => '00', 'jobid' => 'J1'])]);
        $otp = app(PhoneOtp::class);
        $this->assertTrue($otp->send('05327654321')['ok']);
        $this->assertFalse($otp->send('05331112233')['ok']);
    }

    public function test_teklif_sms_metninde_whatsapp_destek(): void
    {
        $this->assertStringContainsString('WhatsApp destek: 0850 346 49 83', config('worksitem_sms.lead_received.text'));
    }
}
