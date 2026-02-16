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
        Schema::create('episode_translations', function (Blueprint $table) {
            $table->id();
            $table->string('lang', 10);
            $table->string('name')->nullable();
            $table->text('overview')->nullable();
            $table->foreignId('episode_data_id')->constrained('episode_data')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['episode_data_id', 'lang']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('episode_translations');
    }
};

