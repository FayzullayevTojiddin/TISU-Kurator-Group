<?php

namespace Database\Seeders;

use App\Models\Faculty;
use Illuminate\Database\Seeder;

class FacultySeeder extends Seeder
{
    public function run(): void
    {
        $faculties = [
            'Iqtisodiyot va axborot texnologiyalari',
            'Pedagogika va ijtimoiy-gumanitar fanlar',
            'Tibbiyot',
        ];

        foreach ($faculties as $name) {
            Faculty::create([
                'name' => $name,
                'is_active' => true,
            ]);
        }
    }
}
