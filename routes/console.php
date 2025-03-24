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

