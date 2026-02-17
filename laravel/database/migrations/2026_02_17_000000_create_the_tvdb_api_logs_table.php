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
        Schema::create('the_tvdb_api_logs', function (Blueprint $table) {
            $table->id();
            $table->string('endpoint');
            $table->string('method', 10)->default('GET');
            $table->json('params')->nullable();
            $table->integer('status_code')->nullable();
            $table->json('response_data')->nullable();
            $table->text('error_message')->nullable();
            $table->unsignedInteger('response_time')->nullable()->comment('Response time in milliseconds');
            $table->boolean('success')->default(false);
            $table->boolean('from_cache')->default(false);
            $table->string('bearer_token_hash')->nullable();
            $table->timestamps();

            // Indexes
            $table->index('endpoint');
            $table->index('method');
            $table->index('status_code');
            $table->index('success');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('the_tvdb_api_logs');
    }
};

