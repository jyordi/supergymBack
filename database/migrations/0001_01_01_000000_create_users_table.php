<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// ... imports

class CreateUsersTable extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('numero_usuario')->unique();

            // Datos personales
            $table->string('nombre');
            $table->string('email')->unique();
            $table->string('avatar')->nullable();
            
            // --- NUEVO CAMPO ---
            $table->date('fecha_nacimiento')->nullable(); 
            // -------------------

            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');

            // Opcionales
            $table->integer('edad')->nullable();
            $table->enum('sexo', ['M', 'F', 'Otro'])->nullable();
            $table->decimal('peso', 5, 2)->nullable();
            $table->decimal('altura', 5, 2)->nullable();

            // Preferencias / roles
            $table->enum('nivel_conocimiento', ['Principiante', 'Intermedio', 'Avanzado'])->default('Principiante');
            $table->enum('objetivo', ['Perder peso', 'Ganar músculo', 'Tonificación'])->nullable();
            $table->enum('tipo_usuario', ['Registrado', 'Invitado', 'Admin'])->default('Registrado');

            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
}