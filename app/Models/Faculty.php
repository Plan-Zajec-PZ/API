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
        'tracking_number_id'
    ];

    public function lecturers(): HasMany
    {
        return $this->hasMany(Lecturer::class);
    }

    public function majors(): HasMany
    {
        return $this->hasMany(Major::class);
    }

    public function trackingNumber(): BelongsTo
    {
        return $this->belongsTo(TrackingNumber::class);
    }
}
