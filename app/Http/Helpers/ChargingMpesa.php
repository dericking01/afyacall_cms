<?php

namespace App\Http\Helpers;

use App\Models\Opt;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Transaction;
use Illuminate\Support\Facades\Log;


class ChargingMpesa
{
    public function __construct()
    {
    }

    public function charging($product_ID, $cellNo, $amount)
    {
        try {
            return $this->chargeviampesa($product_ID, $cellNo, $amount);
        } catch (\Throwable $th) {
            Log::info($th->getMessage());
        }
    }

    public function checkbalance($productID, $msisdn)
    {
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
                //Log::info('response is =>',$data);
            }
        } catch (\Throwable $th) {
            Log::info($th->getMessage());
        }
    }

    private function getBalance($msisdn)
    {
        $payload = [
            'serviceIdentifier' => [
                'value' => $msisdn,
                'schemeName' => 'msisdn'
            ],
            'id' => [
                [
                    'value' => 'airtime:*199*100#',
                    'schemeName' => 'balanceType'
                ]
            ]
        ];
        // Log::info('payload ' . $payload);
        //time for charging
        $chargetime = Opt::getTimestamp();
        $uuid = Opt::generateUUIDv1();

        try {
            $client = new \GuzzleHttp\Client;
            $credentials = base64_encode('svc_afyacall:j8J7EPxXTnrW_#MQ');
            $response = $client->post('https://197.250.9.149:6202/middlewarev2/serviceBalance', [
                'verify' => false,
                'headers' => [
                    'Authorization' => 'Basic ' . $credentials,
                    'Content-Type' => 'application/json',
                    'X-MessageId' => 'uuid: ' . $uuid,
                    'X-Source-Timestamp' => $chargetime,
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

    private function chargeviampesaNew($product_ID, $cellNo)
    {
        // Define fallback ranges for each product
        $fallbackRanges = [
            '921465_P02' => range(150, 50, -10), // SMS Service Product
            '921465_P01' => range(300, 50, -13), // IVR Service Product
            '921465_P03' => range(200, 50, -10), // Doctor Call Service Product
        ];

        // Skip balance query and directly use fallback ranges
        if (!isset($fallbackRanges[$product_ID])) {
            Log::info("Invalid product ID: {$product_ID}");
            return false;
        }

        // Retrieve the customer and product details before entering the loop
        $customer = Customer::where('msisdn', $cellNo)->first();
        if (!$customer) {
            Log::info("Customer with MSISDN {$cellNo} not found");
            return false;
        }

        $product = Product::where('product_ID', $product_ID)->first();
        if (!$product) {
            Log::info("Product with ID {$product_ID} not found");
            return false;
        }

        $fallbackAmounts = $fallbackRanges[$product_ID];

        foreach ($fallbackAmounts as $amount) {
            // Get today's date in 'Y-m-d' format
            $today = Carbon::today()->toDateString();

            // Check if the customer has already been charged for this product today
            $existingTransaction = Transaction::where('customer_ID', $customer->id)
                ->where('product_id', $product->id)
                ->whereDate('transaction_date', $today) // Check if the transaction is from today
                ->where('status', 0) // Assuming 0 means pending or unprocessed
                ->first();

            if ($existingTransaction) {
                Log::info("MSISDN {$cellNo} has already been charged for product {$product_ID} today, skipping...");
                continue; // Skip this iteration and move to the next amount
            }

            // Proceed with the charging process for each amount
            $code = Opt::getCode();

            Log::info("Attempting to charge {$amount} for msisdn {$cellNo} under product {$product_ID}");

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
                $trans->transaction_date = Opt::getServertime(); // Use actual server time for transaction date
                $trans->status = 0;
                $trans->product_id = $product->id;
                $trans->currency = "Airtime";
                $trans->response = $data['output_ResponseDesc'];
                $trans->conventions_ID = $data['output_ConversationID'];
                $trans->response_code = $data['output_ResponseCode'];
                $trans->save();

                Log::info("Transaction saved for {$amount} on msisdn {$cellNo}");

                // Stop further processing after a successful charge
                break;

            } catch (\Throwable $th) {
                Log::info("Charge failed for {$amount} on msisdn {$cellNo}: " . $th->getMessage());
            }
        }

        return true;
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

        Log::info('amount to be charged ' . $amount . ' msisdn  ' . $cellNo . '  Reference Code ' . $code);

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
            // Log::info('SYNC RESPONSE: ' . json_encode($data));
            // Register transaction Log::info('SYNC RESPONSE' .$data);
            $trans = new Transaction();
            $trans->customer_ID = $customer->id;
            $trans->amount_IN = $amount;
            $trans->transaction_date = Opt::getServertime();
            $trans->status = 0;
            $trans->product_id = $product->id;
            $trans->currency = 'Airtime';
            $trans->response = $data['output_ResponseDesc'];
            $trans->conventions_ID = $data['output_ConversationID'];
            $trans->response_code = $data['output_ResponseCode'];
            $trans->save();

            return true;
        } catch (\Throwable $th) {
            Log::info($th->getMessage());
        }
    }

    public function checkbotbalance($productID, $msisdn, $code){
      
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

            Log::info("BOT BALANCE:\n". json_encode($data, JSON_PRETTY_PRINT));

            return $data;
        } catch (\Throwable $th) {
            Log::info($th->getMessage());
        }

    }

    public function ChargeService($cellNo, $OriginatorConversation_id, $amount, $product_ID)
    {
        $code = Opt::getCode();
        $customer = Customer::where('msisdn', $cellNo)->first();
        if (!$customer) {
            return response()->json([
                "output_ResponseCode" => "-3",
                "output_ResponseDesc" => "Customer Not Found",
                "output_ProductID" => $product_ID,
                "output_OriginatorConversationID" => $OriginatorConversation_id,
                "output_ConversationID" => $code,
            ]);
        }

        Log::info("Sending charge request to Vodacom for $cellNo | Amount: $amount");

        $payload = [
            'input_Username' => '921465',
            'input_Password' => '5pmls4V!9]O]{IF',
            'input_WASPShortcode' => '921465',
            'input_ProductID' => $product_ID,
            'input_CustomerMSISDN' => $cellNo,
            'input_Currency' => 'TZS',
            'input_Amount' => $amount,
            'input_ChargeType' => 'Subscription',
            'input_OriginatorConversationID' => 'a255c' . $OriginatorConversation_id,
        ];

        // Log the payload before sending
        Log::info("BOT Sending Charge Request Payload:\n". json_encode($payload, JSON_PRETTY_PRINT));


        try {
            $client = new \GuzzleHttp\Client();
            $response = $client->request('POST', 'https://197.250.9.191:23000/icg/Charge/', [
                'verify' => false,
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'json' => $payload
            ]);
            $results = $response->getBody()->getContents();
            $data = json_decode($results, true);
            // Log::info('SYNC RESPONSE: ' . json_encode($data));
            // Check if JSON decoding failed
            if (!is_array($data)) {
                Log::error("Invalid JSON response from Vodacom API: " . $results);
                return response()->json([
                    "output_ResponseCode" => "-1",
                    "output_ResponseDesc" => "Invalid response from Vodacom",
                ], 500);
            }
            // Return sync response to Docker server
            if ($data['output_ResponseCode'] === "0") {
                // Extract OriginatorConversationID
                $originatorID = $data['output_OriginatorConversationID'] ?? '';
            
                // Check if it starts with 'a255c' and clean it
                if (str_starts_with($originatorID, 'a255c')) {
                    $originatorID = substr($originatorID, strlen('a255c'));
                }
            
                $forwardData = $data;

                // Override only the OriginatorConversationID (if cleaned)
                $forwardData['output_OriginatorConversationID'] = $originatorID;

                Log::info("BOTY SYNC CHARGE RES 2B SENT:\n". json_encode($forwardData, JSON_PRETTY_PRINT));

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

                return response()->json($forwardData);

            }
            
            // Register transaction Log::info('SYNC RESPONSE' .$data);
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

            return response()->json([
                "output_ResponseCode" => "-4",
                "output_ResponseDesc" => "System Error",
                "output_OriginatorConversationID" => $OriginatorConversation_id,
                "output_ConversationID" => $code,
            ], 500);
        }
    }

    public function botOnDemand($msisdn, $product_ID, $amount, $OriginatorConversation_id)
    {
        Log::info("BOT ON DEMAND CHARGING MSISDN: {$msisdn}");
        Log::info("BOT Product ID for this charge: {$product_ID}");

        $code = Opt::getCode();
        $customer = Customer::where('msisdn', $msisdn)->get()->first();
        $product = Product::where('product_ID', $product_ID)->get()->first();

        try {
            $client = new \GuzzleHttp\Client();
            $response = $client->request('POST', 'https://197.250.9.191:23000/icg/charge/without/sub', [
                'verify' => false,
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'input_Username' => '921465',
                    'input_Password' => '5pmls4V!9]O]{IF',
                    'input_WASPShortcode' => '921465',
                    'input_ProductID' => $product_ID,
                    'input_CustomerMSISDN' => $msisdn,
                    'input_Currency' => 'TZS',
                    'input_Amount' => $amount,
                    'input_ChargeType' => 'Subscription',
                    'input_ChargeChannel' => 'Synchronous',
                    'input_OriginatorConversationID' => $OriginatorConversation_id,
                ],
            ]);

            $results = $response->getBody()->getContents();
            $data = json_decode($results, true);
            // Log::info('LONG CODE SYNC RESPONSE',$data);
            Log::info("BOT LONG CODE SYNC RESPONSE:\n" . json_encode($data, JSON_PRETTY_PRINT));


            if ($data['output_ResponseDesc'] == 'Processed Successfully') {
                //check if customer found in database
                if ($customer) {
                    $customer->doctor_status = 1;
                    $customer->save();

                    //register successfully transaction
                    $trans = new Transaction();
                    $trans->customer_ID = $customer->id;
                    $trans->amount_IN = $amount;
                    $trans->transaction_date = Opt::getServertime();
                    $trans->status = 1;
                    $trans->product_id = $product->id;
                    $trans->currency = "Airtime";
                    $trans->response = $data['output_ResponseDesc'];
                    $trans->conventions_ID = $data['output_ConversationID'];
                    $trans->response_code = $data['output_ResponseCode'];
                    $trans->save();

                }
            }

            return $data;

        } catch (\Throwable $th) {
            Log::info("BOT ERROR: " . $th->getMessage());
            return [
                'error' => true,
                'message' => $th->getMessage(),
            ];
        }
    }

    public function loginRequest($loginpayload)
    {

        try {
            $client = new \GuzzleHttp\Client;
            $response = $client->post('https://41.217.203.61:30010/iPG/b2c/ussd_push?wsdl', [
                'verify' => false,
                'headers' => [
                    'Content-Type' => 'text/xml',
                    'accept" => "*/*',
                ],
                'body' => $loginpayload
            ]);

            return $response;

        } catch (\Throwable $th) {

            return $th->getMessage();
        }

    }

    public function charge($product_ID, $cellNo, $amount)
    {
        return $this->chargeviampesa($product_ID, $cellNo, $amount);
    }

}
