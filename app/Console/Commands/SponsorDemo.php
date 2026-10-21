<?php

namespace App\Console\Commands;

use App\Models\Sponsor;
use App\Services\Sponsors\SponsorPackages;
use Illuminate\Console\Command;

/**
 * Staging demosu: kurgusal "Örnek Yapı Kimyasalları" markasını tam kategori paketine yerleştirir.
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

    /** Seramik / vitrifiye markasına giden kategoriler (diğerleri yapı kimyasalı markasına) */
    public const TILE_CATS = ['seramik', 'vitrifiye', 'dus', 'armatur'];

    /** Seramik markasının da görüneceği araçlar */
    public const TILE_TOOLS = ['seramik-maliyet-hesaplayici', 'banyo-maliyet-hesaplayici', 'wc-maliyet-hesabi', 'zemin-kaplama', 'banyo-planlayici', 'wc-planlayici'];

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
                $slots[] = [$tool, $cat, $chem];
            }
        }
        if ($tile) {
            foreach (self::TILE_TOOLS as $tool) {
                foreach (array_intersect(self::TILE_CATS, array_keys((array) ($tools[$tool]['categories'] ?? []))) as $cat) {
                    $slots[] = [$tool, $cat, $tile];
                }
            }
        }
        $n = 0;
        foreach ($slots as [$tool, $cat, $b]) {
            $label = $tools[$tool]['categories'][$cat] ?? $cat;
            Sponsor::query()->create([
                'brand' => $b['brand'], 'tool_slug' => $tool, 'category' => $cat,
                'headline' => mb_substr($b['brand'].' · '.$label, 0, 120),
                'body' => 'Demo görünüm: sözleşme imzalanınca kart metnini ve hedef sayfayı siz belirlersiniz.',
                'cta_label' => 'Ürünleri gör', 'target_url' => $b['url'], 'logo_url' => $b['logo'], 'priority' => 50, 'active' => true,
                'notes' => Sponsor::DEMO_MARK.' staging demosu',
            ]);
            $n++;
        }
        $this->info("$n demo kaydı hazır. Örnek: ".url('/araclar/seramik-maliyet-hesaplayici?surface=zemin&floor=40&size=60x60&quality=orta&scope=hazir&grout=epoksi&extras[]=seviye&extras[]=temizlik'));
        $this->line('Kaldırmak için: php artisan sponsors:demo --remove');

        return self::SUCCESS;
    }
}
