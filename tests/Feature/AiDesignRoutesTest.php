<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AiDesignRoutesTest extends TestCase
{
    use RefreshDatabase;

    public function test_ai_interior_page_opens(): void
    {
        $this->get('/ai-tasarim/ic-mekan')->assertStatus(200);
    }

    public function test_ai_interior_page_contains_form(): void
    {
        $response = $this->get('/ai-tasarim/ic-mekan');
        $response->assertStatus(200);
        $response->assertSee('room_image', false);
        $response->assertSee('style', false);
    }

    public function test_ai_interior_page_contains_prompt_textarea(): void
    {
        $response = $this->get('/ai-tasarim/ic-mekan');
        $response->assertStatus(200);
        $response->assertSee('user_prompt', false);
    }

    public function test_start_endpoint_validation_fails_without_data(): void
    {
        $response = $this->postJson('/ai-tasarim/ic-mekan/start', []);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['room_image', 'style']);
    }

    public function test_status_endpoint_404_for_unknown_id(): void
    {
        $response = $this->getJson('/ai-tasarim/ic-mekan/status/999999');
        $response->assertStatus(404);
    }

    public function test_ai_interior_page_has_csrf_meta(): void
    {
        $response = $this->get('/ai-tasarim/ic-mekan');
        $response->assertStatus(200);
        $response->assertSee('csrf-token', false);
    }
}
