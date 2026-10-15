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
                        $desc .= "\n  Örnek ({$date}): ".trim($qa[0]).' → '.ToolSeo::desc(trim($qa[1]), 220);
                    }
                } catch (\Throwable) {
                    $desc = $desc ?: '';
                }
            }
            $lines[] = '- '.($meta['title'] ?? $slug).': https://worksitem.com/araclar/'.$slug.($desc !== '' ? "\n  ".$desc : '');
        }
        $tools = "## Aktif araçlar\n\nAşağıdaki araçlar şu an kullanıma açıktır. Güncel sonuç için aracın kendi sayfası esas alınmalıdır.\n\n"
            .($lines ? implode("\n", $lines) : '- Şu an listelenecek aktif araç yok.')."\n\n"
            ."## Markalar için\n\nSponsor firma kaydı ve iş birliği: https://worksitem.com/markalar-icin\n\n## Firmalar için\n\nUsta, tadilat firması, müteahhit ve tedarikçiler için ücretsiz firma üyeliği (kurucu firma programı: 31.03.2027'ye kadar komisyon yok): https://worksitem.com/firma-uyeligi\n\n## Uygulama\n\nWorksitem telefona ücretsiz uygulama olarak eklenebilir (Android ve iPhone): https://worksitem.com/uygulama";

        return strtr((string) file_get_contents(resource_path('llms/base.txt')), [
            ':updated' => now('Europe/Istanbul')->toDateString(),
            ':tools' => $tools,
        ]);
    }
}
