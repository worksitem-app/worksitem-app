<?php

namespace App\Http\Controllers\Market;

use App\Estimators\KitRegistry;
use App\Http\Controllers\Controller;
use App\Models\JobOffer;
use App\Models\JobRequest;
use App\Services\Market\Marketplace;
use App\Services\Sms\NetgsmSms;
use App\Services\Sms\PhoneOtp;
use App\Support\LeadTextGuard;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

/**
 * Kullanıcı tarafı: "Kayıtlı firmalardan teklif al".
 * Ad soyad, il/ilçe ve SMS ile doğrulanmış telefon zorunlu; telefon yalnız kabul edilen ve eşleşme bedelini ödeyen firmayla paylaşılır.
 */
class RequestController extends Controller
{
    public function create(Request $request): View|RedirectResponse
    {
        abort_unless(config('worksitem_market.enabled'), 404);
        $tool = (string) $request->query('arac', '');
        $map = config("worksitem_market.tools.$tool");
        $est = $this->estimate($request, $tool);

        return view('market.request-create', [
            'tool' => $map ? $tool : null,
            'toolTitle' => $map ? (string) config("worksitem_tools.tools.$tool.title", '') : null,
            'trade' => old('trade', $map[0] ?? ''),
            'subservice' => $map[1] ?? null,
            'est' => $est,
            'trades' => (array) config('worksitem_trades'),
            'cities' => (array) config('worksitem_firms.cities'),
            'startPrefs' => (array) config('worksitem_market.start_prefs'),
            'feeRate' => (float) config('worksitem_market.fee_rate'),
        ]);
    }

    public function sendCode(Request $request, PhoneOtp $otp): JsonResponse
    {
        $request->validate(['phone' => ['required', 'string', 'max:30']]);
        if (! NetgsmSms::normalize((string) $request->input('phone'))) {
            return response()->json(['ok' => false, 'message' => 'Lütfen 05XX XXX XX XX biçiminde bir cep telefonu yazın.'], 422);
        }
        $r = $otp->send((string) $request->input('phone'), 'teklif');

        return response()->json($r, $r['ok'] ? 200 : 429);
    }

    public function store(Request $request, PhoneOtp $otp, Marketplace $market): RedirectResponse
    {
        abort_unless(config('worksitem_market.enabled'), 404);
        $data = $request->validate([
            'tool' => ['nullable', 'string', 'max:80'],
            'trade' => ['required', Rule::in(array_keys((array) config('worksitem_trades')))],
            'subservice' => ['nullable', 'string', 'max:150'],
            'title' => ['required', 'string', 'min:5', 'max:200'],
            'summary' => ['nullable', 'string', 'max:300'],
            'est_low' => ['nullable', 'integer', 'min:0', 'max:500000000'],
            'est_high' => ['nullable', 'integer', 'min:0', 'max:500000000'],
            'city' => ['required', Rule::in((array) config('worksitem_firms.cities'))],
            'district' => ['required', 'string', 'min:2', 'max:60'],
            'description' => ['nullable', 'string', 'max:2000'],
            'start_pref' => ['required', Rule::in(array_keys((array) config('worksitem_market.start_prefs')))],
            'name' => ['required', 'string', 'max:120'],
            'phone' => ['required', 'string', 'max:30'],
            'code' => ['required', 'digits:6'],
            'email' => ['nullable', 'email', 'max:150'],
            'terms' => ['accepted'],
            'kvkk' => ['accepted'],
        ], [
            'trade.required' => 'İş kolunu seçin.',
            'title.required' => 'İşi kısaca tanımlayın (ör. "Banyo komple yenileme, 5 m²").',
            'city.required' => 'İli seçin.',
            'district.required' => 'İlçeyi yazın.',
            'name.required' => 'Adınızı ve soyadınızı yazın.',
            'phone.required' => 'Cep telefonunuzu yazın.',
            'code.required' => 'Telefonunuza gelen 6 haneli kodu yazın.',
            'code.digits' => 'Kod 6 haneli olmalıdır.',
            'terms.accepted' => 'Devam etmek için teklif alma koşullarını onaylayın.',
            'kvkk.accepted' => 'Devam etmek için aydınlatma metnini okuduğunuzu onaylayın.',
        ]);
        if (! LeadTextGuard::isValidFullName($data['name'])) {
            throw ValidationException::withMessages(['name' => 'Lütfen gerçek adınızı ve soyadınızı yazın.']);
        }
        if (! $otp->verify($data['phone'], $data['code'], 'teklif')) {
            throw ValidationException::withMessages(['code' => 'Kod hatalı veya süresi doldu. Yeni kod isteyebilirsiniz.']);
        }
        $phone = (string) NetgsmSms::normalize($data['phone']);
        $open = JobRequest::query()->where('phone', $phone)->where('status', 'open')->where('created_at', '>', now()->subDay())->count();
        if ($open >= 3) {
            throw ValidationException::withMessages(['phone' => 'Son 24 saatte en fazla 3 açık teklif talebi oluşturabilirsiniz.']);
        }

        // Kit araçlarında tutar sunucuda yeniden hesaplanır; diğerlerinde kullanıcının sayfadaki sonucu kullanılır
        $low = $data['est_low'] ?? null;
        $high = $data['est_high'] ?? null;
        if ($low && $high && $low > $high) {
            [$low, $high] = [$high, $low];
        }

        $job = JobRequest::query()->create([
            'tool_slug' => config("worksitem_market.tools.{$data['tool']}") ? $data['tool'] : null,
            'trade' => $data['trade'], 'subservice' => $data['subservice'] ?? null,
            'title' => trim(strip_tags($data['title'])), 'summary' => isset($data['summary']) ? trim(strip_tags($data['summary'])) : null,
            'est_low' => $low ?: null, 'est_high' => $high ?: null,
            'city' => $data['city'], 'district' => trim($data['district']),
            'description' => isset($data['description']) ? trim(strip_tags($data['description'])) : null,
            'start_pref' => $data['start_pref'], 'name' => trim($data['name']), 'phone' => $phone, 'email' => $data['email'] ?? null,
            'terms_at' => now(),
        ]);
        $invited = $market->invite($job);
        try {
            app(NetgsmSms::class)->send($phone, "Worksitem: Teklif talebiniz alındı ({$job->public_id}). "
                .($invited > 0 ? "$invited firmaya iletildi. " : 'Bölgenize uygun firmayı Worksitem ekibi bulacak. ')
                .'Teklifler: '.$job->customerUrl());
        } catch (\Throwable) {
        }
        $request->session()->put('job_token.'.$job->public_id, $job->token);

        return redirect()->to($job->customerUrl())->with('job_created', $invited);
    }

