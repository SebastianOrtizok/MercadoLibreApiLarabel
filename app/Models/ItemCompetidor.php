<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemCompetidor extends Model
{
    protected $table = 'items_competidores';

    protected $fillable = [
        'competidor_id', 'item_id', 'titulo', 'precio', 'cantidad_disponible',
        'cantidad_vendida', 'envio_gratis', 'ultima_actualizacion', 'imagen', 'following',
    ];

    protected $casts = [
        'ultima_actualizacion' => 'datetime',
        'envio_gratis' => 'boolean',
        'following' => 'boolean',
    ];

    public function competidor()
    {
        return $this->belongsTo(Competidor::class);
    }
}
