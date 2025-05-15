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

        if ($request->requestType !== 'SubsInfo') {
            return response()->json([
                "output_ResponseCode" => "-2",
                "output_ResponseDesc" => "Invalid Request Type",
            ]);
        }

        // Validate as strings
        $validated = $request->validate([
            'requestType' => 'required|string',
            'msisdn'      => 'required|string',
            'productCode' => 'required|string',
            'content'     => 'nullable|string',
            'startsAt'    => 'required|string',
            'endsAt'      => 'required|string',
        ]);

        try {
            // Truncate nanoseconds to microseconds (6 digits)
            $startsAtRaw = preg_replace('/\.(\d{6})\d+/', '.$1', $validated['startsAt']);
            $endsAtRaw = preg_replace('/\.(\d{6})\d+/', '.$1', $validated['endsAt']);

            // Parse with Carbon
            $startsAt = \Carbon\Carbon::parse($startsAtRaw);
            $endsAt = \Carbon\Carbon::parse($endsAtRaw);
        } catch (\Exception $e) {
            Log::error('Date parsing failed', ['error' => $e->getMessage()]);
            return response()->json([
                "output_ResponseCode" => "-3",
                "output_ResponseDesc" => "Invalid date format",
            ]);
        }

        $requestType = $validated['requestType'];
        $msisdn = $validated['msisdn'];
        $productCode = $validated['productCode'];
        $content = $validated['content'];

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