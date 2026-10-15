<?php

namespace App\Console\Commands;

use App\Services\Push\WebPush;
use Illuminate\Console\Command;

/** VAPID anahtarlarını üretir; --write ile .env'e yazar (gizli anahtar ekrana basılmaz). Varsa dokunmaz. */
class PushKeys extends Command
{
    protected $signature = 'push:keys {--write : .env dosyasına yaz}';

    protected $description = 'Web bildirimleri için VAPID anahtarlarını üretir';

    public function handle(): int
    {
        $env = base_path('.env');
        $cur = is_file($env) ? (string) file_get_contents($env) : '';
        if (preg_match('/^VAPID_PRIVATE_PEM_B64=.+$/m', $cur)) {
            $this->info('VAPID anahtarları zaten var; değiştirilmedi.');

            return self::SUCCESS;
        }
        [$pub, $priv] = WebPush::generateKeys();
        if (! $this->option('write')) {
            $this->line('Açık anahtar: '.$pub);
            $this->line('Yazmak için: php artisan push:keys --write');

            return self::SUCCESS;
        }
        copy($env, $env.'.bak-push-'.date('YmdHis'));
        file_put_contents($env, rtrim($cur)."\n\n# Web bildirimleri (VAPID)\nVAPID_PUBLIC_KEY=$pub\nVAPID_PRIVATE_PEM_B64=$priv\n");
        $this->info('VAPID anahtarları .env dosyasına yazıldı (gizli anahtar gösterilmez). Şimdi: php artisan config:cache');

        return self::SUCCESS;
    }
}
