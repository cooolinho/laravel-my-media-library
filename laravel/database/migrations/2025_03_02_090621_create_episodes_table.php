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
            $table->integer('season');
            $table->boolean('owned');
            $table->integer('theTvDbId');
            $table->foreignId('series_id')->constrained()->onDelete('cascade');
        });

        Schema::create('episode_data', function (Blueprint $table) {
            $table->id();
            $table->string('firstAired');
            $table->string('image');
            $table->string('lastAired');
            $table->string('lastUpdated');
            $table->string('name');
            $table->string('nextAired');
            $table->string('slug');
            $table->string('year');
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
