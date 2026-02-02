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
     * Relación con los ejercicios
     */
    public function ejercicios()
    {
        return $this->belongsToMany(Exercise::class, 'rutina_dia_ejercicio')
                    ->withPivot('series', 'repeticiones', 'id')
                    ->withTimestamps();
    }
}