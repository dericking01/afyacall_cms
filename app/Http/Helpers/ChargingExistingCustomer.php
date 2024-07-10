<?php


namespace App\Http\Helpers;

use App\Jobs\ProcessLanguage;
use App\Models\Transaction;
use App\Models\Customer;
use App\Models\Opt;
use App\Models\Product;
use App\Models\Subscription;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Log;

class ChargingExistingCustomer
{
    private $product_ID = null;

    function __construct($product_ID)
    {
        $this->product_ID = $product_ID;
    }

    public function charging($cellNo, $amount)
    {
        try {
            if (!$this->product_ID) {
                throw new Exception('Product is Not defined');
            }
            if ($this->product_ID == '921465_P01') {
                return $this->chargeviaairtimeivr($this->product_ID, $cellNo, $amount);
            } elseif ($this->product_ID == '921465_P02') {
                return $this->chargeviaairtime($this->product_ID, $cellNo, $amount);
            } elseif ($this->product_ID == '921465_P03') {
                return $this->chargeviaairtime_doctorsubscription($this->product_ID, $cellNo, $amount);
            } else {
                Log::info('No Product is selected');
                return true;
            }
        } catch (Exception $e) {
            Log::error('failed to pass the product' . $e->getMessage());
        }
        return true;
    }

