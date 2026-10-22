<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\Sponsor;
use App\Services\BathroomEstimator;
use App\Services\BathroomProReport;
use App\Services\FxRates;
use App\Services\Leads\NewLeadEmailNotifier;
use App\Services\Sms\NetgsmSms;
use App\Services\Sms\PhoneOtp;
use App\Support\LeadTextGuard;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

/**
 * Banyo Pro rapor: telefon SMS koduyla doğrulanan kullanıcıya detaylı PDF.
 * Doğrulanmış telefon + isim bir talep (lead) olarak kaydedilir; arama izni ayrı ve isteğe bağlıdır.
 */
class BathroomProReportController extends Controller
{
    public function show(Request $request, BathroomEstimator $estimator)
    {
        $cfg = $estimator->config();
        $input = BathroomReportController::input($request, $cfg);
        if (! $input) {
            return redirect('/araclar/banyo-maliyet-hesaplayici')
                ->withErrors(['width' => 'Pro rapor için önce banyonun en ve boy ölçülerini girin.']);
        }

        return view('reports.banyo-pro', [
            'est' => $estimator->estimate($input),
            'input' => $input,
            'cfg' => $cfg,
            'query' => $request->query(),
            'verified' => $this->verifiedPhone($request),
        ]);
    }

    public function sendCode(Request $request, PhoneOtp $otp): JsonResponse
    {
        $request->validate(['phone' => ['required', 'string', 'max:30']]);
        if (! NetgsmSms::normalize($request->input('phone'))) {
            return response()->json(['ok' => false, 'message' => 'Lütfen 05XX XXX XX XX biçiminde bir cep telefonu yazın.'], 422);
        }
        $r = $otp->send((string) $request->input('phone'), 'pro_rapor');

        return response()->json($r, $r['ok'] ? 200 : 429);
    }

