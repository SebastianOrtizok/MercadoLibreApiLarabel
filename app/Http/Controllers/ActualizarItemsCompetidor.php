<?php

namespace App\Http\Controllers;

use App\Jobs\ActualizarItemsCompetidor;
use App\Models\Competidor;
use Illuminate\Http\Request;

class CompetidorController extends Controller
{
    public function index()
    {
        $competidores = Competidor::all();
        return view('competidores.index', compact('competidores'));
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
        $competidor = Competidor::findOrFail($request->competidor_id);
        ActualizarItemsCompetidor::dispatch($competidor);

        return redirect()->route('competidores.index')->with('success', 'Actualización en proceso');
    }
}
