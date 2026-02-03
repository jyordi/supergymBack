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
        'nombre', 
        'email', 
        'password',
        'numero_usuario',
        'fecha_nacimiento', 
        'edad',
        'sexo', 
        'peso',
        'altura',
        'nivel_conocimiento',
        'objetivo',
        'tipo_usuario',
        'avatar' 
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'fecha_nacimiento' => 'date', 
    ];

    /**
     * Relación: User -> Rutinas
     */
    public function rutinas()
    {
        return $this->hasMany(Rutina::class);
    }

    /**
     * Para obtener rápido la rutina actual del usuario
     */
    public function rutinaActual()
    {
        return $this->hasOne(Rutina::class)->where('activa', true)->latest();
    }

    public function getJWTIdentifier() { return $this->getKey(); }
    public function getJWTCustomClaims() { return []; }


    /**
     * Esto asegura que el frontend siempre reciba el link completo de la foto
     */
    public function getAvatarAttribute($value)
    {
        if ($value) {
            // Si ya empieza con http, lo deja igual. Si no, le agrega la URL de Railway.
            return str_starts_with($value, 'http') ? $value : asset('storage/' . $value);
        }
        return null; // O pon aquí un link a un avatar genérico por defecto
    }
}