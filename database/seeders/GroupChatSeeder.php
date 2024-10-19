<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class GroupChatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i = 0; $i <= 50; $i++) {
            DB::table('group_chats')->insert([
                'group_owner' => DB::table('users')->inRandomOrder()->first()->id,
                'name' => 'Group Chat' . Str::random(10),
            ]);
        }
    }
}
