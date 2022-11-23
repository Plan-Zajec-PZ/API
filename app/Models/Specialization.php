<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
}
