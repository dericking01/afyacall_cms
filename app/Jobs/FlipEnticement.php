<?php

namespace App\Jobs;

use App\Models\Product;
use App\Models\Customer;
use App\Models\Transaction;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class FlipEnticement implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

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
    public function __construct($output_ResponseCode, $output_ResponseDesc, $output_Receipt, $output_PaymentChannel, $output_ConversationID, $output_ChargedAmount)
    {
        $this->output_ResponseCode = $output_ResponseCode;
        $this->output_ResponseDesc = $output_ResponseDesc;
        $this->output_Receipt = $output_Receipt;
        $this->output_PaymentChannel = $output_PaymentChannel;
        $this->output_ConversationID = $output_ConversationID;
        $this->output_ChargedAmount = $output_ChargedAmount;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        
        // Retrieve the transaction based on the provided conversation ID
        $transaction = Transaction::where("conventions_ID", $this->output_ConversationID)->first();
        // \Log::warning("output_ResponseCode value: {$this->output_ResponseCode}");
        // \Log::warning("output_ResponseCode type: " . gettype($this->output_ResponseCode));
        // \Log::warning('****************FLIPING TRANSACTION LOGIC****************');
        if (!$transaction) {
            // Log or handle the missing transaction case
            \Log::warning("Transaction not found for Conversation ID: {$this->output_ConversationID}");

            return;
        }

        // Proceed only if the response code is "-7"
        if ($this->output_ResponseCode == '-7') {
            // Fetch the customer and product associated with the transaction
            $customer = Customer::find($transaction->customer_ID);
            $product = Product::find($transaction->product_id);

            if (!$customer || !$product) {
                // Log or handle the missing customer or product case
                \Log::warning("Customer or Product not found for Transaction ID: {$transaction->id}");

                return;
            }

            // Apply enticement flag based on the product ID
            switch ($product->product_ID) {
                case '921465_P01': // IVR
                    $customer->ivr_enticement = 0;

                    break;
                case '921465_P03': // Doctor
                    $customer->doctor_enticement = 0;

                    break;
                case '921465_P02': // SMS or default case
                default:
                    $customer->enticement = 0;

                    break;
            }

            // Save the updated customer enticement status
            $customer->save();

            // Optional: Log or add further processing after successful enticement update
            \Log::info("Enticement flag set for Customer ID: {$customer->id}, Product ID: {$product->product_ID}");
        } else {
            // Handle other response codes if needed
            // \Log::info("Response Code: {$this->output_ResponseCode} is not eligible for enticement processing.");
        }
    }
}
