<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Services\AI\AiUsageRecorder;
use Illuminate\Validation\Rule;

class TileVisualizerController extends Controller
{
    private const SESSION_KEY = 'tile_visualizer_credits';

    private const CATALOG = [
        'carrara-beyaz' => ['name'=>'Carrara Beyaz','size'=>'60x120','style'=>'Klasik','file'=>'carrara-beyaz.png'],
        'calacatta-altin' => ['name'=>'Calacatta Altın','size'=>'60x120','style'=>'Klasik','file'=>'calacatta-altin.png'],
        'statuario-gri' => ['name'=>'Statuario Gri','size'=>'60x120','style'=>'Klasik','file'=>'statuario-gri.png'],
        'bianco-krem' => ['name'=>'Bianco Krem','size'=>'60x120','style'=>'Klasik','file'=>'bianco-krem.png'],
        'pietra-gri' => ['name'=>'Pietra Gri','size'=>'60x120','style'=>'Klasik','file'=>'pietra-gri.png'],

        'traverten-bej' => ['name'=>'Traverten Bej','size'=>'60x60','style'=>'Taş','file'=>'traverten-bej.png'],
        'traverten-krem' => ['name'=>'Traverten Krem','size'=>'60x60','style'=>'Taş','file'=>'traverten-krem.png'],
        'traverten-gri' => ['name'=>'Traverten Gri','size'=>'60x60','style'=>'Taş','file'=>'traverten-gri.png'],
        'bazalt-antrasit' => ['name'=>'Bazalt Antrasit','size'=>'60x60','style'=>'Taş','file'=>'bazalt-antrasit.png'],
        'kum-tasi' => ['name'=>'Kum Taşı','size'=>'60x60','style'=>'Taş','file'=>'kum-tasi.png'],

        'antrasit-beton' => ['name'=>'Antrasit Beton','size'=>'60x120','style'=>'Modern','file'=>'antrasit-beton.png'],
        'acik-gri-beton' => ['name'=>'Açık Gri Beton','size'=>'60x120','style'=>'Modern','file'=>'acik-gri-beton.png'],
        'vizon-cement' => ['name'=>'Vizon Cement','size'=>'60x120','style'=>'Modern','file'=>'vizon-cement.png'],
        'metalik-gri' => ['name'=>'Metalik Gri','size'=>'60x120','style'=>'Modern','file'=>'metalik-gri.png'],
        'grafit-mat' => ['name'=>'Grafit Mat','size'=>'60x120','style'=>'Modern','file'=>'grafit-mat.png'],

        'dekor-mavi' => ['name'=>'Dekor Mavi','size'=>'30x60','style'=>'Dekoratif','file'=>'dekor-mavi.png'],
        'dekor-karo' => ['name'=>'Dekor Karo','size'=>'20x20','style'=>'Dekoratif','file'=>'dekor-karo.png'],
        'dekor-vintage' => ['name'=>'Dekor Vintage','size'=>'20x20','style'=>'Dekoratif','file'=>'dekor-vintage.png'],
        'mozaik-mix' => ['name'=>'Mozaik Mix','size'=>'30x30','style'=>'Dekoratif','file'=>'mozaik-mix.png'],
        'geometrik' => ['name'=>'Geometrik','size'=>'20x20','style'=>'Dekoratif','file'=>'geometrik.png'],
    ];

    /** Seramik kataloğu (banyo planlayıcı da kullanır) */
    public static function catalog(): array
    {
        return self::CATALOG;
    }

