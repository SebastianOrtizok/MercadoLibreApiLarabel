<?php
namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ListadoArticulosService
{
    public function getArticulos(array $filters = [])
    {
        // Obtener los ml_account_id asociados al user_id del usuario logueado
        $mlAccountIds = [];
        if (!empty($filters['user_id'])) {
            $mlAccountIds = DB::table('mercadolibre_tokens')
                ->where('user_id', $filters['user_id'])
                ->pluck('ml_account_id')
                ->toArray();
        }

        // Si no hay ml_account_id, retornar colección vacía para evitar errores
        if (empty($mlAccountIds)) {
            Log::warning('No se encontraron ml_account_id para el usuario', [
                'user_id' => $filters['user_id'] ?? 'No user_id',
            ]);
            return collect([]);
        }

        // Construir la consulta para artículos
        $query = DB::table('articulos')
            ->whereIn('user_id', $mlAccountIds); // Filtrar por ml_account_id

        // Aplicar filtros opcionales
        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('titulo', 'like', "%{$filters['search']}%")
                  ->orWhere('sku_interno', 'like', "%{$filters['search']}%");
            });
        }
        if (!empty($filters['estado'])) {
            $query->where('estado', $filters['estado']);
        }

        $result = $query->orderBy('titulo', 'asc')
            ->get();

        Log::info('Datos crudos de ListadoArticulosService', [
            'result_count' => $result->count(),
            'sample' => $result->first() ? json_encode($result->first()) : 'No data',
            'user_id' => $filters['user_id'] ?? 'No user filter',
            'ml_account_ids' => $mlAccountIds,
        ]);

        return $result;
    }
}
