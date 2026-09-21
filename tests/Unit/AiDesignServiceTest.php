<?php

namespace Tests\Unit;

use App\Services\AiDesignService;
use Tests\TestCase;

class AiDesignServiceTest extends TestCase
{
    private AiDesignService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new AiDesignService();
    }

    public function test_service_instantiation(): void
    {
        $this->assertInstanceOf(AiDesignService::class, $this->service);
    }

    public function test_start_async_missing_token_returns_error(): void
    {
        config(['services.replicate.token' => null]);

        $result = $this->service->startAsync('/tmp/fake.jpg', 'Modern');

        $this->assertIsArray($result);
        $this->assertSame('error', $result['status']);
        $this->assertArrayHasKey('message', $result);
    }

    public function test_start_async_nonexistent_image_returns_error(): void
    {
        config(['services.replicate.token' => 'fake_token']);

        $result = $this->service->startAsync('/tmp/nonexistent_image_xyz.jpg', 'Modern');

        $this->assertSame('error', $result['status']);
    }

    public function test_check_async_missing_token_returns_failed(): void
    {
        config(['services.replicate.token' => null]);

        $result = $this->service->checkAsync('fake_prediction_id');

        $this->assertIsArray($result);
        $this->assertSame('failed', $result['status']);
    }

    public function test_check_async_invalid_prediction_returns_failed(): void
    {
        config(['services.replicate.token' => 'fake_invalid_token_12345']);

        $result = $this->service->checkAsync('invalid_id_xyz');

        $this->assertIsArray($result);
        $this->assertContains($result['status'], ['failed', 'processing']);
    }
}
