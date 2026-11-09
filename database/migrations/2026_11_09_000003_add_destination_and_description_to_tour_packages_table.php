<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tour_packages', function (Blueprint $table) {
            if (!Schema::hasColumn('tour_packages', 'destination_id')) {
                $table->foreignId('destination_id')->nullable()->after('country_id')->constrained('destinations')->onDelete('set null');
            }
            if (!Schema::hasColumn('tour_packages', 'description')) {
                $table->text('description')->nullable()->after('duration');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tour_packages', function (Blueprint $table) {
            $table->dropForeign(['destination_id']);
            $table->dropColumn(['destination_id', 'description']);
        });
    }
};

