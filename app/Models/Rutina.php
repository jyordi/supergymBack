<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rutina extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'descripcion',
        'dia',
        'nivel'
    ];

    /**
     * Relación Directa: Rutina -> Ejercicios
     * (Si usas la tabla 'rutina_ejercicio')
     */
    public function exercises()
    {
        return $this->belongsToMany(Exercise::class, 'rutina_ejercicio')
                    ->withPivot('series', 'repeticiones', 'nivel')
                    ->withTimestamps();
    }

    /**
     * Relación Jerárquica: Rutina -> Días
     * (Para dividir la rutina en Lunes, Martes, etc.)
     */
    public function dias()
    {
        return $this->hasMany(RutinaDia::class);
    }
}