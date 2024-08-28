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
       
        //time 
        $excustomer = Customer::where('msisdn',$number)->get()->first();
        if($excustomer){
            $this->sendnotificationmessage($number, $sw,$excustomer->id);
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

