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
        $body = Cache::remember('llms-txt:'.md5(json_encode(config('worksitem_tools.tool_status'))), 3600, fn () => $this->build());

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
                    $desc = ToolSeo::desc((string) (KitRegistry::get($slug)->page()['seo_desc'] ?? ''), 200);
                } catch (\Throwable) {
                    $desc = '';
                }
            }
            $lines[] = '- '.($meta['title'] ?? $slug).': https://worksitem.com/araclar/'.$slug.($desc !== '' ? "\n  ".$desc : '');
        }
        $tools = "## Aktif araçlar\n\nAşağıdaki araçlar şu an kullanıma açıktır. Güncel sonuç için aracın kendi sayfası esas alınmalıdır.\n\n"
            .($lines ? implode("\n", $lines) : '- Şu an listelenecek aktif araç yok.')."\n\n"
            ."## Markalar için\n\nSponsorluk ve iş birliği: https://worksitem.com/markalar-icin\n\n## Firmalar için\n\nUsta, tadilat firması, müteahhit ve tedarikçiler için ücretsiz firma üyeliği: https://worksitem.com/firma-uyeligi";

        return strtr((string) file_get_contents(resource_path('llms/base.txt')), [
            ':updated' => now('Europe/Istanbul')->toDateString(),
            ':tools' => $tools,
        ]);
    }
}
