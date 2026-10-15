<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Country;
use App\Models\Destination;
use App\Models\DayTour;
use App\Models\TourPackage;
use App\Models\Hotel;
use App\Models\Transfer;
use App\Models\PromoCode;

class ThailandSeeder extends Seeder
{
    public function run(): void
    {
        // ── Country ───────────────────────────────────────────────────────────
        $thailand = Country::updateOrCreate(['slug' => 'thailand'], [
            'name'          => 'Thailand',
            'flag'          => '🇹🇭',
            'currency'      => 'THB',
            'hero_image'    => 'https://images.unsplash.com/photo-1552465011-b4e21bf6e79a?w=1600',
            'hero_title'    => 'Amazing Thailand Experience',
            'tagline'       => 'Land of Smiles',
            'description'   => 'From vibrant Bangkok streets to pristine island beaches, Thailand offers a perfect blend of culture, cuisine, and adventure.',
            'tour_count'    => 9,
            'package_count' => 6,
            'is_active'     => true,
        ]);

        // ── Destinations ──────────────────────────────────────────────────────
        $destinations = [
            ['name' => 'Bangkok',    'slug' => 'bangkok',    'image' => 'https://images.unsplash.com/photo-1508009603885-50cf7c579365?w=800',  'badge' => '15 Tours', 'rating' => 4.8, 'locations_count' => 12, 'description' => "Thailand's vibrant capital with temples, street food, and nightlife.", 'highlights' => ['Grand Palace','Chatuchak Market','Khao San Road','Wat Arun']],
            ['name' => 'Pattaya',    'slug' => 'pattaya',    'image' => 'https://autourasia.com/uploads/Travel-Guide-Thailand/pattaya/pattaya-5-days/700-Sanctuary-of-Truth.jpg', 'badge' => '10 Tours', 'rating' => 4.7, 'locations_count' => 8, 'description' => 'Beachside city with vibrant nightlife and water sports.', 'highlights' => ['Sanctuary of Truth','Walking Street','Coral Island','Nong Nooch Garden']],
            ['name' => 'Chiang Mai', 'slug' => 'chiang-mai', 'image' => 'https://images.unsplash.com/photo-1552465011-b4e21bf6e79a?w=1600',  'badge' => '12 Tours', 'rating' => 4.9, 'locations_count' => 10, 'description' => 'Mountain city with ancient temples and elephant sanctuaries.', 'highlights' => ['Doi Suthep','Old City Temples','Night Bazaar','Elephant Nature Park']],
            ['name' => 'Phuket',     'slug' => 'phuket',     'image' => 'https://images.unsplash.com/photo-1589394815804-964ed0be2eb5?w=800',  'badge' => '18 Tours', 'rating' => 4.8, 'locations_count' => 15, 'description' => "Thailand's largest island with stunning beaches and nightlife.", 'highlights' => ['Patong Beach','Phi Phi Islands','Big Buddha','Old Phuket Town']],
            ['name' => 'Krabi',      'slug' => 'krabi',      'image' => 'https://images.unsplash.com/photo-1537956965359-7573183d1f57?w=800',  'badge' => '8 Tours',  'rating' => 4.6, 'locations_count' => 12, 'description' => 'Limestone cliffs, crystal waters, and island adventures.', 'highlights' => ['Railay Beach','4 Islands','Tiger Cave Temple','Ao Nang']],
            ['name' => 'Ayutthaya', 'slug' => 'ayutthaya',   'image' => 'https://res.klook.com/image/upload/w_1265,h_785,c_fill,q_85/w_80,x_15,y_15,g_south_west,l_Klook_water_br_trans_yhcmh3/activities/frwsjalipnxucjz8vieg.webp', 'badge' => 'UNESCO', 'rating' => 4.8, 'locations_count' => 5, 'description' => 'Ancient capital with magnificent temple ruins.', 'highlights' => ['Wat Mahathat','Wat Chaiwatthanaram','Ayutthaya Historical Park']],
        ];

        $destMap = [];
        foreach ($destinations as $d) {
            $dest = Destination::updateOrCreate(['slug' => $d['slug']], array_merge($d, ['country_id' => $thailand->id, 'is_active' => true]));
            $destMap[$d['slug']] = $dest->id;
        }

        // ── Day Tours ─────────────────────────────────────────────────────────
        $tours = [
            ['slug' => 'grand-palace-wat-pho', 'title' => 'Grand Palace & Wat Pho Tour', 'location' => 'Bangkok', 'dest_slug' => 'bangkok', 'image' => 'https://images.unsplash.com/photo-1563492065599-3520f775eeed?w=1200', 'badge' => 'Bestseller', 'duration' => '4 hours', 'description' => "Explore Bangkok's most iconic temples including the magnificent Grand Palace and the famous Reclining Buddha at Wat Pho.", 'features' => ['🎫 Skip the Line','👥 Small Group','🗣️ English Guide'], 'rating' => 4.9, 'reviews_count' => 324, 'price' => 45],
            ['slug' => 'floating-railway-market', 'title' => 'Floating Market & Railway Market', 'location' => 'Bangkok', 'dest_slug' => 'bangkok', 'image' => 'https://image.kkday.com/v2/image/get/w_960,c_fit,q_55,wm_auto/s1.kkday.com/product_144848/20230601091050_QX1lx/jpg', 'badge' => 'Popular', 'duration' => '6 hours', 'description' => 'Experience authentic Thai markets with a boat ride through Damnoen Saduak Floating Market.', 'features' => ['🚤 Boat Ride','🍜 Food Tasting','📸 Photo Stops'], 'rating' => 4.8, 'reviews_count' => 189, 'price' => 65],
            ['slug' => 'phi-phi-islands-speedboat', 'title' => 'Phi Phi Islands by Speedboat', 'location' => 'Phuket', 'dest_slug' => 'phuket', 'image' => 'https://res.klook.com/images/fl_lossy.progressive,q_65/c_fill,w_1295,h_863/w_80,x_15,y_15,g_south_west,l_Klook_water_br_trans_yhcmh3/activities/cs8onldxp0fh6xt8dasi/Phuket:PhiPhi,MayaKhaiIslandsTourbyPremiumSpeedCatamaran.jpg', 'badge' => '', 'duration' => 'Full Day', 'description' => 'Visit the stunning Phi Phi Islands with crystal waters, dramatic cliffs and Maya Bay.', 'features' => ['🤿 Snorkeling','🍱 Lunch Included','🏖️ Beach Time'], 'rating' => 4.7, 'reviews_count' => 512, 'price' => 89],
            ['slug' => 'ayutthaya-ancient-city-tour', 'title' => 'Ayutthaya Ancient City Tour', 'location' => 'Ayutthaya', 'dest_slug' => 'ayutthaya', 'image' => 'https://res.klook.com/image/upload/w_1265,h_785,c_fill,q_85/w_80,x_15,y_15,g_south_west,l_Klook_water_br_trans_yhcmh3/activities/frwsjalipnxucjz8vieg.webp', 'badge' => 'UNESCO Site', 'duration' => 'Full Day', 'description' => "Journey to Thailand's ancient capital and explore magnificent temple ruins.", 'features' => ['🏛️ Temple Ruins','📜 History','🍲 Thai Lunch'], 'rating' => 4.8, 'reviews_count' => 267, 'price' => 55],
            ['slug' => 'elephant-sanctuary-chiang-mai', 'title' => 'Elephant Sanctuary Experience', 'location' => 'Chiang Mai', 'dest_slug' => 'chiang-mai', 'image' => 'https://images.unsplash.com/photo-1552465011-b4e21bf6e79a?w=1600', 'badge' => 'Nature', 'duration' => 'Full Day', 'description' => 'Ethical elephant interaction with feeding, bathing, and conservation.', 'features' => ['🐘 Elephant Care','💚 Ethical','🥘 Local Lunch'], 'rating' => 5.0, 'reviews_count' => 421, 'price' => 75],
            ['slug' => 'krabi-4-islands-tour', 'title' => 'Krabi 4 Islands Tour', 'location' => 'Krabi', 'dest_slug' => 'krabi', 'image' => 'https://www.placesofjuma.com/wp-content/uploads/2019/07/Chicken-Island-Krabi-4-1024x683.jpg', 'badge' => '', 'duration' => '5 hours', 'description' => 'Island hopping to Phra Nang Cave, Chicken Island, Tup Island, and Poda Island.', 'features' => ['🚤 Longtail Boat','🤿 Snorkeling','🏝️ 4 Islands'], 'rating' => 4.9, 'reviews_count' => 298, 'price' => 42],
            ['slug' => 'atv-jungle-adventure-phuket', 'title' => 'ATV Jungle Adventure', 'location' => 'Phuket', 'dest_slug' => 'phuket', 'image' => 'https://dynamic-media.tacdn.com/media/photo-o/2e/be/92/27/caption.jpg?w=1400&h=1000&s=1', 'badge' => 'Adventure', 'duration' => '3 hours', 'description' => 'Thrilling off-road ATV experience through jungle trails.', 'features' => ['🏍️ ATV Riding','🌲 Jungle','🎢 Exciting'], 'rating' => 4.6, 'reviews_count' => 156, 'price' => 52],
            ['slug' => 'bangkok-street-food-tour', 'title' => 'Bangkok Street Food Tour', 'location' => 'Bangkok', 'dest_slug' => 'bangkok', 'image' => 'https://bangkokvanguards.com/wp-content/uploads/2019/08/Mark-Wiens-food-tour-2.jpg', 'badge' => '', 'duration' => '4 hours', 'description' => 'Taste authentic Bangkok street food with a local guide.', 'features' => ['🍜 10+ Tastings','👨‍🍳 Local Guide','🌃 Evening Tour'], 'rating' => 4.9, 'reviews_count' => 387, 'price' => 38],
            ['slug' => 'james-bond-island-phang-nga', 'title' => 'James Bond Island & Phang Nga Bay', 'location' => 'Phuket', 'dest_slug' => 'phuket', 'image' => 'https://media.tacdn.com/media/attractions-splice-spp-674x446/07/b7/da/79.jpg', 'badge' => 'Scenic', 'duration' => '6 hours', 'description' => 'Explore the famous James Bond Island and limestone karsts.', 'features' => ['🛶 Kayaking','🏝️ Island','🍱 Lunch'], 'rating' => 4.7, 'reviews_count' => 445, 'price' => 68],
        ];

        foreach ($tours as $t) {
            $destId = $destMap[$t['dest_slug']] ?? null;
            unset($t['dest_slug']);
            DayTour::updateOrCreate(['slug' => $t['slug']], array_merge($t, [
                'country_id'    => $thailand->id,
                'destination_id'=> $destId,
                'currency'      => 'USD',
                'is_active'     => true,
            ]));
        }

        // ── Tour Packages ─────────────────────────────────────────────────────
        $packages = [
            ['slug' => 'best-of-thailand-7d',    'title' => 'Best of Thailand - 7 Days', 'subtitle' => 'Bangkok • Chiang Mai • Phuket', 'image' => 'https://images.unsplash.com/photo-1552465011-b4e21bf6e79a?w=800', 'badge' => 'Most Popular', 'duration' => '7D/6N', 'duration_days' => 7, 'duration_nights' => 6, 'price' => 1299, 'includes' => ['🏨 Hotels','✈️ Flights','🍽️ Meals','🎫 Tours']],
            ['slug' => 'bangkok-explorer-4d',     'title' => 'Bangkok Explorer', 'subtitle' => 'Perfect city escape with cultural highlights', 'image' => 'https://images.unsplash.com/photo-1508009603885-50cf7c579365?w=800', 'badge' => 'City Break', 'duration' => '4D/3N', 'duration_days' => 4, 'duration_nights' => 3, 'price' => 489, 'includes' => ['🏨 3* Hotel','🍳 Breakfast','🎫 Tours']],
            ['slug' => 'phuket-phi-phi-5d',       'title' => 'Phuket & Phi Phi Islands', 'subtitle' => 'Island hopping and beach relaxation', 'image' => 'https://images.unsplash.com/photo-1589394815804-964ed0be2eb5?w=800', 'badge' => 'Beach Paradise', 'duration' => '5D/4N', 'duration_days' => 5, 'duration_nights' => 4, 'price' => 749, 'includes' => ['🏖️ Resort','🍳 Breakfast','🚤 Island Tours']],
            ['slug' => 'northern-thailand-6d',    'title' => 'Northern Thailand Discovery', 'subtitle' => 'Chiang Mai & Chiang Rai adventure', 'image' => 'https://images.unsplash.com/photo-1552465011-b4e21bf6e79a?w=800', 'badge' => 'Cultural', 'duration' => '6D/5N', 'duration_days' => 6, 'duration_nights' => 5, 'price' => 899, 'includes' => ['🏨 Hotels','🍽️ Meals','🚗 Transport']],
            ['slug' => 'krabi-adventure-5d',      'title' => 'Krabi Adventure Package', 'subtitle' => 'Rock climbing, kayaking & island hopping', 'image' => 'https://images.unsplash.com/photo-1537956965359-7573183d1f57?w=800', 'badge' => 'Adventure', 'duration' => '5D/4N', 'duration_days' => 5, 'duration_nights' => 4, 'price' => 679, 'includes' => ['🏨 Hotel','🍳 Breakfast','🎢 Activities']],
            ['slug' => 'koh-samui-luxury-7d',     'title' => 'Koh Samui Luxury Escape', 'subtitle' => '5-star resorts and exclusive experiences', 'image' => 'https://images.unsplash.com/photo-1583417319070-4a69db38a482?w=800', 'badge' => 'Luxury', 'duration' => '7D/6N', 'duration_days' => 7, 'duration_nights' => 6, 'price' => 1899, 'includes' => ['⭐ 5* Resort','🍽️ All Meals','💆 Spa']],
        ];

        foreach ($packages as $p) {
            TourPackage::updateOrCreate(['slug' => $p['slug']], array_merge($p, [
                'country_id' => $thailand->id,
                'currency'   => 'USD',
                'is_active'  => true,
            ]));
        }

        // ── Hotels ────────────────────────────────────────────────────────────
        $hotels = [
            ['slug' => 'siam-bangkok', 'title' => 'The Siam Bangkok', 'location' => 'Bangkok - Riverside', 'image' => 'https://cdn.audleytravel.com/1050/749/79/16021861-pool-the-siam-bangkok.webp', 'category' => 'luxury', 'stars' => 5, 'destination' => 'Bangkok', 'badge' => 'Featured', 'rating' => 4.9, 'reviews_count' => 250, 'price_per_night' => 380, 'amenities' => ['🏊 Pool','🍽️ Restaurant','💆 Spa','📶 WiFi']],
            ['slug' => 'anantara-phuket-resort', 'title' => 'Anantara Phuket Resort', 'location' => 'Phuket - Mai Khao Beach', 'image' => 'https://assets.anantara.com/image/upload/q_auto,f_auto/media/minor/anantara/images/anantara-vacation-club-mai-khao-phuket/room-category/two_bedroom_pool_villa_726x392.jpg', 'category' => 'luxury', 'stars' => 5, 'destination' => 'Phuket', 'badge' => 'Luxury', 'rating' => 4.8, 'reviews_count' => 180, 'price_per_night' => 280, 'amenities' => ['🏖️ Beach','🏊 Pool','💆 Spa','🍽️ Restaurant']],
            ['slug' => '137-pillars-house-chiang-mai', 'title' => '137 Pillars House', 'location' => 'Chiang Mai - Old City', 'image' => 'https://images.squarespace-cdn.com/content/v1/549d41a3e4b003c6ce131926/1460200338156-USVXBE1953H269468DSD/Chiang_Mai-Web-71-20160127.jpg?format=1500w', 'category' => 'boutique', 'stars' => 5, 'destination' => 'Chiang Mai', 'badge' => 'Boutique', 'rating' => 4.9, 'reviews_count' => 145, 'price_per_night' => 195, 'amenities' => ['🏊 Pool','🍽️ Fine Dining','💆 Spa','🚴 Bikes']],
            ['slug' => 'rayavadee-krabi', 'title' => 'Rayavadee Krabi', 'location' => 'Krabi - Railay Beach', 'image' => 'https://www.rayavadee.com/en/images/room-phranang.jpg', 'category' => 'luxury', 'stars' => 5, 'destination' => 'Krabi', 'badge' => 'Beach Resort', 'rating' => 4.9, 'reviews_count' => 220, 'price_per_night' => 420, 'amenities' => ['🏖️ Private Beach','🛶 Kayaking','🍽️ Restaurants','💆 Spa']],
            ['slug' => 'eastin-grand-sathorn-bangkok', 'title' => 'Eastin Grand Sathorn', 'location' => 'Bangkok - Sathorn', 'image' => 'https://pix10.agoda.net/hotelImages/286835/879540575/c9739689db189b6766fbae87fa54372b.jpg?ce=0&s=414x232', 'category' => 'upscale', 'stars' => 4, 'destination' => 'Bangkok', 'badge' => 'City Center', 'rating' => 4.7, 'reviews_count' => 320, 'price_per_night' => 125, 'amenities' => ['🏊 Rooftop Pool','🏋️ Fitness','🍽️ Restaurant','📶 WiFi']],
            ['slug' => 'ibis-styles-bangkok-sukhumvit', 'title' => 'ibis Styles Bangkok Sukhumvit', 'location' => 'Bangkok - Sukhumvit', 'image' => 'https://q-xx.bstatic.com/xdata/images/hotel/max500/752430596.jpg', 'category' => 'mid-range', 'stars' => 3, 'destination' => 'Bangkok', 'badge' => 'Budget Friendly', 'rating' => 4.5, 'reviews_count' => 430, 'price_per_night' => 48, 'amenities' => ['🍳 Breakfast','📶 WiFi','🚇 Near BTS','🍽️ Restaurant']],
        ];

        foreach ($hotels as $h) {
            Hotel::updateOrCreate(['slug' => $h['slug']], array_merge($h, [
                'country_id' => $thailand->id,
                'currency'   => 'USD',
                'is_active'  => true,
            ]));
        }

        // ── Transfers ─────────────────────────────────────────────────────────
        $transfers = [
            ['slug' => 'bkk-airport-city-transfer', 'title' => 'Bangkok Airport to City', 'from_location' => 'Suvarnabhumi Airport', 'to_location' => 'Bangkok City Center', 'duration' => '45-60 min', 'price' => 25, 'image' => 'https://images.unsplash.com/photo-1508009603885-50cf7c579365?w=800', 'vehicles' => ['Sedan','SUV','Van'], 'description' => 'Comfortable airport transfer with meet & greet service.'],
            ['slug' => 'bkk-pattaya-transfer', 'title' => 'Bangkok to Pattaya', 'from_location' => 'Bangkok', 'to_location' => 'Pattaya', 'duration' => '2-2.5 hours', 'price' => 45, 'image' => 'https://autourasia.com/uploads/Travel-Guide-Thailand/pattaya/pattaya-5-days/700-Sanctuary-of-Truth.jpg', 'vehicles' => ['Sedan','Van','Minibus'], 'description' => 'Private transfer between Bangkok and Pattaya beach city.'],
            ['slug' => 'phuket-airport-transfer', 'title' => 'Phuket Airport Transfer', 'from_location' => 'Phuket Airport', 'to_location' => 'Beach Hotels', 'duration' => '30-60 min', 'price' => 20, 'image' => 'https://images.unsplash.com/photo-1589394815804-964ed0be2eb5?w=800', 'vehicles' => ['Sedan','SUV'], 'description' => 'Direct transfer from Phuket airport to your beach resort.'],
        ];

        foreach ($transfers as $t) {
            Transfer::updateOrCreate(['slug' => $t['slug']], array_merge($t, [
                'country_id' => $thailand->id,
                'currency'   => 'USD',
                'is_active'  => true,
            ]));
        }

        $this->command->info('✅ Thailand data seeded successfully!');
    }
}
