<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use \App\Models\MessageReply;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ReplySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i = 0; $i < 10; $i++) {
            DB::table('message_replies')->insert([
                'user_id' => DB::table('users')->inRandomOrder()->first()->id,
                'reply_to' => DB::table('messages')->inRandomOrder()->first()->id,
                'id' => DB::table('messages')->inRandomOrder()->first()->id,
            ]);
        }
    }
}
