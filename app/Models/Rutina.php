<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;
use App\Models\Ejercicio;
use App\Models\RutinaDia;

class Rutina extends Model
{
    use HasFactory;

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

    // Scope: obtener rutinas del día actual (usa dayOfWeekIso para evitar issues de locale)
    public function scopeHoy($query)
    {
        $days = [
            1 => 'Lunes',
            2 => 'Martes',
            3 => 'Miércoles',
            4 => 'Jueves',
            5 => 'Viernes',
            6 => 'Sábado',
            7 => 'Domingo',
        ];
        $diaHoy = $days[Carbon::now()->dayOfWeekIso];
        return $query->where('dia', $diaHoy);
    }
}
