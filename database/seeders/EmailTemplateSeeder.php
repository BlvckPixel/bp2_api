<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EmailTemplateSeeder extends Seeder
{
    public function run()
    {
        DB::table('email_templatese')->insert([
            'name' => 'Account Activation',
            'subject' => 'Blvckpixel: Account Activation',
            'body' => '
                <h1>Welcome, {{ $user->name }}</h1>
                <p>Thank you for registering. Please click the link below to activate your account:</p>
                <p style="text-align: center;">
                    <a href="{{ env("FRONTEND_URL") }}/activate-account/{{ $token }}" style="padding: 10px 20px; background-color: #000; color: #fff; text-decoration: none;">
                        Activate Account
                    </a>
                </p>
            ',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
