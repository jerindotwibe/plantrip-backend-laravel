<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('destinations') && !Schema::hasColumn('destinations', 'is_featured')) {
            Schema::table('destinations', function (Blueprint $table) {
                $table->boolean('is_featured')->default(false)->after('is_active');
                $table->index('is_featured');
            });
        }

        if (Schema::hasTable('day_tours') && !Schema::hasColumn('day_tours', 'is_featured')) {
            Schema::table('day_tours', function (Blueprint $table) {
                $table->boolean('is_featured')->default(false)->after('is_active');
                $table->index('is_featured');
            });
        }

        if (Schema::hasTable('tour_packages') && !Schema::hasColumn('tour_packages', 'is_featured')) {
            Schema::table('tour_packages', function (Blueprint $table) {
                $table->boolean('is_featured')->default(false)->after('is_active');
                $table->index('is_featured');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('destinations') && Schema::hasColumn('destinations', 'is_featured')) {
            Schema::table('destinations', function (Blueprint $table) {
                $table->dropIndex(['is_featured']);
                $table->dropColumn('is_featured');
            });
        }

        if (Schema::hasTable('day_tours') && Schema::hasColumn('day_tours', 'is_featured')) {
            Schema::table('day_tours', function (Blueprint $table) {
                $table->dropIndex(['is_featured']);
                $table->dropColumn('is_featured');
            });
        }

        if (Schema::hasTable('tour_packages') && Schema::hasColumn('tour_packages', 'is_featured')) {
            Schema::table('tour_packages', function (Blueprint $table) {
                $table->dropIndex(['is_featured']);
                $table->dropColumn('is_featured');
            });
        }
    }
};

