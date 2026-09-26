<?php

namespace App\Console\Commands;

use App\Models\TenderPoz;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class EnrichPozSeo extends Command
{
    protected $signature = 'poz:enrich-seo
                            {--limit=10 : Kaç poz işlenecek}
                            {--ids-file= : Sadece bu ID dosyasındaki pozları işle}';

    protected $description = 'AI ile poz SEO açıklamalarını zenginleştir';

    public function handle(): int
    {
        $limit = (int) $this->option('limit');
        $idsFile = $this->option('ids-file');

        $query = TenderPoz::query()
            ->where('is_active', true)
            ->where('seo_enriched', false);

        if ($idsFile && file_exists($idsFile)) {
            $ids = json_decode(file_get_contents($idsFile), true);
            if (is_array($ids) && !empty($ids)) {
                $query->whereIn('id', $ids);
                $this->info("Filtre: {$idsFile} (" . count($ids) . " ID)");
            }
        }

        $pozs = $query->orderBy('poz_no')->limit($limit)->get();

        if ($pozs->isEmpty()) {
            $this->warn('İşlenecek poz bulunamadı.');
            return self::SUCCESS;
        }

        $this->info("İşlenecek poz: {$pozs->count()}");
        $bar = $this->output->createProgressBar($pozs->count());
        $bar->start();

        $success = 0;
        $failed = 0;

        foreach ($pozs as $poz) {
            try {
                $this->enrich($poz);
                $success++;
            } catch (\Throwable $e) {
                $failed++;
                Log::error('Poz SEO enrich failed', [
                    'poz_id' => $poz->id,
                    'poz_no' => $poz->poz_no,
                    'error' => $e->getMessage(),
                ]);
            }

            $bar->advance();
            usleep(1000000);
        }

        $bar->finish();
        $this->newLine(2);
        $this->info("✅ Başarılı: {$success}");
        if ($failed > 0) {
            $this->warn("❌ Başarısız: {$failed}");
        }

        return self::SUCCESS;
    }

    private function enrich(TenderPoz $poz): void
    {
        $apiKey = config('services.gemini.api_key');
        if (empty($apiKey)) {
            throw new \RuntimeException('Gemini API key bulunamadı');
        }

        $prompt = "Sen Türk inşaat sektöründe SEO içerik uzmanısın. Şu inşaat pozu için SEO-optimized Türkçe içerik üret:

Poz No: {$poz->poz_no}
Açıklama: {$poz->description}
Birim: {$poz->unit}
Kurum: {$poz->institution}

KURALLAR:
- long_description: 150-250 kelime, doğal Türkçe. Şunları içer:
  1) Ne olduğu
  2) Nerede kullanıldığı
  3) Teknik özellikleri
  4) Avantajları
- meta_description: Tam 155 karakter
- meta_keywords: 8-12 anahtar kelime, virgülle ayrılmış
- faq: 3-5 soru-cevap

SADECE JSON formatında çıktı ver, başka metin YOK:
{
  \"long_description\": \"...\",
  \"meta_description\": \"...\",
  \"meta_keywords\": \"kelime1,kelime2\",
  \"faq\": [
    {\"q\": \"Soru?\", \"a\": \"Cevap\"}
  ]
}";

        $response = Http::withHeaders([
            'x-goog-api-key' => $apiKey,
        ])->timeout(90)->post(
            'https://generativelanguage.googleapis.com/v1beta/models/gemini-3.6-flash:generateContent',
            [
                'contents' => [['parts' => [['text' => $prompt]]]],
                'generationConfig' => [
                    'temperature' => 0.3,
                    'maxOutputTokens' => 8000,
                    'thinkingConfig' => [
                        'thinkingBudget' => 0,
                    ],
                ],
            ]
        );

        if (!$response->successful()) {
            throw new \RuntimeException('Gemini API hata: ' . $response->status());
        }

        $text = $response->json()['candidates'][0]['content']['parts'][0]['text'] ?? '';

        // JSON çıkar — çoklu format
        $json = null;

        // 1. Markdown code block
        if (preg_match('/```(?:json)?\s*(\{.*?\})\s*```/s', $text, $m)) {
            $json = $m[1];
        }
        // 2. Direkt JSON
        elseif (preg_match('/(\{.*\})/s', $text, $m)) {
            $json = $m[1];
        }

        if ($json === null) {
            throw new \RuntimeException('AI yanıtı JSON içermiyor: ' . substr($text, 0, 200));
        }

        $data = json_decode($json, true);
        if (!is_array($data)) {
            throw new \RuntimeException('JSON parse hatası: ' . substr($json, 0, 200));
        }

        $poz->update([
            'long_description' => $data['long_description'] ?? null,
            'meta_description' => $data['meta_description'] ?? null,
            'meta_keywords' => $data['meta_keywords'] ?? null,
            'faq_data' => $data['faq'] ?? null,
            'seo_enriched' => true,
        ]);
    }
}
