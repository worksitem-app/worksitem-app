<?php

namespace App\Http\Controllers;

use App\Models\Firm;
use App\Services\Payments\OnlinePayment;
use App\Services\Sms\NetgsmSms;
use App\Services\Sms\PhoneOtp;
use App\Support\LeadTextGuard;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

/**
 * Firma üyeliği: kayıt (SMS doğrulamalı) → Kainta İnşaat Uzmanları onayı → firma rehberi.
 * Rehber FIRMS_DIRECTORY_ENABLED=true olana kadar herkese kapalıdır.
 */
class FirmController extends Controller
{
    public function page(Request $request): View
    {
        $tool = (string) $request->query('arac', '');
        $pre = [];
        foreach ((array) config('worksitem_firms.services') as $k => $s) {
            if ($tool !== '' && in_array($tool, (array) $s[1], true)) {
                $pre[] = $k;
            }
        }

        return view('firmalar.uyelik', [
            'types' => (array) config('worksitem_firms.types'),
            'services' => (array) config('worksitem_firms.services'),
            'cities' => (array) config('worksitem_firms.cities'),
            'preServices' => $pre,
            'directory' => (bool) config('worksitem_firms.directory_enabled'),
            'approvedCount' => Firm::query()->approved()->count(),
        ]);
    }

    public function sendCode(Request $request, PhoneOtp $otp): JsonResponse
    {
        $request->validate(['phone' => ['required', 'string', 'max:30']]);
        if (! NetgsmSms::normalize((string) $request->input('phone'))) {
            return response()->json(['ok' => false, 'message' => 'Lütfen 05XX XXX XX XX biçiminde bir cep telefonu yazın.'], 422);
        }
        $r = $otp->send((string) $request->input('phone'), 'firma');

        return response()->json($r, $r['ok'] ? 200 : 429);
    }

    public function register(Request $request, PhoneOtp $otp): RedirectResponse
    {
        $types = array_keys((array) config('worksitem_firms.types'));
        $services = array_keys((array) config('worksitem_firms.services'));
        $cities = (array) config('worksitem_firms.cities');

        $data = $request->validate([
            'name' => ['required', 'string', 'min:3', 'max:150'],
            'type' => ['required', Rule::in($types)],
            'services' => ['required', 'array', 'min:1', 'max:8'],
            'services.*' => [Rule::in($services)],
            'cities' => ['required', 'array', 'min:1', 'max:10'],
            'cities.*' => [Rule::in($cities)],
            'district' => ['nullable', 'string', 'max:100'],
            'contact_name' => ['required', 'string', 'max:120'],
            'phone' => ['required', 'string', 'max:30'],
            'code' => ['required', 'digits:6'],
            'email' => ['nullable', 'email', 'max:150'],
            'website' => ['nullable', 'url', 'max:200'],
            'tax_no' => ['nullable', 'regex:/^\d{10,11}$/'],
            'founded_year' => ['nullable', 'integer', 'min:1900', 'max:'.date('Y')],
            'description' => ['nullable', 'string', 'max:1500'],
            'terms' => ['accepted'],
            'kvkk' => ['accepted'],
        ], [
            'name.required' => 'Firma adını yazın.',
            'type.required' => 'Firma türünü seçin.',
            'services.required' => 'En az bir hizmet alanı seçin.',
            'cities.required' => 'Hizmet verdiğiniz en az bir ili seçin.',
            'contact_name.required' => 'Yetkili adını ve soyadını yazın.',
            'phone.required' => 'Cep telefonunu yazın.',
            'code.required' => 'Telefonunuza gelen 6 haneli kodu yazın.',
            'code.digits' => 'Kod 6 haneli olmalıdır.',
            'website.url' => 'Web sitesini https:// ile başlayarak yazın.',
            'tax_no.regex' => 'Vergi / TC kimlik numarası 10 veya 11 rakam olmalıdır.',
            'terms.accepted' => 'Devam etmek için üyelik koşullarını onaylayın.',
            'kvkk.accepted' => 'Devam etmek için aydınlatma metnini okuduğunuzu onaylayın.',
        ]);

        if (! LeadTextGuard::isValidFullName($data['contact_name'])) {
            throw ValidationException::withMessages(['contact_name' => 'Lütfen yetkilinin gerçek adını ve soyadını yazın.']);
        }
        if (! $otp->verify($data['phone'], $data['code'], 'firma')) {
            throw ValidationException::withMessages(['code' => 'Kod hatalı veya süresi doldu. Yeni kod isteyebilirsiniz.']);
        }
        $phone = (string) NetgsmSms::normalize($data['phone']);
        if (Firm::query()->where('phone', $phone)->whereIn('status', ['pending', 'approved'])->exists()) {
            return redirect()->route('firms.page')->with('firm_ok', 'exists');
        }

        $firm = Firm::query()->create([
            'name' => trim($data['name']), 'type' => $data['type'], 'services' => array_values(array_unique($data['services'])),
            'cities' => array_values(array_unique($data['cities'])), 'district' => $data['district'] ?? null,
            'contact_name' => trim($data['contact_name']), 'phone' => $phone, 'email' => $data['email'] ?? null,
            'website' => $data['website'] ?? null, 'tax_no' => $data['tax_no'] ?? null, 'founded_year' => $data['founded_year'] ?? null,
            'description' => isset($data['description']) ? trim(strip_tags($data['description'])) : null,
            'status' => 'pending', 'terms_at' => now(),
        ]);

        try {
            if ($to = (string) config('worksitem_leads.notification_email')) {
                Mail::raw(
                    "Yeni firma üyeliği başvurusu ({$firm->public_id})\n\nFirma: {$firm->name}\nTür: {$firm->typeLabel()}\n"
                    .'Hizmetler: '.implode(', ', $firm->serviceLabels())."\nİller: ".implode(', ', (array) $firm->cities)
                    ."\nYetkili: {$firm->contact_name}\nTelefon: 0{$firm->phone}\nE-posta: ".($firm->email ?: '-')
                    ."\n\nOnay: ".url('/admin/firms'),
                    fn ($m) => $m->to($to)->subject('WORKSITEM | Yeni firma üyeliği | '.$firm->name)
                );
            }
        } catch (\Throwable $e) {
            Log::error('Firma üyeliği e-postası', ['error' => $e->getMessage()]);
        }

        return redirect()->route('firms.page')->with('firm_ok', $firm->public_id);
    }

