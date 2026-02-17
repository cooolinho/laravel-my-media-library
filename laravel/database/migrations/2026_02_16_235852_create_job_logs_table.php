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
        Schema::create('job_logs', function (Blueprint $table) {
            $table->id();
            $table->string('job_class')->index();
            $table->string('status')->index();
            $table->text('message')->nullable();
            $table->json('context')->nullable();
            $table->longText('exception')->nullable();

            // Polymorphic relation to Series, Episode, etc.
            $table->nullableMorphs('loggable');

            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('finished_at')->nullable();
            $table->decimal('duration_seconds', 10, 3)->nullable();

            // Indexes for better performance
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_logs');
    }
};

