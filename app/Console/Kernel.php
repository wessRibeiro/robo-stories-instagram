<?php

namespace Louder\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        \Louder\Console\Commands\V1\GetStories::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('Instagram:v1.getStories')
                 ->hourly()
                 ->sendOutputTo('/var/www/log/mylouder/instagram/(date -d \'+ 0 hour\' +\%Y_\%m_\%d__\%H_\%M)__robo_instagram.log');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');
        $this->load(__DIR__.'/Commands/V1');

        require base_path('routes/console.php');
    }
}
