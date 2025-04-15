<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\MercadoLibreToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function index()
    {
        $users = User::with('mercadolibreTokens')->get();
        return view('admin.dashboard', compact('users'));
    }

    public function createUser()
    {
        return view('admin.create-user');
    }

    public function storeUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'is_admin' => false,
        ]);

        return redirect()->route('admin.dashboard')->with('success', 'Usuario creado correctamente.');
    }

    public function showUser($id)
    {
        $user = User::with('mercadolibreTokens')->findOrFail($id);
        return view('admin.user-details', compact('user'));
    }

    public function selectUser(Request $request)
    {
        $request->validate([
            'selected_user_id' => 'required|exists:users,id',
        ]);

        $selectedUserId = $request->input('selected_user_id');
        session(['selected_user_id' => $selectedUserId]);

        return redirect()->route('admin.dashboard')->with('success', 'Usuario seleccionado correctamente.');
    }

    public function clearSelection()
    {
        session()->forget('selected_user_id');
        return redirect()->route('admin.dashboard')->with('success', 'Impersonificación desactivada.');
    }

    public function addInitialToken(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'ml_account_id' => 'required|string',
            'access_token' => 'required|string',
            'refresh_token' => 'required|string',
            'expires_in' => 'nullable|integer|min:1',
        ]);

        $userId = $request->input('user_id');
        $mlAccountId = $request->input('ml_account_id');
        $accessToken = $request->input('access_token');
        $refreshToken = $request->input('refresh_token');
        $expiresIn = $request->input('expires_in', 21600); // Default 6 horas

        try {
            MercadoLibreToken::updateOrCreate(
                [
                    'user_id' => $userId,
                    'ml_account_id' => $mlAccountId,
                ],
                [
                    'access_token' => $accessToken,
                    'refresh_token' => $refreshToken,
                    'expires_at' => now()->addSeconds((int)$expiresIn),
                ]
            );

            return redirect()->route('admin.dashboard')->with('success', 'Token guardado correctamente.');
        } catch (\Exception $e) {
            return redirect()->route('admin.dashboard')->with('error', 'Error al guardar el token: ' . $e->getMessage());
        }
    }
}
