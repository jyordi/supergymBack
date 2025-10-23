<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RutinaDia extends Model
{
    protected $table = 'rutina_dias';
    protected $fillable = ['rutina_id', 'dia', 'nivel'];

    public function rutina()
    {
        return $this->belongsTo(Rutina::class);
    }

    public function ejercicios()
    {
        return $this->belongsToMany(Ejercicio::class, 'rutina_dia_ejercicio', 'rutina_dia_id', 'ejercicio_id')
            ->withPivot('series', 'repeticiones')
            ->withTimestamps();
    }
}
