<?php
namespace App\Http\Controllers;

use App\Services\SuscripcionesService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SuscripcionesController extends Controller
{
    protected $suscripcionesService;

    public function __construct(SuscripcionesService $suscripcionesService)
    {
        $this->suscripcionesService = $suscripcionesService;
    }

    public function index()
    {
        $suscripciones = $this->suscripcionesService->getAllSuscripciones();
        $users = \App\Models\User::all();
        $planes = ['test', 'prueba_gratuita', 'mensual', 'trimestral', 'anual', 'oferta_del_mes'];
        return view('admin.createsuscripcion', compact('suscripciones', 'users', 'planes'));
    }

    public function create()
    {
        $users = \App\Models\User::all();
        $planes = ['test', 'prueba_gratuita', 'mensual', 'trimestral', 'anual', 'oferta_del_mes'];
        return view('admin.createsuscripcion', compact('users', 'planes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'usuario_id' => 'required|exists:users,id',
            'plan' => 'required|in:test,prueba_gratuita,mensual,trimestral,anual,oferta_del_mes',
            'monto' => 'required|numeric|min:0',
            'estado' => 'required|in:activo,vencido,cancelado',
        ]);

        try {
            // Calcular fecha_inicio y fecha_fin
            $fechaInicio = Carbon::now();
            $fechaFin = null;
            switch ($validated['plan']) {
                case 'mensual':
                    $fechaFin = $fechaInicio->copy()->addDays(30);
                    break;
                case 'trimestral':
                    $fechaFin = $fechaInicio->copy()->addDays(90);
                    break;
                case 'anual':
                    $fechaFin = $fechaInicio->copy()->addDays(365);
                    break;
                case 'oferta_del_mes':
                    $fechaFin = $fechaInicio->copy()->addDays(30); // Ajustar si oferta_del_mes tiene otra duración
                    break;
                // test y prueba_gratuita no tienen fecha_fin
            }

            // Actualizar o crear la suscripción
            $this->suscripcionesService->createSuscripcion([
                'usuario_id' => $validated['usuario_id'],
                'plan' => $validated['plan'],
                'monto' => $validated['monto'],
                'fecha_inicio' => $fechaInicio,
                'fecha_fin' => $fechaFin,
                'estado' => $validated['estado'],
            ]);

            return redirect()->route('admin.suscripciones.index')->with('success', 'Suscripción creada o actualizada exitosamente.');
        } catch (\Exception $e) {
            Log::error('Error al crear suscripción', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Error al crear la suscripción: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $suscripcion = $this->suscripcionesService->getSuscripcionById($id);
        $suscripciones = $this->suscripcionesService->getAllSuscripciones();
        $users = \App\Models\User::all();
        $planes = ['test', 'prueba_gratuita', 'mensual', 'trimestral', 'anual', 'oferta_del_mes'];
        return view('admin.createsuscripcion', compact('suscripcion', 'suscripciones', 'users', 'planes'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'usuario_id' => 'required|exists:users,id',
            'plan' => 'required|in:test,prueba_gratuita,mensual,trimestral,anual,oferta_del_mes',
            'monto' => 'required|numeric|min:0',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
            'estado' => 'required|in:activo,vencido,cancelado',
        ]);

        try {
            $this->suscripcionesService->updateSuscripcion($id, $validated);
            return redirect()->route('admin.suscripciones.index')->with('success', 'Suscripción actualizada exitosamente.');
        } catch (\Exception $e) {
            Log::error('Error al actualizar suscripción', ['error' => $e->getMessage(), 'id' => $id]);
            return redirect()->back()->with('error', 'Error al actualizar la suscripción: ' . $e->getMessage());
        }
    }
}
