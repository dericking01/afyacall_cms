<?php

namespace App\Http\Helpers;

use App\Models\Opt;
use App\Models\Customer;
use App\Models\NotifySms;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class LanguageHelper
{
    function __construct()
    {
    }

    public function language($number, $sw, $en)
    {
        $payload = [
            'id'   => [
                array(
                    'value' => $number,
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
        $excustomer = Customer::where('msisdn',$number)->get()->first();
        $chargetime = Opt::getTimestamp();
        $uuid = Opt::generateUUIDv1();
        try {
            $client = new \GuzzleHttp\Client;
            $credentials = base64_encode('svc_afyacall:wHroRA3U03_el701');
            $response = $client->get('https://197.250.9.149:6202/middlewarev2/CustomerPrivacyProfile', [
                'verify' => false,
                'headers' => [
                    'Authorization' => 'Basic ' . $credentials,
                    'Content-Type' => ' application/json',
                    'X-MessageId' => 'uuid: '.$uuid,
                    'X-Source-Timestamp'  => $chargetime,
                ],
                'json' => $payload
            ]);
            $balances = $response->getBody()->getContents();

            $data = json_decode($balances, true);

            $langu = $data['parts']['partyPreference'][0]['value'];

            if ($langu == 'en') {
               
                if ($excustomer) {
                    $excustomer->language = 'en';
                    $excustomer->save();
                }
                $this->sendnotificationmessage($number, $en,$excustomer->id);
            } else {
                //send in swahili
                $this->sendnotificationmessage($number, $sw,$excustomer->id);
            }
        } catch (\Throwable $th) {
            //if there is any error send sms by swahili language
           // $this->sendnotificationmessage($number, $sw,$excustomer->id);
        }
    }

    public function sendnotificationmessage($number, $message,$excustomerid)
    {
        try {
            $code = Opt::getCode();
            $client = new \GuzzleHttp\Client();
            $client->request('GET', 'http://192.168.1.10:6013/cgi-bin/sendsms', [
                'query' => [
                    'username' => 'afya',
                    'password' => 'Afya4017',
                    'from' => '15723',
                    'dlr-mask' => 31,
                    'dlr-url' => 'http://192.168.1.10/api/sms/notifysms?id=' . $code . '&status=%d',
                    'to' => '+' . $number,
                    'text' => $message,
                ]
            ]);

            $notifysms = new NotifySms();
            $notifysms->delivery_id = $code;
            $notifysms->contactid = $excustomerid;
            $notifysms->msisdn = $number;
            $notifysms->other = $message;
            $notifysms->senttime = Carbon::now();
            $notifysms->save();

            return true;
        } catch (\Throwable $th) {
           Log::error($th->getMessage());
        }
    }
}

