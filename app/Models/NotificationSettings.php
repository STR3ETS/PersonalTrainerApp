<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationSettings extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'channel'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isEnabled()
    {
        return $this->channel !== 'none';
    }
}
