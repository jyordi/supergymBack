<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exercise extends Model
{
    use HasFactory;

    // Forzamos el nombre de la tabla para evitar que busque 'ejercicios'
    protected $table = 'exercises';

    protected $fillable = [
        'name',
        'force',
        'level',
        'mechanic',
        'equipment',
        'category',
        'primary_muscles',
        'secondary_muscles',
    ];

    // Convierte automáticamente el JSON de la BD a Array de PHP
    protected $casts = [
        'primary_muscles' => 'array',
        'secondary_muscles' => 'array',
    ];

    // Relación: Un ejercicio tiene muchas instrucciones
    public function instructions()
    {
        return $this->hasMany(ExerciseInstruction::class);
    }

    // Relación: Un ejercicio tiene muchas imágenes
    public function images()
    {
        return $this->hasMany(ExerciseImage::class);
    }

    // Relación: Un ejercicio pertenece a muchas rutinas
    

    public function rutinas()
    {
        return $this->belongsToMany(Rutina::class, 'rutina_ejercicio')
                    ->withPivot('series', 'repeticiones', 'nivel');
    }

    public function rutinaDias()
    {
        return $this->belongsToMany(RutinaDia::class, 'rutina_dia_ejercicio')
                    ->withPivot('series', 'repeticiones');
    }
}