    public function index(Request $request): View
    {
        abort_unless(config('worksitem_firms.directory_enabled'), 404);
        $f = $request->validate(['hizmet' => ['nullable', 'string', 'max:30'], 'il' => ['nullable', 'string', 'max:30']]);
        $q = Firm::query()->approved();
        if (! empty($f['hizmet']) && array_key_exists($f['hizmet'], (array) config('worksitem_firms.services'))) {
            $q->whereJsonContains('services', $f['hizmet']);
        }
        if (! empty($f['il']) && in_array($f['il'], (array) config('worksitem_firms.cities'), true)) {
            $q->whereJsonContains('cities', $f['il']);
        }
        $firms = $q->orderByRaw("CASE WHEN plan = 'pro' AND plan_until > ? THEN 0 ELSE 1 END", [now()])
            ->orderByDesc('approved_at')->paginate(24)->withQueryString();

        return view('firmalar.index', [
            'firms' => $firms, 'services' => (array) config('worksitem_firms.services'),
            'cities' => (array) config('worksitem_firms.cities'), 'filter' => $f,
        ]);
    }

    public function show(Firm $firm): View
    {
        abort_unless(config('worksitem_firms.directory_enabled') && $firm->status === 'approved', 404);
        $firm->increment('views');

        return view('firmalar.show', ['firm' => $firm]);
    }

    /** "Ara / WhatsApp" tıklaması sayılır, numaraya yönlendirilir (numara sayfada açık yazılmaz) */
    public function contact(Firm $firm, string $via): RedirectResponse
    {
        abort_unless(config('worksitem_firms.directory_enabled') && $firm->status === 'approved', 404);
        $firm->increment('contact_clicks');
        $msg = rawurlencode('Merhaba, Worksitem firma rehberinden ulaşıyorum.');

        return $via === 'whatsapp'
            ? redirect()->away('https://wa.me/90'.$firm->phone.'?text='.$msg)
            : redirect()->away('tel:+90'.$firm->phone);
    }

    /** Öne çıkan (Pro) üyelik ödemesi: yöneticinin gönderdiği imzalı bağlantı */
    public function pro(Request $request, Firm $firm, OnlinePayment $pay): RedirectResponse|View
    {
        abort_unless($firm->status === 'approved', 404);
        if (! $pay->enabled()) {
            return view('odeme-sonuc', ['order' => null, 'message' => 'Kartla ödeme şu an kapalı. Lütfen bizimle iletişime geçin.']);
        }
        $order = $pay->firmProOrder($firm);
        $r = $pay->start($order, $request->ip());

        return $r['ok'] ? redirect()->away($r['url']) : view('odeme-sonuc', ['order' => $order->refresh()]);
    }
}
