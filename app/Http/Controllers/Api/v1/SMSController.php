<?php

namespace App\Http\Controllers\Api\v1;

use App\Jobs\AirtimeCharging;
use App\Http\Controllers\Controller;
use App\Http\Helpers\SmsHelper;
use App\Jobs\CustomerUpdates;
use App\Jobs\ProcessCharging;
use App\Jobs\ProcessLanguage;
use App\Jobs\TransactionMpesaUpdates;
use App\Models\Blacklist;
use App\Models\Content;
use App\Models\ContentType;
use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\Enticement;
use App\Models\Log;
use App\Models\Opt;
use App\Models\Product;
use App\Models\SmartBango;
use App\Models\Subscription;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log as FacadesLog;
use Illuminate\Support\Facades\Validator;


class SMSController extends Controller
{

    public function receivedsmsfromkannel(Request $request)
    {
        //check the incoming requests and log into database
        FacadesLog::info($request);

        $content = $request->category;

        if (strlen($request->service) > 4) {

            $content = $this->str_after(strtolower($request->service), 'afya');
        }

        //remove characters from beginning
        $phone = ltrim($request->sender, '+');

        $excustomer = Customer::where('msisdn', $phone)->get()->first();
        if ($excustomer) {
            //save into smartbango table
            if (in_array(strtolower($request->service), ['afyabango', 'afyasmart'])) {
                // Save into smartbango table
                $smartbango = new SmartBango;
                $smartbango->msisdn = $phone;
                $smartbango->state = "existing";
                $smartbango->status = $excustomer->status;
                $smartbango->keyword = $request->service;
                $smartbango->save();
            }
            //unsubsribe users
            if (strtolower($request->service) == 'afyaivr') {

                $product = Product::where('product_ID', '921465_P01')->get()->first();

                if ($excustomer->ivr_status != -1) {

                    $sw = 'Tayari umejiunga na huduma hii piga namba 0900011111 kusikiliza dondoo za afya kwa gharama ya Tsh 300/IVR/siku.';
                    $en = 'You are already subscribed to this service dial 0900011111 to listen to health tips at a cost of Tsh 300 /IVR/day.';
                    ProcessLanguage::dispatchSync($phone, $sw, $en);

                } else {
                    //update the status to charging state
                    $excustomer->ivr_status = 0;
                    $excustomer->keyword = $request->service;
                    $excustomer->updated_at = Carbon::now();
                    $excustomer->save();

                    //update the values
                    $opt = new Opt();
                    $opt->customer_ID = $excustomer->id;
                    $opt->ConversationID = $request->service;
                    $opt->OriginatorConversationID = $content ?? '';
                    $opt->product_ID = $product->id;
                    $opt->opt_value = 1;
                    $opt->date = Opt::getServertime();
                    $opt->save();

                    //Notify customer on afyacall ivr only without charging
                    $res = $this->chargivrtiartime($phone, $product->id, $product->price);
                    if ($res) {
                        //update customer with

                        $sw = 'Umelipia Kikamilifu Tsh ' . $product->price . ' kwenye huduma ya Vodacom AFYACALL IVR piga 0900011111 kusikiliza ';
                        $en = 'You have Successfully paid Tsh ' . $product->price . ' for the Vodacom AFYACALL IVR service dial 0900011111 to listen';
                        ProcessLanguage::dispatchSync($phone, $sw, $en);
                    } else {

                        $sw = 'Hauna salio la kutosha kupata huduma hii.Ongeza salio kisha Tuma neno AFYAIVR kwenda 15723 au piga 0900011111 kwa gharama ya Tsh.300/IVR/siku.';
                        $en = 'You have insufficient balance.Please recharge and send keyword AFYAIVR to shortcode 15723 or dial 0900011111 at a cost of Tsh.300/ IVR/day.';
                        ProcessLanguage::dispatchSync($phone, $sw, $en);
                    }
                }
            } else
                if (strtolower($request->service) == 'ondoaivr') {

                    if ($excustomer->ivr_status != -1) {

                        //check if the customer enticed via ICG
                        if ($excustomer->ivr_enticement == 0) {

                            $excustomer->ivr_status = -1;
                            $excustomer->keyword = $request->service;
                            $excustomer->updated_at = Carbon::now();
                            $excustomer->save();

                            //update the values
                            $opt = new Opt();
                            $opt->customer_ID = $excustomer->id;
                            $opt->ConversationID = $request->service;
                            $opt->OriginatorConversationID = $content ?? '';
                            $opt->opt_value = -1;
                            $opt->date = Opt::getServertime();
                            $opt->save();

                            $subscribeid = Subscription::where('customer_ID', $excustomer->id)->first();
                            if ($subscribeid) {
                                $subscribeid->delete();
                            }

                            $sw = 'Umefanikiwa kujitoa kikamilifu kwenye huduma ya AFYACALL IVR.Kujiunga tena na huduma hii tuma neno AFYAIVR kwenda 15723 kwa gharama ya Tsh 300/siku.';
                            $en = 'You have successfully unsubscribed from AFYACALL IVR service. To rejoin this service send the word AFYAIVR to 15723 at a cost of Tzs 300/day';
                            ProcessLanguage::dispatchSync($phone, $sw, $en);
                        } else {
                            //initiate the unsubscription for a customer via ICG
                            $this->keyword_icg_unsubscribe($phone);
                        }
                    } else {
                        //read customer language
                        $sw = 'Tayari ulijitoa kikamilifu kwenye huduma ya AFYACALL IVR.Kujiunga tena na huduma hii tuma neno AFYAIVR kwenda 15723 kwa gharama ya Tsh 300/siku.';
                        $en = 'You are already unsubscribed to this service. To subscribe send the word AFYAIVR to 15723';

                        ProcessLanguage::dispatchSync($phone, $sw, $en);

                    }
                } else
                    if (strtolower($request->service) == 'ondoasms' || strtolower($request->service) == 'ondoa') {
                        $product = Product::where('product_ID', '921465_P02')->get()->first();
                        if ($excustomer->status != -1) {

                            //check if the customer enticed via ICG
                            if ($excustomer->enticement == 0) {
                                $excustomer->status = -1;
                                $excustomer->ivr_status = -1;
                                $excustomer->keyword = $request->service;
                                $excustomer->updated_at = Carbon::now();
                                $excustomer->save();

                                //update the values
                                $opt = new Opt();
                                $opt->customer_ID = $excustomer->id;
                                $opt->ConversationID = $request->service;
                                $opt->OriginatorConversationID = $content ?? '';
                                $opt->product_ID = $product->id;
                                $opt->opt_value = -1;
                                $opt->date = Opt::getServertime();
                                $opt->save();


                                $subscribeid = Subscription::where('customer_ID', $excustomer->id)->first();
                                if ($subscribeid) {
                                    $subscribeid->delete();
                                }

                                $sw = 'Umefanikiwa kujitoa kikamilifu kwenye huduma ya AFYACALL SMS.Kujiunga tena na huduma hii tuma neno AFYASMS kwenda 15723 kwa gharama ya Tsh 150/siku.';
                                $en = 'You have successfully unsubscribed from AFYACALL SMS service. To rejoin this service send the word AFYASMS to 15723 at a cost of Tzs 150/day';
                                ProcessLanguage::dispatchSync($phone, $sw, $en);
                            } else {

                                //initiate the unsubscription for a customer via ICG
                                $this->keyword_icg_unsubscribe($phone);
                            }
                        } else {


                            if ($excustomer->ivr_status != -1) {
                                //check if the customer enticed via ICG
                                if ($excustomer->enticement == 0) {
                                    $excustomer->ivr_status = -1;
                                    $excustomer->keyword = $request->service;
                                    $excustomer->updated_at = Carbon::now();
                                    $excustomer->save();

                                    //update the values
                                    $opt = new Opt();
                                    $opt->customer_ID = $excustomer->id;
                                    $opt->ConversationID = $request->service;
                                    $opt->OriginatorConversationID = $content ?? '';
                                    $opt->product_ID = 1;
                                    $opt->opt_value = -1;
                                    $opt->date = Opt::getServertime();
                                    $opt->save();


                                    $subscribeid = Subscription::where('customer_ID', $excustomer->id)->first();
                                    if ($subscribeid) {
                                        $subscribeid->delete();
                                    }

                                    $sw = 'Umefanikiwa kujitoa kikamilifu kwenye huduma ya AFYACALL IVR.Kujiunga tena na huduma hii tuma neno AFYAIVR kwenda 15723 kwa gharama ya Tsh 300/siku.';
                                    $en = 'You have successfully unsubscribed from AFYACALL IVR service. To rejoin this service send the word AFYAIVR to 15723 at a cost of Tzs 300/day';
                                    ProcessLanguage::dispatchSync($phone, $sw, $en);

                                } else {

                                    //update ICG when customer try to unsubscription via Keyword
                                    $sw = 'Unakaribia kujiondoa kwenye huduma ya AfyaCall IVR. Piga *150*00# >6 Huduma za kifedha >7 Huduma za Kidigitali >10 Huduma nilizojiunga >Afyacall>Huduma ya IVR.';
                                    $en = 'You are about to unsubscribe to the Afyacall IVR service. Dial *150*00# >6 Financial services >7 Digitial services >10 My Subscriptions>Afyacall >Select IVR.';
                                    ProcessLanguage::dispatchSync($phone, $sw, $en);
                                }
                            } else {

                                //read customer language
                                $sw = 'Tayari ulijitoa kikamilifu kwenye huduma ya AFYACALL SMS.Kujiunga tena na huduma hii tuma neno AFYASMS kwenda 15723 kwa gharama ya Tsh 150/siku.';
                                $en = 'You are already unsubscribed to this service. To subscribe send the word AFYASMS to 15723';
                                ProcessLanguage::dispatchSync($phone, $sw, $en);
                            }
                        }

                    } else
                        if ($excustomer->status == 0 || $excustomer->status == -1) {
                            //charge the user on successfully subscription and send welcome message
                            ProcessCharging::dispatchSync($phone, '15000', $request->service, ucfirst($content));
                        } else {
                            $sw = 'Tayari umejiunga na huduma hii kwa gharama ya Tsh 150/siku.Kujitoa tuma neno ONDOASMS kwenda 15723';
                            $en = 'You are already subscribed to this service. To unsubscribe send the word ONDOASMS to 15723';
                            ProcessLanguage::dispatchSync($phone, $sw, $en);

                        }
        } else {
            if (strtolower($request->service) == 'afyaivr') {

                $product = Product::where('product_ID', '921465_P01')->get()->first();
                //new customer on ivr
                $customer = new Customer();
                $customer->msisdn = $phone;
                $customer->keyword = $request->service;
                $customer->content = ucfirst($content) ?? '';
                $customer->registered_at = Opt::getServertime();
                $customer->ivr_status = 0;
                $customer->save();


                //update the values
                $opt = new Opt();
                $opt->customer_ID = $customer->id;
                $opt->ConversationID = $request->service;
                $opt->OriginatorConversationID = $content ?? '';
                $opt->product_ID = $product->id;
                $opt->opt_value = 1;
                $opt->date = Opt::getServertime();
                $opt->save();

                $res = $this->chargivrtiartime($phone, $product->id, $product->price);
                if ($res) {

                    $sw = 'Umelipia Kikamilifu Tsh ' . $product->price . ' kwenye huduma ya Vodacom AFYACALL IVR piga 0900011111 kusikiliza ';
                    $en = 'You have Successfully paid Tsh ' . $product->price . ' for the Vodacom AFYACALL IVR service dial 0900011111 to listen';
                    ProcessLanguage::dispatchSync($phone, $sw, $en);


                } else {

                    $sw = 'Hauna salio la kutosha kupata huduma hii.Ongeza salio kisha Tuma neno AFYAIVR kwenda 15723 au piga 0900011111 kwa gharama ya Tsh.300/IVR/siku.';
                    $en = 'You have insufficient balance.Please recharge and send keyword AFYAIVR to shortcode 15723 or dial 0900011111 at a cost of Tsh.300/ IVR/day.';
                    ProcessLanguage::dispatchSync($phone, $sw, $en);

                }
            } else {


                if (strtolower($request->service) == 'ondoaivr' || strtolower($request->service) == 'ondoasms' || strtolower($request->service) == 'ondoa') {

                    $customer = new Customer();
                    $customer->msisdn = $phone;
                    $customer->keyword = $request->service;
                    $customer->content = ucfirst($content) ?? '';
                    $customer->registered_at = Opt::getServertime();
                    $customer->status = -1;
                    $customer->ivr_status = -1;
                    $customer->save();

                    //update the values
                    $opt = new Opt();
                    $opt->customer_ID = $customer->id;
                    $opt->ConversationID = $request->service;
                    $opt->OriginatorConversationID = $content ?? '';
                    $opt->product_ID = 2;
                    $opt->opt_value = -1;
                    $opt->date = Opt::getServertime();
                    $opt->save();

                    $sw = 'Tafadhari jiunge na huduma hii kwa kutuma neno Afya kwenda 15723';
                    $en = 'Please subscribe this service by sending the keyword Afya to 15723';
                    ProcessLanguage::dispatchSync($phone, $sw, $en);
                } else {

                    if (strtolower($request->service) == 'afyabango') {

                        $product = Product::where('product_ID', '921465_P02')->get()->first();
                        $customer = new Customer();
                        $customer->msisdn = $phone;
                        $customer->keyword = $request->service;
                        $customer->source = 'SMARTBANGO';
                        $customer->registered_at = Opt::getServertime();
                        $customer->status = 1;
                        $customer->save();

                        //update the values
                        $opt = new Opt();
                        $opt->customer_ID = $customer->id;
                        $opt->ConversationID = $request->service;
                        $opt->OriginatorConversationID = $content ?? '';
                        $opt->product_ID = $product->id;
                        $opt->opt_value = 1;
                        $opt->date = Opt::getServertime();
                        $opt->save();
                        //charge customer airtime ivr

                        //add the customer to subscribtion with 2 days offer
                        $subscrb = Subscription::where('customer_ID', $customer->id)
                            ->where('product_id', $product->id)
                            ->get()->first();
                        if ($subscrb) {
                            $subscrb->customer_ID = $customer->id;
                            $subscrb->product_id = $product->id;
                            $subscrb->starts_at = Carbon::now();
                            $subscrb->ends_at = Carbon::now()->addDays(2);
                            $subscrb->save();
                        } else {
                            $subscribe = new Subscription();
                            $subscribe->customer_ID = $customer->id;
                            $subscribe->product_id = $product->id;
                            $subscribe->starts_at = Carbon::now();
                            $subscribe->ends_at = Carbon::now()->addDays(2);
                            $subscribe->save();
                        }

                        //send the first sms content
                        $this->sendthefirstmessage($phone, null);

                        //send the notification about the offer
                        $sw = 'Hongera!Umejiunga na Afyacall SMS. Umepokea siku mbili za kupata SMS bure. Kuendelea kupata huduma hii utalipia 150/siku.Kujiondoa tuma neno ondoa Kwenda 15723';
                        $en = 'You have subscribed to Afyacall SMS. You will receive 2 SMS for 2 days for FREE. You will then be charged 150/day to continue getting the services. To unsubscribe, send the word ONDOA to 15723';
                        ProcessLanguage::dispatchSync($phone, $sw, $en);
                    } elseif (strtolower($request->service) == 'afyasmart') {

                        $product = Product::where('product_ID', '921465_P01')->get()->first();
                        $customer = new Customer();
                        $customer->msisdn = $phone;
                        $customer->keyword = $request->service;
                        $customer->source = 'SMARTBANGO';
                        $customer->registered_at = Opt::getServertime();
                        $customer->ivr_status = 1;
                        $customer->save();

                        //update the values
                        $opt = new Opt();
                        $opt->customer_ID = $customer->id;
                        $opt->ConversationID = $request->service;
                        $opt->OriginatorConversationID = $content ?? '';
                        $opt->product_ID = $product->id;
                        $opt->opt_value = 1;
                        $opt->date = Opt::getServertime();
                        $opt->save();
                        //charge customer airtime ivr

                        //add the customer to subscribtion with 2 days offer
                        $subscrb = Subscription::where('customer_ID', $customer->id)
                            ->where('product_id', $product->id)
                            ->get()->first();
                        if ($subscrb) {
                            $subscrb->customer_ID = $customer->id;
                            $subscrb->product_id = $product->id;
                            $subscrb->starts_at = Carbon::now();
                            $subscrb->ends_at = Carbon::now()->addDays(2);
                            $subscrb->save();
                        } else {
                            $subscribe = new Subscription();
                            $subscribe->customer_ID = $customer->id;
                            $subscribe->product_id = $product->id;
                            $subscribe->starts_at = Carbon::now();
                            $subscribe->ends_at = Carbon::now()->addDays(2);
                            $subscribe->save();
                        }

                        //notification
                        $sw = 'Umejiunga na Afyacall IVR.Umepokea Siku mbili za bure za kusikiliza, kisha utalipia 300/siku kuendelea kupata huduma. Kujiondoa tuma neno ONDOAIVR Kwenda 15723.';
                        $en = 'You have subscribed to Afyacall IVR. You have two days of Bonus. Thereafter you will listen to IVR at 300/day. To unsubscribe send the word ONDOAIVR TO 15723';
                        ProcessLanguage::dispatchSync($phone, $sw, $en);
                    } else {

                        $product = Product::where('product_ID', '921465_P02')->get()->first();
                        $customer = new Customer();
                        $customer->msisdn = $phone;
                        $customer->keyword = $request->service;
                        $customer->registered_at = Opt::getServertime();
                        $customer->status = 1;
                        $customer->save();

                        //update the values
                        $opt = new Opt();
                        $opt->customer_ID = $customer->id;
                        $opt->ConversationID = $request->service;
                        $opt->OriginatorConversationID = $content ?? '';
                        $opt->product_ID = $product->id;
                        $opt->opt_value = 1;
                        $opt->date = Opt::getServertime();
                        $opt->save();
                        //charge customer airtime ivr

                        //add the customer to subscribtion with 2 days offer
                        $subscrb = Subscription::where('customer_ID', $customer->id)
                            ->where('product_id', $product->id)
                            ->get()->first();
                        if ($subscrb) {
                            $subscrb->customer_ID = $customer->id;
                            $subscrb->product_id = $product->id;
                            $subscrb->starts_at = Carbon::now();
                            $subscrb->ends_at = Carbon::now()->addDays(2);
                            $subscrb->save();
                        } else {
                            $subscribe = new Subscription();
                            $subscribe->customer_ID = $customer->id;
                            $subscribe->product_id = $product->id;
                            $subscribe->starts_at = Carbon::now();
                            $subscribe->ends_at = Carbon::now()->addDays(2);
                            $subscribe->save();
                        }

                        //send the first sms content
                        $this->sendthefirstmessage($phone, null);

                        //send the notification about the offer
                        $sw = 'Hongera!Umejiunga na Afyacall SMS.Umepokea siku mbili za kupata SMS bure.Kuendelea kupata huduma hii utalipia 150/siku.Kujiondoa tuma neno ondoa Kwenda 15723';
                        $en = 'You have subscribed to Afyacall SMS. You will receive 2 SMS for 2 days for FREE. You will then be charged 150/day to continue getting the services. To unsubscribe, send the word ONDOA to 15723';
                        ProcessLanguage::dispatchSync($phone, $sw, $en);

                    }

                    //save into smartbango table
                    if (strtolower($request->service) == 'afyabango' || strtolower($request->service) == 'afyasmart') {
                        $smartbango = new SmartBango;
                        $smartbango->msisdn = $phone;
                        $smartbango->state = "new";
                        $smartbango->keyword = $request->service;
                        $smartbango->save();
                    }
                }
            }
        }
    }

