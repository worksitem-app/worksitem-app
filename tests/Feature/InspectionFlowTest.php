<?php

namespace Tests\Feature;

use App\Models\Lead;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class InspectionFlowTest extends TestCase
{
    use RefreshDatabase;

    // Sunucunun .env'indeki çağrı merkezi numarası testi etkilemesin
    protected function setUp(): void
    {
        parent::setUp();
        config(['worksitem_inspection.phone' => '+90 850 346 4983', 'worksitem_inspection.whatsapp' => '908503464983']);
    }

    private function submitLead(): Lead
    {
        $this->post('/lead-kaydet', [
            'name' => 'Ayşe Demir',
            'phone' => '0532 765 43 21',
            'city' => 'İstanbul',
            'project_type' => 'banyo',
            'contact_consent' => '1',
        ])->assertRedirect(route('lead.thanks'));

        return Lead::query()->latest('id')->firstOrFail();
    }

    private function order(array $over = []): array
    {
        return array_merge(['odeme' => 'kart', 'fatura' => 'bireysel', 'kesif_onay' => '1', 'email' => 'ayse@example.com'], $over);
    }

    public function test_tesekkur_sayfasi_iki_kolon_ve_siparis_penceresi(): void
    {
        $lead = $this->submitLead();

        $this->get(route('lead.thanks'))
            ->assertOk()
            ->assertSee('ty-grid has-offer', false)
            ->assertSee('2.500 TL')
            ->assertSee('+ KDV')
            ->assertSee('3.000 TL')
            ->assertSee('15 gün')
            ->assertSee('Keşif bedelini kabul ediyorum')
            ->assertSee('<dialog class="km" id="kesifModal"', false)
            ->assertSee('Siparişi Onayla ve Öde')
            ->assertSee('Kredi / banka kartı')
            ->assertSee('Havale / EFT')
            ->assertSee(route('legal.kesif_obf'), false)
            ->assertSee(route('legal.kesif_mhs'), false)
            ->assertSee($lead->ref_code);
    }

    public function test_onay_kutusu_olmadan_siparis_verilmez_ve_pencere_hatayla_acilir(): void
    {
        $lead = $this->submitLead();

        $this->from(route('lead.thanks'))->post(route('kesif.accept'), $this->order(['kesif_onay' => null]))
            ->assertRedirect(route('lead.thanks'))
            ->assertSessionHasErrors('kesif_onay');

        $this->assertNull($lead->fresh()->inspection_status);
        $this->get(route('lead.thanks'))->assertSee('data-open-on-load', false);
    }

    public function test_kurumsal_faturada_vergi_bilgisi_zorunlu(): void
    {
        $this->submitLead();

        $this->from(route('lead.thanks'))->post(route('kesif.accept'), $this->order(['fatura' => 'kurumsal', 'vergi_no' => '12']))
            ->assertSessionHasErrors(['fatura_unvan', 'vergi_dairesi', 'vergi_no']);
    }

    public function test_kart_siparisi_kaydedilir_bildirim_gider_ve_odeme_sayfasi_acilir(): void
    {
        Mail::fake();
        $lead = $this->submitLead();

        $this->post(route('kesif.accept'), $this->order(['kesif_erken' => '1']))
            ->assertRedirect(route('kesif.payment'));

        $lead->refresh();
        $this->assertSame('kabul', $lead->inspection_status);
        $this->assertSame('kart', $lead->inspection_payment_method);
        $this->assertSame(2500, (int) $lead->inspection_fee);
        $this->assertTrue($lead->inspection_data['consents']['early_performance']);
        $this->assertSame(3000, $lead->inspection_data['amounts']['total']);

        $this->get(route('kesif.payment'))
            ->assertOk()
            ->assertSee('Kartla güvenli ödeme')
            ->assertSee('https://wa.me/908503464983?text=', false)
            ->assertSee(rawurlencode($lead->public_id), false)
            ->assertSee('tel:+908503464983', false)
            ->assertSee('3.000 TL')
            ->assertSee(route('kesif.contract_pdf'), false)
            ->assertSee('noindex', false);

        // İkinci gönderim kaydı değiştirmez
        $first = $lead->inspection_accepted_at;
        $this->post(route('kesif.accept'), $this->order(['odeme' => 'havale']))->assertRedirect(route('kesif.payment'));
        $this->assertEquals($first, $lead->fresh()->inspection_accepted_at);
        $this->assertSame('kart', $lead->fresh()->inspection_payment_method);
    }

    public function test_havale_siparisinde_iban_ve_kurumsal_fatura(): void
    {
        config(['worksitem_company.iban' => 'TR00 0000 0000 0000 0000 0000 00', 'worksitem_company.iban_holder' => 'Kainta Test']);
        $lead = $this->submitLead();

        $this->post(route('kesif.accept'), $this->order([
            'odeme' => 'havale', 'fatura' => 'kurumsal', 'fatura_unvan' => 'Örnek Yapı A.Ş.', 'vergi_dairesi' => 'Ümraniye', 'vergi_no' => '1234567890',
        ]))->assertRedirect(route('kesif.payment'));

        $this->assertSame('kurumsal', $lead->fresh()->inspection_data['invoice']['type']);
        $this->get(route('kesif.payment'))
            ->assertSee('Havale / EFT ile ödeme')
            ->assertSee('TR00 0000 0000 0000 0000 0000 00')
            ->assertSee('14 günlük süre dolduktan sonra');
    }

    public function test_sozlesme_sayfalari_ve_kisisel_pdf(): void
    {
        $this->get(route('legal.kesif_obf'))->assertOk()
            ->assertSee('Keşif Ön Bilgilendirme Formu')
            ->assertSee('3.000 TL')
            ->assertSee('index, follow', false)
            ->assertSee('Cayma Hakkı');
        $this->get(route('legal.kesif_mhs'))->assertOk()->assertSee('Madde 15')->assertSee('Ek-1');

        $lead = $this->submitLead();
        $this->post(route('kesif.accept'), $this->order());

        $this->get(route('legal.kesif_mhs'))->assertOk()
            ->assertSee('Ayşe Demir')
            ->assertSee('noindex, nofollow', false);

        $pdf = $this->get(route('kesif.contract_pdf'));
        $pdf->assertOk();
        $this->assertStringContainsString('application/pdf', $pdf->headers->get('Content-Type'));
        $this->assertStringContainsString($lead->public_id, (string) $pdf->headers->get('Content-Disposition'));
    }

    public function test_oturumsuz_siparis_ve_odeme_sayfasi_kullanilamaz(): void
    {
        $this->post(route('kesif.accept'), $this->order())->assertRedirect(route('lead.thanks'));
        $this->get(route('kesif.payment'))->assertRedirect(route('lead.thanks'));
        $this->get(route('lead.thanks'))->assertOk()->assertDontSee('Keşif bedelini kabul ediyorum');
    }
}
