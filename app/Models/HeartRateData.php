<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HeartRateData extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'max_hr',
        'rest_hr',
        'zone1',
        'zone2',
        'zone3',
        'zone4',
        'zone5',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}