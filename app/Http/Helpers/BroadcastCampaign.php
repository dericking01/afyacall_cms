<?php


namespace App\Http\Helpers;

use App\Models\Blacklist;
use App\Models\Campaign;
use App\Models\Contact;
use App\Models\Opt;
use App\Models\SmsDelivery;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class BroadcastCampaign
{

    function __construct()
    {
    }

    public function broadcast($receipts, $campaing_id)
    {
        try {
            $receipts = explode(',', $receipts);
            foreach ($receipts as $msisdn) {

                $this->prodcastsms($msisdn, $campaing_id);
            };
        } catch (\Throwable $e) {
            Log::error('failed to broadcast' . $e->getMessage());
            
        }
    }

    public function prodcastsms($msisdn, $campaing_id)
    {
        $code = Opt::getCode();
        $campaign = Campaign::where('id', $campaing_id)->get()->first();
        try {
            $client = new \GuzzleHttp\Client();
            $client->request('GET', 'http://192.168.1.10:6013/cgi-bin/sendsms', [
                'query' => [
                    'username' => 'afya',
                    'password' => 'Afya4017',
                    'from' => 'AFYACALL',
                    'dlr-mask' => 31,
                    'dlr-url' => 'http://192.168.1.10/api/sms/deliveryreport?id=' . $code . '&status=%d',
                    'to' => '+' . $msisdn,
                    'text' => $campaign->message,
                ]
            ]);
            //update the the delivery reports
            $this->deliverysms($campaign->id, $code, $msisdn);
            //update the number of success sms sent
            return true;
        } catch (\Throwable $e) {
            Log::error('failed to send sms' . $e->getMessage());
            return true;
        }
    }



        public function deliverysms($campaing_id, $code, $msisdn)
    {
        try {
            $deliverysms = new SmsDelivery();
            $deliverysms->deliveryid = $code;
            $deliverysms->campaignid = $campaing_id;
            $deliverysms->msisdn = $msisdn;
            $deliverysms->senttime = Carbon::now();
            $deliverysms->save();
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
        }
    }
}
