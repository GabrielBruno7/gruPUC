<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CourseSeeder extends Seeder
{
    public function run(): void
    {
        $courses = [
            ['name' => 'Engenharia de Software', 'type' => 'Bacharelado', 'period' => 8],
            ['name' => 'Pedagogia', 'type' => 'Licenciatura', 'period' => 8],
            ['name' => 'Análise e Desenvolvimento de Sistemas', 'type' => 'Tecnólogo', 'period' => 5],
            ['name' => 'Marketing Digital', 'type' => 'Tecnólogo', 'period' => 4],
            ['name' => 'Administração', 'type' => 'Bacharelado', 'period' => 8],
            ['name' => 'Outro Curso Genérico', 'type' => 'Outro', 'period' => 6],
        ];

        foreach ($courses as $course) {
            DB::table('courses')->insert([
                'id' => Str::uuid(),
                'name' => $course['name'],
                'type' => $course['type'],
                'period' => $course['period'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
