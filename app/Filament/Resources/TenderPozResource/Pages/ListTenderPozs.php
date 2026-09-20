<?php

namespace App\Filament\Resources\TenderPozResource\Pages;

use App\Filament\Resources\TenderPozResource;
use App\Services\TenderCsvImportService;
use Filament\Actions;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListTenderPozs extends ListRecords
{
    protected static string $resource = TenderPozResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),

            Actions\Action::make('importTender')
                ->label('Resmî Poz Dosyası Yükle')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('success')
                ->form([
                    Select::make('institution')
                        ->label('Varsayılan Kurum')
                        ->options([
                            'ÇŞİDB/YFK' => 'ÇŞİDB / YFK',
                            'DSİ' => 'DSİ',
                            'İLBANK' => 'İLBANK',
                            'Karayolları' => 'Karayolları',
                            'MSB' => 'MSB',
                            'Kültür/Vakıflar' => 'Kültür / Vakıflar',
                            'PTT' => 'PTT',
                            'Ulaştırma' => 'Ulaştırma',
                        ])->required(),

                    TextInput::make('year')
                        ->label('Yıl')->default('2026')->required(),

                    TextInput::make('period')
                        ->label('Dönem')
                        ->default('2026-09')
                        ->required(),

                    Select::make('price_type')
                        ->label('Fiyat Türü')
                        ->options([
                            'unit_price' => 'Birim Fiyat',
                            'rayic' => 'Rayiç',
                        ])->required(),

                    Toggle::make('profit_included')
                        ->label('Fiyatta yüklenici kârı/genel gider var'),

                    TextInput::make('included_profit_rate')
                        ->label('Dahil Kâr/Genel Gider Oranı (%)')
                        ->numeric(),

                    Toggle::make('vat_included')
                        ->label('KDV Dahil'),

                    FileUpload::make('file')
                        ->label('Normalize CSV')
                        ->directory('tender-imports')
                        ->preserveFilenames()
                        ->acceptedFileTypes([
                            'text/csv',
                            'text/plain',
                            'text/x-csv',
                            'application/csv',
                            'application/vnd.ms-excel',
                            'text/comma-separated-values',
                        ])
                        ->required(),
                ])
                ->action(function (array $data) {
                    $path = storage_path('app/public/' . $data['file']);

                    $result = app(TenderCsvImportService::class)->import($path, [
                        'institution' => $data['institution'],
                        'year' => $data['year'],
                        'period' => $data['period'],
                        'price_type' => $data['price_type'],
                        'profit_included' => $data['profit_included'] ?? false,
                        'included_profit_rate' => $data['included_profit_rate'] ?? null,
                        'vat_included' => $data['vat_included'] ?? false,
                    ]);

                    @unlink($path);

                    Notification::make()
                        ->title("{$result['imported']} poz aktarıldı")
                        ->body("Atlanan satır: {$result['skipped']}")
                        ->success()
                        ->send();
                }),
        ];
    }
}
