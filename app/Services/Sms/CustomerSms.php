<?php

namespace App\Services\Sms;

use App\Models\Lead;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/** Müşteriye giden bilgilendirme SMS'leri (metinler: config/worksitem_sms.php) */
class CustomerSms
{
    public function __construct(private NetgsmSms $sms) {}

    public function leadReceived(Lead $lead): bool
    {
        // Numara doğrulanmadığı için: numara, IP ve site geneli günlük tavan (SMS faturası ve taciz koruması)
        $no = NetgsmSms::normalize((string) $lead->phone);
        if (! $no || ! config('worksitem_sms.lead_received.enabled')) {
            return false;
        }
        $day = now('Europe/Istanbul')->format('Ymd');
        $c = (array) config('worksitem_sms.lead_received');
        $keys = [
            'sms-lead-phone:'.$day.':'.$no => (int) ($c['max_per_phone_day'] ?? 2),
            'sms-lead-ip:'.$day.':'.sha1((string) request()->ip()) => (int) ($c['max_per_ip_day'] ?? 5),
            'sms-lead-global:'.$day => (int) ($c['global_day'] ?? 300),
        ];
        foreach ($keys as $k => $max) {
            if ((int) Cache::get($k, 0) >= $max) {
                Log::warning('Talep SMS günlük tavanı doldu, SMS atlanıyor', ['lead' => $lead->id, 'tavan' => explode(':', $k)[0]]);

                return false;
            }
        }
        foreach (array_keys($keys) as $k) {
            Cache::add($k, 0, now()->addDay());
            Cache::increment($k);
        }

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
