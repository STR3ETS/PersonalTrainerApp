<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrainingBackground extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'background',
        'current_frequency',
        'current_activities',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}