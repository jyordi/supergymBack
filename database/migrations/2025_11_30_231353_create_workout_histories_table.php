<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('workout_histories', function (Blueprint $table) {
            $table->id();
            // Asegúrate de tener una tabla users, si no, cambia esto por $table->unsignedBigInteger('user_id');
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); 
            
            $table->string('rutina_nombre')->nullable(); // Guardamos el nombre por si borras la rutina original
            $table->string('nivel')->nullable(); // Principiante, etc.
            
            $table->integer('duration_seconds')->default(0); // Tiempo en segundos
            $table->integer('calories')->default(0); // Calorías quemadas
            $table->string('difficulty')->nullable(); // 'easy', 'medium', 'hard'
            
            $table->date('completed_date'); // Para buscar fácil por calendario (YYYY-MM-DD)
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('workout_histories');
    }
};