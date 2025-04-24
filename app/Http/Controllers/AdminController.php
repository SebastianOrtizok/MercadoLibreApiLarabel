<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Suscripcion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function index()
    {
        $users = User::all();
        $selectedUser = session('selected_user');
        return view('admin.dashboard', compact('users', 'selectedUser'));
    }

    public function createUser()
    {
        return view('admin.create-user');
    }

    public function storeUser(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'is_admin' => 'boolean',
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
            'is_admin' => $request->has('is_admin'),
        ]);

        return redirect()->route('admin.dashboard')->with('success', 'Usuario creado exitosamente.');
    }

    public function showUser($id)
    {
        $user = User::with(['suscripciones', 'mercadolibreTokens'])->findOrFail($id);
        return view('admin.user-details', compact('user'));
    }

    public function editToken(User $user, $token)
    {
        $token = $user->mercadolibreTokens()->findOrFail($token);
        return view('admin.edit-token', compact('user', 'token'));
    }

    public function updateToken(Request $request, User $user, $token)
    {
        $token = $user->mercadolibreTokens()->findOrFail($token);
        $validated = $request->validate([
            'access_token' => 'required|string',
            'refresh_token' => 'required|string',
            'ml_account_id' => 'required|string',
        ]);

        $token->update($validated);
        return redirect()->route('admin.user-details', $user->id)->with('success', 'Token actualizado exitosamente.');
    }

    public function destroyToken(User $user, $token)
    {
        $token = $user->mercadolibreTokens()->findOrFail($token);
        $token->delete();
        return redirect()->route('admin.user-details', $user->id)->with('success', 'Token eliminado exitosamente.');
    }

    public function selectUser(Request $request)
    {
        $request->validate([
            'selected_user_id' => 'required|exists:users,id',
        ]);

        $request->session()->put('selected_user_id', $request->selected_user_id);
        return redirect()->route('admin.dashboard')->with('success', 'Usuario seleccionado exitosamente.');
    }

    public function clearSelection(Request $request)
    {
        $request->session()->forget('selected_user_id');
        return redirect()->route('admin.dashboard')->with('success', 'Selección de usuario eliminada.');
    }

    public function addInitialToken(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'access_token' => 'required|string',
            'refresh_token' => 'required|string',
            'ml_account_id' => 'required|string',
        ]);

        $user = User::findOrFail($validated['user_id']);
        $user->mercadolibreTokens()->create([
            'access_token' => $validated['access_token'],
            'refresh_token' => $validated['refresh_token'],
            'ml_account_id' => $validated['ml_account_id'],
        ]);

        return redirect()->route('admin.user-details', $user->id)->with('success', 'Token inicial añadido exitosamente.');
    }

    public function exportDatabase(Request $request)
    {
        $tables = $request->input('tables', []);
        $data = [];

        if (in_array('users', $tables)) {
            $data['users'] = \App\Models\User::with(['suscripciones', 'pagos', 'mercadolibreTokens', 'articulos', 'ordenes', 'itemPromotions'])->get()->toArray();
        }
        if (in_array('suscripciones', $tables)) {
            $data['suscripciones'] = \App\Models\Suscripcion::all()->toArray();
        }
        if (in_array('pagos', $tables)) {
            $data['pagos'] = \App\Models\Pago::all()->toArray();
        }
        if (in_array('mercadolibre_tokens', $tables)) {
            $data['mercadolibre_tokens'] = \App\Models\MercadoLibreToken::all()->toArray();
        }
        if (in_array('articulos', $tables)) {
            $data['articulos'] = \App\Models\Articulo::all()->toArray();
        }
        if (in_array('ordenes', $tables)) {
            $data['ordenes'] = \App\Models\Orden::all()->toArray();
        }
        if (in_array('item_promotions', $tables)) {
            $data['item_promotions'] = \App\Models\ItemPromotion::all()->toArray();
        }

        if (empty($data)) {
            return redirect()->back()->with('error', 'No se seleccionaron tablas para exportar.');
        }

        return response()->json($data)
            ->header('Content-Disposition', 'attachment; filename="database_export_' . now()->format('Y-m-d_H-i-s') . '.json"')
            ->header('Content-Type', 'application/json');
    }

    public function importDatabase(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:json',
            'tables' => 'required|array',
            'tables.*' => 'in:users,suscripciones,pagos,mercadolibre_tokens,articulos,ordenes,item_promotions',
        ]);

        $file = $request->file('file');
        $tables = $request->input('tables', []);
        $data = json_decode(file_get_contents($file->path()), true);

        if (empty($data)) {
            return redirect()->back()->with('error', 'El archivo JSON está vacío o no es válido.');
        }

        try {
            if (in_array('users', $tables) && isset($data['users'])) {
                foreach ($data['users'] as $userData) {
                    $user = \App\Models\User::updateOrCreate(
                        ['email' => $userData['email']],
                        [
                            'name' => $userData['name'],
                            'password' => $userData['password'],
                            'created_at' => $userData['created_at'],
                            'updated_at' => $userData['updated_at'],
                        ]
                    );

                    if (isset($userData['suscripciones'])) {
                        foreach ($userData['suscripciones'] as $suscripcionData) {
                            \App\Models\Suscripcion::updateOrCreate(
                                ['usuario_id' => $user->id, 'plan' => $suscripcionData['plan']],
                                array_merge($suscripcionData, ['usuario_id' => $user->id])
                            );
                        }
                    }
                    if (isset($userData['pagos'])) {
                        foreach ($userData['pagos'] as $pagoData) {
                            \App\Models\Pago::updateOrCreate(
                                ['usuario_id' => $user->id, 'id_transaccion' => $pagoData['id_transaccion']],
                                array_merge($pagoData, ['usuario_id' => $user->id])
                            );
                        }
                    }
                    if (isset($userData['mercadolibre_tokens'])) {
                        foreach ($userData['mercadolibre_tokens'] as $tokenData) {
                            \App\Models\MercadoLibreToken::updateOrCreate(
                                ['user_id' => $user->id, 'ml_account_id' => $tokenData['ml_account_id']],
                                array_merge($tokenData, ['user_id' => $user->id])
                            );
                        }
                    }
                    if (isset($userData['articulos'])) {
                        foreach ($userData['articulos'] as $articuloData) {
                            \App\Models\Articulo::updateOrCreate(
                                ['user_id' => $user->id, 'ml_item_id' => $articuloData['ml_item_id']],
                                array_merge($articuloData, ['user_id' => $user->id])
                            );
                        }
                    }
                    if (isset($userData['ordenes'])) {
                        foreach ($userData['ordenes'] as $ordenData) {
                            \App\Models\Orden::updateOrCreate(
                                ['user_id' => $user->id, 'ml_order_id' => $ordenData['ml_order_id']],
                                array_merge($ordenData, ['user_id' => $user->id])
                            );
                        }
                    }
                    if (isset($userData['item_promotions'])) {
                        foreach ($userData['item_promotions'] as $promotionData) {
                            \App\Models\ItemPromotion::updateOrCreate(
                                ['user_id' => $user->id, 'promotion_id' => $promotionData['promotion_id']],
                                array_merge($promotionData, ['user_id' => $user->id])
                            );
                        }
                    }
                }
            }

            if (in_array('suscripciones', $tables) && isset($data['suscripciones'])) {
                foreach ($data['suscripciones'] as $suscripcionData) {
                    \App\Models\Suscripcion::updateOrCreate(
                        ['usuario_id' => $suscripcionData['usuario_id'], 'plan' => $suscripcionData['plan']],
                        $suscripcionData
                    );
                }
            }

            if (in_array('pagos', $tables) && isset($data['pagos'])) {
                foreach ($data['pagos'] as $pagoData) {
                    \App\Models\Pago::updateOrCreate(
                        ['usuario_id' => $pagoData['usuario_id'], 'id_transaccion' => $pagoData['id_transaccion']],
                        $pagoData
                    );
                }
            }

            if (in_array('mercadolibre_tokens', $tables) && isset($data['mercadolibre_tokens'])) {
                foreach ($data['mercadolibre_tokens'] as $tokenData) {
                    \App\Models\MercadoLibreToken::updateOrCreate(
                        ['user_id' => $tokenData['user_id'], 'ml_account_id' => $tokenData['ml_account_id']],
                        $tokenData
                    );
                }
            }

            if (in_array('articulos', $tables) && isset($data['articulos'])) {
                foreach ($data['articulos'] as $articuloData) {
                    \App\Models\Articulo::updateOrCreate(
                        ['user_id' => $articuloData['user_id'], 'ml_item_id' => $articuloData['ml_item_id']],
                        $articuloData
                    );
                }
            }

            if (in_array('ordenes', $tables) && isset($data['ordenes'])) {
                foreach ($data['ordenes'] as $ordenData) {
                    \App\Models\Orden::updateOrCreate(
                        ['user_id' => $ordenData['user_id'], 'ml_order_id' => $ordenData['ml_order_id']],
                        $ordenData
                    );
                }
            }

            if (in_array('item_promotions', $tables) && isset($data['item_promotions'])) {
                foreach ($data['item_promotions'] as $promotionData) {
                    \App\Models\ItemPromotion::updateOrCreate(
                        ['user_id' => $promotionData['user_id'], 'promotion_id' => $promotionData['promotion_id']],
                        $promotionData
                    );
                }
            }

            return redirect()->back()->with('success', 'Datos importados exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al importar datos: ' . $e->getMessage());
        }
    }
}
