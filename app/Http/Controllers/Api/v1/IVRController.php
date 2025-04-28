<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessLanguage;
use App\Models\Blacklist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Customer;
use App\Models\Opt;
use App\Models\Product;
use App\Models\Subscription;
use App\Models\Transaction;
use App\Models\Promotion;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;


class IVRController extends Controller
{
    public function chargeMpesaAirtimeIvr(Request $request)
    {
        Log::info("Received request", $request->all());

        $validator = Validator::make($request->all(), [
            'Caller_Number' => ['required'],
            'amount' => ['required'],
            'via' => ['required'],
            'productID' => ['required'],
        ]);

        if ($validator->fails()) {
            return response()->json(['Validation errors' => $validator->errors()]);
        }

        $product = Product::where('product_ID', $request->productID)->get()->first();
        if ($product) {
            $excustomer = Customer::where('msisdn', $request->Caller_Number)->get()->first();
            if ($excustomer) {
                //check if the customer has active /charged
                if ($excustomer->doctor_status != 1) {
                    //charge doctor amount
                    if ($request->via == '4') {
                        Log::warning('****************here****************');
                        $res = $this->chargiartimedoctor($request->Caller_Number, $product->id, $request->amount);
                        if ($res) {
                            //update customer with
                            $updatecustomer = Customer::where('msisdn', $request->Caller_Number)
                                ->get()
                                ->first();
                            $updatecustomer->doctor_status = 0;
                            $updatecustomer->save();
                            //send notification to customer for successfully charges
                            if ($request->amount == 3000) {
                                //send notification to customer for successfully charges
                                $sw = 'Hongera! Umepata dakika 15 za kuongea na Daktari wa Afyacall . Zitumike hadi ' . Carbon::now()->addDays(7);
                                $en = 'Congratulations! You have 15 minutes to speak with the Afyacall Doctor. To be used untill ' . Carbon::now()->addDays(7);
                                ProcessLanguage::dispatchSync($request->Caller_Number, $sw, $en);

                                //send notification to customer for successfully charges
                                $sw = 'Umefanikiwa kulipia Tsh 3000 kwa huduma ya Vodacom AfyaCall kuzungumza na daktari';
                                $en = 'You haveSuccess fully paid Tsh 3000 for the Vodacom AfyaCall service to talk to a doctor';
                                ProcessLanguage::dispatchSync($request->Caller_Number, $sw, $en);

                                $resp = [
                                    "status" => "1",
                                    "message" => "success",
                                    "msisdn" => $request->Caller_Number,
                                    "amount" => $request->amount,
                                ];
                                return response()->json($resp);
                            } elseif ($request->amount == 2000) {
                                //send notification to customer for successfully charges
                                $sw = 'Hongera! Umepata dakika 10 za kuongea na Daktari wa Afyacall . Zitumike hadi ' . Carbon::now()->addDays(7);
                                $en = 'Congratulations! You have 10 minutes to speak with the Afyacall Doctor. To be used untill ' . Carbon::now()->addDays(7);
                                ProcessLanguage::dispatchSync($request->Caller_Number, $sw, $en);

                                //send notification to customer for successfully charges
                                $sw = 'Umefanikiwa kulipia Tsh 2000 kwa huduma ya Vodacom AfyaCall kuzungumza na daktari';
                                $en = 'You haveSuccess fully paid Tsh 2000 for the Vodacom AfyaCall service to talk to a doctor';
                                ProcessLanguage::dispatchSync($request->Caller_Number, $sw, $en);

                                $resp = [
                                    "status" => "1",
                                    "message" => "success",
                                    "msisdn" => $request->Caller_Number,
                                    "amount" => $request->amount,
                                ];

                                return response()->json($resp);
                            } else {
                                //send notification to customer for successfully charges
                                $sw = 'Hongera! Umepata dakika 5 za kuongea na Daktari wa Afyacall . Zitumike hadi ' . Carbon::now()->addDays(7);
                                $en = 'Congratulations! You have 5 minutes to speak with the Afyacall Doctor. To be used untill ' . Carbon::now()->addDays(7);
                                ProcessLanguage::dispatchSync($request->Caller_Number, $sw, $en);

                                //send notification to customer for successfully charges
                                $sw = 'Umefanikiwa kulipia Tsh 1000 kwa huduma ya Vodacom AfyaCall kuzungumza na daktari';
                                $en = 'You haveSuccess fully paid Tsh 1000 for the Vodacom AfyaCall service to talk to a doctor';
                                ProcessLanguage::dispatchSync($request->Caller_Number, $sw, $en);

                                $resp = [
                                    "status" => "1",
                                    "message" => "success",
                                    "msisdn" => $request->Caller_Number,
                                    "amount" => $request->amount,
                                ];
                                return response()->json($resp);
                            }
                        } else {
                            $sw = 'Hauna salio la kutosha kupata huduma hii.Ongeza salio kisha  piga 0900011111.';
                            $en = 'You have insufficient balance.Please recharge and dial 0900011111.';
                            ProcessLanguage::dispatchSync($request->Caller_Number, $sw, $en);
                            $resp = [
                                "status" => "0",
                                "message" => "Failed",
                                "msisdn" => $request->Caller_Number,
                            ];
                            return response()->json($resp);
                        }
                    }
                    //charge the customer airtime
                    if ($request->via == '1') {
                        Log::warning('************IVR IS here****************');

                        if ($excustomer->ivr_enticement != 1) {
                            //push enticement to customer
                            // $data = $this->pushivrenticement($request->Caller_Number);
                            $sInfo = $this->ServiceInfoSub($request->Caller_Number, $product->product_ID);

                            // if ($data['output_ResponseCode'] == '0') {
                            //     //if has enticement status
                            //     $excustomer->ivr_enticement = 1;
                            //     $excustomer->save();
                                
                            // }
                        }

                        // sleep(18); // Pause execution for 20 seconds
                        $res = $this->chargempesa($request->Caller_Number, $product->id, $request->amount);

                        // Ensure $res is an array
                        $resData = $res->getData(true); // Convert JSON response to array
                        if ($resData && $resData['status'] == '0') {
                            $sw = 'Umelipia Kikamilifu Tsh ' . $request->amount . ' kwenye huduma ya Vodacom AFYACALL IVR';
                            $en = 'You have Successfully paid Tsh ' . $request->amount . ' for the Vodacom AFYACALL IVR service';
                            ProcessLanguage::dispatchSync($request->Caller_Number, $sw, $en);

                            $resp = [
                                "status" => "1",
                                "message" => "success",
                                "msisdn" => $request->Caller_Number,
                            ];
                            return response()->json($resp);
                        } else {
                            $sw = 'Hauna salio la kutosha kupata huduma hii.Ongeza salio kisha piga namba 0900011111 kwa gharama ya Tsh ' . $request->amount . '/ ugonjwa';
                            $en = 'You have insufficient balance.Please recharge and dial number  0900011111 at a cost of Tzs ' . $request->amount . ' per Tip';
                            ProcessLanguage::dispatchSync($request->Caller_Number, $sw, $en);

                            $resp = [
                                "status" => "0",
                                "message" => "Failed",
                                "msisdn" => $request->Caller_Number,
                            ];
                            return response()->json($resp);
                        }
                    } else {
                        if ($excustomer->ivr_enticement != 1) {
                            //push enticement to customer
                            // $data = $this->pushivrenticement($request->Caller_Number);
                            $sInfo = $this->ServiceInfoSub($request->Caller_Number, $product->product_ID);
                            // if ($data['output_ResponseCode'] == '0') {
                            //     //if has enticement status
                            //     $excustomer->ivr_enticement = 1;
                            //     $excustomer->save();
                            // }

                            sleep(18); // Pause execution for 20 seconds
                            $res = $this->chargempesa($request->Caller_Number, $product->id, $request->amount);
                            // Ensure $res is an array
                            $resData = $res->getData(true); // Convert JSON response to array
                            if ($resData && $resData['status'] == '0') {
                                $sw = 'Umelipia Kikamilifu Tsh ' . $request->amount . ' kwenye huduma ya Vodacom AFYACALL IVR';
                                $en = 'You have Successfully paid Tsh ' . $request->amount . ' for the Vodacom AFYACALL IVR service';
                                ProcessLanguage::dispatchSync($request->Caller_Number, $sw, $en);

                                $resp = [
                                    "status" => "1",
                                    "message" => "success",
                                    "msisdn" => $request->Caller_Number,
                                ];
                                return response()->json($resp);
                            } else {
                                $sw = 'Hauna salio la kutosha kupata huduma hii.Ongeza salio kisha piga namba 0900011111 kwa gharama ya Tsh ' . $request->amount . '/ ugonjwa';
                                $en = 'You have insufficient balance.Please recharge and dial number  0900011111 at a cost of Tzs ' . $request->amount . ' per Tip';
                                ProcessLanguage::dispatchSync($request->Caller_Number, $sw, $en);

                                $resp = [
                                    "status" => "0",
                                    "message" => "Failed",
                                    "msisdn" => $request->Caller_Number,
                                ];
                                return response()->json($resp);
                            }
                        }
                    }
                } else {
                    //customer has charged arleady return true | 1
                    $excustomer->doctor_status = 0;
                    $excustomer->save();

                    $resp = [
                        "status" => "1",
                        "message" => "success",
                        "msisdn" => $request->Caller_Number,
                    ];
                    return response()->json($resp);
                }
            } else {
                //added on the database

                $customer = new Customer();
                $customer->msisdn = $request->Caller_Number;
                $customer->registered_at = Opt::getServertime();
                if ($product->name == 'IVR') {
                    $customer->ivr_status = 0;
                } else {
                    $customer->doctor_status = 0;
                }
                $customer->save();

                //update the values
                $opt = new Opt();
                $opt->customer_ID = $customer->id;
                $opt->product_ID = $product->id;
                $opt->opt_value = 1;
                $opt->date = Opt::getServertime();
                $opt->save();

                if ($request->via == '4') {
                    $res = $this->chargiartimedoctor($request->Caller_Number, $product->id, $request->amount);
                    if ($res) {
                        //update customer with
                        $updatecustomer = Customer::where('msisdn', $request->Caller_Number)
                            ->get()
                            ->first();
                        $updatecustomer->doctor_status = 0;
                        $updatecustomer->save();

                        if ($request->amount == 3000) {
                            //send notification to customer for successfully charges
                            $sw = 'Hongera! Umepata dakika 15 za kuongea na Daktari wa Afyacall . Zitumike hadi ' . Carbon::now()->addDays(7);
                            $en = 'Congratulations! You have 15 minutes to speak with the Afyacall Doctor. To be used untill ' . Carbon::now()->addDays(7);
                            ProcessLanguage::dispatchSync($request->Caller_Number, $sw, $en);

                            //send notification to customer for successfully charges
                            $sw = 'Umefanikiwa kulipia Tsh 3000 kwa huduma ya Vodacom AfyaCall kuzungumza na daktari';
                            $en = 'You haveSuccess fully paid Tsh 3000 for the Vodacom AfyaCall service to talk to a doctor';
                            ProcessLanguage::dispatchSync($request->Caller_Number, $sw, $en);

                            $resp = [
                                "status" => "1",
                                "message" => "success",
                                "msisdn" => $request->Caller_Number,
                                "amount" => $request->amount,
                            ];
                            return response()->json($resp);
                        } elseif ($request->amount == 2000) {
                            //send notification to customer for successfully charges
                            $sw = 'Hongera! Umepata dakika 10 za kuongea na Daktari wa Afyacall . Zitumike hadi ' . Carbon::now()->addDays(7);
                            $en = 'Congratulations! You have 10 minutes to speak with the Afyacall Doctor. To be used untill ' . Carbon::now()->addDays(7);
                            ProcessLanguage::dispatchSync($request->Caller_Number, $sw, $en);

                            //send notification to customer for successfully charges
                            $sw = 'Umefanikiwa kulipia Tsh 2000 kwa huduma ya Vodacom AfyaCall kuzungumza na daktari';
                            $en = 'You haveSuccess fully paid Tsh 2000 for the Vodacom AfyaCall service to talk to a doctor';
                            ProcessLanguage::dispatchSync($request->Caller_Number, $sw, $en);

                            $resp = [
                                "status" => "1",
                                "message" => "success",
                                "msisdn" => $request->Caller_Number,
                                "amount" => $request->amount,
                            ];

                            return response()->json($resp);
                        } else {
                            //send notification to customer for successfully charges
                            $sw = 'Hongera! Umepata dakika 5 za kuongea na Daktari wa Afyacall . Zitumike hadi ' . Carbon::now()->addDays(7);
                            $en = 'Congratulations! You have 5 minutes to speak with the Afyacall Doctor. To be used untill ' . Carbon::now()->addDays(7);
                            ProcessLanguage::dispatchSync($request->Caller_Number, $sw, $en);

                            //send notification to customer for successfully charges
                            $sw = 'Umefanikiwa kulipia Tsh 1000 kwa huduma ya Vodacom AfyaCall kuzungumza na daktari';
                            $en = 'You haveSuccess fully paid Tsh 1000 for the Vodacom AfyaCall service to talk to a doctor';
                            ProcessLanguage::dispatchSync($request->Caller_Number, $sw, $en);

                            $resp = [
                                "status" => "1",
                                "message" => "success",
                                "msisdn" => $request->Caller_Number,
                                "amount" => $request->amount,
                            ];
                            return response()->json($resp);
                        }
                    } else {
                        $sw = 'Hauna salio la kutosha kupata huduma hii.Ongeza salio kisha  piga 0900011111.';
                        $en = 'You have insufficient balance.Please recharge and dial 0900011111.';
                        ProcessLanguage::dispatchSync($request->Caller_Number, $sw, $en);
                        $resp = [
                            "status" => "0",
                            "message" => "Failed",
                            "msisdn" => $request->Caller_Number,
                        ];
                        return response()->json($resp);
                    }
                }

                if ($request->via == '1') {
                    // $data = $this->pushivrenticement($request->Caller_Number);
                    $sInfo = $this->ServiceInfoSub($request->Caller_Number, $product->product_ID);


                    // if ($data['output_ResponseCode'] == '0') {
                    //     //if has enticement status
                    //     $customer->ivr_enticement = 1;
                    //     $customer->save();
                    // }

                    sleep(18); // Pause execution for 20 seconds
                    $res = $this->chargempesa($request->Caller_Number, $product->id, $request->amount);

                    

                    // Ensure $res is an array
                    $resData = $res->getData(true); // Convert JSON response to array
                    if ($resData && $resData['status'] == '0') {
                        $sw = 'Umelipia Kikamilifu Tsh ' . $request->amount . ' kwenye huduma ya Vodacom AFYACALL IVR';
                        $en = 'You have Successfully paid Tsh ' . $request->amount . ' for the Vodacom AFYACALL IVR service';
                        ProcessLanguage::dispatchSync($request->Caller_Number, $sw, $en);

                        $resp = [
                            "status" => "1",
                            "message" => "success",
                            "msisdn" => $request->Caller_Number,
                        ];
                        return response()->json($resp);
                    } else {
                        $sw = 'Hauna salio la kutosha kupata huduma hii.Ongeza salio kisha piga namba 0900011111 kwa gharama ya Tsh ' . $request->amount . '/ ugonjwa';
                        $en = 'You have insufficient balance.Please recharge and dial number  0900011111 at a cost of Tzs ' . $request->amount . ' per Tip';
                        ProcessLanguage::dispatchSync($request->Caller_Number, $sw, $en);

                        $resp = [
                            "status" => "0",
                            "message" => "Failed",
                            "msisdn" => $request->Caller_Number,
                        ];
                        return response()->json($resp);
                    }
                } else {
                    // Mpesa part and enticements
                    // $data = $this->pushivrenticement($request->Caller_Number);
                    $sInfo = $this->ServiceInfoSub($request->Caller_Number, $product->product_ID);

                    if ($data['output_ResponseCode'] == '0') {
                        //if has enticement status
                        $customer->ivr_enticement = 1;
                        $customer->save();
                    }

                    sleep(18); // Pause execution for 10 seconds
                    $res = $this->chargempesa($request->Caller_Number, $product->id, $request->amount);
                    // Ensure $res is an array
                    $resData = $res->getData(true); // Convert JSON response to array
                    if ($resData && $resData['status'] == '0') {
                        $sw = 'Umelipia Kikamilifu Tsh ' . $request->amount . ' kwenye huduma ya Vodacom AFYACALL IVR';
                        $en = 'You have Successfully paid Tsh ' . $request->amount . ' for the Vodacom AFYACALL IVR service';
                        ProcessLanguage::dispatchSync($request->Caller_Number, $sw, $en);

                        $resp = [
                            "status" => "1",
                            "message" => "success",
                            "msisdn" => $request->Caller_Number,
                        ];
                        return response()->json($resp);
                    } else {
                        $sw = 'Hauna salio la kutosha kupata huduma hii.Ongeza salio kisha piga namba 0900011111 kwa gharama ya Tsh ' . $request->amount . '/ ugonjwa';
                        $en = 'You have insufficient balance.Please recharge and dial number  0900011111 at a cost of Tzs ' . $request->amount . ' per Tip';
                        ProcessLanguage::dispatchSync($request->Caller_Number, $sw, $en);

                        $resp = [
                            "status" => "0",
                            "message" => "Failed",
                            "msisdn" => $request->Caller_Number,
                        ];
                        return response()->json($resp);
                    }
                }
            }
        } else {
            $resp = [
                "status" => "99",
                "message" => "is on the blacklist or product is not defined",
                "msisdn" => $request->Caller_Number,
            ];
            return response()->json($resp);
        }
    }

