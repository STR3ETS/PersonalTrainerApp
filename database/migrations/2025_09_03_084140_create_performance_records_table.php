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
        Schema::create('performance_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('exercise_type', 50); // 'bench_press', etc.
            $table->decimal('weight_kg', 5, 2)->nullable();
            $table->integer('reps')->nullable();
            $table->enum('mode', ['1rm', '5reps', '10reps', 'unknown'])->default('unknown');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('performance_records');
    }
};
