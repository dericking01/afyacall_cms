<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Jobs\ProcessLanguage;
use Carbon\Carbon;

class SendRevenueReport extends Command
{
    protected $signature = 'sms:revenue-report';
    protected $description = 'Send revenue report SMS to recipients';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        // Get yesterday's date
        $yesterday = Carbon::yesterday()->toDateString();

        // Query the database using Laravel's query builder
        $row = DB::table('transactions')
            ->selectRaw("
                CAST(created_at AS DATE) AS DateCreated,
                SUM(CASE WHEN product_id = 1 THEN amount_IN END) AS ivr,
                SUM(CASE WHEN product_id = 2 THEN amount_IN END) AS sms,
                SUM(CASE WHEN product_id = 4 THEN amount_IN END) AS doctor_subs,
                SUM(CASE WHEN product_id IN (3, 5, 6) THEN amount_IN ELSE 0 END) AS calls,
                SUM(amount_IN) AS total
            ")
            ->where('status', 1)
            ->whereDate('created_at', $yesterday)
            ->groupBy(DB::raw('CAST(created_at AS DATE)'))
            ->first();

        // Check if we have results
        if ($row) {
            // Format the revenue amounts with commas
            $ivr = number_format(round($row->ivr, 2), 2, '.', ',');
            $sms = number_format(round($row->sms, 2), 2, '.', ',');
            $doctorSubs = number_format(round($row->doctor_subs, 2), 2, '.', ',');
            $calls = number_format(round($row->calls, 2), 2, '.', ',');
            $total = number_format(round($row->total, 2), 2, '.', ',');

            // List of recipients with their names
            $recipients = [
                ['msisdn' => '255743956595', 'name' => 'Derrick'],
                ['msisdn' => '255746805383', 'name' => 'Julius'],
                ['msisdn' => '255746088031', 'name' => 'Wingslaus'],
                // ['msisdn' => '255746193050', 'name' => 'Ireri'],
                ['msisdn' => '255754710722', 'name' => 'Mwamba'],
                ['msisdn' => '255754710702', 'name' => 'Sam'],
                ['msisdn' => '255756532635', 'name' => 'Siwangu']
                // ['msisdn' => '255745994671', 'name' => 'Rodrick']
            ];

            // Dispatch SMS for each recipient
            foreach ($recipients as $recipient) {
                $msisdn = $recipient['msisdn'];
                $name = $recipient['name'];
                $message = [
                    'sw' => "Hi Mr.$name, revenue ($yesterday): IVR => $ivr, SMS => $sms, Dr Subs => $doctorSubs, Calls => $calls, TOTAL => $total",
                    'en' => "Hi Mr.$name, revenue ($yesterday): IVR => $ivr, SMS => $sms, Dr Subs => $doctorSubs, Calls => $calls, TOTAL => $total",
                ];

                // Dispatch the job synchronously (or asynchronously based on your setup)
                ProcessLanguage::dispatchSync($msisdn, $message['sw'], $message['en']);
                $this->info("Message sent to $name successfully.");
            }
        } else {
            $this->info("No revenue data found for yesterday ($yesterday).");
        }
    }
}

?>
