<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\Auth\PublicAuthController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function (): void {
    Route::get('/giris', [PublicAuthController::class, 'showLogin'])
        ->name('login');

    Route::post('/giris', [PublicAuthController::class, 'login'])
        ->middleware('throttle:6,1')
        ->name('login.store');

    Route::get('/kayit', [PublicAuthController::class, 'showRegister'])
        ->name('register');

    Route::post('/kayit', [PublicAuthController::class, 'register'])
        ->middleware('throttle:4,1')
        ->name('register.store');
});

Route::middleware('auth')->group(function (): void {
    Route::get('/hesabim', [AccountController::class, 'dashboard'])
        ->name('account.dashboard');

    Route::post('/cikis', [PublicAuthController::class, 'logout'])
        ->name('logout');
});