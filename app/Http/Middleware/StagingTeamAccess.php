<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class StagingTeamAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! app()->environment('staging')) {
            return $next($request);
        }

        $expectedUser = (string) config('staging_access.user', '');
        $expectedHash = (string) config('staging_access.password_hash', '');

        if ($expectedUser === '' || $expectedHash === '') {
            return response(
                'WORKSITEM staging access is not configured.',
                503
            )->withHeaders([
                'X-Robots-Tag' => 'noindex, nofollow, noarchive, nosnippet',
                'Cache-Control' => 'no-store, private',
            ]);
        }

        $user = (string) ($request->getUser() ?? '');
        $password = (string) ($request->getPassword() ?? '');

        $userValid =
            $user !== '' &&
            hash_equals($expectedUser, $user);

        $passwordValid =
            $password !== '' &&
            password_verify($password, $expectedHash);

        if (! $userValid || ! $passwordValid) {
            return response(
                'Authentication required.',
                401
            )->withHeaders([
                'WWW-Authenticate' => 'Basic realm="WORKSITEM Staging - Team Only"',
                'X-Robots-Tag' => 'noindex, nofollow, noarchive, nosnippet',
                'Cache-Control' => 'no-store, private',
            ]);
        }

        $response = $next($request);

        $response->headers->set(
            'X-Robots-Tag',
            'noindex, nofollow, noarchive, nosnippet'
        );

        $response->headers->set(
            'Cache-Control',
            'no-store, private'
        );

        return $response;
    }
}
