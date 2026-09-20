<?php
namespace App\Http\Controllers\ConTech;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\ConTech\PriceImportService;

class AdminPriceController extends Controller
{
    public function index()
    {
        return view('admin.price-import');
    }

    public function upload(Request $request, PriceImportService $importService)
    {
        // Yapay zeka analizi uzun sürebileceği için PHP limitlerini kaldırıyoruz
        set_time_limit(300); // 5 dakika
        ini_set('memory_limit', '512M');

        $request->validate([
            'price_pdf' => 'required|file|mimes:pdf|max:15360'
        ]);

        try {
            $result = $importService->importFromPdf($request->file('price_pdf'));
            return back()->with('success', $result['message']);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
