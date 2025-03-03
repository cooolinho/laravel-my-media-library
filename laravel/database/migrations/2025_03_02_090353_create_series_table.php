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
        Schema::create('series', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('theTvDbId');
        });

        Schema::create('series_data', function (Blueprint $table) {
            $table->id();
            $table->json('translations')->nullable();
            $table->string('slug')->nullable();
            $table->string('image')->nullable();
            $table->date('firstAired')->nullable();
            $table->date('lastAired')->nullable();
            $table->date('nextAired')->nullable();
            $table->integer('score')->nullable();
            $table->string('status')->nullable();
            $table->string('originalCountry')->nullable();
            $table->string('originalLanguage')->nullable();
            $table->tinyInteger('defaultSeasonType')->nullable();
            $table->boolean('isOrderRandomized')->nullable();
            $table->dateTime('lastUpdated')->nullable();
            $table->smallInteger('averageRuntime')->nullable();
            $table->year('year')->nullable();
            $table->foreignId('series_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('series');
        Schema::dropIfExists('series_data');
    }
};
