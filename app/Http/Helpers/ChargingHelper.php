<?php


namespace App\Http\Helpers;

use App\Jobs\ProcessLanguage;
use App\Models\Content;
use App\Models\ContentType;
use App\Models\Transaction;
use App\Models\Customer;
use App\Models\Log;
use App\Models\Opt;
use App\Models\Product;
use App\Models\Subscription;
use Carbon\Carbon;

class ChargingHelper
{
    function __construct()
    {
    }

    public function charging($cellNo, $amount, $service, $category)
    {
        try {
            return $this->chargeviaairtime($cellNo, $amount, $service, $category);
        } catch (\Throwable $th) {
            //throw $th;
        }
    }


    public function airtimecharging($cellNo, $amount, $service, $category)
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
                'adjustmentAmount' => $amount
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

        //time for charging
        $chargetime = Opt::getTimestamp();
        $customer = Customer::where('msisdn', $cellNo)->get()->first();
        //try charging
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
                $customer->keyword = $service;
                $customer->content = $category;
                $customer->updated_at = Carbon::now();
                $customer->save();

                //register transaction
                $trans = new Transaction();
                $trans->customer_ID = $customer->id;
                $trans->amount_IN = $amount / 100;
                $trans->transaction_date = Opt::getServertime();
                $trans->status = 1;
                $trans->currency = "Airtime";
                $trans->response = 'Process service request successfully.';
                $trans->save();

