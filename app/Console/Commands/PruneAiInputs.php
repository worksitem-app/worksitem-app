<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

/**
 * KVKK — saklama süresi: kullanıcıların AI iç mekan tasarımı için yüklediği
 * oda fotoğrafları (storage/app/public/ai_inputs) public URL ile süresiz duruyordu.
 * Belirtilen günden eski dosyalar silinir. Zamanlayıcıda her gece çalışır.
 */
class PruneAiInputs extends Command
{
    protected $signature = 'app:prune-ai-inputs {--days=30 : Bu günden eski dosyalar silinir} {--dry-run}';

    protected $description = 'Kullanıcıların yüklediği eski AI giriş görsellerini siler (KVKK saklama süresi)';

    public function handle(): int
    {
        $days = max(1, (int) $this->option('days'));
        $cutoff = now()->subDays($days)->getTimestamp();
        $deleted = 0;

        // v107: yeni yüklemeler özel diskte; eski public kopyalar da temizlenir
        foreach (['local', 'public'] as $name) {
            $disk = Storage::disk($name);
            foreach ($disk->allFiles('ai_inputs') as $file) {
                if ($disk->lastModified($file) >= $cutoff) {
                    continue;
                }

                if (! $this->option('dry-run')) {
                    $disk->delete($file);
                }
                $deleted++;
            }
        }

        $this->info(($this->option('dry-run') ? '[dry-run] ' : '')."{$deleted} dosya silindi ({$days} günden eski).");

        return self::SUCCESS;
    }
}
