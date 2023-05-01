<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Log;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('price:check')->everyMinute()         
            ->before(function () {
                Log::info('Initializing Price Check:');
            })
            ->after(function () {
                Log::info('Price Check Job Done!');
            });

        $schedule->command('fxprice:check')->everyMinute()         
            ->before(function () {
                Log::info('Initializing Price Check:');
            })
            ->after(function () {
                Log::info('Price Check Job Done!');
            });

            
            
        $schedule->command('binance:generate-coins')->daily()       
            ->before(function () {
                Log::info('Initializing Generate Coins:');
            })
            ->after(function () {
                Log::info('Generate Coins Job Done!');
            });
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
