<?php

namespace Tests\Feature;

use App\Services\Pricing\CalculationSnapshotService;
use Illuminate\Http\Request;
use Illuminate\Session\Store;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class CalculationSnapshotSecurityTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Cache::flush();
    }

    private function requestWithSession(string $sessionId): Request
    {
        $request = Request::create('/', 'GET');

        $session = app('session')->driver();
        $session->setId($sessionId);
        $session->start();

        $request->setLaravelSession($session);

        return $request;
    }

    public function test_same_session_can_read_renovation_snapshot(): void
    {
        $service = app(CalculationSnapshotService::class);

        $request = $this->requestWithSession(
            'qa-renovation-session-a'
        );

        $stored = $service->store(
            $request,
            'renovation_market_estimate',
            [
                'scope' => 'complete',
                'quality' => 'standard',
                'area' => 850,
                'market_estimate' => [
                    'total' => 19550000,
                    'estimate_low' => 17595000,
                    'estimate_high' => 21505000,
                ],
            ]
        );

        $snapshot = $service->get(
            $request,
            $stored['calculation_id']
        );

        $this->assertIsArray($snapshot);

        $this->assertSame(
            'renovation_market_estimate',
            $snapshot['calculation_type']
        );

        $this->assertSame(
            'complete',
            $snapshot['estimate']['scope']
        );

        $this->assertSame(
            'standard',
            $snapshot['estimate']['quality']
        );

        $this->assertEquals(
            850,
            $snapshot['estimate']['area']
        );

        $this->assertEquals(
            19550000,
            $snapshot['estimate']['market_estimate']['total']
        );
    }

    public function test_other_session_cannot_replay_snapshot(): void
    {
        $service = app(CalculationSnapshotService::class);

        $sessionA = $this->requestWithSession(
            'qa-renovation-session-a'
        );

        $stored = $service->store(
            $sessionA,
            'renovation_market_estimate',
            [
                'scope' => 'complete',
                'quality' => 'standard',
                'area' => 850,
                'market_estimate' => [
                    'total' => 19550000,
                ],
            ]
        );

        $sessionB = $this->requestWithSession(
            'qa-renovation-session-b'
        );

        $replay = $service->get(
            $sessionB,
            $stored['calculation_id']
        );

        $this->assertNull($replay);
    }

    public function test_fake_uuid_returns_null(): void
    {
        $service = app(CalculationSnapshotService::class);

        $request = $this->requestWithSession(
            'qa-renovation-session-a'
        );

        $snapshot = $service->get(
            $request,
            '00000000-0000-4000-8000-000000000000'
        );

        $this->assertNull($snapshot);
    }
}