    public function show(Request $request, JobRequest $job): View
    {
        $this->authorizeToken($request, $job);
        $offers = $job->offers()->with('firm')->whereIn('status', ['submitted', 'accepted', 'matched', 'expired'])
            ->orderByRaw("CASE status WHEN 'matched' THEN 0 WHEN 'accepted' THEN 1 WHEN 'submitted' THEN 2 ELSE 3 END")->orderBy('amount')->get();

        return view('market.request-show', [
            'job' => $job, 'offers' => $offers, 't' => $job->token,
            'pending' => $offers->firstWhere('status', 'accepted'),
            'matched' => $offers->firstWhere('status', 'matched'),
        ]);
    }

    public function accept(Request $request, JobRequest $job, JobOffer $offer, Marketplace $market): RedirectResponse
    {
        $this->authorizeToken($request, $job);
        $request->validate(['share' => ['accepted']], ['share.accepted' => 'Telefon numaranızın bu firmayla paylaşılmasını onaylamanız gerekiyor.']);
        abort_unless($offer->job_request_id === $job->id, 404);
        if (! $job->isOpen() || $offer->status !== 'submitted') {
            return back()->withErrors(['offer' => 'Bu teklif artık kabul edilemiyor.']);
        }
        if ($job->offers()->where('status', 'accepted')->exists()) {
            return back()->withErrors(['offer' => 'Kabul ettiğiniz bir teklif firmanın onayını bekliyor. Süre dolarsa başka teklif seçebilirsiniz.']);
        }
        $market->accept($job, $offer);

        return redirect()->to($job->customerUrl())->with('job_ok', 'Teklifi kabul ettiniz. Firma eşleşmeyi onaylayınca telefon numaranız firmayla paylaşılacak ve firma sizi arayacak.');
    }

    public function close(Request $request, JobRequest $job): RedirectResponse
    {
        $this->authorizeToken($request, $job);
        if ($job->status === 'open') {
            $job->update(['status' => 'closed']);
            $job->offers()->whereIn('status', ['submitted', 'accepted'])->update(['status' => 'not_selected']);
        }

        return redirect()->to($job->customerUrl())->with('job_ok', 'Talebiniz kapatıldı. Firmalara yeni bildirim gitmeyecek.');
    }

    private function authorizeToken(Request $request, JobRequest $job): void
    {
        $t = (string) $request->input('t', $request->session()->get('job_token.'.$job->public_id, ''));
        abort_unless($t !== '' && hash_equals($job->token, $t), 404);
        $request->session()->put('job_token.'.$job->public_id, $job->token);
    }

    /** @return array{low:?int, high:?int, summary:?string} */
    private function estimate(Request $request, string $tool): array
    {
        if (in_array($tool, KitRegistry::slugs(), true)) {
            try {
                $e = KitRegistry::get($tool);
                $r = $e->estimate($e->normalize($request->query()));
                if (! empty($r['ready'])) {
                    return ['low' => (int) $r['low'], 'high' => (int) $r['high'], 'summary' => mb_substr((string) $r['summary'], 0, 300)];
                }
            } catch (\Throwable) {
            }
        }
        $low = (int) $request->query('min', 0);
        $high = (int) $request->query('max', 0);

        return [
            'low' => $low > 0 && $low < 500000000 ? $low : null,
            'high' => $high > 0 && $high < 500000000 ? $high : null,
            'summary' => mb_substr(strip_tags((string) $request->query('ozet', '')), 0, 300) ?: null,
        ];
    }
}
