<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Country;
use App\Models\PromoCode;

class MalaysiaSeeder extends Seeder
{
    public function run(): void
    {
        Country::updateOrCreate(['slug' => 'malaysia'], [
            'name'          => 'Malaysia',
            'flag'          => '🇲🇾',
            'currency'      => 'MYR',
            'hero_image'    => 'https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=1600',
            'hero_title'    => 'Truly Malaysia',
            'tagline'       => 'Truly Asia',
            'description'   => 'Explore the beauty of Malaysia with our curated tours and packages — from the twin towers of KL to the pristine islands of Langkawi.',
            'tour_count'    => 0,
            'package_count' => 0,
            'is_active'     => true,
        ]);

        Country::updateOrCreate(['slug' => 'india'], [
            'name'          => 'India',
            'flag'          => '🇮🇳',
            'currency'      => 'INR',
            'hero_image'    => 'https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=1600',
            'hero_title'    => 'Incredible India',
            'tagline'       => 'Atithi Devo Bhava',
            'description'   => 'Discover the rich culture, stunning landscapes, and vibrant heritage of India.',
            'tour_count'    => 0,
            'package_count' => 0,
            'is_active'     => true,
        ]);

        $this->command->info('✅ Malaysia & India country records created!');
    }
}
