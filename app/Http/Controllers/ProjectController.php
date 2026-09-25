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
