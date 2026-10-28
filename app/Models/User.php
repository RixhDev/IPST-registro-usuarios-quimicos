<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

#[Fillable(['name', 'email', 'password', 'status'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Bootstrapping del modelo.
     * Intercepta la eliminación para proteger al usuario admin principal.
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($user) {
            // Cancela el borrado si es el ID 1 o tiene un correo específico de admin
            if ($user->id === 1 || strtolower($user->email) === 'admin@admin.com') {
                throw new \Exception('Acción no permitida: El usuario administrador principal está protegido y no puede ser eliminado.');
            }
        });
    }

    /**
     * Casts de atributos
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}