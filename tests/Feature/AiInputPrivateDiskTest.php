<?php

namespace Tests\Feature;

use App\Http\Middleware\EnsureAiQuota;
use App\Http\Middleware\EnsureAiSpendEnabled;
use App\Services\AiDesignService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/** v107: müşteri oda fotoğrafı herkese açık diske yazılmaz */
class AiInputPrivateDiskTest extends TestCase
{
    use RefreshDatabase;

    public function test_oda_fotografi_ozel_diske_yazilir(): void
    {
        Storage::fake('local');
        Storage::fake('public');
        $this->withoutMiddleware([EnsureAiSpendEnabled::class, EnsureAiQuota::class, ThrottleRequests::class]);

        $seen = null;
        $this->mock(AiDesignService::class, function ($m) use (&$seen) {
            $m->shouldReceive('startAsync')->once()->andReturnUsing(function ($path) use (&$seen) {
                $seen = $path;

                return ['status' => 'started', 'prediction_id' => 'p1', 'prompt_used' => 'x'];
            });
        });

        $this->post(route('ai.interior.start'), [
            'room_image' => UploadedFile::fake()->image('oda.jpg', 800, 600),
            'style' => 'modern',
        ], ['Accept' => 'application/json']);

        $this->assertNotNull($seen, 'AI servisi çağrılmadı');
        $this->assertCount(1, Storage::disk('local')->allFiles('ai_inputs'));
        $this->assertCount(0, Storage::disk('public')->allFiles('ai_inputs'));
        $this->assertFileExists($seen);
    }
}
