<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Tymon\JWTAuth\Contracts\JWTSubject;


class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'numero_usuario',
        'name',
        'email',
        'password',
        'edad',
        'sexo',
        'peso',
        'altura',
        'nivel_conocimiento',
        'objetivo',
        'tipo_usuario'
    ];

    protected $hidden = ['password', 'remember_token'];

    // JWT
    public function getJWTIdentifier() { return $this->getKey(); }
    public function getJWTCustomClaims() { return []; }

    // Cast para password
    protected $casts = [
        'password' => 'hashed',
    ];


    
}
