<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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

    public function abbreviationLegend(): HasOne
    {
        return $this->hasOne(AbbreviationLegend::class);
    }

    public function subjectLegend(): HasOne
    {
        return $this->hasOne(SubjectLegend::class);
    }
}
