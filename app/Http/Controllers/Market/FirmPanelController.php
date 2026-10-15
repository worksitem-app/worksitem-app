<?php

namespace App\Http\Controllers\Market;

use App\Http\Controllers\Controller;
use App\Models\Firm;
use App\Models\JobInvite;
use App\Models\JobOffer;
use App\Models\JobRequest;
use App\Services\Market\FirmQuestions;
use App\Services\Market\Marketplace;
use App\Services\Payments\OnlinePayment;
use App\Services\Sms\NetgsmSms;
use App\Services\Sms\PhoneOtp;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

/**
 * Firma paneli: SMS kodu ile giriş (şifre yok), davet edilen işler, teklif verme, eşleşme bedeli ödeme,
 * eşleşen işlerde müşteri iletişim bilgisi.
 */
class FirmPanelController extends Controller
{
    private const SESSION = 'firm_panel';

    public function login(Request $request): View|RedirectResponse
    {
        if ($this->firm($request)) {
            return redirect()->route('market.panel');
        }

        return view('market.panel-login');
    }

    public function sendCode(Request $request, PhoneOtp $otp): JsonResponse
    {
        $request->validate(['phone' => ['required', 'string', 'max:30']]);
        $no = NetgsmSms::normalize((string) $request->input('phone'));
        if (! $no) {
            return response()->json(['ok' => false, 'message' => 'Lütfen 05XX XXX XX XX biçiminde bir cep telefonu yazın.'], 422);
        }
        // Kayıtlı olmayan numaraya SMS harcanmaz; yanıt aynı olur (numara taraması yapılamasın)
        if (! Firm::query()->where('phone', $no)->where('status', 'approved')->exists()) {
            return response()->json(['ok' => true, 'message' => 'Numara onaylı bir firmaya kayıtlıysa doğrulama kodu gönderildi.']);
        }
        $r = $otp->send($no, 'firma_panel');

        return response()->json($r, $r['ok'] ? 200 : 429);
    }

    public function verify(Request $request, PhoneOtp $otp): RedirectResponse
    {
        $data = $request->validate(['phone' => ['required', 'string', 'max:30'], 'code' => ['required', 'digits:6']]);
        $no = NetgsmSms::normalize($data['phone']);
        $firm = $no ? Firm::query()->where('phone', $no)->where('status', 'approved')->first() : null;
        if (! $firm || ! $otp->verify($no, $data['code'], 'firma_panel')) {
            throw ValidationException::withMessages(['code' => 'Kod hatalı, süresi dolmuş veya numara onaylı bir firmaya kayıtlı değil.']);
        }
        $request->session()->regenerate();
        $request->session()->put(self::SESSION, ['id' => $firm->id, 'until' => now()->addDays((int) config('worksitem_market.panel_session_days', 30))->timestamp]);

        return redirect()->intended(route('market.panel'));
    }

    public function logout(Request $request): RedirectResponse
    {
        $request->session()->forget(self::SESSION);

        return redirect()->route('market.panel.login');
    }

    public function dashboard(Request $request): View|RedirectResponse
    {
        if (! $firm = $this->firm($request)) {
            return $this->toLogin($request);
        }
        $invites = JobInvite::query()->where('firm_id', $firm->id)->with('job')->latest()->limit(100)->get()->filter(fn ($i) => $i->job);
        $offers = JobOffer::query()->where('firm_id', $firm->id)->with('job')->latest()->limit(100)->get()->keyBy('job_request_id');

        return view('market.panel', ['firm' => $firm, 'invites' => $invites, 'offers' => $offers]);
    }

    public function toggle(Request $request): RedirectResponse
    {
        if (! $firm = $this->firm($request)) {
            return $this->toLogin($request);
        }
        $firm->update(['accepts_jobs' => ! $firm->accepts_jobs]);

        return back()->with('panel_ok', $firm->accepts_jobs ? 'Yeni iş davetleri açıldı.' : 'Yeni iş davetleri durduruldu.');
    }

    public function job(Request $request, JobRequest $job): View|RedirectResponse
    {
        if (! $firm = $this->firm($request)) {
            return $this->toLogin($request);
        }
        $invite = JobInvite::query()->where('job_request_id', $job->id)->where('firm_id', $firm->id)->first();
        abort_unless($invite, 404);
        if (! $invite->viewed_at) {
            $invite->update(['viewed_at' => now(), 'status' => $invite->status === 'sent' ? 'viewed' : $invite->status]);
        }
        $offer = JobOffer::query()->where('job_request_id', $job->id)->where('firm_id', $firm->id)->first();

        return view('market.panel-job', ['firm' => $firm, 'job' => $job, 'invite' => $invite, 'offer' => $offer,
            'competitors' => $job->offers()->whereIn('status', ['submitted', 'accepted'])->count()]);
    }

    public function offer(Request $request, JobRequest $job, Marketplace $market): RedirectResponse
    {
        if (! $firm = $this->firm($request)) {
            return $this->toLogin($request);
        }
        abort_unless(JobInvite::query()->where('job_request_id', $job->id)->where('firm_id', $firm->id)->exists(), 404);
        $existing = JobOffer::query()->where('job_request_id', $job->id)->where('firm_id', $firm->id)->first();
        if (! $job->isOpen() || ($existing && $existing->status !== 'submitted')) {
            return back()->withErrors(['amount' => 'Bu işe artık teklif verilemiyor veya teklifiniz değiştirilemez.']);
        }
        $data = $request->validate([
            'amount' => ['required', 'integer', 'min:100', 'max:500000000'],
            'duration_days' => ['nullable', 'integer', 'min:1', 'max:730'],
            'start_when' => ['nullable', 'string', 'max:40'],
            'includes_material' => ['nullable', 'boolean'],
            'note' => ['nullable', 'string', 'max:1500'],
            'rules' => ['accepted'],
        ], [
            'amount.required' => 'Teklif tutarını yazın (KDV dahil toplam).',
            'rules.accepted' => 'Teklif kurallarını onaylayın.',
        ]);
        $market->submitOffer($firm, $job, $data);

        return redirect()->route('market.panel.job', $job)->with('panel_ok', $existing ? 'Teklifiniz güncellendi.' : 'Teklifiniz müşteriye iletildi.');
    }

