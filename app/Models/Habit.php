<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Habit extends Model
{
    protected $fillable =[
        'user_id',
        'name',
        'description',
        'category',
        'frequency',
        'target',
        'color',
        'icon',
        'is_active',
        'current_streak',
        'longest_streak',
        'total_completions',
        'reminder_time',
        'reminder_days',
        'difficulty',

    ];

    protected $casts = [
        'reminder_days' => 'array',
        'is_active' => 'boolean'
    ];


    public function user() {
        return $this->belongsTo(User::class);
    }

    public function trackings() {
        return $this->hasMany(HabitTracking::class);
    }


}
