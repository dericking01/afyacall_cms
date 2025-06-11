<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessDeliverySMS;
use App\Models\Log;
use App\Models\NotifySms;
use App\Models\SmsDelivery;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log as FacadesLog;
use App\Models\ObdDelivery;
use App\Http\Helpers\BotCampaignHelper;

class DeliverySMSCallbackController extends Controller
{
    public function deliveryobd(Request $request)
    {
        FacadesLog::info('THERE OBD');

     ProcessDeliverySMS::dispatch('obd', $request->account, $request->status)->onQueue('delivery');
    }
    public function deliveryreport(Request $request)
    {
        FacadesLog::info('here dlr OBD');
    ProcessDeliverySMS::dispatch('campaign', $request->id, $request->status)->onQueue('delivery');
    }

    public function dailydeliveryreport(Request $request)
    {
        // FacadesLog::info($request->all());
        $msisdn = $request->input('id');
        $status = $request->input('status');
        // return false;
        BotCampaignHelper::updateStatus($msisdn, $status);

        return response()->json(['message' => 'Status updated'], 200);

    ProcessDeliverySMS::dispatch('dailysms', $request->id, $request->status)->onQueue('delivery');
	     
    }

    public function notifysms(Request $request)
    {
        try {
            $deliverystatus = NotifySms::where('delivery_id', $request->id)->get()->first();
            if ($deliverystatus) {
                $deliverystatus->status = $request->status;
                $deliverystatus->deliverytime = Carbon::now();
                $deliverystatus->save();
            }
        } catch (\Throwable $e) {
            FacadesLog::error($e->getMessage());
        }
    }
}

