<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('test_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('test_type', ['cooper_12min', '5k_run']);
            $table->string('result_value');
            $table->enum('result_unit', ['meters', 'time']);
            $table->date('test_date');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('test_results');
    }
};
