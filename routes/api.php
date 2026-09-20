<?php

use App\Http\Controllers\ToolEventController;
use Illuminate\Support\Facades\Route;

Route::post('/tool-events', [ToolEventController::class, 'store'])
    ->middleware('throttle:60,1');
