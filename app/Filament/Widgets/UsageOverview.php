<?php

namespace App\Filament\Widgets;

use App\Models\AiCreditTransaction;
use App\Models\AiUsageLedger;
use App\Models\AiWallet;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Cache;

/** Ürün kullanımı ve maliyet: AI, SMS, telefonla giriş */
class UsageOverview extends BaseWidget
{
    protected static ?int $sort = 5;

    protected int|string|array $columnSpan = 'full';

    protected static ?string $pollingInterval = '120s';

    protected ?string $heading = 'Kullanım ve maliyet';

    protected function getStats(): array
    {
        $today = now('Europe/Istanbul')->startOfDay();
        $aiToday = AiCreditTransaction::query()->whereIn('kind', ['free', 'use'])->where('created_at', '>=', $today)->count();
        $aiRefund = AiCreditTransaction::query()->where('kind', 'refund')->where('created_at', '>=', $today)->count();
        $daily = collect(range(13, 0))->map(fn ($d) => AiCreditTransaction::query()->whereIn('kind', ['free', 'use'])
            ->whereBetween('created_at', [now()->subDays($d)->startOfDay(), now()->subDays($d)->endOfDay()])->count())->all();

        $cost30 = 0.0;
        try {
            $cost30 = (float) AiUsageLedger::query()->where('created_at', '>=', now()->subDays(30))->sum('provider_cost_try');
        } catch (\Throwable) {
        }

        $users = AiWallet::query()->count();
        $active7 = AiWallet::query()->where('last_used_at', '>=', now()->subDays(7))->count();
        $otp = (int) Cache::get('otp-global:'.now('Europe/Istanbul')->format('Ymd'), 0);
        $cap = (int) config('worksitem_sms.otp.global_day', 400);

        return [
            Stat::make('AI kullanımı (bugün)', $aiToday)
                ->description($aiRefund ? $aiRefund.' başarısız (hak iade edildi)' : 'Son 14 gün')
                ->chart($daily)->color('info'),
            Stat::make('AI sağlayıcı maliyeti (30 gün)', '₺'.number_format($cost30, 0, ',', '.'))
                ->description('Gemini / Replicate kayıtlarından')->descriptionIcon('heroicon-m-cpu-chip')->color('gray'),
            Stat::make('Telefonla giriş yapan', number_format($users, 0, ',', '.'))
                ->description($active7.' kişi son 7 günde AI kullandı')->descriptionIcon('heroicon-m-device-phone-mobile')->color('primary'),
            Stat::make('SMS doğrulama kodu (bugün)', $otp.' / '.$cap)
                ->description('Günlük tavan (OTP_GLOBAL_DAY)')->descriptionIcon('heroicon-m-chat-bubble-left-ellipsis')
                ->color($otp >= $cap * 0.8 ? 'danger' : 'success'),
        ];
    }

    protected function getColumns(): int
    {
        return 4;
    }
}
