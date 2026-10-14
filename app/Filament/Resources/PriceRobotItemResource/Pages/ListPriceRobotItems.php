<?php

namespace App\Filament\Resources\PriceRobotItemResource\Pages;

use App\Filament\Resources\PriceRobotItemResource;
use App\Models\PriceRobotItem;
use App\Services\Pricing\PriceRobot;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListPriceRobotItems extends ListRecords
{
    protected static string $resource = PriceRobotItemResource::class;

    public function mount(): void
    {
        // Katalogdaki kalemler henüz araştırılmamış olsa da listede görünsün
        try {
            foreach (app(PriceRobot::class)->catalog() as $path => $it) {
                PriceRobotItem::query()->firstOrCreate(['path' => $path], [
                    'tool_slug' => $it['tool'] ?? null, 'label' => $it['label'], 'unit' => $it['unit'] ?? null,
                    'default_value' => ($it['kind'] ?? '') === 'poz' ? null : config($path), 'status' => 'new',
                ]);
            }
        } catch (\Throwable) {
        }
        parent::mount();
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('run')->label('Şimdi 5 kalem araştır')->icon('heroicon-o-magnifying-glass')
                ->requiresConfirmation()->modalDescription('Sıradaki 5 kalem Google\'da aranır (yaklaşık 1–2 dakika).')
                ->action(function () {
                    $robot = app(PriceRobot::class);
                    $sum = ['live' => 0, 'needs_review' => 0, 'error' => 0];
                    foreach ($robot->due(5) as $it) {
                        $r = $robot->run($it);
                        $sum[$r['status']] = ($sum[$r['status']] ?? 0) + 1;
                    }
                    Notification::make()->title("{$sum['live']} yayında · {$sum['needs_review']} onay bekliyor · {$sum['error']} bulunamadı")->success()->send();
                }),
        ];
    }
}
