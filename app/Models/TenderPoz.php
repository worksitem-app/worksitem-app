<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TenderPoz extends Model
{
    use HasFactory;

    protected $table = 'tender_pozs';

    protected $fillable = [
        'year',
        'period',
        'institution',
        'poz_no',
        'description',
        'unit',
        'unit_price',
        'price_type',
        'material_price',
        'installation_price',
        'profit_included',
        'profit_status',
        'included_profit_rate',
        'vat_included',
        'source_file',
        'source_page',
        'source_note',
        'is_active',
        'long_description',
        'seo_data',
        'faq_data',
        'meta_description',
        'meta_keywords',
        'seo_enriched',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'material_price' => 'decimal:4',
        'installation_price' => 'decimal:4',
        'included_profit_rate' => 'decimal:4',
        'profit_included' => 'boolean',
        'vat_included' => 'boolean',
        'is_active' => 'boolean',
        'seo_enriched' => 'boolean',
        'seo_data' => 'array',
        'faq_data' => 'array',
    ];
}
