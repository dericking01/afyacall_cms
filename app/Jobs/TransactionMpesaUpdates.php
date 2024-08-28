<?php

namespace App\Jobs;

use App\Http\Helpers\ChargingHelper;
use App\Models\Customer;
use App\Models\Opt;
use App\Models\Product;
use App\Models\Subscription;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class TransactionMpesaUpdates implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $output_ConversationID = null;
    private $output_ResponseCode = null;
    private $output_Receipt = null;
    private $output_ResponseDesc = null;
    private $output_ChargedAmount = null;
    private $output_PaymentChannel = null;
    
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($output_ConversationID, $output_ResponseCode, $output_Receipt, $output_ResponseDesc,$output_ChargedAmount,$output_PaymentChannel)
    {
        $this->output_ConversationID = $output_ConversationID;
        $this->output_ResponseCode = $output_ResponseCode;
        $this->output_Receipt = $output_Receipt;
        $this->output_ResponseDesc = $output_ResponseDesc;
        $this->output_ChargedAmount = $output_ChargedAmount;
        $this->output_PaymentChannel = $output_PaymentChannel;
    }


    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $transaction = Transaction::where("conventions_ID", $this->output_ConversationID)->first();
        if ($transaction) {
            if ($this->output_ResponseCode == '0') {
                $transaction->status = 1;
                $customer = Customer::find($transaction->customer_ID);
                $product = Product::find($transaction->product_id);
        
                if ($customer && $product) {
                    switch ($product->product_ID) {
                        case '921465_P01': // IVR
                            $customer->ivr_status = 1;
                            break;
                        case '921465_P03': // Doctor
                            $customer->doctor_subscription_status = 1;
                            break;
                        case '921465_P02': // Default which is sms
                        default:
                            $customer->status = 1;
                            break;
                    }
        
                    $customer->save();
        
                    $subscription = Subscription::firstOrNew(
                        [
                            'customer_ID' => $customer->id,
                            'product_id' => $product->id,
                        ]
                    );
                    $subscription->starts_at = Carbon::now();
                    $subscription->ends_at = Carbon::now()->addDays(1);
                    $subscription->save();
                }
            } else {
                $transaction->status = 0;
            }
        
            $transaction->response_code = $this->output_ResponseCode;
            $transaction->receipt = $this->output_Receipt;
            $transaction->response = $this->output_ResponseDesc;
            $transaction->currency =  $this->output_PaymentChannel;
            $transaction->amount_IN = $this->output_ChargedAmount;
            $transaction->updated_at = Carbon::now();
            $transaction->save();
        }
        
    }
}


