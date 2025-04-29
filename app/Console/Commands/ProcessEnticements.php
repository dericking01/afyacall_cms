<?php


namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Models\Customer;

class ProcessEnticements extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'enticement:process';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process and push enticements to customers in chunks of 500 every 4 minutes';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $cases = [
            ['condition' => ['enticement' => 0, 'status' => 0], 'productID' => '921465_P02'],
            ['condition' => ['ivr_enticement' => 0, 'ivr_status' => 0], 'productID' => '921465_P01'],
            ['condition' => ['doctor_enticement' => 0, 'doctor_subscription_status' => 0], 'productID' => '921465_P03'],
        ];

        foreach ($cases as $case) {
            $this->processCase($case['condition'], $case['productID']);
        }

        $this->info("Enticements processed successfully.");
        return 0;
    }

    /**
     * Process a single case of enticement.
     *
     * @param array $conditions
     * @param string $productID
     */
    private function processCase(array $conditions, string $productID)
    {
        // Build the query
        $query = Customer::whereNull('deleted_at');
        foreach ($conditions as $field => $value) {
            $query->where($field, $value);
        }

        // Log the case details
        $count = $query->count();
        Log::info("Processing PPEnticement for ProductID: {$productID}", ['conditions' => $conditions, 'count' => $count]);

        // Process customers in chunks of 500
        $query->chunk(500, function ($customers) use ($productID) {
            foreach ($customers as $customer) {
                try {
                    $response = \App\Models\Enticement::promptEnticement($customer->msisdn, $productID);
                    Log::info("PPEnticement sent for customer {$customer->id}", ['response' => $response]);

                } catch (\Throwable $e) {
                    Log::error("Failed to send PPEnticement for customer {$customer->id}: {$e->getMessage()}");
                }
            }

            // Pause for 4 minutes after processing 500 customers
            Log::info("Processed 500 customers, pausing for 4 minutes.");
            sleep(240); // 240 seconds = 4 minutes
        });
    }
}
