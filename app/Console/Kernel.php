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
        \Louder\Console\Commands\V1\GetStoriesOktober::class,
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
                 ->hourlyAt(1)
                 ->sendOutputTo('/var/www/log/mylouder/instagram/'.date('d_m_Y_H_m_s').'_robo_instagram.log');
        $schedule->command('Instagram:v1.getStoriesOktober')
            ->hourlyAt(10)
            ->sendOutputTo('/var/www/log/mylouder/oktoberfest/'.date('d_m_Y_H_m_s').'_robo_oktoberfest.log');
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
