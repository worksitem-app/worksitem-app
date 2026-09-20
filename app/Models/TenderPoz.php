<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TenderPoz extends Model
{
    use HasFactory;
    
    protected $table = 'tender_pozs';
    protected $guarded = [];
}
