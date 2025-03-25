<?php
use Illuminate\Support\Facades\Artisan;

Artisan::command('orders:sync-hourly', function () {
    Log::info('Ejecutando orders:sync-hourly en: ' . Carbon::now());
    $this->comment('Ejecutando orders:sync-hourly');
    $this->call(\App\Console\Commands\SyncOrdersHourly::class);
})->describe('Sincroniza las órdenes de MercadoLibre cada hora')
  ->hourly();

Artisan::command('stock:sync-hourly', function () {
    $this->comment('Ejecutando stock:sync-hourly');
    $this->call(\App\Console\Commands\SyncStockHourly::class);
})->describe('Sincroniza el stock de ventas cada hora')
  ->hourlyAt(5); // Corre a los 5 minutos de cada hora (00:05, 01:05, etc.)
