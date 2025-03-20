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

        $result = $query->orderBy('titulo', 'asc')
            ->get(); // Sin select(), trae todos los campos por defecto

        Log::info('Datos crudos de ListadoArticulosService', [
            'result_count' => $result->count(),
            'sample' => $result->first() ? json_encode($result->first()) : 'No data'
        ]);

        return $result;
    }
}
