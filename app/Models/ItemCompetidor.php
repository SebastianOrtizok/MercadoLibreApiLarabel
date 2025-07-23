<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemCompetidor extends Model
{
    protected $table = 'items_competidores';

    protected $fillable = [
        'competidor_id',
        'item_id',
        'titulo',
        'precio',
        'precio_descuento',
        'precio_sin_impuestos',
        'info_cuotas',
        'url',
        'categorias', // Agregar el campo aquí
        'es_full',
        'envio_gratis',
        'cantidad_disponible',
        'cantidad_vendida',
        'following',
        'ultima_actualizacion',
    ];

    protected $dates = ['ultima_actualizacion']; // Esto hace que Laravel trate el campo como fecha

    public function competidor()
    {
        return $this->belongsTo(Competidor::class);
    }
}
