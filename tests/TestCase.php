<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Http;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Testler gerçek Gemini / Replicate / OpenAI API'lerine ASLA istek atmaz
        // (maliyet + flaky test + CI'da anahtar sızıntısı riski). Gerekirse Http::fake() kullanın.
        Http::preventStrayRequests();
    }
}
