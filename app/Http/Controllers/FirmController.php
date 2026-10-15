<?php

namespace App\Http\Controllers;

use App\Models\Firm;
use App\Services\Payments\OnlinePayment;
use App\Services\Sms\NetgsmSms;
use App\Services\Sms\PhoneOtp;
use App\Support\LeadTextGuard;
use App\Support\TaxId;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

/**
 * Firma üyeliği: kayıt (SMS doğrulamalı) → hizmet bilgileri (kısa sorular, isteğe bağlı) → Worksitem onayı → firma rehberi.
 * Rehber FIRMS_DIRECTORY_ENABLED=true olana kadar herkese kapalıdır.
 */
class FirmController extends Controller
{
    public function page(Request $request): View
    {
        $tool = (string) $request->query('arac', '');
        $pre = [];
        if ($t = config("worksitem_market.tools.$tool")) {
            $pre[] = $t[0];
        }

        return view('firmalar.uyelik', [
            'types' => (array) config('worksitem_firms.types'),
            'trades' => (array) config('worksitem_trades'),
            'cities' => (array) config('worksitem_firms.cities'),
            'preTrades' => $pre,
            'ref' => mb_substr(preg_replace('/[^\pL\pN_-]/u', '', (string) $request->query('ref', '')), 0, 40),
            'directory' => (bool) config('worksitem_firms.directory_enabled'),
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
        $trades = (array) config('worksitem_trades');
        $cities = (array) config('worksitem_firms.cities');
        $allSubs = collect($trades)->flatMap(fn ($t) => $t['services'])->all();
        $c = (array) config('worksitem_firms');

        $data = $request->validate([
            'name' => ['required', 'string', 'min:3', 'max:150'],
            'type' => ['required', Rule::in(array_keys((array) $c['types']))],
            'legal_type' => ['required', Rule::in(['sahis', 'sirket'])],
            'legal_name' => ['required', 'string', 'min:3', 'max:200'],
            'tax_no' => ['required', 'string', 'max:14'],
            'tax_office' => ['required', 'string', 'min:2', 'max:80'],
            'mersis_no' => ['nullable', 'regex:/^\d{16}$/'],
            'kep' => ['nullable', 'email', 'max:150'],
            'efatura' => ['required', Rule::in(['yes', 'no', 'unknown'])],
            'invoice_city' => ['required', Rule::in($cities)],
            'invoice_district' => ['required', 'string', 'max:60'],
            'invoice_address' => ['required', 'string', 'min:10', 'max:300'],
            'tax_doc' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'trades' => ['required', 'array', 'min:1', 'max:'.(int) $c['max_trades']],
            'trades.*' => [Rule::in(array_keys($trades))],
            'subservices' => ['nullable', 'array', 'max:'.(int) $c['max_subservices']],
            'subservices.*' => [Rule::in($allSubs)],
            'cities' => ['required', 'array', 'min:1', 'max:'.(int) $c['max_cities']],
            'cities.*' => [Rule::in($cities)],
            'districts' => ['nullable', 'string', 'max:500'],
            'contact_name' => ['required', 'string', 'max:120'],
            'phone' => ['required', 'string', 'max:30'],
            'code' => ['required', 'digits:6'],
            'email' => ['required', 'email', 'max:150'],
            'website' => ['nullable', 'url', 'max:200'],
            'founded_year' => ['nullable', 'integer', 'min:1900', 'max:'.date('Y')],
            'description' => ['nullable', 'string', 'max:1500'],
            'ref' => ['nullable', 'string', 'max:40'],
            'terms' => ['accepted'],
            'declare' => ['accepted'],
            'kvkk' => ['accepted'],
        ], [
            'name.required' => 'Firma adını yazın.',
            'type.required' => 'Firma türünü seçin.',
            'legal_name.required' => 'Vergi levhasındaki unvanı yazın.',
            'tax_no.required' => 'Vergi kimlik numarasını yazın.',
            'tax_office.required' => 'Vergi dairesini yazın.',
            'mersis_no.regex' => 'MERSİS numarası 16 rakam olmalıdır.',
            'invoice_city.required' => 'Fatura adresinin ilini seçin.',
            'invoice_district.required' => 'Fatura adresinin ilçesini yazın.',
            'invoice_address.required' => 'Fatura adresini yazın.',
            'tax_doc.required' => 'Vergi levhanızı yükleyin (PDF, JPG veya PNG).',
            'tax_doc.mimes' => 'Vergi levhası PDF, JPG veya PNG olmalıdır.',
            'tax_doc.max' => 'Vergi levhası en fazla 5 MB olabilir.',
            'trades.required' => 'En az bir iş kolu seçin.',
            'cities.required' => 'Hizmet verdiğiniz en az bir ili seçin.',
            'contact_name.required' => 'Yetkili adını ve soyadını yazın.',
            'phone.required' => 'Cep telefonunu yazın.',
            'email.required' => 'Fatura ve iş bildirimleri için e-posta adresini yazın.',
            'code.required' => 'Telefonunuza gelen 6 haneli kodu yazın.',
            'code.digits' => 'Kod 6 haneli olmalıdır.',
            'website.url' => 'Web sitesini https:// ile başlayarak yazın.',
            'terms.accepted' => 'Devam etmek için sözleşmeyi onaylayın.',
            'declare.accepted' => 'Devam etmek için beyanı onaylayın.',
            'kvkk.accepted' => 'Devam etmek için aydınlatma metnini okuduğunuzu onaylayın.',
        ]);

        $taxNo = preg_replace('/\D+/', '', $data['tax_no']);
        $okLen = $data['legal_type'] === 'sirket' ? strlen($taxNo) === 10 : in_array(strlen($taxNo), [10, 11], true);
        if (! $okLen || ! TaxId::valid($taxNo)) {
            throw ValidationException::withMessages(['tax_no' => $data['legal_type'] === 'sirket'
                ? 'Şirketler için 10 haneli vergi kimlik numarası gerekir; numara geçerli görünmüyor.'
                : 'Şahıs işletmesi için 11 haneli TC kimlik veya 10 haneli vergi numarası gerekir; numara geçerli görünmüyor.']);
        }
        if (! LeadTextGuard::isValidFullName($data['contact_name'])) {
            throw ValidationException::withMessages(['contact_name' => 'Lütfen yetkilinin gerçek adını ve soyadını yazın.']);
        }
        if (! $otp->verify($data['phone'], $data['code'], 'firma')) {
            throw ValidationException::withMessages(['code' => 'Kod hatalı veya süresi doldu. Yeni kod isteyebilirsiniz.']);
        }
        $phone = (string) NetgsmSms::normalize($data['phone']);
        if (Firm::query()->where(fn ($q) => $q->where('phone', $phone)->orWhere('tax_no', $taxNo))->whereIn('status', ['pending', 'approved'])->exists()) {
            return redirect()->route('firms.page')->with('firm_ok', 'exists');
        }

        // Alt hizmetler yalnız seçilen iş kollarından olabilir
        $subs = array_values(array_intersect((array) ($data['subservices'] ?? []), collect($trades)->only($data['trades'])->flatMap(fn ($t) => $t['services'])->all()));
        $districts = array_values(array_unique(array_filter(array_map(fn ($d) => mb_substr(trim($d), 0, 60), preg_split('/[,;\n]+/u', (string) ($data['districts'] ?? '')) ?: []))));

        $doc = $request->file('tax_doc')->store('firm-docs', 'local');
        $firm = Firm::query()->create([
            'name' => trim($data['name']), 'type' => $data['type'], 'legal_type' => $data['legal_type'], 'legal_name' => trim($data['legal_name']),
            'tax_no' => $taxNo, 'tax_office' => trim($data['tax_office']), 'mersis_no' => $data['mersis_no'] ?? null, 'kep' => $data['kep'] ?? null,
            'efatura' => $data['efatura'], 'invoice_city' => $data['invoice_city'], 'invoice_district' => trim($data['invoice_district']),
            'invoice_address' => trim($data['invoice_address']), 'tax_doc_path' => $doc,
            'trades' => array_values(array_unique($data['trades'])), 'subservices' => $subs,
            'cities' => array_values(array_unique($data['cities'])), 'districts' => array_slice($districts, 0, 30),
            'district' => trim($data['invoice_district']),
            'contact_name' => trim($data['contact_name']), 'phone' => $phone, 'email' => $data['email'],
            'website' => $data['website'] ?? null, 'founded_year' => $data['founded_year'] ?? null,
            'description' => isset($data['description']) ? trim(strip_tags($data['description'])) : null,
            'referrer' => ($data['ref'] ?? null) ? mb_substr(preg_replace('/[^\pL\pN_-]/u', '', $data['ref']), 0, 40) : null,
            'status' => 'pending', 'terms_at' => now(),
        ]);

        try {
            if ($to = (string) config('worksitem_leads.notification_email')) {
                Mail::raw(
                    "Yeni firma üyeliği başvurusu ({$firm->public_id})\n\nFirma: {$firm->name}\nUnvan: {$firm->legal_name}\nTür: {$firm->typeLabel()}\n"
                    ."VKN/TCKN: {$firm->tax_no} · {$firm->tax_office} V.D. · ".Firm::EFATURA[$firm->efatura]."\n"
                    .'İş kolları: '.implode(', ', $firm->serviceLabels())."\nİller: ".implode(', ', (array) $firm->cities)
                    ."\nYetkili: {$firm->contact_name}\nTelefon: 0{$firm->phone}\nE-posta: {$firm->email}"
                    .($firm->referrer ? "\nSaha temsilcisi: {$firm->referrer}" : '')
                    ."\n\nKontrol: vergi levhası + GİB e-Fatura kayıtlı kullanıcılar listesi → onay: ".url('/admin/firms'),
                    fn ($m) => $m->to($to)->subject('WORKSITEM | Yeni firma üyeliği | '.$firm->name)
                );
            }
        } catch (\Throwable $e) {
            Log::error('Firma üyeliği e-postası', ['error' => $e->getMessage()]);
        }

        $request->session()->put('firm_onboard', ['id' => $firm->id, 'until' => now()->addHours(2)->timestamp]);

        return redirect()->route('market.panel.info')->with('firm_new', $firm->public_id);
    }

    public function index(Request $request): View
    {
        abort_unless(config('worksitem_firms.directory_enabled'), 404);
        $f = $request->validate(['hizmet' => ['nullable', 'string', 'max:40'], 'il' => ['nullable', 'string', 'max:30']]);
        $q = Firm::query()->approved();
        if (! empty($f['hizmet']) && array_key_exists($f['hizmet'], (array) config('worksitem_trades'))) {
            $q->whereJsonContains('trades', $f['hizmet']);
        }
        if (! empty($f['il']) && in_array($f['il'], (array) config('worksitem_firms.cities'), true)) {
            $q->whereJsonContains('cities', $f['il']);
        }
        $firms = $q->orderByRaw("CASE WHEN plan = 'pro' AND plan_until > ? THEN 0 ELSE 1 END", [now()])
            ->orderByDesc('approved_at')->paginate(24)->withQueryString();

        return view('firmalar.index', [
            'firms' => $firms, 'services' => collect((array) config('worksitem_trades'))->map(fn ($t) => [$t['label']])->all(),
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
