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
        \Louder\Console\Commands\V1\GetStoriesGallo::class,
        \Louder\Console\Commands\V1\GetStoriesNissin::class,
        \Louder\Console\Commands\V1\GetStoriespassionclub::class,
        \Louder\Console\Commands\V1\GetStoriesLouder::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('Instagram:V1.GetStoriesLouder')
            ->hourly()
            ->sendOutputTo('/var/www/log/mylouder/louderbase/'.date('(d-m-Y)_H_m_s').'_louderbase.log');
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
