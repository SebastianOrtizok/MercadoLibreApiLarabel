<?php
use Illuminate\Support\Facades\Artisan;

Artisan::command('orders:sync-daily', function () {
    $this->comment('Ejecutando orders:sync-daily');
    // Acá podrías poner la lógica de sincronización directamente
    // O mejor, llamar a tu comando existente:
    $this->call(\App\Console\Commands\SyncOrdersDaily::class);
})->describe('Sincroniza las órdenes de MercadoLibre diariamente')
  ->dailyAt('22:30');

