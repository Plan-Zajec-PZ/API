<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Faculty extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'link',
    ];

    public function lecturers(): HasMany
    {
        return $this->hasMany(Lecturer::class);
    }

    public function majors(): HasMany
    {
        return $this->hasMany(Major::class);
    }

    public function trackNumber(): BelongsTo
    {
        return $this->belongsTo(TrackNumber::class);
    }
}
