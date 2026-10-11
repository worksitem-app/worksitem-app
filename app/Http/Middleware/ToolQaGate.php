<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ToolQaGate
{
    public function handle(Request $request, Closure $next): Response
    {
        $path = trim($request->path(), '/');

        if ($path === 'araclar' || !str_starts_with($path, 'araclar/')) {
            return $next($request);
        }

        $segments = explode('/', $path);
        $slug = $segments[1] ?? null;

        if (!$slug) {
            return $next($request);
        }

        $config = config('worksitem_tools.tool_status', []);
        $default = $config['default'] ?? 'review';
        $overrides = $config['overrides'] ?? [];
        $status = $overrides[$slug] ?? $default;
        /* WORKSITEM_STAGING_QA_BYPASS
           app.worksitem.com ekip erişimi zaten StagingTeamAccess ile korunuyor.
           İncelemedeki araçlar staging üzerinde QA için kullanılabilir.
        */
        // Host header istemci kontrolünde: yalnızca gerçekten staging ortamındaysak bypass et
        // (StagingTeamAccess de yalnızca APP_ENV=staging iken devrede).
        if (app()->environment('staging') && $request->getHost() === 'app.worksitem.com') {
            return $next($request);
        }


        if ($status === 'active') {
            return $next($request);
        }

        /* İncelemedeki aracın işlem endpointlerini çalıştırma */
        if (!$request->isMethod('GET') && !$request->isMethod('HEAD')) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Bu araç kalite kontrolünden geçiyor ve geçici olarak kullanıma kapalıdır.',
                    'status' => 'review',
                ], 423);
            }

            return response(
                'Bu araç kalite kontrolünden geçiyor ve geçici olarak kullanıma kapalıdır.',
                423
            );
        }

        $response = $next($request);

        /*
         * SEO: kilitli (review/soon/inactive) araçlar sitemap'te yok; arama motoruna da
         * "indeksleme ama linkleri izle" diyoruz. Aksi halde Google tam ekran perdeli,
         * kullanılamayan sayfaları indeksler → hemen çıkma ve düşük CTR sinyali.
         */
        $response->headers->set('X-Robots-Tag', 'noindex, follow');

        /* Redirect/404 vb. normal Laravel davranışını bozma */
        if (
            $request->isMethod('HEAD') ||
            $response->getStatusCode() !== 200 ||
            !method_exists($response, 'getContent') ||
            !method_exists($response, 'setContent')
        ) {
            return $response;
        }

        $contentType = (string) $response->headers->get('Content-Type', '');

        if (!str_contains($contentType, 'text/html')) {
            return $response;
        }

        $html = $response->getContent();

        if (!is_string($html) || stripos($html, '</body>') === false) {
            return $response;
        }

        $labels = [
            'review' => [
                'badge' => 'İNCELEMEDE',
                'title' => 'Bu araç kalite kontrolünden geçiyor',
                'text' => 'Hesaplama özelliği kısa süreliğine kullanıma kapalıdır. Sayfa ve SEO içeriği korunmaktadır.',
            ],
            'soon' => [
                'badge' => 'YAKINDA',
                'title' => 'Bu araç hazırlanıyor',
                'text' => 'Hesaplama özelliği henüz kullanıma açılmadı.',
            ],
            'inactive' => [
                'badge' => 'PASİF',
                'title' => 'Bu araç şu anda kullanılamıyor',
                'text' => 'Araç geçici olarak kullanıma kapalıdır.',
            ],
        ];

        $copy = $labels[$status] ?? $labels['review'];

        $overlay = '
<style id="worksitem-tool-qa-gate">
html,body{overflow:hidden!important}
#wsQaGate{
    position:fixed;
    inset:0;
    z-index:2147483647;
    display:flex;
    align-items:center;
    justify-content:center;
    padding:24px;
    background:rgba(15,23,42,.76);
    backdrop-filter:blur(5px);
}
#wsQaGate .ws-qa-card{
    width:min(100%,560px);
    padding:32px;
    border-radius:20px;
    background:#fff;
    color:#111827;
    box-shadow:0 25px 80px rgba(0,0,0,.35);
    text-align:center;
    font-family:Inter,Arial,sans-serif;
}
#wsQaGate .ws-qa-badge{
    display:inline-block;
    margin-bottom:14px;
    padding:7px 11px;
    border-radius:999px;
    background:#fff7d6;
    color:#8a5a00;
    font-size:11px;
    font-weight:900;
    letter-spacing:.5px;
}
#wsQaGate h2{
    margin:0 0 10px;
    font-size:24px;
    line-height:1.25;
}
#wsQaGate p{
    margin:0 0 22px;
    color:#6b7280;
    font-size:14px;
    line-height:1.6;
}
#wsQaGate a{
    display:inline-flex;
    min-height:46px;
    align-items:center;
    justify-content:center;
    padding:0 18px;
    border-radius:11px;
    background:#ffb800;
    color:#111827;
    font-weight:900;
    text-decoration:none;
}
</style>

<div id="wsQaGate" role="dialog" aria-modal="true" aria-labelledby="wsQaGateTitle">
    <div class="ws-qa-card">
        <span class="ws-qa-badge">'.e($copy['badge']).'</span>
        <h2 id="wsQaGateTitle">'.e($copy['title']).'</h2>
        <p>'.e($copy['text']).'</p>
        <a href="/araclar">Diğer araçlara dön →</a>
    </div>
</div>
';

        $html = str_ireplace('</body>', $overlay."\n</body>", $html);

        $response->setContent($html);

        return $response;
    }
}
