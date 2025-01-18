<?php
namespace App\Http\Controllers;

use App\Models\Articulo;
use App\Services\MercadoLibreService;
use Illuminate\Http\Request;

class BdMercadoLibreController extends Controller
{
    protected $mercadoLibreService;

    public function __construct(MercadoLibreService $mercadoLibreService)
    {
        $this->mercadoLibreService = $mercadoLibreService;
    }

    public function index()
    {
        return view('sincronizacion.index'); // Vista para mostrar el estado de la sincronización
    }

    public function primeraSincronizacion()
    {
        // Aquí llamamos a la función que obtiene todos los artículos de MercadoLibre
        $articulos = $this->mercadoLibreService->obtenerArticulosDesdeMercadoLibre();

        foreach ($articulos as $articulo) {
            Articulo::updateOrCreate(
                ['sku' => $articulo['sku']],  // Clave primaria o única
                $articulo  // Los datos que deseas guardar o actualizar
            );
        }

        return redirect()->route('sincronizacion.index')->with('success', 'Primera sincronización completada.');
    }

    public function actualizarArticulos()
    {
        // Aquí llamamos a la función que solo obtiene los artículos nuevos o actualizados
        $articulos = $this->mercadoLibreService->obtenerArticulosActualizados();

        foreach ($articulos as $articulo) {
            Articulo::updateOrCreate(
                ['sku' => $articulo['sku']],  // Clave primaria o única
                $articulo  // Los datos que deseas guardar o actualizar
            );
        }

        return redirect()->route('sincronizacion.index')->with('success', 'Artículos actualizados correctamente.');
    }
}
