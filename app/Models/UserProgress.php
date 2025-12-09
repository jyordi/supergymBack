<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserProgress extends Model
{
    use HasFactory;

    protected $table = 'user_progress';

    protected $fillable = [
        'user_id',
        'peso',
        'altura',
        'cintura',
        'foto_path',
        'notas',
        'avatar'
    ];

    // Relación inversa
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}