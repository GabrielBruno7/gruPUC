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
        $courses = DB::table('courses')->pluck('id')->toArray();
        $courseId = count($courses) ? $courses[array_rand($courses)] : Str::uuid();

        DB::table('users')->insert([
            [
                'id'             => Str::uuid(),
                'name'           => 'Lucas Lima',
                'email'          => 'gabriel@gmail.com',
                'password'       => Hash::make('password'),
                'role'           => 'student',
                'group_id'       => null,
                'remember_token' => Str::random(10),
                'period'         => 5,
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
                'period'         => 5,
                'course_id'      => $courseId,
                'created_at'     => now(),
                'updated_at'     => now(),
            ]
        ]);
    }
}
