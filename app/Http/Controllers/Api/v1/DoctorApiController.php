<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessLanguage;
use App\Models\Customer;
use App\Models\Opt;
use App\Models\Product;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class DoctorApiController extends Controller
{

    public function chargeDoctorAirtime(Request $request)
    {
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

                $res = $this->chargiartimedoctor($request->msisdn, $request->amount);
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

                        $resp = array(
                            "status" => "1",
                            "message" => "success",
                            "msisdn" => $request->msisdn,
                            "amount" => $request->amount,
                        );
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

                        $resp = array(
                            "status" => "1",
                            "message" => "success",
                            "msisdn" => $request->msisdn,
                            "amount" => $request->amount,
                        );
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

                        $resp = array(
                            "status" => "1",
                            "message" => "success",
                            "msisdn" => $request->msisdn,
                            "amount" => $request->amount,
                        );
                        return response()->json($resp);
                    }
                } else {
                    $sw = 'Hauna salio la kutosha kupata huduma hii.Ongeza salio kisha  piga 0900011111';
                    $en = 'You have insufficient balance.Please recharge and dial 0900011111';
                    ProcessLanguage::dispatchSync($request->msisdn, $sw, $en);

                    $resp = array(
                        "status" => "0",
                        "message" => "Failed",
                        "msisdn" => $request->msisdn,
                        "amount" => $request->amount,
                    );
                    return response()->json($resp);
                }
            } else {
                //customer has charged arleady return true | 1
                $excustomer->doctor_status = 0;
                $excustomer->save();

                $resp = array(
                    "status" => "1",
                    "message" => "success",
                    "msisdn" => $request->msisdn,
                );
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
            $opt->product_ID = 3;
            $opt->opt_value = 1;
            $opt->date = Opt::getServertime();
            $opt->save();

            $res = $this->chargiartimedoctor($request->msisdn, $request->amount);
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

                    $resp = array(
                        "status" => "1",
                        "message" => "success",
                        "msisdn" => $request->msisdn,
                        "amount" => $request->amount,
                    );
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

                    $resp = array(
                        "status" => "1",
                        "message" => "success",
                        "msisdn" => $request->msisdn,
                        "amount" => $request->amount,
                    );
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

                    $resp = array(
                        "status" => "1",
                        "message" => "success",
                        "msisdn" => $request->msisdn,
                        "amount" => $request->amount,
                    );
                    return response()->json($resp);
                }
            } else {
                $sw = 'Hauna salio la kutosha kupata huduma hii.Ongeza salio kisha  piga 0900011111 ';
                $en = 'You have insufficient balance.Please recharge and dial 0900011111 ';
                ProcessLanguage::dispatch($request->msisdn, $sw, $en);

                $resp = array(
                    "status" => "0",
                    "message" => "Failed",
                    "msisdn" => $request->msisdn,
                    "amount" => $request->amount,
                );
                return response()->json($resp);
            }
        }
    }

    public function chargiartimedoctor($cellNo, $amount)
    {
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
                'adjustmentAmount' => $amount . '00'
            ],
            'name' => 'MW',
            'desc' => 'Afyacall Doctor Charges',
            'category' => [
                array(
                    'value' => 'MW',
                    'listHierarchyId' => 'eventClass'
                )
            ]
        ];


        //time for charging
        $chargetime = Opt::getServertime();
        $customer = Customer::where('msisdn', $cellNo)->get()->first();
        //try charging
        try {
            $client = new \GuzzleHttp\Client;
            $credentials = base64_encode('svc_afyacall:gCt5mos5QAJtcqN5');
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
            Log::info($data);

            //check if customer found in database
            if ($customer) {
                //update customer status
                $customer->doctor_status = 1;
                $customer->updated_at = Carbon::now();
                $customer->save();

                //register transaction
                $trans = new Transaction();
                $trans->customer_ID = $customer->id;
                $trans->amount_IN = $amount;
                $trans->product_id = 3;
                $trans->transaction_date = Opt::getServertime();
                $trans->status = 1;
                $trans->currency = "Airtime";
                $trans->response = 'Process service request successfully.';
                $trans->save();

                return true;
            }
        } catch (\Throwable $th) {
            //register transaction
            $trans = new Transaction();
            $trans->customer_ID = $customer->id;
            $trans->amount_IN = $amount;
            $trans->product_id = 3;
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
}

