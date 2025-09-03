<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'is_temp_account',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function profile()
    {
        return $this->hasOne(UserProfile::class);
    }

    public function activeGoal()
    {
        return $this->hasOne(FitnessGoal::class)->where('is_active', true);
    }

    public function goals()
    {
        return $this->hasMany(FitnessGoal::class);
    }

    public function activeTrainingSchedule()
    {
        return $this->hasOne(TrainingSchedule::class)->where('is_active', true);
    }

    public function trainingSchedules()
    {
        return $this->hasMany(TrainingSchedule::class);
    }

    public function performanceRecords()
    {
        return $this->hasMany(PerformanceRecord::class);
    }

    public function nutritionSettings()
    {
        return $this->hasOne(NutritionSettings::class);
    }

    public function notificationSettings()
    {
        return $this->hasOne(NotificationSettings::class);
    }

    public function getLatestBenchPressRecord()
    {
        return $this->performanceRecords()
            ->where('exercise_type', 'bench_press')
            ->latest()
            ->first();
    }
}
