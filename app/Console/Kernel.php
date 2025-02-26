<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        \App\Console\Commands\SyncOrdersDaily::class,
    ];

    protected function schedule(Schedule $schedule)
    {
        $schedule->command('orders:sync-daily')
                 ->dailyAt('12:20')
                 ->timezone('America/Argentina/Buenos_Aires')
                 ->onSuccess(function () {
                     \Log::info("Sincronización diaria ejecutada con éxito");
                 })
                 ->onFailure(function () {
                     \Log::error("Fallo en la sincronización diaria");
                 });
    }

    protected function commands()
    {
        $this->load(__DIR__.'/Commands');
        require base_path('routes/console.php');
    }
}
