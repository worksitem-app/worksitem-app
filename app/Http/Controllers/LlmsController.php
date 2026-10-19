<?php

namespace App\Http\Controllers;

use App\Estimators\KitRegistry;
use App\Support\ToolSeo;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;

/**
 * /llms.txt — yapay zekâ asistanları için site özeti.
 * Sabit metin resources/llms/base.txt; "Aktif araçlar" listesi yalnız yayında olan
 * araçlardan üretilir (kapalı araçlar listelenmez).
 */
class LlmsController extends Controller
{
    public function __invoke(): Response
    {
        $body = Cache::remember('llms-txt:'.md5(json_encode(config('worksitem_tools.tool_status')).@filemtime(resource_path('llms/base.txt')).config('worksitem_market.enabled')), 3600, fn () => $this->build());

        return response($body, 200, ['Content-Type' => 'text/plain; charset=UTF-8']);
    }

    private function build(): string
    {
        $cfg = (array) config('worksitem_tools', []);
        $default = $cfg['tool_status']['default'] ?? 'review';
        $over = (array) ($cfg['tool_status']['overrides'] ?? []);
        $kits = KitRegistry::slugs();
        $lines = [];
        foreach ((array) ($cfg['tools'] ?? []) as $slug => $meta) {
            if (($over[$slug] ?? $default) !== 'active' || $slug === 'havalandirma-kanali') {
                continue;
            }
            $desc = '';
            if (in_array($slug, $kits, true)) {
                try {
                    $page = KitRegistry::get($slug)->page();
                    $desc = ToolSeo::desc((string) ($page['seo_desc'] ?? ''), 200);
                    // Soru-cevap biçiminde tarihli örnek: yapay zekâ asistanları doğrudan alıntılayabilsin
                    if ($qa = ToolSeo::quickAnswer(ToolSeo::faq((array) ($page['faq'] ?? [])))) {
                        $date = \App\Support\PriceDate::for($slug)->locale('tr')->translatedFormat('j F Y');
                        $desc .= " Örnek ({$date}): ".trim($qa[0]).' → '.ToolSeo::desc(trim($qa[1]), 220);
                    }
                } catch (\Throwable) {
                    $desc = $desc ?: '';
                }
            }
            // llmstxt.org biçimi: - [Ad](adres): açıklama (tek satır)
            $title = str_replace(['[', ']'], ['(', ')'], (string) ($meta['title'] ?? $slug));
            $lines[] = '- ['.$title.'](https://worksitem.com/araclar/'.$slug.')'.($desc !== '' ? ': '.preg_replace('/\s+/u', ' ', $desc) : '');
        }
        $tools = "## Aktif araçlar\n\n"
            .($lines ? implode("\n", $lines) : '- Şu an listelenecek aktif araç yok.');

        return strtr((string) file_get_contents(resource_path('llms/base.txt')), [
            ':updated' => now('Europe/Istanbul')->toDateString(),
            ':tools' => $tools,
        ]);
    }
}
