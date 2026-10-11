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

    public function pozImportUpload(Request $request)
    {
        $request->validate([
            // xlsx/xls fgetcsv ile okunamaz; yalnızca CSV kabul edilir.
            'file' => 'required|file|mimes:csv,txt|max:20480',
            'institution' => 'required|string|max:100',
            'year' => 'required|digits:4',
        ]);

        $institution = $request->string('institution')->trim()->toString();
        $year = (string) $request->input('year');

        $handle = fopen($request->file('file')->getRealPath(), 'r');
        if (! $handle) {
            return back()->with('error', 'Dosya okunamadı.');
        }

        // Ayraç tespiti: ilk satırda hangi ayraç daha çok geçiyorsa o.
        $firstLine = (string) fgets($handle);
        $delimiter = substr_count($firstLine, ';') >= substr_count($firstLine, ',') ? ';' : ',';
        rewind($handle);
        fgetcsv($handle, 0, $delimiter); // başlık satırını atla

        $importedCount = 0;
        $skipped = 0;
        $now = now();

        DB::transaction(function () use ($handle, $delimiter, $institution, $year, $now, &$importedCount, &$skipped) {
            while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
                if (count($row) < 4) {
                    $skipped++;
                    continue;
                }

                $pozNo = trim((string) $row[0]);
                $description = trim((string) $row[1]);
                $unitPrice = self::parseTurkishPrice((string) $row[3]);

                if ($pozNo === '' || $description === '' || $unitPrice === null) {
                    $skipped++;
                    continue;
                }

                // Unique index olmadığı için upsert yerine anahtar bazlı updateOrInsert:
                // aynı kurum + yıl + poz tekrar import edildiğinde güncellenir, çoğalmaz.
                TenderPoz::query()->updateOrInsert(
                    ['institution' => $institution, 'year' => $year, 'poz_no' => $pozNo],
                    [
                        'description' => $description,
                        'unit' => trim((string) $row[2]) ?: 'm²',
                        'unit_price' => $unitPrice,
                        'updated_at' => $now,
                        'created_at' => $now,
                    ]
                );

                $importedCount++;
            }
        });

        fclose($handle);

        return back()->with('success', "Toplam {$importedCount} poz içe aktarıldı/güncellendi. Atlanan satır: {$skipped}.");
    }

    /**
     * "1.250,50 TL" → 1250.50 | "3.000" → 3000 | "1250.50" → 1250.50 | "1,250.50" → 1250.50
     */
    public static function parseTurkishPrice(string $raw): ?float
    {
        $s = str_replace(['TL', '₺', ' ', "\u{00A0}"], '', trim($raw));
        if ($s === '') {
            return null;
        }

        $lastComma = strrpos($s, ',');
        $lastDot = strrpos($s, '.');

        if ($lastComma !== false && ($lastDot === false || $lastComma > $lastDot)) {
            // Türkçe format: nokta binlik, virgül ondalık → "1.250,50"
            $s = str_replace('.', '', $s);
            $s = str_replace(',', '.', $s);
        } elseif ($lastComma === false && $lastDot !== false && preg_match('/^\d{1,3}(\.\d{3})+$/', $s)) {
            // Yalnızca binlik noktası → "3.000" / "1.250.000" (Türkçe)
            $s = str_replace('.', '', $s);
        } else {
            // İngilizce format: virgül binlik, nokta ondalık → "1,250.50" / "1250.50"
            $s = str_replace(',', '', $s);
        }

        return is_numeric($s) ? round((float) $s, 2) : null;
    }
}