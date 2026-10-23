<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AiGeneration;
use App\Services\AiDesignService;
use App\Services\AiCostEstimationService;
use Illuminate\Support\Facades\Log;

class AiDesignController extends Controller
{
    protected AiDesignService $aiService;

    private const FREE_DAILY_LIMIT = 3;

    public function __construct(AiDesignService $aiService)
    {
        $this->aiService = $aiService;
    }

    public function interiorIndex()
    {
        return view('ai.interior');
    }

    public function startGeneration(Request $request)
    {
        $request->validate([
            'room_image' => 'required|image|mimes:jpg,jpeg,png,webp|max:5120',
            'style' => 'required|string|max:50',
            'user_prompt' => 'nullable|string|max:300',
            'room_area' => 'nullable|numeric|min:1|max:10000',
        ]);

        $ipHash = $this->ipHash($request);
        $sessionKey = $request->input('session_key') ?? session()->getId();

        $todayCount = AiGeneration::where('ip_hash', $ipHash)
            ->whereDate('created_at', today())
            ->where('is_free_quota', true)
            ->count();

        // Telefonla giriş + kredi sistemi hakkı ayırdıysa IP tabanlı eski günlük sınır uygulanmaz
        $walletCharge = $request->attributes->get('ai_charge');
        if (! $walletCharge && $todayCount >= self::FREE_DAILY_LIMIT) {
            return response()->json([
                'success' => false,
                'message' => 'Günlük ücretsiz tasarım hakkınız doldu. Profesyonel tasarım için Kainta İnşaat ile iletişime geçin.',
                'kainta_url' => 'https://kainta.com',
                'quota_exceeded' => true,
            ], 429);
        }

        // v107: müşteri oda fotoğrafı herkese açık diske değil özel diske (KVKK)
        $path = $request->file('room_image')->store('ai_inputs', 'local');

        $generation = AiGeneration::create([
            'tool_name' => 'ic-mekan',
            'style' => $request->style,
            'input_image' => $path,
            'status' => 'processing',
            'user_prompt' => $request->input('user_prompt'),
            'room_area' => $request->input('room_area'),
            'ip_hash' => $ipHash,
            'session_key' => $sessionKey,
            'is_free_quota' => true,
        ]);

        $result = $this->aiService->startAsync(
            \Illuminate\Support\Facades\Storage::disk('local')->path($path),
            $request->style,
            $request->input('user_prompt')
        );

        if ($result['status'] === 'error') {
            $generation->update(['status' => 'failed']);
            return response()->json([
                'success' => false,
                'message' => $result['message'],
            ], 500);
        }

        $generation->update([
            'prediction_id' => $result['prediction_id'],
            'ai_prompt' => $result['prompt_used'] ?? null,
        ]);

        $remaining = max(0, self::FREE_DAILY_LIMIT - ($todayCount + 1));
        if ($walletCharge) {
            // Tasarım sonradan başarısız olursa hak iade edilsin (checkStatus)
            \Illuminate\Support\Facades\Cache::put('ai-charge:'.$generation->public_id, $walletCharge, now()->addHours(6));
        }

        return response()->json([
            'success' => true,
            'generation_id' => $generation->public_id,
            'remaining_quota' => $remaining,
            'message' => 'Tasarım başlatıldı.',
        ]);
    }

    public function checkStatus(string $id)
    {
        $generation = AiGeneration::findPublicOrFail($id);

        if ($generation->status === 'completed' && $generation->output_image) {
            return response()->json([
                'status' => 'succeeded',
                'output_url' => $generation->output_image,
                'generation_id' => $generation->public_id,
                'room_area' => $generation->room_area,
                'can_estimate_cost' => true,
            ]);
        }

        if ($generation->status === 'failed') {
            return response()->json(['status' => 'failed', 'message' => 'Tasarım başarısız oldu.']);
        }

        $predictionId = $generation->prediction_id;
        if (!$predictionId) {
            return response()->json(['status' => 'processing', 'message' => 'Prediction ID bekleniyor...']);
        }

        $result = $this->aiService->checkAsync($predictionId);

        if ($result['status'] === 'succeeded' && !empty($result['output_url'])) {
            $generation->update([
                'output_image' => $result['output_url'],
                'provider' => 'replicate',
                'status' => 'completed',
            ]);
        } elseif ($result['status'] === 'failed') {
            $generation->update(['status' => 'failed']);
            $this->refundWalletCharge($generation->public_id);
        }

        return response()->json($result);
    }

    public function getQuota(Request $request)
    {
        $ipHash = $this->ipHash($request);

        $todayCount = AiGeneration::where('ip_hash', $ipHash)
            ->whereDate('created_at', today())
            ->where('is_free_quota', true)
            ->count();

        return response()->json([
            'daily_limit' => self::FREE_DAILY_LIMIT,
            'used' => $todayCount,
            'remaining' => max(0, self::FREE_DAILY_LIMIT - $todayCount),
        ]);
    }

    public function estimate(Request $request, string $id)
    {
        $request->validate([
            'room_area' => 'required|numeric|min:1|max:10000',
        ]);

        $generation = AiGeneration::findPublicOrFail($id);

        $service = new AiCostEstimationService();
        $result = $service->estimate($generation->id, (float) $request->room_area);

        $generation->update([
            'room_area' => $request->room_area,
            'estimated_cost' => $result['total'],
            'cost_breakdown' => $result,
            'cost_calculated_at' => now(),
        ]);

        return response()->json($result);
    }

    /**
     * KVKK: IP ham saklanmaz. Salt sabit string yerine APP_KEY'den türetilir
     * (kaynak kod sızsa bile rainbow-table ile IP geri çözülemez).
     */
    private function ipHash(Request $request): string
    {
        return hash_hmac('sha256', (string) $request->ip(), (string) config('app.key'));
    }

    /** Kredi/ücretsiz hak ayrılmış bir tasarım başarısız olduysa hakkı bir kez iade eder */
    private function refundWalletCharge(string $publicId): void
    {
        $charge = \Illuminate\Support\Facades\Cache::pull('ai-charge:'.$publicId);
        if (is_array($charge) && ($wallet = \App\Models\AiWallet::query()->find($charge['wallet_id'] ?? 0))) {
            app(\App\Services\AI\AiCredits::class)->refund($wallet, $charge['reservation'], 'ai.interior.start');
        }
    }
}
