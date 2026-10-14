<?php

namespace App\Http\Controllers\Tools;

use App\Http\Controllers\Controller;
use App\Http\Controllers\TileVisualizerController;
use App\Models\PlannerProduct;
use App\Models\PlannerTile;
use App\Services\AI\GeminiImage;
use Illuminate\Http\Request;

/**
 * 2D / 3D banyo planlayıcı. Banyo maliyet hesaplayıcısından gelen ölçüler
 * (width, length, height metre; shower; ex_dolap, ex_havlupan, ex_rezervuar)
 * planı otomatik doldurur. Plan tarayıcıda çizilir; sunucu yalnızca sayfayı verir.
 */
class BathPlannerController extends Controller
{
    /** WC / misafir tuvaleti: aynı planlayıcı, küçük oda ve duşsuz başlangıç */
    public function wc(Request $request)
    {
        $request->query->set('bathType', 'wc');

        return $this->show($request, 'wc');
    }

    /** Mutfak: aynı planlayıcı; dolap dizisi, tezgah, ankastre ve buzdolabı parçalarıyla */
    public function kitchen(Request $request)
    {
        return $this->show($request, 'mutfak');
    }

    public function show(Request $request, string $variant = 'banyo')
    {
        $cfg = (array) config('worksitem_planner');
        $lim = $cfg['limits'];
        $def = match ($variant) {
            'wc' => array_merge($cfg['defaults'], (array) ($cfg['wc_defaults'] ?? [])),
            'mutfak' => array_merge($cfg['defaults'], (array) ($cfg['kitchen_defaults'] ?? [])),
            default => $cfg['defaults'],
        };

        // metre → cm, sınırlar içinde
        $cm = function (string $key, int $fallback, int $min, int $max) use ($request): int {
            $v = (float) str_replace(',', '.', (string) $request->query($key, ''));
            if ($v <= 0) {
                return $fallback;
            }
            $v = $v <= 20 ? $v * 100 : $v;   // 1.8 → 180 cm; 180 olarak gelirse aynen

            return (int) max($min, min($max, round($v)));
        };

        $type = (string) $request->query('bathType', 'standart');
        $shower = (string) $request->query('shower', '');
        $showerType = $type === 'wc' ? null : ($cfg['shower_map'][$shower] ?? ($request->hasAny(['width', 'length']) ? null : 'kabin'));

        $initial = [
            'w' => $cm('width', $def['w'], $lim['min'], $lim['max']),
            'l' => $cm('length', $def['l'], $lim['min'], $lim['max']),
            'h' => $cm('height', $def['h'], $lim['h_min'], $lim['h_max']),
            'shower' => $showerType,
            'dolap' => $request->boolean('ex_dolap'),
            'havlupan' => $request->boolean('ex_havlupan'),
            'rezervuar' => $request->boolean('ex_rezervuar'),
            'fromCalc' => $request->hasAny(['width', 'length']),
        ];
        if ($variant === 'mutfak') {
            // mutfak hesabından: alt dolap uzunluğu (m) ve ekstralar
            $initial['shower'] = null;
            $initial['run'] = (int) min(1500, max(0, round((float) str_replace(',', '.', (string) $request->query('run', '0')) * 100)));
            $initial['boy'] = $request->boolean('boy_dolabi');
            $initial['ada'] = $request->boolean('ada');
        }

        $tiles = self::tiles();   // sponsor seramikleri önce

        // Banyo hesabına dönüş bağlantısı: aynı girdiler
        $back = $variant === 'mutfak'
            ? route('tools.mutfak_calculator', array_filter($request->only(['scope', 'quality', 'counter']), fn ($v) => is_string($v) && $v !== ''))
            : ($variant === 'wc'
            ? route('tools.wc_calculator', array_filter($request->only(['scope', 'quality']), fn ($v) => is_string($v) && $v !== ''))
            : route('tools.banyo_calculator', array_filter($request->only([
                'bathType', 'width', 'length', 'height', 'scope', 'quality', 'shower', 'ex_dolap', 'ex_rezervuar', 'ex_havlupan', 'ex_tavan', 'ex_elektrik',
            ]), fn ($v) => $v !== null && $v !== '')));

        // Varyanta göre parça türleri: mutfakta mutfak parçaları, banyo/WC'de banyo parçaları (yapı elemanları her ikisinde)
        $types = array_filter((array) $cfg['types'], fn ($t) => ! empty($t['element']) || (($variant === 'mutfak') === ! empty($t['kitchen'])));

        return view('tools.banyo-planlayici', [
            'plannerCfg' => [
                'limits' => $lim,
                'shapes' => $cfg['shapes'],
                'doorW' => (int) $def['door_w'],
                'types' => $types,
                'products' => array_intersect_key(self::mergeProducts((array) $cfg['products'], PlannerProduct::forPlanner()) + (array) $cfg['elements'], $types),
                'window' => $cfg['window'],
                'tiles' => $tiles,
                'tileDefaults' => $cfg['tiles'],
                'initial' => $initial,
                'back' => $back,
                'renderUrl' => route('tools.banyo_planner_render'),
                'variant' => $variant,
                'bathCalc' => route('tools.banyo_calculator'),
                'query' => array_filter($request->only(['bathType', 'scope', 'quality', 'ex_tavan', 'ex_elektrik']), fn ($v) => is_string($v) && $v !== '' && strlen($v) < 20),
            ],
            'three' => $cfg['three'],
            'back' => $back,
            'variant' => $variant,
        ]);
    }