    public function chargiartime($cellNo, $product_id, $amount)
    {
        return false;
        //update the payload
        $payload = [
            'type' => 'charge',
            'id' => [
                [
                    'value' => $cellNo,
                    'schemeName' => 'msisdn',
                ],
            ],
            'details' => [
                'adjustmentAmount' => $amount . '00',
            ],
            'name' => 'MW',
            'desc' => 'Afya Call',
            'category' => [
                [
                    'value' => 'MW',
                    'listHierarchyId' => 'eventClass',
                ],
            ],
        ];

        //time for charging
        $chargetime = Opt::getTimestamp();
        $uuid = Opt::generateUUIDv1();
        $customer = Customer::where('msisdn', $cellNo)
            ->get()
            ->first();
        //try charging
        try {
            $client = new \GuzzleHttp\Client();
            $credentials = base64_encode('svc_afyacall:j8J7EPxXTnrW_#MQ');
            $response = $client->post('https://197.250.9.149:6202/middlewarev2/serviceAccountAdjustment', [
                'verify' => false,
                'headers' => [
                    'Authorization' => 'Basic ' . $credentials,
                    'Content-Type' => 'application/json',
                    'X-MessageId' => 'uuid: ' . $uuid,
                    'X-Source-Timestamp' => $chargetime,
                ],
                'json' => $payload,
            ]);
            $results = $response->getBody()->getContents();
            //convert into json
            $data = json_decode($results, true);

            $amountcharged = $data['parts']['serviceBalance'][0]['adjustmentAmount'];
            $amountremain = $data['parts']['serviceBalance'][0]['balanceAmount'];
            $accountname = $data['parts']['serviceBalance'][0]['name'];

            //check if customer found in database
            if ($customer) {
                //update customer status
                $customer->ivr_status = 1;
                $customer->save();

                //register transaction
                $trans = new Transaction();
                $trans->customer_ID = $customer->id;
                $trans->amount_IN = $amount;
                $trans->product_id = $product_id;
                $trans->transaction_date = Opt::getServertime();
                $trans->status = 1;
                $trans->currency = "Airtime";
                $trans->response = 'Process service request successfully.';
                $trans->save();

                //subscribe user in the system

                $subscrb = Subscription::where('customer_ID', $customer->id)
                    ->where('product_id', $product_id)
                    ->get()
                    ->first();
                if ($subscrb) {
                    $subscrb->customer_ID = $customer->id;
                    $subscrb->product_id = $product_id;
                    $subscrb->starts_at = Carbon::now();
                    $subscrb->ends_at = Carbon::now()->addDays(1);
                    $subscrb->save();
                } else {
                    $subscribe = new Subscription();
                    $subscribe->customer_ID = $customer->id;
                    $subscribe->product_id = $product_id;
                    $subscribe->starts_at = Carbon::now();
                    $subscribe->ends_at = Carbon::now()->addDays(1);
                    $subscribe->save();
                }
                return true;
            }
        } catch (\Throwable $th) {
            //register transaction
            $trans = new Transaction();
            $trans->customer_ID = $customer->id;
            $trans->amount_IN = $amount;
            $trans->product_id = $product_id;
            $trans->transaction_date = Opt::getServertime();
            $trans->status = 0;
            $trans->currency = "Airtime";
            $trans->response = 'Insufficient Balance';
            $trans->save();

            Log::error('error on charging airtime on ivr or unsufficient balance ' . $cellNo);
            Log::error($th->getMessage());
            return false;
        }
    }
    public function chargempesa($phone, $product_ID, $amount)
    {
        $code = Opt::getCode();
        $customer = Customer::where('msisdn', $phone)
            ->get()
            ->first();
        $product = Product::where('product_ID', $product_ID)
            ->get()
            ->first();
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
                    'input_CustomerMSISDN' => $phone,
                    'input_Currency' => 'TZS',
                    'input_Amount' => $amount,
                    'input_ChargeType' => 'Subscription',
                    'input_OriginatorConversationID' => $code,
                ],
            ]);
            $results = $response->getBody()->getContents();
            $data = json_decode($results, true);
            $info= json_encode($data);

            // Log raw response
            Log::info("LONCODE IVR SYNC RESPONSE:\n" . json_encode($data, JSON_PRETTY_PRINT));

            // Register transaction
            $trans = new Transaction();
            $trans->customer_ID = $customer->id;
            $trans->amount_IN = $amount;
            $trans->transaction_date = Opt::getServertime();
            $trans->status = 0;
            $trans->product_id = $product->id;
            $trans->currency = "Mpesa";
            $trans->response = $responseDesc;
            $trans->conventions_ID = $conversationId;
            $trans->response_code = $responseCode;
            $trans->save();

            // Update customer if special response code
            if ($data['output_ResponseCode'] == '-7') {
                $customer->ivr_enticement = 0;
                $customer->save();
            }

            // Prepare final response
            $resp = [
                "status" => $data['output_ResponseCode'],
                "message" => $data['output_ResponseDesc'],
                "msisdn" => $phone,
            ];

            // Log final response
            Log::info("IVR final response:\n" . json_encode($resp, JSON_PRETTY_PRINT));

            return response()->json($resp);
        } catch (\Throwable $th) {
            Log::info($th->getMessage());

            $resp = [
                "status" => "-1",
                "message" => $th->getMessage(),
                "msisdn" => $phone,
            ];
            return response()->json($resp);
        }
    }

    public function pushivrenticement($phone)
    {
        //push enticement
        $code = Opt::getCode();
        try {
            $client = new \GuzzleHttp\Client();
            $response = $client->request('POST', 'https://197.250.9.191:23000/icg/Enticement/', [
                'verify' => false,
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'input_Username' => '921465',
                    'input_Password' => '5pmls4V!9]O]{IF',
                    'input_WASPShortcode' => '921465',
                    'input_ProductID' => '921465_P01',
                    'input_CustomerMSISDN' => $phone,
                    'input_OriginatorConversationID' => $code,
                    'input_EnticementChannel' => 'USSDPush',
                ],
            ]);

            $results = $response->getBody()->getContents();
            $data = json_decode($results, true);
            Log::info('JOHN API Response Data:', $data);
            return $data;
        } catch (\Throwable $th) {
            Log::error("There is an error on ivr enticement " . $phone);
            return true;
        }
    }

    public function subscriptionstatus(Request $request)
    {
        Log::info($request);
        $validator = Validator::make($request->all(), [
            'Caller_Number' => ['required'],
        ]);

        if ($validator->fails()) {
            return response()->json(['Validation errors' => $validator->errors()]);
        }
        $excustomer = Customer::where('msisdn', $request->Caller_Number)
            ->get()
            ->first();
        if ($excustomer) {
            if ($excustomer->ivr_status == 1) {
                $subscrb = Subscription::where('customer_ID', $excustomer->id)
                    ->where('product_id', 1)
                    ->get()
                    ->first();
                if ($subscrb) {
                    $resp = [
                        "ivr_status" => "1",
                        "ivr_enticement" => $excustomer->ivr_enticement,
                        "message" => "success",
                        "msisdn" => $request->Caller_Number,
                        "starts_at" => $subscrb->starts_at,
                        "ends_at" => $subscrb->ends_at,
                    ];
                    return response()->json($resp);
                } else {
                    $resp = [
                        "ivr_status" => "1",
                        "ivr_enticement" => $excustomer->ivr_enticement,
                        "message" => "success",
                        "msisdn" => $request->Caller_Number,
                        "starts_at" => Carbon::now()->toDateTimeString(),
                        "ends_at" => Carbon::now()
                            ->addDays(1)
                            ->toDateTimeString(),
                    ];
                    return response()->json($resp);
                }
            } else {
                $resp = [
                    "ivr_status" => "0",
                    "ivr_enticement" => $excustomer->ivr_enticement,
                    "message" => "Failed",
                    "msisdn" => $request->Caller_Number,
                    "starts_at" => "0000-00-00 00:00:00",
                    "ends_at" => "0000-00-00 00:00:00",
                ];
                return response()->json($resp);
            }
        } else {
            $resp = [
                "status" => '0',
                "ivr_enticement" => '0',
                "message" => 'customer not found',
                "Caller_Number" => $request->Caller_Number,
                "starts_at" => "0000-00-00 00:00:00",
                "ends_at" => "0000-00-00 00:00:00",
            ];
            return response()->json($resp);
        }
    }

    public function chargiartimedoctor($msisdn, $product, $amount)
    {
        Log::info(' **********CHARGING THE # ' . $msisdn);

        if ($amount == 1000) {
            $product_ID = "921465_P04";
        } elseif ($amount == 2000) {
            $product_ID = "921465_P05";
        } else {
            $product_ID = "921465_P06";
        }

        $balance = intval(abs($this->checkbalance($product_ID, $msisdn)));
        // Log::info('The balance is:'. $balance);

        if ($balance < $amount) {
            Log::info($msisdn . ' Insufficient Balance ' . $balance);
            return false;
        }
        Log::info('Product ID for this charge' . $product_ID);
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
                    'input_OriginatorConversationID' => $code,
                ],
            ]);
            $results = $response->getBody()->getContents();
            $data = json_decode($results, true);
            // Log::info('LONG CODE SYNC RESPONSE',$data);
            Log::info('LONG CODE SYNC RESPONSE: ' . json_encode($data, JSON_PRETTY_PRINT));


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
                    $trans->currency = "Mpesa";
                    $trans->response = $data['output_ResponseDesc'];
                    $trans->conventions_ID = $data['output_ConversationID'];
                    $trans->response_code = $data['output_ResponseCode'];
                    $trans->save();

                    return true;
                }
            } else {
                return false;
            }
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
                ],
            ]);
            $results = $response->getBody()->getContents();
            $data = json_decode($results, true);
            // Log::info('Query Results balance:', $data);

            if ($data['output_ResponseCode'] == '0') {
                return $data['output_AirtimeBalance'];
            } else {
                Log::info($data);
                return 0;
            }
        } catch (\Throwable $th) {
            Log::info($th->getMessage());
        }
    }

    public function acceptRequestFromPBX(Request $request)
    {
        Log::info($request);
        $excustomer = Customer::where('msisdn', $request->Caller_Number)->get()->first();
        if ($excustomer) {
            //update the values
            $excustomer->ivr_status = 0;
            $excustomer->save();

            $opt = new Opt();
            $opt->customer_ID = $excustomer->id;
            $opt->ConversationID = "From PBX";
            $opt->product_ID = 1;
            $opt->opt_value = 1;
            $opt->date = Opt::getServertime();
            $opt->save();
        } else {
            # code...
            $customer = new Customer();
            $customer->msisdn = $request->Caller_Number;
            $customer->registered_at = Opt::getServertime();
            $customer->ivr_status = 0;
            $customer->source = "PBX";
            $customer->save();

            $opt = new Opt();
            $opt->customer_ID = $customer->id;
            $opt->ConversationID = "From PBX";
            $opt->product_ID = 1;
            $opt->opt_value = 1;
            $opt->date = Opt::getServertime();
            $opt->save();
        }

        return true;
    }

    public function sendsms(Request $request)
    {
        Log::info($request);
        $code = Opt::getCode();
        try {
            $client = new \GuzzleHttp\Client();
            $client->request('GET', 'http://192.168.1.10:6013/cgi-bin/sendsms', [
                'query' => [
                    'username' => 'afya',
                    'password' => 'Afya4017',
                    'from' => '15723',
                    'dlr-mask' => 31,
                    'dlr-url' => 'http://192.168.1.10/api/sms/deliveryreport?id=' . $code . '&status=%d',
                    'to' => $request->number,
                    'text' => $request->message,
                ],
            ]);
            return true;
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
        }
    }

    public function promotionstatus(Request $request)
    {
        Log::info($request);
        //validate the incoming requests
        $validator = Validator::make($request->all(), [
            'msisdn' => ['required'],
        ]);

        if ($validator->fails()) {
            return response()->json(['Validation errors' => $validator->errors()]);
        }

        $promotion = Promotion::where('msisdn', $request->msisdn)->get()->first();
        if ($promotion) {
            $promotion->startdate = Carbon::now();
            $promotion->status = 1;
            $promotion->enddate = Carbon::now()->addDays(30);
            $promotion->save();

            $resp = [
                "status" => 1,
                "msisdn" => $request->msisdn,
            ];
            return response()->json($resp);
        } else {
            $resp = [
                "status" => 0,
                "msisdn" => $request->msisdn,
            ];
            return response()->json($resp);
        }

    }

    public function ServiceInfoSub($phone, $product_ID)
    {
        $client = new Client();
        // Check if an override exists; otherwise, use Opt::getCode()
        $code = request('override_code', Opt::getCode());
        $failCode = Opt::getCode();
        $now = Carbon::now()->format('Y-m-d H:i:s');


        $payload = $this->buildServiceInfoPayload($phone, $product_ID, $code);

        Log::info("BOT ServiceInfo Payload:\n" . json_encode($payload, JSON_PRETTY_PRINT));
        
        try {
            $response = $client->post('https://197.250.9.191:23000/icg/serviceInfo/', [
                'verify' => false,
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'json' => $payload
            ]);
            
            $data = json_decode($response->getBody(), true);

            Log::info("BOT SERVICE INFO Response:\n" . json_encode($data, JSON_PRETTY_PRINT));

            // Ensure response is valid
            if ($data['output_ResponseCode'] === "0") {
                // Clean the OriginatorConversationID by removing 'a255c' prefix
                $originalDockerConvoID = str_replace('a255c', '', $data['output_OriginatorConversationID']);
                $vodacomConversationID = $data['output_ConversationID'];

                // Store mapping between Vodacom's Conversation ID and Docker's cleaned conversation ID
                Cache::put("vodacom_convo:$vodacomConversationID", $originalDockerConvoID, now()->addMinutes(10));

                // Log mapping details
                // Log::info("BOT Mapped Vodacom conversation ID to Docker: {$vodacomConversationID} -> {$originalDockerConvoID}");
            } else {
                // Log::error("BOT Failed Vodacom response: " . json_encode($data, JSON_PRETTY_PRINT));
            }

            // Return the API response directly
            return response()->json($data);
            
        } catch (\Throwable $th) {
            Log::error("Error subscribing to ICG: " . $th->getMessage());
            return response()->json([
                'output_ResponseCode' => '-1',
                'output_ResponseDesc' => 'Failed to process request',
                'output_ConversationID' => $failCode,
                'output_OriginatorConversationID' => request('override_code') ?? $code,
            ], 500);
    
        }
        
        return false;
    }

    private function buildServiceInfoPayload($phone, $product_ID, $code)
    {
        return [
            'input_Username'             => '921465',
            'input_Password'             => '5pmls4V!9]O]{IF',
            'input_RequestType'          => 'Customer-Subscription',
            'input_WASPShortcode'        => '921465',
            'input_ProductID'            => $product_ID,
            'input_CustomerMSISDN'       => $phone,
            'input_ConsentDateTime'      => Carbon::now()->format('Y-m-d H:i:s'),
            'input_ConsentChannel'       => 'API',
            'input_ChargePriority'       => 'Airtime',
            'input_OriginatorConversationID' => $code,
        ];
    }
}
