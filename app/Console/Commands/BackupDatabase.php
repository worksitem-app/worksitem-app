<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

/**
 * Günlük veritabanı yedeği (MySQL/MariaDB): storage/app/backups/db-TARIH.sql.gz
 * Web'den erişilemez (storage/app/public dışında). Son --keep kadar yedek tutulur.
 *   php artisan app:backup-db            # yedek al
 *   php artisan app:backup-db --keep=30  # 30 yedek tut
 */
class BackupDatabase extends Command
{
    protected $signature = 'app:backup-db {--keep=14 : Saklanacak yedek sayısı}';

    protected $description = 'Veritabanının sıkıştırılmış yedeğini storage/app/backups altına alır, eskileri siler';

    public function handle(): int
    {
        $conn = (string) config('database.default');
        $cfg = (array) config("database.connections.$conn");
        if (! in_array($cfg['driver'] ?? '', ['mysql', 'mariadb'], true)) {
            $this->warn("Yedek yalnızca MySQL/MariaDB için alınır (şu an: {$conn}).");

            return self::SUCCESS;
        }

        $dir = storage_path('app/backups');
        if (! is_dir($dir) && ! mkdir($dir, 0750, true) && ! is_dir($dir)) {
            $this->error("Klasör oluşturulamadı: $dir");

            return self::FAILURE;
        }
        $file = $dir.'/db-'.now('Europe/Istanbul')->format('Ymd-His').'.sql.gz';

        $cmd = 'mysqldump --single-transaction --quick --no-tablespaces'
            .' -h '.escapeshellarg((string) ($cfg['host'] ?? '127.0.0.1'))
            .' -P '.escapeshellarg((string) ($cfg['port'] ?? '3306'))
            .' -u '.escapeshellarg((string) ($cfg['username'] ?? ''))
            .' '.escapeshellarg((string) ($cfg['database'] ?? ''))
            .' | gzip > '.escapeshellarg($file);

        $p = new Process(['bash', '-c', 'set -o pipefail; '.$cmd], null, ['MYSQL_PWD' => (string) ($cfg['password'] ?? '')], null, 600);
        $p->run();

        if (! $p->isSuccessful() || ! is_file($file) || filesize($file) < 200) {
            @unlink($file);
            $this->error('Yedek alınamadı: '.trim(mb_substr($p->getErrorOutput(), 0, 300)));
            report(new \RuntimeException('app:backup-db başarısız'));

            return self::FAILURE;
        }
        @chmod($file, 0640);

        $keep = max(1, (int) $this->option('keep'));
        $all = glob($dir.'/db-*.sql.gz') ?: [];
        rsort($all);   // en yeni önce (tarih adlı)
        foreach (array_slice($all, $keep) as $old) {
            @unlink($old);
        }

        $this->info('Yedek: '.basename($file).' ('.round(filesize($file) / 1048576, 2).' MB) · tutulan: '.min(count($all), $keep));

        return self::SUCCESS;
    }
}
