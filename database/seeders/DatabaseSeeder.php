<?php

namespace Database\Seeders;

use App\Models\Faculty;
use App\Models\Group;
use App\Models\Task;
use App\Models\TaskSubmission;
use App\Models\User;
use App\Models\Week;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Super Admin
        User::create([
            'name' => 'Super Admin',
            'email' => 'admin@tisu.uz',
            'password' => Hash::make('password'),
            'role' => 'super_admin',
            'is_active' => true,
        ]);

        // Fakultetlar
        $faculties = collect([
            'Axborot texnologiyalari',
            'Iqtisodiyot',
            'Huquqshunoslik',
            'Pedagogika',
        ])->map(fn ($name) => Faculty::create(['name' => $name]));

        // Dekanlar
        foreach ($faculties as $faculty) {
            $dean = User::create([
                'name' => $faculty->name . ' Dekani',
                'email' => 'dean' . $faculty->id . '@tisu.uz',
                'password' => Hash::make('password'),
                'role' => 'dean',
                'faculty_id' => $faculty->id,
                'is_active' => true,
            ]);

            $faculty->update(['dean_id' => $dean->id]);
        }

        // Kuratorlar va Guruhlar
        $groupNames = ['A', 'B', 'C'];
        foreach ($faculties as $faculty) {
            foreach ($groupNames as $i => $suffix) {
                $curator = User::create([
                    'name' => "Kurator {$faculty->id}-{$suffix}",
                    'email' => "curator{$faculty->id}{$suffix}@tisu.uz",
                    'password' => Hash::make('password'),
                    'role' => 'curator',
                    'faculty_id' => $faculty->id,
                    'is_active' => true,
                ]);

                Group::create([
                    'name' => "{$faculty->id}0" . ($i + 1) . "-guruh",
                    'faculty_id' => $faculty->id,
                    'curator_id' => $curator->id,
                ]);
            }
        }

        // Haftalar (joriy oy uchun)
        $year = now()->year;
        $month = now()->month;

        for ($w = 1; $w <= 4; $w++) {
            $week = Week::create([
                'year' => $year,
                'month' => $month,
                'week_number' => $w,
                'title' => "{$w}-hafta rejasi",
                'description' => "{$w}-hafta uchun kurator vazifalari",
            ]);

            // Har bir hafta uchun 7 kun — har kunga 1 ta vazifa
            for ($d = 1; $d <= 7; $d++) {
                Task::create([
                    'week_id' => $week->id,
                    'title' => "Vazifa {$w}-{$d}",
                    'description' => "{$d}-kun uchun vazifa tavsifi",
                    'day_of_week' => $d,
                    'sort_order' => $d,
                ]);
            }
        }

        $groups = Group::all();

        // ====================================================================
        // ANIQ STATUS PATTERN — har bir guruhga kuniga belgilangan status
        // Fakultet 1 (AT):       asosan completed
        // Fakultet 2 (Iqtisodiy): aralash
        // Fakultet 3 (Huquq):    asosan not_completed
        // Fakultet 4 (Ped):      asosan under_review
        // ====================================================================
        $patterns = [
            // Fakultet 1 — AT (guruhlar: 101, 102, 103)
            '101-guruh' => [
                1 => 'completed', 2 => 'completed', 3 => 'completed',
                4 => 'completed', 5 => 'completed', 6 => 'completed', 7 => 'completed',
            ],
            '102-guruh' => [
                1 => 'completed', 2 => 'completed', 3 => 'completed',
                4 => 'completed', 5 => 'under_review', 6 => 'completed', 7 => 'completed',
            ],
            '103-guruh' => [
                1 => 'completed', 2 => 'completed', 3 => 'not_completed',
                4 => 'completed', 5 => 'completed', 6 => 'not_completed', 7 => 'completed',
            ],

            // Fakultet 2 — Iqtisodiyot (guruhlar: 201, 202, 203)
            '201-guruh' => [
                1 => 'completed', 2 => 'not_completed', 3 => 'completed',
                4 => 'under_review', 5 => 'rejected', 6 => 'not_completed', 7 => 'completed',
            ],
            '202-guruh' => [
                1 => 'not_completed', 2 => 'not_completed', 3 => 'under_review',
                4 => 'not_completed', 5 => 'not_completed', 6 => 'rejected', 7 => 'not_completed',
            ],
            '203-guruh' => [
                1 => 'under_review', 2 => 'completed', 3 => 'rejected',
                4 => 'completed', 5 => 'completed', 6 => 'under_review', 7 => 'rejected',
            ],

            // Fakultet 3 — Huquqshunoslik (guruhlar: 301, 302, 303)
            '301-guruh' => [
                1 => 'not_completed', 2 => 'not_completed', 3 => 'not_completed',
                4 => 'not_completed', 5 => 'not_completed', 6 => 'rejected', 7 => 'not_completed',
            ],
            '302-guruh' => [
                1 => 'not_completed', 2 => 'rejected', 3 => 'not_completed',
                4 => 'not_completed', 5 => 'not_completed', 6 => 'not_completed', 7 => 'rejected',
            ],
            '303-guruh' => [
                1 => 'rejected', 2 => 'rejected', 3 => 'rejected',
                4 => 'not_completed', 5 => 'rejected', 6 => 'rejected', 7 => 'rejected',
            ],

            // Fakultet 4 — Pedagogika (guruhlar: 401, 402, 403)
            '401-guruh' => [
                1 => 'under_review', 2 => 'under_review', 3 => 'under_review',
                4 => 'under_review', 5 => 'completed', 6 => 'under_review', 7 => 'under_review',
            ],
            '402-guruh' => [
                1 => 'under_review', 2 => 'under_review', 3 => 'completed',
                4 => 'under_review', 5 => 'under_review', 6 => 'under_review', 7 => 'under_review',
            ],
            '403-guruh' => [
                1 => 'under_review', 2 => 'completed', 3 => 'under_review',
                4 => 'rejected', 5 => 'under_review', 6 => 'under_review', 7 => 'completed',
            ],
        ];

        // Barcha haftalar uchun topshirishlarni yaratish
        $weeks = Week::with('tasks')->get();

        foreach ($weeks as $week) {
            foreach ($groups as $group) {
                $groupPattern = $patterns[$group->name] ?? [
                    1 => 'not_completed', 2 => 'not_completed', 3 => 'not_completed',
                    4 => 'not_completed', 5 => 'not_completed', 6 => 'not_completed', 7 => 'not_completed',
                ];

                foreach ($week->tasks as $task) {
                    $dayNum = $task->day_of_week instanceof \App\Enums\DayOfWeek
                        ? (int) $task->day_of_week->value
                        : (int) $task->day_of_week;

                    TaskSubmission::create([
                        'task_id' => $task->id,
                        'group_id' => $group->id,
                        'status' => $groupPattern[$dayNum] ?? 'not_completed',
                        'submitted_at' => now()->subDays(rand(0, 3)),
                    ]);
                }
            }
        }
    }
}
