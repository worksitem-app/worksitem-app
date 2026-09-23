<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        // JSON isteği → 401 dön
        if ($request->expectsJson()) {
            return null;
        }

        // Admin paneli → Filament login
        if ($request->is('admin') || $request->is('admin/*')) {
            return route('filament.admin.auth.login');
        }

        // Diğer her yer → Filament login (veya ana sayfa)
        return route('filament.admin.auth.login');
    }
}
