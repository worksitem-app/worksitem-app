<?php

namespace App\Services\Payments;

use App\Models\Lead;
use App\Services\Sms\NetgsmSms;
use App\Support\InspectionOffer;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Keşif ödemesi: iyzico ortak ödeme sayfasını başlatır, dönüşü doğrular,
 * talebi "Ödendi" yapar ve müşteriye / ekibe bildirim (e-posta + SMS) gönderir.
 */
class InspectionPayment
{
    public function __construct(private IyzicoClient $iyzico, private NetgsmSms $sms) {}

    public function cardEnabled(): bool
    {
        return (bool) config('worksitem_inspection.iyzico_enabled') && $this->iyzico->configured();
    }

    /**
     * @return array{ok: bool, url?: string, error?: string}
     */
    public function start(Lead $lead, ?string $ip): array
    {
        if (! $this->cardEnabled()) {
            return ['ok' => false, 'error' => 'Kartla ödeme şu an kapalı.'];
        }

        $total = InspectionOffer::amounts()['total'];
        $parts = preg_split('/\s+/u', trim((string) $lead->name)) ?: [];
        $surname = count($parts) > 1 ? array_pop($parts) : '-';
        $name = trim(implode(' ', $parts)) ?: (string) $lead->name;
        $city = $lead->city ?: 'İstanbul';
        $address = trim(collect([$lead->district, $lead->city])->filter()->implode(' / ')) ?: $city;
        $inv = (array) ($lead->inspection_data['invoice'] ?? []);
        $contact = ($inv['type'] ?? '') === 'kurumsal' ? ($inv['title'] ?? $lead->name) : $lead->name;
        $gsm = NetgsmSms::normalize($lead->phone);
        $addr = ['contactName' => (string) $contact, 'city' => $city, 'country' => 'Turkey', 'address' => $address];

        $payload = [
            'locale' => 'tr',
            'conversationId' => $lead->public_id,
            'price' => IyzicoClient::price($total),
            'paidPrice' => IyzicoClient::price($total),
            'currency' => 'TRY',
            'basketId' => $lead->public_id,
            'paymentGroup' => 'PRODUCT',
            'callbackUrl' => route('kesif.iyzico.callback'),
            'enabledInstallments' => [1],
            'buyer' => [
                'id' => $lead->public_id,
                'name' => $name,
                'surname' => $surname,
                'gsmNumber' => $gsm ? '+90'.$gsm : '',
                'email' => (string) $lead->email,
                // TC kimlik no toplanmıyor; iyzico'nun bilinmeyen kimlik için kabul ettiği değer
                'identityNumber' => '11111111111',
                'registrationAddress' => $address,
                'ip' => (string) $ip,
                'city' => $city,
                'country' => 'Turkey',
            ],
            'shippingAddress' => $addr,
            'billingAddress' => $addr,
            'basketItems' => [[
                'id' => 'KESIF-'.$lead->public_id,
                'name' => 'Yerinde keşif ve keşif raporu',
                'category1' => 'Hizmet',
                'itemType' => 'VIRTUAL',
                'price' => IyzicoClient::price($total),
            ]],
        ];

        $res = $this->iyzico->initialize($payload);

        if (($res['status'] ?? '') !== 'success' || empty($res['token']) || empty($res['paymentPageUrl'])) {
            Log::warning('iyzico ödeme başlatılamadı', ['lead' => $lead->public_id, 'error' => $res['errorMessage'] ?? null, 'code' => $res['errorCode'] ?? null]);

            return ['ok' => false, 'error' => (string) ($res['errorMessage'] ?? 'Ödeme sayfası açılamadı.')];
        }

        $data = (array) $lead->inspection_data;
        $data['iyzico'] = ['token' => $res['token'], 'started_at' => now()->toIso8601String(), 'sandbox' => $this->iyzico->isSandbox()];
        $lead->forceFill(['inspection_data' => $data])->save();

        return ['ok' => true, 'url' => (string) $res['paymentPageUrl']];
    }

