<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i = 0; $i <= 50; $i++) {
            $password = 'pass';
            $password .= Str::random(10);
            DB::table('users')->insert([
                'username' => 'test user'.Str::random(10),
                'name' => 'Test'.Str::random(10),
                'email' => 'testmail'.Str::random(10) . '@example.com',
                'password' => Hash::make($password),
            ]);
        }
    }
}
