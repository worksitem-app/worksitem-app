<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\AI\AiCredits;
use App\Services\Payments\OnlinePayment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\View\View;

/** Online satış: yapay zekâ kredisi (iyzico) ve sipariş sonucu */
class OrderController extends Controller
{
    public function buyCredits(Request $request, AiCredits $credits, OnlinePayment $pay): JsonResponse
    {
        if (! $pay->creditsEnabled()) {
            return response()->json(['ok' => false, 'message' => 'Kartla ödeme şu an kapalı.'], 409);
        }
        $w = $credits->current($request);
        if (! $w) {
            return response()->json(['ok' => false, 'message' => 'Önce telefonunuzla giriş yapın.'], 401);
        }
        $data = $request->validate(['package' => ['required', 'integer', 'min:0', 'max:20'], 'email' => ['nullable', 'email', 'max:150']]);
        $order = $pay->creditOrder($w, (int) $data['package']);
        if (! $order) {
            return response()->json(['ok' => false, 'message' => 'Paket bulunamadı.'], 422);
        }
        $order->update(['email' => $data['email'] ?? null]);
        $r = $pay->start($order, $request->ip());

        return response()->json($r['ok'] ? ['ok' => true, 'url' => $r['url']] : ['ok' => false, 'message' => $r['error']], $r['ok'] ? 200 : 502);
    }

    /** iyzico dönüşü: oturum/CSRF yok (çapraz site POST); sonuç iyzico'dan doğrulanır */
    public function callback(Request $request, OnlinePayment $pay): RedirectResponse
    {
        $token = (string) $request->input('token', '');
        $order = ($token !== '' && strlen($token) <= 200) ? $pay->complete($token) : null;
        if (! $order) {
            return redirect('/');
        }

        return redirect()->to(URL::temporarySignedRoute('odeme.sonuc', now()->addDays(7), ['order' => $order->public_id]));
    }

    public function result(Request $request, Order $order): View|RedirectResponse
    {
        if ($order->isPaid() && $order->kind === 'pro_report' && $order->return_url) {
            return redirect()->to($order->return_url)->with('pro_paid', 'Ödemeniz alındı. Pro raporunuzu şimdi indirebilirsiniz.');
        }
        if ($order->isPaid() && $order->kind === 'match_fee' && $order->return_url) {
            return redirect()->to($order->return_url)->with('panel_ok', 'Ödemeniz alındı. Müşterinin iletişim bilgileri açıldı.');
        }

        return view('odeme-sonuc', ['order' => $order]);
    }
}
