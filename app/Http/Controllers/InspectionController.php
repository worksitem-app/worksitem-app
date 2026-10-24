<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Services\Payments\InspectionPayment;
use App\Support\InspectionOffer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

/**
 * Yerinde keşif siparişi.
 * Teşekkür sayfasındaki pencerede (popup) ön bilgilendirme formu ve mesafeli hizmet sözleşmesi onaylanır,
 * ödeme yöntemi ve fatura tipi seçilir; sipariş talep kaydına işlenir, info@ adresine sözleşme PDF'iyle bildirim
 * gider ve ödeme sayfası açılır. Kayıt oturuma bağlıdır: başkasının talep numarasıyla işlem yapılamaz.
 */
class InspectionController extends Controller
{
    public function accept(Request $request): RedirectResponse
    {
        $lead = $this->sessionLead($request);

        if (! $lead) {
            return redirect()->route('lead.thanks');
        }

        if ($lead->inspection_status !== null) {
            return redirect()->route('kesif.payment');
        }

        // Pilot dönem: keşif yalnız hizmet bölgesinde (bölge dışına ödeme alınmaz)
        if (\App\Support\Pilot::inArea($lead->city, $lead->district) === false) {
            return redirect()->route('lead.thanks');
        }

        $kurumsal = $request->input('fatura') === 'kurumsal';
        $data = $request->validate([
            'odeme' => ['required', Rule::in(array_keys((array) config('worksitem_inspection.payment_methods')))],
            'fatura' => ['required', Rule::in(['bireysel', 'kurumsal'])],
            'fatura_unvan' => [Rule::requiredIf($kurumsal), 'nullable', 'string', 'max:200'],
            'vergi_dairesi' => [Rule::requiredIf($kurumsal), 'nullable', 'string', 'max:100'],
            'vergi_no' => [Rule::requiredIf($kurumsal), 'nullable', 'regex:/^\d{10,11}$/'],
            'email' => ['required', 'email:rfc', 'max:150'],
            'kesif_onay' => ['accepted'],
            'kesif_erken' => ['nullable', 'boolean'],
        ], [
            'email.required' => 'Sözleşme ve fatura için e-posta adresinizi yazın.',
            'email.email' => 'Lütfen geçerli bir e-posta adresi yazın.',
            'odeme.required' => 'Lütfen ödeme yöntemini seçin.',
            'odeme.in' => 'Lütfen ödeme yöntemini seçin.',
            'fatura.required' => 'Lütfen fatura tipini seçin.',
            'fatura_unvan.required' => 'Kurumsal fatura için firma unvanını yazın.',
            'vergi_dairesi.required' => 'Kurumsal fatura için vergi dairesini yazın.',
            'vergi_no.required' => 'Kurumsal fatura için vergi numarasını yazın.',
            'vergi_no.regex' => 'Vergi numarası 10 haneli (TC kimlik no ise 11 haneli) olmalıdır.',
            'kesif_onay.accepted' => 'Devam etmek için ön bilgilendirme formunu ve sözleşmeyi onaylayın.',
        ]);

        $lead->forceFill([
            'email' => $data['email'],
            'inspection_status' => 'kabul',
            'inspection_accepted_at' => now(),
            'inspection_fee' => InspectionOffer::fee(),
            'inspection_terms_version' => (string) config('worksitem_inspection.terms_version'),
            'inspection_payment_method' => $data['odeme'],
            'inspection_data' => [
                'amounts' => InspectionOffer::amounts(),
                'invoice' => $kurumsal
                    ? ['type' => 'kurumsal', 'title' => $data['fatura_unvan'], 'tax_office' => $data['vergi_dairesi'], 'tax_no' => $data['vergi_no']]
                    : ['type' => 'bireysel'],
                'consents' => [
                    'obf_mhs' => true,
                    'early_performance' => (bool) ($data['kesif_erken'] ?? false),
                    'texts' => InspectionOffer::consentTexts(),
                    'at' => now()->toIso8601String(),
                    'ip_hash' => hash_hmac('sha256', (string) $request->ip(), (string) config('app.key')),
                    'user_agent' => mb_substr((string) $request->userAgent(), 0, 200),
                ],
            ],
            'last_activity_at' => now(),
        ])->save();

        $payment = app(InspectionPayment::class);
        $notifier = app(\App\Services\Payments\InspectionNotifier::class);

        // Kart + iyzico açık: doğrudan iyzico'nun güvenli ödeme sayfasına.
        // v109: SMS ve e-postalar burada GİTMEZ; ödeme onaylanınca (InspectionPayment::complete) gider.
        if ($lead->inspection_payment_method === 'kart' && $payment->cardEnabled()) {
            $start = $payment->start($lead, $request->ip());
            if ($start['ok']) {
                return redirect()->away($start['url']);
            }

            // Ödeme sayfası açılamadı: müşteriye bir şey gönderme, ekip arayıp bağlantı göndersin
            $notifier->teamEmail($lead, 'odeme-acilamadi');

            return redirect()->route('kesif.payment')->with('iyzico_error', $start['error']);
        }

        // Havale/EFT (ya da kartla ödeme kapalı): müşterinin IBAN/ödeme bilgisine hemen ihtiyacı var
        $notifier->teamEmail($lead, 'siparis');
        $notifier->customerCopy($lead);
        $payment->notifyOrdered($lead);

        return redirect()->route('kesif.payment');
    }

