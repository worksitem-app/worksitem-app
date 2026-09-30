<?php

namespace App\Services\Leads;

use App\Filament\Resources\LeadResource;
use App\Models\Lead;
use App\Models\User;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class SalesLeadNotifier
{
    public function notify(Lead $lead): int
    {
        if (
            !$lead->sales_ready
            || !in_array($lead->lead_quality, ['hot', 'warm'], true)
        ) {
            return 0;
        }

        $recipients = User::query()
            ->where('is_admin', true)
            ->get();

        if ($recipients->isEmpty()) {
            Log::warning('Sales lead notification recipient not found', [
                'lead_id' => $lead->id,
            ]);

            return 0;
        }

        if ($lead->assigned_user_id === null) {
            $lead->update([
                'assigned_user_id' => $recipients->first()->id,
            ]);
        }

        $qualityLabel = $lead->lead_quality === 'hot'
            ? '🔥 HOT'
            : '☀️ WARM';

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

        $bodyParts = [
            $lead->name,
            $lead->project_type ?: 'Proje türü belirtilmedi',
            $location ?: 'Konum belirtilmedi',
            'Bütçe: ' . $budget,
            'Skor: ' . $lead->lead_score . '/100',
        ];

        $notification = Notification::make()
            ->title($qualityLabel . ' — Yeni satış talebi')
            ->body(implode(' · ', $bodyParts))
            ->icon(
                $lead->lead_quality === 'hot'
                    ? 'heroicon-o-fire'
                    : 'heroicon-o-sun'
            )
            ->color(
                $lead->lead_quality === 'hot'
                    ? 'danger'
                    : 'warning'
            )
            ->persistent()
            ->actions([
                Action::make('open')
                    ->label('Lead’i Aç')
                    ->button()
                    ->url(LeadResource::getUrl('edit', [
                        'record' => $lead,
                    ])),

                Action::make('whatsapp')
                    ->label('WhatsApp')
                    ->url(
                        'https://wa.me/' . (
                            $lead->phone_normalized
                            ?: preg_replace('/\D+/', '', $lead->phone)
                        )
                    )
                    ->openUrlInNewTab(),

                Action::make('call')
                    ->label('Ara')
                    ->url(
                        'tel:+' . (
                            $lead->phone_normalized
                            ?: preg_replace('/\D+/', '', $lead->phone)
                        )
                    ),
            ]);

        /*
         * Filament DatabaseNotification sınıfı ShouldQueue kullanır.
         * Hostinger ortamında sürekli queue worker bulunmadığı için
         * bildirimi veritabanına senkron ve anlık olarak kaydediyoruz.
         */
        foreach ($recipients as $recipient) {
            $recipient->notifyNow($notification->toDatabase());
        }

        Log::info('Sales-ready lead panel notification sent', [
            'lead_id' => $lead->id,
            'recipient_count' => $recipients->count(),
        ]);

        return $recipients->count();
    }
}
