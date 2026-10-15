<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Order extends Model
{
    protected $guarded = ['id'];

    protected $casts = ['amount' => 'float', 'paid_at' => 'datetime', 'invoiced_at' => 'datetime'];

    public const KINDS = ['credits' => 'Yapay zekâ kredisi', 'pro_report' => 'Pro rapor', 'firm_pro' => 'Öne çıkan firma', 'match_fee' => 'Eşleşme bedeli'];

    public function firm(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Firm::class);
    }

    public function net(): float
    {
        return round($this->amount / (1 + (float) config('worksitem_payments.vat_rate', 0.20)), 2);
    }

    public function vat(): float
    {
        return round($this->amount - $this->net(), 2);
    }

    /**
     * Fatura alıcısı: firmaya (eşleşme bedeli, öne çıkan üyelik) vergi bilgileriyle; e-Fatura mükellefine e-Fatura, değilse e-Arşiv.
     * Tüketiciye (kredi, Pro rapor) e-Arşiv; TCKN bilinmediğinden GİB uygulamasındaki 11111111111 kullanılır.
     *
     * @return array{type:string,title:string,tax_no:string,tax_office:string,address:string,email:string,phone:string}
     */
    public function buyer(): array
    {
        $f = $this->firm_id ? $this->firm : (str_starts_with((string) $this->ref, 'firm:') ? Firm::query()->find((int) substr((string) $this->ref, 5)) : null);
        if ($f) {
            return ['type' => $f->invoiceType(), 'title' => $f->invoiceTitle(), 'tax_no' => (string) $f->tax_no, 'tax_office' => (string) $f->tax_office,
                'address' => trim($f->invoice_address.' '.$f->invoice_district.'/'.$f->invoice_city, ' /'), 'email' => (string) $f->email, 'phone' => '0'.$f->phone];
        }

        return ['type' => 'e-Arşiv', 'title' => (string) ($this->name ?: 'Nihai tüketici'), 'tax_no' => '11111111111', 'tax_office' => '',
            'address' => 'İnternet satışı', 'email' => (string) $this->email, 'phone' => $this->phone ? '0'.$this->phone : ''];
    }

    protected static function booted(): void
    {
        static::creating(function (Order $o) {
            $o->public_id ??= 'WS-ODM-'.strtoupper(Str::random(8));
        });
    }

    public function getRouteKeyName(): string
    {
        return 'public_id';
    }

    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }
}
