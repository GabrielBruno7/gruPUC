<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class InvitationSeeder extends Seeder
{
    public function run(): void
    {
        $group   = DB::table('groups')->inRandomOrder()->first();
        $inviter = DB::table('users')->inRandomOrder()->first();

        DB::table('invitations')->insert([
            'id'         => Str::uuid(),
            'group_id'   => $group ? $group->id : Str::uuid(),
            'invited_by' => $inviter ? $inviter->id : Str::uuid(),
            'email'      => $inviter ? $inviter->email : 'default@example.com',
            'status'     => 'pending',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
