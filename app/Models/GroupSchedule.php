<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GroupSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'content',
    ];

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }
}
