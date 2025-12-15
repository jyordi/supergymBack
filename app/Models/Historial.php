<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Historial extends Model
{
    use HasFactory;

    protected $table = 'historial'; // O el nombre de tu tabla

    // AGREGAR 'fecha_realizacion' AQUÍ ES OBLIGATORIO
    protected $fillable = [
        'user_id',
        'rutina_nombre',
        'nivel',
        'duration_seconds',
        'calories',
        'difficulty',
        'fecha_realizacion', // <--- ¡IMPORTANTE!
        'completada'
    ];

    // Opcional: Para que Laravel la trate siempre como fecha
    protected $casts = [
        'fecha_realizacion' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}