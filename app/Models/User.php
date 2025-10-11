<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'nombre', 
        'email', 
        'password',
        'numero_usuario',
        'edad',
        'sexo', 
        'peso',
        'altura',
        'nivel_conocimiento',
        'objetivo',
        'tipo_usuario'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];
}