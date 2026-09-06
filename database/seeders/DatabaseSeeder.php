<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Subject;
use App\Models\Assignment;
use App\Models\Schedule;
use App\Models\WorkoutPlan;
use App\Models\WorkoutSession;
use App\Models\Exercise;
use App\Models\WorkoutCheckin;
use App\Models\Goal;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Tạo sẵn thư viện bài tập gốc trước (Bảng cố định)
        $exercises = [
            ['name' => 'Hít đất (Push up)', 'muscle_group' => 'Ngực'],
            ['name' => 'Squat (Gánh đùi)', 'muscle_group' => 'Chân'],
            ['name' => 'Plank', 'muscle_group' => 'Bụng'],
            ['name' => 'Kéo xà (Pull up)', 'muscle_group' => 'Lưng'],
            ['name' => 'Chạy bộ (Treadmill)', 'muscle_group' => 'Cardio'],
        ];
        foreach ($exercises as $ex) {
            Exercise::create($ex);
        }
        $allExercises = Exercise::all();

        // 2. Tạo ra 5 người dùng mẫu để test hệ thống
        User::factory(5)->create()->each(function ($user) use ($allExercises) {

            // Với mỗi user: Tạo 3 môn học
            Subject::factory(3)->create(['user_id' => $user->id])->each(function ($subject) {
                // Với mỗi môn học: Tạo 2 bài tập
                Assignment::factory(2)->create(['subject_id' => $subject->id]);
            });

            // Với mỗi user: Tạo 4 lịch trình cá nhân
            Schedule::factory(4)->create(['user_id' => $user->id]);

            // Với mỗi user: Tạo 2 mục tiêu cá nhân
            Goal::factory(2)->create(['user_id' => $user->id]);

            // Với mỗi user: Tạo 1 kế hoạch tập luyện
            WorkoutPlan::factory(1)->create(['user_id' => $user->id])->each(function ($plan) use ($allExercises) {

                // Tạo 3 buổi check-in điểm danh tập luyện
                WorkoutCheckin::factory(3)->create(['workout_plan_id' => $plan->id]);

                // Tạo 3 buổi tập nhỏ (Thứ 2, 4, 6) cho kế hoạch này
                WorkoutSession::factory(3)->create(['workout_plan_id' => $plan->id])->each(function ($session) use ($allExercises) {

                    // Lấy ngẫu nhiên 2 bài tập từ thư viện gốc gán vào buổi tập này (Bảng trung gian)
                    $randomExercises = $allExercises->random(2);

                    foreach ($randomExercises as $exercise) {
                        $session->exercises()->attach($exercise->id, [
                            'sets' => rand(3, 5),
                            'reps' => rand(8, 15),
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                });
            });
        });
    }
}
