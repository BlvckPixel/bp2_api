<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PackagesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('packages')->insert([
            [
                'name' => 'Basic Package',
                'price' => 19.99,
                'features' => json_encode(['Feature 1', 'Feature 2', 'Feature 3']),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Standard Package',
                'price' => 39.99,
                'features' => json_encode(['Feature 1', 'Feature 2', 'Feature 3', 'Feature 4']),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Premium Package',
                'price' => 59.99,
                'features' => json_encode(['Feature 1', 'Feature 2', 'Feature 3', 'Feature 4', 'Feature 5']),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
