<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * I comandi Artisan custom registrati.
     *
     * @var array<class-string>
     */
    protected $commands = [
        \App\Console\Commands\ItemsCreateCommand::class,
    ];

    /**
     * Definisci la pianificazione dei comandi.
     */
    protected function schedule(Schedule $schedule): void
    {
        // esempio:
        // $schedule->command('inspire')->hourly();
    }

    /**
     * Registra i comandi per l'applicazione.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}