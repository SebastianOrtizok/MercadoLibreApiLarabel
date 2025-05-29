<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemCompetidor extends Model
{
    protected $table = 'items_competidores'; // Asegurate de que esta línea esté presente

    protected $fillable = [
        'competidor_id',
        'item_id',
        'titulo',
        'precio',
        'precio_descuento',
        'precio_sin_impuestos',
        'info_cuotas',
        'url',
        'es_full',
        'envio_gratis',
        'cantidad_disponible',
        'cantidad_vendida',
        'following',
        'ultima_actualizacion',
    ];

    public function competidor()
    {
        return $this->belongsTo(Competidor::class);
    }
}
