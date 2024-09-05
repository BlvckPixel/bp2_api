<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            [
                'name' => 'Spark7',
                'email' => 'gdsa006@gmail.com',
                'password' => Hash::make('Qwerty123#'),
                'role_id' => 1,
                'api_token' => Str::random(60),
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Teddy Pahagabia',
                'email' => 'teddy@blvckpixel.com',
                'password' => Hash::make('Password123!'),
                'role_id' => 1,
                'api_token' => Str::random(60),
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Jose',
                'email' => 'jose@blvckpixel.com',
                'password' => Hash::make('Password123!'),
                'role_id' => 3,
                'api_token' => Str::random(60),
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
