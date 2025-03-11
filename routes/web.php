<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\PromotionsController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Services\MercadoLibreService;
use App\Services\ItemVenta;
use Illuminate\Http\Request;
use App\Exports\ConsolidadoVentasExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\ItemPromotionsController;
use App\Http\Controllers\OrderDbController;
use App\Http\Controllers\VentasConsolidadasControllerDB;
use App\Http\Controllers\SinVentasController;
use App\Http\Controllers\SkuController;
use App\Http\Controllers\CatalogoController;

Route::get('/catalogo', [CatalogoController::class, 'index'])->name('catalogo.index');

Route::middleware(['auth'])->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/inventory', [AccountController::class, 'showInventory'])->name('dashboard.inventory');
    Route::get('/dashboard/account', [AccountController::class, 'showAccountInfo'])->name('dashboard.account');
    Route::get('/dashboard/order_report', [AccountController::class, 'ShowSales'])->name('dashboard.ventas');
    Route::get('/dashboard/order_report/{item_id?}/{fecha_inicio?}/{fecha_fin?}', [AccountController::class, 'venta_consolidada'])->name('dashboard.ventaid');
    Route::get('/dashboard/ventas_consolidadas/{fecha_inicio?}/{fecha_fin?}', [AccountController::class, 'ventas_consolidadas'])->name('dashboard.ventasconsolidadas');
    Route::get('/dashboard/ventas-consolidadas-db/{fecha_inicio?}/{fecha_fin?}', [VentasConsolidadasControllerDB::class, 'ventasConsolidadas'])->name('dashboard.ventasconsolidadasdb');
    Route::get('/dashboard/sin-ventas', [SinVentasController::class, 'index'])->name('dashboard.sinventas');
    Route::get('/dashboard/publications', [AccountController::class, 'showOwnPublications'])->name('dashboard.publications');
    Route::get('/dashboard/sku', [SkuController::class, 'index'])->name('dashboard.sku');
    Route::patch('/dashboard/sku/update-sku', [SkuController::class, 'updateSku'])->name('dashboard.sku.update-sku');
    Route::post('/dashboard/category/{categoryId}', [AccountController::class, 'showItemsByCategory'])->name('dashboard.category.items');
    Route::get('/dashboard/item_venta', [ItemVenta::class, 'item_venta'])->name('dashboard.itemVenta');
    Route::get('/dashboard/catalogo', [CatalogoController::class, 'index'])->name('dashboard.catalogo');
    Route::get('/dashboard/catalogo/competencia/{mlProductId}', [CatalogoController::class, 'competencia'])->name('dashboard.catalogo.competencia');
    // Viejo PromotionsController (dejamos como está)
    Route::get('/dashboard/promotions', [PromotionsController::class, 'promotions'])->name('dashboard.promociones');

    // Nuevas rutas para ItemPromotionsController
    Route::get('/sync-promotions-db', [ItemPromotionsController::class, 'promotions'])->name('sync.promotions.db'); // Sincronizar
    Route::get('/dashboard/item_promotions', [ItemPromotionsController::class, 'showPromotions'])->name('dashboard.item_promotions'); // Mostrar

    // Rutas de sincronización
    Route::get('/sincronizacion', [AccountController::class, 'sincronizacion'])->name('sincronizacion.index');
    Route::get('/sincronizar/primera/{user_id}', [AccountController::class, 'primeraSincronizacionDB'])->name('sincronizacion.primera');
    Route::get('/sincronizacion/actualizar', [AccountController::class, 'actualizarArticulosDB'])->name('sincronizacion.actualizar');
    Route::get('/sync-orders-db', [OrderDbController::class, 'syncOrders'])->name('sync.orders.db');

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/exportar-ventas', function () {
        $ventas = session('ventas_consolidadas', []);
        return Excel::download(new ConsolidadoVentasExport($ventas), 'ventas_consolidadas.xlsx');
    })->name('exportar.ventas');
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
