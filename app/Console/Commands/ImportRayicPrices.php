<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class ImportRayicPrices extends Command
{
    protected $signature = 'rayic:import
                            {--file= : İçe aktarılacak CSV dosyası}
                            {--dry-run : Veritabanına yazmadan kontrol et}
                            {--source=YFK : Kaynak kurum}
                            {--period= : Fiyat dönemi, örn. 2026-08}';

    protected $description = 'YFK rayiç fiyatlarını CSV dosyasından rayic_prices tablosuna aktarır';

    public function handle(): int
    {
        $file = $this->option('file');

        if (!$file) {
            $files = File::glob(storage_path('app/rayic/*.csv'));

            if (empty($files)) {
                $this->error('CSV dosyası bulunamadı: storage/app/rayic/');
                return self::FAILURE;
            }

            $this->info('Bulunan CSV dosyaları:');

            foreach ($files as $index => $path) {
                $this->line(($index + 1) . '. ' . basename($path));
            }

            $this->error('Dosya belirtmek için --file=dosya.csv kullanın.');
            return self::FAILURE;
        }

        if (!str_starts_with($file, '/')) {
            $file = storage_path('app/rayic/' . ltrim($file, '/'));
        }

        if (!File::exists($file)) {
            $this->error("Dosya bulunamadı: {$file}");
            return self::FAILURE;
        }

        $handle = fopen($file, 'r');

        if (!$handle) {
            $this->error("CSV dosyası açılamadı: {$file}");
            return self::FAILURE;
        }

        $header = fgetcsv($handle);

        if (!$header) {
            fclose($handle);
            $this->error('CSV başlığı okunamadı.');
            return self::FAILURE;
        }

        // UTF-8 BOM temizle
        $header[0] = preg_replace('/^\xEF\xBB\xBF/', '', $header[0]);

        $required = [
            'source',
            'period',
            'poz_no',
            'description',
            'unit',
            'price',
            'source_file',
            'source_page',
            'category',
        ];

        $missing = array_diff($required, $header);

        if (!empty($missing)) {
            fclose($handle);
            $this->error('Eksik CSV kolonları: ' . implode(', ', $missing));
            return self::FAILURE;
        }

        $headerMap = array_flip($header);

        $count = 0;
        $valid = 0;
        $invalid = 0;
        $duplicates = 0;

        $source = $this->option('source');
        $periodOption = $this->option('period');
        $dryRun = (bool) $this->option('dry-run');

        $this->info('Dosya: ' . basename($file));
        $this->info('Mod: ' . ($dryRun ? 'DRY-RUN — DB değişmeyecek' : 'GERÇEK IMPORT'));
        $this->line('');

        while (($row = fgetcsv($handle)) !== false) {
            $count++;

            if (count($row) < count($header)) {
                $invalid++;
                continue;
            }

            $data = [];

            foreach ($header as $column) {
                $data[$column] = isset($row[$headerMap[$column]])
                    ? trim($row[$headerMap[$column]])
                    : null;
            }

            $data['source'] = $source ?: ($data['source'] ?: 'YFK');
            $data['period'] = $periodOption ?: $data['period'];

            if (
                empty($data['period']) ||
                !preg_match('/^\d{4}-\d{2}$/', $data['period']) ||
                empty($data['poz_no']) ||
                empty($data['description']) ||
                empty($data['unit']) ||
                $data['price'] === '' ||
                !is_numeric($data['price'])
            ) {
                $invalid++;
                continue;
            }

            $valid++;

            if ($dryRun) {
                continue;
            }

            $now = now();

            DB::table('rayic_prices')->upsert(
                [[
                    'source' => $data['source'],
                    'period' => $data['period'],
                    'poz_no' => $data['poz_no'],
                    'description' => $data['description'],
                    'unit' => $data['unit'],
                    'price' => (float) $data['price'],
                    'source_file' => $data['source_file'] ?: null,
                    'source_page' => $data['source_page'] !== ''
                        ? (int) $data['source_page']
                        : null,
                    'category' => $data['category'] ?: null,
                    'is_active' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]],
                ['source', 'period', 'poz_no'],
                [
                    'description',
                    'unit',
                    'price',
                    'source_file',
                    'source_page',
                    'category',
                    'is_active',
                    'updated_at',
                ]
            );
        }

        fclose($handle);

        $this->line('');
        $this->info('=== IMPORT RAPORU ===');
        $this->line("Okunan satır : {$count}");
        $this->line("Geçerli       : {$valid}");
        $this->line("Hatalı        : {$invalid}");

        if ($dryRun) {
            $this->info('DRY-RUN tamamlandı. Veritabanında değişiklik yapılmadı.');
        } else {
            $this->info('Import tamamlandı.');
        }

        return self::SUCCESS;
    }
}
