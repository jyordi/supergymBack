<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('numero_usuario')->unique();
            $table->string('nombre');
            $table->string('email')->unique();
            $table->string('password');
            $table->integer('edad')->nullable();
            $table->enum('sexo',['M','F','Otro'])->nullable();
            $table->decimal('peso',5,2)->nullable();
            $table->decimal('altura',5,2)->nullable();
            $table->enum('nivel_conocimiento',['Principiante','Intermedio','Avanzado'])->default('Principiante');
            $table->enum('objetivo',['Perder peso','Ganar músculo','Tonificación'])->nullable();
            $table->enum('tipo_usuario',['Registrado','Invitado','Admin'])->default('Registrado');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('users');
    }
}
