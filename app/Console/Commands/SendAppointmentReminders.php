<?php

namespace App\Console\Commands;

use App\Models\Lead;
use App\Services\Sms\CustomerSms;
use Illuminate\Console\Command;

/** Yarınki keşif randevuları için müşteriye hatırlatma SMS'i (her gün 18:00) */
class SendAppointmentReminders extends Command
{
    protected $signature = 'sms:appointment-reminders';

    protected $description = 'Yarın keşif randevusu olan müşterilere hatırlatma SMS\'i gönderir';

    public function handle(CustomerSms $sms): int
    {
        $tz = 'Europe/Istanbul';
        $from = now($tz)->addDay()->startOfDay()->setTimezone(config('app.timezone'));
        $to = now($tz)->addDay()->endOfDay()->setTimezone(config('app.timezone'));
        $sent = 0;

        Lead::query()
            ->whereBetween('inspection_appointment_at', [$from, $to])
            ->whereNull('inspection_reminder_sent_at')
            ->where(fn ($q) => $q->whereNull('inspection_status')->orWhere('inspection_status', '!=', 'iptal'))
            ->each(function (Lead $lead) use ($sms, &$sent) {
                if ($sms->appointmentReminder($lead)) {
                    $lead->forceFill(['inspection_reminder_sent_at' => now()])->saveQuietly();
                    $sent++;
                }
            });

        $this->info("$sent hatırlatma gönderildi.");

        return self::SUCCESS;
    }
}
