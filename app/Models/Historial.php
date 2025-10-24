<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Historial extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'rutina_id',
        'fecha_realizacion',
        'completada',
    ];

    protected $casts = [
        'fecha_realizacion' => 'datetime',
        'completada' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function rutina()
    {
        return $this->belongsTo(Rutina::class);
    }
}
