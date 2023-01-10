<?php

namespace App\Console\Commands;

use App\Jobs\ProcesSendDailySMS;
use App\Models\Subscription;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendDailySms extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sms:daily';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        //start sending contents
        Subscription::with('customer')->where('status', 1)
            ->where('product_id', 2)
            ->chunkById(100, function ($subscriptions) {
                foreach ($subscriptions as $subscription) {
                    Log::info($subscription->customer['msisdn']);
                    ProcesSendDailySMS::dispatch($subscription->customer['msisdn'], $subscription->customer['content']);
                }
            });
    }
}

