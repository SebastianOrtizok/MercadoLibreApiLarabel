<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SyncTimestamp extends Model
{
    use HasFactory;

    // Indica que se puede asignar masivamente el campo 'timestamp'
    protected $fillable = ['timestamp'];

    // Si necesitas más campos en el futuro, puedes agregarlos al array $fillable
}
