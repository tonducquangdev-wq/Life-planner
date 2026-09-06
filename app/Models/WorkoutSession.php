<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkoutSession extends Model
{
    public function exercises()
    {
        // Một buổi tập sẽ có nhiều bài tập thông qua bảng trung gian
        return $this->belongsToMany(Exercise::class, 'workout_session_exercises')
            ->withPivot('sets', 'reps') // Lấy thêm các cột trong bảng trung gian
            ->withTimestamps();
    }
}
