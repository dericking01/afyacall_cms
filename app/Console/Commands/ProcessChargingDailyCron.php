<?php

namespace App\Console\Commands;

use App\Jobs\ProcessCharingDaily;
use App\Models\Customer;
use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ProcessChargingDailyCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'charging:daily';

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
        // charge number with inactive status before start sending content

	    Customer::where('status', 0)
            ->chunkById(1000, function ($customers) {
                foreach ($customers as $customer) {
                  Log::info('Start charging to customer.'.$customer->msisdn);
                  ProcessCharingDaily::dispatch('921465_P02', $customer->msisdn, '15000')->onQueue('transaction');
                }
            });

        Customer::where('ivr_status', 0)
            ->chunkById(1000, function ($customers) {
                foreach ($customers as $customer) {
                  Log::info('charging airtime ivr customer.'.$customer->msisdn);
                  ProcessCharingDaily::dispatch('921465_P01', $customer->msisdn, '30000')->onQueue('transaction');
                }
            });


        Customer::where('doctor_subscription_status', 0)
            ->chunkById(1000, function ($customers) {
                foreach ($customers as $customer) {
                  Log::info('charging airtime doctor subscription customer.'.$customer->msisdn);
                  ProcessCharingDaily::dispatch('921465_P04', $customer->msisdn, '20000')->onQueue('transaction');
                }
            });
    }
}

