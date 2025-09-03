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
        Schema::create('user_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->decimal('current_weight_kg', 5, 2)->nullable();
            $table->integer('height_cm')->nullable();
            $table->integer('birth_year')->nullable();
            $table->enum('sex', ['male', 'female'])->nullable();
            $table->enum('activity_level', ['sedentary', 'light', 'moderate', 'very'])->nullable();
            $table->enum('experience_level', ['beginner', 'intermediate', 'advanced'])->nullable();
            $table->enum('train_location', ['home', 'gym'])->nullable();
            $table->json('equipment')->nullable(); // ['mat', 'dumbbells', 'bands', 'none']
            $table->json('weekdays')->nullable(); // ['mon', 'wed', 'fri']
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_profiles');
    }
};
