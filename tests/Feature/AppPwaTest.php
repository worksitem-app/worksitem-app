<?php

namespace Tests\Feature;

use App\Models\Firm;
use App\Models\JobRequest;
use App\Models\PushSubscription;
use App\Services\Push\WebPush;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class AppPwaTest extends TestCase
{
    use RefreshDatabase;

    private const EP = 'https://fcm.googleapis.com/fcm/send/abc123';

    protected function setUp(): void
    {
        parent::setUp();
        Mail::fake();
        [$pub, $priv] = WebPush::generateKeys();
        config([
            'services.netgsm.usercode' => '8501234567', 'services.netgsm.password' => 'x', 'services.netgsm.header' => 'worksitem',
            'worksitem_market.enabled' => true, 'worksitem_market.free_until' => '',
            'worksitem_app.push.public_key' => $pub, 'worksitem_app.push.private_pem_b64' => $priv,
        ]);
        Http::fake([
            'api.netgsm.com.tr/*' => Http::response(['code' => '00', 'jobid' => 'J1']),
            'fcm.googleapis.com/*' => Http::response('', 201),
        ]);
    }

    private function firm(string $phone = '5321110001'): Firm
    {
        return Firm::query()->create([
            'name' => 'Alfa Yapı', 'type' => 'tadilat', 'legal_type' => 'sirket', 'legal_name' => 'Alfa Yapı Ltd. Şti.', 'tax_no' => '1234567890', 'tax_office' => 'Kadıköy',
            'efatura' => 'yes', 'invoice_city' => 'İstanbul', 'invoice_district' => 'Kadıköy', 'invoice_address' => 'Moda Cad. No: 1',
            'trades' => ['ince-insaat'], 'cities' => ['İstanbul'], 'contact_name' => 'Ali Veli', 'phone' => $phone, 'email' => 'alfa@example.com',
            'status' => 'approved', 'approved_at' => now(), 'tax_verified_at' => now(),
        ]);
    }

    private function code(): string
    {
        $last = collect(Http::recorded())->filter(fn ($p) => str_contains($p[0]->url(), 'netgsm'))->last();
        preg_match('/(\d{6})/', $last[0]['messages'][0]['msg'] ?? '', $m);

        return $m[1];
    }

    private function login(Firm $f): void
    {
        Cache::flush();
        $this->postJson(route('market.panel.code'), ['phone' => '0'.$f->phone])->assertOk();
        $this->post(route('market.panel.verify'), ['phone' => '0'.$f->phone, 'code' => $this->code()])->assertRedirect();
    }

    public function test_manifest_servis_calisani_ve_cevrimdisi_sayfa(): void
    {
        $m = json_decode((string) file_get_contents(public_path('site.webmanifest')), true);
        $this->assertSame('standalone', $m['display']);
        $this->assertSame('Worksitem', $m['short_name']);
        $this->assertTrue(collect($m['icons'])->contains(fn ($i) => $i['purpose'] === 'maskable' && is_file(public_path(ltrim($i['src'], '/')))));
        $sw = (string) file_get_contents(public_path('sw.js'));
        $this->assertStringContainsString("req.mode === 'navigate'", $sw);
        $this->assertStringContainsString("'/cevrimdisi'", $sw);
        $this->assertStringNotContainsString('caches.match(req).then', $sw); // sayfalar önbellekten verilmez

        $this->get('/cevrimdisi')->assertOk()->assertSee('İnternet bağlantısı yok');
        $this->get('/')->assertOk()->assertSee('serviceWorker.register("/sw.js")', false)->assertSee('rel="manifest"', false);
        $this->get('/araclar')->assertOk()->assertSee('/sw.js', false)->assertSee('apple-mobile-web-app-title', false);
    }

    public function test_assetlinks_yalniz_parmak_izi_varken_dolu(): void
    {
        $this->getJson('/.well-known/assetlinks.json')->assertOk()->assertExactJson([]);
        config(['worksitem_app.twa.sha256' => ['AA:BB'], 'worksitem_app.twa.package' => 'com.worksitem.app']);
        $this->getJson('/.well-known/assetlinks.json')->assertOk()
            ->assertJsonPath('0.target.package_name', 'com.worksitem.app')
            ->assertJsonPath('0.target.sha256_cert_fingerprints.0', 'AA:BB');
    }

    public function test_vapid_imzasi_dogrulanir(): void
    {
        $jwt = WebPush::jwt(['aud' => 'https://fcm.googleapis.com', 'exp' => time() + 3600, 'sub' => 'mailto:info@worksitem.com']);
        [$h, $p, $sig] = explode('.', $jwt);
        $raw = base64_decode(strtr($sig, '-_', '+/'));
        $this->assertSame(64, strlen($raw));
        $int = fn ($v) => (ord(($v = ltrim($v, "\0"))[0] ?? "\0") & 0x80 ? "\0".$v : $v);
        [$r, $s] = [$int(substr($raw, 0, 32)), $int(substr($raw, 32))];
        $der = "\x30".chr(4 + strlen($r) + strlen($s))."\x02".chr(strlen($r)).$r."\x02".chr(strlen($s)).$s;
        $pem = base64_decode((string) config('worksitem_app.push.private_pem_b64'));
        $pubPem = openssl_pkey_get_details(openssl_pkey_get_private($pem))['key'];
        $this->assertSame(1, openssl_verify("$h.$p", $der, $pubPem, OPENSSL_ALGO_SHA256));
        $this->assertSame(65, strlen(base64_decode(strtr(WebPush::publicKey(), '-_', '+/'))));
    }

    public function test_abonelik_giris_ister_ve_yabanci_adresi_reddeder(): void
    {
        $f = $this->firm();
        $this->postJson(route('app.push.subscribe'), ['endpoint' => self::EP])->assertStatus(401);
        $this->login($f);
        $this->postJson(route('app.push.subscribe'), ['endpoint' => 'https://evil.example.com/x'])->assertStatus(422);
        $this->postJson(route('app.push.subscribe'), ['endpoint' => 'http://fcm.googleapis.com/x'])->assertStatus(422);
        $this->postJson(route('app.push.subscribe'), ['endpoint' => self::EP, 'keys' => ['p256dh' => 'k', 'auth' => 'a']])->assertOk()->assertJsonPath('ok', true);
        $this->assertSame($f->id, PushSubscription::query()->sole()->firm_id);
        $this->get(route('market.panel'))->assertOk()->assertSee('Yeni işleri kaçırmayın')->assertSee('Bildirimleri aç');

        $this->postJson(route('app.push.unsubscribe'), ['endpoint' => self::EP])->assertOk();
        $this->assertSame(0, PushSubscription::query()->count());
    }

    public function test_yeni_iste_bildirim_gider_ve_mesaj_cekilir(): void
    {
        $f = $this->firm();
        PushSubscription::query()->create(['firm_id' => $f->id, 'endpoint' => self::EP, 'endpoint_hash' => PushSubscription::hash(self::EP)]);

        Cache::flush();
        $this->postJson(route('market.request.code'), ['phone' => '05327654321'])->assertOk();
        $this->post(route('market.request.store'), [
            'tool' => 'boya-hesaplayici', 'trade' => 'ince-insaat', 'title' => 'Daire boyası, 100 m²', 'est_low' => 49000, 'est_high' => 69000,
            'city' => 'İstanbul', 'district' => 'Kadıköy', 'description' => 'Duvar ve tavan.', 'start_pref' => '1ay',
            'name' => 'Ayşe Demir', 'phone' => '0532 765 43 21', 'code' => $this->code(), 'terms' => '1', 'kvkk' => '1',
        ])->assertRedirect();
        $job = JobRequest::query()->firstOrFail();

        $push = collect(Http::recorded())->first(fn ($p) => str_starts_with($p[0]->url(), self::EP));
        $this->assertNotNull($push, 'bildirim gönderilmedi');
        $this->assertStringStartsWith('vapid t=', $push[0]->header('Authorization')[0]);
        $this->assertSame('', $push[0]->body()); // içeriksiz bildirim (müşteri bilgisi taşımaz)

        $this->getJson(route('app.push.message', ['e' => PushSubscription::hash(self::EP)]))->assertOk()
            ->assertJsonPath('url', route('market.panel.job', $job))
            ->assertJsonPath('title', 'Yeni iş: '.$job->tradeLabel());
        $this->getJson(route('app.push.message', ['e' => 'yanlis']))->assertOk()->assertJsonPath('title', 'Worksitem');
        $this->assertStringNotContainsString('Ayşe', json_encode(PushSubscription::query()->sole()->pending));
    }

    public function test_iptal_edilmis_abonelik_silinir(): void
    {
        $ep = 'https://updates.push.services.mozilla.com/wpush/v2/gone';
        Http::fake(['updates.push.services.mozilla.com/*' => Http::response('', 410)]);
        $f = $this->firm();
        PushSubscription::query()->create(['firm_id' => $f->id, 'endpoint' => $ep, 'endpoint_hash' => PushSubscription::hash($ep)]);
        $this->assertSame(0, app(WebPush::class)->toFirm($f, 'T', 'B', '/firma-paneli'));
        $this->assertSame(0, PushSubscription::query()->count());
    }

    public function test_anahtar_komutu_env_yazar_ve_tekrar_dokunmaz(): void
    {
        $env = base_path('.env');
        $orig = is_file($env) ? file_get_contents($env) : null;
        try {
            file_put_contents($env, "APP_NAME=Test\n");
            $this->artisan('push:keys --write')->assertSuccessful();
            $once = file_get_contents($env);
            $this->assertMatchesRegularExpression('/^VAPID_PUBLIC_KEY=[A-Za-z0-9_-]{80,}$/m', $once);
            $this->assertMatchesRegularExpression('/^VAPID_PRIVATE_PEM_B64=.+$/m', $once);
            $this->artisan('push:keys --write')->expectsOutputToContain('zaten var')->assertSuccessful();
            $this->assertSame($once, file_get_contents($env));
        } finally {
            $orig === null ? @unlink($env) : file_put_contents($env, $orig);
            foreach (glob($env.'.bak-push-*') ?: [] as $b) {
                @unlink($b);
            }
        }
    }

    public function test_uygulama_sayfasi_ve_yukleme_bandi(): void
    {
        $this->get('/uygulama')->assertOk()->assertSee('Nasıl yüklenir?')->assertSee('Ana Ekrana Ekle')
            ->assertDontSee("Google Play'den indir", false);
        config(['worksitem_app.twa.play_url' => 'https://play.google.com/store/apps/details?id=com.worksitem.app']);
        $this->get('/uygulama')->assertOk()->assertSee("Google Play'den indir", false);

        $home = $this->get('/')->assertOk();
        $home->assertSee('id="wsAppBanner"', false)->assertSee('data-play="https://play.google.com', false);
        $this->assertSame(1, substr_count($home->getContent(), 'id="wsAppBanner"'));
        $this->get('/araclar/boya-hesaplayici')->assertOk()->assertSee('id="wsAppBanner"', false);
        $this->get('/sitemap.xml')->assertSee('https://worksitem.com/uygulama');
        $this->get('/llms.txt')->assertSee('https://worksitem.com/uygulama');
    }

    public function test_firma_panelinde_band_yerine_kart(): void
    {
        $this->login($this->firm());
        $html = $this->get(route('market.panel'))->assertOk()->getContent();
        $this->assertStringContainsString('id="wsApp"', $html);
        $this->assertStringNotContainsString('id="wsAppBanner"', $html);
    }
}
