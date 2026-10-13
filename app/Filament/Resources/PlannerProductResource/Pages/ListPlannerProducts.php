<?php

namespace App\Filament\Resources\PlannerProductResource\Pages;

use App\Filament\Resources\PlannerProductResource;
use App\Services\Planner\CatalogSheet;
use Filament\Actions;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ListPlannerProducts extends ListRecords
{
    protected static string $resource = PlannerProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Yeni ürün'),
            self::importAction(),
            self::templateAction(),
        ];
    }

    /** Firmaya gönderilecek Excel şablonu (ürünler + seramikler) */
    public static function templateAction(): Actions\Action
    {
        return Actions\Action::make('template')->label('Excel şablonunu indir')->icon('heroicon-o-arrow-down-tray')->color('gray')
            ->action(fn () => response()->streamDownload(function () {
                (new Xlsx(app(CatalogSheet::class)->template()))->save('php://output');
            }, 'worksitem-sponsor-urun-sablonu.xlsx'));
    }

    /** Firmanın doldurduğu şablonu içe aktarır (ürünler ve seramikler; aynı stok kodu güncellenir) */
    public static function importAction(): Actions\Action
    {
        return Actions\Action::make('import')->label('Excel\'den içe aktar')->icon('heroicon-o-arrow-up-tray')->color('gray')
            ->form([
                FileUpload::make('file')->label('Doldurulmuş şablon (.xlsx)')->disk('local')->directory('planner-imports')
                    ->acceptedFileTypes(['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/octet-stream'])
                    ->maxSize(10240)->required(),
            ])
            ->action(function (array $data) {
                $path = storage_path('app/private/'.$data['file']);
                if (! is_file($path)) {
                    $path = storage_path('app/'.$data['file']);
                }
                try {
                    $r = app(CatalogSheet::class)->import($path);
                } catch (\Throwable $e) {
                    Notification::make()->title('Dosya okunamadı')->body('Lütfen Worksitem şablonunu .xlsx olarak yükleyin.')->danger()->send();

                    return;
                } finally {
                    @unlink($path);
                }
                Notification::make()->title("{$r['products']} ürün, {$r['tiles']} seramik aktarıldı")
                    ->body($r['skipped'] ? "Atlanan satır: {$r['skipped']}. ".implode(' ', array_slice($r['errors'], 0, 5)).' Görsel ve 3D modelleri ürün kayıtlarına ayrıca yükleyin.' : 'Görsel ve 3D modelleri ürün kayıtlarına ayrıca yükleyin.')
                    ->success()->persistent()->send();
            });
    }
}
