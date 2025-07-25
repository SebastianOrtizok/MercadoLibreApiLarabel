<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MercadoLibreToken extends Model
{
    use HasFactory;

    protected $table = 'mercadolibre_tokens';

    protected $fillable = [
        'user_id',
        'ml_account_id',
        'seller_name',
        'access_token',
        'refresh_token',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
