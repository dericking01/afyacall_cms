<?php

namespace App\Console\Commands;

use App\Jobs\ProcessMpesaDaily;
use App\Models\Customer;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ProcessMpesaDailyChargeCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mpesa:daily';

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
        Customer::where('status', 0)->where('enticement', 1)
            ->chunkById(1000000, function ($customers) {
                foreach ($customers as $customer) {
                    Log::info('Mpesa charging for SMS .' . $customer->msisdn);
		      ProcessMpesaDaily::dispatch('921465_P02', $customer->msisdn, '150')->onQueue('transaction');
                }
            });

        // charge number with inactive status before start sending content
        Customer::where('ivr_status', 0)->where('ivr_enticement', 1)
            ->chunkById(1000000, function ($customers) {
                foreach ($customers as $customer) {
                    Log::info('Mpesa charging for IVR.' . $customer->msisdn);
		     ProcessMpesaDaily::dispatch('921465_P01', $customer->msisdn, '300')->onQueue('transaction');
                }
            });
    }
}

