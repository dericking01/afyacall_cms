<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Jobs\WebsiteEnticement;
use App\Models\Blacklist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Models\Opt;
use App\Models\Customer;
use App\Models\Product;

class WebsiteApiController extends Controller
{

    public function websiteEnticement(Request $request)
    {

        $validator = Validator::make(
            $request->all(),
            [
                'msisdn' => ['required'],
                'amount' => ['required'],
                'product' => ['required'],
            ]
        );

        if ($validator->fails()) {
            return response()->json(['Validation errors' => $validator->errors()]);
        }
        $product = Product::where('product_ID', $request->product)->get()->first();
        if ($product) {
            $excustomer = Customer::where('msisdn', $request->msisdn)->get()->first();
            if ($excustomer) {

                if ($excustomer->enticement == 0) {
                    # code...
                    $data = $this->pushenticement($request->msisdn, $product->product_ID);

                    if ($data['output_ResponseCode'] == 0) {

                        $resp = array(
                            "status" => "success",
                            "message" => $data['output_ResponseDesc'],
                            "msisdn" => $request->msisdn,
                        );
                        return response()->json($resp);
                    } else {

                        $resp = array(
                            "status" => "failed",
                            "message" => $data['output_ResponseDesc'],
                            "msisdn" => $request->msisdn,
                        );
                        return response()->json($resp);
                    }
                } else {

                    $resp = array(
                        "status" => "failed",
                        "message" => "Arleady subscribed to this service",
                        "msisdn" => $request->msisdn,
                    );
                    return response()->json($resp);
                }
            } else {
                //create new customer
                $customer = new Customer();
                $customer->msisdn = $request->msisdn;
                $customer->registered_at = Opt::getServertime();
                $customer->status = 0;
                $customer->source = "Website";
                $customer->save();

                //update the values
                $opt = new Opt();
                $opt->customer_ID = $customer->id;
                $opt->product_ID = $product->id;
                $opt->opt_value = 1;
                $opt->date = Opt::getServertime();
                $opt->save();

                $data = $this->pushenticement($request->msisdn, $product->product_ID);

                if ($data['output_ResponseCode'] == 0) {

                    $resp = array(
                        "status" => "success",
                        "message" => $data['output_ResponseDesc'],
                        "msisdn" => $request->msisdn,
                    );
                    return response()->json($resp);
                } else {
                    $resp = array(
                        "status" => "failed",
                        "message" => $data['output_ResponseDesc'],
                        "msisdn" => $request->msisdn,
                    );
                    return response()->json($resp);
                }

            }

        } else {
            $resp = array(
                "status" => "99",
                "message" => "is on the blacklist or product is not defined",
                "msisdn" => $request->msisdn,
            );
            return response()->json($resp);
        }
    }


    public function pushenticement($phone, $productID)
    {
        //push enticement 
        $code = Opt::getCode();
        try {
            $client = new \GuzzleHttp\Client();
            $response = $client->request('POST', 'https://197.250.9.191:23000/icg/Enticement/', [
                'verify' => false,
                'headers' => [
                    'Content-Type' => ' application/json',
                ],
                'json' => [
                    'input_Username' => '921465',
                    'input_Password' => '5pmls4V!9]O]{IF',
                    'input_WASPShortcode' => '921465',
                    'input_ProductID' => $productID,
                    'input_CustomerMSISDN' => $phone,
                    'input_OriginatorConversationID' => $code,
                    'input_EnticementChannel' => 'USSDPush'
                ]
            ]);

            $results = $response->getBody()->getContents();
            $data = json_decode($results, true);

            return $data;
        } catch (\Throwable $th) {
            Log::error("There is an error on enticement " . $phone);
            return true;
        }
    }
}
