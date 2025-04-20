<?php
namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ListadoArticulosService
{
    public function getArticulos(array $filters = [])
    {
        $query = DB::table('articulos')
            ->join('mercadolibre_tokens', 'articulos.seller_id', '=', 'mercadolibre_tokens.ml_account_id');

        // Filtrar por user_id si está presente
        if (!empty($filters['user_id'])) {
            $query->where('mercadolibre_tokens.user_id', $filters['user_id']);
        }

        // Aplicar filtros opcionales
        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('articulos.titulo', 'like', "%{$filters['search']}%")
                  ->orWhere('articulos.sku_interno', 'like', "%{$filters['search']}%");
            });
        }
        if (!empty($filters['estado'])) {
            $query->where('articulos.estado', $filters['estado']);
        }

        $result = $query->orderBy('articulos.titulo', 'asc')
            ->get(); // Sin select(), trae todos los campos por defecto

        Log::info('Datos crudos de ListadoArticulosService', [
            'result_count' => $result->count(),
            'sample' => $result->first() ? json_encode($result->first()) : 'No data',
            'user_id' => $filters['user_id'] ?? 'No user filter',
        ]);

        return $result;
    }
}
