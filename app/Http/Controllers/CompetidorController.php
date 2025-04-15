<?php

namespace App\Http\Controllers;

use App\Jobs\ActualizarItemsCompetidor;
use App\Models\Competidor;
use Illuminate\Http\Request;

class CompetidorController extends Controller
{
    public function index()
    {
        $competidores = Competidor::where('user_id', auth()->id())->get();
        $items = \App\Models\ItemCompetidor::whereIn('competidor_id', $competidores->pluck('id'))
            ->with('competidor')
            ->get();
        return view('competidores.index', compact('competidores', 'items'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'seller_id' => 'required|string',
            'nickname' => 'required|string',
            'nombre' => 'required|string',
        ]);

        Competidor::create([
            'user_id' => auth()->id(),
            'seller_id' => $request->seller_id,
            'nickname' => $request->nickname,
            'nombre' => $request->nombre,
        ]);

        return redirect()->route('competidores.index')->with('success', 'Competidor agregado');
    }

    public function actualizar(Request $request)
    {
        $competidor = Competidor::where('user_id', auth()->id())
            ->findOrFail($request->competidor_id);
        $job = new ActualizarItemsCompetidor($competidor);
        $job->handle();

        return redirect()->route('competidores.index')->with('success', 'Actualización completada');
    }

    public function destroy(Request $request)
    {
        $competidor = Competidor::where('user_id', auth()->id())
            ->findOrFail($request->competidor_id);
        $competidor->delete();

        return redirect()->route('competidores.index')->with('success', 'Competidor eliminado');
    }
}
