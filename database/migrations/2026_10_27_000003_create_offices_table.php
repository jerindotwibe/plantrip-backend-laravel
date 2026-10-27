<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('offices', function (Blueprint $table) {
            $table->id();
            $table->string('title');                        // e.g. "Thailand Office", "Malaysia Office"
            $table->string('address');                      // e.g. "Bangkok, Thailand"
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->text('map_embed_url')->nullable();
            $table->string('map_image_url')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['is_active', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('offices');
    }
};

