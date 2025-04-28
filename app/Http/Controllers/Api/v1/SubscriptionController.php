<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Helpers\BotSubs;

class SubscriptionController extends Controller
{
    public function subsInfo(Request $request)
    {
        Log::info('****** BOTSubs Info FROM DOCKER SERVER ********');
        Log::info($request->all());

        // Validate incoming request
        $validated = $request->validate([
            'requestType' => 'required|string',
            'msisdn'      => 'required|string',
            'productCode' => 'required|string',
            'Content'     => 'required|string',
            'startsAt'    => 'required|date',
            'endsAt'      => 'required|date',
        ]);

        $requestType = $validated['requestType'];
        $msisdn = $validated['msisdn'];
        $productCode = $validated['productCode'];
        $content = $validated['Content'];
        $startsAt = $validated['startsAt'];
        $endsAt = $validated['endsAt'];

        if ($requestType !== 'SubsInfo') {
            return response()->json([
                "output_ResponseCode" => "-2",
                "output_ResponseDesc" => "Invalid Request Type",
            ]);
        }

        switch ($productCode) {
            case '921465_P02':
                return BotSubs::botSubscription($msisdn, $content, $startsAt, $endsAt);

            default:
                return response()->json([
                    "output_ResponseCode" => "-2",
                    "output_ResponseDesc" => "Invalid Product Code",
                ]);
        }
    }
}