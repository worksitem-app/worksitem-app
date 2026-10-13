<?php

namespace Tests\Feature;

use App\Filament\Widgets;
use App\Models\Lead;
use App\Models\Sponsor;
use App\Models\ToolStatus;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ControlCenterTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['is_admin' => true, 'name' => 'Kainta Yönetici']);
    }

    public function test_kontrol_merkezi_acilir_ve_tum_paneller_calisir(): void
    {
        $admin = $this->admin();
        Lead::query()->create(['name' => 'Ayşe Yılmaz', 'phone' => '05321112233', 'status' => 'yeni', 'source' => 'banyo_pro_rapor', 'inspection_status' => 'kabul', 'inspection_fee' => 2500,
            'inspection_appointment_at' => now()->addDay(), 'inspection_address' => 'Kadıköy']);
        Sponsor::query()->create(['brand' => 'Örnek', 'headline' => 'Başlık', 'target_url' => 'https://example.com', 'tool_slug' => 'banyo-maliyet-hesaplayici', 'impressions' => 100, 'clicks' => 5, 'contract_amount' => 15000]);

        $this->actingAs($admin)->get('/admin')->assertOk()->assertSee('Kontrol Merkezi');

        Livewire::actingAs($admin)->test(Widgets\ControlCenterHeader::class)
            ->assertSee('Kainta')->assertSee('1 yeni talep aranmayı bekliyor')->assertSee('ödeme bekleniyor')->assertSee('keşif randevusu');
        Livewire::actingAs($admin)->test(Widgets\RevenueOverview::class)->assertSee('Ödeme bekleyen keşif')->assertSee('₺3.000')->assertSee('Yayındaki sponsor');
        Livewire::actingAs($admin)->test(Widgets\UsageOverview::class)->assertSee('SMS doğrulama kodu');
        Livewire::actingAs($admin)->test(Widgets\LeadSourceChart::class)->assertOk();
        Livewire::actingAs($admin)->test(Widgets\UpcomingAppointments::class)->assertSee('Ayşe Yılmaz')->assertSee('Kadıköy');
        Livewire::actingAs($admin)->test(Widgets\SystemHealthWidget::class)->assertSee('Netgsm SMS')->assertSee('iyzico kartla ödeme')->assertSee('Zamanlayıcı');
    }

    public function test_arac_yayin_durumu_listesi_ve_canli_kilit(): void
    {
        $admin = $this->admin();
        $this->actingAs($admin)->get('/admin/tool-statuses')->assertOk()->assertSee('Banyo Tadilat Maliyeti');
        $this->assertGreaterThan(50, ToolStatus::query()->count());

        // Canlı ortam davranışı: banyo incelemede → hesaplama POST'u 423
        ToolStatus::query()->where('slug', 'banyo-maliyet-hesaplayici')->update(['active' => false]);
        ToolStatus::applyToConfig();
        $this->post('/araclar/banyo-maliyet-hesaplayici/pro-rapor/kod', ['phone' => '05321112233'])->assertStatus(423);

        // Admin'den yayına alınca açılır
        ToolStatus::query()->where('slug', 'banyo-maliyet-hesaplayici')->first()->update(['active' => true]);
        ToolStatus::applyToConfig();
        $this->assertSame('active', config('worksitem_tools.tool_status.overrides.banyo-maliyet-hesaplayici'));
        $this->postJson('/araclar/banyo-maliyet-hesaplayici/pro-rapor/kod', ['phone' => '12'])->assertStatus(422);
    }
}
