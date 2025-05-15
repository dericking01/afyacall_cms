<?php

namespace App\Console;

use Illuminate\Console\Command;
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
        Commands\SendDailySms::class,
        Commands\unsubscribeCron::class,
        Commands\ProcessChargingDailyCron::class,
        Commands\ProcessMpesaDailyChargeCron::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {

        // $schedule->command('invoice:generator')
        //     ->timezone('Africa/Dar_es_Salaam')
        //     ->lastDayOfMonth('23:30');

        // $schedule->command('unsubscribe:cron')
        //    ->timezone('Africa/Dar_es_Salaam')
        //     ->cron('15,45 0,1,2,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23 * * * ');

        // //run charging airtime
        // $schedule->command('mpesa:daily')
        //     ->timezone('Africa/Dar_es_Salaam')
        // //     // ->cron('0 0,2,4,6,8,10,12,14,16,18,20,22 * * *');
        //     ->cron('*/45 * * * *');
            
        // //current revenue            
        // $schedule->command('sms:revenue')
        // ->timezone('Africa/Dar_es_Salaam')
        // ->cron('45 */5 * * *');

        // //End of Day revenue-report            
        // $schedule->command('sms:revenue-report')
        // ->timezone('Africa/Dar_es_Salaam')
        // ->dailyAt('05:00');

        // // $schedule->command('remove:agedays')
        // //     ->timezone('Africa/Dar_es_Salaam')
        // //     ->lastDayOfMonth('23:00');
            

        // //send sms twice daily
        // $schedule->command('sms:daily')
        //     ->timezone('Africa/Dar_es_Salaam')
        //     ->twiceDaily(7, 19);

        // send sms tips twice daily
        $schedule->command('send:daily-tips')
            ->timezone('Africa/Dar_es_Salaam')
            ->twiceDaily(7, 19);

    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}

