<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $guarded = [];

    // Projenin tipine (Villa, Tadilat vb.) olan bağlantısı
    public function projectType()
    {
        return $this->belongsTo(ProjectType::class, 'project_type_id');
    }

    // Projenin cevaplarına olan bağlantısı (ileride lazım olacak)
    public function answers()
    {
        return $this->hasMany(ProjectAnswer::class, 'project_id');
    }
}
