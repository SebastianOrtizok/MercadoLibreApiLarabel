<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Suscripcion;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Auth\Events\Registered;

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

    // Muestra el formulario de registro
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    // Maneja el registro de un nuevo usuario
    public function register(Request $request)
    {
        Log::info('Iniciando registro de usuario', ['email' => $request->email]);

        // Validación de los datos
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/',
            ],
            'accept_terms' => ['required', 'accepted'],
        ], [
            'name.required' => 'El nombre es obligatorio.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El correo electrónico debe ser válido.',
            'email.unique' => 'El correo electrónico ya está registrado.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
            'password.regex' => 'La contraseña debe contener al menos una letra mayúscula, una minúscula, un número y un carácter especial (@$!%*?&).',
            'accept_terms.required' => 'Debes aceptar los Términos y Condiciones.',
            'accept_terms.accepted' => 'Debes aceptar los Términos y Condiciones.',
        ]);

        if ($validator->fails()) {
            Log::warning('Validación fallida en registro', [
                'email' => $request->email,
                'errors' => $validator->errors()->toArray(),
            ]);
            return back()->withErrors($validator)->withInput();
        }

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'accept_terms' => true,
            ]);
            Log::info('Usuario registrado exitosamente', ['user_id' => $user->id]);

            // Verificar que el usuario_id sea válido
            if (!$user->id) {
                throw new \Exception('El ID del usuario no se generó correctamente');
            }

            // Disparar el evento Registered
            event(new Registered($user));
            Log::info('Evento Registered disparado para: ' . $user->email);

            // Asignar plan de prueba de 7 días
            $suscripcion = Suscripcion::create([
                'usuario_id' => $user->id,
                'plan' => 'prueba_gratuita',
                'monto' => 0.00,
                'fecha_inicio' => Carbon::now(),
                'fecha_fin' => Carbon::now()->addDays(7),
                'estado' => 'activo',
            ]);
            Log::info('Suscripción creada para usuario', [
                'user_id' => $user->id,
                'suscripcion_id' => $suscripcion->id,
            ]);

            // Iniciar sesión automáticamente tras el registro
            Auth::login($user);

            return redirect()->route('dashboard')->with('success', 'Registro exitoso. Se te ha asignado un plan de prueba de 7 días.');
        } catch (\Exception $e) {
            Log::error('Error al registrar usuario o crear suscripción', [
                'error' => $e->getMessage(),
                'email' => $request->email,
                'trace' => $e->getTraceAsString(),
            ]);
            return back()->withErrors(['error' => 'Ocurrió un error al registrar el usuario. Por favor, intenta de nuevo.'])->withInput();
        }
    }

    // Maneja el cierre de sesión
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}
