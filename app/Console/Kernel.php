<?php

namespace App\Console;

use App\Console\Commands\RunPhpFile;
use App\Console\Commands\SendRandomFaucetTweet;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

/**
 * Class Kernel
 *
 * @author  Rob Attfield <emailme@robertattfield.com> <http://www.robertattfield.com>
 * @package App\Console
 */
class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        SendRandomFaucetTweet::class,
        RunPhpFile::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('faucets:send-random-tweet')
            ->hourly()
            ->environments('production');
    }
}
