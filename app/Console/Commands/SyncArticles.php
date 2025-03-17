<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ArticuloSyncService;

class SyncArticles extends Command
{
    protected $signature = 'articles:sync';
    protected $description = 'Sincroniza los artículos de MercadoLibre';

    protected $articuloSyncService;

    public function __construct(ArticuloSyncService $articuloSyncService)
    {
        parent::__construct();
        $this->articuloSyncService = $articuloSyncService;
    }

    public function handle()
    {
        $this->comment('Ejecutando articles:sync');
        $this->articuloSyncService->syncArticulos('1', 20);
        $this->info('Sincronización de artículos completada.');
    }
}
