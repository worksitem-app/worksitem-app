<?php

namespace App\Console\Commands;

use App\Models\TenderPoz;
use App\Support\PozCatalog;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;

/**
 * v114: 43 bin pozu sponsor ürün gruplarıyla eşleştirir ve tender_pozs.sponsor_categories alanına yazar.
 * Kurallar config/worksitem_sponsors.php › $pozRules. Yeni ürün grubu ya da sponsor gelince:
 *   1) kurala bir satır ekleyin  2) php artisan sponsors:poz-esle --rapor  3) sonuç doğruysa kaydedilmiştir.
 * Parça tanımlar ("(40 x 40 cm) …") grubun son başlığıyla birlikte değerlendirilir.
 */
class SponsorPozMatch extends Command
{
    protected $signature = 'sponsors:poz-esle
        {--rapor : Ürün grubu başına poz sayısı ve örnekleri yaz}
        {--ornek=3 : Grup başına örnek sayısı (--rapor ile)}
        {--csv= : Eşleşen pozları CSV dosyasına yaz (pazarlama için)}
        {--kuru : Kaydetmeden yalnız hesapla}';

    protected $description = 'Pozları sponsor ürün gruplarıyla eşleştirir (poz sayfası marka kartları)';

    public function handle(): int
    {
        $hasColumn = Schema::hasColumn('tender_pozs', 'sponsor_categories');
        $save = ! $this->option('kuru') && $hasColumn;
        if (! $hasColumn && ! $this->option('kuru')) {
            $this->warn('sponsor_categories alanı yok (migration çalışmadı); yalnız hesaplanıyor.');
        }
        $labels = (array) config('worksitem_sponsors.tools.poz-sayfalari.categories', []);
        $sample = max(0, (int) $this->option('ornek'));

        $heading = [];            // (kurum|grup) → son tam başlık
        $byValue = [];            // yeni değer → [id, ...]
        $count = array_fill_keys(array_keys($labels), 0);
        $examples = [];
        $total = 0;
        $matched = 0;
        $changed = 0;
        $csv = null;
        if ($path = $this->option('csv')) {
            $csv = fopen($path, 'w');
            fwrite($csv, "\xEF\xBB\xBF");   // Excel Türkçe karakterler için
            fputcsv($csv, ['poz_no', 'kurum', 'birim', 'birim_fiyat', 'urun_gruplari', 'tanim', 'adres']);
        }

        $cols = ['id', 'poz_no', 'institution', 'unit', 'unit_price', 'description'];
        if ($hasColumn) {
            $cols[] = 'sponsor_categories';
        }
        TenderPoz::query()->where('is_active', true)->select($cols)
            ->orderBy('institution')->orderBy('poz_no')->orderBy('id')
            ->chunk(2000, function ($rows) use (&$heading, &$byValue, &$count, &$examples, &$total, &$matched, &$changed, $sample, $csv, $labels, $hasColumn) {
                foreach ($rows as $p) {
                    $total++;
                    $key = $p->institution.'|'.PozCatalog::groupOf((string) $p->poz_no);
                    $desc = (string) $p->description;
                    $frag = PozCatalog::isFragment($desc);
                    $cats = PozCatalog::sponsorCategories($desc, $frag ? ($heading[$key] ?? null) : null);
                    if (! $frag) {
                        $heading[$key] = $desc;
                    }
                    $value = implode(',', $cats);
                    if (! $hasColumn || (string) $p->getAttribute('sponsor_categories') !== $value || $p->getAttribute('sponsor_categories') === null) {
                        $byValue[$value][] = $p->id;
                        $changed++;
                    }
                    if (! $cats) {
                        continue;
                    }
                    $matched++;
                    foreach ($cats as $c) {
                        $count[$c] = ($count[$c] ?? 0) + 1;
                        if (count($examples[$c] ?? []) < $sample) {
                            $examples[$c][] = $p->poz_no.' ('.PozCatalog::shortInstitution($p->institution).') '.mb_substr($desc, 0, 70);
                        }
                    }
                    if ($csv) {
                        fputcsv($csv, [$p->poz_no, $p->institution, $p->unit, number_format((float) $p->unit_price, 2, ',', ''),
                            implode('; ', array_map(fn ($c) => $labels[$c] ?? $c, $cats)), $desc, \App\Support\PozUrl::absolute((string) $p->poz_no)]);
                    }
                }
            });
        if ($csv) {
            fclose($csv);
        }

        if ($save) {
            foreach ($byValue as $value => $ids) {
                foreach (array_chunk($ids, 1000) as $part) {
                    TenderPoz::query()->whereIn('id', $part)->update(['sponsor_categories' => $value]);
                }
            }
        }

        $this->info(sprintf('%s aktif poz · %s pozda en az bir ürün grubu · %s kayıt %s',
            number_format($total, 0, ',', '.'), number_format($matched, 0, ',', '.'), number_format($changed, 0, ',', '.'),
            $save ? 'güncellendi' : 'değişecekti (kaydedilmedi)'));

        if ($this->option('rapor')) {
            arsort($count);
            $this->table(['Ürün grubu', 'Anahtar', 'Poz'], collect($count)->map(fn ($n, $k) => [$labels[$k] ?? $k, $k, number_format($n, 0, ',', '.')])->values()->all());
            foreach ($count as $k => $n) {
                if ($n > 0 && ! empty($examples[$k])) {
                    $this->line('<comment>'.($labels[$k] ?? $k).'</comment>');
                    foreach ($examples[$k] as $e) {
                        $this->line('   '.$e);
                    }
                }
            }
        }
        if ($csv) {
            $this->info('CSV: '.$this->option('csv'));
        }

        return self::SUCCESS;
    }
}
