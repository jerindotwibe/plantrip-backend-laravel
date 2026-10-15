<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('booking_reference')->unique();  // BK-2026-001
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('tour_id')->nullable()->constrained('day_tours')->onDelete('set null');
            $table->foreignId('package_id')->nullable()->constrained('tour_packages')->onDelete('set null');

            // Traveller Details
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('country_code', 10)->nullable();
            $table->string('whatsapp')->nullable();
            $table->string('nationality')->nullable();

            // Trip Details
            $table->string('destination');
            $table->string('country')->nullable();
            $table->date('travel_date');
            $table->integer('adults')->default(1);
            $table->integer('children')->default(0);
            $table->string('pick_up')->nullable();
            $table->string('drop_point')->nullable();
            $table->text('special_requests')->nullable();

            // Payment
            $table->enum('payment_method', ['online', 'offline'])->default('online');
            $table->string('promo_code')->nullable();
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('taxes', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->string('currency', 10)->default('USD');

            $table->enum('status', ['pending', 'confirmed', 'cancelled', 'completed'])->default('pending');
            $table->enum('payment_status', ['unpaid', 'paid', 'refunded'])->default('unpaid');
            $table->text('admin_notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
