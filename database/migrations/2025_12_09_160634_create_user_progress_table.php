<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up(): void
{
    Schema::create('user_progress', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        
        // CAMBIA ESTO:
        // Antes probablemente tenías $table->tinyInteger('altura');
        
        // POR ESTO (Float acepta decimales y números grandes, Integer solo enteros):
        $table->float('peso');   // Ejemplo: 90.5
        $table->integer('altura'); // Ejemplo: 188 (en cm)
        
        $table->integer('cintura')->nullable();
        $table->string('foto_path')->nullable();
        $table->integer('edad')->nullable();
        $table->timestamps();
    });
}

    public function down()
    {
        Schema::dropIfExists('user_progress');
    }
};