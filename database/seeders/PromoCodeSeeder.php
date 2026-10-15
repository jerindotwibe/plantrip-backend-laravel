<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PromoCode;

class PromoCodeSeeder extends Seeder
{
    public function run(): void
    {
        $codes = [
            [
                'code'           => 'WELCOME',
                'discount_type'  => 'fixed',
                'discount_value' => 25,
                'min_amount'     => 0,
                'max_uses'       => null,
                'expires_at'     => now()->addYear(),
                'is_active'      => true,
            ],
            [
                'code'           => 'PLANTRIP10',
                'discount_type'  => 'percentage',
                'discount_value' => 10,
                'min_amount'     => 50,
                'max_uses'       => 500,
                'expires_at'     => now()->addYear(),
                'is_active'      => true,
            ],
            [
                'code'           => 'SUMMER25',
                'discount_type'  => 'fixed',
                'discount_value' => 25,
                'min_amount'     => 100,
                'max_uses'       => 200,
                'expires_at'     => now()->addMonths(6),
                'is_active'      => true,
            ],
        ];

        foreach ($codes as $code) {
            PromoCode::updateOrCreate(['code' => $code['code']], $code);
        }

        $this->command->info('✅ Promo codes seeded!');
    }
}
