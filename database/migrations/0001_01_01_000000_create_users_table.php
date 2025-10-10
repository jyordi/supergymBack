<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToUsersTable extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('numero_usuario')->unique()->after('id');
            $table->integer('edad')->nullable();
            $table->enum('sexo',['M','F','Otro'])->nullable();
            $table->decimal('peso',5,2)->nullable();
            $table->decimal('altura',5,2)->nullable();
            $table->enum('nivel_conocimiento',['Principiante','Intermedio','Avanzado'])->default('Principiante');
            $table->enum('objetivo',['Perder peso','Ganar músculo','Tonificación'])->nullable();
            $table->enum('tipo_usuario',['Registrado','Invitado','Admin'])->default('Registrado');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['numero_usuario','edad','sexo','peso','altura','nivel_conocimiento','objetivo','tipo_usuario']);
        });
    }
}
