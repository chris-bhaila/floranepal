<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Plant;
use App\Models\Nursery;

class PlantSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Clear out the old data so you don't get millions of rows when testing
        Plant::truncate();

        $nurseries = Nursery::all();

        if ($nurseries->isEmpty()) {
            $this->command->warn('No nurseries found. Create nurseries first.');
            return;
        }

        $categories = ['flowering', 'succulents', 'herbs', 'trees'];
        $seasons = ['summer', 'autumn', 'winter', 'spring'];
        $sunlight = ['full_sun', 'partial_shade', 'full_shade'];
        $water = ['low', 'moderate', 'high'];
        $locations = ['indoor', 'outdoor', 'both'];

        foreach ($nurseries as $nursery) {
            
            // 2. Set this to 35-50 to properly test the "Show More" button multiple times
            $count = rand(35, 50); 

            for ($i = 0; $i < $count; $i++) {
                $offerPrice = rand(500, 3000);
                $sellingPrice = $offerPrice - rand(50, 500);

                Plant::create([
                    'nursery_id'           => $nursery->id,
                    // 3. fake() will generate random Latin-style words for names
                    'name'                 => ucwords(fake()->words(rand(1, 3), true)), 
                    'scientific_name'      => ucfirst(fake()->words(2, true)),
                    'location'             => $locations[array_rand($locations)],
                    'category'             => $categories[array_rand($categories)],
                    'offer_price'          => $offerPrice,
                    'selling_price'        => $sellingPrice,
                    'stock_quantity'       => rand(1, 50),
                    'description'          => fake()->paragraph(), // Random dummy paragraph
                    'best_season'          => $seasons[array_rand($seasons)],
                    'sunlight_requirement' => $sunlight[array_rand($sunlight)],
                    'water_requirement'    => $water[array_rand($water)],
                    'image'                => null,
                ]);
            }
        }

        $this->command->info('Heavy test data seeded successfully. UI is ready for testing!');
    }
}