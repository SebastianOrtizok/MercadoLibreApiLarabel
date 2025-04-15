<?php

// app/Models/Pago.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pago extends Model
{
    protected $table = 'pagos'; // Nombre de la tabla en español

    protected $fillable = [
        'usuario_id', 'suscripcion_id', 'monto', 'metodo_pago', 'id_transaccion', 'estado', 'fecha_pago',
    ];

    protected $casts = [
        'fecha_pago' => 'datetime',
    ];

    // Relación con el usuario
    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    // Relación con la suscripción
    public function suscripcion()
    {
        return $this->belongsTo(Suscripcion::class, 'suscripcion_id');
    }
}
