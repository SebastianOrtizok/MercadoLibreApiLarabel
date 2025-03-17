<?php
use Illuminate\Support\Facades\Artisan;

Artisan::command('orders:sync-daily', function () {
    $this->comment('Ejecutando orders:sync-daily');
    // Acá podrías poner la lógica de sincronización directamente
    // O mejor, llamar a tu comando existente:
    $this->call(\App\Console\Commands\SyncOrdersDaily::class);
})->describe('Sincroniza las órdenes de MercadoLibre diariamente')
  ->dailyAt('22:30');

  Artisan::command('articles:sync', function () {
    $this->comment('Ejecutando articles:sync');
    $this->call(\App\Console\Commands\SyncArticles::class);
})->describe('Sincroniza los artículos de MercadoLibre cada 15 minutos')
  ->dailyAt('08:00')
  ->withoutOverlapping();
