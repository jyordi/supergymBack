<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkoutHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'rutina_nombre',
        'nivel',
        'duration_seconds',
        'calories',
        'difficulty',
        'completed_date'
        
    ];

    // Relación inversa (opcional)
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}