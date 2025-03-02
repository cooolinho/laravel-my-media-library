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
        Schema::create('episodes', function (Blueprint $table) {
            $table->id();
            $table->integer('number');
            $table->integer('seasonNumber');
            $table->boolean('owned')->default(false);
            $table->integer('theTvDbId');
            $table->foreignId('series_id')->constrained()->onDelete('cascade');
        });

        Schema::create('episode_data', function (Blueprint $table) {
            $table->id();
            $table->json('translations')->nullable();
            $table->foreignId('episode_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('episodes');
        Schema::dropIfExists('episode_data');
    }
};
