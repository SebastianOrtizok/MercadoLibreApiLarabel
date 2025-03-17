<?php
namespace App\Console;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;


class Kernel extends ConsoleKernel
{
    protected $commands = [
        \App\Console\Commands\SyncOrdersDaily::class,
        \App\Console\Commands\SyncArticles::class, 
    ];

    protected function schedule(Schedule $schedule)
    {

    }

    protected function commands()
    {
        $this->load(__DIR__.'/Commands');
        require base_path('routes/console.php');
    }
}
