<?php

namespace App\Http\Controllers\Kit;

use App\Estimators\KitEstimator;
use App\Estimators\KitRegistry;
use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\Sponsor;
use App\Services\FxRates;
use App\Services\Leads\NewLeadEmailNotifier;
use App\Services\Sms\NetgsmSms;
use App\Services\Sms\PhoneOtp;
use App\Support\LeadTextGuard;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;

/**
 * Hesap motoru araçları için Pro rapor (banyo pilotunun genel hâli):
 * SMS koduyla doğrulanan telefona ücretsiz, çok sayfalı PDF; doğrulanan kişi talep (lead) olarak kaydedilir.
 */
class KitProReportController extends Controller
{
    private function estimator(Request $request): KitEstimator
    {
        return KitRegistry::get((string) $request->route('kit'));
    }

    private function input(Request $request, KitEstimator $e): ?array
    {
        $raw = $request->isMethod('GET') ? $request->query() : $request->except(['_token', 'name', 'phone', 'code', 'kvkk', 'call_ok']);
        $in = $e->normalize($raw);

        return $e->missing($in) || ! ($e->estimate($in)['ready'] ?? false) ? null : $in;
    }

    public function show(Request $request)
    {
        $e = $this->estimator($request);
        $in = $this->input($request, $e);
        if (! $in) {
            return redirect('/araclar/'.$e->slug())->withErrors(['kit' => 'Pro rapor için önce ölçüleri girip seçimleri yapın.']);
        }

        return view('reports.kit-pro', [
            'e' => $e,
            'page' => $e->page(),
            'est' => $e->estimate($in),
            'query' => $request->query(),
            'verified' => $this->verifiedPhone($request),
        ]);
    }

    public function sendCode(Request $request, PhoneOtp $otp): JsonResponse
    {
        $request->validate(['phone' => ['required', 'string', 'max:30']]);
        if (! NetgsmSms::normalize((string) $request->input('phone'))) {
            return response()->json(['ok' => false, 'message' => 'Lütfen 05XX XXX XX XX biçiminde bir cep telefonu yazın.'], 422);
        }
        $r = $otp->send((string) $request->input('phone'), 'pro_rapor');

        return response()->json($r, $r['ok'] ? 200 : 429);
    }

    public function download(Request $request, PhoneOtp $otp, FxRates $fx)
    {
        $e = $this->estimator($request);
        $in = $this->input($request, $e);
        if (! $in) {
            return redirect('/araclar/'.$e->slug());
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
            // Banyo Pro raporuyla aynı oturum anahtarı: bir kez doğrulayan diğer raporları da indirir
            $request->session()->put('pro_report', ['phone' => $phone, 'name' => $data['name'], 'until' => now()->addMinutes((int) config('worksitem_pro_report.session_minutes', 120))->timestamp]);
            $this->recordLead($request, $e, $data['name'], $phone, $in);
        }

        // Ücretli mod: bu hesap için ödeme yoksa iyzico ödeme sayfasına gider; ödeme sonrası aynı sayfaya dönülür
        if (config('worksitem_pro_report.mode') === 'paid') {
            $ref = 'pro:'.$e->slug().':'.md5(json_encode($in));
            if (! \App\Services\Payments\OnlinePayment::reportPaid($ref, $phone)) {
                $pay = app(\App\Services\Payments\OnlinePayment::class);
                if (! $pay->enabled()) {
                    return back()->withErrors(['pay' => 'Pro rapor satışı şu an kapalı. Lütfen daha sonra tekrar deneyin.']);
                }
                $query = http_build_query($request->except(['_token', 'name', 'phone', 'code', 'kvkk', 'call_ok']));
                $name = (string) ($request->input('name') ?: ($request->session()->get('pro_report')['name'] ?? 'Worksitem Müşterisi'));
                $order = $pay->reportOrder($ref, 'Worksitem Pro rapor · '.($e->page()['title'] ?? $e->slug()), $name, $phone, url('/araclar/'.$e->slug().'/pro-rapor').($query !== '' ? '?'.$query : ''));
                $r = $pay->start($order, $request->ip());

                return $r['ok'] ? redirect()->away($r['url']) : back()->withErrors(['pay' => $r['error']]);
            }
        }

        $content = $e->proReport($in);
        $sponsors = Schema::hasTable('sponsors')
            ? Sponsor::applyExclusive(Sponsor::query()->live()->where('tool_slug', $e->slug())->orderBy('priority')->get())->take(4)->values()
            : collect();
        $logo = public_path('brand/worksitem-logo.png');
        $equivalents = \App\Services\Sponsors\SponsorLines::forMaterials($e->slug(), (array) ($content['materials'] ?? []));

        $pdf = Pdf::loadView('reports.kit-pro-pdf', $content + [
            'equivalents' => $equivalents,
            'issuedAt' => now('Europe/Istanbul'),
            'e' => $e,
            'page' => $e->page(),
            'fx' => $fx->forReport(),
            'sponsors' => $sponsors,
            'logo' => is_file($logo) ? 'data:image/png;base64,'.base64_encode((string) file_get_contents($logo)) : null,
            'pricesLabel' => $e->pricesLabel(),
            'reportNo' => 'WS-PRO-'.now()->format('ymd').'-'.strtoupper(substr(md5($phone.json_encode($in)), 0, 5)),
        ])->setPaper('a4')->setOption(['isFontSubsettingEnabled' => true, 'isRemoteEnabled' => false]);

        return $pdf->download('worksitem-'.$e->key().'-pro-rapor-'.now()->format('Y-m-d').'.pdf');
    }

    private function verifiedPhone(Request $request): ?string
    {
        $s = $request->session()->get('pro_report');

        return is_array($s) && ($s['until'] ?? 0) > time() ? (string) $s['phone'] : null;
    }

    private function recordLead(Request $request, KitEstimator $e, string $name, string $phone, array $in): void
    {
        $source = $e->key().'_pro_rapor';
        $callOk = $request->boolean('call_ok');
        if (Lead::query()->where('phone_normalized', '90'.$phone)->where('source', $source)->where('created_at', '>=', now()->subDay())->exists()) {
            return;
        }
        $est = $e->estimate($in);
        $lead = Lead::query()->create([
            'name' => $name,
            'phone' => '0'.$phone,
            'source' => $source,
            'project_type' => $e->page()['service'].' (Pro rapor indirdi)',
            'estimated_min' => $est['low'],
            'estimated_max' => $est['high'],
            'estimated_cost' => $est['mid'],
            'notes' => 'Pro rapor · telefon SMS ile doğrulandı · '.$est['summary'].' · Arama izni: '.($callOk ? 'EVET' : 'HAYIR (aramayın)'),
            'contact_consent' => $callOk,
            'consent_at' => $callOk ? now() : null,
            'status' => 'yeni',
            'landing_page' => '/araclar/'.$e->slug(),
        ]);
        if ($callOk) {
            try {
                app(NewLeadEmailNotifier::class)->notify($lead);
            } catch (\Throwable $ex) {
                Log::error('Pro rapor lead e-postası', ['error' => $ex->getMessage()]);
            }
        }
    }
}
