<?php

namespace App\Services\Payments;

use App\Models\AiWallet;
use App\Models\Order;
use App\Services\AI\AiCredits;
use App\Services\Sms\NetgsmSms;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Online satış (iyzico ortak ödeme sayfası): yapay zekâ kredisi ve ücretli Pro rapor.
 * Ödeme sonucu tarayıcıdan gelen veriye değil, iyzico'dan token ile sorgulanan sonuca göre işlenir.
 */
class OnlinePayment
{
    public function __construct(private IyzicoClient $iyzico, private AiCredits $credits) {}

    public function enabled(): bool
    {
        return (bool) config('worksitem_payments.online_enabled') && $this->iyzico->configured();
    }

    /** Kredi satışı: online ödeme açık VE yapay zekâ harcama kilidi açık olmalı (aksi halde kredi kullanılamaz) */
    public function creditsEnabled(): bool
    {
        return $this->enabled() && (bool) config('services.ai_spend_enabled');
    }

    /** Kredi paketi siparişi (paket dizini config/worksitem_ai_credits.php) */
    public function creditOrder(AiWallet $w, int $package): ?Order
    {
        $p = config("worksitem_ai_credits.packages.$package");
        if (! is_array($p) || empty($p['credits']) || empty($p['price'])) {
            return null;
        }

        return Order::query()->create([
            'kind' => 'credits', 'ai_wallet_id' => $w->id, 'title' => 'Worksitem yapay zekâ kredisi ('.$p['credits'].')',
            'credits' => (int) $p['credits'], 'amount' => (float) $p['price'], 'name' => $w->name, 'phone' => $w->phone,
        ]);
    }

    /** Ücretli Pro rapor siparişi (fiyat + KDV) */
    public function reportOrder(string $ref, string $title, string $name, string $phone, string $returnUrl): Order
    {
        $net = (float) config('worksitem_pro_report.price', 149);

        return Order::query()->create([
            'kind' => 'pro_report', 'ref' => $ref, 'title' => $title, 'amount' => round($net * (1 + (float) config('worksitem_payments.vat_rate', 0.20)), 2),
            'name' => $name, 'phone' => $phone, 'return_url' => $returnUrl,
        ]);
    }

    /** Öne çıkan (Pro) firma üyeliği siparişi: yıllık, fiyat + KDV */
    public function firmProOrder(\App\Models\Firm $firm): Order
    {
        $net = (float) config('worksitem_firms.pro_price', 2400);

        return Order::query()->create([
            'kind' => 'firm_pro', 'ref' => 'firm:'.$firm->id, 'title' => 'Worksitem öne çıkan firma üyeliği (1 yıl) · '.$firm->name,
            'amount' => round($net * (1 + (float) config('worksitem_payments.vat_rate', 0.20)), 2),
            'name' => $firm->contact_name, 'phone' => $firm->phone, 'email' => $firm->email,
        ]);
    }

    /** Bu hesap için ödenmiş Pro rapor var mı (aynı telefon, son N gün) */
    public static function reportPaid(string $ref, string $phone): bool
    {
        return Order::query()->where('kind', 'pro_report')->where('ref', $ref)->where('phone', $phone)->where('status', 'paid')
            ->where('paid_at', '>=', now()->subDays((int) config('worksitem_payments.pro_report_days', 30)))->exists();
    }

