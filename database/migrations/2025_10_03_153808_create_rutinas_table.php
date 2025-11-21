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
        Schema::create('rutinas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            // Día de la semana asignado a la rutina (Lunes..Domingo)
            $table->enum('dia', ['Lunes','Martes','Miércoles','Jueves','Viernes','Sábado','Domingo'])->nullable();
            // Nivel de la rutina
            $table->enum('nivel', ['Principiante','Intermedio','Avanzado'])->default('Principiante');
            $table->timestamps();
        });

        Schema::create('rutina_ejercicio', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rutina_id')->constrained('rutinas')->onDelete('cascade');
            $table->foreignId('ejercicio_id')->constrained('exercises')->onDelete('cascade');
            $table->integer('series')->default(3);
            $table->string('repeticiones')->default('12');
            // Nivel específico para este ejercicio dentro de la rutina
            $table->enum('nivel', ['Principiante','Intermedio','Avanzado'])->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rutina_ejercicio');
        Schema::dropIfExists('rutinas');
    }
};
