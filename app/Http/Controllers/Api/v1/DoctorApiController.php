<?php

namespace App\Http\Controllers\Api\v1;

use Carbon\Carbon;
use App\Models\Opt;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Transaction;
use App\Models\Subscription;
use Illuminate\Http\Request;
use App\Jobs\ProcessLanguage;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class DoctorApiController extends Controller
{
    public function chargeDoctorAirtime(Request $request)
    {
        Log::info('=================charge airtime via new icg airtime endpoint==========');
        Log::info($request->all());
        //validate the incoming data

        $validator = Validator::make(
            $request->all(),
            [
                'msisdn' => ['required'],
                'amount' => ['required'],
            ]
        );

        if ($validator->fails()) {
            return response()->json(['Validation errors' => $validator->errors()]);
        }

        $excustomer = Customer::where('msisdn', $request->msisdn)->get()->first();
        if ($excustomer) {
            //check if the customer has active /charged
            if ($excustomer->doctor_status != 1) {
                $res = $this->chargiartimedoctoricg($request->msisdn, $request->amount);
                if ($res) {
                    //update customer with
                    $excustomer->doctor_status = 0;
                    $excustomer->save();

                    if ($request->amount == 3000) {
                        //send notification to customer for successfully charges
                        $sw = 'Hongera! Umepata dakika 15 za kuongea na Daktari wa Afyacall . Zitumike hadi ' . Carbon::now()->addDays(7);
                        $en = 'Congratulations! You have 15 minutes to speak with the Afyacall Doctor. To be used untill ' . Carbon::now()->addDays(7);
                        ProcessLanguage::dispatchSync($request->msisdn, $sw, $en);

                        //send notification to customer for successfully charges
                        $sw = 'Umefanikiwa kulipia Tsh 3000 kwa huduma ya Vodacom AfyaCall kuzungumza na daktari';
                        $en = 'You haveSuccess fully paid Tsh 3000 for the Vodacom AfyaCall service to talk to a doctor';
                        ProcessLanguage::dispatchSync($request->msisdn, $sw, $en);

                        $resp = [
                            'status' => '1',
                            'message' => 'success',
                            'msisdn' => $request->msisdn,
                            'amount' => $request->amount,
                        ];

                        return response()->json($resp);
                    } elseif ($request->amount == 2000) {
                        //send notification to customer for successfully charges
                        $sw = 'Hongera! Umepata dakika 10 za kuongea na Daktari wa Afyacall . Zitumike hadi ' . Carbon::now()->addDays(7);
                        $en = 'Congratulations! You have 10 minutes to speak with the Afyacall Doctor. To be used untill ' . Carbon::now()->addDays(7);
                        ProcessLanguage::dispatchSync($request->msisdn, $sw, $en);

                        //send notification to customer for successfully charges
                        $sw = 'Umefanikiwa kulipia Tsh 2000 kwa huduma ya Vodacom AfyaCall kuzungumza na daktari';
                        $en = 'You haveSuccess fully paid Tsh 2000 for the Vodacom AfyaCall service to talk to a doctor';
                        ProcessLanguage::dispatchSync($request->msisdn, $sw, $en);

                        $resp = [
                            'status' => '1',
                            'message' => 'success',
                            'msisdn' => $request->msisdn,
                            'amount' => $request->amount,
                        ];

                        return response()->json($resp);
                    } else {
                        //send notification to customer for successfully charges
                        $sw = 'Hongera! Umepata dakika 5 za kuongea na Daktari wa Afyacall . Zitumike hadi ' . Carbon::now()->addDays(7);
                        $en = 'Congratulations! You have 5 minutes to speak with the Afyacall Doctor. To be used untill ' . Carbon::now()->addDays(7);
                        ProcessLanguage::dispatchSync($request->msisdn, $sw, $en);

                        //send notification to customer for successfully charges
                        $sw = 'Umefanikiwa kulipia Tsh 1000 kwa huduma ya Vodacom AfyaCall kuzungumza na daktari';
                        $en = 'You haveSuccess fully paid Tsh 1000 for the Vodacom AfyaCall service to talk to a doctor';
                        ProcessLanguage::dispatchSync($request->msisdn, $sw, $en);

                        $resp = [
                            'status' => '1',
                            'message' => 'success',
                            'msisdn' => $request->msisdn,
                            'amount' => $request->amount,
                        ];

                        return response()->json($resp);
                    }
                } else {
                    $sw = 'Hauna salio la kutosha kupata huduma hii.Ongeza salio kisha  piga 0900011111';
                    $en = 'You have insufficient balance.Please recharge and dial 0900011111';
                    ProcessLanguage::dispatchSync($request->msisdn, $sw, $en);

                    $resp = [
                        'status' => '0',
                        'message' => 'Failed',
                        'msisdn' => $request->msisdn,
                        'amount' => $request->amount,
                    ];

                    return response()->json($resp);
                }
            } else {
                //customer has charged arleady return true | 1
                $excustomer->doctor_status = 0;
                $excustomer->save();

                $resp = [
                    'status' => '1',
                    'message' => 'success',
                    'msisdn' => $request->msisdn,
                ];

                return response()->json($resp);
            }
        } else {
            //added on the database
            $customer = new Customer();
            $customer->msisdn = $request->msisdn;
            $customer->registered_at = Opt::getServertime();
            $customer->doctor_status = 0;
            $customer->save();

            //update the values
            $opt = new Opt();
            $opt->customer_ID = $customer->id;
            $opt->product_ID = 4;
            $opt->opt_value = 1;
            $opt->date = Opt::getServertime();
            $opt->save();

            $res = $this->chargiartimedoctoricg($request->msisdn, $request->amount);
            if ($res) {
                //update customer with status of 0 after success charging
                Customer::where('msisdn', $request->msisdn)->update(['doctor_status', 0]);

                if ($request->amount == 3000) {
                    //send notification to customer for successfully charges
                    $sw = 'Hongera! Umepata dakika 15 za kuongea na Daktari wa Afyacall . Zitumike hadi ' . Carbon::now()->addDays(7);
                    $en = 'Congratulations! You have 15 minutes to speak with the Afyacall Doctor. To be used untill ' . Carbon::now()->addDays(7);
                    ProcessLanguage::dispatchSync($request->msisdn, $sw, $en);

                    //send notification to customer for successfully charges
                    $sw = 'Umefanikiwa kulipia Tsh 3000 kwa huduma ya Vodacom AfyaCall kuzungumza na daktari';
                    $en = 'You haveSuccess fully paid Tsh 3000 for the Vodacom AfyaCall service to talk to a doctor';
                    ProcessLanguage::dispatchSync($request->msisdn, $sw, $en);

                    $resp = [
                        'status' => '1',
                        'message' => 'success',
                        'msisdn' => $request->msisdn,
                        'amount' => $request->amount,
                    ];

                    return response()->json($resp);
                } elseif ($request->amount == 2000) {
                    //send notification to customer for successfully charges
                    $sw = 'Hongera! Umepata dakika 10 za kuongea na Daktari wa Afyacall . Zitumike hadi ' . Carbon::now()->addDays(7);
                    $en = 'Congratulations! You have 10 minutes to speak with the Afyacall Doctor. To be used untill ' . Carbon::now()->addDays(7);
                    ProcessLanguage::dispatchSync($request->msisdn, $sw, $en);

                    //send notification to customer for successfully charges
                    $sw = 'Umefanikiwa kulipia Tsh 2000 kwa huduma ya Vodacom AfyaCall kuzungumza na daktari';
                    $en = 'You haveSuccess fully paid Tsh 2000 for the Vodacom AfyaCall service to talk to a doctor';
                    ProcessLanguage::dispatchSync($request->msisdn, $sw, $en);

                    $resp = [
                        'status' => '1',
                        'message' => 'success',
                        'msisdn' => $request->msisdn,
                        'amount' => $request->amount,
                    ];

                    return response()->json($resp);
                } else {
                    //send notification to customer for successfully charges
                    $sw = 'Hongera! Umepata dakika 5 za kuongea na Daktari wa Afyacall . Zitumike hadi ' . Carbon::now()->addDays(7);
                    $en = 'Congratulations! You have 5 minutes to speak with the Afyacall Doctor. To be used untill ' . Carbon::now()->addDays(7);
                    ProcessLanguage::dispatchSync($request->msisdn, $sw, $en);

                    //send notification to customer for successfully charges
                    $sw = 'Umefanikiwa kulipia Tsh 1000 kwa huduma ya Vodacom AfyaCall kuzungumza na daktari';
                    $en = 'You haveSuccess fully paid Tsh 1000 for the Vodacom AfyaCall service to talk to a doctor';
                    ProcessLanguage::dispatchSync($request->msisdn, $sw, $en);

                    $resp = [
                        'status' => '1',
                        'message' => 'success',
                        'msisdn' => $request->msisdn,
                        'amount' => $request->amount,
                    ];

                    return response()->json($resp);
                }
            } else {
                $sw = 'Hauna salio la kutosha kupata huduma hii.Ongeza salio kisha  piga 0900011111 ';
                $en = 'You have insufficient balance.Please recharge and dial 0900011111 ';
                ProcessLanguage::dispatch($request->msisdn, $sw, $en);

                $resp = [
                    'status' => '0',
                    'message' => 'Failed',
                    'msisdn' => $request->msisdn,
                    'amount' => $request->amount,
                ];

                return response()->json($resp);
            }
        }
    }

    public function chargiartimedoctoricg($msisdn, $amount)
    {
        if ($amount == 1000) {
            $product_ID = '921465_P04';
        } elseif ($amount == 2000) {
            $product_ID = '921465_P05';
        } else {
            $product_ID = '921465_P06';
        }

        $balance = intval(abs($this->checkbalance($product_ID, $msisdn)));
        if ($balance != $amount) {
            Log::info($msisdn . ' Insufficient Balance ' . $balance);

            return false;
        }

        $code = Opt::getCode();
        $customer = Customer::where('msisdn', $msisdn)->get()->first();
        $product = Product::where('product_ID', $product_ID)->get()->first();

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
                    'input_CustomerMSISDN' => $msisdn,
                    'input_Currency' => 'TZS',
                    'input_Amount' => $amount,
                    'input_ChargeType' => 'Subscription',
                    'input_ChargeChannel' => 'Synchronous',
                    'input_OriginatorConversationID' => $code,
                ]
            ]);
            $results = $response->getBody()->getContents();
            $data = json_decode($results, true);
            Log::info($data);
            //register transaction

            //check if customer found in database
            if ($customer) {
                //register successfully transaction
                $trans = new Transaction();
                $trans->customer_ID = $customer->id;
                $trans->amount_IN = $amount;
                $trans->transaction_date = Opt::getServertime();
                $trans->status = 1;
                $trans->product_id = $product->id;
                $trans->currency = 'Mpesa';
                $trans->response = $data['output_ResponseDesc'];
                $trans->conventions_ID = $data['output_ConversationID'];
                $trans->response_code = $data['output_ResponseCode'];
                $trans->save();

                return true;
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
                ]
            ]);
            $results = $response->getBody()->getContents();
            $data = json_decode($results, true);
            Log::info($data);
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

    public function chargedoctorrequestfrompbx(Request $request)
    {
        // Log all incoming requests
        Log::info($request->all());

        // Validate the incoming data
        $validator = Validator::make($request->all(), [
            'msisdn' => 'required',
            'amount' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['Validation errors' => $validator->errors()]);
        }

        $msisdn = $request->msisdn;
        $amount = $request->amount;

        $customer = Customer::where('msisdn', $msisdn)->first();

        if ($customer) {
            if ($customer->doctor_subscription_status != 1) {
                $res = $this->chargiartimedoctorsubsription($msisdn, $amount);

                if ($res) {
                    $customer->doctor_subscription_status = 1;
                    $customer->other_status += 60;
                    $customer->save();

                    //add the customer to subscribtion
                    $subscrb = Subscription::where('customer_ID', $customer->id)
                        ->where('product_id', 4)
                        ->get()->first();
                    if ($subscrb) {
                        $subscrb->customer_ID = $customer->id;
                        $subscrb->product_id = 4;
                        $subscrb->starts_at = Carbon::now();
                        $subscrb->ends_at = Carbon::now()->addDays(1);
                        $subscrb->save();
                    } else {
                        $subscribe = new Subscription();
                        $subscribe->customer_ID = $customer->id;
                        $subscribe->product_id = 4;
                        $subscribe->starts_at = Carbon::now();
                        $subscribe->ends_at = Carbon::now()->addDays(1);
                        $subscribe->save();
                    }

                    $messageSwahili = 'Umejiunga na huduma ya Kuongea na madktari wa Afyacall.Utapokea dakika moja zitazokusanywa kila siku kwa TSH 200/Siku.Kujiondoa Tuma Neno ONDOADOC Kwenda 15723.';
                    $messageEnglish = 'You have subscribed Afyacall Direct Doctors call Service.You will receive accumulative 1 min daily for TSH 200 per Day.To unsubscribe send ONDOADOC TO 15723.';
                    ProcessLanguage::dispatchSync($msisdn, $messageSwahili, $messageEnglish);

                    $response = [
                        'status' => '1',
                        'message' => 'success',
                        'seconds' => $customer->other_status,
                        'msisdn' => $msisdn,
                        'amount' => $amount,
                    ];

                    return response()->json($response);
                } else {
                    $customer->doctor_subscription_status = 0;
                    $customer->save();

                    $messageSwahili = 'Umejiunga na huduma ya Kuongea na madktari wa Afyacall.Utapokea dakika moja zitazokusanywa kila siku kwa TSH 200/Siku.Kujiondoa Tuma Neno ONDOADOC Kwenda 15723.';
                    $messageEnglish = 'You have subscribed Afyacall Direct Doctors call Service.You will receive accumulative 1 min daily for TSH 200 per Day.To unsubscribe send ONDOADOC TO 15723.';
                    ProcessLanguage::dispatchSync($msisdn, $messageSwahili, $messageEnglish);

                    $response = [
                        'status' => '0',
                        'message' => 'Failed',
                        'msisdn' => $msisdn,
                        'seconds' => $customer->other_status,
                        'amount' => $amount,
                    ];

                    return response()->json($response);
                }
            } else {
                $response = [
                    'status' => '1',
                    'message' => 'already charged',
                    'seconds' => $customer->other_status,
                    'msisdn' => $msisdn,
                    'amount' => $amount,
                ];

                return response()->json($response);
            }
        } else {
            $customer = new Customer();
            $customer->msisdn = $msisdn;
            $customer->registered_at = Opt::getServertime();
            $customer->doctor_subscription_status = 0;
            $customer->save();

            $opt = new Opt();
            $opt->customer_ID = $customer->id;
            $opt->product_ID = 4;
            $opt->opt_value = 1;
            $opt->date = Opt::getServertime();
            $opt->save();

            $res = $this->chargiartimedoctorsubsription($msisdn, $amount);

            if ($res) {
                $customer->doctor_subscription_status = 1;
                $customer->other_status += 60;
                $customer->save();

                //add the customer to subscribtion
                $subscrb = Subscription::where('customer_ID', $customer->id)
                    ->where('product_id', 4)
                    ->get()->first();
                if ($subscrb) {
                    $subscrb->customer_ID = $customer->id;
                    $subscrb->product_id = 4;
                    $subscrb->starts_at = Carbon::now();
                    $subscrb->ends_at = Carbon::now()->addDays(1);
                    $subscrb->save();
                } else {
                    $subscribe = new Subscription();
                    $subscribe->customer_ID = $customer->id;
                    $subscribe->product_id = 4;
                    $subscribe->starts_at = Carbon::now();
                    $subscribe->ends_at = Carbon::now()->addDays(1);
                    $subscribe->save();
                }

                $messageSwahili = 'Umejiunga na huduma ya Kuongea na madktari wa Afyacall.Utapokea dakika moja zitazokusanywa kila siku kwa TSH 200/Siku.Kujiondoa Tuma Neno ONDOADOC Kwenda 15723.';
                $messageEnglish = 'You have subscribed Afyacall Direct Doctors call Service.You will receive accumulative 1 min daily for TSH 200 per Day.To unsubscribe send ONDOADOC TO 15723.';
                ProcessLanguage::dispatchSync($msisdn, $messageSwahili, $messageEnglish);

                $response = [
                    'status' => '1',
                    'message' => 'success',
                    'msisdn' => $msisdn,
                    'seconds' => $customer->other_status,
                    'amount' => $amount,
                ];

                return response()->json($response);
            } else {
                $messageSwahili = 'Umejiunga na huduma ya Kuongea na madktari wa Afyacall.Utapokea dakika moja zitazokusanywa kila siku kwa TSH 200/Siku.Kujiondoa Tuma Neno ONDOADOC Kwenda 15723.';
                $messageEnglish = 'You have subscribed Afyacall Direct Doctors call Service.You will receive accumulative 1 min daily for TSH 200 per Day.To unsubscribe send ONDOADOC TO 15723.';
                ProcessLanguage::dispatchSync($msisdn, $messageSwahili, $messageEnglish);

                $response = [
                    'status' => '0',
                    'message' => 'Failed',
                    'msisdn' => $msisdn,
                    'seconds' => $customer->other_status,
                    'amount' => $amount,
                ];

                return response()->json($response);
            }
        }
    }

    public function doctorsubscriptionstatus(Request $request)
    {
        Log::info('checking doctor status');
        $validator = Validator::make($request->all(), [
            'msisdn' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['Validation errors' => $validator->errors()]);
        }

        $excustomer = Customer::where('msisdn', $request->msisdn)->first();

        if ($excustomer) {
            $resp = [
                'doctor_subscription_status' => $excustomer->doctor_subscription_status,
                'doctor_enticement' => $excustomer->doctor_enticement,
                'message' => 'success',
                'msisdn' => $request->msisdn,
                'seconds' => $excustomer->other_status,
                'starts_at' => '0000-00-00 00:00:00',
                'ends_at' => '0000-00-00 00:00:00',
            ];

            Log::info($resp);

            return response()->json($resp);
        }

        return response()->json([
            'doctor_subscription_status' => '-1',
            'doctor_enticement' => '0',
            'message' => 'customer not found',
            'msisdn' => $request->msisdn,
            'seconds' => null,
            'starts_at' => '0000-00-00 00:00:00',
            'ends_at' => '0000-00-00 00:00:00',
        ]);
    }

    public function removeseconds(Request $request)
    {
        Log::info($request);

        $validator = Validator::make($request->all(), [
            'msisdn' => 'required',
            'seconds' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['Validation errors' => $validator->errors()]);
        }

        $excustomer = Customer::where('msisdn', $request->msisdn)->first();

        if ($excustomer) {
            if ($excustomer->other_status >= $request->seconds) {
                $excustomer->other_status -= $request->seconds;
                $excustomer->save();

                $resp = [
                    'status' => 1,
                    'message' => 'success',
                    'msisdn' => $request->msisdn,
                    'seconds' => $excustomer->other_status,
                ];

                return response()->json($resp);
            } else {
                $resp = [
                    'status' => 0,
                    'message' => 'failed',
                    'msisdn' => $request->msisdn,
                    'seconds' => $excustomer->other_status,
                ];

                return response()->json($resp);
            }
        } else {
            $resp = [
                'status' => 0,
                'message' => 'customer not found',
                'msisdn' => $request->msisdn,
                'seconds' => 0,
            ];

            return response()->json($resp);
        }
    }

    public function chargiartimedoctorsubsription($msisdn, $amount)
    {
        //update the payload
        $payload = [
            'type' => 'charge',
            'id' => [
                [
                    'value' => $msisdn,
                    'schemeName' => 'msisdn'
                ]
            ],
            'details' => [
                'adjustmentAmount' => $amount . '00'
            ],
            'name' => 'MW',
            'desc' => 'Afyacall Doctor Charges',
            'category' => [
                [
                    'value' => 'MW',
                    'listHierarchyId' => 'eventClass'
                ]
            ]
        ];

        //time for charging
        $chargetime = Opt::getTimestamp();
        $uuid = Opt::generateUUIDv1();
        $customer = Customer::where('msisdn', $msisdn)->get()->first();

        //try charging
        try {
            $client = new \GuzzleHttp\Client();
            $credentials = base64_encode('svc_afyacall:wHroRA3U03_el701');
            $response = $client->post('https://197.250.9.149:6202/middlewarev2/serviceAccountAdjustment', [
                'verify' => false,
                'headers' => [
                    'Authorization' => 'Basic ' . $credentials,
                    'Content-Type' => 'application/json',
                    'X-MessageId' => 'uuid: ' . $uuid,
                    'X-Source-Timestamp' => $chargetime,
                ],
                'json' => $payload,
                'timeout' => 30,
                'connect_timeout' => 10,
            ]);
            $results = $response->getBody()->getContents();
            //convert into json
            $data = json_decode($results, true);
            Log::info($data);

            //check if customer found in database
            if ($customer) {
                //register transaction
                $trans = new Transaction();
                $trans->customer_ID = $customer->id;
                $trans->amount_IN = $amount;
                $trans->product_id = 4;
                $trans->transaction_date = Opt::getServertime();
                $trans->status = 1;
                $trans->currency = 'Airtime';
                $trans->response = 'Process service request successfully.';
                $trans->save();

                return true;
            }
        } catch (\Throwable $th) {
            //register transaction
            $trans = new Transaction();
            $trans->customer_ID = $customer->id;
            $trans->amount_IN = $amount;
            $trans->product_id = 4;
            $trans->transaction_date = Opt::getServertime();
            $trans->status = 0;
            $trans->currency = 'Airtime';
            $trans->response = 'Insufficient Balance';
            $trans->save();

            Log::error('error on charging airtime on ivr or unsufficient balance ' . $msisdn);
            Log::error($th->getMessage());

            return false;
        }
    }
}
