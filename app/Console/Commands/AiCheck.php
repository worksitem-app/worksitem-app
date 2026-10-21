<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

/**
 * Yapay zekâ hazırlık kontrolü (yalnız okur, ücretli çağrı yapmaz):
 * harcama kilidi, anahtar, günlük tavan, kredi ayarları ve yapılandırılmış Gemini modellerinin
 * sağlayıcıda gerçekten var olup olmadığı (model listesi ücretsizdir). Anahtar asla yazdırılmaz.
 */
class AiCheck extends Command
{
    protected $signature = 'ai:check';

    protected $description = 'Yapay zekâ özelliklerinin hazır olup olmadığını kontrol eder (ücretsiz)';

    public function handle(): int
    {
        $ok = fn ($t) => $this->line('  <fg=green>✔</> '.$t);
        $no = fn ($t) => $this->line('  <fg=red>✘</> '.$t);
        $fail = 0;

        $this->line('▶ Ayarlar');
        config('services.ai_spend_enabled') ? $ok('AI_SPEND_ENABLED açık') : $no('AI_SPEND_ENABLED kapalı (yapay zekâ özellikleri 503 döner)');
        $key = (string) config('services.gemini.api_key');
        $key !== '' ? $ok('GEMINI_API_KEY tanımlı ('.strlen($key).' karakter)') : $no('GEMINI_API_KEY yok');
        $ok('Günlük toplam tavan: '.config('services.ai.global_daily_limit').' istek (AI_GLOBAL_DAILY_LIMIT)');
        $ok('Kullanıcı başına günlük ücretsiz: '.config('worksitem_ai_credits.free_per_day').' · kredi sistemi '.(config('worksitem_ai_credits.enabled') ? 'açık' : 'kapalı'));

        if ($key === '') {
            return self::FAILURE;
        }
        $this->line('▶ Gemini modelleri');
        try {
            $names = [];
            $page = null;
            do {
                $res = Http::timeout(20)->withHeaders(['x-goog-api-key' => $key])
                    ->get('https://generativelanguage.googleapis.com/v1beta/models', array_filter(['pageSize' => 1000, 'pageToken' => $page]));
                if (! $res->successful()) {
                    $no('Model listesi alınamadı: HTTP '.$res->status().' (anahtar geçersiz veya API kapalı olabilir)');

                    return self::FAILURE;
                }
                foreach ((array) $res->json('models') as $m) {
                    $names[] = preg_replace('#^models/#', '', (string) ($m['name'] ?? ''));
                }
                $page = $res->json('nextPageToken');
            } while ($page);
        } catch (\Throwable $e) {
            $no('Gemini sunucusuna bağlanılamadı: '.class_basename($e));

            return self::FAILURE;
        }
        $want = [
            'Metin (keşif, rayiç, satış)' => config('services.gemini.boq_model'),
            'Rayiç PDF okuma' => config('services.gemini.rayic_model'),
            'Fiyat robotu' => config('worksitem_price_robot.model'),
            'Görsel' => config('services.gemini.image_model'),
            'Görsel HD' => config('services.gemini.image_model_hd'),
        ];
        foreach ($want as $label => $model) {
            if (in_array((string) $model, $names, true)) {
                $ok("$label: $model");
            } else {
                $fail++;
                $close = array_values(array_filter($names, fn ($n) => str_contains($n, str_contains((string) $model, 'image') ? 'image' : 'flash')));
                $no("$label: $model sağlayıcıda YOK".($close ? ' · benzerleri: '.implode(', ', array_slice($close, -4)) : ''));
            }
        }
        $this->line($fail ? "Sonuç: $fail model bulunamadı" : 'Sonuç: yapay zekâ hazır');

        return $fail ? self::FAILURE : self::SUCCESS;
    }
}
