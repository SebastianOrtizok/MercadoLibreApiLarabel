<?php
namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ListadoArticulosService
{
    public function getArticulos(array $filters = [])
    {
        $query = DB::table('articulos');

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

        $result = $query->select([
                'id',
                'titulo',
                'precio',
                'stock_actual',
                'estado',
                'sku_interno',
                'imagen',
                'permalink',
            ])
            ->orderBy('titulo', 'asc')
            ->get();

        Log::info('Datos crudos de ListadoArticulosService', ['result_count' => $result->count(), 'sample' => $result->first()]);

        return $result;
    }
}
