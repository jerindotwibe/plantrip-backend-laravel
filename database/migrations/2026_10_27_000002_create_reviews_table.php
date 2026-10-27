<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('trip')->nullable();             // e.g. "Kuala Lumpur Tour", "Langkawi Trip"
            $table->unsignedTinyInteger('rating')->default(5); // 1 - 5 stars
            $table->text('comment');
            $table->string('date')->nullable();             // e.g. "October 2026"
            $table->string('avatar')->nullable();
            $table->boolean('is_featured')->default(true);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['is_active', 'is_featured']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};

