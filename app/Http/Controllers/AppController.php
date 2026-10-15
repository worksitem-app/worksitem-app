<?php

namespace App\Http\Controllers;

use App\Models\Firm;
use App\Models\PushSubscription;
use App\Services\Push\WebPush;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

/** PWA / TWA: çevrimdışı sayfa, Android uygulama bağlantısı, firma paneli web bildirimleri */
class AppController extends Controller
{
    public function offline(): Response
    {
        return response()->view('app.offline')->header('X-Robots-Tag', 'noindex');
    }

    /** Google Play TWA: alan adı doğrulaması (Digital Asset Links) */
    public function assetlinks(): JsonResponse
    {
        $fp = (array) config('worksitem_app.twa.sha256');
        $body = $fp ? [[
            'relation' => ['delegate_permission/common.handle_all_urls'],
            'target' => ['namespace' => 'android_app', 'package_name' => (string) config('worksitem_app.twa.package'), 'sha256_cert_fingerprints' => $fp],
        ]] : [];

        return response()->json($body)->header('Cache-Control', 'public, max-age=3600');
    }

    public function subscribe(Request $request): JsonResponse
    {
        if (! $firm = $this->firm($request)) {
            return response()->json(['ok' => false, 'message' => 'Oturum süresi doldu; lütfen tekrar giriş yapın.'], 401);
        }
        if (! WebPush::enabled()) {
            return response()->json(['ok' => false, 'message' => 'Bildirimler şu an kapalı.'], 503);
        }
        $data = $request->validate([
            'endpoint' => ['required', 'string', 'max:1000'],
            'keys.p256dh' => ['nullable', 'string', 'max:200'],
            'keys.auth' => ['nullable', 'string', 'max:100'],
        ]);
        if (! WebPush::allowedEndpoint($data['endpoint'])) {
            return response()->json(['ok' => false, 'message' => 'Bu tarayıcının bildirim servisi desteklenmiyor.'], 422);
        }
        PushSubscription::query()->updateOrCreate(['endpoint_hash' => PushSubscription::hash($data['endpoint'])], [
            'endpoint' => $data['endpoint'], 'firm_id' => $firm->id,
            'p256dh' => $data['keys']['p256dh'] ?? null, 'auth' => $data['keys']['auth'] ?? null,
            'user_agent' => mb_substr((string) $request->userAgent(), 0, 255), 'failed_count' => 0,
        ]);

        return response()->json(['ok' => true, 'message' => 'Bildirimler açıldı. Yeni iş geldiğinde bu cihaza haber vereceğiz.']);
    }

    public function unsubscribe(Request $request): JsonResponse
    {
        $endpoint = (string) $request->input('endpoint');
        if ($endpoint !== '' && ($firm = $this->firm($request))) {
            PushSubscription::query()->where('endpoint_hash', PushSubscription::hash($endpoint))->where('firm_id', $firm->id)->delete();
        }

        return response()->json(['ok' => true, 'message' => 'Bu cihazda bildirimler kapatıldı.']);
    }

    /** Servis çalışanı bildirimi alınca son mesajı buradan çeker (abonelik adresinin özeti ile) */
    public function message(Request $request): JsonResponse
    {
        $h = (string) $request->query('e');
        $s = preg_match('/^[a-f0-9]{64}$/', $h) ? PushSubscription::query()->where('endpoint_hash', $h)->first() : null;
        $p = (array) ($s?->pending ?? []);

        return response()->json([
            'title' => $p['title'] ?? 'Worksitem',
            'body' => $p['body'] ?? 'Firma panelinizde yeni bir gelişme var.',
            'url' => $p['url'] ?? route('market.panel'),
        ])->header('Cache-Control', 'no-store');
    }

    private function firm(Request $request): ?Firm
    {
        $s = $request->session()->get('firm_panel');
        if (! is_array($s) || ($s['until'] ?? 0) < time()) {
            return null;
        }
        $f = Firm::query()->find($s['id'] ?? 0);

        return $f && $f->status === 'approved' ? $f : null;
    }
}
