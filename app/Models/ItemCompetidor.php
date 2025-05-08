<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemCompetidor extends Model
{
    protected $table = 'items_competidores';

    protected $fillable = [
        'competidor_id', 'item_id', 'titulo', 'precio', 'precio_descuento', 'url', 'es_full',
        'cantidad_disponible', 'cantidad_vendida', 'envio_gratis', 'ultima_actualizacion',
        'imagen', 'following',
    ];

    protected $casts = [
        'ultima_actualizacion' => 'datetime',
        'envio_gratis' => 'boolean',
        'es_full' => 'boolean',
        'following' => 'boolean',
    ];

    public function competidor()
    {
        return $this->belongsTo(Competidor::class);
    }
}
