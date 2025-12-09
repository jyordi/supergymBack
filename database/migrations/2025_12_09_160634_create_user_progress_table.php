<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('user_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            
            // Datos físicos
            $table->decimal('peso', 5, 2); // Ej: 80.50
            $table->decimal('altura', 3, 2)->nullable(); // Ej: 1.75
            $table->decimal('cintura', 5, 2)->nullable(); // Medida opcional
            
            // Foto
            $table->string('foto_path')->nullable(); // Guardaremos la ruta de la imagen
            
            $table->text('notas')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_progress');
    }
};