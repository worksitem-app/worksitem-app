<?php

namespace App\Console\Commands;

use App\Models\ToolStatus;
use Illuminate\Console\Command;

/** Canlıya çıkışta araçları ön ayar listesiyle yayına alır (admin › Araç Yayın Durumu ile aynı kayıtlar) */
class PublishTools extends Command
{
    protected $signature = 'tools:publish
        {preset? : config/worksitem_golive.php içindeki ön ayar adı}
        {--list : Ön ayarları ve şu anki durumu göster}
        {--only : Listede olmayan tüm araçları incelemeye al}
        {--dry-run : Değişiklik yapmadan ne olacağını göster}';

    protected $description = 'Araç yayın durumunu ön ayar listesine göre günceller';

    public function handle(): int
    {
        ToolStatus::syncFromConfig();
        $presets = (array) config('worksitem_golive.presets', []);
        $known = ToolStatus::query()->pluck('active', 'slug');

        if ($this->option('list') || ! $this->argument('preset')) {
            $this->info('Şu an yayında: '.$known->filter()->count().' / '.$known->count());
            foreach ($presets as $name => $slugs) {
                $this->line("\n<fg=yellow>$name</> (".count($slugs).' araç)');
                foreach ($slugs as $s) {
                    $this->line('  '.(($known[$s] ?? false) ? '<fg=green>● yayında</>  ' : '○ incelemede ').$s.($known->has($s) ? '' : ' <fg=red>(bilinmeyen araç)</>'));
                }
            }

            return self::SUCCESS;
        }

        $name = (string) $this->argument('preset');
        if (! isset($presets[$name])) {
            $this->error("Ön ayar bulunamadı: $name (".implode(', ', array_keys($presets)).')');

            return self::FAILURE;
        }
        $list = $presets[$name];
        $unknown = array_diff($list, $known->keys()->all());
        if ($unknown) {
            $this->warn('Bilinmeyen araçlar atlandı: '.implode(', ', $unknown));
        }

        $on = array_values(array_intersect($list, $known->keys()->all()));
        $off = $this->option('only') ? $known->keys()->diff($on)->values()->all() : [];
        $this->line(($this->option('dry-run') ? '[deneme] ' : '').'Yayına alınacak: '.count($on).($off ? ' · incelemeye alınacak: '.count($off) : ''));

        if (! $this->option('dry-run')) {
            foreach ($on as $s) {
                ToolStatus::query()->where('slug', $s)->first()?->update(['active' => true, 'note' => 'tools:publish '.$name]);
            }
            foreach ($off as $s) {
                ToolStatus::query()->where('slug', $s)->first()?->update(['active' => false]);
            }
        }
        foreach ($on as $s) {
            $this->line('  <fg=green>●</> '.$s);
        }
        $this->info('Tamam. Şimdi yayında: '.ToolStatus::query()->where('active', true)->count().' araç.');

        return self::SUCCESS;
    }
}
