<?php

namespace App\Console\Commands;

use App\Models\Customer;
use App\Models\Subscription;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
class unsubscribeCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'unsubscribe:cron';

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

        Subscription::with('customer')
            ->chunkById(100, function ($subscriptions) {
                foreach ($subscriptions as $subscription) {
                    //parse the expiration date to carbon object
                    $expirationDate = Carbon::parse($subscription->ends_at)->subHour(1);
                    $currentDate = Carbon::now();
                    if ($expirationDate <= $currentDate) {
                        $customer = Customer::find($subscription->customer['id']);
                        if ($customer) {
                            Log::info($customer->msisdn . ' removed customer from subscription');
                            //if the product is IVR of Id =1
                            if ($subscription->product_id == 1) {
                                $customer->ivr_status = 0;
                                $customer->save();
                            } else {
                                $customer->status = 0;
                                $customer->save();
                            }
                        }
                        $subscription->delete();
                    }
                }
            });
    }	    
}

