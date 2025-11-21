<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExerciseImage extends Model
{
    use HasFactory;

    protected $fillable = ['exercise_id', 'image_path'];

    public function exercise()
    {
        return $this->belongsTo(Exercise::class);
    }
}