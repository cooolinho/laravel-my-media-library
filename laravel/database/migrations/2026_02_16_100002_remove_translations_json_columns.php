<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Remove translations JSON column from episode_data table
        Schema::table('episode_data', function (Blueprint $table) {
            $table->dropColumn('translations');
        });

        // Remove translations JSON column from series_data table
        Schema::table('series_data', function (Blueprint $table) {
            $table->dropColumn('translations');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Add back translations JSON column to episode_data table
        Schema::table('episode_data', function (Blueprint $table) {
            $table->json('translations')->nullable()->after('id');
        });

        // Add back translations JSON column to series_data table
        Schema::table('series_data', function (Blueprint $table) {
            $table->json('translations')->nullable()->after('id');
        });
    }
};

