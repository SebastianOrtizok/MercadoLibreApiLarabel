<?php

use Illuminate\Support\Facades\Artisan;
use App\Services\StockSyncService;

Artisan::command('orders:sync-daily', function () {
    $this->comment('Ejecutando orders:sync-daily');
    $this->call(\App\Console\Commands\SyncOrdersDaily::class);
})->describe('Sincroniza las órdenes de MercadoLibre diariamente')
  ->dailyAt('22:30');

Artisan::command('articles:sync', function () {
    $this->comment('Ejecutando articles:sync');
    $this->call(\App\Console\Commands\SyncArticles::class);
})->describe('Sincroniza los artículos de MercadoLibre diariamente')
  ->dailyAt('08:00')
  ->withoutOverlapping();

Artisan::command('stocks:sync {userId?}', function ($userId = null) {
    $this->comment('Ejecutando stocks:sync');
    $userId = $userId ?? 1; // Por defecto user_id 1 si no se pasa argumento
    app(StockSyncService::class)->syncStocks($userId);
    $this->info("Sincronización iniciada para user_id: {$userId}");
})->describe('Sincroniza los stocks de fulfillment y depósito diariamente')
  ->dailyAt('09:00')
  ->withoutOverlapping();
