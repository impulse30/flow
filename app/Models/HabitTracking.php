<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HabitTracking extends Model
{
    protected $fillable = [
        'habit_id',
        'date',
        'completed',
        'notes',
        'fail_reason',
    ];

    public function habit() {
        return $this->belongsTo(Habit::class);
    }
}
