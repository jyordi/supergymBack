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
     * Asegura el link con HTTPS forzado para Railway
     */
    public function getAvatarAttribute($value)
    {
        if ($value) {
            // Si ya tiene el link completo, lo deja pasar
            if (str_starts_with($value, 'http')) {
                return $value;
            }
            // Si no, forzamos el HTTPS con tu dominio de Railway
            return 'https://supergymback-production.up.railway.app/storage/' . $value;
        }
        // Si no hay foto, mandamos null para que el HTML use el de defecto
        return null; 
    }
}