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
        // v86 mobil hız: Inter kendi sunucumuzdan, <head> başında önceden yüklenir; Google Fonts gidiş-dönüşü kalkar.
        // (Önceden yazı Google'dan geç gelince metin yeniden çiziliyor, ana içerik ~2,3 sn gecikiyordu.)
        $html = self::selfHostFonts($html);
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
        // Erişilebilirlik (v105): eski hesaplayıcılarda <label> ile alanı bağla; ekran okuyucu alanın adını söylesin
        if ($request->is('araclar/*')) {
            $html = self::linkLabels($html);
        }
        // Küçük ortak CSS'ler sayfaya gömülür: ayrı istek oluşturmayı engellemesin (v86; ölçüm: 150–550 ms)
        $html = self::inlineCss($html);
        if (! str_contains($html, 'id="ws-brand-css"') && ! str_contains($html, '/css/ws-brand.css')) {
            $pos = stripos($html, '</head>');
            $html = substr($html, 0, $pos).'<style id="ws-brand-css">'.\App\Support\Asset::inline('css/ws-brand.css').'</style>'."\n".substr($html, $pos);
        }

        // v117 ortak kabuk: tasarım token'ları + üst menü/alt bilgi stilleri her sayfada, sayfa stillerinden sonra
        $html = self::injectShell($html);

        // v115 UI/UX denetimi: ortak erişilebilirlik katmanı sayfanın en sonunda (sayfa ve bileşen stillerinin üstüne yazar)
        if (! str_contains($html, 'id="ws-a11y-css"') && ($b = strripos($html, '</body>')) !== false) {
            $html = substr($html, 0, $b).'<style id="ws-a11y-css">'.\App\Support\Asset::inline('css/ws-a11y.css').'</style>'.substr($html, $b);
        }

        $response->setContent(self::deferFonts($html));

        return $response;
    }

    /**
     * v117 ortak kabuk:
     *  - <head> başına tema başlatıcı (kayıtlı tema ilk boyamadan önce; koyu temada beyaz yanıp sönme olmaz)
     *  - </head> öncesine wk-tokens.css + wk-shell.css (gömülü; küçük, her sayfada gerekli)
     *  - yeni tasarıma geçmiş sayfalara (.wk-root) wk-ui.css + wk-ui.js (sürümlü, önbelleğe alınır)
     */
    public static function injectShell(string $html): string
    {
        if (! str_contains($html, 'id="wk-theme-init"') && preg_match('#<head\b[^>]*>#i', $html, $m, PREG_OFFSET_CAPTURE)) {
            $at = $m[0][1] + strlen($m[0][0]);
            $html = substr($html, 0, $at).self::THEME_INIT_SCRIPT.substr($html, $at);
        }
        $pos = stripos($html, '</head>');
        if ($pos === false) {
            return $html;
        }
        $add = '';
        if (! str_contains($html, 'id="wk-shell-css"')) {
            $add .= '<style id="wk-tokens-css">'.\App\Support\Asset::inline('css/wk-tokens.css').'</style>'
                .'<style id="wk-shell-css">'.\App\Support\Asset::inline('css/wk-shell.css').'</style>';
        }
        if (preg_match('#class=["\'][^"\']*\bwk-root\b#', $html) && ! str_contains($html, '/css/wk-ui.css')) {
            $add .= '<link rel="stylesheet" href="'.\App\Support\Asset::url('css/wk-ui.css').'">'
                .'<script src="'.\App\Support\Asset::url('js/wk-ui.js').'" defer></script>';
        }

        return $add === '' ? $html : substr($html, 0, $pos).$add."\n".substr($html, $pos);
    }

    /**
     * Varsayılan tema AÇIK (telefon/bilgisayar koyu modda olsa da). Koyu tema yalnız kullanıcı düğmeyle seçince
     * (worksitem-theme=dark) ve ilk boyamadan önce uygulanır.
     */
    public const THEME_INIT_SCRIPT = '<script id="wk-theme-init">(function(){var t="light";try{if(localStorage.getItem("worksitem-theme")==="dark")t="dark"}catch(e){}document.documentElement.setAttribute("data-theme",t)})()</script>';

    /**
     * for= özelliği olmayan <label>'ı, ardından gelen ilk id'li alana bağlar (arada başka label ya da alan yoksa).
     * 45 eski hesaplayıcının şablonuna tek tek dokunmadan alan adlarını ekran okuyucuya açar.
     */
    public static function linkLabels(string $html): string
    {
        return (string) preg_replace_callback(
            '#<label\b(?![^>]*\bfor=)([^>]*)>((?:(?!</label>|<input\\b|<select\\b|<textarea\\b).){1,300}?)</label>((?:(?!<label\b|<input\b|<select\b|<textarea\b).){0,400}?)<(input|select|textarea)\b([^>]*?\bid=["\']([A-Za-z][\w-]*)["\'])#s',
            function (array $m): string {
                if (preg_match('#\btype=["\']?(hidden|submit|button)#i', $m[5])) {
                    return $m[0];
                }

                return '<label for="'.$m[6].'"'.$m[1].'>'.$m[2].'</label>'.$m[3].'<'.$m[4].$m[5];
            },
            $html
        ) ?: $html;
    }

    /** Sayfaya gömülen (oluşturmayı engelleyen ayrı istek olmasın diye) küçük yerel CSS dosyaları */
    public const INLINE_CSS = ['css/ws-brand.css' => 'ws-brand-css', 'css/worksitem-design.css' => 'ws-design-css'];

    /**
     * <head> içindeki <link rel="stylesheet" href="/css/ws-brand.css?v=..."> gibi bağlantıları
     * dosyanın içeriğiyle değiştirir. Aynı dosya iki kez gömülmez.
     */
    public static function inlineCss(string $html): string
    {
        $head = stripos($html, '</head>');
        if ($head === false) {
            return $html;
        }
        $done = [];
        $headHtml = preg_replace_callback('#<link\b[^>]*>#i', function (array $m) use (&$done): string {
            $tag = $m[0];
            if (! preg_match('#rel=["\']stylesheet["\']#i', $tag) || preg_match('#\bmedia=["\'](?!all|screen)#i', $tag)) {
                return $tag;
            }
            foreach (self::INLINE_CSS as $path => $id) {
                if (preg_match('#href=["\'](?:https://worksitem\.com)?/'.preg_quote($path, '#').'(?:\?[^"\']*)?["\']#i', $tag)) {
                    if (isset($done[$id])) {
                        return '';
                    }
                    $done[$id] = true;

                    return '<style id="'.$id.'">'.\App\Support\Asset::inline($path).'</style>';
                }
            }

            return $tag;
        }, substr($html, 0, $head));

        return $headHtml.substr($html, $head);
    }

    /**
     * Inter (değişken, 100–900) — Türkçe için latin + latin-ext birlikte gerekir (ğ, ş, İ latin-ext'te).
     * font-display:optional: yazı tipi ilk ~100 ms'de gelmezse o sayfada ölçülü yedek (Inter Fallback) kalır,
     * sonradan değişmez → metin yeniden çizilmez, sayfa kaymaz (anasayfa CLS 0,147). Sonraki sayfalarda önbellekten Inter.
     */
    public const FONT_FILES = [
        'fonts/inter/inter-latin-wght-normal.woff2' => 'U+0000-00FF,U+0131,U+0152-0153,U+02BB-02BC,U+02C6,U+02DA,U+02DC,U+0304,U+0308,U+0329,U+2000-206F,U+20AC,U+2122,U+2191,U+2193,U+2212,U+2215,U+FEFF,U+FFFD',
        'fonts/inter/inter-latin-ext-wght-normal.woff2' => 'U+0100-02BA,U+02BD-02C5,U+02C7-02CC,U+02CE-02D7,U+02DD-02FF,U+0304,U+0308,U+0329,U+1D00-1DBF,U+1E00-1E9F,U+1EF2-1EFF,U+2020,U+20A0-20AB,U+20AD-20C0,U+2113,U+2C60-2C7F,U+A720-A7FF',
    ];

    /**
     * Google Fonts'taki Inter bağlantılarını (ve onların preconnect'lerini) <head>'den kaldırır,
     * yerine <head> başına kendi sunucumuzdaki dosyalar için preload + @font-face koyar.
     * Inter dışında bir Google yazı tipi varsa ona dokunmaz (deferFonts erteler).
     */
    public static function selfHostFonts(string $html): string
    {
        $head = stripos($html, '</head>');
        if ($head === false || str_contains(substr($html, 0, $head), 'id="ws-fonts"')
            || ! preg_match('#<head[^>]*>#i', $html, $hm, PREG_OFFSET_CAPTURE)) {
            return $html;
        }
        $headHtml = substr($html, 0, $head);
        $headHtml = preg_replace('#<noscript>\s*(<link\b[^>]*fonts\.googleapis\.com/css2?\?family=Inter[:&"\'][^>]*>)\s*</noscript>#i', '', $headHtml);
        $headHtml = preg_replace('#<link\b[^>]*href=["\']https://fonts\.googleapis\.com/css2?\?family=Inter[:&"\'][^>]*>#i', '', $headHtml);
        if (! str_contains($headHtml, 'fonts.googleapis.com/css')) {
            $headHtml = preg_replace('#<link\b[^>]*rel=["\'](?:preconnect|dns-prefetch)["\'][^>]*fonts\.(?:googleapis|gstatic)\.com[^>]*>#i', '', $headHtml);
        }

        $pre = '';
        $faces = '';
        foreach (self::FONT_FILES as $file => $range) {
            $url = \App\Support\Asset::url($file);
            $pre .= '<link rel="preload" href="'.$url.'" as="font" type="font/woff2" crossorigin>';
            $faces .= "@font-face{font-family:'Inter';font-style:normal;font-display:optional;font-weight:100 900;src:url({$url}) format('woff2-variations'),url({$url}) format('woff2');unicode-range:{$range}}";
        }
        $tags = $pre.'<style id="ws-fonts">'.$faces.'</style>';

        $at = $hm[0][1] + strlen($hm[0][0]);
        // <meta charset> varsa ondan sonra (tarayıcı ilk 1 KB'ta karakter kümesini görmeli)
        if (preg_match('#<meta\s+charset=[^>]*>#i', $headHtml, $cm, PREG_OFFSET_CAPTURE) && $cm[0][1] < 1024) {
            $at = $cm[0][1] + strlen($cm[0][0]);
        }

        return substr($headHtml, 0, $at).$tags.substr($headHtml, $at).substr($html, $head);
    }

    /**
     * Google Fonts stil dosyalarını oluşturmayı engellemeyecek biçimde yükler.
     * Mobilde sayfa, fonts.googleapis.com + fonts.gstatic.com gidiş-dönüşünü beklemeden çizilir;
     * yazı önce benzer ölçülü yedek yazı tipiyle (ws-brand.css › "Inter Fallback") görünür.
     * JavaScript kapalıysa <noscript> içindeki normal bağlantı devreye girer.
     */
    public static function deferFonts(string $html): string
    {
        $head = stripos($html, '</head>');
        if ($head === false || ! str_contains(substr($html, 0, $head), 'fonts.googleapis.com/css')) {
            return $html;
        }

        $headHtml = preg_replace_callback('#<link\b[^>]*>#i', function (array $m): string {
            $tag = $m[0];
            if (! preg_match('#href=["\'](https://fonts\.googleapis\.com/css[^"\']*)["\']#i', $tag, $h)
                || ! preg_match('#rel=["\']stylesheet["\']#i', $tag)
                || preg_match('#\bmedia=#i', $tag)) {
                return $tag;
            }
            $href = $h[1];

            return '<link rel="preload" as="style" href="'.$href.'">'
                .'<link rel="stylesheet" href="'.$href.'" media="print" onload="this.media=\'all\'">'
                .'<noscript><link rel="stylesheet" href="'.$href.'"></noscript>';
        }, substr($html, 0, $head));

        return $headHtml.substr($html, $head);
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
