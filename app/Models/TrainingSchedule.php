<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrainingSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'days_per_week', 'session_minutes', 'weekdays', 'is_active'
    ];

    protected $casts = [
        'weekdays' => 'array',
        'is_active' => 'boolean'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getWeekdayLabelsAttribute()
    {
        $labels = [
            'mon' => 'Maandag',
            'tue' => 'Dinsdag', 
            'wed' => 'Woensdag',
            'thu' => 'Donderdag',
            'fri' => 'Vrijdag',
            'sat' => 'Zaterdag',
            'sun' => 'Zondag'
        ];

        return collect($this->weekdays ?? [])
            ->map(fn($day) => $labels[$day] ?? $day)
            ->toArray();
    }
}
