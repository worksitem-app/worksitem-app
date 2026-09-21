<?php

namespace Tests\Feature;

use App\Models\AiGeneration;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AiDesignControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_ai_generation_model_exists(): void
    {
        $this->assertTrue(class_exists(AiGeneration::class));
    }

    public function test_ai_generation_can_be_created(): void
    {
        $generation = AiGeneration::create([
            'tool_name' => 'ic-mekan',
            'style' => 'Modern',
            'input_image' => 'test/path.jpg',
            'status' => 'processing',
        ]);

        $this->assertDatabaseHas('ai_generations', [
            'id' => $generation->id,
            'tool_name' => 'ic-mekan',
            'style' => 'Modern',
        ]);
    }

    public function test_ai_generation_has_prediction_id_column(): void
    {
        $generation = AiGeneration::create([
            'tool_name' => 'ic-mekan',
            'style' => 'Minimalist',
            'input_image' => 'test/path.jpg',
            'status' => 'processing',
            'prediction_id' => 'test_prediction_123',
        ]);

        $this->assertDatabaseHas('ai_generations', [
            'id' => $generation->id,
            'prediction_id' => 'test_prediction_123',
        ]);
    }

    public function test_ai_generation_status_can_be_updated(): void
    {
        $generation = AiGeneration::create([
            'tool_name' => 'ic-mekan',
            'style' => 'Modern',
            'input_image' => 'test/path.jpg',
            'status' => 'processing',
        ]);

        $generation->update([
            'status' => 'completed',
            'output_image' => 'https://example.com/output.jpg',
        ]);

        $this->assertDatabaseHas('ai_generations', [
            'id' => $generation->id,
            'status' => 'completed',
            'output_image' => 'https://example.com/output.jpg',
        ]);
    }

    public function test_ai_generation_multiple_styles(): void
    {
        $styles = ['Modern', 'Minimalist', 'Endüstriyel', 'Lüks', 'Japandi', 'Klasik'];

        foreach ($styles as $style) {
            $generation = AiGeneration::create([
                'tool_name' => 'ic-mekan',
                'style' => $style,
                'input_image' => 'test/path.jpg',
                'status' => 'processing',
            ]);

            $this->assertDatabaseHas('ai_generations', [
                'id' => $generation->id,
                'style' => $style,
            ]);
        }
    }
}
