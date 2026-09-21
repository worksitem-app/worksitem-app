<?php

namespace Tests\Feature;

use App\Models\Lead;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeadStoreTest extends TestCase
{
    use RefreshDatabase;

    public function test_lead_basarili_kaydedilir(): void
    {
        $response = $this->postJson('/lead-kaydet', [
            'name' => 'Ahmet Yılmaz',
            'phone' => '0532 123 45 67',
            'email' => 'ahmet@example.com',
            'city' => 'istanbul',
            'district' => 'kadikoy',
            'project_type' => 'konut',
            'contact_consent' => true,
            'marketing_consent' => false,
        ]);

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'message',
            'public_id',
            'quality',
            'sales_ready',
        ]);

        $this->assertDatabaseHas('leads', [
            'phone' => '0532 123 45 67',
            'city' => 'istanbul',
        ]);
    }

    public function test_lead_zorunlu_alanlar_eksikse_422_doner(): void
    {
        $response = $this->postJson('/lead-kaydet', []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['name', 'phone', 'contact_consent']);
    }

    public function test_lead_consent_olmadan_kabul_edilmez(): void
    {
        $response = $this->postJson('/lead-kaydet', [
            'name' => 'Ahmet Yılmaz',
            'phone' => '0532 123 45 67',
            'contact_consent' => false,
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['contact_consent']);
    }

    public function test_lead_telefon_10_karakterden_kisa_olmaz(): void
    {
        $response = $this->postJson('/lead-kaydet', [
            'name' => 'Ahmet Yılmaz',
            'phone' => '0532',
            'contact_consent' => true,
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['phone']);
    }

    public function test_lead_gecersiz_email_kabul_edilmez(): void
    {
        $response = $this->postJson('/lead-kaydet', [
            'name' => 'Ahmet Yılmaz',
            'phone' => '0532 123 45 67',
            'email' => 'gecersiz-email',
            'contact_consent' => true,
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['email']);
    }
}