    public function index(Request $request)
    {
        if ($this->testMode()) {
            if ($request->boolean("test_topup_20") && app()->environment() !== "production" && ! $request->session()->get("tile_visualizer_topup_20_done")) {
                $request->session()->increment(self::SESSION_KEY, 20);
                $request->session()->put("tile_visualizer_topup_20_done", true);
            }
            // CREDIT_PERSIST_V1
            if (!$request->session()->has(self::SESSION_KEY)) {
                $request->session()->put(self::SESSION_KEY, 20);
            }
        }

        $catalog = self::CATALOG;
        foreach ($catalog as &$tile) {
            $path = public_path('images/tile-visualizer/'.$tile['file']);
            $tile['preview_data'] = is_file($path)
                ? 'data:image/png;base64,'.base64_encode((string) file_get_contents($path))
                : '';
        }
        unset($tile);

        $walletMode = (bool) config('worksitem_ai_credits.enabled');
        $credits = app(\App\Services\AI\AiCredits::class);

        return view('tools.tile-visualizer', [
            'catalog' => $catalog,
            'credits' => (int) $request->session()->get(self::SESSION_KEY, 0),
            'walletMode' => $walletMode,
            'wallet' => $walletMode ? $credits->status($credits->current($request)) : null,
            'testMode' => $this->testMode(),
        ]);
    }
    public function render(Request $request)
    {
        $catalogKeys = array_keys(self::CATALOG);

        $validated = $request->validate([
            'room_image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
            'tile_source' => ['required', Rule::in(['catalog', 'custom'])],
            'catalog_tile' => ['nullable', Rule::in($catalogKeys)],
            'tile_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'surface' => ['required', Rule::in(['floor', 'wall', 'shower_wall', 'backsplash'])],
            'tile_size' => ['required', 'string', 'max:25', 'regex:/^\d+(?:\.\d+)?x\d+(?:\.\d+)?$/'],
            'surface_width_m' => ['required', 'numeric', 'min:0.5', 'max:30'],
            'surface_height_m' => ['required', 'numeric', 'min:0.5', 'max:30'],
            'user_prompt' => ['nullable', 'string', 'max:500'],
        ]);

        /* TILE_SIZE_RANGE_GUARD */
        [$tileWidthCm, $tileHeightCm] = array_map('floatval', explode('x', $validated['tile_size']));
        if ($tileWidthCm < 2 || $tileWidthCm > 300 || $tileHeightCm < 2 || $tileHeightCm > 300) {
            return response()->json(['message' => 'Karo ebadı 2–300 cm aralığında olmalıdır.'], 422);
        }
        if ($validated['tile_source'] === 'custom' && ! $request->hasFile('tile_image')) {
            return response()->json([
                'message' => 'Kendi seramiğinizi seçtiğiniz için karo görseli yüklemelisiniz.',
            ], 422);
        }

        // Telefonla giriş + kredi sistemi hakkı ayırdıysa oturumdaki eski test kredisi kullanılmaz
        $walletCharge = $request->attributes->get('ai_charge');
        $credits = $walletCharge ? 1 : (int) $request->session()->get(self::SESSION_KEY, 0);
        if ($credits < 1) {
            return response()->json([
                'message' => 'Test render krediniz kalmadı.',
                'payment_required' => true,
            ], 402);
        }

        if (!config('services.ai_spend_enabled')) {
            return response()->json([
                'message' => 'AI görselleştirme geçici olarak devre dışı.',
                'ai_spend_disabled' => true,
            ], 503);
        }

        $apiKey = config('services.gemini.api_key');
        if (! $apiKey) {
            return response()->json([
                'message' => 'GEMINI_API_KEY tanımlı değil. 2D yerleşim çalışır; Gemini AI render için anahtar gerekli.',
            ], 503);
        }

        $room = $request->file('room_image');
        $roomBytes = file_get_contents($room->getRealPath());
        if ($roomBytes === false) {
            return response()->json(['message' => 'Mekân görseli okunamadı.'], 422);
        }

        $tileBytes = null;
        $tileMime = null;

        if ($validated['tile_source'] === 'custom') {
            $tile = $request->file('tile_image');
            $tileBytes = file_get_contents($tile->getRealPath());
            $tileMime = $tile->getMimeType() ?: 'image/png';
        } else {
            $tileMeta = self::CATALOG[$validated['catalog_tile'] ?? 'carrara-beyaz'];
            $tilePath = public_path('images/tile-visualizer/'.$tileMeta['file']);

            if (! is_file($tilePath)) {
                return response()->json(['message' => 'Test karo görseli bulunamadı.'], 500);
            }

            $tileBytes = file_get_contents($tilePath);
            $tileMime = mime_content_type($tilePath) ?: 'image/png';
        }

        if ($tileBytes === false) {
            return response()->json(['message' => 'Karo görseli okunamadı.'], 422);
        }

        $surfaceLabels = [
            'floor' => 'floor surface',
            'wall' => 'main wall surface',
            'shower_wall' => 'shower wall surface',
            'backsplash' => 'backsplash surface',
        ];

        // TILE_SCALE_SYNC_V2 + GRID_REFERENCE_V1
        $surfaceWidthM = (float) $validated['surface_width_m'];
        $surfaceHeightM = (float) $validated['surface_height_m'];

        $tileWidthM = $tileWidthCm / 100;
        $tileHeightM = $tileHeightCm / 100;

        $tileCols = (int) ceil($surfaceWidthM / $tileWidthM);
        $tileRows = (int) ceil($surfaceHeightM / $tileHeightM);
        $tileCount = $tileCols * $tileRows;

        $tileScaleInstruction =
            "The ceramic tile is exactly {$tileWidthCm} cm wide by {$tileHeightCm} cm high. ".
            "The target surface is {$surfaceWidthM} m wide by {$surfaceHeightM} m high. ".
            "The mathematically matching 2D layout is {$tileCols} columns by {$tileRows} rows, ".
            "{$tileCount} tiles total before cuts or waste. ".
            "Keep this physical tile scale, aspect ratio and grout density in perspective. ".
            "Do not arbitrarily enlarge or shrink the tiles.";

        // GRID_REFERENCE_V2 — fiziksel karo oranını koruyan grid
        $gridW = 800;
        $surfaceAspect = $surfaceHeightM / max(0.0001, $surfaceWidthM);
        $gridH = (int) round($gridW * $surfaceAspect);
        $gridH = max(260, min(1200, $gridH));
        $grid  = imagecreatetruecolor($gridW, $gridH);

        $white = imagecolorallocate($grid, 255, 255, 255);
        $fill  = imagecolorallocate($grid, 242, 244, 247);
        $line  = imagecolorallocate($grid, 102, 112, 133);
        imagefilledrectangle($grid, 0, 0, $gridW, $gridH, $white);

        $pad    = 20;
        $innerW = $gridW - ($pad * 2);
        $innerH = $gridH - ($pad * 2);
        imagefilledrectangle($grid, $pad, $pad, $pad + $innerW, $pad + $innerH, $fill);

        $pxPerMeterX = $innerW / $surfaceWidthM;
        $pxPerMeterY = $innerH / $surfaceHeightM;
        $cellW = max(1, (int) round($tileWidthM  * $pxPerMeterX));
        $cellH = max(1, (int) round($tileHeightM * $pxPerMeterY));

        $tileRef = @imagecreatefromstring($tileBytes);
        if ($tileRef) {
            for ($cy = 0; $cy < $tileRows; $cy++) {
                for ($cx = 0; $cx < $tileCols; $cx++) {
                    $x1 = $pad + ($cx * $cellW);
                    $y1 = $pad + ($cy * $cellH);
                    $w = min($cellW, $pad + $innerW - $x1);
                    $h = min($cellH, $pad + $innerH - $y1);
                    if ($w < 1 || $h < 1) continue;
                    imagecopyresampled(
                        $grid, $tileRef,
                        $x1, $y1,
                        0, 0,
                        $w, $h,
                        imagesx($tileRef), imagesy($tileRef)
                    );
                }
            }
            imagedestroy($tileRef);
        }

        for ($x = 0; $x <= $tileCols; $x++) {
            $px = $pad + ($x * $cellW);
            if ($px > $pad + $innerW) $px = $pad + $innerW;
            imageline($grid, $px, $pad, $px, $pad + $innerH, $line);
        }
        for ($y = 0; $y <= $tileRows; $y++) {
            $py = $pad + ($y * $cellH);
            if ($py > $pad + $innerH) $py = $pad + $innerH;
            imageline($grid, $pad, $py, $pad + $innerW, $py, $line);
        }

        ob_start();
        imagepng($grid);
        $gridBytes = (string) ob_get_clean();
        imagedestroy($grid);
        $prompt = implode(' ', [
            'Edit the FIRST image, which is the customer room photo.',
            'Use the SECOND image only as the ceramic tile texture, color and pattern reference.',
            'The THIRD image is the exact tile-layout density reference. Match its tile count, proportions and grout-line density. Do not merge adjacent tiles. Preserve exactly the same columns and rows before applying perspective.',
            'Apply that tile only to the '.$surfaceLabels[$validated['surface']].'.',
            'Preserve the original room geometry, camera angle, perspective, furniture, sanitary fixtures, doors, windows, lighting and every non-selected surface.',
            'Do not redesign the room. Do not add, remove, move or replace objects.',
            $tileScaleInstruction,
            'Use straight realistic grout joints, correct perspective, physically believable reflections and installation scale.',
            'Return one photorealistic architectural material visualization. No text, labels, collage, borders or before-after layout.',
        ]);

        if (! empty($validated['user_prompt'])) { $prompt .= ' Additional customer revision request: '.strip_tags($validated['user_prompt']).' Preserve room geometry and all unchanged elements.'; }
        $model = config('services.gemini.image_model', 'gemini-3.1-flash-image');
        $endpoint = 'https://generativelanguage.googleapis.com/v1/models/'.rawurlencode($model).':generateContent';

        $payload = [
            'contents' => [[
                'parts' => [
                    ['text' => $prompt],
                    [
                        'inline_data' => [
                            'mime_type' => $room->getMimeType() ?: 'image/jpeg',
                            'data' => base64_encode($roomBytes),
                        ],
                    ],
                    [
                        'inline_data' => [
                            'mime_type' => $tileMime,
                            'data' => base64_encode($tileBytes),
                        ],
                    ],                    // GRID_REFERENCE_INPUT_V1
                    [
                        'inline_data' => [
                            'mime_type' => 'image/png',
                            'data' => base64_encode($gridBytes),
                        ],
                    ],
                ],
            ]],
            'generationConfig' => [
                'responseModalities' => ['IMAGE'],
            ],
        ];

        $usageRecorder = app(AiUsageRecorder::class);
        $usage = $usageRecorder->create([
            'user_id' => auth()->id(),
            'tool_key' => 'tile_visualizer',
            'provider' => 'gemini',
            'model' => $model,
            'operation' => 'image_generation',
            'input_image_count' => 3,
            'status' => 'pending',
            'metadata' => [
                'surface' => $validated['surface'],
                'tile_source' => $validated['tile_source'],
                'tile_size' => $validated['tile_size'],
            ],
        ]);

        $startedAt = microtime(true);

        try {
            $response = Http::withHeaders([
                    'x-goog-api-key' => $apiKey,
                    'Content-Type' => 'application/json',
                ])
                ->timeout(180)
                ->post($endpoint, $payload);

            if (! $response->successful()) {
                $latencyMs = (int) round((microtime(true) - $startedAt) * 1000);

                $usageRecorder->fail(
                    $usage,
                    'HTTP_'.$response->status(),
                    $latencyMs,
                    [
                        'provider_status' => $response->status(),
                    ]
                );

                if ($response->status() === 402) { return response()->json(['message' => 'AI servis kotası doldu. Gemini API bakiyesi veya kotası yenilenmelidir.', 'provider_quota_exhausted' => true], 503); }
                Log::error('Tile visualizer Gemini error', [
                    'status' => $response->status(),
                    'body' => mb_substr($response->body(), 0, 3000),
                ]);

                return response()->json([
                    'message' => 'Gemini AI görsel üretilemedi. Lütfen tekrar deneyin.',
                ], 502);
            }

            $responseJson = $response->json();
            $latencyMs = (int) round((microtime(true) - $startedAt) * 1000);

            $usageRecorder->complete(
                $usage,
                is_array($responseJson) ? $responseJson : [],
                $latencyMs
            );

            $usage->update([
                'output_image_count' => 1,
                'provider_request_id' =>
                    $response->header('x-request-id')
                    ?: $response->header('x-goog-request-id')
                    ?: null,
            ]);

            $parts = $response->json('candidates.0.content.parts', []);
            $imageData = null;
            $imageMime = 'image/png';

            foreach ($parts as $part) {
                $inline = $part['inlineData'] ?? $part['inline_data'] ?? null;
                if (! is_array($inline)) {
                    continue;
                }

                $data = $inline['data'] ?? null;
                if (! is_string($data) || $data === '') {
                    continue;
                }

                $mime = $inline['mimeType'] ?? $inline['mime_type'] ?? 'image/png';
                if (! str_starts_with((string) $mime, 'image/')) {
                    continue;
                }

                $imageData = $data;
                $imageMime = $mime;
                break;
            }

            if (! $imageData) {
                $usageRecorder->fail(
                    $usage,
                    'NO_IMAGE',
                    (int) round((microtime(true) - $startedAt) * 1000)
                );

                Log::warning('Tile visualizer Gemini returned no image', [
                    'body' => mb_substr($response->body(), 0, 3000),
                ]);

                return response()->json([
                    'message' => 'Gemini yanıtında görsel bulunamadı. Tekrar deneyin.',
                ], 502);
            }

            // PAID_PREVIEW_V7: original output never goes to the browser as preview.
            $originalBytes = base64_decode($imageData, true);
            if ($originalBytes === false || $originalBytes === '') {
                $usageRecorder->fail(
                    $usage,
                    'IMAGE_DECODE_FAILED',
                    (int) round((microtime(true) - $startedAt) * 1000)
                );

                return response()->json(['message' => 'AI görsel çıktısı çözümlenemedi.'], 502);
            }

            $render = $this->storePrivateRender($request, $originalBytes, (string) $imageMime);
            $previewBytes = $this->makeWatermarkedPreview($originalBytes);

            $remaining = max(0, $credits - 1);
            $creditsLabel = null;
            if ($walletCharge) {
                $svc = app(\App\Services\AI\AiCredits::class);
                $st = $svc->status(\App\Models\AiWallet::query()->find($walletCharge['wallet_id']));
                $remaining = (int) ($st['credits'] ?? 0);
                $creditsLabel = self::walletLabel($st);
            } else {
                $request->session()->put(self::SESSION_KEY, $remaining);
            }

            return response()->json([
                'success' => true,
                'image' => 'data:image/jpeg;base64,'.base64_encode($previewBytes),
                'preview_only' => true,
                'render_token' => $render['token'],
                'download_url' => url('/api/seramik-gorsellestirici/download/'.$render['token']),
                'credits' => $remaining,
                'credits_label' => $creditsLabel,
                'model' => $model,
                'provider' => 'gemini',
            ]);
        } catch (\Throwable $e) {
            if (isset($usage, $usageRecorder)) {
                $usageRecorder->fail(
                    $usage,
                    'EXCEPTION',
                    isset($startedAt)
                        ? (int) round((microtime(true) - $startedAt) * 1000)
                        : null,
                    [
                        'exception_class' => get_class($e),
                    ]
                );
            }

            Log::error('Tile visualizer Gemini exception', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'message' => 'Gemini AI bağlantısında hata oluştu. Lütfen tekrar deneyin.',
            ], 500);
        }
    }

    public function download(Request $request, string $token)
    {
        if (! preg_match('/^[a-f0-9]{32}$/', $token)) {
            abort(404);
        }

        $renders = (array) $request->session()->get('tile_visualizer_render_tokens', []);
        $meta = $renders[$token] ?? null;
        if (! is_array($meta) || (int) ($meta['created_at'] ?? 0) < time() - 86400) {
            abort(404);
        }

        $file = basename((string) ($meta['file'] ?? ''));
        $path = storage_path('app/private/tile-visualizer/'.$file);
        if ($file === '' || ! is_file($path)) {
            abort(404);
        }

        $ext = preg_replace('/[^a-z0-9]/i', '', (string) ($meta['ext'] ?? 'png')) ?: 'png';
        $mime = (string) ($meta['mime'] ?? 'image/png');

        return response()->download(
            $path,
            'worksitem-seramik-render-'.$token.'.'.$ext,
            ['Content-Type' => $mime]
        );
    }

    private function storePrivateRender(Request $request, string $bytes, string $mime): array
    {
        $dir = storage_path('app/private/tile-visualizer');
        if (! is_dir($dir) && ! mkdir($dir, 0775, true) && ! is_dir($dir)) {
            throw new \RuntimeException('Private render dizini oluşturulamadı.');
        }

        foreach (glob($dir.'/*') ?: [] as $oldFile) {
            if (is_file($oldFile) && filemtime($oldFile) < time() - 86400) {
                @unlink($oldFile);
            }
        }

        $ext = match (strtolower($mime)) {
            'image/jpeg', 'image/jpg' => 'jpg',
            'image/webp' => 'webp',
            default => 'png',
        };

        $token = bin2hex(random_bytes(16));
        $file = $token.'.'.$ext;
        if (file_put_contents($dir.'/'.$file, $bytes, LOCK_EX) === false) {
            throw new \RuntimeException('HD render geçici olarak saklanamadı.');
        }

        $renders = (array) $request->session()->get('tile_visualizer_render_tokens', []);
        foreach ($renders as $key => $meta) {
            if (! is_array($meta) || (int) ($meta['created_at'] ?? 0) < time() - 86400) {
                unset($renders[$key]);
            }
        }
        $renders[$token] = [
            'file' => $file,
            'ext' => $ext,
            'mime' => $mime,
            'created_at' => time(),
        ];
        if (count($renders) > 12) {
            uasort($renders, fn ($a, $b) => ((int) ($a['created_at'] ?? 0)) <=> ((int) ($b['created_at'] ?? 0)));
            while (count($renders) > 12) {
                array_shift($renders);
            }
        }
        $request->session()->put('tile_visualizer_render_tokens', $renders);

        return ['token' => $token, 'file' => $file];
    }

    /** Filigranlı, küçültülmüş önizleme (banyo planlayıcı render'ı da kullanır) */
    public static function watermark(string $bytes): string
    {
        return (new self)->makeWatermarkedPreview($bytes);
    }

    private function makeWatermarkedPreview(string $bytes): string
    {
        if (false && class_exists(\Imagick::class) && class_exists(\ImagickDraw::class)) {
            try {
                return $this->makeWatermarkedPreviewWithImagick($bytes);
            } catch (\Throwable $e) {
                \Log::warning('Tile visualizer Imagick watermark fallback', ['error' => $e->getMessage()]);
            }
        }

        if (! function_exists('imagecreatefromstring')) {
            throw new \RuntimeException('Görsel önizleme modülü etkin değil.');
        }

        $src = @imagecreatefromstring($bytes);
        if (! $src) {
            throw new \RuntimeException('AI görsel önizlemesi oluşturulamadı.');
        }
        $srcW = imagesx($src); $srcH = imagesy($src);
        $maxSide = 960; $scale = min(1, $maxSide / max($srcW, $srcH));
        $w = max(1, (int) round($srcW * $scale));
        $h = max(1, (int) round($srcH * $scale));
        $dst = imagecreatetruecolor($w, $h);
        imagecopyresampled($dst, $src, 0, 0, 0, 0, $w, $h, $srcW, $srcH);
        imagedestroy($src);
        imagealphablending($dst, true);
        $white = imagecolorallocatealpha($dst, 255, 255, 255, 58);
        $shadow = imagecolorallocatealpha($dst, 0, 0, 0, 92);
        $text = 'WORKSITEM.COM TARAFINDAN CIZILMISTIR';
        for ($y = 45, $row = 0; $y < $h; $y += 125, $row++) {
            $offset = ($row % 2) ? 170 : 0;
            for ($x = -80; $x < $w + 420; $x += 420) {
                imagestring($dst, 5, $x + $offset + 1, $y + 1, $text, $shadow);
                imagestring($dst, 5, $x + $offset, $y, $text, $white);
            }
        }
        ob_start(); imagejpeg($dst, null, 84); $out = (string) ob_get_clean(); imagedestroy($dst);
        if ($out === '') throw new \RuntimeException('Filigranlı önizleme üretilemedi.');
        return $out;
    }

    private function makeWatermarkedPreviewWithImagick(string $bytes): string
    {
        $image = new \Imagick();
        $image->readImageBlob($bytes);
        $image->setIteratorIndex(0);
        $image->setImageColorspace(\Imagick::COLORSPACE_SRGB);
        $srcW = $image->getImageWidth(); $srcH = $image->getImageHeight();
        $maxSide = 960; $scale = min(1, $maxSide / max(1, max($srcW, $srcH)));
        $w = max(1, (int) round($srcW * $scale));
        $h = max(1, (int) round($srcH * $scale));
        if ($scale < 1) $image->resizeImage($w, $h, \Imagick::FILTER_LANCZOS, 1, true);
        else { $w = $srcW; $h = $srcH; }

        $text = 'WORKSITEM.COM TARAFINDAN ÇİZİLMİŞTİR';
        $font = 'Droid-Sans-Fallback';
        $size = max(16, min(26, (int) round($w / 38)));
        $probe = new \ImagickDraw();
        $probe->setFont($font); $probe->setFontSize($size);
        if (method_exists($probe, 'setTextEncoding')) $probe->setTextEncoding('UTF-8');
        $metrics = $image->queryFontMetrics($probe, $text, false);
        $textW = max(300, (int) ceil($metrics['textWidth'] ?? 360));
        $textH = max(24, (int) ceil($metrics['textHeight'] ?? 30));
        $stepX = max(460, (int) round($textW * 1.25));
        $stepY = max(145, (int) round($textH * 4.6));

        $draw = new \ImagickDraw();
        $draw->setFont($font); $draw->setFontSize($size);
        if (method_exists($draw, 'setTextEncoding')) $draw->setTextEncoding('UTF-8');
        $draw->setFillColor(new \ImagickPixel('white'));
        $draw->setFillOpacity(0.42);
        $draw->setStrokeColor(new \ImagickPixel('black'));
        $draw->setStrokeOpacity(0.16);
        $draw->setStrokeWidth(0.7);

        for ($y = 55, $row = 0; $y < $h + $stepY; $y += $stepY, $row++) {
            $offset = ($row % 2) ? (int) round($stepX / 2) : 0;
            for ($x = -$stepX; $x < $w + $stepX; $x += $stepX) {
                $image->annotateImage($draw, $x + $offset, $y, -18, $text);
            }
        }
        $image->setImageFormat('jpeg');
        $image->setImageCompressionQuality(84);
        $blob = $image->getImageBlob();
        $image->clear(); $image->destroy();
        if ($blob === '') throw new \RuntimeException('Imagick filigranlı önizleme üretilemedi.');
        return $blob;
    }
    private function testMode(): bool
    {
        return app()->environment('staging') || (bool) config('services.tile_visualizer.test_mode');
    }

    /** Telefonla giriş yapan kullanıcının AI hakkı özeti (sayfadaki rozet için) */
    public static function walletLabel(?array $st): string
    {
        if (! $st || empty($st['logged_in'])) {
            return 'Günde '.($st['free_per_day'] ?? 1).' ücretsiz AI';
        }

        return 'Bugün '.$st['free_left'].' ücretsiz · '.$st['credits'].' kredi';
    }
}
