<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');


/*
|--------------------------------------------------------------------------
| WORKSITEM Daily Material Price Report
|--------------------------------------------------------------------------
|
| Son 24 saatte material_price_history tablosuna eklenen fiyatları
| info@worksitem.com adresine gönderir.
|
*/

Schedule::command('prices:daily-material-report')
    ->dailyAt('08:30')
    ->timezone('Europe/Istanbul')
    ->withoutOverlapping();
