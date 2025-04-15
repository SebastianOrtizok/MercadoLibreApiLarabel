<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_admin',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
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

    // Relación con Articulo (ya estaba)
    public function articulos()
    {
        return $this->hasMany(Articulo::class, 'user_id');
    }

    // Relación con MercadoLibreToken (nueva)
    public function mercadolibreTokens()
    {
        return $this->hasMany(MercadoLibreToken::class, 'user_id');
    }

    // Relación con suscripciones
    public function suscripciones()
    {
        return $this->hasMany(Suscripcion::class, 'usuario_id');
    }

    // Relación con pagos
    public function pagos()
    {
        return $this->hasMany(Pago::class, 'usuario_id');
    }

}
