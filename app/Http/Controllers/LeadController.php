<?php

namespace App\Http\Controllers;

use App\Services\Leads\LeadCaptureService;
use App\Services\Leads\SalesLeadNotifier;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\View\View;

class LeadController extends Controller
{
    public function store(
        Request $request,
        LeadCaptureService $captureService,
        SalesLeadNotifier $salesLeadNotifier
    ): RedirectResponse|JsonResponse {
        $request->merge([
            'name' => $request->input(
                'name',
                $request->input('full_name')
            ),
        ]);

        $key = 'lead-form:' . sha1((string) $request->ip());

        if (RateLimiter::tooManyAttempts($key, 3)) {
            $seconds = RateLimiter::availableIn($key);

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Çok fazla talep gönderdiniz.',
                    'retry_after' => $seconds,
                ], 429);
            }

            return back()
                ->withErrors([
                    'message' => 'Çok fazla talep gönderdiniz. Lütfen daha sonra tekrar deneyin.',
                ])
                ->withInput();
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'phone' => ['required', 'string', 'min:10', 'max:30'],
            'email' => ['nullable', 'email', 'max:150'],
            'source' => ['nullable', 'string', 'max:100'],
            'source_tool' => ['nullable', 'string', 'max:150'],
            'calculated_service' => ['nullable', 'string', 'max:150'],
            'calculated_area' => ['nullable', 'string', 'max:100'],
            'project_type' => ['nullable', 'string', 'max:255'],
            'location' => ['nullable', 'string', 'max:150'],
            'city' => ['nullable', 'string', 'max:100'],
            'district' => ['nullable', 'string', 'max:150'],
            'timeline' => ['nullable', 'string', 'max:30'],
            'notes' => ['nullable', 'string', 'max:3000'],
            'project_data' => ['nullable'],
            'estimated_cost' => ['nullable', 'numeric', 'min:0'],
            'estimated_min' => ['nullable', 'numeric', 'min:0'],
            'estimated_max' => ['nullable', 'numeric', 'min:0'],
            'budget_confirmed' => ['nullable', 'boolean'],
            'explicit_request' => ['nullable', 'boolean'],
            'has_file' => ['nullable', 'boolean'],
            'is_decision_maker' => ['nullable', 'boolean'],
            'contact_consent' => ['required', 'accepted'],
            'marketing_consent' => ['nullable', 'boolean'],
            'consent_version' => ['nullable', 'string', 'max:50'],
            'utm_source' => ['nullable', 'string', 'max:150'],
            'utm_medium' => ['nullable', 'string', 'max:150'],
            'utm_campaign' => ['nullable', 'string', 'max:150'],
            'utm_content' => ['nullable', 'string', 'max:150'],
            'utm_term' => ['nullable', 'string', 'max:150'],
            'landing_page' => ['nullable', 'string', 'max:2000'],
            'referrer' => ['nullable', 'string', 'max:2000'],
            'price_version' => ['nullable', 'string', 'max:100'],
        ]);

        /*
         * lead_quality doğrulanmış veriye bilerek alınmaz.
         * Tarayıcıdan gelen kalite yerine sunucu puanlaması kullanılır.
         */
        $result = $captureService->capture(
            input: $validated,
            ip: $request->ip(),
            landingPage: $request->fullUrl(),
            referrer: $request->headers->get('referer')
        );

        RateLimiter::hit($key, 600);

        $lead = $result['lead'];

        if (!$result['duplicate'] && $lead->sales_ready) {
            try {
                $salesLeadNotifier->notify($lead);
            } catch (\Throwable $exception) {
                /*
                 * Bildirim hatası müşterinin form gönderimini engellemez.
                 */
                Log::error('Sales lead notification failed', [
                    'lead_id' => $lead->id,
                    'error' => $exception->getMessage(),
                ]);
            }
        }

        Log::info('Worksitem lead captured', [
            'lead_id' => $lead->id,
            'public_id' => $lead->public_id,
            'quality' => $lead->lead_quality,
            'score' => $lead->lead_score,
            'sales_ready' => $lead->sales_ready,
            'duplicate' => $result['duplicate'],
            'source' => $lead->source,
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Talebiniz alınmıştır.',
                'public_id' => $lead->public_id,
                'quality' => $lead->lead_quality,
                'sales_ready' => $lead->sales_ready,
                'duplicate' => $result['duplicate'],
            ], $result['duplicate'] ? 200 : 201);
        }

        return redirect()
            ->route('lead.thanks')
            ->with('status', $lead->lead_quality)
            ->with('lead_public_id', $lead->public_id)
            ->with('google_ads_conversion', ! $result['duplicate']);
    }

    public function thanks(Request $request): View
    {
        return view('thanks', [
            'status' => session('status', 'qualified'),
        ]);
    }
}
