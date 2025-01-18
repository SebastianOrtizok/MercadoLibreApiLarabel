<?php

namespace App\Http\Controllers;

use App\Services\ArticuloService;
use Illuminate\Http\Request;

class ArticuloController extends Controller
{
    protected $articuloService;

    public function __construct(ArticuloService $articuloService)
    {
        $this->articuloService = $articuloService;
    }

    public function obtenerArticulos($userId)
    {
        $articulos = $this->articuloService->obtenerArticulosPorUsuario($userId);
        return response()->json($articulos);
    }

    public function actualizarArticulo(Request $request, $articuloId)
    {
        $data = $request->all();
        $articulo = $this->articuloService->actualizarArticulo($articuloId, $data);
        return response()->json($articulo);
    }

    public function crearArticulo(Request $request)
    {
        $data = $request->all();
        $articulo = $this->articuloService->crearArticulo($data);
        return response()->json($articulo);
    }

    public function actualizarArticulos(Request $request, $userId)
    {
        $nuevosArticulos = $request->input('articulos');
        $this->articuloService->actualizarArticulosDesdePublicaciones($userId, $nuevosArticulos);
        return response()->json(['message' => 'Artículos actualizados']);
    }
}
