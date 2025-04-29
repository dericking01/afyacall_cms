<?php

namespace App\Http\Helpers;

use App\Models\BotSubscription;
use App\Jobs\DeleteExpiredSubscription;
use Carbon\Carbon;

class BotSubs
{
    public static function botSubscription($msisdn, $content, $startsAt, $endsAt)
    {
        try {
            // Optimized search: Check if msisdn already exists
            $exists = BotSubscription::where('msisdn', $msisdn)->exists();

            if ($exists) {
                return response()->json([
                    'status'  => -2,
                    'message' => 'Duplicate entry. MSISDN already subscribed.',
                ], 200);
            }

            // Save new subscription
            $subscription = BotSubscription::create([
                'msisdn'    => $msisdn,
                'content'   => $content,
                'starts_at' => $startsAt,
                'ends_at'   => $endsAt,
            ]);

            // Calculate delay time
            $delay = Carbon::parse($endsAt)->diffInSeconds(now());

            // Dispatch a deletion job after delay
            DeleteExpiredSubscription::dispatch($subscription->id)->delay(now()->addSeconds($delay));

            return response()->json([
                'status'  => 0,
                'message' => 'Subscription saved successfully',
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status'  => -1,
                'message' => 'Failed to save subscription',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}