    public function download(Request $request, BathroomEstimator $estimator, BathroomProReport $report, PhoneOtp $otp, FxRates $fx)
    {
        $cfg = $estimator->config();
        $input = BathroomReportController::input($request, $cfg);
        if (! $input) {
            return redirect('/araclar/banyo-maliyet-hesaplayici');
        }

        $phone = $this->verifiedPhone($request);
        if (! $phone) {
            $data = $request->validate([
                'name' => ['required', 'string', 'max:120'],
                'phone' => ['required', 'string', 'max:30'],
                'code' => ['required', 'digits:6'],
                'kvkk' => ['accepted'],
            ], [
                'name.required' => 'Adınızı ve soyadınızı yazın.',
                'phone.required' => 'Cep telefonunuzu yazın.',
                'code.required' => 'Telefonunuza gelen 6 haneli kodu yazın.',
                'code.digits' => 'Kod 6 haneli olmalıdır.',
                'kvkk.accepted' => 'Devam etmek için aydınlatma metnini okuduğunuzu onaylayın.',
            ]);
            if (! LeadTextGuard::isValidFullName($data['name'])) {
                throw ValidationException::withMessages(['name' => 'Lütfen gerçek adınızı ve soyadınızı yazın.']);
            }
            if (! $otp->verify($data['phone'], $data['code'], 'pro_rapor')) {
                throw ValidationException::withMessages(['code' => 'Kod hatalı veya süresi doldu. Yeni kod isteyebilirsiniz.']);
            }
            $phone = (string) NetgsmSms::normalize($data['phone']);
            $request->session()->put('pro_report', ['phone' => $phone, 'name' => $data['name'], 'until' => now()->addMinutes((int) config('worksitem_pro_report.session_minutes', 120))->timestamp]);
            $this->recordLead($request, $data['name'], $phone, $estimator->estimate($input), $input);
        }

        // Ücretli mod: bu hesap için ödeme yoksa iyzico ödeme sayfasına gider; ödeme sonrası aynı sayfaya dönülür
        if (config('worksitem_pro_report.mode') === 'paid') {
            $ref = 'pro:'.'banyo-maliyet-hesaplayici'.':'.md5(json_encode($input));
            if (! \App\Services\Payments\OnlinePayment::reportPaid($ref, $phone)) {
                $pay = app(\App\Services\Payments\OnlinePayment::class);
                if (! $pay->enabled()) {
                    return back()->withErrors(['pay' => 'Pro rapor satışı şu an kapalı. Lütfen daha sonra tekrar deneyin.']);
                }
                $query = http_build_query($request->except(['_token', 'name', 'phone', 'code', 'kvkk', 'call_ok']));
                $name = (string) ($request->input('name') ?: ($request->session()->get('pro_report')['name'] ?? 'Worksitem Müşterisi'));
                $order = $pay->reportOrder($ref, 'Worksitem Pro rapor · Banyo tadilat maliyeti', $name, $phone, route('tools.banyo_pro_report').($query !== '' ? '?'.$query : ''));
                $r = $pay->start($order, $request->ip());

                return $r['ok'] ? redirect()->away($r['url']) : back()->withErrors(['pay' => $r['error']]);
            }
        }

        $content = $report->build($input);
        $sponsors = \Illuminate\Support\Facades\Schema::hasTable('sponsors')
            ? Sponsor::applyExclusive(Sponsor::query()->live()->where('tool_slug', 'banyo-maliyet-hesaplayici')->orderBy('priority')->get())->take(4)->values()
            : collect();
        $logo = public_path('brand/worksitem-logo.png');

        $pdf = Pdf::loadView('reports.banyo-pro-pdf', $content + [
            'input' => $input,
            'cfg' => $cfg,
            'type' => $cfg['types'][$input['type']] ?? $cfg['types']['standart'],
            'scope' => $cfg['scopes'][$input['scope']] ?? $cfg['scopes']['komple'],
            'fx' => $fx->current(),
            'sponsors' => $sponsors,
            'logo' => is_file($logo) ? 'data:image/png;base64,'.base64_encode((string) file_get_contents($logo)) : null,
            'pricesLabel' => $estimator->pricesLabel(),
            'reportNo' => 'WS-PRO-'.now()->format('ymd').'-'.strtoupper(substr(md5($phone.json_encode($input)), 0, 5)),
        ])->setPaper('a4')->setOption(['isFontSubsettingEnabled' => true, 'isRemoteEnabled' => false]);

        return $pdf->download('worksitem-banyo-pro-rapor-'.now()->format('Y-m-d').'.pdf');
    }

    private function verifiedPhone(Request $request): ?string
    {
        $s = $request->session()->get('pro_report');

        return is_array($s) && ($s['until'] ?? 0) > time() ? (string) $s['phone'] : null;
    }

    private function recordLead(Request $request, string $name, string $phone, array $est, array $input): void
    {
        $callOk = $request->boolean('call_ok');
        $existing = Lead::query()->where('phone_normalized', '90'.$phone)->where('source', 'banyo_pro_rapor')
            ->where('created_at', '>=', now()->subDay())->first();
        if ($existing) {
            return;
        }

        $lead = Lead::query()->create([
            'name' => $name,
            'phone' => '0'.$phone,
            'source' => 'banyo_pro_rapor',
            'project_type' => 'Banyo tadilatı (Pro rapor indirdi)',
            'estimated_min' => $est['low'],
            'estimated_max' => $est['high'],
            'estimated_cost' => $est['mid'],
            'area' => $est['floor'],
            'notes' => sprintf('Pro rapor · telefon SMS ile doğrulandı · %s×%s m · %s / %s · Arama izni: %s',
                $input['w'], $input['l'], $input['scope'], $input['quality'], $callOk ? 'EVET' : 'HAYIR (aramayın)'),
            'contact_consent' => $callOk,
            'consent_at' => $callOk ? now() : null,
            'status' => 'yeni',
            'landing_page' => '/araclar/banyo-maliyet-hesaplayici',
        ]);

        if ($callOk) {
            try {
                app(NewLeadEmailNotifier::class)->notify($lead);
            } catch (\Throwable $e) {
                Log::error('Pro rapor lead e-postası', ['error' => $e->getMessage()]);
            }
        }
    }
}
