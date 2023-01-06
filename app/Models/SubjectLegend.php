<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubjectLegend extends Model
{
    use HasFactory;

    protected $fillable = [
        'content',
        'name'
    ];

    public function specialization(): BelongsTo
    {
        return $this->belongsTo(Specialization::class);
    }
}
