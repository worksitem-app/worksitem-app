<?php

namespace App\Console\Commands;

use App\Models\Sponsor;
use App\Services\Sponsors\SponsorPackages;
use Illuminate\Console\Command;

/**
 * Staging demosu: yapı kimyasalı markasını Tam Kategori paketine (münhasır), seramik markasını Seramik ve Banyo ek paketine yerleştirir.
 * Canlı ortamda çalışmaz; canlıda bu kayıtlar Sponsor::live() tarafından da hiçbir zaman gösterilmez.
 */
class SponsorDemo extends Command
{
    protected $signature = 'sponsors:demo
        {--remove : Demo kayıtlarını sil}
        {--brand= : Yapı kimyasalı kalemlerinde görünecek marka (boşsa kurgusal örnek marka)}
        {--url= : Bu markanın hedef adresi}
        {--logo= : Bu markanın logo adresi (ör. /storage/sponsor-demo/kalekim.png)}
        {--tile-brand= : Seramik, vitrifiye ve duş kalemlerinde görünecek marka}
        {--tile-url= : Seramik markasının hedef adresi}
        {--tile-logo= : Seramik markasının logo adresi}';

    /** Seramik markasının paketi (Tam Kategori'ye dahil olmayan ek paket) */
    public const TILE_PACKAGE = 'seramik-banyo';

    protected $description = 'Staging: örnek marka ile sponsor demo kayıtları (canlıda çalışmaz)';

    public const BRAND = 'Örnek Yapı Kimyasalları';

    public function handle(): int
    {
        if (app()->environment('production')) {
            $this->error('Canlı ortamda demo sponsor açılmaz (APP_ENV=production).');

            return self::FAILURE;
        }
        $q = Sponsor::query()->where('notes', 'like', '%'.Sponsor::DEMO_MARK.'%');
        if ($this->option('remove')) {
            $n = $q->delete();
            $this->info("$n demo kaydı silindi.");

            return self::SUCCESS;
        }
        $tools = (array) config('worksitem_sponsors.tools');
        $chem = ['brand' => (string) ($this->option('brand') ?: self::BRAND), 'url' => (string) ($this->option('url') ?: url('/markalar-icin')), 'logo' => $this->option('logo') ?: null];
        $tile = $this->option('tile-brand')
            ? ['brand' => (string) $this->option('tile-brand'), 'url' => (string) ($this->option('tile-url') ?: $chem['url']), 'logo' => $this->option('tile-logo') ?: null]
            : null;

        // Önceki demo kayıtları temizlenir (marka değişmiş olabilir)
        (clone $q)->delete();

        $slots = [];
        foreach (SponsorPackages::get(SponsorPackages::ALL)['tools'] as $tool => $cats) {
            foreach ($cats as $cat) {
                $slots[] = [$tool, $cat, $chem, true];
            }
        }
        if ($tile) {
            foreach (SponsorPackages::get(self::TILE_PACKAGE)['tools'] as $tool => $cats) {
                foreach ($cats as $cat) {
                    $slots[] = [$tool, $cat, $tile, false];
                }
            }
        }
        $n = 0;
        foreach ($slots as [$tool, $cat, $b, $excl]) {
            $label = $tools[$tool]['categories'][$cat] ?? $cat;
            Sponsor::query()->create([
                'brand' => $b['brand'], 'tool_slug' => $tool, 'category' => $cat,
                'headline' => mb_substr($b['brand'].' · '.$label, 0, 120),
                'body' => 'Demo görünüm: sözleşme imzalanınca kart metnini ve hedef sayfayı siz belirlersiniz.',
                'cta_label' => 'Ürünleri gör', 'target_url' => $b['url'], 'logo_url' => $b['logo'], 'priority' => 50, 'active' => true,
                'notes' => Sponsor::DEMO_MARK.' staging demosu'.($excl ? ' '.Sponsor::EXCLUSIVE_MARK : ''),
            ]);
            $n++;
        }
        $this->info("$n demo kaydı hazır. Örnek: ".url('/araclar/seramik-maliyet-hesaplayici?surface=zemin&en=5&boy=8&size=60x60&quality=orta&scope=hazir&grout=epoksi&extras[]=seviye&extras[]=temizlik'));
        $this->info('Poz sayfası örnekleri (v113): '.url('/pozlar?q=yap%C4%B1%C5%9Ft%C4%B1r%C4%B1c%C4%B1').' ve '.url('/pozlar?q=su+yal%C4%B1t%C4%B1m'));
        $this->line('Kaldırmak için: php artisan sponsors:demo --remove');

        return self::SUCCESS;
    }
}
