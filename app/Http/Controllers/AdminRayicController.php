<?php

namespace App\Http\Controllers;

use App\Services\GeminiRayicService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

class AdminRayicController extends Controller
{
    public function index()
    {
        $imports = DB::table('rayic_imports')
            ->orderByDesc('id')
            ->paginate(20);

        return view('admin.rayic.index', compact('imports'));
    }

    public function create()
    {
        return view('admin.rayic.create');
    }

    public function analyze(
        Request $request,
        GeminiRayicService $gemini
    ) {
        $validated = $request->validate([
            'source' => ['required', 'string', 'max:50'],
            'period' => [
                'required',
                'regex:/^\d{4}-\d{2}$/',
            ],
            'pdf' => [
                'required',
                'file',
                'mimes:pdf',
                'max:51200',
            ],
        ]);

        $file = $request->file('pdf');

        $filename =
            now()->format('Ymd_His') .
            '_' .
            Str::slug(
                pathinfo(
                    $file->getClientOriginalName(),
                    PATHINFO_FILENAME
                )
            ) .
            '.pdf';

        $path = $file->storeAs(
            'rayic/uploads',
            $filename
        );

        $absolutePath = Storage::path($path);

        $importId = DB::table('rayic_imports')->insertGetId([
            'source' => $validated['source'],
            'period' => $validated['period'],
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $path,
            'status' => 'analyzing',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        try {
            $result = $gemini->analyzePdf(
                $absolutePath,
                $validated['period'],
                $validated['source']
            );

            $records = $result['records'] ?? [];

            $valid = 0;
            $invalid = 0;
            $changed = 0;

            foreach ($records as $record) {
                $poz = trim((string) ($record['poz_no'] ?? ''));
                $description = trim(
                    (string) ($record['description'] ?? '')
                );
                $unit = trim((string) ($record['unit'] ?? ''));
                $price = $record['price'] ?? null;

                $status = 'valid';
                $note = null;
                $oldPrice = null;

                if (
                    !$poz ||
                    !$description ||
                    !$unit ||
                    !is_numeric($price) ||
                    (float) $price <= 0
                ) {
                    $status = 'invalid';
                    $note = 'Zorunlu alan eksik veya fiyat geçersiz.';
                    $invalid++;
                } else {
                    $valid++;

                    $existing = DB::table('rayic_prices')
                        ->where('source', $validated['source'])
                        ->where('period', $validated['period'])
                        ->where('poz_no', $poz)
                        ->first();

                    if ($existing) {
                        $oldPrice = (float) $existing->price;

                        if (
                            abs(
                                $oldPrice -
                                (float) $price
                            ) > 0.001
                        ) {
                            $changed++;
                            $note = 'Mevcut fiyat değişiyor.';
                        }
                    }
                }

                DB::table('rayic_import_items')->insert([
                    'rayic_import_id' => $importId,
                    'poz_no' => $poz,
                    'description' => $description,
                    'unit' => $unit,
                    'price' => is_numeric($price)
                        ? (float) $price
                        : 0,
                    'source_page' =>
                        isset($record['source_page'])
                            ? (int) $record['source_page']
                            : null,
                    'category' =>
                        trim(
                            (string) (
                                $record['category'] ?? 'diger'
                            )
                        ),
                    'old_price' => $oldPrice,
                    'validation_status' => $status,
                    'validation_note' => $note,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            DB::table('rayic_imports')
                ->where('id', $importId)
                ->update([
                    'total_records' => count($records),
                    'valid_records' => $valid,
                    'invalid_records' => $invalid,
                    'changed_records' => $changed,
                    'status' => 'ready',
                    'analyzed_at' => now(),
                    'updated_at' => now(),
                ]);

            return redirect()
                ->route(
                    'admin.rayic.preview',
                    $importId
                );

        } catch (Throwable $e) {
            DB::table('rayic_imports')
                ->where('id', $importId)
                ->update([
                    'status' => 'error',
                    'error_message' => $e->getMessage(),
                    'updated_at' => now(),
                ]);

            return back()
                ->withInput()
                ->withErrors([
                    'pdf' =>
                        'PDF analiz edilemedi: ' .
                        $e->getMessage(),
                ]);
        }
    }

    public function preview(int $id)
    {
        $import = DB::table('rayic_imports')
            ->where('id', $id)
            ->first();

        abort_unless($import, 404);

        $items = DB::table('rayic_import_items')
            ->where('rayic_import_id', $id)
            ->orderBy('id')
            ->paginate(100);

        return view(
            'admin.rayic.preview',
            compact('import', 'items')
        );
    }

    public function approve(int $id)
    {
        $import = DB::table('rayic_imports')
            ->where('id', $id)
            ->first();

        abort_unless($import, 404);

        if ($import->status !== 'ready') {
            return back()->withErrors([
                'import' =>
                    'Bu import onaylanabilir durumda değil.',
            ]);
        }

        DB::transaction(function () use ($import, $id) {

            $items = DB::table('rayic_import_items')
                ->where('rayic_import_id', $id)
                ->where('validation_status', 'valid')
                ->get();

            foreach ($items as $item) {
                DB::table('rayic_prices')->upsert(
                    [[
                        'source' => $import->source,
                        'period' => $import->period,
                        'poz_no' => $item->poz_no,
                        'description' => $item->description,
                        'unit' => $item->unit,
                        'price' => $item->price,
                        'source_file' => $import->file_name,
                        'source_page' => $item->source_page,
                        'category' => $item->category,
                        'is_active' => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]],
                    [
                        'source',
                        'period',
                        'poz_no',
                    ],
                    [
                        'description',
                        'unit',
                        'price',
                        'source_file',
                        'source_page',
                        'category',
                        'is_active',
                        'updated_at',
                    ]
                );
            }

            DB::table('rayic_imports')
                ->where('id', $id)
                ->update([
                    'status' => 'approved',
                    'approved_at' => now(),
                    'updated_at' => now(),
                ]);
        });

        return redirect()
            ->route('admin.rayic.index')
            ->with(
                'success',
                'Rayiç fiyatlar başarıyla veritabanına aktarıldı.'
            );
    }
}
