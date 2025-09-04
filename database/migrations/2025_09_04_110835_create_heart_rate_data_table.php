<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('heart_rate_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->integer('max_hr')->nullable();
            $table->integer('rest_hr')->nullable();
            $table->integer('zone1')->nullable();
            $table->integer('zone2')->nullable();
            $table->integer('zone3')->nullable();
            $table->integer('zone4')->nullable();
            $table->integer('zone5')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('heart_rate_data');
    }
};
