<?php


namespace App\Http\Helpers;

use App\Models\Deliveryobd;
use App\Models\Opt;
use App\Models\Outboundcall;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class HelperObd
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
        $campaign = Outboundcall::where('id', $campaing_id)->get()->first();
        try {
            $client = new \GuzzleHttp\Client();
	    $client->request('GET', 'http://192.168.1.41/callfile/callfile.php', [
		     'verify' => false,
                'query' => [
                    'msisdn' => $msisdn,
                    'account' => $code,
                    'MaxRetries' => $campaign->maxretries,
                    'RetryTime' => $campaign->retrytime,
                    'WaitTime' => $campaign->waittime,
                ]
            ]);

            //save to database 
            $this->deliverysms($campaign->id, $code, $msisdn);
            return true;
        } catch (\Throwable $e) {
            Log::error($e->getMessage());
            return false;
        }
        return false;
    }

    public function deliverysms($campaing_id, $code, $msisdn)
    {
        try {
            $deliverysms = new Deliveryobd();
            $deliverysms->deliveryid = $code;
            $deliverysms->obdid = $campaing_id;
            $deliverysms->msisdn = $msisdn;
            $deliverysms->senttime = Carbon::now();
            $deliverysms->save();
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
        }
    }
}

