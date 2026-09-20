<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UnitPrice;
use App\Models\Lead;
use App\Models\TenderPoz;
use Illuminate\Support\Facades\DB;
use App\Services\AISalesEngineer;

class AdminController extends Controller
{
    public function index()
    {
        // Fiyatları kategorilerine göre gruplayarak çekiyoruz
        $pricesByCategory = UnitPrice::orderBy('category')->get()->groupBy('category');
        return view('admin.prices.index', compact('pricesByCategory'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'price' => 'required|numeric|min:0'
        ]);

        $unitPrice = UnitPrice::findOrFail($id);
        $unitPrice->update([
            'price' => $request->price
        ]);

        \App\Services\CostEngine::clearCache();
        return back()->with('success', $unitPrice->name . ' fiyatı başarıyla güncellendi!');
    }

    public function leads()
    {
        $leads = Lead::orderBy('created_at', 'desc')->get();
        return view('admin.crm.index', compact('leads'));
    }

    public function generateProposal($id)
    {
        $lead = Lead::findOrFail($id);
        
        $serviceName = $lead->calculated_service ?: 'Genel Tadilat/İnşaat';
        $area = $lead->calculated_area ?: 'Belirtilmemiş';
        
        // AI Servisini Çağırıyoruz
        $aiProposalHtml = AISalesEngineer::generateProposal($lead->name, $serviceName, $area, $lead->notes);
        
        // Talebin durumunu güncelleyelim
        $lead->update(['status' => 'teklif_iletildi']);

        return view('admin.crm.proposal-pdf', compact('lead', 'aiProposalHtml'));
    }

    public function pozImportForm()
    {
        $totalPozCount = TenderPoz::count();
        $institutionCounts = TenderPoz::select('institution', DB::raw('count(*) as total'))
                                      ->groupBy('institution')
                                      ->get();
        return view('admin.poz-import', compact('totalPozCount', 'institutionCounts'));
    }

    public function pozImportUpload(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt,xlsx,xls',
            'institution' => 'required|string',
            'year' => 'required|string'
        ]);

        $file = $request->file('file');
        $institution = $request->input('institution');
        $year = $request->input('year');

        $handle = fopen($file->getRealPath(), 'r');
        if (!$handle) {
            return back()->with('error', 'Dosya okunamadı.');
        }

        $header = fgetcsv($handle, 2000, ';'); // Semicolon separator deneme
        if (!$header || count($header) < 3) {
            rewind($handle);
            $header = fgetcsv($handle, 2000, ','); // Comma separator deneme
        }

        $batch = [];
        $importedCount = 0;
        $now = now();

        while (($row = fgetcsv($handle, 2000, ';')) !== FALSE || ($row = fgetcsv($handle, 2000, ',')) !== FALSE) {
            if (count($row) < 4) continue;

            $pozNo = trim($row[0]);
            $description = trim($row[1]);
            $unit = trim($row[2]);
            
            // Fiyatı temizle (1.250,50 TL -> 1250.50)
            $priceStr = str_replace(['TL', '₺', ' ', '.'], '', trim($row[3]));
            $priceStr = str_replace(',', '.', $priceStr);
            $unitPrice = floatval($priceStr);

            if (empty($pozNo) || empty($description)) continue;

            $batch[] = [
                'year' => $year,
                'institution' => $institution,
                'poz_no' => $pozNo,
                'description' => $description,
                'unit' => $unit ?: 'm²',
                'unit_price' => $unitPrice,
                'created_at' => $now,
                'updated_at' => $now,
            ];

            // 500'erli paketler halinde veritabanına toplu yazma (Yüksek Performans)
            if (count($batch) >= 500) {
                TenderPoz::upsert($batch, ['poz_no'], ['year', 'institution', 'description', 'unit', 'unit_price', 'updated_at']);
                $importedCount += count($batch);
                $batch = [];
            }
        }

        if (count($batch) > 0) {
            TenderPoz::upsert($batch, ['poz_no'], ['year', 'institution', 'description', 'unit', 'unit_price', 'updated_at']);
            $importedCount += count($batch);
        }

        fclose($handle);

        return back()->with('success', "Tebrikler! Toplam {$importedCount} adet poz başarıyla içe aktarıldı ve güncellendi.");
    }
}