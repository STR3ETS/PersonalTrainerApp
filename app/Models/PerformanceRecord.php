<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PerformanceRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'exercise_type', 'weight_kg', 'reps', 'mode'
    ];

    protected $casts = [
        'weight_kg' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getEstimated1RmAttribute()
    {
        if (!$this->weight_kg || !$this->reps) {
            return null;
        }

        // Brzycki formule: 1RM = weight / (1.0278 - (0.0278 * reps))
        if ($this->reps == 1) {
            return $this->weight_kg;
        }

        return round($this->weight_kg / (1.0278 - (0.0278 * $this->reps)), 1);
    }
}
