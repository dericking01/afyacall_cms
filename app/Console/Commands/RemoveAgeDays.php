<?php

namespace App\Console\Commands;

use App\Models\Customer;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Opt;

class RemoveAgeDays extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'remove:agedays';

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

        // Find customers with transactions older than 180 days
        $customersToRemove = Customer::leftJoin(DB::raw('(SELECT customer_ID, MAX(created_at) AS latest_transaction_date
                                FROM transactions
                                WHERE status = 1
                                GROUP BY customer_ID) t'), 'customers.id', '=', 't.customer_ID')
            ->whereNull('customers.deleted_at')
            ->whereRaw("DATEDIFF(CURRENT_DATE(), t.latest_transaction_date) > 180")
            ->get();

        foreach ($customersToRemove as $customer) {

            Log::info($customer->msisdn ." removed after days passed without charged");
            $opt = new Opt();
            $opt->customer_ID = $customer->id;
            $opt->opt_value = -1;
            $opt->ConversationID = "Removed after days passed without charged";
            $opt->date = Opt::getServertime();
            $opt->save();

            $customer->update(['status' => -5,
                               'ivr_status'=>-5,
                               'doctor_subscription_status'=>-5]);
        }
        return 0;
    }
}
