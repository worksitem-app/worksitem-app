<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SponsorApplication extends Model
{
    protected $guarded = ['id'];

    protected $casts = ['categories' => 'array', 'tools' => 'array'];

    public const STATUSES = ['new' => 'Yeni', 'contacted' => 'Görüşüldü', 'offer' => 'Teklif verildi', 'signed' => 'Sözleşme imzalandı', 'rejected' => 'Olumsuz'];

    public const BUDGETS = ['k25' => '25.000 TL\'ye kadar / ay', 'k75' => '25.000–75.000 TL / ay', 'k200' => '75.000–200.000 TL / ay', 'k200p' => '200.000 TL üzeri / ay', 'bilmiyorum' => 'Henüz belirlemedik'];

    public const STARTS = ['hemen' => 'Hemen', 'ay' => 'Önümüzdeki ay', 'ceyrek' => 'Önümüzdeki 3 ay', 'arastirma' => 'Araştırıyoruz'];

    protected static function booted(): void
    {
        static::creating(function (SponsorApplication $a) {
            $a->public_id ??= 'WS-SPN-'.strtoupper(Str::random(6));
        });
    }

    /** Sponsor kategorileri (tüm araçlardan, tekil) */
    public static function categoryOptions(): array
    {
        $c = [];
        foreach ((array) config('worksitem_sponsors.tools') as $t) {
            foreach ((array) ($t['categories'] ?? []) as $k => $l) {
                if ($k !== 'genel') {
                    $c[$k] ??= $l;
                }
            }
        }
        asort($c);

        return $c;
    }

    public static function toolOptions(): array
    {
        return collect((array) config('worksitem_sponsors.tools'))->map(fn ($t) => $t['label'] ?? '')->filter()->sort()->all();
    }
}
