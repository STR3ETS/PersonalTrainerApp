<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('training_backgrounds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->text('background');
            $table->string('current_frequency');
            $table->text('current_activities');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('training_backgrounds');
    }
};
