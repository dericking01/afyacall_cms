<?php

namespace App\Http\Helpers;


use App\Models\Customer;
use App\Models\Opt;
use App\Models\Product;
use App\Models\Subscription;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class ChargingMpesa
{
    function __construct()
    {
    }

    public function charging($product_ID,$cellNo, $amount)
    {
        try {
            return $this->chargeviampesa($product_ID,$cellNo, $amount);
        } catch (\Throwable $th) {
            //throw $th;
        }
    }


    private function chargeviampesa($product_ID,$cellNo, $amount)
    {

        $code = Opt::getCode();
        $customer = Customer::where('msisdn', $cellNo)->get()->first();
        $product = Product::where('product_ID', $product_ID,)->get()->first();

        try {
            $client = new \GuzzleHttp\Client();
            $response = $client->request('POST', 'https://197.250.9.191:23000/icg/Charge/', [
                'verify' => false,
                'headers' => [
                    'Content-Type' => ' application/json',
                ],
                'json' => [
                    'input_Username' => '921465',
                    'input_Password' => '5pmls4V!9]O]{IF',
                    'input_WASPShortcode' => '921465',
                    'input_ProductID' => $product_ID,
                    'input_CustomerMSISDN' => $cellNo,
                    'input_Currency' => 'TZS',
                    'input_Amount' => $amount,
                    'input_ChargeType' => 'Subscription',
                    'input_OriginatorConversationID' => $code,
                ]
            ]);
            $results = $response->getBody()->getContents();
	    $data = json_decode($results, true);
        Log::info($data);
            if ($data['output_ResponseCode'] == '-7' && $product->name == 'IVR' ) {
                $customer->ivr_enticement = 0;
                $customer->save();
            }

            if ($data['output_ResponseCode'] == '-7' && $product->name == 'SMS' ) {
                $customer->enticement = 0;
                $customer->save();
            }            

            //register transaction
            $trans = new Transaction();
            $trans->customer_ID = $customer->id;
            $trans->amount_IN = $amount;
            $trans->transaction_date = Opt::getServertime();
            $trans->status = 0;
            $trans->product_id = $product->id;
            $trans->currency = "Mpesa";
            $trans->response = $data['output_ResponseDesc'];
            $trans->conventions_ID = $data['output_ConversationID'];
            $trans->response_code = $data['output_ResponseCode'];
            $trans->save();

            return true;
        } catch (\Throwable $th) {
            Log::info($th->getMessage());
        }
    }
}

