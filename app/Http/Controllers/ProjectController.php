<?php
namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Question;
use App\Services\KaintaCostEngine;
use App\Services\AiRenderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProjectController extends Controller
{
    public function create() {
        $projectTypes = DB::table('project_types')->where('is_active', 1)->orderBy('sort_order')->get();
        return view('projects.create', compact('projectTypes'));
    }

    public function start($slug) {
        $projectType = DB::table('project_types')->where('slug', $slug)->orWhere('id', $slug)->first();
        if (!$projectType) abort(404, 'Proje tipi bulunamadı.');
        $project = Project::create(['project_type_id' => $projectType->id, 'status' => 'draft']);
        return redirect()->route('projects.questions', $project->id);
    }

    public function questions(Project $project) {
        $questions = Question::with('options')->where('project_type_id', $project->project_type_id)->orderBy('sort_order')->get();
        return view('projects.questions', compact('project', 'questions'));
    }

    // =========================================================================
    // YENİ: JSON FORMATINDAKİ KOMUTLARI ALIP AI SERVİSİNE İLETEN METOT
    // =========================================================================
    
    /**
     * Wizard: sorular formundan gelen cevapları kaydet.
     */
    public function saveAnswers(Request $request, Project $project)
    {
        $validated = $request->validate([
            'answers' => ['required', 'array'],
            'answers.*' => ['nullable'],
        ]);

        DB::transaction(function () use ($validated, $project) {
            DB::table('project_answers')
                ->where('project_id', $project->id)
                ->delete();

            foreach ($validated['answers'] as $questionId => $value) {
                if ($value === null || $value === '') {
                    continue;
                }

                $question = DB::table('project_questions')
                    ->where('id', $questionId)
                    ->where('project_type_id', $project->project_type_id)
                    ->first();

                if (! $question) {
                    continue;
                }

                $optionId = null;
                if (in_array($question->type, ['select', 'radio'], true)) {
                    $optionId = DB::table('project_question_options')
                        ->where('question_id', $questionId)
                        ->where('value', $value)
                        ->value('id');
                }

                DB::table('project_answers')->insert([
                    'project_id' => $project->id,
                    'question_id' => $questionId,
                    'option_id' => $optionId,
                    'answer_value' => is_array($value)
                        ? json_encode($value, JSON_UNESCAPED_UNICODE)
                        : (string) $value,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            $project->update(['status' => 'submitted']);
        });

        return redirect()->route('projects.summary', $project);
    }

    /**
     * Wizard: cevapları özetle.
     */
    public function summary(Project $project): \Illuminate\View\View
    {
        $project->load(['answers.question', 'answers.option']);

        return view('projects.summary', compact('project'));
    }

    /**
     * Wizard: ön maliyet hesapla.
     */
    public function calculate(Project $project)
    {
        $project->load('answers.question');

        $params = $this->extractParametersFromAnswers($project);

        $engine = app(KaintaCostEngine::class);
        $costData = $engine->calculate(
            $params['project_type'] ?? 'komple',
            (float) ($params['area'] ?? 100),
            $params['quality'] ?? 'standart',
            $params['district'] ?? 'Genel'
        );

        $formatted = [
            'total' => (float) ($costData['hesaplanan_maliyet_tl'] ?? 0),
            'm2_cost' => (float) ($costData['unit_price'] ?? 0),
            'items' => collect($costData['ana_kalemler'] ?? [])
                ->mapWithKeys(fn ($v, $k) => [$k => (float) $v])
                ->toArray(),
            'ai_advice' => $costData['ai_tavsiye'] ?? null,
        ];

        $project->update([
            'estimated_cost' => $formatted['total'],
            'status' => 'calculated',
        ]);

        return view('projects.result', [
            'project' => $project,
            'costData' => $formatted,
        ]);
    }

    /**
     * create.blade.php'deki her iki formu da işler.
     */
    public function store(Request $request)
    {
        $isRenovation = (bool) $request->input('is_renovation', false);

        if ($isRenovation) {
            $validated = $request->validate([
                'property_type' => ['required', 'string', 'max:100'],
                'renovation_area' => ['required', 'numeric', 'min:1', 'max:100000'],
                'renovation_city' => ['required', 'string', 'max:50'],
                'renovation_scope' => ['required', 'in:hafif,orta,komple'],
                'renovation_quality' => ['required', 'in:temel,orta,premium'],
                'rooms' => ['nullable', 'array'],
                'rooms.*' => ['string', 'max:50'],
            ]);

            $projectType = DB::table('project_types')
                ->where('slug', 'tadilat')
                ->orWhere('slug', 'tadilat-renovasyon')
                ->first();

            if (! $projectType) {
                $projectType = DB::table('project_types')
                    ->where('name', 'like', '%Tadilat%')
                    ->first();
            }

            $project = Project::create([
                'project_type_id' => $projectType->id ?? 1,
                'name' => $validated['property_type'] . ' Tadilatı',
                'city' => $validated['renovation_city'],
                'construction_area' => $validated['renovation_area'],
                'status' => 'calculated',
                'notes' => 'Kapsam: ' . $validated['renovation_scope']
                    . ' | Kalite: ' . $validated['renovation_quality']
                    . ' | Alanlar: ' . implode(', ', $validated['rooms'] ?? []),
            ]);

            return redirect()->route('projects.calculate', $project);

        } else {
            $validated = $request->validate([
                'name' => ['required', 'string', 'max:200'],
                'city' => ['required', 'string', 'max:50'],
                'district' => ['required', 'string', 'max:50'],
                'project_type_id' => ['required', 'integer', 'exists:project_types,id'],
                'construction_area' => ['required', 'numeric', 'min:1', 'max:10000000'],
                'land_area' => ['nullable', 'numeric', 'min:0'],
                'floor_area' => ['nullable', 'numeric', 'min:0'],
                'floors' => ['nullable', 'integer', 'min:1', 'max:100'],
                'basement' => ['nullable', 'integer', 'min:0', 'max:10'],
                'quality' => ['required', 'in:ekonomi,standart,premium'],
                'notes' => ['nullable', 'string', 'max:3000'],
                'file_bina' => ['nullable', 'file', 'max:10240'],
                'file_kat' => ['nullable', 'file', 'max:10240'],
                'file_kolon' => ['nullable', 'file', 'max:10240'],
                'file_doseme' => ['nullable', 'file', 'max:10240'],
                'file_diger' => ['nullable', 'file', 'max:10240'],
            ]);

            $project = Project::create([
                'project_type_id' => $validated['project_type_id'],
                'name' => $validated['name'],
                'city' => $validated['city'],
                'district' => $validated['district'],
                'land_area' => $validated['land_area'] ?? null,
                'construction_area' => $validated['construction_area'],
                'status' => 'calculated',
                'notes' => trim(
                    ($validated['notes'] ?? '')
                    . "\nKalite: " . $validated['quality']
                    . " | Kat: " . ($validated['floors'] ?? '-')
                    . " | Bodrum: " . ($validated['basement'] ?? 0)
                ),
            ]);

            return redirect()->route('projects.calculate', $project);
        }
    }

    /**
     * Wizard cevaplarından KaintaCostEngine parametrelerini çıkar.
     */
    private function extractParametersFromAnswers(Project $project): array
    {
        $params = [
            'project_type' => 'komple',
            'quality' => 'standart',
            'district' => $project->district ?? 'Genel',
            'area' => $project->construction_area ?? 100,
        ];

        foreach ($project->answers as $answer) {
            $slug = $answer->question->slug ?? null;
            if (! $slug) continue;

            $value = $answer->option->value ?? $answer->answer_value;

            match ($slug) {
                'insaat-alani' => $params['area'] = (float) $value,
                'kalite-seviyesi' => $params['quality'] = (string) $value,
                'yapi-sistemi' => $params['project_type'] = (string) $value,
                default => null,
            };
        }

        return $params;
    }
public function guestRender(Request $request, AiRenderService $renderer)
    {
        try {
            if (\Illuminate\Support\Facades\RateLimiter::tooManyAttempts('ai-render:'.$request->ip(), 2)) {
                return response()->json(['success' => false, 'message' => 'Lütfen yeni görsel için biraz bekleyin.'], 429);
            }
            \Illuminate\Support\Facades\RateLimiter::hit('ai-render:'.$request->ip(), 60);

            // Frontend'den gelen JSON komutlarını dizi olarak alıyoruz
                        // Frontend'den gelen veriyi validate et (XSS ve injection koruması)
            $projectData = $request->validate([
                'room_type' => ['nullable', 'string', 'max:50'],
                'style' => ['nullable', 'string', 'max:50'],
                'w' => ['nullable', 'numeric', 'min:1', 'max:100'],
                'l' => ['nullable', 'numeric', 'min:1', 'max:100'],
                'colors' => ['nullable', 'string', 'max:200'],
                'materials' => ['nullable', 'string', 'max:200'],
                'notes' => ['nullable', 'string', 'max:500'],
            ]);

            $imageUrl = null;
            if (method_exists($renderer, 'generateGuestConceptRender')) {
                $imageUrl = $renderer->generateGuestConceptRender($projectData);
            }

            if ($imageUrl) {
                return response()->json(['success' => true, 'image_url' => $imageUrl]);
            }

            throw new \Exception("Render API geçerli bir görsel döndürmedi.");

        } catch (\Throwable $e) {
            Log::error('Guest AI Render Hatası: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Görsel üretilemedi.'], 500);
        }
    }

    public function generateRender(Project $project, AiRenderService $renderer)
    {
        try {
            $answers = DB::table('project_answers')
                ->join('project_questions', 'project_answers.question_id', '=', 'project_questions.id')
                ->where('project_answers.project_id', $project->id)
                ->select('project_answers.answer_value', 'project_questions.slug')
                ->get()->keyBy('slug');

            $renderImage = null;
            if (method_exists($renderer, 'generateAndSaveRender')) {
                $renderImage = $renderer->generateAndSaveRender($project->id, $answers);
            }

            return response()->json(['success' => true, 'image' => $renderImage]);
        } catch (\Throwable $e) {
            Log::error('Eski AJAX Render Hatasi: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Görsel üretilemedi.']);
        }
    }

    public function lead(\App\Models\Project $project): \Illuminate\View\View
    {
        return view('projects.lead', [
            'project' => $project,
        ]);
    }

    public function saveLead(
        Request $request,
        \App\Models\Project $project,
        \App\Services\Leads\LeadCaptureService $captureService,
        \App\Services\Leads\SalesLeadNotifier $salesLeadNotifier
    ) {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'phone' => ['required', 'string', 'min:10', 'max:30'],
            'email' => ['nullable', 'email', 'max:150'],
            'notes' => ['nullable', 'string', 'max:3000'],
            'contact_consent' => ['required', 'accepted'],
            'marketing_consent' => ['nullable', 'boolean'],
            'consent_version' => ['required', 'string', 'max:50'],
            'utm_source' => ['nullable', 'string', 'max:150'],
            'utm_medium' => ['nullable', 'string', 'max:150'],
            'utm_campaign' => ['nullable', 'string', 'max:150'],
            'utm_content' => ['nullable', 'string', 'max:150'],
            'utm_term' => ['nullable', 'string', 'max:150'],
            'landing_page' => ['nullable', 'string', 'max:2000'],
            'referrer' => ['nullable', 'string', 'max:2000'],
        ]);

        $validated['source'] = 'worksitem_project_wizard';
        $validated['project_type'] = 'Proje Sihirbazı';
        $validated['project_data'] = [
            'project_id' => $project->getKey(),
            'project_reference' => (string) $project->getRouteKey(),
        ];

        $validated['notes'] = trim(
            "Worksitem proje sihirbazı başvurusu.\n".
            "Proje ID: ".$project->getKey()."\n".
            ($validated['notes'] ?? '')
        );

        $result = $captureService->capture(
            input: $validated,
            ip: $request->ip(),
            landingPage: $request->fullUrl(),
            referrer: $request->headers->get('referer')
        );

        $lead = $result['lead'];

        if (! $result['duplicate'] && $lead->sales_ready) {
            try {
                $salesLeadNotifier->notify($lead);
            } catch (\Throwable $exception) {
                report($exception);
            }
        }

        return redirect()
            ->route('lead.thanks')
            ->with('status', $lead->lead_quality)
            ->with('lead_public_id', $lead->public_id)
            ->with('google_ads_conversion', ! $result['duplicate']);
    }
}
