<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Firm extends Model
{
    protected $fillable = [
        'name', 'type', 'services', 'cities', 'district', 'contact_name', 'phone', 'email', 'website', 'tax_no',
        'founded_year', 'description', 'status', 'plan', 'plan_until', 'admin_note', 'approved_at', 'terms_at',
        'legal_type', 'legal_name', 'tax_office', 'mersis_no', 'kep', 'efatura', 'invoice_city', 'invoice_district', 'invoice_address',
        'tax_doc_path', 'tax_verified_at', 'trades', 'subservices', 'districts', 'referrer', 'last_invited_at', 'accepts_jobs',
        'answers', 'answers_at', 'min_job',
    ];

    protected $casts = [
        'services' => 'array', 'cities' => 'array', 'plan_until' => 'datetime', 'approved_at' => 'datetime', 'terms_at' => 'datetime',
        'trades' => 'array', 'subservices' => 'array', 'districts' => 'array', 'tax_verified_at' => 'datetime', 'last_invited_at' => 'datetime',
        'accepts_jobs' => 'boolean', 'answers' => 'array', 'answers_at' => 'datetime',
    ];

    public const EFATURA = ['unknown' => 'Kontrol edilmedi', 'yes' => 'e-Fatura mükellefi', 'no' => 'e-Fatura mükellefi değil (e-Arşiv)'];

    public const STATUSES = ['pending' => 'Onay bekliyor', 'approved' => 'Onaylı', 'rejected' => 'Reddedildi', 'paused' => 'Durduruldu'];

    protected static function booted(): void
    {
        static::creating(function (Firm $f) {
            $f->public_id ??= 'WS-FRM-'.strtoupper(Str::random(6));
            $base = Str::slug(Str::limit($f->name, 80, ''), '-', 'tr') ?: 'firma';
            $slug = $base;
            for ($i = 2; static::query()->where('slug', $slug)->exists(); $i++) {
                $slug = $base.'-'.$i;
            }
            $f->slug = $slug;
        });
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function scopeApproved(Builder $q): Builder
    {
        return $q->where('status', 'approved');
    }

    public function isPro(): bool
    {
        return $this->plan === 'pro' && $this->plan_until && $this->plan_until->isFuture();
    }

    public function typeLabel(): string
    {
        return (string) config("worksitem_firms.types.{$this->type}", $this->type);
    }

    /** İş kolu etiketleri (config/worksitem_trades.php) @return list<string> */
    public function serviceLabels(): array
    {
        $all = (array) config('worksitem_trades');

        return array_values(array_filter(array_map(fn ($k) => $all[$k]['label'] ?? null, (array) $this->trades)));
    }

    /** Fatura belgesi türü: e-Fatura mükellefine e-Fatura, diğerlerine e-Arşiv */
    public function invoiceType(): string
    {
        return $this->efatura === 'yes' ? 'e-Fatura' : 'e-Arşiv';
    }

    public function invoiceTitle(): string
    {
        return (string) ($this->legal_name ?: $this->name);
    }

    /** Türkçe küçük harf + boşluk sadeleştirme (ilçe eşleştirme) */
    public static function norm(?string $s): string
    {
        return trim(preg_replace('/\s+/u', ' ', mb_strtolower(strtr((string) $s, ['I' => 'ı', 'İ' => 'i']), 'UTF-8')));
    }

    /** Bu firma bu iş için uygun mu: iş kolu + il (+ ilçe kısıtı varsa ilçe) */
    public function serves(string $trade, string $city, string $district): bool
    {
        if (! in_array($trade, (array) $this->trades, true) || ! in_array($city, (array) $this->cities, true)) {
            return false;
        }
        $ds = array_filter(array_map([self::class, 'norm'], (array) $this->districts));

        return $ds === [] || in_array(self::norm($district), $ds, true);
    }

    public function maskedPhone(): string
    {
        $p = (string) $this->phone;

        return strlen($p) === 10 ? '0'.substr($p, 0, 3).' *** ** '.substr($p, -2) : '***';
    }
}
