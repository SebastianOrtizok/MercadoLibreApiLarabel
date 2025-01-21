<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // Muestra el formulario de inicio de sesión
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // Maneja el inicio de sesión
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        $credentials = $request->only('email', 'password');

        // Verifica las credenciales del usuario
        if (Auth::attempt($credentials)) {
            return redirect()->intended('/dashboard');
        }

        return back()->withErrors([
            'email' => 'Las credenciales no son correctas.',
        ]);
    }

    // Maneja el cierre de sesión
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate(); // Invalidar la sesión actual.
        $request->session()->regenerateToken(); // Generar un nuevo token CSRF.
        return redirect('/login');
    }
}
