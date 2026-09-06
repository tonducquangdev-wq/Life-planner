<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\WorkoutPlan;

class WorkoutController extends Controller
{
    public function index()
    {
        // Lấy User đầu tiên trong DB để test dữ liệu seeder
        $user = User::first();

        // Lấy kế hoạch tập luyện của user đó, kèm theo các buổi tập (sessions)
        // và các bài tập cụ thể trong từng buổi tập đó
        $workoutPlan = WorkoutPlan::where('user_id', $user->id)
            ->with(['workoutSessions.exercises'])
            ->first();

        // Trả dữ liệu về view
        return view('workout.index', compact('user', 'workoutPlan'));
    }
}
