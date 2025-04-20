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
use App\Http\Controllers\ArticuloSyncController;
use App\Http\Controllers\StockSyncController;
use App\Http\Controllers\StockVentaController;
use App\Http\Controllers\ListadoArticulosController;
use App\Http\Controllers\SyncVentasStockController;
use App\Http\Controllers\EstadisticasController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CompetidorController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\CompetidorXCategoriaController;

Route::post('/payment/ipn', [PaymentController::class, 'ipn'])->name('payment.ipn'); // IPN no necesita auth
Route::group(['middleware' => ['auth', \App\Http\Middleware\AdminMiddleware::class]], function () {

// URL PLANES
    Route::get('/plans', [PaymentController::class, 'showPlans'])->name('plans');
    Route::post('/payment', [PaymentController::class, 'createPayment'])->name('payment.create');
    Route::get('/payment/success', [PaymentController::class, 'success'])->name('payment.success');
    Route::get('/payment/failure', [PaymentController::class, 'failure'])->name('payment.failure');
    Route::get('/payment/pending', [PaymentController::class, 'pending'])->name('payment.pending');


    //Rutas de admin
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.dashboard');
    Route::get('/admin/create-user', [AdminController::class, 'createUser'])->name('admin.create-user');
    Route::post('/admin/store-user', [AdminController::class, 'storeUser'])->name('admin.store-user');
    Route::get('/admin/user/{id}', [AdminController::class, 'showUser'])->name('admin.user-details');
    Route::post('/admin/select-user', [AdminController::class, 'selectUser'])->name('admin.select-user');
    Route::get('/admin/clear-selection', [AdminController::class, 'clearSelection'])->name('admin.clear-selection');
    Route::post('/admin/add-initial-token', [AdminController::class, 'addInitialToken'])->name('admin.add-initial-token');
});
Route::get('/catalogo', [CatalogoController::class, 'index'])->name('catalogo.index');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/inventory', [AccountController::class, 'showInventory'])->name('dashboard.inventory');
    Route::get('/dashboard/account', [AccountController::class, 'showAccountInfo'])->name('dashboard.account');
    Route::get('/dashboard/listado-articulos', [ListadoArticulosController::class, 'index'])->name('dashboard.listado_articulos');
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
    Route::post('/promotions/renew/{promotion_id}', [ItemPromotionsController::class, 'renewPromotion'])->name('promotions.renew');
    Route::get('/sync-promotions-db', [ItemPromotionsController::class, 'syncPromotions'])->name('sync.promotions.db'); // Sincronizar
    Route::get('/dashboard/item_promotions', [ItemPromotionsController::class, 'showPromotions'])->name('dashboard.item_promotions'); // Mostrar
    // Rutas de estadísticas
    Route::get('/dashboard/estadisticas', [EstadisticasController::class, 'index'])->name('dashboard.estadisticas');
    // Rutas de sincronización
    Route::get('/dashboard/stock/sync', [StockSyncController::class, 'sync'])->name('dashboard.stock.sync');
    Route::get('/dashboard/sync-ventas-stock-now', [SyncVentasStockController::class, 'syncAllNow'])->name('dashboard.sync.ventas.stock');
    Route::get('/dashboard/stock/syncventas', [StockVentaController::class, 'sync'])->name('dashboard.stock.syncventas');
    Route::get('/articulos/sync', [ArticuloSyncController::class, 'sync'])->name('articulos.sync');
    Route::get('/sincronizacion', [AccountController::class, 'sincronizacion'])->name('sincronizacion.index');
    Route::get('/sincronizar/primera/{user_id}', [AccountController::class, 'primeraSincronizacionDB'])->name('sincronizacion.primera');
    Route::post('/missing-articles/sync/{mlAccountId}', [App\Http\Controllers\MissingArticlesController::class, 'sync'])->name('missing.articles.sync');
    //Route::get('/sincronizacion/actualizar', [AccountController::class, 'actualizarArticulosDB'])->name('sincronizacion.actualizar');
    Route::get('/sync-orders-db', [OrderDbController::class, 'syncOrders'])->name('sync.orders.db');

    Route::get('/competidores/category', [CompetidorXCategoriaController::class, 'index'])->name('competidores.category');
Route::post('/competidores/category', [CompetidorXCategoriaController::class, 'index'])->name('competidores.category.analyze');
    Route::get('/competidores', [CompetidorController::class, 'index'])->name('competidores.index');
    Route::post('/competidores', [CompetidorController::class, 'store'])->name('competidores.store');
    Route::post('/competidores/actualizar', [CompetidorController::class, 'actualizar'])->name('competidores.actualizar');
    Route::delete('/competidores', [CompetidorController::class, 'destroy'])->name('competidores.destroy');



    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/exportar-ventas', function () {
        $ventas = session('ventas_consolidadas', []);
        return Excel::download(new ConsolidadoVentasExport($ventas), 'ventas_consolidadas.xlsx');
    })->name('exportar.ventas');
});
Route::get('/', [HomeController::class, 'index'])->name('home');
// Rutas de autenticación
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
