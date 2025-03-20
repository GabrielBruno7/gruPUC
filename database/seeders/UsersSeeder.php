<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    public function run(): void
    {
        // Obtém um curso existente para associar o usuário (caso haja cursos já semeados)
        $courses = DB::table('courses')->pluck('id')->toArray();
        $courseId = count($courses) ? $courses[array_rand($courses)] : Str::uuid();

        // Insere usuários de exemplo
        DB::table('users')->insert([
            [
                'id'             => Str::uuid(),
                'name'           => 'John Doe',
                'email'          => 'john.doe@example.com',
                'password'       => Hash::make('password'),
                'role'           => 'student',
                'group_id'       => null,
                'remember_token' => Str::random(10),
                'period'         => 1,
                'course_id'      => $courseId,
                'created_at'     => now(),
                'updated_at'     => now(),
            ],
            [
                'id'             => Str::uuid(),
                'name'           => 'Jane Smith',
                'email'          => 'jane.smith@example.com',
                'password'       => Hash::make('password'),
                'role'           => 'student',
                'group_id'       => null,
                'remember_token' => Str::random(10),
                'period'         => 2,
                'course_id'      => $courseId,
                'created_at'     => now(),
                'updated_at'     => now(),
            ]
        ]);
    }
}
