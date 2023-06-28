<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SmartBango;
use Illuminate\Support\Facades\Log as FacadesLog;
use App\Services\CustomerService;


class SMSController extends Controller
{

    public function receivedsmsfromkannel(Request $request, CustomerService $customerService)
    {
        //check the incoming requests and log into database
        FacadesLog::info($request);

        //validate data 

        //update to the smartbango tables
        if (in_array(strtolower($request->service), ['afyabango', 'afyasmart', 'afyasmartd'])) {
            $smartBangoData = [
                'msisdn' => ltrim($request->sender, '+'),
                'keyword' => $request->service,
            ];
            SmartBango::create($smartBangoData);
        }
        if (strtolower($request->service) == 'afyaivr') {
            return $customerService->subscribe_ivr($request);
        } elseif (strtolower($request->service) == 'ondoaivr' || strtolower($request->service) == 'ondoa') {
            return $customerService->unsubscribe_ivr($request);
        } elseif (strtolower($request->service) == 'afyabango') {
            return $customerService->subscribe_sms($request);
        } elseif (strtolower($request->service) == 'afyasmart') {
            return $customerService->subscribe_ivr($request);
        } elseif (strtolower($request->service) == 'afyasmartd') {
            return $customerService->subscribe_doctor_sub($request);
        } elseif (strtolower($request->service) == 'ondoadoc') {
            return $customerService->unsubscribe_doctor_subscription($request);
        } elseif (strtolower($request->service) == 'ondoasms' || strtolower($request->service) == 'ondoa') {
            return $customerService->unsubscribe_sms($request);
        } else {
            return $customerService->subscribe_sms($request);
        }
    }

}