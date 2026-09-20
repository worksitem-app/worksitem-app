<?php

namespace App\Services;

use App\Models\TenderPoz;
use RuntimeException;

class TenderCsvImportService
{
    public function import(string $path, array $defaults = []): array
    {
        if (!is_file($path)) {
            throw new RuntimeException('CSV dosyası bulunamadı.');
        }

        $h = fopen($path, 'r');
        $first = fgets($h);
        rewind($h);

        $delimiter = substr_count($first, ';') >= substr_count($first, ',') ? ';' : ',';
        $headers = fgetcsv($h, 0, $delimiter);

        $headers = array_map(function ($v) {
            $v = preg_replace('/^\xEF\xBB\xBF/', '', trim((string) $v));
            return strtolower($v);
        }, $headers ?: []);

        if (!in_array('poz_no', $headers, true)) {
            fclose($h);
            throw new RuntimeException('CSV başlıklarında poz_no bulunamadı.');
        }

        $count = 0;
        $skipped = 0;

        while (($row = fgetcsv($h, 0, $delimiter)) !== false) {
            if (count($row) !== count($headers)) {
                $skipped++;
                continue;
            }

            $r = array_combine($headers, $row);

            $poz = trim((string) ($r['poz_no'] ?? ''));
            $desc = trim((string) ($r['description'] ?? ''));

            if ($poz === '' || $desc === '') {
                $skipped++;
                continue;
            }

            $material = $this->num($r['material_price'] ?? null);
            $install = $this->num($r['installation_price'] ?? null);
            $unitPrice = $this->num($r['unit_price'] ?? null);

            if ($material !== null || $install !== null) {
                $unitPrice = ($material ?? 0) + ($install ?? 0);
            }

            if ($unitPrice === null || $unitPrice <= 0) {
                $skipped++;
                continue;
            }

            $profitIncluded = $this->bool(
                $r['profit_included'] ?? ($defaults['profit_included'] ?? false)
            );

            $profitRate = $this->rate(
                $r['included_profit_rate'] ?? ($defaults['included_profit_rate'] ?? null)
            );

            TenderPoz::updateOrCreate(
                [
                    'institution' => trim((string) ($r['institution'] ?? $defaults['institution'])),
                    'period' => trim((string) ($r['period'] ?? $defaults['period'])),
                    'poz_no' => $poz,
                    'price_type' => trim((string) ($r['price_type'] ?? $defaults['price_type'] ?? 'unit_price')),
                ],
                [
                    'year' => trim((string) ($r['year'] ?? $defaults['year'])),
                    'description' => $desc,
                    'unit' => trim((string) ($r['unit'] ?? '')),
                    'unit_price' => $unitPrice,
                    'material_price' => $material,
                    'installation_price' => $install,
                    'profit_included' => $profitIncluded,
                    'included_profit_rate' => $profitIncluded ? $profitRate : null,
                    'vat_included' => $this->bool(
                        $r['vat_included'] ?? ($defaults['vat_included'] ?? false)
                    ),
                    'source_file' => trim((string) ($r['source_file'] ?? basename($path))),
                    'source_page' => $this->intOrNull($r['source_page'] ?? null),
                    'source_note' => trim((string) ($r['source_note'] ?? '')) ?: null,
                    'is_active' => true,
                ]
            );

            $count++;
        }

        fclose($h);

        return ['imported' => $count, 'skipped' => $skipped];
    }

    private function num($value): ?float
    {
        $v = trim(str_replace(['TL', '₺', ' '], '', (string) $value));
        if ($v === '') return null;

        if (str_contains($v, ',') && str_contains($v, '.')) {
            $v = str_replace('.', '', $v);
            $v = str_replace(',', '.', $v);
        } elseif (str_contains($v, ',')) {
            $v = str_replace(',', '.', $v);
        }

        return is_numeric($v) ? (float) $v : null;
    }

    private function rate($value): ?float
    {
        $n = $this->num($value);
        if ($n === null) return null;
        return $n > 1 ? $n / 100 : $n;
    }

    private function bool($value): bool
    {
        return in_array(strtolower(trim((string) $value)),
            ['1','true','evet','yes','dahil'], true);
    }

    private function intOrNull($value): ?int
    {
        return is_numeric($value) ? (int) $value : null;
    }
}
