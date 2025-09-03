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
        Schema::create('nutrition_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->boolean('enabled')->default(false);
            $table->string('calorie_adjustment_pct', 5)->nullable(); // '-20', '+10'
            $table->enum('diet_preference', ['none', 'vegetarian', 'halal', 'allergy', 'other'])->default('none');
            $table->text('diet_preference_text')->nullable();
            $table->json('injuries')->nullable(); // ['knee', 'back', 'shoulder', 'none', 'other']
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nutrition_settings');
    }
};
