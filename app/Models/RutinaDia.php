<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RutinaDia extends Model
{
    use HasFactory;

    protected $table = 'rutina_dias';
    protected $fillable = ['rutina_id', 'dia', 'nivel'];

    public function rutina()
    {
        return $this->belongsTo(Rutina::class);
    }

    // CAMBIO IMPORTANTE: Renombramos de 'ejercicios' a 'exercises'
    // para que coincida con lo que busca tu controlador.
    public function exercises()
    {
        return $this->belongsToMany(
            Exercise::class,           // Modelo destino
            'rutina_dia_ejercicio',    // Tabla pivote (en español en tu BD)
            'rutina_dia_id',           // FK de este modelo en pivote
            'ejercicio_id'             // FK del otro modelo en pivote (ejercicios)
        )
        ->withPivot('series', 'repeticiones')
        ->withTimestamps();
    }
}