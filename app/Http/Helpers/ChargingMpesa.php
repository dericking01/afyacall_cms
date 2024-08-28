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
            Log::info($th->getMessage());
        }
    }

    public function checkbalance($productID, $msisdn){

        $code = Opt::getCode();
        try {
            $client = new \GuzzleHttp\Client();
            $response = $client->request('POST', 'https://197.250.9.191:23000/icg/query/balance/', [
                'verify' => false,
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'input_Username' => '921465',
                    'input_Password' => '5pmls4V!9]O]{IF',
                    'input_WASPShortcode' => '921465',
                    'input_ChannelType' => 'API',
                    'input_ProductID' => $productID,
                    'input_CustomerMSISDN' => $msisdn,
                    'input_OriginatorConversationID' => $code,
                ]
            ]);
            $results = $response->getBody()->getContents();
            $data = json_decode($results, true);

            if ($data['output_ResponseCode'] == '0'){
                return $data['output_AirtimeBalance'];
            } else {
                //Log::info($data);
            }
        } catch (\Throwable $th) {
            Log::info($th->getMessage());
        }

    }

    private function getBalance($msisdn)
    {

        $payload = [
            'serviceIdentifier'   =>
            array(
                'value' => $msisdn,
                'schemeName' => 'msisdn'
            ),
            'id' => [
                array(
                    'value' => "airtime:*199*100#",
                    'schemeName' => "balanceType"
                )
            ]
        ];
        // Log::info('payload ' . $payload);
        //time for charging
        $chargetime = Opt::getTimestamp();
        $uuid = Opt::generateUUIDv1();

        try {
            $client = new \GuzzleHttp\Client;
            $credentials = base64_encode('svc_afyacall:wHroRA3U03_el701');
            $response = $client->post('https://197.250.9.149:6202/middlewarev2/serviceBalance', [
                'verify' => false,
                'headers' => [
                    'Authorization' => 'Basic ' . $credentials,
                    'Content-Type' => 'application/json',
                    'X-MessageId' => 'uuid: '.$uuid,
                    'X-Source-Timestamp'  => $chargetime,
                ],
                'json' => $payload
            ]);
            $balances = $response->getBody()->getContents();
            

            $data = json_decode($balances, true);


            return $data[0]['details']['balanceAmount'][0]['amount'];
        } catch (\Throwable $th) {
            return 0;
        }
    }

    private function chargeviampesa($product_ID, $cellNo, $amount)
    {

    $balance = intval(abs($this->checkbalance($product_ID, $cellNo)));

    // Define balance ranges for each product
    $balanceRanges = [
        '921465_P02' => ['min' => 50, 'max' => 150],
        '921465_P01' => ['min' => 50, 'max' => 300],
        '921465_P03' => ['min' => 50, 'max' => 200],
    ];
    
    // Check if product_ID exists in the balance ranges and update the amount
    if (isset($balanceRanges[$product_ID])) {
        $range = $balanceRanges[$product_ID];
        if ($balance >= $range['min'] && $balance <= $range['max']) {
            $amount = $balance;
        } elseif ($balance > $range['max']) {
            $amount = $range['max']; // Assign max if balance is above max
        } else {
            return true; // Balance out of range
        }
    } else {
        return true; // product_ID not in balance ranges
    }
    // Proceed with the rest of the charging process
    $code = Opt::getCode();
    $customer = Customer::where('msisdn', $cellNo)->first();
    $product = Product::where('product_ID', $product_ID)->first();

    Log::info("amount to be charged ".$amount . " msisdn  ".$cellNo ."  Reference Code " .$code);

    try {
        $client = new \GuzzleHttp\Client();
        $response = $client->request('POST', 'https://197.250.9.191:23000/icg/Charge/', [
            'verify' => false,
            'headers' => [
                'Content-Type' => 'application/json',
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

        // Register transaction
        $trans = new Transaction();
        $trans->customer_ID = $customer->id;
        $trans->amount_IN = $amount;
        $trans->transaction_date = Opt::getServertime();
        $trans->status = 0;
        $trans->product_id = $product->id;
        $trans->currency = "Airtime";
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

