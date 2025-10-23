<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Ejercicio extends Model
{
    use HasFactory;

    protected $fillable = ['nombre', 'descripcion']; // ajusta campos según tu migración

    public function rutinas()
    {
        return $this->belongsToMany(Rutina::class, 'rutina_ejercicio')
            ->withPivot('series', 'repeticiones', 'nivel')
            ->withTimestamps();
    }

    public function rutinaDias()
    {
        return $this->belongsToMany(RutinaDia::class, 'rutina_dia_ejercicio', 'ejercicio_id', 'rutina_dia_id')
            ->withPivot('series', 'repeticiones')
            ->withTimestamps();
    }
}
