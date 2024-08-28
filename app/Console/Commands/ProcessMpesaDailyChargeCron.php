<?php

namespace App\Console\Commands;
use App\Jobs\ProcessCharingDaily;
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
        $this->chargeCustomersForService('DOCTOR SUBSCRIPTION', '921465_P03', '200', 'doctor_subscription_status', 0, 'doctor_enticement', 1);
        $this->chargeCustomersForService('SMS', '921465_P02', '150', 'status', 0, 'enticement', 1);
        $this->chargeCustomersForService('IVR', '921465_P01', '300', 'ivr_status', 0, 'ivr_enticement', 1);
       
    }
    
    private function chargeCustomersForService($service, $mpesaCode, $amount, $statusColumn, $statusValue, $enticementColumn, $enticementValue)
    {
        Customer::where($statusColumn, $statusValue)
            ->chunkById(1000, function ($customers) use ($service, $mpesaCode, $amount) {
                $data = [];
    
                foreach ($customers as $customer) {
                    $data[] = [
                        'service' => $service,
                        'mpesa_code' => $mpesaCode,
                        'amount' => $amount,
                        'msisdn' => $customer->msisdn
                    ];
                }
    
                $this->chargeCustomersWithMpesa($data);
            });
    }
    
    private function chargeCustomersWithMpesa($data)
    {
        foreach ($data as $customerData) {
            $mpesaCode = $customerData['mpesa_code'];
            $msisdn = $customerData['msisdn'];
            $amount = $customerData['amount'];
    
            ProcessMpesaDaily::dispatch($mpesaCode, $msisdn, $amount)->onQueue('transaction');

        }
    }
    

}

