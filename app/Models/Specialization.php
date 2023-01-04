<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Specialization extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'link',
    ];

    public function major(): BelongsTo
    {
        return $this->belongsTo(Major::class);
    }

    public function abbreviationLegends(): HasMany
    {
        return $this->hasMany(AbbreviationLegend::class);
    }

    public function subjectLegend(): HasOne
    {
        return $this->hasOne(SubjectLegend::class);
    }

    public function groups(): HasMany
    {
        return $this->hasMany(Group::class);
    }
}
