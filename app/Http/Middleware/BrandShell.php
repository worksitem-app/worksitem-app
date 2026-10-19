<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Tüm public HTML sayfalarına marka katmanını ekler:
 *  - Inter font bağlantısı (yoksa)
 *  - /css/ws-brand.css (</head> öncesine, sayfa stillerinden SONRA → tipografi her yerde aynı)
 *
 * 100+ bağımsız Blade sayfası tek bir layout'tan türemediği için ortak katman buradan verilir.
 */
class BrandShell
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($response->getStatusCode() !== 200
            || $request->is('admin', 'admin/*', 'livewire/*', 'up')
            || ! str_contains((string) $response->headers->get('Content-Type', ''), 'text/html')) {
            return $response;
        }

        $html = $response->getContent();
        if (! is_string($html) || stripos($html, '</head>') === false) {
            return $response;
        }
        // Mobil hız: Google Fonts kullanan sayfada bağlantıyı erkenden aç (preconnect yoksa <head> başına)
        if (str_contains($html, 'fonts.googleapis.com/css') && ! str_contains($html, 'preconnect" href="https://fonts.gstatic.com')
            && preg_match('#<head[^>]*>#i', $html, $hm, PREG_OFFSET_CAPTURE)) {
            $at = $hm[0][1] + strlen($hm[0][0]);
            $html = substr($html, 0, $at).'<link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>'.substr($html, $at);
            $response->setContent($html);
        }
        // Uygulama (PWA): manifest, ana ekran simgesi ve servis çalışanı her sayfada
        if (! str_contains($html, '/sw.js')) {
            $html = self::injectApp($html);
            $response->setContent($html);
        }
        // "Uygulamayı yükle" bandı (yalnız telefonda, kuralları bileşende); panelde kendi kartı var
        if (! str_contains($html, 'id="wsAppBanner"') && ! str_contains($html, 'id="wsApp"') && ($b = strripos($html, '</body>')) !== false) {
            $html = substr($html, 0, $b).view('components.site.app-banner')->render().substr($html, $b);
            $response->setContent($html);
        }
        // Form güvenlik anahtarını tazele: sayfa saatlerce açık kalınca form "419 süresi doldu" vermesin
        if (! str_contains($html, 'id="wsCsrfRefresh"') && self::hasCsrfToken($html) && ($b = strripos($html, '</body>')) !== false) {
            $html = substr($html, 0, $b).self::CSRF_REFRESH_SCRIPT.substr($html, $b);
            $response->setContent($html);
        }
        if (str_contains($html, '/css/ws-brand.css')) {
            return $response;
        }

        $tags = '';
        if (! str_contains($html, 'family=Inter')) {
            $tags .= '<link rel="preconnect" href="https://fonts.googleapis.com">'
                .'<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>'
                .'<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">';
        }
        $tags .= '<link rel="stylesheet" href="'.\App\Support\Asset::url('css/ws-brand.css').'">';

        $pos = stripos($html, '</head>');
        $response->setContent(substr($html, 0, $pos).$tags."\n".substr($html, $pos));

        return $response;
    }

    /**
     * Her 20 dakikada ve sekmeye uzun aradan sonra dönüldüğünde /csrf-yenile'den yeni anahtar alır,
     * sayfadaki tüm _token alanlarını ve csrf-token meta etiketini günceller (oturumu da canlı tutar).
     */
    public const CSRF_REFRESH_SCRIPT = '<script id="wsCsrfRefresh">(function(){var last=Date.now(),busy=false;'
        .'function upd(){if(busy)return;busy=true;fetch("/csrf-yenile",{credentials:"same-origin",cache:"no-store",headers:{Accept:"application/json"}})'
        .'.then(function(r){return r.ok?r.json():null}).then(function(d){if(!d||!d.token)return;last=Date.now();'
        .'document.querySelectorAll(\'input[name="_token"]\').forEach(function(i){i.value=d.token});'
        .'var m=document.querySelector(\'meta[name="csrf-token"]\');if(m)m.setAttribute("content",d.token);'
        .'if(window.axios&&window.axios.defaults&&window.axios.defaults.headers&&window.axios.defaults.headers.common)window.axios.defaults.headers.common["X-CSRF-TOKEN"]=d.token;})'
        .'.catch(function(){}).then(function(){busy=false})}'
        .'setInterval(upd,1200000);'
        .'document.addEventListener("visibilitychange",function(){if(!document.hidden&&Date.now()-last>600000)upd()});'        .'})();</script>';

    public static function hasCsrfToken(string $html): bool
    {
        return str_contains($html, 'name="_token"') || str_contains($html, 'name="csrf-token"');
    }

    public static function injectApp(string $html): string
    {
        $tags = '';
        if (! preg_match('/rel=["\']manifest["\']/i', $html)) {
            $tags .= '<link rel="manifest" href="/site.webmanifest">';
        }
        if (! str_contains($html, 'name="theme-color"')) {
            $tags .= '<meta name="theme-color" content="#ffffff">';
        }
        if (! str_contains($html, 'apple-touch-icon')) {
            $tags .= '<link rel="apple-touch-icon" href="/apple-touch-icon.png">';
        }
        if (! str_contains($html, 'apple-mobile-web-app-title')) {
            $tags .= '<meta name="apple-mobile-web-app-capable" content="yes"><meta name="mobile-web-app-capable" content="yes"><meta name="apple-mobile-web-app-title" content="Worksitem">';
        }
        $tags .= '<script>if("serviceWorker"in navigator){addEventListener("load",function(){navigator.serviceWorker.register("/sw.js").catch(function(){})})}</script>';
        $pos = stripos($html, '</head>');

        return $pos === false ? $html : substr($html, 0, $pos).$tags."\n".substr($html, $pos);
    }
}
