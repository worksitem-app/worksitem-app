<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AiCheckTest extends TestCase
{
    public function test_modeller_saglayicida_var_mi_ve_anahtar_yazilmaz(): void
    {
        config(['services.ai_spend_enabled' => true, 'services.gemini.api_key' => 'GIZLI-ANAHTAR-123',
            'services.gemini.boq_model' => 'gemini-3.6-flash', 'services.gemini.rayic_model' => 'gemini-3.6-flash',
            'worksitem_price_robot.model' => 'gemini-3.6-flash', 'services.gemini.image_model' => 'gemini-3.1-flash-image', 'services.gemini.image_model_hd' => 'gemini-3.1-flash-image']);
        Http::fake(['generativelanguage.googleapis.com/*' => Http::response(['models' => [['name' => 'models/gemini-3.6-flash'], ['name' => 'models/gemini-3.1-flash-image']]])]);
        $this->artisan('ai:check')->expectsOutputToContain('yapay zekâ hazır')->doesntExpectOutputToContain('GIZLI')->assertSuccessful();

        config(['services.gemini.image_model' => 'gemini-eski-image']);
        $this->artisan('ai:check')->expectsOutputToContain('gemini-eski-image sağlayıcıda YOK')->assertFailed();
    }
}
