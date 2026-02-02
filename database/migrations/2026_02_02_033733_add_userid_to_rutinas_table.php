<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rutinas', function (Blueprint $table) {
            // Vinculamos la rutina a un usuario
            $table->foreignId('user_id')->nullable()->after('id')->constrained('users')->onDelete('cascade');
            
            // Un switch para saber cuál es la rutina que el usuario está usando actualmente
            $table->boolean('activa')->default(true)->after('nombre');
        });
    }

    public function down(): void
    {
        Schema::table('rutinas', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn(['user_id', 'activa']);
        });
    }
};