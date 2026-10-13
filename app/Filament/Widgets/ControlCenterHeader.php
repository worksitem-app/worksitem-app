<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\LeadResource;
use App\Filament\Resources\ToolStatusResource;
use App\Models\Lead;
use App\Models\ToolStatus;
use App\Support\SystemHealth;
use Filament\Widgets\Widget;

/** Üst şerit: ortam, bugün, dikkat gerektirenler ve hızlı işlemler */
class ControlCenterHeader extends Widget
{
    protected static string $view = 'filament.widgets.control-center-header';

    protected static ?int $sort = -10;

    protected int|string|array $columnSpan = 'full';

    protected static ?string $pollingInterval = '60s';

    protected function getViewData(): array
    {
        $health = SystemHealth::checks();
        $sum = SystemHealth::summary($health);
        $leadsUrl = fn (array $f) => LeadResource::getUrl('index', ['tableFilters' => $f]);

        $untouched = Lead::query()->where('status', 'yeni')->count();
        $awaitingPay = Lead::query()->where('inspection_status', 'kabul')->count();
        $sponsorApps = Lead::query()->where('source', 'sponsor_basvurusu')->where('status', 'yeni')->count();
        $appts = Lead::query()->whereNotNull('inspection_appointment_at')
            ->whereBetween('inspection_appointment_at', [now()->startOfDay(), now()->addDay()->endOfDay()])->count();
        $overdue = Lead::query()->whereNotNull('next_action_at')->where('next_action_at', '<', now())
            ->whereNotIn('status', ['kazanildi', 'kaybedildi', 'iptal'])->count();

        $alerts = array_values(array_filter([
            $untouched ? ['tone' => 'warn', 'text' => $untouched.' yeni talep aranmayı bekliyor', 'url' => $leadsUrl(['status' => ['values' => ['yeni']]])] : null,
            $awaitingPay ? ['tone' => 'warn', 'text' => $awaitingPay.' keşif siparişinde ödeme bekleniyor', 'url' => $leadsUrl(['inspection_status' => ['value' => 'kabul']])] : null,
            $appts ? ['tone' => 'info', 'text' => 'Bugün/yarın '.$appts.' keşif randevusu', 'url' => LeadResource::getUrl('index')] : null,
            $overdue ? ['tone' => 'fail', 'text' => $overdue.' talepte takip tarihi geçti', 'url' => LeadResource::getUrl('index')] : null,
            $sponsorApps ? ['tone' => 'info', 'text' => $sponsorApps.' yeni sponsorluk başvurusu', 'url' => $leadsUrl(['source' => ['value' => 'sponsor_basvurusu']])] : null,
            $sum['fail'] ? ['tone' => 'fail', 'text' => $sum['fail'].' sistem kontrolü hatalı', 'url' => '#ws-health'] : null,
        ]));

        // Etkin durum: config varsayılanı + admin'den gelen kayıtlar (ToolStatus::applyToConfig ile birleşik)
        $st = (array) config('worksitem_tools.tool_status', []);
        $slugs = array_keys((array) config('worksitem_tools.tools', []));
        $over = array_merge((array) ($st['overrides'] ?? []), ToolStatus::overrides());
        $live = count(array_filter($slugs, fn ($s) => ($over[$s] ?? ($st['default'] ?? 'review')) === 'active'));
        $tools = count($slugs);

        return [
            'env' => app()->environment(),
            'host' => parse_url((string) config('app.url'), PHP_URL_HOST),
            'alerts' => $alerts,
            'health' => $sum,
            'live' => $live,
            'tools' => $tools,
            'toolsUrl' => ToolStatusResource::getUrl('index'),
            'leadsUrl' => LeadResource::getUrl('index'),
            'sponsorsUrl' => \App\Filament\Resources\SponsorResource::getUrl('index'),
            'walletsUrl' => \App\Filament\Resources\AiWalletResource::getUrl('index'),
            'pricesUrl' => \App\Filament\Resources\UnitPriceResource::getUrl('index'),
            'name' => strtok((string) auth()->user()?->name, ' ') ?: 'Merhaba',
        ];
    }
}