                $sw = 'Karibu katika huduma ya Vodacom Afyacall.Utapata dondoo mbalimbali kuhusu afya yako kila siku kwa Gharama ya Tsh 300 tu. Kujitoa tuma neno ONDOAIVR kwenda 15723';
                $en = 'Welcome on Vodacom Afyacall Service. You will receive various tips about health daily at a cost of TZS 300/day.To unsubscribe send the word ONDOAIVR to 15723';
                ProcessLanguage::dispatchSync($cellNo, $sw, $en);
            } else {
                # customer not found in the database but has successfully charged.
                # save the customer to database
                $newcustomer = new Customer();
                $newcustomer->msisdn = $cellNo;
                $newcustomer->keyword = $service;
                $newcustomer->registered_at = Opt::getServertime();
                $newcustomer->ivr_status = 1;
                $newcustomer->save();

                #update the opt in value as 1
                $opt = new Opt();
                $opt->customer_ID = $newcustomer->id;
                $opt->opt_value = 1;
                $opt->date = Opt::getServertime();
                $opt->save();

                #save the transaction 
                $transaction = new Transaction();
                $transaction->customer_ID = $newcustomer->id;
                $transaction->amount_IN = $amount / 100;
                $transaction->response = 'Process service request successfully.';
                $transaction->transaction_date = Opt::getServertime();
                $transaction->status = 1;
                $transaction->currency = "Airtime";
                $transaction->save();
            }
        } catch (\Throwable $th) {

            // $this->chargeviaampesa($cellNo, $amount,$service, $category);
            #save the transaction 
            $sw = 'Hauna salio la kutosha kupata huduma hii.Ongeza salio kisha Tuma neno AFYASMS kwenda 15723 kwa gharama ya Tsh 150/siku. Kujitoa tuma neno ONDOASMS kwenda 15723';
            $en = 'You have insufficient balance.Please recharge and send keyword AFYASMS to 15723 at a cost of Tzs 150 per Tip. To unsubscribe send the word ONDOASMS to 15723';
            ProcessLanguage::dispatchSync($cellNo, $sw, $en);
        }
    }


    private function chargeviaairtime($cellNo, $amount, $service, $category)
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
                'adjustmentAmount' => $amount
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

        //time for charging
        $chargetime = Opt::getTimestamp();
        $customer = Customer::where('msisdn', $cellNo)->get()->first();
        $product = Product::where('product_ID', '921465_P02')->get()->first();

        //try charging
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

            $amountcharged = $data['parts']['serviceBalance'][0]['adjustmentAmount'];
            $amountremain = $data['parts']['serviceBalance'][0]['balanceAmount'];
            $accountname = $data['parts']['serviceBalance'][0]['name'];

            //check if customer found in database
            if ($customer) {
                //update customer status
                $customer->status = 1;
                $customer->keyword = $service;
                $customer->content = $category;
                $customer->save();


                //update the values
                $opt = new Opt();
                $opt->customer_ID = $customer->id;
                $opt->ConversationID = $service;
                $opt->OriginatorConversationID = $category ?? '';
                $opt->product_ID = $product->id;
                $opt->opt_value = 1;
                $opt->date = Opt::getServertime();
                $opt->save();

                $subscrb = Subscription::where('customer_ID', $customer->id)
                ->where('product_id', $product->product_ID)
                ->get()->first();
                if ($subscrb) {
                    $subscrb->customer_ID = $customer->id;
                    $subscrb->product_id = $product->id;
                    $subscrb->starts_at = Carbon::now();
                    $subscrb->ends_at = Carbon::now()->addDays(1);
                    $subscrb->save();
                } else {
                    $subscribe = new Subscription();
                    $subscribe->customer_ID = $customer->id;
                    $subscribe->product_id = $product->id;
                    $subscribe->starts_at = Carbon::now();
                    $subscribe->ends_at = Carbon::now()->addDays(1);
                    $subscribe->save();
                }
                
                //register transaction
                $trans = new Transaction();
                $trans->customer_ID = $customer->id;
                $trans->amount_IN = $amount / 100;
                $trans->transaction_date = Opt::getServertime();
                $trans->status = 1;
                $trans->product_id = $product->id;
                $trans->currency = "Airtime";
                $trans->response = 'Process service request successfully.';
                $trans->save();


                $sw = 'Karibu katika huduma ya Vodacom Afyacall.Utapata dondoo mbalimbali kuhusu afya yako kila siku kwa Gharama ya Tsh 150 tu. Kujitoa tuma neno ONDOASMS kwenda 15723';
                $en = 'Welcome on Vodacom Afyacall Service. You will receive various tips about health daily at a cost of TZS 150/day.To unsubscribe send the word ONDOASMS to 15723';
                ProcessLanguage::dispatchSync($cellNo, $sw, $en);
                $this->sendthefirstmessage($cellNo, $category);
            }
        } catch (\Throwable $th) {

            // $this->chargeviaampesa($cellNo, $amount,$service, $category);
            #save the transaction 
            $sw = 'Hauna salio la kutosha kupata huduma hii.Ongeza salio kisha Tuma neno AFYASMS kwenda 15723 kwa gharama ya Tsh 150/siku. Kujitoa tuma neno ONDOASMS kwenda 15723';
            $en = 'You have insufficient balance.Please recharge and send keyword AFYASMS to 15723 at a cost of Tzs 150 per Tip. To unsubscribe send the word ONDOASMS to 15723';
            ProcessLanguage::dispatchSync($cellNo, $sw, $en);

            #save the transaction 
            $transaction = new Transaction();
            $transaction->customer_ID = $customer->id;
            $transaction->amount_IN = $amount / 100;
            $transaction->product_id = $product->id;
            $transaction->response = 'Insufficient Balance';
            $transaction->transaction_date = Opt::getServertime();
            $transaction->status = 0;
            $transaction->currency = "Airtime";
            $transaction->save();
        }
    }


    public function sendthefirstmessage($number, $message_content_key)
    {
        $last_sms_send = $this->last_message_sent($number);
        if ($last_sms_send) {
            $next_sms_send = $this->is_message_sent($last_sms_send, $message_content_key);
            if ($next_sms_send) {
                $smsHelper = new SmsHelper();
                $smsHelper->sendSms($number, $next_sms_send);
            }
        }
    }

    public function last_message_sent($number)
    {

        $lastmessage = Log::where('last_sent', $number)->get()->pluck('content_id');
        if (!$lastmessage->isEmpty()) {
            return $lastmessage->toArray();
        } else {
            return array(0);
        }
    }

    public function is_message_sent($message_sent_ids, $message_content_key)
    {
        $messagetype = ContentType::where('name', $message_content_key)->first();
        if ($messagetype) {
            $allmessage = Content::where('content_type', $messagetype['id'])->get();
            foreach ($allmessage as  $value) {
                if (!in_array($value->id, $message_sent_ids)) {
                    return  $value->id;
                }
            }
        } else {
            //without message keyword
            $allmessage = Content::all();
            foreach ($allmessage as  $value) {
                if (!in_array($value->id, $message_sent_ids)) {
                    return  $value->id;
                }
            }
        }
    }
}

