<?php

namespace App\Services\AI;

use App\Models\AiCreditTransaction;
use App\Models\AiWallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * AI kullanım hakkı: telefonla giriş yapan kullanıcıya günde N ücretsiz kullanım, sonrası kredi.
 * Hak, isteğin başında ayrılır; AI isteği başarısız olursa iade edilir.
 */
class AiCredits
{
    public const SESSION = 'ai_wallet';

    public function current(Request $request): ?AiWallet
    {
        $s = $request->session()->get(self::SESSION);
        if (! is_array($s) || ($s['until'] ?? 0) < time()) {
            return null;
        }
        $w = AiWallet::query()->find($s['id'] ?? 0);

        return $w && ! $w->blocked ? $w : null;
    }

    public function login(Request $request, string $phone, ?string $name = null): AiWallet
    {
        $w = AiWallet::query()->firstOrCreate(['phone' => $phone]);
        $w->forceFill(['verified_at' => now(), 'name' => $name ?: $w->name])->save();
        $request->session()->regenerate();
        $request->session()->put(self::SESSION, [
            'id' => $w->id,
            'until' => now()->addDays((int) config('worksitem_ai_credits.session_days', 30))->timestamp,
        ]);

        return $w;
    }

    public function logout(Request $request): void
    {
        $request->session()->forget(self::SESSION);
    }

    /**
     * Bir kullanım ayırır. Dönen dizi: ['ok' => bool, 'kind' => free|use|null, 'tx' => id, 'cost' => kredi]
     * $cost > 1 (ör. HD görsel 2 kredi): günlük ücretsiz hak kullanılmaz, yalnızca kredi düşer.
     */
    public function reserve(AiWallet $w, string $feature, int $cost = 1): array
    {
        $cost = max(1, $cost);

        return DB::transaction(function () use ($w, $feature, $cost) {
            /** @var AiWallet $w */
            $w = AiWallet::query()->lockForUpdate()->findOrFail($w->id);
            $today = AiWallet::today();
            $limit = (int) config('worksitem_ai_credits.free_per_day', 1);
            $used = $w->free_day?->toDateString() === $today ? (int) $w->free_used : 0;

            if ($cost === 1 && $used < $limit) {
                $w->forceFill(['free_day' => $today, 'free_used' => $used + 1])->save();
                $kind = 'free';
                $delta = 0;
            } elseif ($w->credits >= $cost) {
                $w->decrement('credits', $cost);
                $kind = 'use';
                $delta = -$cost;
            } else {
                return ['ok' => false, 'kind' => null, 'tx' => null, 'cost' => $cost];
            }

            $w->forceFill(['total_used' => $w->total_used + 1, 'last_used_at' => now()])->save();
            $tx = AiCreditTransaction::query()->create(['ai_wallet_id' => $w->id, 'delta' => $delta, 'kind' => $kind, 'feature' => $feature]);

            return ['ok' => true, 'kind' => $kind, 'tx' => $tx->id, 'cost' => $kind === 'use' ? $cost : 0];
        });
    }

    /** Başarısız AI isteğinde ayrılan hakkı geri verir */
    public function refund(AiWallet $w, array $reservation, string $feature): void
    {
        DB::transaction(function () use ($w, $reservation, $feature) {
            $w = AiWallet::query()->lockForUpdate()->findOrFail($w->id);
            if ($reservation['kind'] === 'free') {
                if ($w->free_day?->toDateString() === AiWallet::today() && $w->free_used > 0) {
                    $w->decrement('free_used');
                }
            } elseif ($reservation['kind'] === 'use') {
                $w->increment('credits', max(1, (int) ($reservation['cost'] ?? 1)));
            }
            if ($w->total_used > 0) {
                $w->decrement('total_used');
            }
            AiCreditTransaction::query()->create([
                'ai_wallet_id' => $w->id,
                'delta' => $reservation['kind'] === 'use' ? max(1, (int) ($reservation['cost'] ?? 1)) : 0,
                'kind' => 'refund',
                'feature' => $feature,
                'note' => 'AI isteği başarısız, hak iade edildi',
            ]);
        });
    }

    public function grant(AiWallet $w, int $credits, ?string $note = null, ?int $adminId = null): void
    {
        DB::transaction(function () use ($w, $credits, $note, $adminId) {
            $w = AiWallet::query()->lockForUpdate()->findOrFail($w->id);
            $credits < 0 ? $w->decrement('credits', min($w->credits, -$credits)) : $w->increment('credits', $credits);
            AiCreditTransaction::query()->create(['ai_wallet_id' => $w->id, 'delta' => $credits, 'kind' => 'grant', 'note' => $note, 'admin_id' => $adminId]);
        });
    }

    /** @return array{logged_in: bool, phone?: string, free_left?: int, credits?: int, free_per_day: int} */
    public function status(?AiWallet $w): array
    {
        $base = ['free_per_day' => (int) config('worksitem_ai_credits.free_per_day', 1)];
        if (! $w) {
            return $base + ['logged_in' => false];
        }
        $w->refresh();

        return $base + ['logged_in' => true, 'phone' => $w->maskedPhone(), 'free_left' => $w->freeLeft(), 'credits' => (int) $w->credits];
    }
}
