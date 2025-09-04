<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->date('birth_date');
            $table->text('address');
            $table->enum('gender', ['man', 'vrouw']);
            $table->integer('height_cm');
            $table->decimal('weight_kg', 5, 1);
            $table->text('injuries')->nullable();
            $table->enum('training_location', ['thuis', 'sportschool', 'buiten', 'combinatie']);
            $table->text('equipment')->nullable();
            $table->text('hyrox_equipment')->nullable();
            $table->text('additional_notes')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('profiles');
    }
};
