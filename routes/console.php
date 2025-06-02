<?php
use Illuminate\Support\Facades\Artisan;
use App\Models\Competidor;
use App\Jobs\ActualizarItemsCompetidor;
use App\Jobs\ArticuloSyncJob;
use App\Jobs\StockSyncJob;
use App\Jobs\SyncItemPromotionsJob;
use App\Models\Articulo;

Artisan::command('orders:sync-hourly', function () {
    $this->comment('Ejecutando orders:sync-hourly');
    $this->call(\App\Console\Commands\SyncOrdersHourly::class);
})->describe('Sincroniza las órdenes de MercadoLibre al final del día')
  ->dailyAt('23:00');

Artisan::command('stock:sync-hourly', function () {
    $this->comment('Ejecutando stock:sync-hourly');
    $this->call(\App\Console\Commands\SyncStockHourly::class);
})->describe('Sincroniza el stock de ventas al final del día')
  ->dailyAt('23:00');

// Comandos desactivados por ahora (solo queremos ventas y stock al final del día)
/*
Artisan::command('competitors:items-sync', function () {
    $this->comment('Ejecutando competitors:items-sync');
    $competitors = Competidor::all();
    foreach ($competitors as $competitor) {
        ActualizarItemsCompetidor::dispatch($competitor);
    }
    $this->info('Jobs de sincronización de ítems de competidores despachados: ' . $competitors->count());
})->describe('Sincroniza ítems de competidores cada 2 horas')
  ->everyTwoHours();

Artisan::command('items:sync-hourly', function () {
    $this->comment('Ejecutando items:sync-hourly');
    $accounts = \App\Models\MLAccount::all();
    foreach ($accounts as $account) {
        $itemIds = Articulo::where('user_id', $account->id)
            ->where('estado', 'active')
            ->pluck('ml_product_id')
            ->toArray();

        $chunks = array_chunk($itemIds, 50);
        foreach ($chunks as $chunk) {
            ArticuloSyncJob::dispatch($chunk, $account->access_token, $account->user_id, $account->id);
        }
    }
    $this->info('Jobs de sincronización de ítems despachados para ' . $accounts->count() . ' cuentas');
})->describe('Sincroniza ítems de las cuentas cada hora')
  ->hourlyAt(15);

Artisan::command('stock:deep-sync-hourly', function () {
    $this->comment('Ejecutando stock:deep-sync-hourly');
    $accounts = \App\Models\MLAccount::all();
    foreach ($accounts as $account) {
        StockSyncJob::dispatch($account->user_id, $account->id);
    }
    $this->info('Jobs de sincronización profunda de stock despachados para ' . $accounts->count() . ' cuentas');
})->describe('Sincroniza stock de artículos cada hora')
  ->hourlyAt(30);

Artisan::command('promotions:sync', function () {
    $this->comment('Ejecutando promotions:sync');
    $accounts = \App\Models\MLAccount::all();
    foreach ($accounts as $account) {
        $products = Articulo::where('user_id', $account->id)
            ->where('estado', 'active')
            ->get();
        SyncItemPromotionsJob::dispatch($products, $account->access_token, $account->id);
    }
    $this->info('Jobs de sincronización de promociones despachados para ' . $accounts->count() . ' cuentas');
})->describe('Sincroniza promociones de ítems cada 4 horas')
  ->everyFourHours();
*/
