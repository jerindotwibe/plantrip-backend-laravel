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
        Schema::table('hotels', function (Blueprint $table) {
            if (!Schema::hasColumn('hotels', 'destination_id')) {
                $table->foreignId('destination_id')->nullable()->after('country_id')->constrained('destinations')->onDelete('set null');
            }
            if (!Schema::hasColumn('hotels', 'is_featured')) {
                $table->boolean('is_featured')->default(false)->after('is_active')->index();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hotels', function (Blueprint $table) {
            $table->dropForeign(['destination_id']);
            $table->dropColumn(['destination_id', 'is_featured']);
        });
    }
};