    /** Ödeme sayfasındaki "Kartla öde" (yeniden deneme) */
    public function iyzicoStart(Request $request, InspectionPayment $payment): RedirectResponse
    {
        $lead = $this->sessionLead($request);
        if (! $lead || $lead->inspection_status !== 'kabul' || $lead->inspection_payment_method !== 'kart') {
            return redirect()->route('kesif.payment');
        }

        $start = $payment->start($lead, $request->ip());

        return $start['ok']
            ? redirect()->away($start['url'])
            : redirect()->route('kesif.payment')->with('iyzico_error', $start['error']);
    }

    /**
     * iyzico dönüşü (tarayıcı token'ı POST eder). Oturum ve CSRF kullanılmaz;
     * sonuç iyzico'dan sunucu tarafında sorgulanır, kullanıcı imzalı sonuç adresine yönlendirilir.
     */
    public function iyzicoCallback(Request $request, InspectionPayment $payment): RedirectResponse
    {
        $token = (string) $request->input('token', '');
        if ($token === '' || strlen($token) > 200) {
            return redirect()->route('lead.thanks');
        }

        $result = $payment->complete($token);
        if (! $result['lead']) {
            return redirect()->route('lead.thanks');
        }

        return redirect()->to(\Illuminate\Support\Facades\URL::temporarySignedRoute(
            'kesif.result', now()->addMinutes(30), ['lead' => $result['lead']->public_id, 'ok' => $result['paid'] ? 1 : 0]
        ));
    }

    /** İmzalı sonuç adresi: oturumu talebe yeniden bağlar ve ödeme sayfasını sonuçla açar */
    public function result(Request $request, string $lead): RedirectResponse
    {
        $request->session()->put('kesif_lead', $lead);

        return redirect()->route('kesif.payment')->with(
            $request->boolean('ok') ? 'iyzico_ok' : 'iyzico_error',
            $request->boolean('ok') ? 'Ödemeniz alındı.' : 'Ödeme tamamlanmadı. Kart bilgilerinizi kontrol edip yeniden deneyin.'
        );
    }

    public function payment(Request $request): View|RedirectResponse
    {
        $lead = $this->sessionLead($request);

        if (! $lead || $lead->inspection_status === null) {
            return redirect()->route('lead.thanks');
        }

        $method = $lead->inspection_payment_method ?: 'kart';
        $message = sprintf(
            "Merhaba, Worksitem üzerinden yerinde keşif siparişi verdim. Talep no: %s. Ad: %s%s. Ödeme yöntemi: %s. %s",
            $lead->public_id,
            $lead->name ?: '-',
            $lead->city ? ', Şehir: '.$lead->city : '',
            config("worksitem_inspection.payment_methods.$method.label", $method),
            $method === 'havale' ? 'Dekontu ekte gönderiyorum.' : 'Kart ödeme bağlantısını rica ederim.'
        );

        return view('kesif.odeme', [
            'lead' => $lead,
            'method' => $method,
            'amounts' => InspectionOffer::amounts(),
            'feeLabel' => InspectionOffer::feeLabel(),
            'whatsappUrl' => InspectionOffer::whatsappUrl($message),
            'phone' => (string) config('worksitem_inspection.phone'),
            'iyzico' => app(InspectionPayment::class)->cardEnabled(),
            'company' => (array) config('worksitem_company'),
        ]);
    }

    /** /legal/kesif-on-bilgilendirme-formu ve /legal/kesif-mesafeli-hizmet-sozlesmesi */
    public function document(Request $request, string $doc): View
    {
        $lead = $this->sessionLead($request);

        return view('legal.kesif-document', [
            'doc' => $doc === 'mhs' ? 'mhs' : 'obf',
            'lead' => $lead,
        ]);
    }

    /** Sözleşmenin (ÖBF + MHS) logolu PDF kopyası — kalıcı veri saklayıcısı */
    public function contractPdf(Request $request): Response
    {
        $lead = $this->sessionLead($request);

        return $this->pdf($lead)->download('worksitem-kesif-sozlesmesi'.($lead ? '-'.$lead->public_id : '').'.pdf');
    }

    private function pdf(?Lead $lead): \Barryvdh\DomPDF\PDF
    {
        return app(\App\Services\Payments\InspectionNotifier::class)->pdf($lead);
    }

    private function sessionLead(Request $request): ?Lead
    {
        $publicId = $request->session()->get('kesif_lead');

        return $publicId ? Lead::query()->where('public_id', $publicId)->first() : null;
    }
}
