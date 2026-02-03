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

    /**
     * ¡AQUÍ ESTÁ EL CAMBIO! 
     * Cambiamos 'ejercicios' por 'exercises' para que coincida con tu controlador.
     */
    /**
     * Relación con los ejercicios
     */
    public function exercises()
    {
        // Parámetros: (Modelo, Tabla Pivote, Llave Local en Pivote, Llave Foránea en Pivote)
        return $this->belongsToMany(Exercise::class, 'rutina_dia_ejercicio', 'rutina_dia_id', 'ejercicio_id')
                    ->withPivot('series', 'repeticiones', 'id')
                    ->withTimestamps();
    }
}