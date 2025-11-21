<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rutina_dias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rutina_id')->constrained('rutinas')->onDelete('cascade');
            // Día de la semana asignado a esta entrada (Lunes..Domingo)
            $table->enum('dia', ['Lunes','Martes','Miércoles','Jueves','Viernes','Sábado','Domingo']);
            // Nivel específico para este día opcional
            $table->enum('nivel', ['Principiante','Intermedio','Avanzado'])->nullable();
            $table->timestamps();
        });

        Schema::create('rutina_dia_ejercicio', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rutina_dia_id')->constrained('rutina_dias')->onDelete('cascade');
            $table->foreignId('ejercicio_id')->constrained('exercises')->onDelete('cascade');
            $table->integer('series')->default(3);
            $table->string('repeticiones')->default('12');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rutina_dia_ejercicio');
        Schema::dropIfExists('rutina_dias');
    }
};
