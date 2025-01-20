<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Muestra la vista de inicio.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('dashboard.index'); // Asegúrate de tener una vista llamada 'home.blade.php' en resources/views
    }
}
