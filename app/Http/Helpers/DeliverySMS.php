<?php


namespace App\Http\Helpers;

use App\Models\Deliveryobd;
use App\Models\Log;
use App\Models\NotifySms;
use App\Models\SmsDelivery;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log as FacadesLog;

class DeliverySMS
{

    private $destination = null;

    function __construct($destination)
    {
        $this->destination = $destination;
    }

    public function smsDelivery($id, $status)
    {

        if ($this->destination == 'campaign') {
            return $this->smsDeliveryCampaign($id, $status);
        } elseif ($this->destination == 'dailysms') {
            return $this->smsDeliverydailysms($id, $status);
        } elseif ($this->destination == 'obd') {
            return $this->obdDeliverystatus($id, $status);
        } else {
            return $this->notifysms($id, $status);
        }
    }



    public function smsDeliveryCampaign($id, $status)
    {

        try {

            $deliverystatus = SmsDelivery::where('deliveryid', $id)->get()->first();
            if ($deliverystatus) {
                $deliverystatus->status = $status;
                $deliverystatus->deliverytime = Carbon::now();
                $deliverystatus->save();
            }
        } catch (\Throwable $e) {
            FacadesLog::error($e->getMessage());
        }
    }


    public function obdDeliverystatus($id, $status)
    {
        try {
            $deliverystatus = Deliveryobd::where('deliveryid',$id)->get()->first();
            if ($deliverystatus) {
                $deliverystatus->status = $status;
                $deliverystatus->deliverytime = Carbon::now();
                $deliverystatus->save();
            }
        } catch (\Throwable $e) {
            FacadesLog::error($e->getMessage());
        }
    }


    public function smsDeliverydailysms($id, $status)
    {
        try {
            $deliverystatus = Log::where('delivery_id', $id)->get()->first();
            if ($deliverystatus) {
                $deliverystatus->status = $status;
                $deliverystatus->delivery_at = Carbon::now();
                $deliverystatus->save();
            }
        } catch (\Throwable $e) {
            FacadesLog::error($e->getMessage());
        }
    }

    public function notifysms($id, $status)
    {
        try {
            $deliverystatus = NotifySms::where('delivery_id', $id)->get()->first();
            if ($deliverystatus) {
                $deliverystatus->status = $status;
                $deliverystatus->deliverytime = Carbon::now();
                $deliverystatus->save();
            }
        } catch (\Throwable $e) {
            FacadesLog::error($e->getMessage());
        }
    }
}

