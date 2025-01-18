<?php

namespace App\Services;

use App\Models\Articulo;

class ArticuloService
{
    /**
     * Obtener todos los artículos relacionados con el usuario logueado.
     *
     * @param int $userId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function obtenerArticulosPorUsuario(int $userId)
    {
        return Articulo::where('user_id', $userId)->get();
    }

    /**
     * Obtener un artículo por su ID.
     *
     * @param int $articuloId
     * @return \App\Models\Articulo|null
     */
    public function obtenerArticuloPorId(int $articuloId)
    {
        return Articulo::find($articuloId);
    }

    /**
     * Actualizar un artículo con nuevos datos.
     *
     * @param int $articuloId
     * @param array $data
     * @return \App\Models\Articulo
     */
    public function actualizarArticulo(int $articuloId, array $data)
    {
        $articulo = $this->obtenerArticuloPorId($articuloId);

        if ($articulo) {
            $articulo->update($data);
        }

        return $articulo;
    }

    /**
     * Crear un nuevo artículo.
     *
     * @param array $data
     * @return \App\Models\Articulo
     */
    public function crearArticulo(array $data)
    {
        return Articulo::create($data);
    }

    /**
     * Actualizar todos los artículos de un usuario a partir de una nueva lista de publicaciones.
     *
     * @param int $userId
     * @param array $nuevosArticulos
     * @return void
     */
    public function actualizarArticulosDesdePublicaciones(int $userId, array $nuevosArticulos)
    {
        foreach ($nuevosArticulos as $articuloData) {
            // Comprobar si el artículo ya existe
            $articulo = Articulo::where('user_id', $userId)
                                ->where('sku', $articuloData['sku']) // Suponiendo que el SKU es único
                                ->first();

            if ($articulo) {
                // Actualizar el artículo existente
                $articulo->update($articuloData);
            } else {
                // Crear un nuevo artículo si no existe
                $articuloData['user_id'] = $userId;
                Articulo::create($articuloData);
            }
        }
    }
}
