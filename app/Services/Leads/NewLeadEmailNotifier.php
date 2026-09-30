<?php

namespace App\Services\Leads;

use App\Filament\Resources\LeadResource;
use App\Models\Lead;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class NewLeadEmailNotifier
{
    public function notify(Lead $lead): void
    {
        $recipient = trim((string) config(
            'worksitem_leads.notification_email'
        ));

        if ($recipient === '') {
            Log::warning('Lead email recipient missing', [
                'lead_id' => $lead->id,
            ]);

            return;
        }

        $qualityLabel = match (
            strtolower((string) $lead->lead_quality)
        ) {
            'hot' => '🔥 HOT',
            'warm' => '☀️ WARM',
            'cold' => '❄️ COLD',
            default => strtoupper(
                (string) ($lead->lead_quality ?: 'UNKNOWN')
            ),
        };

        $location = collect([
            $lead->city,
            $lead->district,
        ])->filter()->implode(' / ');

        $budget = $lead->estimated_cost !== null
            ? number_format(
                (float) $lead->estimated_cost,
                0,
                ',',
                '.'
            ) . ' TL'
            : 'Belirtilmedi';

        $areaLabel = $lead->area !== null
            ? number_format(
                (float) $lead->area,
                0,
                ',',
                '.'
            ) . ' m²'
            : 'Belirtilmedi';

        $phoneNormalized = (
            $lead->phone_normalized
            ?: preg_replace('/\D+/', '', (string) $lead->phone)
        );

        $adminUrl = LeadResource::getUrl(
            'edit',
            ['record' => $lead]
        );

        $subject = sprintf(
            'WORKSITEM | Yeni Lead | %s | %s',
            $qualityLabel,
            $lead->name ?: 'İsimsiz'
        );

        Mail::send(
            'emails.new-lead',
            [
                'lead' => $lead,
                'qualityLabel' => $qualityLabel,
                'location' => $location ?: 'Belirtilmedi',
                'budget' => $budget,
                'areaLabel' => $areaLabel,
                'phoneNormalized' => $phoneNormalized,
                'adminUrl' => $adminUrl,
            ],
            function ($message) use (
                $recipient,
                $subject
            ): void {
                $message
                    ->to($recipient)
                    ->subject($subject);
            }
        );

        Log::info('New lead email sent', [
            'lead_id' => $lead->id,
            'public_id' => $lead->public_id,
            'recipient' => $recipient,
        ]);
    }
}
