<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Rutina extends Model
{
    protected $fillable = ['nombre', 'descripcion', 'dia', 'nivel'];

    public function ejercicios()
    {
        return $this->belongsToMany(Ejercicio::class, 'rutina_ejercicio')
            ->withPivot('series', 'repeticiones', 'nivel')
            ->withTimestamps();
    }

    // Relación: días específicos de esta rutina
    public function dias()
    {
        return $this->hasMany(RutinaDia::class);
    }

    // Scope: filtrar por día (acepta nombre del día en español, ej. 'Lunes')
    public function scopePorDia($query, $dia)
    {
        return $query->where('dia', $dia);
    }

    // Scope: filtrar por nivel
    public function scopePorNivel($query, $nivel)
    {
        return $query->where('nivel', $nivel);
    }

    // Scope: obtener rutinas del día actual
    public function scopeHoy($query)
    {
        $diaHoy = Carbon::now()->locale('es')->translatedFormat('l'); // devuelve e.g. 'lunes' o 'Lunes' según locale
        // Normalizar a primera letra mayúscula para coincidir con enum
        $diaHoy = ucfirst(mb_strtolower($diaHoy));
        // Ajuste: en algunos entornos 'Wednesday' traducido a 'miércoles' con tilde; usamos ucfirst con mb_strtolower
        return $query->where('dia', $diaHoy);
    }
}
