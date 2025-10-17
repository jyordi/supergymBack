<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ejercicio extends Model
{
    protected $fillable = ['nombre', 'descripcion'];

    public function rutinas()
    {
        return $this->belongsToMany(Rutina::class, 'rutina_ejercicio')
            ->withPivot('series', 'repeticiones')
            ->withTimestamps();
    }
}
