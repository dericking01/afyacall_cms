<?php

namespace App\Console\Commands;

use App\Jobs\ProcessLanguage;
use App\Models\Subscription;
use Illuminate\Console\Command;

class SendIVRNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ivr:daily';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'send ivr reminder';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        //Notify Customer who subscribe ivr to listen to it
        Subscription::with('customer')->where('status', 1)
            ->where('product_id', 1)
            ->chunkById(100, function ($subscriptions) {
                foreach ($subscriptions as $subscription) {
                    $sw = 'Ndugu Mteja, tunakukumbusha kusikilza ujumbe wako mfupi wa Sauti wa siku kwa kupiga 0900011111.';
                    $en = 'Dear Subscriber, this is a reminder to listen to your daily health tip, DIAL 0900011111.';
                    ProcessLanguage::dispatchSync($subscription->customer['msisdn'], $sw, $en);
                }
            });
    }
}

