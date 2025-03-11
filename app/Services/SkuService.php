<?php
namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SkuService
{
    public function getProductos(array $filters = [])
    {
        $userId = auth()->id();
        $tokens = DB::table('mercadolibre_tokens')->where('user_id', $userId)->pluck('ml_account_id')->toArray();
        Log::info('Tokens obtenidos para user_id ' . $userId, ['tokens' => $tokens]);

        if (empty($tokens)) {
            throw new \Exception('No se encontraron cuentas asociadas al usuario.');
        }

        $query = DB::table('articulos as a')
            ->leftJoin('mercadolibre_tokens as mt', 'a.user_id', '=', 'mt.ml_account_id')
            ->whereIn('a.user_id', $tokens);

        // Aplicar filtros opcionales
        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('a.sku_interno', 'like', "%{$filters['search']}%")
                  ->orWhere('a.titulo', 'like', "%{$filters['search']}%");
            });
        }
        if (!empty($filters['ml_account_id'])) {
            $query->where('a.user_id', $filters['ml_account_id']);
        }
        if (!empty($filters['tipo_publicacion'])) {
            $query->where('a.tipo_publicacion', $filters['tipo_publicacion']);
        }
        if (!empty($filters['estado_publicacion'])) {
            $query->where('a.estado', $filters['estado_publicacion']);
        }

        $result = $query->select(
                'mt.seller_name as usuario',
                'a.imagen',
                'a.titulo',
                'a.precio',
                'a.condicion',
                'a.stock_actual',
                'a.estado',
                'a.sku_interno as sku',
                'a.tipo_publicacion',
                DB::raw('CASE WHEN a.en_catalogo = 1 THEN "Sí" WHEN a.en_catalogo = 0 THEN "No" ELSE "N/A" END as catalogo'),
                'a.category_id as categoria',
                'a.ml_product_id',
                'a.permalink'
            )
            ->orderBy('a.titulo', 'asc')
            ->get();

        Log::info('Datos crudos de SkuService', ['result_count' => $result->count(), 'sample' => $result->first()]);

        return $result;
    }
}