    /**
     * iyzico dönüşü: token ile sonucu iyzico'dan sorgular (tarayıcıdan gelen veriye güvenmez).
     *
     * @return array{lead: ?Lead, paid: bool, error?: string}
     */
    public function complete(string $token): array
    {
        $lead = Lead::query()->where('inspection_data->iyzico->token', $token)->first();
        if (! $lead) {
            return ['lead' => null, 'paid' => false, 'error' => 'Ödeme kaydı bulunamadı.'];
        }
        if ($lead->inspection_status === 'odendi') {
            return ['lead' => $lead, 'paid' => true];
        }

        $res = $this->iyzico->retrieve($token, $lead->public_id);
        $total = (float) InspectionOffer::amounts()['total'];
        $paid = ($res['status'] ?? '') === 'success'
            && ($res['paymentStatus'] ?? '') === 'SUCCESS'
            && ($res['basketId'] ?? '') === $lead->public_id
            && abs((float) ($res['paidPrice'] ?? 0) - $total) < 0.01;

        $data = (array) $lead->inspection_data;
        $data['iyzico'] = array_merge((array) ($data['iyzico'] ?? []), [
            'payment_id' => $res['paymentId'] ?? null,
            'payment_status' => $res['paymentStatus'] ?? null,
            'paid_price' => $res['paidPrice'] ?? null,
            'error' => $paid ? null : ($res['errorMessage'] ?? null),
            'checked_at' => now()->toIso8601String(),
        ]);

        $lead->forceFill(array_filter([
            'inspection_data' => $data,
            'inspection_status' => $paid ? 'odendi' : null,
            'inspection_paid_at' => $paid ? now() : null,
            'last_activity_at' => now(),
        ], fn ($v) => $v !== null))->save();

        if ($paid) {
            $this->notifyPaid($lead);
        }

        return ['lead' => $lead, 'paid' => $paid, 'error' => $paid ? null : (string) ($res['errorMessage'] ?? 'Ödeme tamamlanmadı.')];
    }

    /** Sipariş verildiğinde müşteriye SMS (havale: IBAN; kart: bilgi) ve ekibe SMS */
    public function notifyOrdered(Lead $lead): void
    {
        $total = InspectionOffer::tl(InspectionOffer::amounts()['total']);
        $iban = (string) config('worksitem_company.iban');
        $msg = $lead->inspection_payment_method === 'havale'
            ? "Kainta İnşaat: Keşif siparişiniz alındı ({$lead->public_id}). Tutar {$total}."
                .($iban !== '' ? " IBAN: {$iban} Açıklama: {$lead->public_id}." : ' IBAN bilgisi WhatsApp ile iletilecek.')
                .' Dekont için WhatsApp: 0850 346 49 83'
            : "Kainta İnşaat: Keşif siparişiniz alındı ({$lead->public_id}). Tutar {$total}. Ödemeden sonra uzmanımız sizi arayacak. 0850 346 49 83";

        $this->sms->send((string) $lead->phone, $msg);
        $this->teamSms("Worksitem KEŞİF SİPARİŞİ: {$lead->name}, {$lead->phone}, {$lead->city}, ".($lead->inspection_payment_method === 'havale' ? 'HAVALE' : 'KART')." {$lead->public_id}");
    }

    private function notifyPaid(Lead $lead): void
    {
        $total = InspectionOffer::tl(InspectionOffer::amounts()['total']);
        $this->sms->send((string) $lead->phone, "Kainta İnşaat: {$total} keşif ödemeniz alındı ({$lead->public_id}). Uzmanımız randevu için sizi arayacak. 0850 346 49 83");
        $this->teamSms("Worksitem KEŞİF ÖDENDİ: {$lead->name}, {$lead->phone}, {$lead->city}, {$total} {$lead->public_id} - randevu verin");

        $to = trim((string) config('worksitem_leads.notification_email'));
        if ($to !== '') {
            try {
                Mail::raw(
                    "Keşif ödemesi kartla ALINDI — müşteriyi arayıp randevu verin.\n\nTalep no: {$lead->public_id}\nAd soyad: {$lead->name}\nTelefon: {$lead->phone}\nŞehir: {$lead->city}\nTutar: {$total}\niyzico ödeme no: ".($lead->inspection_data['iyzico']['payment_id'] ?? '-'),
                    fn ($m) => $m->to($to)->subject('WORKSITEM | KEŞİF ÖDENDİ | '.($lead->name ?: 'İsimsiz').' | '.$lead->public_id)
                );
            } catch (\Throwable $e) {
                Log::error('Keşif ödeme e-postası gönderilemedi', ['lead' => $lead->public_id, 'error' => $e->getMessage()]);
            }
        }
    }

    private function teamSms(string $msg): void
    {
        $team = (string) config('services.netgsm.team_phone');
        if ($team !== '') {
            $this->sms->send($team, $msg);
        }
    }
}
