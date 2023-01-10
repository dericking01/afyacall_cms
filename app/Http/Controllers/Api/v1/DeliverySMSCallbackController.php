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
     ProcessDeliverySMS::dispatch('obd', $request->account, $request->status)->onQueue('delivery');
    }
    public function deliveryreport(Request $request)
    {

    ProcessDeliverySMS::dispatch('campaign', $request->id, $request->status)->onQueue('delivery');
    }

    public function dailydeliveryreport(Request $request)
    {
	     FacadesLog::info($request);
        try {
            $dailydeliverystatus = Log::where('delivery_id', $request->id)->get()->first();
            if ($dailydeliverystatus) {
                $dailydeliverystatus->status = $request->status;
                $dailydeliverystatus->delivery_at = Carbon::now();
                $dailydeliverystatus->save();
            }
        } catch (\Throwable $e) {
            FacadesLog::error($e->getMessage());
        }
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

