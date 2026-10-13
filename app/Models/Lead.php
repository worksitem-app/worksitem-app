<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Lead extends Model
{
    use HasFactory;

    protected $fillable = [
        'public_id',
        'ref_code',
        'name',
        'phone',
        'phone_normalized',
        'email',
        'source',
        'project_type',
        'notes',
        'project_data',
        'estimated_cost',
        'estimated_min',
        'estimated_max',
        'budget_confirmed',
        'lead_score',
        'lead_quality',
        'sales_ready',
        'status',
        'city',
        'district',
        'area',
        'timeline',
        'utm_source',
        'utm_medium',
        'utm_campaign',
        'utm_content',
        'utm_term',
        'landing_page',
        'referrer',
        'price_version',
        'price_snapshot',
        'contact_consent',
        'marketing_consent',
        'consent_version',
        'consent_at',
        'ip_hash',
        'assigned_user_id',
        'first_contact_at',
        'next_action_at',
        'qualified_at',
        'proposal_sent_at',
        'won_at',
        'lost_at',
        'last_activity_at',
        'loss_reason',
        'inspection_status',
        'inspection_accepted_at',
        'inspection_fee',
        'inspection_terms_version',
        'inspection_paid_at',
        'inspection_payment_method',
        'inspection_data',
        'inspection_appointment_at',
        'inspection_address',
        'inspection_reminder_sent_at',
    ];

    protected $casts = [
        'project_data' => 'array',
        'price_snapshot' => 'array',
        'budget_confirmed' => 'boolean',
        'sales_ready' => 'boolean',
        'contact_consent' => 'boolean',
        'marketing_consent' => 'boolean',
        'estimated_cost' => 'decimal:2',
        'estimated_min' => 'decimal:2',
        'estimated_max' => 'decimal:2',
        'area' => 'decimal:2',
        'consent_at' => 'datetime',
        'first_contact_at' => 'datetime',
        'next_action_at' => 'datetime',
        'qualified_at' => 'datetime',
        'proposal_sent_at' => 'datetime',
        'won_at' => 'datetime',
        'lost_at' => 'datetime',
        'last_activity_at' => 'datetime',
        'inspection_accepted_at' => 'datetime',
        'inspection_paid_at' => 'datetime',
        'inspection_data' => 'array',
        'inspection_appointment_at' => 'datetime',
        'inspection_reminder_sent_at' => 'datetime',
    ];

    /** Müşteriye gösterilen kısa talep no (SMS, telefon): WS-7K3M9P — karışan harfler (0/O, 1/I) yok */
    public static function newRefCode(): string
    {
        $abc = 'ACDEFGHJKLMNPRTUVWXY3479';
        do {
            $code = 'WS-';
            for ($i = 0; $i < 6; $i++) {
                $code .= $abc[random_int(0, strlen($abc) - 1)];
            }
        } while (static::query()->where('ref_code', $code)->exists());

        return $code;
    }

    /** Kısa talep no; eski kayıtlarda yoksa uzun numara */
    public function displayNo(): string
    {
        return (string) ($this->ref_code ?: $this->public_id);
    }

    protected static function booted(): void
    {
        static::creating(function (Lead $lead): void {
            $lead->public_id ??= (string) Str::ulid();
            $lead->ref_code ??= self::newRefCode();
            $lead->last_activity_at ??= now();

            if (!$lead->phone_normalized && $lead->phone) {
                $lead->phone_normalized = self::normalizePhone($lead->phone);
            }
        });

        // Randevu girildiğinde/değiştiğinde müşteriye SMS; hatırlatma yeniden gönderilebilsin
        static::updating(function (Lead $lead): void {
            if ($lead->isDirty('inspection_appointment_at')) {
                $lead->inspection_reminder_sent_at = null;
            }
        });
        static::updated(function (Lead $lead): void {
            if ($lead->wasChanged('inspection_appointment_at') && $lead->inspection_appointment_at?->isFuture()) {
                app(\App\Services\Sms\CustomerSms::class)->appointmentSet($lead);
            }
        });

        static::updating(function (Lead $lead): void {
            $lead->last_activity_at = now();

            if ($lead->isDirty('phone')) {
                $lead->phone_normalized = self::normalizePhone($lead->phone);
            }

            if (!$lead->isDirty('status')) {
                return;
            }

            $status = (string) $lead->status;

            if (
                $status !== 'yeni'
                && $lead->first_contact_at === null
            ) {
                $lead->first_contact_at = now();
            }

            if (
                in_array($status, [
                    'nitelikli',
                    'kesif_planlandi',
                    'teklif_hazirlaniyor',
                    'teklif_verildi',
                    'takipte',
                    'kazanildi',
                ], true)
                && $lead->qualified_at === null
            ) {
                $lead->qualified_at = now();
            }

            if (
                in_array($status, [
                    'teklif_verildi',
                    'takipte',
                    'kazanildi',
                ], true)
                && $lead->proposal_sent_at === null
            ) {
                $lead->proposal_sent_at = now();
            }

            if ($status === 'kazanildi') {
                $lead->won_at ??= now();
                $lead->lost_at = null;
            }

            if (in_array($status, ['kaybedildi', 'iptal'], true)) {
                $lead->lost_at ??= now();
                $lead->won_at = null;
            }
        });
    }

    private static function normalizePhone(?string $phone): ?string
    {
        $digits = preg_replace('/\D+/', '', (string) $phone);

        if (str_starts_with($digits, '0090')) {
            $digits = substr($digits, 2);
        }

        if (str_starts_with($digits, '0') && strlen($digits) === 11) {
            $digits = '90' . substr($digits, 1);
        }

        if (strlen($digits) === 10) {
            $digits = '90' . $digits;
        }

        return preg_match('/^90[2-5]\d{9}$/', $digits)
            ? $digits
            : null;
    }
}
