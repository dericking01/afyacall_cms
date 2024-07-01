<?php

namespace App\Repositories;

use App\Http\Helpers\SmsHelper;
use App\Jobs\ProcessLanguage;
use App\Models\Content;
use App\Models\ContentType;
use App\Models\Customer;
use App\Models\Opt;
use App\Models\Log as FacadesLog;
use App\Models\Product;
use App\Models\Subscription;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class CustomerRepository
{

    protected $customer;

    public function __construct(Customer $customer)
    {
        $this->customer = $customer;
    }


    public function getAll()
    {
        return $this->customer
            ->get();
    }


    public function subscribe_sms($data)
    {
        //remove
        $msisdn = ltrim($data['sender'], '+');
        $product = Product::where('product_ID', '921465_P02')->first();

        $customer = Customer::where('msisdn', $msisdn)->first();

        if (!$customer) {
            //register new customer
            $customer = new Customer();
            $customer->msisdn = $msisdn;
            $customer->keyword = $data['service'];
            $customer->registered_at = Opt::getServertime();
            $customer->status = 0;
            $customer->save();
        }

        //update the values
        $opt = new Opt();
        $opt->customer_ID = $customer->id;
        $opt->ConversationID = $data['service'];
        $opt->OriginatorConversationID = $data['content'] ?? '';
        $opt->product_ID = $product->id;
        $opt->opt_value = 1;
        $opt->date = Opt::getServertime();
        $opt->save();

        if ($customer->status == 1) {
            $swMessage = 'Tayari umejiunga na huduma hii kwa gharama ya Tsh 150/siku.Kujitoa tuma neno ONDOASMS kwenda 15723';
            $enMessage = 'You are already subscribed to this service. To unsubscribe send the word ONDOASMS to 15723';
        } else {
            $res = $this->chargivrtiartime($customer->id, $customer->msisdn, $product->id, $product->price);

            if ($res) {

                $customer->status = 1;
                $customer->save();

                //add to the subscription
                $this->updateCustomerSubscription($customer->id, $product->id);

                $this->sendthefirstmessage($msisdn, $data['content']);

                $swMessage = 'Umelipia Kikamilifu Tsh ' . $product->price . ' kwenye huduma ya Vodacom AFYACALL';
                $enMessage = 'You have Successfully paid Tsh ' . $product->price . ' for the Vodacom AFYACALL';

            } else {
                $swMessage = 'Hauna salio la kutosha kupata huduma hii. Ongeza salio kisha Tuma neno AFYA kwenda 15723 au piga 0900011111.';
                $enMessage = 'You have insufficient balance. Please recharge and send keyword AFYA to shortcode 15723 or dial 0900011111';
            }
        }
        ProcessLanguage::dispatchSync($msisdn, $swMessage, $enMessage);

    }

    public function unsubscribe_sms($data)
    {
        $msisdn = ltrim($data['sender'], '+');

        $customer = Customer::where('msisdn', $msisdn)->first();
        $product = Product::where('product_ID', '921465_P02')->get()->first();
        if ($customer && $customer->status != -1) {
            if ($customer->enticement == 0) {
                $customer->status = -1;
                $customer->keyword = $data['service'];
                $customer->save();

                $opt = new Opt();
                $opt->customer_ID = $customer->id;
                $opt->product_ID = $product->id;
                $opt->ConversationID = $data['service'];
                $opt->OriginatorConversationID = $data['content'] ?? '';
                $opt->opt_value = -1;
                $opt->date = Opt::getServertime();
                $opt->save();

                Subscription::where('customer_ID', $customer->id)->delete();

                $message = [
                    'sw' => 'Umefanikiwa kujitoa kikamilifu kwenye huduma ya AFYACALL SMS.Kujiunga tena na huduma hii tuma neno AFYASMS kwenda 15723 kwa gharama ya Tsh 150/siku.',
                    'en' => 'You have successfully unsubscribed from AFYACALL SMS service. To rejoin this service send the word AFYASMS to 15723 at a cost of Tzs 150/day'
                ];
                ProcessLanguage::dispatchSync($msisdn, $message['sw'], $message['en']);
            } else {
                $this->keyword_icg_unsubscribe($msisdn);
            }
        } else {
            $message = [
                'sw' => 'Tayari ulijitoa kikamilifu kwenye huduma ya AFYACALL SMS. Kujiunga tena na huduma hii tuma neno AFYASMS kwenda 15723 kwa gharama ya Tsh 150/siku.',
                'en' => 'You are already unsubscribed to this service. To subscribe send the word AFYASMS to 15723'
            ];
            ProcessLanguage::dispatchSync($msisdn, $message['sw'], $message['en']);
        }
    }

    public function subscribe_ivr($data)
    {
        //remove
        $msisdn = ltrim($data['sender'], '+');
        $product = Product::where('product_ID', '921465_P01')->first();

        $customer = Customer::where('msisdn', $msisdn)->first();

        if (!$customer) {
            //register new customer
            $customer = new Customer();
            $customer->msisdn = $msisdn;
            $customer->keyword = $data['service'];
            $customer->registered_at = Opt::getServertime();
            $customer->ivr_status = 0;
            $customer->save();
        }

        //update the values
        $opt = new Opt();
        $opt->customer_ID = $customer->id;
        $opt->ConversationID = $data['service'];
        $opt->OriginatorConversationID = $data['content'] ?? '';
        $opt->product_ID = $product->id;
        $opt->opt_value = 1;
        $opt->date = Opt::getServertime();
        $opt->save();

        if ($customer->ivr_status == 1) {
            $swMessage = 'Tayari umejiunga na huduma hii piga namba 0900011111 kusikiliza dondoo za afya kwa gharama ya Tsh 300/IVR/siku.';
            $enMessage = 'You are already subscribed to this service dial 0900011111 to listen to health tips at a cost of Tsh 300 /IVR/day.';
        } else {
            $res = $this->chargivrtiartime($customer->id, $customer->msisdn, $product->id, $product->price);

            if ($res) {
                // Update the doctor_subscription_status
                $customer->ivr_status = 1;
                $customer->save();

                //add to the subscription
                $this->updateCustomerSubscription($customer->id, $product->id);

                $swMessage = 'Umelipia Kikamilifu Tsh ' . $product->price . ' kwenye huduma ya Vodacom AFYACALL IVR piga 0900011111 kusikiliza ';
                $enMessage = 'You have Successfully paid Tsh ' . $product->price . ' for the Vodacom AFYACALL IVR service dial 0900011111 to listen';

            } else {
                $swMessage = 'Hauna salio la kutosha kupata huduma hii. Ongeza salio kisha Tuma neno AFYAIVR kwenda 15723 au piga 0900011111 kwa gharama ya Tsh.300/IVR/siku.';
                $enMessage = 'You have insufficient balance. Please recharge and send keyword AFYAIVR to shortcode 15723 or dial 0900011111 at a cost of Tsh.300/ IVR/day.';
            }
        }
        ProcessLanguage::dispatchSync($msisdn, $swMessage, $enMessage);
    }

    public function unsubscribe_ivr($data)
    {
        $msisdn = ltrim($data['sender'], '+');

        $customer = Customer::where('msisdn', $msisdn)->first();
        $product = Product::where('product_ID', '921465_P01')->get()->first();
        if ($customer && $customer->ivr_status != -1) {
            if ($customer->ivr_enticement == 0) {
                $customer->ivr_status = -1;
                $customer->keyword = $data['service'];
                $customer->save();

                $opt = new Opt();
                $opt->customer_ID = $customer->id;
                $opt->product_ID = $product->id;
                $opt->ConversationID = $data['service'];
                $opt->OriginatorConversationID = $data['content'] ?? '';
                $opt->opt_value = -1;
                $opt->date = Opt::getServertime();
                $opt->save();

                Subscription::where('customer_ID', $customer->id)->delete();

                $message = [
                    'sw' => 'Umefanikiwa kujitoa kikamilifu kwenye huduma ya AFYACALL IVR. Kujiunga tena na huduma hii tuma neno AFYAIVR kwenda 15723 kwa gharama ya Tsh 300/siku.',
                    'en' => 'You have successfully unsubscribed from AFYACALL IVR service. To rejoin this service send the word AFYAIVR to 15723 at a cost of Tsh 300/day'
                ];
                ProcessLanguage::dispatchSync($msisdn, $message['sw'], $message['en']);
            } else {
                $this->keyword_icg_unsubscribe($msisdn);
            }
        } else {
            $message = [
                'sw' => 'Tayari ulijitoa kikamilifu kwenye huduma ya AFYACALL IVR. Kujiunga tena na huduma hii tuma neno AFYAIVR kwenda 15723 kwa gharama ya Tsh 300/siku.',
                'en' => 'You are already unsubscribed to this service. To subscribe send the word AFYAIVR to 15723'
            ];
            ProcessLanguage::dispatchSync($msisdn, $message['sw'], $message['en']);
        }
    }
    public function subscribe_doctor_sub($data)
    {
        //remove
        $msisdn = ltrim($data['sender'], '+');
        //assign the product id for doctor sub 921465_P03
        $product = Product::where('product_ID', '921465_P03')->first();

        //check if the customer exists on the system
        $customer = Customer::where('msisdn', $msisdn)->first();

        if ($customer){

            if($customer->doctor_subscription_status != -1){
                //send the notification to customer for succefull subscribed on doctor subs
                $swMessage = 'Tayari umejiunga na huduma hii. Kujitoa tuma neno ONDOADOC kwenda 15723';
                $enMessage = 'You are already subscribed to this service. To unsubscribe send the word ONDOADOC to 15723';
                ProcessLanguage::dispatchSync($msisdn, $swMessage, $enMessage);
            } else {
            //update its status to 0 as to be charged
                $customer->doctor_subscription_status = 0;
                $customer->keyword = $data['service'];
                $customer->save();

                //send the notification to customer for succefull subscribed on doctor subs
                $messageSwahili = 'Umejiunga na huduma ya Kuongea na madktari wa Afyacall.Utapokea dakika moja zitazokusanywa kila siku kwa TSH 200/Siku.Kujiondoa Tuma Neno ONDOADOC Kwenda 15723.';
                $messageEnglish = 'You have subscribed Afyacall Direct Doctors call Service.You will receive accumulative 1 min daily for TSH 200 per Day.To unsubscribe send ONDOADOC TO 15723.';
                ProcessLanguage::dispatchSync($msisdn, $messageSwahili, $messageEnglish);
            }

        } else {
            //register the customer, not found on database
            $customer = new Customer();
            $customer->msisdn = $msisdn;
            $customer->keyword = $data['service'];
            $customer->registered_at = Opt::getServertime();
            $customer->doctor_subscription_status = 0;

            //mapping
            $serviceSources = [
                'afya1' => 'INSTAGRAM',
                'afya2' => 'AFYA2',
                'afya3' => 'AFYA3',
                'afya4' => 'AFYA4',
                'afya5' => 'AFYA5',
                'afya6' => 'AFYA6',
                'afya7' => 'AFYA7',
                'afya8' => 'AFYA8',
                'afya9' => 'AFYA9',
                'afya10' => 'AFYA10',
		'afya11' => 'AFYA11',
		'afya12' => 'AFYA12',
		'afya13' => 'AFYA13',
		'afya14' => 'AFYA14',
		'afya15' => 'AFYA15',
		'afya16' => 'AFYA16',
            ];

            // Convert the service to lowercase for consistent comparison
            $service = strtolower(trim($data['service']));  // Trim to remove any extra spaces

            // Check if the service exists in the mapping array and set the source
            if (isset($serviceSources[$service])) {
                $customer->source = $serviceSources[$service];
            } else {
                // where the service is not recognized
                $customer->source = 'NIL';
            }
            $customer->save();

            //send the notification to customer for succefull subscribed on doctor subs
            $messageSwahili = 'Umejiunga na huduma ya Kuongea na madktari wa Afyacall.Utapokea dakika moja zitazokusanywa kila siku kwa TSH 200/Siku.Kujiondoa Tuma Neno ONDOADOC Kwenda 15723.';
            $messageEnglish = 'You have subscribed Afyacall Direct Doctors call Service.You will receive accumulative 1 min daily for TSH 200 per Day.To unsubscribe send ONDOADOC TO 15723.';
            ProcessLanguage::dispatchSync($msisdn, $messageSwahili, $messageEnglish);
        }

        //update the values
        $opt = new Opt();
        $opt->customer_ID = $customer->id;
        $opt->ConversationID = $data['service'];
        $opt->OriginatorConversationID = $data['content'] ?? '';
        $opt->product_ID = $product->id;
        $opt->opt_value = 1;
        $opt->date = Opt::getServertime();
        $opt->save();

        $res = $this->chargivrtiartime($customer->id, $customer->msisdn, $product->id, $product->price);

        if ($res) {
            // Update the doctor_subscription_status
            $customer->doctor_subscription_status = 1;
            $customer->other_status += 60;
            $customer->save();

            //add to the subscription
            $this->updateCustomerSubscription($customer->id, $product->id);

            $swMessage = 'Umelipia Kikamilifu Tsh ' . $product->price . ' kwenye huduma ya Vodacom AFYACALL piga 0900011111 kusikiliza';
            $enMessage = 'You have Successfully paid Tsh ' . $product->price . ' for the Vodacom AFYACALL service dial 0900011111 to listen';
        } else {
            //send the notification to customer for insufficient balance
            $swMessage = 'Hauna salio la kutosha kupata huduma hii. Ongeza salio kisha Tuma neno AFYADOC kwenda 15723 au piga 0900011111 kwa gharama ya Tsh.200/siku.';
            $enMessage = 'You have insufficient balance. Please recharge and send keyword AFYADOC to shortcode 15723 or dial 0900011111 at a cost of Tsh.200/day.';

        }
        ProcessLanguage::dispatchSync($msisdn, $swMessage, $enMessage);

    }

    public function unsubscribe_doctor_subscription($data)
    {
        $msisdn = ltrim($data['sender'], '+');

        $customer = Customer::where('msisdn', $msisdn)->first();
        $product = Product::where('product_ID', '921465_P03')->first();

        if ($customer->doctor_subscription_status != -1) {
            $customer->doctor_subscription_status = -1;
            $customer->keyword = $data['service'];
            $customer->save();

            $opt = new Opt([
                'customer_ID' => $customer->id,
                'ConversationID' => $data['service'],
                'OriginatorConversationID' => $data['content'] ?? '',
                'opt_value' => -1,
                'product_ID' => $product->id,
                'date' => Opt::getServertime()
            ]);
            $opt->save();

            Subscription::where('customer_ID', $customer->id)
                ->where('product_id', $product->id)
                ->delete();

            $message = [
                'sw' => 'Umefanikiwa kujitoa kikamilifu kwenye huduma ya AFYACALL DOCTOR. Kujiunga tena na huduma piga namba 0900011111.',
                'en' => 'You have successfully unsubscribed from AFYACALL Doctors Live Call service. To rejoin this service dial 0900011111.'
            ];
        } else {
            $message = [
                'sw' => 'Tayari ulijitoa kikamilifu kwenye huduma ya AFYACALL DOCTOR. Kujiunga tena na huduma hii piga namba 0900011111.',
                'en' => 'You are already unsubscribed to this service. To rejoin this service dial 0900011111.'
            ];
        }

        ProcessLanguage::dispatchSync($msisdn, $message['sw'], $message['en']);
    }


    public function chargivrtiartime($customer_id, $msisdn, $product_id, $amount)
    {
        try {

            // Prepare the payload for the charging request
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
                'desc' => 'Afya Call',
                'category' => [
                    [
                        'value' => 'MW',
                        'listHierarchyId' => 'eventClass'
                    ]
                ]
            ];

            // Get the current server time
            $chargetime = Opt::getServertime();

            // Create the HTTP client
            $client = new \GuzzleHttp\Client;

            // Prepare the request headers
            $credentials = base64_encode('svc_afyacall:wHroRA3U03_el701');
            $headers = [
                'Authorization' => 'Basic ' . $credentials,
                'Content-Type' => 'application/json',
                'X-MessageId' => 'uuid:a5c49974-353e-11e5-a151-feff819cdc9f',
                'X-Source-Timestamp' => $chargetime,
            ];

            // Send the charging request
            $response = $client->post('https://197.250.9.149:6202/middlewarev2/serviceAccountAdjustment', [
                'verify' => false,
                'headers' => $headers,
                'json' => $payload
            ]);

            // Process the response
            $results = $response->getBody()->getContents();
            $data = json_decode($results, true);

            // Extract relevant information from the response
            $amountcharged = $data['parts']['serviceBalance'][0]['adjustmentAmount'];
            $amountremain = $data['parts']['serviceBalance'][0]['balanceAmount'];
            $accountname = $data['parts']['serviceBalance'][0]['name'];

            // Register the transaction
            $trans = new Transaction();
            $trans->customer_ID = $customer_id;
            $trans->amount_IN = $amount;
            $trans->product_id = $product_id;
            $trans->transaction_date = Opt::getServertime();
            $trans->status = 1;
            $trans->currency = "Airtime";
            $trans->response = 'Process service request successfully.';
            $trans->save();

            return true;
        } catch (\Throwable $th) {
            // Log the error for debugging purposes
            Log::error("dc there is network problem or the customer has insufficient balance");
            Log::error('dc ' .$th->getMessage());

            #save the transaction
            $transaction = new Transaction();
            $transaction->customer_ID = $customer_id;
            $transaction->amount_IN = $amount;
            $transaction->product_id = $product_id;
            $transaction->response = 'Insufficient Balance';
            $transaction->transaction_date = Opt::getServertime();
            $transaction->status = 0;
            $transaction->currency = "Airtime";
            $transaction->save();

            return false;
        }
    }


    public function updateCustomerSubscription($customer_id, $product_id)
    {
        $subscription = Subscription::firstOrNew([
            'customer_ID' => $customer_id,
            'product_id' => $product_id,
        ]);

        $subscription->starts_at = now();
        $subscription->ends_at = now()->addDay();
        $subscription->save();
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
            Log::info($data);

            if ($data['output_ResponseCode'] == 0) {
                $excustomer = Customer::where('msisdn', $phone)->get()->first();
                if ($excustomer) {

                    $excustomer->ivr_status = -1;
                    $excustomer->status = -1;
                    $excustomer->enticement = 0;
                    $excustomer->ivr_enticement = 0;
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
            Log::error("there is an error on unsub from icg");
            Log::error($th->getMessage());
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
        $lastmessage = FacadesLog::where('last_sent', $number)->get()->pluck('content_id');
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

    public function unsubscribe_allservices($data)
    {
        $msisdn = ltrim($data['sender'], '+');

        $customer = Customer::where('msisdn', $msisdn)->first();
        if ($customer) {
            if ($customer->enticement == 0) {
                $customer->status = -1;
                $customer->ivr_status = -1;
                $customer->doctor_subscription_status = -1;
                $customer->keyword = $data['service'];
                $customer->save();

                $opt = new Opt();
                $opt->customer_ID = $customer->id;
                $opt->ConversationID = $data['service'];
                $opt->OriginatorConversationID = $data['content'] ?? '';
                $opt->opt_value = -1;
                $opt->date = Opt::getServertime();
                $opt->save();

                Subscription::where('customer_ID', $customer->id)->delete();

                $message = [
                    'sw' => 'Umefanikiwa kujitoa kikamilifu kwenye huduma zote za AFYACALL .Kujiunga tena na huduma hizi tuma neno AFYA kwenda 15723 au piga 0900011111.',
                    'en' => 'You have successfully unsubscribed from all AFYACALL services. To rejoin this service send the word AFYA to 15723 or call 0900011111'
                ];
                ProcessLanguage::dispatchSync($msisdn, $message['sw'], $message['en']);
            } else {
                $this->keyword_icg_unsubscribe($msisdn);
            }
        } else {
            $message = [
                'sw' => 'Tayari ulijitoa kikamilifu kwenye huduma zote za AFYACALL. Kujiunga tena na huduma hii tuma neno AFYA kwenda 15723 au piga 0900011111.',
                'en' => 'You are already unsubscribed to this service. To subscribe send the word AFYA to 15723 or call 0900011111'
            ];
            ProcessLanguage::dispatchSync($msisdn, $message['sw'], $message['en']);
        }
    }
}
