<?php

namespace App\Services\Pricing\MaterialSources;

interface MaterialPriceSource
{
    public function key(): string;

    public function fetch(): array;
}
