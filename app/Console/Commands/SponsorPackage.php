<?php

namespace App\Console\Commands;

use App\Models\Sponsor;
use App\Services\Sponsors\SponsorPackages;
use Illuminate\Console\Command;

/**
 * Bir markayı tematik pakete tek komutla yerleştirir: paketteki her (araç, kategori) için sponsor kaydı açar.
 * Kayıtlar varsayılan olarak KAPALI açılır; sözleşme imzalanınca --active ile ya da adminden açılır.
 */
class SponsorPackage extends Command
{
    protected $signature = 'sponsors:package
        {package? : Paket anahtarı (boş: listele)}
        {--brand= : Marka adı}
        {--url= : Hedef adres (https://...)}
        {--headline= : Kart başlığı (boş: kategori adına göre)}
        {--logo= : Logo adresi}
        {--starts= : Başlangıç (YYYY-AA-GG)}
        {--ends= : Bitiş (YYYY-AA-GG)}
        {--amount= : Anlaşma bedeli, TL KDV hariç (rapor için)}
        {--exclusive : Münhasır (kategoride tek marka); Tam Kategori paketinde her zaman açık}
        {--active : Hemen yayına al (yalnız imzalı sözleşmeyle)}
        {--dry-run : Kaydetmeden göster}';

    protected $description = 'Markayı sponsor paketine yerleştirir (paketteki tüm araç ve kategoriler)';

    public function handle(): int
    {
        $key = (string) $this->argument('package');
        if ($key === '') {
            foreach (SponsorPackages::all() as $k => $p) {
                $m = SponsorPackages::monthly($k);
                $this->line(sprintf('%-18s %-20s %2d araç · %2d kalem · %s/ay + KDV%s', $k, $p['label'], count($p['tools']), SponsorPackages::slots($p), $m ? SponsorPackages::tl($m) : '—', ! empty($p['addon']) ? ' · ek paket' : (! empty($p['exclusive']) ? ' · tek marka' : '')));
            }
            $f = SponsorPackages::founding();
            $this->line(sprintf('Kurucu sponsor: %s, %d ay, %s/ay + KDV. Pilot süresi: %d ay.', SponsorPackages::get($f['package'])['label'] ?? $f['package'], $f['months'], SponsorPackages::tl($f['monthly']), SponsorPackages::pilotMonths()));

            return self::SUCCESS;
        }
        $pkg = SponsorPackages::get($key);
        if (! $pkg) {
            $this->error("Paket yok: $key");

            return self::FAILURE;
        }
        $brand = trim((string) $this->option('brand'));
        $url = trim((string) $this->option('url'));
        if ($brand === '' || ! preg_match('#^https?://#', $url)) {
            $this->error('--brand ve --url (https://...) gerekli.');

            return self::FAILURE;
        }
        $active = (bool) $this->option('active');
        $exclusive = (bool) $this->option('exclusive') || ! empty($pkg['exclusive']);
        $tools = (array) config('worksitem_sponsors.tools');
        $made = 0;
        foreach ($pkg['tools'] as $tool => $cats) {
            foreach ($cats as $cat) {
                $label = $tools[$tool]['categories'][$cat] ?? $cat;
                $row = [
                    'brand' => $brand, 'target_url' => $url, 'tool_slug' => $tool, 'category' => $cat,
                    'headline' => (string) ($this->option('headline') ?: $brand.' · '.$label),
                    'cta_label' => 'İncele', 'logo_url' => $this->option('logo') ?: null, 'priority' => 10, 'active' => $active,
                    'starts_on' => $this->option('starts') ?: null, 'ends_on' => $this->option('ends') ?: null,
                    'contract_amount' => $this->option('amount') !== null ? (float) $this->option('amount') : null,
                    'notes' => 'Paket: '.$pkg['label'].($exclusive ? ' '.Sponsor::EXCLUSIVE_MARK : ''),
                ];
                $this->line(sprintf('  %-30s %-22s %s', $tool, $cat, $active ? 'YAYINDA' : 'kapalı'));
                if (! $this->option('dry-run')) {
                    Sponsor::query()->updateOrCreate(['brand' => $brand, 'tool_slug' => $tool, 'category' => $cat], $row);
                }
                $made++;
            }
        }
        $this->info("$made kalem ".($this->option('dry-run') ? '(deneme, kaydedilmedi)' : 'kaydedildi').'.');
        if (! $active) {
            $this->warn('Kayıtlar kapalı. Sözleşme imzalanınca adminden ya da --active ile açın.');
        }

        return self::SUCCESS;
    }
}
