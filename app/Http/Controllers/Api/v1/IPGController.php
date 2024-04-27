<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Helpers\IPGCharging;
use App\Jobs\ProcessLanguage;
use App\Models\Blacklist;
use App\Models\Customer;
use App\Models\Opt;
use App\Models\Product;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use SimpleXMLElement;

class IPGController extends Controller
{
    public function callbacksrequests(Request $request)
    {
        Log::info("======================updated request=========================");
        $xml_data = $request->getContent();
        Log::info($xml_data);
        $response = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $xml_data);
        $xml = new SimpleXMLElement($response);
        $body = $xml->xpath('//Request')[0];
        $data = json_encode($body);
        $jdatason = json_decode($data, true);



        $insightReference = $jdatason['dataItem'][12]['value'];
        $conversationID = $jdatason['dataItem'][5]['value'];
        $thirdPartyReference = $jdatason['dataItem'][11]['value'];
        $amountcharged = $jdatason['dataItem'][9]['value'];

        $ResultCode = $jdatason['dataItem'][1]['value'];


        $msisdn = substr($thirdPartyReference, -12);


        //changes on trancation
        if ($ResultCode == '0') {

            DB::table('customers')
            ->where('msisdn', $msisdn)
            ->update(['doctor_status' => 1]);

            if ($amountcharged == 3000) {
                //send notification to customer for successfully charges
                $sw = 'Hongera! Umepata dakika 15 za kuongea na Daktari wa Afyacall . Zitumike hadi ' . Carbon::now()->addDays(7);
                $en = 'Congratulations! You have 15 minutes to speak with the Afyacall Doctor. To be used untill ' . Carbon::now()->addDays(7);
                ProcessLanguage::dispatchSync($msisdn, $sw, $en);

                //send notification to customer for successfully charges
                $sw = 'Umefanikiwa kulipia Tsh 3000 kwa huduma ya Vodacom AfyaCall kuzungumza na daktari';
                $en = 'You have Success fully paid Tsh 3000 for the Vodacom AfyaCall service to talk to a doctor';
                ProcessLanguage::dispatchSync($msisdn, $sw, $en);
            } elseif ($amountcharged == 2000) {
                //send notification to customer for successfully charges
                $sw = 'Hongera! Umepata dakika 10 za kuongea na Daktari wa Afyacall . Zitumike hadi ' . Carbon::now()->addDays(7);
                $en = 'Congratulations! You have 10 minutes to speak with the Afyacall Doctor. To be used untill ' . Carbon::now()->addDays(7);
                ProcessLanguage::dispatchSync($msisdn, $sw, $en);

                //send notification to customer for successfully charges
                $sw = 'Umefanikiwa kulipia Tsh 2000 kwa huduma ya Vodacom AfyaCall kuzungumza na daktari';
                $en = 'You have Success fully paid Tsh 2000 for the Vodacom AfyaCall service to talk to a doctor';
                ProcessLanguage::dispatchSync($msisdn, $sw, $en);
            } else {
                //send notification to customer for successfully charges
                $sw = 'Hongera! Umepata dakika 5 za kuongea na Daktari wa Afyacall . Zitumike hadi ' . Carbon::now()->addDays(7);
                $en = 'Congratulations! You have 5 minutes to speak with the Afyacall Doctor. To be used untill ' . Carbon::now()->addDays(7);
                ProcessLanguage::dispatchSync($msisdn, $sw, $en);

                //send notification to customer for successfully charges
                $sw = 'Umefanikiwa kulipia Tsh 1000 kwa huduma ya Vodacom AfyaCall kuzungumza na daktari';
                $en = 'You have Success fully paid Tsh 1000 for the Vodacom AfyaCall service to talk to a doctor';
		        ProcessLanguage::dispatchSync($msisdn, $sw, $en);

                 }

            DB::table('transactions')
                ->where('conventions_ID', $thirdPartyReference)
                ->where('currency', 'Mpesa')
		->update(['status' => 1]);


        } else {

            $sw = 'Hauna salio la kutosha, tafadhali ongeza salio na upige tena 0900011111';
            $en = 'You have insufficient balance.Please recharge and dial 0900011111';
            ProcessLanguage::dispatchSync($msisdn, $sw, $en);
        }
    }


    public function transactionRequest(Request $request)
    {

        //log the incoming data
        Log::info($request->all());

        //validate the incomin data
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

        //get the current timestamp
        $current_timestamp = Carbon::now()->timestamp;
        Log::info($current_timestamp);

        //check if the product exists in the system
        $product = Product::where('product_ID', '921465_P03')->get()->first();
        if ($product) {
            $reference = Opt::getCode();
            $loginrequest = new IPGCharging;
            $timedate = Carbon::now()->timestamp;
            $transactionpayload = '
            <soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:soap="http://www.4cgroup.co.za/soapauth" xmlns:gen="http://www.4cgroup.co.za/genericsoap">
               <soapenv:Header>
                  <soap:Token>' . $loginrequest->loginRequest() . '</soap:Token>
                  <soap:EventID>40009</soap:EventID>
               </soapenv:Header>
               <soapenv:Body>
                  <gen:getGenericResult>
                     <Request>
            <dataItem>
                <name>CustomerMSISDN</name>
                <type>String</type>
                <value>' . $request->msisdn . '</value>
            </dataItem>
            <dataItem>
                <name>BusinessName</name>
                <type>String</type>
                <value>Afyacall</value>
            </dataItem>
            <dataItem>
                <name>BusinessNumber</name>
                <type>String</type>
                <value>921465</value>
            </dataItem>
            <dataItem>
                <name>Currency</name>
                <type>String</type>
                <value>Tsh</value>
            </dataItem>
            <dataItem>
                <name>Date</name>
                <type>String</type>
                <value>' . $timedate . '</value>
            </dataItem>
            <dataItem>
                <name>Amount</name>
                <type>String</type>
                <value>' . $request->amount . '</value>
            </dataItem>
            <dataItem>
                <name>ThirdPartyReference</name>
                <type>String</type>
                <value>' . $current_timestamp . $request->msisdn . '</value>
            </dataItem>
            <dataItem>
                <name>Command</name>
                <type>String</type>
                <value>customerPayBill</value>
            </dataItem>
            <dataItem>
                <name>CallBackChannel</name>
                <type>String</type>
                <value>1</value></dataItem>
            <dataItem>
                <name>CallbackDestination</name>
                <type>String</type>
                <value>http://192.168.1.10/api/afyacall/TransactionListener2</value>
            </dataItem>
            <dataItem>
                <name>Username</name>
                <type>String</type>
                <value>921465</value>
             </dataItem>
            </Request>
             </gen:getGenericResult>
               </soapenv:Body>
            </soapenv:Envelope>
            ';

            Log::info($transactionpayload);
            try {
                $client = new \GuzzleHttp\Client;
                $response = $client->post('https://41.217.203.61:30010/iPG/b2c/ussd_push?wsdl', [
                    'verify' => false,
                    'headers' => [
                        'Content-Type' => 'text/xml',
                        'accept" => "*/*',
                    ],
                    'body' => $transactionpayload
                ]);
                $result = $response->getBody()->getContents();

                Log::info($result);

                $response = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $result);
                $xml = new SimpleXMLElement($response);
                $body = $xml->xpath('//SOAPAPIResult')[0];
                $data = json_encode($body);
                $jdatason = json_decode($data, true);
                Log::info($jdatason);
                $customer = Customer::where('msisdn', $request->msisdn)->get()->first();

                if ($customer) {
                    //register transaction
                    $trans = new Transaction();
                    $trans->customer_ID = $customer->id;
                    $trans->amount_IN = $request->amount;
                    $trans->transaction_date = Opt::getServertime();
                    $trans->status = 0;
                    $trans->product_id = $product->id;
                    $trans->currency = "Mpesa";
                    $trans->response = $jdatason['response']['dataItem'][1]['value'];
                    $trans->conventions_ID = $jdatason['response']['dataItem'][0]['value'];
                    $trans->response_code = $jdatason['response']['dataItem'][2]['value'];
                    $trans->save();
                } else {
                    //register a customer in a database first
                    $newcustomer = new Customer();
                    $newcustomer->msisdn = $request->msisdn;
                    $newcustomer->registered_at = Opt::getServertime();
                    $newcustomer->doctor_status = 0;
                    $newcustomer->save();


                    $opt = new Opt();
                    $opt->customer_ID = $newcustomer->id;
                    $opt->opt_value = 1;
                    $opt->product_ID = $product->id;
                    $opt->ConversationID = "Join from Direct Calling";
                    $opt->date = Opt::getServertime();
                    $opt->save();

                    //register transaction
                    $trans = new Transaction();
                    $trans->customer_ID = $newcustomer->id;
                    $trans->amount_IN = $request->amount;
                    $trans->transaction_date = Opt::getServertime();
                    $trans->status = 0;
                    $trans->product_id = $product->id;
                    $trans->currency = "Mpesa";
                    $trans->response = $jdatason['response']['dataItem'][1]['value'];
                    $trans->conventions_ID = $jdatason['response']['dataItem'][0]['value'];
                    $trans->response_code = $jdatason['response']['dataItem'][2]['value'];
                    $trans->save();
                }

                $resp = array(
                    "status" => "89",
                    "message" => "waiting for transaction to complete",
                    "msisdn" => $request->msisdn,
                );
                return response()->json($resp);
            } catch (\Throwable $th) {

                $resp = array(
                    "status" => "0",
                    "message" => $th->getMessage(),
                    "msisdn" => $request->msisdn,
                );
                return response()->json($resp);
            }
        } else {
            $resp = array(
                "status" => "0",
                "message" => "Product not configured",
                "msisdn" => $request->msisdn,
            );
            return response()->json($resp);
        }
    }


    public function subscriptiondoctorstatus(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'msisdn' => ['required'],
            ]
        );

        if ($validator->fails()) {
            return response()->json(['Validation errors' => $validator->errors()]);
        }


        $excustomer = Customer::where('msisdn', $request->msisdn)->get()->first();
        if ($excustomer) {
            if ($excustomer->doctor_status == 1) {
                $excustomer->doctor_status = 0;
                $excustomer->save();

                $resp = array(
                    "doctor_status" => "1",
                    "message" => "success",
                    "msisdn" => $request->msisdn,
                );
                return response()->json($resp);
            } else {
                $resp = array(
                    "doctor_status" => "0",
                    "message" => "Failed",
                    "msisdn" => $request->msisdn,
                );
                return response()->json($resp);
            }
        } else {
            $resp = array(
                "doctor_status" => '99',
                "message" => 'customer not found',
                "msisdn" => $request->msisdn,
            );
            return response()->json($resp);
        }
    }
}
