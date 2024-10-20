<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MessageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i = 0; $i < 50; $i++) {
            DB::table('messages')->insert([
                'user_id' => DB::table('users')->inRandomOrder()->first()->id,
                'message' => 'hello here is random string' . Str::random(10),
            ]);
        }
    }
}
