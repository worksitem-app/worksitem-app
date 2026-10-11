<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Project extends Model
{
    /**
     * Mass assignment sınırı: id / public_token / estimated_cost dışarıdan set edilemez.
     */
    protected $fillable = [
        'project_type_id',
        'name',
        'city',
        'district',
        'land_area',
        'construction_area',
        'status',
        'notes',
        'estimated_cost',
    ];

    protected static function booted(): void
    {
        static::creating(function (Project $project) {
            $project->public_token ??= (string) Str::ulid();
        });
    }

    /**
     * Public URL'lerde sıralı ID yerine tahmin edilemez token.
     */
    public function getRouteKeyName(): string
    {
        return 'public_token';
    }

    public function projectType()
    {
        return $this->belongsTo(ProjectType::class, 'project_type_id');
    }

    public function answers()
    {
        return $this->hasMany(ProjectAnswer::class, 'project_id');
    }
}
