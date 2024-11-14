<?php

namespace App\Console\Commands;

use App\Jobs\ProcessLanguage;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class SmsRevenue extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sms:revenue';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send daily revenue notifications';

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
        try {
            // Define the start and end of the current day for range filtering
            $startDate = now()->startOfDay()->toDateTimeString();
            $endDate = now()->endOfDay()->toDateTimeString();

            // Start query timing
            $queryStartTime = microtime(true);

            // Fetch today's total revenue using a range for better performance
            $revenue = DB::table('transactions')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->where('status', 1)
                ->sum('amount_IN');

            // End query timing
            $queryEndTime = microtime(true);
            $queryTimeTaken = $queryEndTime - $queryStartTime;
            $this->info("Query execution time: " . round($queryTimeTaken, 4) . " seconds");

            // Format the revenue amount
            $revenueAmount = number_format(round($revenue, 2), 2, '.', ',');

            // List of recipients with their names
            $recipients = [
                ['msisdn' => '255743956595', 'name' => 'Derrick'],
                ['msisdn' => '255746805383', 'name' => 'Julius'],
                ['msisdn' => '255746088031', 'name' => 'Wingslaus'],
                ['msisdn' => '255746193050', 'name' => 'Ireri'],
                ['msisdn' => '255754710722', 'name' => 'Mwamba'],
                ['msisdn' => '255754710702', 'name' => 'Sam'],
                ['msisdn' => '255756532635', 'name' => 'Siwangu'],
                ['msisdn' => '255745994671', 'name' => 'Rodrick']
            ];

            // Loop through each recipient and send the personalized message
            foreach ($recipients as $recipient) {
                $msisdn = $recipient['msisdn'];
                $name = $recipient['name'];
                $message = [
                    'sw' => "Hi Mr. $name, the current revenue is => $revenueAmount",
                    'en' => "Hi Mr. $name, the current revenue is => $revenueAmount",
                ];
                
                // Dispatch the job synchronously
                ProcessLanguage::dispatchSync($msisdn, $message['sw'], $message['en']);
                $this->info("Message sent to $name successfully.");
            }
            // Log::info('The revenue is ' . $revenueAmount);

        } catch (\Exception $e) {
            // Handle any errors
            $this->error("Error: " . $e->getMessage());
        }

        return 0;
    }
}
