<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class CatalogoService
{
    public function getArticulosEnCatalogo($userId, $filters = [], $limit = 10, $currentPage = 1)
    {
        $tokens = DB::table('mercadolibre_tokens')
            ->where('user_id', $userId)
            ->pluck('ml_account_id')
            ->toArray();

        Log::info('Cuentas de Mercado Libre obtenidas para el usuario', [
            'user_id' => $userId,
            'tokens' => $tokens
        ]);

        if (empty($tokens)) {
            Log::warning('No se encontraron cuentas asociadas al usuario', ['user_id' => $userId]);
            return [
                'articulos' => collect(),
                'total' => 0,
                'totalPages' => 1,
            ];
        }

        $query = DB::table('articulos as a')
            ->leftJoin('mercadolibre_tokens as mt', 'a.user_id', '=', 'mt.ml_account_id')
            ->whereIn('a.user_id', $tokens)
            ->where('a.en_catalogo', 1)
            ->where('a.estado', 'active')
            ->select([
                'a.ml_product_id',
                'a.titulo',
                'a.precio',
                'a.stock_actual',
                'a.tipo_publicacion',
                'a.permalink',
                'a.imagen',
                'mt.seller_name as cuenta_ml'
            ]);

        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('a.titulo', 'like', "%{$filters['search']}%")
                  ->orWhere('a.sku', 'like', "%{$filters['search']}%");
            });
        }
        if (!empty($filters['cuenta_ml'])) {
            $query->where('mt.seller_name', $filters['cuenta_ml']);
        }

        $total = $query->count();
        $articulos = $query->orderBy('a.titulo', 'asc')
                           ->offset(($currentPage - 1) * $limit)
                           ->limit($limit)
                           ->get();

        $totalPages = ceil($total / $limit);

        Log::info('Artículos en catálogo obtenidos', [
            'user_id' => $userId,
            'total' => $total,
            'current_page' => $currentPage,
            'total_pages' => $totalPages,
            'sample' => $articulos->isNotEmpty() ? $articulos->first() : null
        ]);

        return [
            'articulos' => $articulos,
            'total' => $total,
            'totalPages' => $totalPages,
        ];
    }

    public function getCompetenciaArticulo($userId, $mlProductId)
    {
        // Obtener el artículo con user_id explícitamente
        $articulo = DB::table('articulos as a')
            ->leftJoin('mercadolibre_tokens as mt', 'a.user_id', '=', 'mt.ml_account_id')
            ->where('a.ml_product_id', $mlProductId)
            ->where('a.en_catalogo', 1)
            ->where('a.estado', 'active')
            ->select([
                'a.ml_product_id',
                'a.titulo',
                'a.precio',
                'a.stock_actual',
                'a.tipo_publicacion',
                'a.permalink',
                'a.imagen',
                'a.user_id', // Agregamos user_id explícitamente
                'mt.seller_name as cuenta_ml'
            ])
            ->first();

        if (!$articulo) {
            Log::warning('Artículo no encontrado en catálogo', ['ml_product_id' => $mlProductId]);
            throw new \Exception("Artículo no encontrado o no está en catálogo.");
        }

        Log::info('Datos del artículo obtenidos', [
            'ml_product_id' => $mlProductId,
            'user_id' => $articulo->user_id
        ]);

        // Obtener el access_token
        $accessToken = DB::table('mercadolibre_tokens')
            ->where('user_id', $userId)
            ->where('ml_account_id', $articulo->user_id)
            ->value('access_token');

        Log::info('Token de acceso buscado', [
            'user_id' => $userId,
            'ml_account_id' => $articulo->user_id,
            'access_token' => $accessToken ? 'Encontrado' : 'No encontrado'
        ]);

        if (!$accessToken) {
            Log::warning('No se encontró access_token', [
                'user_id' => $userId,
                'ml_account_id' => $articulo->user_id
            ]);
            throw new \Exception("No se encontró un token de acceso para esta cuenta.");
        }

        // Consultar la API con el endpoint correcto
        $response = Http::withToken($accessToken)
            ->get("https://api.mercadolibre.com/items/{$mlProductId}/price_to_win?version=v2");

        if ($response->successful()) {
            $competencia = $response->json();
            Log::info('Datos de competencia obtenidos', [
                'ml_product_id' => $mlProductId,
                'competencia' => $competencia
            ]);
        } else {
            Log::warning('Error al consultar price_to_win', [
                'ml_product_id' => $mlProductId,
                'status' => $response->status(),
                'response' => $response->body()
            ]);
            $competencia = null;
        }

        return [
            'articulo' => $articulo,
            'competencia' => $competencia
        ];
    }
}
