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
    protected $signature = 'sponsors:demo {--remove : Demo kayıtlarını sil}';

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
        $n = 0;
        foreach (SponsorPackages::get(SponsorPackages::ALL)['tools'] as $tool => $cats) {
            foreach ($cats as $cat) {
                $label = $tools[$tool]['categories'][$cat] ?? $cat;
                Sponsor::query()->updateOrCreate(['brand' => self::BRAND, 'tool_slug' => $tool, 'category' => $cat], [
                    'headline' => $label.' çözümleri',
                    'body' => 'Demo kartı: sözleşmeli markanın ürünü bu alanda görünür.',
                    'cta_label' => 'Ürünleri gör', 'target_url' => url('/markalar-icin'), 'priority' => 50, 'active' => true,
                    'notes' => Sponsor::DEMO_MARK.' v76 staging demosu',
                ]);
                $n++;
            }
        }
        $this->info("$n demo kaydı hazır. Örnek: ".url('/araclar/seramik-maliyet-hesaplayici?surface=zemin&floor=40&size=60x60&quality=orta&scope=hazir&grout=epoksi&extras[]=seviye&extras[]=temizlik'));
        $this->line('Kaldırmak için: php artisan sponsors:demo --remove');

        return self::SUCCESS;
    }
}
