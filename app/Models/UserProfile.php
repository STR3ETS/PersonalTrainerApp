<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'current_weight_kg', 'height_cm', 'birth_year', 
        'sex', 'activity_level', 'experience_level', 'train_location',
        'equipment', 'weekdays'
    ];

    protected $casts = [
        'equipment' => 'array',
        'weekdays' => 'array',
        'current_weight_kg' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getAgeAttribute()
    {
        return $this->birth_year ? now()->year - $this->birth_year : null;
    }

    public function getBmiAttribute()
    {
        if (!$this->current_weight_kg || !$this->height_cm) {
            return null;
        }
        
        $heightM = $this->height_cm / 100;
        return round($this->current_weight_kg / ($heightM * $heightM), 1);
    }
}
