<?php
namespace App\Services;

use App\Models\Suscripcion;
use Illuminate\Support\Facades\Log;

class SuscripcionesService
{
    public function getAllSuscripciones()
    {
        return Suscripcion::with('usuario')->get();
    }

    public function getSuscripcionById($id)
    {
        $suscripcion = Suscripcion::with('usuario')->findOrFail($id);
        return $suscripcion;
    }

    public function createSuscripcion(array $data)
    {
        $suscripcion = Suscripcion::create([
            'usuario_id' => $data['usuario_id'],
            'plan' => $data['plan'],
            'monto' => $data['monto'],
            'fecha_inicio' => $data['fecha_inicio'],
            'fecha_fin' => $data['fecha_fin'],
            'estado' => $data['estado'],
        ]);

        Log::info('Suscripción creada', ['suscripcion_id' => $suscripcion->id, 'usuario_id' => $data['usuario_id']]);
        return $suscripcion;
    }

    public function updateSuscripcion($id, array $data)
    {
        $suscripcion = Suscripcion::findOrFail($id);
        $suscripcion->update([
            'usuario_id' => $data['usuario_id'],
            'plan' => $data['plan'],
            'monto' => $data['monto'],
            'fecha_inicio' => $data['fecha_inicio'],
            'fecha_fin' => $data['fecha_fin'],
            'estado' => $data['estado'],
        ]);

        Log::info('Suscripción actualizada', ['suscripcion_id' => $suscripcion->id, 'usuario_id' => $data['usuario_id']]);
        return $suscripcion;
    }
}
