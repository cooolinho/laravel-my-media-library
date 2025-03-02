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
            $table->string('aired');
            $table->string('image');
            $table->string('lastUpdated');
            $table->string('name');
            $table->string('overview');
            $table->string('runtime');
            $table->string('year');
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
