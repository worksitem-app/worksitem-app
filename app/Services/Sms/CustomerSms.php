<?php

namespace App\Services\Sms;

use App\Models\Lead;
use Illuminate\Support\Facades\Log;

/** Müşteriye giden bilgilendirme SMS'leri (metinler: config/worksitem_sms.php) */
class CustomerSms
{
    public function __construct(private NetgsmSms $sms) {}

    public function leadReceived(Lead $lead): bool
    {
        return $this->send('lead_received', $lead);
    }

    public function appointmentSet(Lead $lead): bool
    {
        return $lead->inspection_appointment_at ? $this->send('appointment_set', $lead) : false;
    }

    public function appointmentReminder(Lead $lead): bool
    {
        return $lead->inspection_appointment_at ? $this->send('appointment_reminder', $lead) : false;
    }

    private function send(string $key, Lead $lead): bool
    {
        if (! config("worksitem_sms.$key.enabled") || blank($lead->phone)) {
            return false;
        }

        $at = $lead->inspection_appointment_at?->copy()->timezone('Europe/Istanbul');
        $text = strtr((string) config("worksitem_sms.$key.text"), [
            ':no' => $lead->displayNo(),
            ':ad' => (string) $lead->name,
            ':tarih' => $at ? $at->locale('tr')->translatedFormat('j F l') : '',
            ':saat' => $at ? $at->format('H:i') : '',
            ':adres' => (string) ($lead->inspection_address ?: collect([$lead->district, $lead->city])->filter()->implode(' / ')),
        ]);

        try {
            return $this->sms->send((string) $lead->phone, $text)['ok'];
        } catch (\Throwable $e) {
            Log::warning('Müşteri SMS gönderilemedi', ['key' => $key, 'lead' => $lead->id, 'error' => $e->getMessage()]);

            return false;
        }
    }
}
