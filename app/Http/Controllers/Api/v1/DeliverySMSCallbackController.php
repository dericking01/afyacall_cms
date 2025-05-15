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
        return false;
        FacadesLog::info('THERE DAILY OBD');

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

