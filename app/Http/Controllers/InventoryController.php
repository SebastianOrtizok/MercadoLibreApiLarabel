<?php

namespace App\Http\Controllers;

use App\Services\MercadoLibreService;

class InventoryController extends Controller
{
    protected $mercadoLibreService;

    public function __construct(MercadoLibreService $mercadoLibreService)
    {
        $this->mercadoLibreService = $mercadoLibreService;
    }

    public function showInventory()
    {
        try {
            $data = $this->mercadoLibreService->getInventory();

            if (empty($data) || !isset($data['results']) || !is_array($data['results'])) {
                throw new \Exception("La respuesta del inventario es inválida.");
            }

            // Asegúrate de pasar un array limpio a la vista
            $inventory = $data['results'];
            //dd($inventory); // Usa `dd($publications)` para revisar qué datos estás recibiendo.
            return view('dashboard.inventory', ['inventory' => $inventory]);

        } catch (\Exception $e) {
            \Log::error("Error al obtener el inventario: " . $e->getMessage());

            return response()->json([
                'inventory' => [],
                'total' => 0
            ])->setStatusCode(500);
        }
    }
}
