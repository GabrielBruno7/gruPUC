<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class GroupSeeder extends Seeder
{
    public function run(): void
    {
        $course = DB::table('courses')->inRandomOrder()->first();
        $owner  = DB::table('users')->inRandomOrder()->first();

        DB::table('groups')->insert([
            'id'         => Str::uuid(),
            'name'       => 'The Coders',
            'course_id'  => $course ? $course->id : Str::uuid(),
            'owner_id'   => $owner ? $owner->id : Str::uuid(),
            'period'     => 1,
            'status'     => 'ativo',
            'created_at' => now(),
            'updated_at' => now(),
            'deleted_at' => null,
        ]);
    }
}
