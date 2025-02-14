<?php
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Services\MercadoLibreService;
use App\Services\ItemVenta;
use Illuminate\Http\Request;



Route::middleware(['auth'])->group(function () {
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/dashboard/inventory', [AccountController::class, 'showInventory'])->name('dashboard.inventory');
Route::get('/dashboard/account', [AccountController::class, 'showAccountInfo'])->name('dashboard.account');
Route::get('/dashboard/order_report', [AccountController::class, 'ShowSales'])->name('dashboard.ventas');
Route::get('/dashboard/order_report/{item_id?}/{fecha_inicio?}/{fecha_fin?}', [AccountController::class, 'venta_consolidada'])->name('dashboard.ventaid');
Route::get('/dashboard/ventas_consolidadas/{fecha_inicio?}/{fecha_fin?}', [AccountController::class, 'ventas_consolidadas'])->name('dashboard.ventasconsolidadas');
Route::get('/dashboard/publications', [AccountController::class, 'showOwnPublications'])->name('dashboard.publications');
Route::post('dashboard/publications', [AccountController::class, 'showOwnPublications'])->name('dashboard.publications');
Route::post('/dashboard/category/{categoryId}', [AccountController::class, 'showItemsByCategory'])->name('dashboard.category.items');
Route::get('/dashboard/category/{categoryId}', [AccountController::class, 'showItemsByCategory'])->name('dashboard.category.items');
// Route::get('/dashboard/analyze-low-conversion', [AccountController::class, 'analyzeLowConversion'])->name('dashboard.analyze.low_conversion');
Route::get('/dashboard/item_venta', [ItemVenta::class, 'item_venta'])->name('dashboard.itemVenta');
Route::get('/sincronizacion', [AccountController::class, 'sincronizacion'])->name('sincronizacion.index');
Route::get('/sincronizacion/primera', [AccountController::class, 'primeraSincronizacionDB'])->name('sincronizacion.primera');
Route::get('/sincronizacion/actualizar', [AccountController::class, 'actualizarArticulosDB'])->name('sincronizacion.actualizar');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);


Route::get('/add-initial-token', function () {
    return view('add-token');
});

Route::post('/add-initial-token', function () {
    $userId = request('user_id');
    $mlAccountId = request('ml_account_id');
    $accessToken = request('access_token');
    $refreshToken = request('refresh_token');
    $expiresIn = request('expires_in', 21600); // Usamos 21600 por defecto

    // Validar los datos
    if (!$userId || !$mlAccountId || !$accessToken || !$refreshToken) {
        return response()->json(['error' => 'Faltan parámetros.'], 400);
    }

    // Guardar o actualizar en la tabla
    App\Models\MercadoLibreToken::updateOrCreate(
        [
            'user_id' => $userId,
            'ml_account_id' => $mlAccountId,
        ],
        [
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken,
            'expires_at' => now()->addSeconds((int)$expiresIn), // Si necesitas la fecha
        ]
    );

    return response()->json(['message' => 'Token guardado correctamente.'], 200);
});
