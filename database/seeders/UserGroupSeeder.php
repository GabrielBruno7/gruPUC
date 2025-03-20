<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UserGroupSeeder extends Seeder
{
    public function run(): void
    {
        $users  = DB::table('users')->pluck('id')->toArray();
        $groups = DB::table('groups')->pluck('id')->toArray();

        if (empty($users) || empty($groups)) {
            return;
        }

        $statusOptions = ['pending', 'approved', 'reproved'];

        $data = [];
        foreach ($users as $user) {
            $data[] = [
                'id'         => Str::uuid(),
                'group_id'   => $groups[array_rand($groups)],
                'user_id'    => $user,
                'status'     => $statusOptions[array_rand($statusOptions)], // Escolhe um aleatório
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('user_group')->insert($data);
    }
}
