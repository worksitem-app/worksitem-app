<?php

namespace App\Console\Commands;

use App\Models\PriceRobotItem;
use App\Services\Pricing\PriceRobot;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;

/**
 * Her sabah: yeni (hiç araştırılmamış) ve eskiyen fiyat kalemlerini araştırır.
 *   php artisan prices:robot                 # günlük kota kadar
 *   php artisan prices:robot --list          # kalemler ve durumları
 *   php artisan prices:robot --limit=3 --dry-run
 *   php artisan prices:robot --path=worksitem_estimators.parke.material_m2.laminat
 */
class RunPriceRobot extends Command
{
    protected $signature = 'prices:robot {--limit= : Bu çalıştırmada en fazla kaç kalem} {--path= : Yalnız bu kalem (kilitli olsa bile)} {--dry-run : Kaydetmeden göster} {--list : Kalemleri listele}';

    protected $description = 'Fiyat robotu: önce resmî poz, yoksa Google fiyatı (× çarpan); şüpheli sonuç onaya düşer';

    public function handle(PriceRobot $robot): int
    {
        if (! Schema::hasTable('price_robot_items')) {
            $this->error('price_robot_items tablosu yok: php artisan migrate');

            return self::FAILURE;
        }

        if ($this->option('list')) {
            $rows = PriceRobotItem::query()->get()->keyBy('path');
            $this->table(['Kalem', 'Araç', 'Durum', 'Yayındaki', 'Araştırma'], collect($robot->catalog())->map(fn ($it, $p) => [
                mb_substr($it['label'], 0, 40), $it['tool'] ?? '-', $rows->get($p)?->status ?? 'yeni',
                PriceRobotItem::fmt($rows->get($p)?->value), $rows->get($p)?->researched_at?->format('d.m.Y') ?? '-',
            ])->values()->all());

            return self::SUCCESS;
        }

        if (! config('worksitem_price_robot.enabled') && ! $this->option('path')) {
            $this->warn('Fiyat robotu kapalı (PRICE_ROBOT_ENABLED=false).');

            return self::SUCCESS;
        }

        $limit = (int) ($this->option('limit') ?: config('worksitem_price_robot.daily_limit', 20));
        $items = $robot->due($limit, $this->option('path') ?: null);
        if (! $items) {
            $this->info('Bugün araştırılacak kalem yok (hepsi güncel ya da kilitli).');

            return self::SUCCESS;
        }

        $sum = ['live' => 0, 'needs_review' => 0, 'error' => 0];
        foreach ($items as $it) {
            $r = $robot->run($it, (bool) $this->option('dry-run'));
            $sum[$r['status']] = ($sum[$r['status']] ?? 0) + 1;
            $line = str_pad(mb_substr($it['label'], 0, 42), 44).' ';
            $line .= match ($r['status']) {
                'live' => '✔ yayında: '.PriceRobotItem::fmt($r['value']),
                'needs_review' => '⏸ onay bekliyor: '.PriceRobotItem::fmt($r['proposed']).' ('.implode('; ', $r['reasons']).')',
                default => '✘ '.mb_substr($r['message'] ?? implode('; ', $r['reasons'] ?? []), 0, 120),
            };
            $this->line($line);
        }
        $this->newLine();
        $this->info("Bitti: {$sum['live']} yayında · {$sum['needs_review']} onay bekliyor · {$sum['error']} hata".($this->option('dry-run') ? ' (deneme, kaydedilmedi)' : ''));

        return self::SUCCESS;
    }
}
