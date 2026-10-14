<?php

namespace App\Console\Commands;

use App\Estimators\KitRegistry;
use App\Services\Pricing\PriceRobot;
use App\Support\ToolSeo;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

/**
 * Araç sonuçlarını piyasayla karşılaştırır (boyada yaptığımız kontrolün otomatiği):
 * her aracın "Kısa cevap" örneği (ör. "100 m² ev boyası kaç TL?") Google'da aranır,
 * piyasa ortancası ile aracın orta değeri karşılaştırılır; ±%35'ten fazla sapan araç işaretlenir.
 *   php artisan prices:compare                 # tüm hesap motoru araçları
 *   php artisan prices:compare --tool=boya-hesaplayici
 *   php artisan prices:compare --mail          # sonucu fiyat raporu adresine gönder
 */
class ComparePrices extends Command
{
    protected $signature = 'prices:compare {--tool= : Yalnız bu araç} {--mail : Sonucu e-postayla gönder} {--limit=40}';

    protected $description = 'Araç örnek sonuçlarını Google piyasa fiyatlarıyla karşılaştırır';

    public function handle(PriceRobot $robot): int
    {
        $slugs = $this->option('tool') ? [(string) $this->option('tool')] : KitRegistry::slugs();
        $rows = [];
        foreach (array_slice($slugs, 0, (int) $this->option('limit')) as $slug) {
            try {
                $page = KitRegistry::get($slug)->page();
            } catch (\Throwable) {
                continue;
            }
            $qa = ToolSeo::quickAnswer(ToolSeo::faq($page['faq'] ?? []));
            $ours = $qa ? self::range($qa[1]) : null;
            if (! $qa || ! $ours) {
                $rows[] = [$slug, '—', '—', '—', 'örnek fiyat yok'];

                continue;
            }
            try {
                $found = $robot->search(['q' => $qa[0].' ('.mb_substr($qa[1], 0, 140).')', 'unit' => 'aynı kapsam, toplam TL']);
            } catch (\Throwable $e) {
                $rows[] = [$slug, self::fmt($ours), '—', '—', 'arama hatası'];

                continue;
            }
            $prices = array_values(array_filter(array_map(fn ($s) => (float) $s['price_tl'], $found['sources'])));
            if (count($prices) < 2) {
                $rows[] = [$slug, self::fmt($ours), '—', '—', 'piyasa verisi yetersiz'];

                continue;
            }
            sort($prices);
            $mid = $prices[intdiv(count($prices), 2)];
            $ourMid = ($ours[0] + $ours[1]) / 2;
            $ratio = $ourMid / $mid;
            $flag = $ratio > 1.35 ? 'YÜKSEK' : ($ratio < 0.65 ? 'DÜŞÜK' : 'uygun');
            $rows[] = [$slug, self::fmt($ours), number_format($mid, 0, ',', '.').' ('.count($prices).' kaynak)', number_format($ratio, 2, ',', ''), $flag];
        }

        $this->table(['Araç', 'Bizim örnek', 'Piyasa ortanca', 'Oran', 'Sonuç'], $rows);
        $bad = collect($rows)->filter(fn ($r) => in_array($r[4], ['YÜKSEK', 'DÜŞÜK'], true))->count();
        $this->info("Bitti: {$bad} araç piyasadan ±%35'ten fazla sapıyor.");

        if ($this->option('mail') && ($to = (string) config('worksitem_prices.report_email', config('worksitem_leads.notification_email')))) {
            $body = "Araç fiyat karşılaştırması (".now('Europe/Istanbul')->format('d.m.Y H:i').")\n\n"
                .collect($rows)->map(fn ($r) => implode(' | ', $r))->implode("\n");
            Mail::raw($body, fn ($m) => $m->to($to)->subject('WORKSITEM | Araç fiyatları piyasa karşılaştırması | '.$bad.' sapma'));
            $this->line('E-posta: '.$to);
        }

        return self::SUCCESS;
    }

    /** "yaklaşık 49–69 bin TL", "1.000–1.400 TL / m²", "4.320 TL" → [alt, üst] TL */
    public static function range(string $text): ?array
    {
        if (preg_match('/([\d.]+(?:,\d+)?)\s*[–-]\s*([\d.]+(?:,\d+)?)\s*(bin\s*)?TL/u', $text, $m)) {
            $k = ! empty($m[3]) ? 1000 : 1;

            return [PriceRobot::parsePrice($m[1]) * $k, PriceRobot::parsePrice($m[2]) * $k];
        }
        if (preg_match('/([\d.]+(?:,\d+)?)\s*(bin\s*)?TL/u', $text, $m)) {
            $v = PriceRobot::parsePrice($m[1]) * (! empty($m[2]) ? 1000 : 1);

            return $v > 0 ? [$v, $v] : null;
        }

        return null;
    }

    private static function fmt(array $r): string
    {
        $f = fn ($v) => number_format($v, 0, ',', '.');

        return $r[0] === $r[1] ? $f($r[0]) : $f($r[0]).'–'.$f($r[1]);
    }
}
