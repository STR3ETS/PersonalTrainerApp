<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NutritionSettings extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'enabled', 'calorie_adjustment_pct', 
        'diet_preference', 'diet_preference_text', 'injuries'
    ];

    protected $casts = [
        'enabled' => 'boolean',
        'injuries' => 'array'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getCalorieAdjustmentAttribute()
    {
        if (!$this->calorie_adjustment_pct) {
            return 0;
        }
        
        return (int) str_replace(['%', '+'], '', $this->calorie_adjustment_pct);
    }
}
