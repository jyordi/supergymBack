<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserProgress extends Model
{
    use HasFactory;

    protected $table = 'user_progress';

    // ESTO ES LO MÁS IMPORTANTE PARA EVITAR ERROR 500
    protected $fillable = [
        'user_id',
        'peso',
        'altura',
        'cintura',
        'foto_path',
        'notas'
    ];
}