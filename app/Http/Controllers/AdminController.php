<?php
namespace App\Http\Controllers;

use App\Models\User;
use App\Models\MercadoLibreToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

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

    public function editToken(User $user, MercadoLibreToken $token)
    {
        return view('admin.edit-mercadolibre-token', compact('user', 'token'));
    }

    public function updateToken(Request $request, User $user, MercadoLibreToken $token)
    {
        $validated = $request->validate([
            'ml_account_id' => 'nullable|string|max:255',
            'seller_name' => 'nullable|string|max:255',
            'access_token' => 'required|string|max:255',
            'refresh_token' => 'required|string|max:255',
            'expires_in' => 'nullable|integer|min:0',
        ]);

        try {
            $expiresIn = $validated['expires_in'] ?? 21600; // Default 6 horas
            $token->update([
                'ml_account_id' => $validated['ml_account_id'],
                'seller_name' => $validated['seller_name'],
                'access_token' => $validated['access_token'],
                'refresh_token' => $validated['refresh_token'],
                'expires_at' => now()->addSeconds((int)$expiresIn),
            ]);

            return redirect()->route('admin.user-details', $user->id)->with('success', 'Token actualizado exitosamente.');
        } catch (\Exception $e) {
            Log::error('Error al actualizar token', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Error al actualizar el token: ' . $e->getMessage());
        }
    }

    public function destroyToken(User $user, MercadoLibreToken $token)
    {
        try {
            $token->delete();
            return redirect()->route('admin.user-details', $user->id)->with('success', 'Token eliminado exitosamente.');
        } catch (\Exception $e) {
            Log::error('Error al eliminar token', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Error al eliminar el token: ' . $e->getMessage());
        }
    }
}
