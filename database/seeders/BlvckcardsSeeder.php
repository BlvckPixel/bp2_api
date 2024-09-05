<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Blvckbox;
use App\Models\Blvckcard;

class BlvckcardsSeeder extends Seeder
{
    public function run()
    {
        // Get all Blvckboxes
        $blvckboxes = Blvckbox::all();

        // Define Blvckcard data
        $blvckcardsData = [
            [
                'title' => 'Harmonizing Technology with the Human Spirit',
                'slug' => 'harmonizing-technology-with-the-human-spirit',
                'description' => '<p>As urban landscapes evolve...</p>',
                'date' => '2024-05-20',
                'images' => json_encode(['/img1.png', '/img2.png']),
                'blvckbox_id' => $blvckboxes->firstWhere('slug', 'cognitive-cities')->id,
            ],
            [
                'title' => 'Urban Environments as Mirrors to the Collective Consciousness Itself',
                'slug' => 'urban-environments-as-mirrors-to-the-collective-consciousness-itself',
                'description' => '<p>The leap to cognitive cities introduces...</p>',
                'date' => '2024-06-15',
                'images' => json_encode(['/img1.png', '/img2.png']),
                'blvckbox_id' => $blvckboxes->firstWhere('slug', 'cognitive-cities')->id,
            ],
            [
                'title' => 'Placing Human Experience at the Core of Urban Planning',
                'slug' => 'placing-human-experience-at-the-core-of-urban-planning',
                'description' => '<p>This evolution in urban planning and architecture...</p>',
                'date' => '2024-07-10',
                'images' => json_encode(['/img1.png', '/img2.png']),
                'blvckbox_id' => $blvckboxes->firstWhere('slug', 'cognitive-cities')->id,
            ],
            [
                'title' => 'Quantum Computing Breakthrough',
                'slug' => 'quantum-computing-breakthrough',
                'description' => 'A seminar on the latest advancements in quantum computing.',
                'date' => '2024-06-15',
                'images' => json_encode(['/img1.png', '/img2.png']),
                'blvckbox_id' => $blvckboxes->firstWhere('slug', 'quantum-leap')->id,
            ],
           
        ];

        // Insert Blvckcards into the database
        foreach ($blvckcardsData as $data) {
            Blvckcard::create($data);
        }
    }
}
