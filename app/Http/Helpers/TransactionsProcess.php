<?php

namespace App\Http\Helpers;


use App\Models\Customer;
use App\Models\Opt;
use App\Models\Product;
use App\Models\Subscription;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class TransactionsProcess
{
    function __construct()
    {
    }

    public function transactions($output_ConversationID,$output_ResponseCode, $output_Receipt,$output_ResponseDesc, $output_ChargedAmount)
    {
        $transaction = Transaction::where("conventions_ID", $this->output_ConversationID)->get()->first();
        if ($transaction) {
            if ($this->output_ResponseCode == '0') {
                $transaction->status = 1;
                $customer = Customer::where('id', $transaction->customer_ID)->get()->first();
                $product = Product::where('id', $transaction->product_id)->get()->first();
                if ($product->name == 'IVR') {
                    if ($customer) {
                        $customer->ivr_status = 1;
                        $customer->updated_at = Carbon::now();
                        $customer->save();

                        $subscrb = Subscription::where('customer_ID', $customer->id)
                            ->where('product_id', $product->id)
                            ->get()->first();
                        if ($subscrb) {
                            $subscrb->customer_ID = $customer->id;
                            $subscrb->product_id = $product->id;
                            $subscrb->starts_at = Carbon::now();
                            $subscrb->ends_at = Carbon::now()->addDays(1);
                            $subscrb->save();
                        } else {
                            $subscribe = new Subscription();
                            $subscribe->customer_ID = $customer->id;
                            $subscribe->product_id = $product->id;
                            $subscribe->starts_at = Carbon::now();
                            $subscribe->ends_at = Carbon::now()->addDays(1);
                            $subscribe->save();
                        }
                    }
                } else {
                    if ($customer) {
                        //send notification if the customer is first time or was removed out once
                        if ($customer->status != 0) {
                            $paymentHelper = new ChargingHelper();
                            $paymentHelper->sendthefirstmessage($customer->msisdn, null);
                        }

                        //update the user info
                        $customer->status = 1;
                        $customer->updated_at = Carbon::now();
                        $customer->save();
                        //subscribe user in the system
                        $subscrb = Subscription::where('customer_ID', $customer->id)
                            ->where('product_id', $product->id)
                            ->get()->first();
                        if ($subscrb) {
                            $subscrb->customer_ID = $customer->id;
                            $subscrb->product_id = $product->id;
                            $subscrb->starts_at = Carbon::now();
                            $subscrb->ends_at = Carbon::now()->addDays(1);
                            $subscrb->save();
                        } else {
                            $subscribe = new Subscription();
                            $subscribe->customer_ID = $customer->id;
                            $subscribe->product_id = $product->id;
                            $subscribe->starts_at = Carbon::now();
                            $subscribe->ends_at = Carbon::now()->addDays(1);
                            $subscribe->save();
                        }
                    }
                }
            } else {
                $transaction->status = 0;
            }
            $transaction->response_code = $this->output_ResponseCode;
            $transaction->receipt = $this->output_Receipt;
            $transaction->response = $this->output_ResponseDesc;
            $transaction->amount_IN = $this->output_ChargedAmount;
            $transaction->updated_at = Carbon::now();
            $transaction->save();

        }
    }

}

