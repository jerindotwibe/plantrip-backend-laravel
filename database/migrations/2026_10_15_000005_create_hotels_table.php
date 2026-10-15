<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hotels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('country_id')->constrained()->onDelete('cascade');
            $table->string('slug')->unique();
            $table->string('title');
            $table->string('location')->nullable();
            $table->string('image')->nullable();
            $table->string('category')->nullable();        // luxury, boutique, mid-range
            $table->integer('stars')->default(3);
            $table->string('destination')->nullable();     // Bangkok, Phuket
            $table->string('badge')->nullable();
            $table->decimal('rating', 3, 1)->default(0);
            $table->integer('reviews_count')->default(0);
            $table->decimal('price_per_night', 10, 2)->default(0);
            $table->string('currency', 10)->default('USD');
            $table->json('amenities')->nullable();         // ["Pool", "WiFi", "Spa"]
            $table->text('description')->nullable();
            $table->text('policies')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hotels');
    }
};