    /** @return array{ok: bool, url?: string, error?: string} */
    public function start(Order $order, ?string $ip): array
    {
        $parts = preg_split('/\s+/u', trim((string) ($order->name ?: 'Worksitem Müşterisi'))) ?: [];
        $surname = count($parts) > 1 ? array_pop($parts) : '-';
        $name = trim(implode(' ', $parts)) ?: 'Müşteri';
        $gsm = NetgsmSms::normalize((string) $order->phone);
        $addr = ['contactName' => trim($name.' '.$surname), 'city' => 'İstanbul', 'country' => 'Turkey', 'address' => 'Online hizmet'];
        $price = IyzicoClient::price($order->amount);

        $res = $this->iyzico->initialize([
            'locale' => 'tr', 'conversationId' => $order->public_id, 'price' => $price, 'paidPrice' => $price, 'currency' => 'TRY',
            'basketId' => $order->public_id, 'paymentGroup' => 'PRODUCT', 'callbackUrl' => route('odeme.iyzico.callback'), 'enabledInstallments' => [1],
            'buyer' => [
                'id' => $order->public_id, 'name' => $name, 'surname' => $surname, 'gsmNumber' => $gsm ? '+90'.$gsm : '',
                'email' => (string) ($order->email ?: 'musteri@worksitem.com'), 'identityNumber' => '11111111111',
                'registrationAddress' => 'Online hizmet', 'ip' => (string) $ip, 'city' => 'İstanbul', 'country' => 'Turkey',
            ],
            'shippingAddress' => $addr, 'billingAddress' => $addr,
            'basketItems' => [['id' => $order->public_id, 'name' => mb_substr($order->title, 0, 100), 'category1' => 'Dijital hizmet', 'itemType' => 'VIRTUAL', 'price' => $price]],
        ]);

        if (($res['status'] ?? '') !== 'success' || empty($res['token']) || empty($res['paymentPageUrl'])) {
            $order->update(['status' => 'failed', 'error' => mb_substr((string) ($res['errorMessage'] ?? 'Ödeme sayfası açılamadı.'), 0, 500)]);
            Log::warning('iyzico online ödeme başlatılamadı', ['order' => $order->public_id, 'error' => $res['errorMessage'] ?? null, 'code' => $res['errorCode'] ?? null, 'group' => $res['errorGroup'] ?? null]);

            return ['ok' => false, 'error' => (string) ($res['errorMessage'] ?? 'Ödeme sayfası açılamadı.')];
        }
        $order->update(['iyzico_token' => $res['token']]);

        return ['ok' => true, 'url' => (string) $res['paymentPageUrl']];
    }

    /** iyzico dönüşü: ödeme doğrulanırsa sipariş bir kez işlenir (kredi yüklenir) */
    public function complete(string $token): ?Order
    {
        $order = Order::query()->where('iyzico_token', $token)->first();
        if (! $order || $order->isPaid()) {
            return $order;
        }
        $res = $this->iyzico->retrieve($token, $order->public_id);
        $paid = ($res['status'] ?? '') === 'success' && ($res['paymentStatus'] ?? '') === 'SUCCESS'
            && ($res['basketId'] ?? '') === $order->public_id && abs((float) ($res['paidPrice'] ?? 0) - $order->amount) < 0.01;

        DB::transaction(function () use ($order, $res, $paid) {
            $fresh = Order::query()->lockForUpdate()->find($order->id);
            if ($fresh->isPaid()) {
                return;
            }
            $fresh->update([
                'status' => $paid ? 'paid' : 'failed', 'paid_at' => $paid ? now() : null, 'payment_id' => $res['paymentId'] ?? null,
                'error' => $paid ? null : mb_substr((string) ($res['errorMessage'] ?? 'Ödeme tamamlanmadı.'), 0, 500),
            ]);
            if ($paid && $fresh->kind === 'credits' && $fresh->ai_wallet_id && ($w = AiWallet::query()->find($fresh->ai_wallet_id))) {
                $this->credits->grant($w, (int) $fresh->credits, 'Online satın alma '.$fresh->public_id);
            }
            if ($paid && $fresh->kind === 'match_fee') {
                app(\App\Services\Market\Marketplace::class)->matched($fresh);
            }
            if ($paid && $fresh->kind === 'firm_pro' && str_starts_with((string) $fresh->ref, 'firm:')
                && ($f = \App\Models\Firm::query()->find((int) substr((string) $fresh->ref, 5)))) {
                $from = $f->isPro() ? $f->plan_until : now();
                $f->update(['plan' => 'pro', 'plan_until' => $from->copy()->addDays((int) config('worksitem_firms.pro_days', 365))]);
            }
        });

        return $order->refresh();
    }
}
