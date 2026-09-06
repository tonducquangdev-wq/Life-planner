<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Exercise extends Model
{
    public function workoutSessions()
{
    // Một bài tập có thể xuất hiện trong nhiều buổi tập khác nhau
    return $this->belongsToMany(WorkoutSession::class, 'workout_session_exercises')
                ->withPivot('sets', 'reps')
                ->withTimestamps();
}

}
