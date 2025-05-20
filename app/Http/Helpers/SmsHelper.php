<?php


namespace App\Http\Helpers;

use App\Models\Content;
use App\Models\ContentType;
use App\Models\Customer;
use App\Models\Log;
use App\Models\Opt;
use Carbon\Carbon;

class SmsHelper
{


    function __construct()
    {
    }

    public function sendSms($number, $messageid)
    {
        try {
            return $this->sendSmsViKannel($number, $messageid);
        } catch (\Throwable $e) {
            //write error log
        }
    }

    public function sendSmsViKannel($number, $messageid)
    {
        try {
            $code = Opt::getCode();
            $messagecontent = Content::where('id', $messageid)->first();
            $customer = Customer::where('msisdn', $number)->first();
            if ($messagecontent) {
                if ($customer['language'] == 'en') {
                    $message =  $messagecontent['eng_message'];
                } else {
                    $message =  $messagecontent['message'];
                }
                $client = new \GuzzleHttp\Client();
                $client->request('GET', 'http://192.168.1.200:6013/cgi-bin/sendsms', [
                    'query' => [
                        'username' => 'afya',
                        'password' => 'Afya4017',
                        'from' => 'AFYACALL',
                        'dlr-mask' => 31,
                        'dlr-url' => 'http://192.168.1.10/api/sms/dailydeliveryreport?id=' . $code . '&status=%d',
                        'to' => '+' . $number,
                        'text' => $message,
                    ]
                ]);
                $this->logSmsToDB($number, $messageid,$code);
            }

        } catch (\Throwable $e) {
        }
    }

    public function SendDailyTips($number, $message)
    {
        try {
            $code = Opt::getCode();

                $client = new \GuzzleHttp\Client();
                $client->request('GET', 'http://192.168.1.200:6013/cgi-bin/sendsms', [
                    'query' => [
                        'username' => 'afya',
                        'password' => 'Afya4017',
                        'from' => 'AFYACALL',
                        'dlr-mask' => 31,
                        'dlr-url' => 'http://192.168.1.200:5443/api/sms/dailydeliveryreport?id=' . $code . '&status=%d',
                        'to' => '+' . $number,
                        'text' => $message,
                    ]
                ]);
                $this->logSmsToDB($number, $messageid,$code);

        } catch (\Throwable $e) {
        }
    }

    public function logSmsToDB($number, $messageid,$code)
    {

        $check_msisdn = Customer::where('msisdn', $number)->first();
        if ($check_msisdn) {
            $logsms = new Log();
            $logsms->customer_id = $check_msisdn->id;
            $logsms->content_id = $messageid;
            $logsms->last_sent = $number;
            $logsms->delivery_id = $code;
            $logsms->sent_at = Carbon::now();
            $logsms->save();
        }

        return true;
    }
}

