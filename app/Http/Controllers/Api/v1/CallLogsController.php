<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessLanguage;
use App\Models\Customer;
use App\Models\DoctorStastic;
use App\Models\IvrStatistic;
use App\Models\Opt;
use App\Models\Product;
use App\Models\Subscription;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class CallLogsController extends Controller
{

    public function ivrsavestatistics(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'Caller_Number' => ['required'],
                'calldate' => ['required'],
                'status' => ['required'],
            ]
        );

        if ($validator->fails()) {
            return response()->json(['Validation errors' => $validator->errors()]);
        }

        $customer = Customer::where("msisdn", $request->Caller_Number)->get()->first();
        if ($customer) {
            $statics = new IvrStatistic();
            $statics->customer_id = $customer->id;
            $statics->calldate = $request->calldate;
            $statics->selectedAudio = $request->selectedAudio;
            $statics->duration = $request->duration;
            $statics->status = $request->status;
            $statics->price = $request->price;
            $statics->paymentMode = $request->paymentMode;
            $statics->expiredate = $request->expiredate;
            $statics->save();

            $resp = array(
                "status" => "1",
                "message" => 'successful',
                "msisdn" => $request->Caller_Number,
            );
            return response()->json($resp);
        } else {
            $resp = array(
                "status" => "0",
                "message" => 'customer not found',
                "msisdn" => $request->Caller_Number,
            );
            return response()->json($resp);
        }
    }

    public function language(Request $request)
    {
        $payload = [
            'id'   => [
                array(
                    'value' => $request->number,
                    'schemeName' => 'msisdn'
                )
            ],
            'parts' => array(
                'partyPreference' => [
                    array(
                        'name' => "languageId",
                    )
                ]
            )
        ];

        //time 
        $chargetime = Opt::getServertime();
        try {
            $client = new \GuzzleHttp\Client;
            $credentials = base64_encode('svc_afyacall:gCt5mos5QAJtcqN5');
            $response = $client->get('https://197.250.9.149:6202/middlewarev2/CustomerPrivacyProfile', [
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

            return $data;
            $langu = $data['parts']['partyPreference'][0]['value'];
            return $langu;
        } catch (\Throwable $th) {
            //if there is any error send sms by swahili language
            Log::error($th->getMessage());
        }
    }

    public function livecalldoctorstastics(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'caller_number' => ['required'],
                'call_date' => ['required'],
            ]
        );

        if ($validator->fails()) {
            return response()->json(['Validation errors' => $validator->errors()]);
        }

        $customer = Customer::where("msisdn", $request->caller_number)->get()->first();
        if ($customer) {
            $statics = new DoctorStastic();
            $statics->customer_id = $customer->id;
            $statics->call_date = $request->call_date;
            $statics->call_duration = $request->call_duration;
            $statics->duration = $request->duration;
            $statics->call_talktime = $request->call_talktime;
            $statics->status = $request->status;
            $statics->channel = $request->channel;
            $statics->call_disconnection = $request->call_disconnection;
            $statics->save();

            $resp = array(
                "status" => "1",
                "message" => 'successful',
                "msisdn" => $request->caller_number,
            );
            return response()->json($resp);
        } else {
            $resp = array(
                "status" => "0",
                "message" => 'customer not found',
                "msisdn" => $request->caller_number,
            );
            return response()->json($resp);
        }
    }

    public function unsubscribeafterlisten(Request $request)
    {
        Log::info($request);
        //check if the user exists in a system
        $excustomer = Customer::where('msisdn', $request->msisdn)->get()->first();
        if ($excustomer->ivr_status != -1) {
            //check if the user has enticement 
            if ($excustomer->ivr_enticement == 1) {
                $code = Opt::getCode();
                try {
                    $client = new \GuzzleHttp\Client();
                    $response = $client->request('POST', 'https://197.250.9.191:23000/icg/unsub/', [
                        'verify' => false,
                        'headers' => [
                            'Content-Type' => ' application/json',
                        ],
                        'json' => [
                            'input_ProductID' => '921465_P01',
                            'input_RequestType' => 'Opt-Out',
                            'input_Username' => '921465',
                            'input_Password' => 'JrF8#u73%&ev',
                            'input_WASPShortcode' => '921465',
                            'input_CustomerMSISDN' => $request->msisdn,
                            'input_OriginatorConversationID' => $code,
                        ]
                    ]);
                    $results = $response->getBody()->getContents();
                    $data = json_decode($results, true);
                    Log::info($data);

                    if ($data['output_ResponseCode'] == 0) {

                        $excustomer->ivr_status = -1;
                        $excustomer->ivr_enticement = 0;
                        $excustomer->updated_at = Carbon::now();
                        $excustomer->save();

                        //update the values
                        $opt = new Opt();
                        $opt->customer_ID = $excustomer->id;
                        $opt->ConversationID = "Removed after listening IVR";
                        $opt->product_ID = 1;
                        $opt->opt_value = -1;
                        $opt->date = Opt::getServertime();
                        $opt->save();


                        $subscribeid = Subscription::where('customer_ID', $excustomer->id)->first();
                        if ($subscribeid) {
                            $subscribeid->delete();
                        }

                        $sw = 'Umefanyikiwa kujiondoa kwenye  huduma ya AFYACALL IVR. Ili kujiunga tena, piga 0900011111, chagua 2 kutoka menyu kuu. Gharama za huduma ni Tzs300/siku. Asante.';
                        $en = 'You have successfully unsubscribed from AFYACALL IVR service. To rejoin, dial 0900011111, select 2 from main menu. Service cost Tzs300/day. Karibu Afyacall';
                        ProcessLanguage::dispatchSync($request->msisdn, $sw, $en);

                        $resp = array(
                            "status" => "1",
                            "message" => "success",
                            "msisdn" => $request->msisdn,
                        );
                        return response()->json($resp);
                    }

                    $resp = array(
                        "status" => "0",
                        "message" => "failed",
                        "msisdn" => $request->msisdn,
                    );
                    return response()->json($resp);
                } catch (\Throwable $th) {
                    Log::error("there is an error on unsub from icg");
                    Log::error($th->getMessage());
                    $resp = array(
                        "status" => "0",
                        "message" => "failed",
                        "msisdn" => $request->msisdn,
                    );
                    return response()->json($resp);
                }
            } else {
                //update customer info
                $excustomer->ivr_status = -1;
                $excustomer->ivr_enticement = 0;
                $excustomer->updated_at = Carbon::now();
                $excustomer->save();

                //update the values
                $opt = new Opt();
                $opt->customer_ID = $excustomer->id;
                $opt->ConversationID = "Removed after listening IVR";
                $opt->product_ID = 1;
                $opt->opt_value = -1;
                $opt->date = Opt::getServertime();
                $opt->save();


                $subscribeid = Subscription::where('customer_ID', $excustomer->id)->first();
                if ($subscribeid) {
                    $subscribeid->delete();
                }

                $sw = 'Umefanyikiwa kujiondoa kwenye  huduma ya AFYACALL IVR. Ili kujiunga tena, piga 0900011111, chagua 2 kutoka menyu kuu. Gharama za huduma ni Tzs300/siku. Asante.';
                $en = 'You have successfully unsubscribed from AFYACALL IVR service. To rejoin, dial 0900011111, select 2 from main menu. Service cost Tzs300/day. Karibu Afyacall';
                ProcessLanguage::dispatchSync($request->msisdn, $sw, $en);

                $resp = array(
                    "status" => "1",
                    "message" => "success",
                    "msisdn" => $request->msisdn,
                );
                return response()->json($resp);
            }
        } else {
            $resp = array(
                "status" => "0",
                "message" => "Customer Arleady Unsubscribed",
                "msisdn" => $request->msisdn,
            );
            return response()->json($resp);
        }
    }
}

