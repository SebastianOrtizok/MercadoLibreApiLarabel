<?php
// app/Models/Suscripcion.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Suscripcion extends Model
{
    protected $table = 'suscripciones'; // Nombre de la tabla en español

    protected $fillable = [
        'usuario_id', 'plan', 'monto', 'fecha_inicio', 'fecha_fin', 'estado',
    ];

    protected $casts = [
        'fecha_inicio' => 'datetime',
        'fecha_fin' => 'datetime',
    ];

    // Relación con el usuario
    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    // Relación con los pagos
    public function pagos()
    {
        return $this->hasMany(Pago::class, 'suscripcion_id');
    }
}
