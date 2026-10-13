<?php

namespace Tests\Feature;

use App\Models\Lead;
use App\Services\Sms\PhoneOtp;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request as HttpRequest;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class CustomerSmsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['services.netgsm.usercode' => '8501234567', 'services.netgsm.password' => 'x', 'services.netgsm.header' => 'worksitem']);
        Http::fake(['api.netgsm.com.tr/*' => Http::response(['code' => '00', 'jobid' => 'J1'])]);
    }

    private function sent(string $needle): bool
    {
        return Http::recorded()->contains(fn ($p) => str_contains((string) (json_decode($p[0]->body(), true)['messages'][0]['msg'] ?? ''), $needle));
    }

    public function test_teklif_talebinde_musteriye_sms_gider_tekrarda_gitmez(): void
    {
        $this->postJson('/lead-kaydet', ['name' => 'Ayşe Demir', 'phone' => '0532 765 43 21', 'contact_consent' => true])->assertStatus(201);
        $lead = Lead::query()->firstOrFail();
        Http::assertSent(fn (HttpRequest $r) => $r['messages'][0]['no'] === '5327654321' && str_contains($r['messages'][0]['msg'], 'Talebiniz alındı ('.$lead->ref_code.')'));

        $this->postJson('/lead-kaydet', ['name' => 'Ayşe Demir', 'phone' => '0532 765 43 21', 'contact_consent' => true]);
        Http::assertSentCount(1);
    }

    public function test_randevu_girilince_sms_ve_bir_gun_once_hatirlatma(): void
    {
        $lead = Lead::query()->create(['name' => 'Ali Kaya', 'phone' => '05321112233', 'contact_consent' => true, 'status' => 'yeni']);
        $at = now('Europe/Istanbul')->addDay()->setTime(14, 30)->setTimezone(config('app.timezone'));

        $lead->update(['inspection_appointment_at' => $at, 'inspection_address' => 'Bağdat Cd. No 5, Kadıköy']);
        $this->assertTrue($this->sent("14:30"));
        $this->assertTrue($this->sent('Bağdat Cd'));

        $this->artisan('sms:appointment-reminders')->expectsOutput('1 hatırlatma gönderildi.')->assertSuccessful();
        $this->assertTrue($this->sent('hatırlatma: Yarın saat 14:30'));
        $this->assertNotNull($lead->fresh()->inspection_reminder_sent_at);

        // ikinci çalıştırmada tekrar göndermez
        $this->artisan('sms:appointment-reminders')->expectsOutput('0 hatırlatma gönderildi.');
    }

    public function test_tek_seferlik_kod_dogrulanir_ve_sinirlanir(): void
    {
        $otp = app(PhoneOtp::class);
        $this->assertTrue($otp->send('05327654321')['ok']);

        $msg = Http::recorded()->last()[0]['messages'][0]['msg'];
        preg_match('/(\d{6})/', $msg, $m);

        $this->assertFalse($otp->verify('05327654321', '000000'));
        $this->assertTrue($otp->verify('0532 765 43 21', $m[1]));
        $this->assertFalse($otp->verify('05327654321', $m[1]), 'kod tek kullanımlık');

        $otp->send('05327654321');
        $otp->send('05327654321');
        $this->assertFalse($otp->send('05327654321')['ok'], '10 dakikada en fazla 3 kod');
    }

    public function test_bes_yanlis_denemede_kod_iptal(): void
    {
        $otp = app(PhoneOtp::class);
        $otp->send('05327654321');
        preg_match('/(\d{6})/', Http::recorded()->last()[0]['messages'][0]['msg'], $m);
        for ($i = 0; $i < 5; $i++) {
            $otp->verify('05327654321', '111111');
        }
        $this->assertFalse($otp->verify('05327654321', $m[1]));
    }
}
