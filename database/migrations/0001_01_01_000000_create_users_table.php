<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

<<<<<<< HEAD
class CreateUsersTable extends Migration
=======
return new class extends Migration
>>>>>>> raamses
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('numero_usuario')->unique();
<<<<<<< HEAD
            $table->string('nombre');
            $table->string('email')->unique();
=======
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
>>>>>>> raamses
            $table->string('password');
            $table->integer('edad')->nullable();
            $table->enum('sexo', ['M','F','Otro'])->nullable();
            $table->decimal('peso',5,2)->nullable();
            $table->decimal('altura',5,2)->nullable();
<<<<<<< HEAD
            $table->enum('nivel_conocimiento',['Principiante','Intermedio','Avanzado'])->default('Principiante');
            $table->enum('objetivo',['Perder peso','Ganar músculo','Tonificación'])->nullable();
            $table->enum('tipo_usuario',['Registrado','Invitado','Admin'])->default('Registrado');
=======
            $table->enum('nivel_conocimiento', ['Principiante', 'Intermedio', 'Avanzado'])->default('Principiante');
            $table->enum('objetivo', ['Perder peso', 'Ganar músculo', 'Tonificación'])->nullable();
            $table->enum('tipo_usuario', ['Registrado', 'Invitado', 'Admin'])->default('Registrado');
            $table->rememberToken();
>>>>>>> raamses
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
