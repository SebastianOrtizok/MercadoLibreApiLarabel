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
        'ml_product_id',
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
        'logistic_type',
        'inventory_id',
        'user_product_id',
        'precio_original',
        'category_id',
        'en_promocion',
        'descuento_porcentaje',
        'deal_ids',
        'stock_fulfillment', 
        'stock_deposito',
    ];

    // Casting para tipos específicos
    protected $casts = [
        'en_promocion' => 'boolean', // Convierte en_promocion a booleano
        'deal_ids' => 'array',       // Convierte deal_ids a arreglo desde JSON
    ];

    // Relación con la tabla 'mercadolibre_tokens'
    public function mercadolibreToken()
    {
        // Relación inversa: un artículo pertenece a un token de MercadoLibre
        return $this->belongsTo(MercadolibreToken::class, 'user_id', 'id');
    }
}
