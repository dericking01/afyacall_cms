<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Helpers\ChargingMpesa;
use App\Models\Enticement;
use App\Services\CustomerService;
use App\Repositories\CustomerRepository;
use App\Http\Controllers\Api\v1\IVRController;
use Illuminate\Support\Facades\Log;


class ServiceController extends Controller
{
    protected $serviceRequest;
    protected $enticement;
    protected $subscription;
    protected $ondemand;

    public function __construct(
        ChargingMpesa $serviceRequest,
        Enticement $enticement,
        CustomerRepository $subscription,
        ChargingMpesa $ondemand
    ) {
        $this->serviceRequest = $serviceRequest;
        $this->enticement = $enticement;
        $this->subscription = $subscription;
        $this->ondemand = $ondemand;
    }

    public function serviceRequests(Request $request)
    {
        Log::info('******BOT REQUEST FROM DOCKER SERVER********');
        Log::info($request->all());

        $cellNo = $request->cellNo;
        $amount = $request->Amount;
        $conversationId = $request->OriginatorConversation_id;
        $requestType = $request->request_Type;
        $productId = $request->product_ID;
        $loginpayload = $request->login_payload;

        if (in_array($productId, ['921465_P04', '921465_P05', '921465_P06'])) {
            Log::info("BOT ON-DEMAND CHARGE REQUEST FOR", ['cell_no' => $cellNo]);
            request()->merge(['override_code' => $conversationId]);
            return $this->ondemand->botOnDemand($cellNo, $productId, $amount, $conversationId);
        } else {
            Log::info("BOT REGULAR FLOW REQUEST FOR", ['product_id' => $productId]);
        }

        switch ($requestType) {
            case 'ChargeCustomer':
                return $this->serviceRequest->ChargeService($cellNo, $conversationId, $amount, $productId);

            case 'Enticement':
                return $this->enticement->promptDkrEnticement($cellNo, $conversationId);

            case 'Unsubscription':
                Log::info("BOT UNSUB REQUEST FOR", ['cell_no' => $cellNo]);
                request()->merge(['override_code' => $conversationId]);
                return $this->subscription->keyword_icg_unsubscribe_specific_product($cellNo, $productId);

            case 'Serviceinfo':
                Log::info("BOT ServiceInfo REQUEST FOR", ['cell_no' => $cellNo]);
                request()->merge(['override_code' => $conversationId]);
                return $this->subscription->ServiceInfoSub($cellNo, $productId);

            case 'QueryBalance':
                Log::info("BOT QueryBalance REQUEST FOR", ['cell_no' => $cellNo]);
                request()->merge(['override_code' => $conversationId]);
                return $this->serviceRequest->checkbotbalance($productId, $cellNo, $conversationId);

            case 'IpgAuthentication':
                Log::info("BOT IPG AUTHENTICATION REQUEST");
                return $this->serviceRequest->loginRequest($loginpayload);

            default:
                return response()->json([
                    "output_ResponseCode" => "-2",
                    "output_ResponseDesc" => "Invalid Request Type",
                ], 400);
        }
    }
}

