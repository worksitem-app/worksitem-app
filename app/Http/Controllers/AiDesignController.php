<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AiGeneration;
use App\Services\AiDesignService;
use Illuminate\Support\Facades\Log;

class AiDesignController extends Controller
{
    protected AiDesignService $aiService;

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
            'room_image' => 'required|image|max:5120',
            'style' => 'required|string',
            'user_prompt' => 'nullable|string|max:300',
            'room_area' => 'nullable|numeric|min:1|max:10000',
        ]);

        $path = $request->file('room_image')->store('ai_inputs', 'public');

        $generation = AiGeneration::create([
            'tool_name' => 'ic-mekan',
            'style' => $request->style,
            'input_image' => $path,
            'status' => 'processing',
            'user_prompt' => $request->input('user_prompt'),
            'room_area' => $request->input('room_area'),
        ]);

        $result = $this->aiService->startAsync(
            storage_path('app/public/' . $path),
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

        // AI prompt'unu kaydet
        $generation->update([
            'prediction_id' => $result['prediction_id'],
            'ai_prompt' => $result['prompt_used'] ?? null,
        ]);

        return response()->json([
            'success' => true,
            'generation_id' => $generation->id,
            'prediction_id' => $result['prediction_id'],
            'message' => 'Tasarım başlatıldı.',
        ]);
    }

    public function checkStatus($id)
    {
        $generation = AiGeneration::findOrFail($id);

        if ($generation->status === 'completed' && $generation->output_image) {
            return response()->json([
                'status' => 'succeeded',
                'output_url' => $generation->output_image,
                'generation_id' => $generation->id,
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
        }

        return response()->json($result);
    }
}