    public function checkbalance($productID, $msisdn){
        
        $code = Opt::getCode();
        try {
            $client = new \GuzzleHttp\Client();
            $response = $client->request('POST', 'https://197.250.9.191:23000/icg/query/balance/', [
                'verify' => false,
                'headers' => [
                    'Content-Type' => ' application/json',
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
            Log::info($data);
            if ($data['output_ResponseCode'] == '0'){
                return $data['output_AirtimeBalance'];
            } else {
                Log::info($data);
                return 0;
            }
        } catch (\Throwable $th) {
            Log::info($th->getMessage());
        }

    }

    //charge via airtime
    private function chargeviaairtime($product_ID, $cellNo, $amount)
    {
        //check the customer balance first
	    $balance = intval(abs($this->getBalance($cellNo)));
        if ($balance >= 15000) {
            $amount = 15000;
        } elseif ($balance > 3000 && $balance < 15000) {
            $amount = $balance;
        } else {
            return true;
        }
       
        //update the payload 
        $payload = [
            'type' => 'charge',
            'id'   => [
                array(
                    'value' => $cellNo,
                    'schemeName' => 'msisdn'
                )
            ],
            'details' => [
                'adjustmentAmount' =>  strval($amount)
            ],
            'name' => 'MW',
            'desc' => 'Afya Call',
            'category' => [
                array(
                    'value' => 'MW',
                    'listHierarchyId' => 'eventClass'
                )
            ]
        ];

        //time for charging, customer id, and product id from database
        $chargetime = Opt::getTimestamp();
        $customer = Customer::where('msisdn', $cellNo)->get()->first();
        $product = Product::where('product_ID', $product_ID)->get()->first();

        //try charging the customer
        try {
            $client = new \GuzzleHttp\Client;
            $credentials = base64_encode('svc_afyacall:wHroRA3U03_el701');
            $response = $client->post('https://197.250.9.149:6202/middlewarev2/serviceAccountAdjustment', [
                'verify' => false,
                'headers' => [
                    'Authorization' => 'Basic ' . $credentials,
                    'Content-Type' => ' application/json',
                    'X-MessageId' => 'uuid: a5c49974-353e-11e5-a151-feff819cdc9f',
                    'X-Source-Timestamp'  => $chargetime,
                ],
                'json' => $payload
            ]);
            $results = $response->getBody()->getContents();
            //convert into json
            $data = json_decode($results, true);

            //check if customer found in database
            if ($customer) {
                //update customer status
                $customer->status = 1;
                $customer->save();

                //register successfully transaction
                $trans = new Transaction();
                $trans->customer_ID = $customer->id;
                $trans->amount_IN = $amount / 100;
                $trans->product_id = $product->id;
                $trans->transaction_date = Opt::getServertime();
                $trans->status = 1;
                $trans->currency = "Airtime";
                $trans->response = 'Process service request successfully.';
                $trans->save();

                //register the subscription
                $subscribeid = Subscription::where('customer_ID', $customer->id)->where('product_id', $product->id)->first();

                if ($subscribeid) {
                    $subscribeid->starts_at = Carbon::now();
                    $subscribeid->ends_at = Carbon::now()->addDays(1);
                    $subscribeid->status = 1;
                    $subscribeid->save();
                } else {
                     //register the subscription
                    $subscribe = new Subscription();
                    $subscribe->customer_ID = $customer->id;
                    $subscribe->product_id = $product->id;
                    $subscribe->starts_at = Carbon::now();
                    $subscribe->ends_at = Carbon::now()->addDays(1);
                    $subscribe->status = 1;
                    $subscribe->save();
                }

                return true;
            }
        } catch (\Throwable $th) {
		Log::error('failed in charging airtime sms' . $th->getMessage());
		Log::error($th);
            //register failed transaction
            $trans = new Transaction();
            $trans->customer_ID = $customer->id;
            $trans->amount_IN = $amount / 100;
            $trans->product_id = $product->id;
            $trans->transaction_date = Opt::getServertime();
            $trans->status = 0;
            $trans->currency = "Airtime";
            $trans->response = 'Insufficient Balance';
            $trans->save();

            return true;
        }
    }

    //charge via airtime ivr
    private function chargeviaairtimeivr($product_ID, $cellNo, $amount)
    {
	    //check the customer balance first
	    $balance = intval(abs($this->getBalance($cellNo)));
        if ($balance >= 30000) {
            $amount = 30000;
        } elseif ($balance > 5000 && $balance < 30000) {
            $amount = $balance;
        } else {
	    	return true;
        }
        //update the payload 
        $payload = [
            'type' => 'charge',
            'id'   => [
                array(
                    'value' => $cellNo,
                    'schemeName' => 'msisdn'
                )
            ],
            'details' => [
                'adjustmentAmount' => strval($amount)
            ],
            'name' => 'MW',
            'desc' => 'Afya Call',
            'category' => [
                array(
                    'value' => 'MW',
                    'listHierarchyId' => 'eventClass'
                )
            ]
        ];

        //time for charging, customer id, and product id from database
        $chargetime = Opt::getTimestamp();
        $customer = Customer::where('msisdn', $cellNo)->get()->first();
        $product = Product::where('product_ID', $product_ID)->get()->first();

        //try charging the customer
        try {
            $client = new \GuzzleHttp\Client;
            $credentials = base64_encode('svc_afyacall:wHroRA3U03_el701');
            $response = $client->post('https://197.250.9.149:6202/middlewarev2/serviceAccountAdjustment', [
                'verify' => false,
                'headers' => [
                    'Authorization' => 'Basic ' . $credentials,
                    'Content-Type' => ' application/json',
                    'X-MessageId' => 'uuid: a5c49974-353e-11e5-a151-feff819cdc9f',
                    'X-Source-Timestamp'  => $chargetime,
                ],
                'json' => $payload
            ]);
            $results = $response->getBody()->getContents();
            //convert into json
            $data = json_decode($results, true);

            

            //check if customer found in database
            if ($customer) {
                //update customer status
                $customer->ivr_status = 1;
                $customer->save();

                //register successfully transaction
                $trans = new Transaction();
                $trans->customer_ID = $customer->id;
                $trans->amount_IN = $amount / 100;
                $trans->product_id = $product->id;
                $trans->transaction_date = Opt::getServertime();
                $trans->status = 1;
                $trans->currency = "Airtime";
                $trans->response = 'Process service request successfully.';
                $trans->save();

                //register the subscription
                $subscribeid = Subscription::where('customer_ID', $customer->id)->where('product_id', $product->id)->first();
                if ($subscribeid) {
                    $subscribeid->starts_at = Carbon::now();
                    $subscribeid->ends_at = Carbon::now()->addDays(1);
                    $subscribeid->status = 1;
                    $subscribeid->save();
                } else {
                     //register the subscription
                    $subscribe = new Subscription();
                    $subscribe->customer_ID = $customer->id;
                    $subscribe->product_id = $product->id;
                    $subscribe->starts_at = Carbon::now();
                    $subscribe->ends_at = Carbon::now()->addDays(1);
                    $subscribe->status = 1;
                    $subscribe->save();
                }

                return true;
            }
        } catch (\Throwable $th) {
            //register failed transaction
            $trans = new Transaction();
            $trans->customer_ID = $customer->id;
            $trans->amount_IN = $amount / 100;
            $trans->product_id = $product->id;
            $trans->transaction_date = Opt::getServertime();
            $trans->status = 0;
            $trans->currency = "Airtime";
            $trans->response = 'Insufficient Balance';
            $trans->save();
        }
    }


        //function to check the balance
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

        try {
            $client = new \GuzzleHttp\Client;
            $credentials = base64_encode('svc_afyacall:wHroRA3U03_el701');
            $response = $client->post('https://197.250.9.149:6202/middlewarev2/serviceBalance', [
                'verify' => false,
                'headers' => [
                    'Authorization' => 'Basic ' . $credentials,
                    'Content-Type' => ' application/json',
                    'X-MessageId' => 'uuid: a5c49974-353e-11e5-a151-feff819cdc9f',
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

    private function chargeviaairtime_doctorsubscription($product_ID, $cellNo, $amount)
    {

        //check balance
	    $balance = intval(abs($this->getBalance($cellNo)));
        if ($balance >= 20000) {
            $amount = 20000;
            $seconds = 60;
        } elseif ($balance > 15000 && $balance < 20000) {
            $amount = 15000;
            $seconds = 45;
        } elseif ($balance > 10000 && $balance < 15000) {
            $amount = 10000;
            $seconds = 30;
        } elseif ($balance > 5000 && $balance < 10000) {
            $amount = 5000;
            $seconds = 15;
        } else {
		   return true;
        }

        try {           
            // Prepare the payload for the charging request
            $payload = [
                'type' => 'charge',
                'id'   => [
                    [
                        'value' => $cellNo,
                        'schemeName' => 'msisdn'
                    ]
                ],
                'details' => [
                    'adjustmentAmount' => strval($amount)
                ],
                'name' => 'MW',
                'desc' => 'Afya Call',
                'category' => [
                    [
                        'value' => 'MW',
                        'listHierarchyId' => 'eventClass'
                    ]
                ]
            ];

            // Get the current time for charging
            $chargetime = Opt::getTimestamp();
            
            // Get the customer and product information from the database
            $customer = Customer::where('msisdn', $cellNo)->first();
            $product = Product::where('product_ID', $product_ID)->get()->first();

            if ($customer && $product) {
                // Try charging the customer
                $client = new \GuzzleHttp\Client;
                $credentials = base64_encode('svc_afyacall:wHroRA3U03_el701');
                
                // Send a POST request to the charging endpoint
                $response = $client->post('https://197.250.9.149:6202/middlewarev2/serviceAccountAdjustment', [
                    'verify' => false,
                    'headers' => [
                        'Authorization' => 'Basic ' . $credentials,
                        'Content-Type' => 'application/json',
                        'X-MessageId' => 'uuid: a5c49974-353e-11e5-a151-feff819cdc9f',
                        'X-Source-Timestamp'  => $chargetime,
                    ],
                    'json' => $payload
                ]);
                
                // Get the response content
                $data = json_decode($response->getBody()->getContents(), true);

                // Update customer status
                $customer->doctor_subscription_status = 1;
                $customer->other_status += $seconds;
                $customer->save();


                //register successfully transaction
                $trans = new Transaction();
                $trans->customer_ID = $customer->id;
                $trans->amount_IN = $amount / 100;
                $trans->product_id = $product->id;
                $trans->transaction_date = Opt::getServertime();
                $trans->status = 1;
                $trans->currency = "Airtime";
                $trans->response = 'Process service request successfully.';
                $trans->save();


                $subscribeid = Subscription::where('customer_ID', $customer->id)->where('product_id', $product->id)->first();
                if ($subscribeid) {
                    $subscribeid->starts_at = Carbon::now();
                    $subscribeid->ends_at = Carbon::now()->addDays(1);
                    $subscribeid->status = 1;
                    $subscribeid->save();
                } else {
                     //register the subscription
                    $subscribe = new Subscription();
                    $subscribe->customer_ID = $customer->id;
                    $subscribe->product_id = $product->id;
                    $subscribe->starts_at = Carbon::now();
                    $subscribe->ends_at = Carbon::now()->addDays(1);
                    $subscribe->status = 1;
                    $subscribe->save();
                }

                return true;
            } else {
                // Log the error if customer or product is not found
                Log::error('Customer or product not found in the database.');
            }
        } catch (\Throwable $th) {
            // Log the error if charging fails
            Log::error('Failed to charge airtime: ' . $th->getMessage());
        }

        return false;
    }


}

