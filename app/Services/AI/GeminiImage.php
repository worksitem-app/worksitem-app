<?php

namespace App\Services\AI;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Gemini ile görsel üretimi (metin + referans görseller → tek görsel).
 * Kullanım kaydı AiUsageRecorder ile tutulur; hata durumunda kullanıcıya gösterilebilir mesajla RuntimeException fırlatır.
 */
class GeminiImage
{
    /**
     * @param  array<int, array{mime:string, bytes:string}>  $images
     * @param  array{hd?: bool}  $opts  hd: yüksek çözünürlük (services.gemini.image_model_hd + image_hd_size)
     * @return array{bytes:string, mime:string, model:string}
     */
    public function generate(string $prompt, array $images, string $toolKey, array $meta = [], array $opts = []): array
    {
        $hd = ! empty($opts['hd']);
        $apiKey = config('services.gemini.api_key');
        if (! $apiKey) {
            throw new \RuntimeException('Görsel servisi yapılandırılmamış.');
        }
        $model = $hd ? config('services.gemini.image_model_hd', config('services.gemini.image_model', 'gemini-3.1-flash-image'))
            : config('services.gemini.image_model', 'gemini-3.1-flash-image');
        $gen = ['responseModalities' => ['IMAGE']];
        if ($hd) {
            $gen['imageConfig'] = ['imageSize' => (string) config('services.gemini.image_hd_size', '2K')];
        }
        $parts = [['text' => $prompt]];
        foreach ($images as $img) {
            $parts[] = ['inline_data' => ['mime_type' => $img['mime'], 'data' => base64_encode($img['bytes'])]];
        }

        $rec = app(AiUsageRecorder::class);
        $usage = $rec->create([
            'user_id' => auth()->id(), 'tool_key' => $toolKey, 'provider' => 'gemini', 'model' => $model,
            'operation' => 'image_generation', 'input_image_count' => count($images), 'status' => 'pending', 'metadata' => $meta + ['hd' => $hd],
        ]);
        $t0 = microtime(true);
        $ms = fn () => (int) round((microtime(true) - $t0) * 1000);

        try {
            $res = Http::withHeaders(['x-goog-api-key' => $apiKey, 'Content-Type' => 'application/json'])->timeout(180)
                ->post('https://generativelanguage.googleapis.com/v1/models/'.rawurlencode($model).':generateContent', [
                    'contents' => [['parts' => $parts]],
                    'generationConfig' => $gen,
                ]);
        } catch (\Throwable $e) {
            $rec->fail($usage, 'EXCEPTION', $ms(), ['exception_class' => get_class($e)]);
            Log::error('GeminiImage exception', ['tool' => $toolKey, 'error' => $e->getMessage()]);
            throw new \RuntimeException('Görsel servisine bağlanılamadı. Lütfen tekrar deneyin.');
        }

        if (! $res->successful()) {
            $rec->fail($usage, 'HTTP_'.$res->status(), $ms(), ['provider_status' => $res->status()]);
            Log::error('GeminiImage error', ['tool' => $toolKey, 'status' => $res->status(), 'body' => mb_substr($res->body(), 0, 2000)]);
            throw new \RuntimeException($res->status() === 429 || $res->status() === 402 ? 'Görsel servisi şu an yoğun. Biraz sonra tekrar deneyin.' : 'Görsel üretilemedi. Lütfen tekrar deneyin.');
        }

        $json = (array) $res->json();
        $rec->complete($usage, $json, $ms());
        foreach ((array) data_get($json, 'candidates.0.content.parts', []) as $part) {
            $inline = $part['inlineData'] ?? $part['inline_data'] ?? null;
            $mime = (string) ($inline['mimeType'] ?? $inline['mime_type'] ?? '');
            if (is_array($inline) && str_starts_with($mime, 'image/') && ! empty($inline['data'])) {
                $bytes = base64_decode((string) $inline['data'], true);
                if ($bytes) {
                    $usage->update(['output_image_count' => 1]);

                    return ['bytes' => $bytes, 'mime' => $mime, 'model' => $model];
                }
            }
        }
        $rec->fail($usage, 'NO_IMAGE', $ms());

        throw new \RuntimeException('Görsel servisi yanıtında görsel yoktu. Lütfen tekrar deneyin.');
    }
}
