<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class FitnessGoal extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'goal_type', 'target_weight_kg', 'fit_goal_text',
        'target_date', 'is_active'
    ];

    protected $casts = [
        'target_date' => 'date',
        'target_weight_kg' => 'decimal:2',
        'is_active' => 'boolean'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getDaysUntilTargetAttribute()
    {
        if (!$this->target_date) {
            return null;
        }
        
        return now()->diffInDays($this->target_date, false);
    }

    public function getWeightToLoseGainAttribute()
    {
        if (!$this->target_weight_kg || !$this->user->profile) {
            return null;
        }
        
        return $this->target_weight_kg - $this->user->profile->current_weight_kg;
    }
}
