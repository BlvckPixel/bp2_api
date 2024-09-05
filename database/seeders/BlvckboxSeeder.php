<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Blvckbox;

class BlvckboxSeeder extends Seeder
{
    public function run()
    {
        Blvckbox::create([
            'slug' => 'cognitive-cities',
            'title' => 'Cognitive Cities',
            'subtitle' => '',
            'description' => 'It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout.',
            'date' => null,
            'background' => 'img2.png',
        ]);

        Blvckbox::create([
            'slug' => 'quantum-leap',
            'title' => 'Quantum Leap',
            'subtitle' => 'BLVCKBOOK | the foresight journal',
            'description' => 'An abstract cosmic background with a silhouette of a person\'s profile facing right.',
            'date' => '2024-03-01',
            'background' => 'img2.png',
        ]);

        Blvckbox::create([
            'slug' => 'the-rise-of-ai',
            'title' => 'The Rise of AI',
            'subtitle' => 'BLVCKBOOK: the foresight journal. January 2024',
            'description' => 'An intricate design of a human profile overlaid with digital patterns.',
            'date' => '2024-01-01',
            'background' => 'img2.png',
            'image' => 'img2.png',
        ]);
    }
}