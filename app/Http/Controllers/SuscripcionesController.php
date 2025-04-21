<?php
namespace App\Http\Controllers;

use App\Services\SuscripcionesService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
            'estado' => 'required|in:activo,vencido,cancelado',
        ]);

        try {
            $this->suscripcionesService->createSuscripcion($validated);
            return redirect()->route('admin.suscripciones.index')->with('success', 'Suscripción creada exitosamente.');
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
