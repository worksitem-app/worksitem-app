<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\AiGeneration;
use App\Services\AiDesignService;

class AiDesignController extends Controller
{
    protected $aiService;
    public function __construct(AiDesignService $aiService) { $this->aiService = $aiService; }

    public function interiorIndex() { return view('ai.interior'); }

    public function generateInterior(Request $request)
    {
        $request->validate([
            'room_image' => 'required|image|max:5120', // Maks 5MB
            'style' => 'required|string'
        ]);

        // Resmi Sunucuya Kaydet
        $path = $request->file('room_image')->store('ai_inputs', 'public');
        
        // Veritabanına Ön Kayıt (Geçmişi tutmak için)
        $generation = AiGeneration::create([
            'tool_name' => 'ic-mekan',
            'style' => $request->style,
            'input_image' => $path,
            'status' => 'processing'
        ]);

        // Servise Gönder (API Çağrısı)
        $response = $this->aiService->generateInterior(storage_path('app/public/' . $path), $request->style);

        if ($response['status'] === 'success') {
            $generation->update([
                'output_image' => $response['output_url'],
                'provider' => $response['provider'],
                'status' => 'completed'
            ]);
            // Çıktıyı sayfaya geri gönder
            return back()->with('success_image', $response['output_url'])->with('style', $request->style);
        }

        return back()->withErrors(['Hata' => $response['message'] ?? 'Bilinmeyen bir API hatası oluştu.']);
    }
}
