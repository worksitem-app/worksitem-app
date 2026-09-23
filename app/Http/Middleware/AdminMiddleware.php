<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            abort(401, 'Yetkisiz erişim.');
        }

        $isAdmin = method_exists($user, 'isAdmin')
            ? $user->isAdmin()
            : (($user->role ?? null) === 'admin' || ($user->is_admin ?? false) === true);

        if (!$isAdmin) {
            abort(403, 'Bu sayfaya erişim yetkiniz yok.');
        }

        $response = $next($request);
        $response->headers->set('X-Robots-Tag', 'noindex, nofollow');

        return $response;
    }
}
