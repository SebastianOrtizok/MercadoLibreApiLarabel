<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Competidor extends Model
{
    // Nombre de la tabla asociada
    protected $table = 'competidores';

    // Campos que se pueden llenar masivamente
    protected $fillable = ['user_id', 'seller_id', 'nickname', 'nombre', 'official_store_id'];

    // Indicar si el modelo usa timestamps (creado por defecto, pero lo explicitamos)
    public $timestamps = true;

    // Relación con el usuario (el que está logueado)
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relación con los ítems del competidor
    public function items()
    {
        return $this->hasMany(ItemCompetidor::class, 'competidor_id');
    }

    // Método para verificar si es una tienda oficial
    public function isOfficialStore()
    {
        return !is_null($this->official_store_id);
    }
}
