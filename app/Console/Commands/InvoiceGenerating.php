<?php

namespace App\Console\Commands;

use App\Models\Invoice;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Console\Command;

class InvoiceGenerating extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invoice:generator';

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
        $sum = 0;
        $currentmonth  = Carbon::now()->month;
        $transactions = Transaction::where('status', 1)
            ->whereMonth('transaction_date', $currentmonth)->get();
        foreach ($transactions as $transaction) {
            $sum += $transaction->amount_IN;
        }
        $existinvoice =  Invoice::where('invoice_number', $currentmonth)->get()->first();
        if ($existinvoice) {
            $existinvoice->total_amount = $sum;
            $existinvoice->save();
        } else {
            $newinvoice = new Invoice();
            $newinvoice->invoice_number = $currentmonth;
            $newinvoice->invoice_reference = 'INV-' . Invoice::getReferenceNumber();
            $newinvoice->total_amount = $sum;
            $newinvoice->period_from = Carbon::now()->firstOfMonth(); 
            $newinvoice->period_to = Carbon::now()->lastOfMonth();
            $newinvoice->status = 0;
            $newinvoice->save();
        }
    }
}