    public function decline(Request $request, JobRequest $job): RedirectResponse
    {
        if (! $firm = $this->firm($request)) {
            return $this->toLogin($request);
        }
        JobInvite::query()->where('job_request_id', $job->id)->where('firm_id', $firm->id)->whereIn('status', ['sent', 'viewed'])->update(['status' => 'declined']);

        return redirect()->route('market.panel')->with('panel_ok', 'İş listenizden kaldırıldı.');
    }

    public function withdraw(Request $request, JobRequest $job): RedirectResponse
    {
        if (! $firm = $this->firm($request)) {
            return $this->toLogin($request);
        }
        JobOffer::query()->where('job_request_id', $job->id)->where('firm_id', $firm->id)->where('status', 'submitted')->update(['status' => 'withdrawn']);

        return redirect()->route('market.panel.job', $job)->with('panel_ok', 'Teklifiniz geri çekildi.');
    }

    public function pay(Request $request, JobRequest $job, Marketplace $market, OnlinePayment $pay): RedirectResponse
    {
        if (! $firm = $this->firm($request)) {
            return $this->toLogin($request);
        }
        $offer = JobOffer::query()->where('job_request_id', $job->id)->where('firm_id', $firm->id)->where('status', 'accepted')->first();
        if (! $offer || ($offer->fee_due_at && $offer->fee_due_at->isPast()) || (float) $offer->fee_net <= 0) {
            return back()->withErrors(['pay' => 'Ödenecek bir eşleşme bedeli yok veya süresi doldu.']);
        }
        if (! $pay->enabled()) {
            return back()->withErrors(['pay' => 'Kartla ödeme şu an kapalı. Lütfen WhatsApp hattımıza yazın: '.config('worksitem_inspection.phone')]);
        }
        $order = $market->feeOrder($offer);
        $r = $pay->start($order, $request->ip());

        return $r['ok'] ? redirect()->away($r['url']) : back()->withErrors(['pay' => $r['error']]);
    }

    /** Ücretsiz dönemde kabul edilen işi onayla → eşleşme */
    public function confirm(Request $request, JobRequest $job, Marketplace $market): RedirectResponse
    {
        if (! $firm = $this->firm($request)) {
            return $this->toLogin($request);
        }
        $offer = JobOffer::query()->where('job_request_id', $job->id)->where('firm_id', $firm->id)->where('status', 'accepted')->first();
        if (! $offer || ! $market->confirmFree($offer)) {
            return back()->withErrors(['pay' => 'Onaylanacak bir eşleşme yok veya süresi doldu.']);
        }

        return redirect()->route('market.panel.job', $job)->with('panel_ok', 'Eşleşme onaylandı. Müşterinin iletişim bilgileri açıldı; lütfen 24 saat içinde arayın.');
    }

    /** Hizmet bilgileri (kısa sorular): kayıttan hemen sonra veya panelden */
    public function info(Request $request): View|RedirectResponse
    {
        $firm = $this->firm($request) ?? $this->onboarding($request);
        if (! $firm) {
            return $this->toLogin($request);
        }

        return view('market.panel-info', ['firm' => $firm, 'questions' => FirmQuestions::for($firm), 'answers' => (array) $firm->answers,
            'onboarding' => ! $this->firm($request), 'supplier' => FirmQuestions::isSupplier($firm)]);
    }

    public function saveInfo(Request $request): RedirectResponse
    {
        $panel = $this->firm($request);
        $firm = $panel ?? $this->onboarding($request);
        if (! $firm) {
            return $this->toLogin($request);
        }
        if (! $request->boolean('skip')) {
            $data = $request->validate(FirmQuestions::rules($firm) + ['a' => ['nullable', 'array']], [], ['a.*' => 'cevap']);
            FirmQuestions::save($firm, (array) ($data['a'] ?? []));
        }
        if ($panel) {
            return redirect()->route('market.panel')->with('panel_ok', 'Hizmet bilgileriniz kaydedildi. Size daha uygun işler gönderilecek.');
        }
        $request->session()->forget('firm_onboard');

        return redirect()->route('firms.page')->with('firm_ok', $firm->public_id)->with('firm_info', true);
    }

    /** Kayıt sonrası kısa süreli oturum (hizmet bilgileri adımı için; panel girişi değildir) */
    private function onboarding(Request $request): ?Firm
    {
        $s = $request->session()->get('firm_onboard');
        if (! is_array($s) || ($s['until'] ?? 0) < time()) {
            return null;
        }

        return Firm::query()->whereIn('status', ['pending', 'approved'])->find($s['id'] ?? 0);
    }

    private function firm(Request $request): ?Firm
    {
        $s = $request->session()->get(self::SESSION);
        if (! is_array($s) || ($s['until'] ?? 0) < time()) {
            return null;
        }
        $f = Firm::query()->find($s['id'] ?? 0);

        return $f && $f->status === 'approved' ? $f : null;
    }

    private function toLogin(Request $request): RedirectResponse
    {
        $request->session()->put('url.intended', $request->fullUrl());

        return redirect()->route('market.panel.login');
    }
}