    /** Sponsor ürünleri önce, örnek ürünler sonra */
    public static function mergeProducts(array $examples, array $sponsored): array
    {
        foreach ($sponsored as $type => $list) {
            if (isset($examples[$type])) {
                $examples[$type] = array_merge($list, $examples[$type]);
            }
        }

        return $examples;
    }

    /** Ürün tıklaması: sayılır, markanın ürün sayfasına gider */
    public function product(PlannerProduct $product)
    {
        if (! $product->active || ! $product->product_url) {
            return redirect()->route('tools.banyo_planner');
        }
        $product->increment('clicks');

        return redirect()->away($product->trackedUrl());
    }

    /** Seramik tıklaması: sayılır, markanın ürün sayfasına gider */
    public function tile(PlannerTile $tile)
    {
        if (! $tile->active || ! $tile->product_url) {
            return redirect()->route('tools.banyo_planner');
        }
        $tile->increment('clicks');

        return redirect()->away($tile->trackedUrl());
    }

    /**
     * Gerçekçi görsel (yapay zekâ). Kota: ai.quota (günde 1 ücretsiz, sonrası kredi).
     * Ücretsiz hakla filigranlı önizleme; kredi ile filigransız tam çözünürlük.
     * mode=plan: 3D görünümün ekran görüntüsü · mode=photo: müşterinin kendi banyo fotoğrafı.
     */
    public function render(Request $request, GeminiImage $gemini)
    {
        $tileIds = array_keys(self::tiles());
        $v = $request->validate([
            'mode' => ['required', 'in:plan,photo'],
            'scene' => ['required_if:mode,plan', 'nullable', 'string', 'max:9000000'],
            'photo' => ['required_if:mode,photo', 'nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
            'floor' => ['required', 'string', 'in:'.implode(',', $tileIds)],
            'wall' => ['required', 'string', 'in:'.implode(',', $tileIds)],
            'paint' => ['nullable', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'tile_height' => ['nullable', 'integer', 'min:-1', 'max:400'],
            'items' => ['nullable', 'string', 'max:800'],
            'note' => ['nullable', 'string', 'max:300'],
            'quality' => ['nullable', 'in:std,hd'],
        ]);
        $paid = $request->user()?->is_admin || data_get($request->attributes->get('ai_charge'), 'reservation.kind') === 'use';
        $hd = ($v['quality'] ?? 'std') === 'hd' && $paid;   // HD yalnızca kredi (2) ile; ara katman ücretsiz hakkı HD'ye kullandırmaz

        if ($v['mode'] === 'plan') {
            if (! preg_match('#^data:image/(png|jpeg);base64,([A-Za-z0-9+/=]+)$#', (string) $v['scene'], $m) || ! ($scene = base64_decode($m[2], true))) {
                return response()->json(['message' => '3D görünüm alınamadı. Lütfen tekrar deneyin.'], 422);
            }
            $base = ['mime' => 'image/'.$m[1], 'bytes' => $scene];
        } else {
            $f = $request->file('photo');
            $base = ['mime' => $f->getMimeType() ?: 'image/jpeg', 'bytes' => (string) file_get_contents($f->getRealPath())];
        }

        $images = [$base];
        foreach (['floor', 'wall'] as $k) {
            if ($img = self::tileBytes($v[$k])) {
                $images[] = $img;
            }
        }
        $tiles = self::tiles();
        $fl = $tiles[$v['floor']];
        $wl = $tiles[$v['wall']];
        $th = (int) ($v['tile_height'] ?? 0);
        $wallText = $th < 0 ? 'Walls are painted (no wall tiles).'
            : 'Wall tiles ('.$wl['size'].' cm, reference image 3) cover the walls '.($th > 0 ? "up to {$th} cm from the floor; above that the wall is painted." : 'up to the ceiling.');
        $items = strip_tags((string) ($v['items'] ?? ''));

        $prompt = implode(' ', array_filter([
            $v['mode'] === 'plan'
                ? 'The FIRST image is a simple 3D layout render of a bathroom. Turn it into ONE photorealistic interior photograph of the same bathroom from the same camera angle.'
                : 'The FIRST image is a real photo of the customer bathroom. Renovate it into ONE photorealistic photograph from exactly the same camera angle.',
            'Keep the room shape, wall positions, door, windows and the position and size of every fixture exactly as in the first image. Do not add or remove fixtures.',
            'Floor tiles: use the SECOND image as the tile texture, real tile size '.$fl['size'].' cm with realistic grout lines.',
            $wallText,
            ! empty($v['paint']) ? 'Paint colour for painted areas and ceiling: '.$v['paint'].'.' : null,
            $items ? 'Fixtures in the room: '.$items.'.' : null,
            ! empty($v['note']) ? 'Customer note: '.strip_tags((string) $v['note']).'.' : null,
            'Natural soft daylight, realistic materials and reflections, modern Turkish apartment bathroom. No text, labels, watermark, collage or people.',
        ]));

        try {
            $out = $gemini->generate($prompt, $images, 'bath_planner', ['mode' => $v['mode'], 'floor' => $v['floor'], 'wall' => $v['wall']], ['hd' => $hd]);
        } catch (\RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 502);
        }

        $bytes = $paid ? $out['bytes'] : \App\Http\Controllers\TileVisualizerController::watermark($out['bytes']);
        $mime = $paid ? $out['mime'] : 'image/jpeg';

        return response()->json([
            'success' => true,
            'image' => 'data:'.$mime.';base64,'.base64_encode($bytes),
            'preview_only' => ! $paid,
            'hd' => $hd,
            'message' => $paid ? ($hd ? 'HD görseliniz hazır (filigransız, yüksek çözünürlük).' : 'Görseliniz hazır (filigransız, tam çözünürlük).') : 'Ücretsiz önizleme (filigranlı). Filigransız tam çözünürlük için kredi ile oluşturun.',
        ]);
    }

    /** Planlayıcı seramikleri: sponsor + görselleştirici kataloğu */
    public static function tiles(): array
    {
        $tiles = PlannerTile::forPlanner();
        foreach (TileVisualizerController::catalog() as $id => $t) {
            [$tw, $th] = array_map('intval', explode('x', (string) $t['size']) + [1 => 0]);
            $tiles[$id] = ['name' => $t['name'], 'style' => $t['style'], 'size' => $t['size'], 'tw' => $tw ?: 60, 'th' => $th ?: 60, 'url' => '/images/tile-visualizer/'.$t['file'], 'file' => $t['file']];
        }

        return $tiles;
    }

    /** @return array{mime:string, bytes:string}|null */
    private static function tileBytes(string $id): ?array
    {
        if (str_starts_with($id, 'db-')) {
            $path = PlannerTile::query()->find((int) substr($id, 3))?->localPath();
        } else {
            $file = TileVisualizerController::catalog()[$id]['file'] ?? null;
            $path = $file ? public_path('images/tile-visualizer/'.$file) : null;
        }
        if (! $path || ! is_file($path)) {
            return null;
        }

        return ['mime' => mime_content_type($path) ?: 'image/png', 'bytes' => (string) file_get_contents($path)];
    }
}