    function str_after($str, $search)
    {
        return $search === '' ? $str : array_reverse(explode($search, $str, 2))[0];
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
            foreach ($allmessage as $value) {
                if (!in_array($value->id, $message_sent_ids)) {
                    return $value->id;
                }
            }
        } else {
            //without message keyword
            $allmessage = Content::all();
            foreach ($allmessage as $value) {
                if (!in_array($value->id, $message_sent_ids)) {
                    return $value->id;
                }
            }
        }
    }
    public function pushenticement($phone, $product)
    {
        //push enticement 
        $code = Opt::getCode();
        try {
            $client = new \GuzzleHttp\Client();
            $response = $client->request('POST', 'https://197.250.9.191:23000/icg/Enticement/', [
                'headers' => [
                    'Content-Type' => ' application/json',
                ],
                'json' => [

                    'input_Username' => '921465',
                    'input_Password' => '5pmls4V!9]O]{IF',
                    'input_WASPShortcode' => '921465',
                    'input_ProductID' => $product,
                    'input_CustomerMSISDN' => $phone,
                    'input_OriginatorConversationID' => $code,
                    'input_EnticementChannel' => 'USSDPush'
                ]
            ]);
            $results = $response->getBody()->getContents();
            $data = json_decode($results, true);
            FacadesLog::info($data);

            return true;
        } catch (\Throwable $th) {
            FacadesLog::error("there is an error");
            return false;
        }
    }


    public function MpesaCharge($phone, $product_ID, $amount)
    {
        FacadesLog::info("Start charching Mpesa on ivr");
        $code = Opt::getCode();
        $customer = Customer::where('msisdn', $phone)->get()->first();
        $product = Product::where('product_ID', $product_ID)->get()->first();
        try {
            $client = new \GuzzleHttp\Client();
            $response = $client->request('POST', 'https://197.250.9.191:23000/icg/Charge/', [
                'verify' => false,
                'headers' => [
                    'Content-Type' => ' application/json',
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
                ]
            ]);
            $results = $response->getBody()->getContents();
            $data = json_decode($results, true);

            FacadesLog::info($data);

            //register transaction
            $trans = new Transaction();
            $trans->customer_ID = $customer->id;
            $trans->amount_IN = $amount;
            $trans->transaction_date = Opt::getServertime();
            $trans->status = 0;
            $trans->product_id = $product->id;
            $trans->currency = "Mpesa";
            $trans->response = $data['output_ResponseDesc'];
            $trans->conventions_ID = $data['output_ConversationID'];
            $trans->response_code = $data['output_ResponseCode'];
            $trans->save();

            return true;
        } catch (\Throwable $th) {
            FacadesLog::info($th->getMessage());
        }
    }

    public function chargivrtiartime($cellNo, $product_id, $amount)
    {

        FacadesLog::info($cellNo);
        //update the payload
        $payload = [
            'type' => 'charge',
            'id' => [
                array(
                    'value' => $cellNo,
                    'schemeName' => 'msisdn'
                )
            ],
            'details' => [
                'adjustmentAmount' => $amount . '00'
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
                    'X-Source-Timestamp' => $chargetime,
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
                $customer->ivr_status = 1;
                $customer->updated_at = Carbon::now();
                $customer->save();

                //register transaction
                $trans = new Transaction();
                $trans->customer_ID = $customer->id;
                $trans->amount_IN = $amount;
                $trans->product_id = $product_id;
                $trans->transaction_date = Opt::getServertime();
                $trans->status = 1;
                $trans->currency = "Airtime";
                $trans->response = $results;
                $trans->save();

                return true;
            }
        } catch (\Throwable $th) {
            return false;
        }
    }



    public function keyword_icg_unsubscribe($phone)
    {
        //push enticement
        $code = Opt::getCode();
        try {
            $client = new \GuzzleHttp\Client();
            $response = $client->request('POST', 'https://197.250.9.191:23000/icg/unsub/', [
                'verify' => false,
                'headers' => [
                    'Content-Type' => ' application/json',
                ],
                'json' => [
                    'input_RequestType' => 'Bulk Opt-Out',
                    'input_Username' => '921465',
                    'input_Password' => '5pmls4V!9]O]{IF',
                    'input_WASPShortcode' => '921465',
                    'input_CustomerMSISDN' => $phone,
                    'input_OriginatorConversationID' => $code,
                ]
            ]);
            $results = $response->getBody()->getContents();
            $data = json_decode($results, true);
            FacadesLog::info($data);

            if ($data['output_ResponseCode'] == 0) {
                $excustomer = Customer::where('msisdn', $phone)->get()->first();
                if ($excustomer) {

                    $excustomer->ivr_status = -1;
                    $excustomer->status = -1;
                    $excustomer->enticement = 0;
                    $excustomer->ivr_enticement = 0;
                    $excustomer->updated_at = Carbon::now();
                    $excustomer->save();

                    //update the values
                    $opt = new Opt();
                    $opt->customer_ID = $excustomer->id;
                    $opt->product_ID = 1;
                    $opt->opt_value = -1;
                    $opt->date = Opt::getServertime();
                    $opt->save();

                    //update the values
                    $opt = new Opt();
                    $opt->customer_ID = $excustomer->id;
                    $opt->product_ID = 2;
                    $opt->opt_value = -1;
                    $opt->date = Opt::getServertime();
                    $opt->save();

                    $subscribeid = Subscription::where('customer_ID', $excustomer->id)->first();
                    if ($subscribeid) {
                        $subscribeid->delete();
                    }

                    $sw = 'Umefanikiwa kikamilifu kijitoa kwenye huduma zote za Vodacom Afyacall.kujiunga tena neno AFYASMS /AFYAIVR kwenda 15723.';
                    $en = 'You have successfully unsubscribed to all Vodacom Afyacall services.To rejoin again send keyword AFYASMS/AFYAIVR to 15723';
                    ProcessLanguage::dispatchSync($phone, $sw, $en);
                }
            }

            return true;
        } catch (\Throwable $th) {
            FacadesLog::error("there is an error on unsub from icg");
            FacadesLog::error($th->getMessage());
        }
    }
}