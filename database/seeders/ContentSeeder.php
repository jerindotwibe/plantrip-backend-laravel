<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Review;
use App\Models\Office;
use App\Models\Setting;
use App\Models\Destination;
use App\Models\DayTour;
use App\Models\TourPackage;

class ContentSeeder extends Seeder
{
    public function run(): void
    {
        // ── Reviews / Testimonials ────────────────────────────────────────────
        $reviews = [
            [
                'name'        => 'Arun M.',
                'trip'        => 'Kuala Lumpur Tour',
                'rating'      => 5,
                'comment'     => 'Absolutely amazing experience! The guide was knowledgeable and friendly. Everything was perfectly organised.',
                'date'        => 'October 2026',
                'is_featured' => true,
                'is_active'   => true,
            ],
            [
                'name'        => 'Sarah L.',
                'trip'        => 'Penang Experience',
                'rating'      => 5,
                'comment'     => 'Seamless booking and professional service. The cultural tour made our trip truly unforgettable. Will definitely book again.',
                'date'        => 'September 2026',
                'is_featured' => true,
                'is_active'   => true,
            ],
            [
                'name'        => 'Alisha R.',
                'trip'        => 'Langkawi Trip',
                'rating'      => 4,
                'comment'     => 'The trip was seamless and beautiful! The guides were knowledgeable and the accommodations were perfect.',
                'date'        => 'August 2026',
                'is_featured' => true,
                'is_active'   => true,
            ],
        ];

        foreach ($reviews as $r) {
            Review::updateOrCreate(['name' => $r['name'], 'trip' => $r['trip']], $r);
        }

        // ── Branch Offices ────────────────────────────────────────────────────
        $offices = [
            [
                'title'         => 'Thailand Office',
                'address'       => 'Bangkok, Thailand',
                'phone'         => '+60106661747',
                'email'         => 'info@plantriponline.com',
                'map_embed_url' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3875.8037258235813!2d100.58340297455838!3d13.730329797791342!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x30e29face0fe1c5f%3A0x946b2c52565971a8!2sTIC%20Holidays%20Co.%2C%20Ltd!5e0!3m2!1sen!2sin!4v1760425553469!5m2!1sen!2sin',
                'sort_order'    => 1,
                'is_active'     => true,
            ],
            [
                'title'         => 'Malaysia Office',
                'address'       => 'Kuala Lumpur, Malaysia',
                'phone'         => '+60106661747',
                'email'         => 'info@plantriponline.com',
                'map_embed_url' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d497.968414009103!2d101.74160738491821!3d3.161160650012506!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31cc374b36465787%3A0xa278f30e45c2ecb6!2sTIC%20Holidays%20SDN.%20BHD!5e0!3m2!1sen!2sin!4v1760425638175!5m2!1sen!2sin',
                'sort_order'    => 2,
                'is_active'     => true,
            ],
            [
                'title'         => 'India Office',
                'address'       => 'Kochi, Kerala',
                'phone'         => '+60106661747',
                'email'         => 'info@plantriponline.com',
                'map_embed_url' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3928.87403894727!2d76.3088994745088!3d10.027251872566273!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3b080da789b465e1%3A0x17d5b462b95c2f5e!2sEurasia%20Holidays!5e0!3m2!1sen!2sin!4v1760425678415!5m2!1sen!2sin',
                'sort_order'    => 3,
                'is_active'     => true,
            ],
        ];

        foreach ($offices as $o) {
            Office::updateOrCreate(['title' => $o['title']], $o);
        }

        // ── Site Settings ─────────────────────────────────────────────────────
        $settings = [
            'brand_name'       => 'PlantripOnline',
            'brand_short'      => 'Plantrip',
            'contact_phone'    => '+60106661747',
            'contact_phone_display' => '+60 10 666 1747',
            'contact_email'    => 'info@plantriponline.com',
            'contact_whatsapp' => '+60106661747',
            'facebook_url'     => 'https://www.facebook.com/TICTours.BKK/',
            'instagram_url'    => 'https://www.instagram.com/tictoursthailand/?hl=en',
            'twitter_url'      => 'https://x.com/tictoursindia',
            'about_content'    => 'PlantripOnline is a trusted name in travel, dedicated to Malaysia-specialized tour experiences. With a presence in Thailand and India, we combine local knowledge with global hospitality to offer affordable, luxury, and custom tour packages for couples, families, and groups.',
            'hero_heading'     => 'Discover the Magic of Asia',
            'hero_subheading'  => 'Explore Thailand & Malaysia with our curated packages',
        ];

        foreach ($settings as $key => $val) {
            Setting::set($key, $val, 'general');
        }

        // ── Mark Top Items as Featured for Homepage ───────────────────────────
        Destination::whereIn('slug', ['kuala-lumpur', 'langkawi', 'penang', 'bangkok', 'phuket', 'chiang-mai'])
            ->update(['is_featured' => true]);

        DayTour::whereIn('slug', [
            'tour-petronas-sky-deck', 'tour-langkawi-island-hopping', 'tour-genting-cable-car',
            'grand-palace-wat-pho', 'phi-phi-islands-speedboat', 'elephant-sanctuary-chiang-mai'
        ])->update(['is_featured' => true]);

        TourPackage::whereIn('slug', ['best-of-malaysia', 'kl-explorer', 'best-of-thailand-7d', 'bangkok-explorer-4d'])
            ->update(['is_featured' => true]);

        $this->command->info('✅ Reviews, Offices, Settings, and Featured flags seeded!');
    }
}

