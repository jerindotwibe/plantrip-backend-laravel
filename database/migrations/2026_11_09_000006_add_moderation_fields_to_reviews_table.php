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
        Schema::table('reviews', function (Blueprint $table) {
            if (!Schema::hasColumn('reviews', 'email')) {
                $table->string('email')->nullable()->after('name');
            }
            if (!Schema::hasColumn('reviews', 'destination')) {
                $table->string('destination')->nullable()->after('trip');
            }
            if (!Schema::hasColumn('reviews', 'status')) {
                $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending')->after('avatar')->index();
            }
            if (!Schema::hasColumn('reviews', 'admin_note')) {
                $table->text('admin_note')->nullable()->after('status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->dropColumn(['email', 'destination', 'status', 'admin_note']);
        });
    }
};

