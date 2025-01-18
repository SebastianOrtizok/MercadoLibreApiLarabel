<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Articulo extends Model
{
    use HasFactory;

    // Nombre de la tabla en la base de datos
    protected $table = 'articulos';

    // Columnas que son asignables en masa (mass assignment)
    protected $fillable = [
        'user_id',
        'ml_product_id',  // Agregado el campo ml_product_id
        'titulo',
        'imagen',
        'stock_actual',
        'precio',
        'estado',
        'permalink',
        'condicion',
        'sku',
        'tipo_publicacion',
        'en_catalogo',
    ];

    // Relación con la tabla 'mercadolibre_tokens'
    public function mercadolibreToken()
    {
        // Relación inversa: un artículo pertenece a un token de MercadoLibre
        return $this->belongsTo(MercadolibreToken::class, 'user_id', 'id');
    }
}
